<?php

namespace App\Http\Controllers;

use App\Entities\Order;
use App\Entities\Product;
use App\Entities\ProductVariant;
use App\Repositories\ProductRepository;
use Illuminate\Http\Request;
use App\Http\Requests\OrderProduct;
use App\Events\OrderCreated;
use App\Notifications\OrderStatusChanged;
use BrianFaust\Braintree\Facades\Braintree;
use Ayeo\Price\Price;
use Carbon\Carbon;

class OrderController extends Controller
{
    public function create($id, Request $request)
    {
    	$product = Product::with('owner.shippingPrices')->findOrFail($id);

        if($request->has('variant'))
        {
            $variant = ProductVariant::where('product_id', $product->id)->findOrFail($request->variant);

            $product->price = $variant->price;
        }

        $cities = app(ProductRepository::class)->getCitiesWithShipping($product->owner);

        $price = $product->price;

        $city_id = old('city_id', auth()->user()->city_id) ?? 0;

        $payment = [
            'authcode' => app("braintree")->getClientToken()->generate(),
            'headerText' => __('Card details'),
            'cardNumber' => __('Card Number'),
            'exDate' => __('Expiration Date (MM/YY)'),
        ];

        capsule('frontend')->addJs(compact('cities', 'price', 'city_id', 'payment'));

        return view('products.order', compact('product'));
    }

    public function store($id, OrderProduct $request)
    {
        $product = Product::findOrFail($id);

        $user = auth()->user();

        $variants_count = ProductVariant::where('product_id', $product->id)->where('in_stock', '>', 0)->count();

        if($product->has_variants && $variants_count) 
        {
            $variant = ProductVariant::where('product_id', $product->id)->findOrFail($request->variant);

            $productPrice = Price::buildByGross($variant->price, 0, $product->currency);
        } 
        else 
        {
            $variant = null;
            
            $productPrice = Price::buildByGross($product->price, 0, $product->currency);
        }

        $subTotal = $productPrice->multiply($request->quantity);

        $mShippingPrice = app(ProductRepository::class)->getShippingPriceForCity($request->city_id, $product->owner_id);

        $shippingPrice = Price::buildByGross($mShippingPrice->price, 0, $product->currency);

        $total = $productPrice->add($shippingPrice)->getGross();

        $windowFrom = Carbon::now()->addDays($mShippingPrice->window_from);

        $windowTo = Carbon::now()->addDays($mShippingPrice->window_to);

        $order = Order::create([
            'status' => 'created',
            'amount' => $total,
            'currency' => $product->currency,
            'quantity' => $request->quantity,
            'address' => $request->address,
            'shipping_cost' => $shippingPrice->getGross(),
            'shipping_window_from' => $windowFrom,
            'shipping_window_to' => $windowTo,
            'payment_method' => 'COD',
            'user_id' => $user->id,
            'product_id' => $product->id,
            'product_variant' => $variant ? $variant->name : null,
            'merchant_id' => $product->owner_id,
            'city_id' => $request->city_id,
            'phone' => $request->phone,
            'title' => $product->title,
        ]);

        $transaction = Braintree::getTransaction()->sale([
            'amount' => $total,
            'paymentMethodNonce' => $request->payload,
            'billing' => [
              'countryCodeAlpha2' => 'GE'
            ],
            'options' => [
                'submitForSettlement' => true,
            ]
        ]);

        if(!$transaction->success)
        {
            return redirect()->back()->withErrors([
                'card' => __('Transaction rejected, please recheck card details or contact your bank.')
            ]);
        }

        $order->notify(new OrderStatusChanged());

        event(new OrderCreated($order, $user));

        if($product->has_variants) 
        {
            $variant->decrement('in_stock', $request->quantity);

            $variant->update();
        } 
        else 
        {
            $product->decrement('in_stock', $request->quantity);
        }

        $product->increment('sales_count', $request->quantity);

        $product->update();

        return redirect()->route('orders.show', ['id' => $order->id])->with('flash', 'thanks');
    }

    public function index()
    {
        $user = auth()->user();

        $orders = Order::where('user_id', $user->id)->orWhere('merchant_id', $user->id)->with('product')->latest()->paginate();

        return view('orders.index', compact('orders'));
    }

    public function show($id)
    {
        $user = auth()->user();

    	$order = Order::with('product', 'product.owner', 'user')->where(function($q) {
            return $q->where('user_id', auth()->id())->orWhere('merchant_id', auth()->id());
        })->findOrFail($id);

        return view('orders.show', compact('order'));
    }
}
