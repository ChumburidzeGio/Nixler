<?php

namespace Modules\Order\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Modules\Product\Entities\Product;
use Modules\Address\Entities\ShippingPrice;
use Modules\Order\Entities\Order;
use Ayeo\Price\Price;
use Carbon\Carbon;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $order = Order::where('user_id', auth()->id())->orWhere('merchant_id', auth()->id())->latest()->first();

        if(!$order){
            return view('order::index');
        }

        return redirect()->route('order.show', ['id' => $order->id]);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create(Request $request)
    {
        $product = Product::findorFail($request->input('product_id'));

        $user = auth()->user();

        $merchant = $product->owner;

        $variants = collect($product->getMeta('variants'));

        $shipping_prices = ShippingPrice::where('user_id', $merchant->id)->get();

        $addresses = $user->addresses()->get(['name', 'street', 'id', 'city_id', 'country_id']);

        $addresses = $addresses->map(function($address) use($shipping_prices){

            $shipping = $shipping_prices->filter(function($item) use ($address) {
                return ($item->type == 'city' && $item->location_id == $address->city_id);
            });

            if(!$shipping){
                $shipping = $shipping_prices->filter(function($item) use ($address) {
                    return ($item->type == 'country' && $item->location_id == $address->country_id);
                });
            }

            $address->shipping = $shipping->map(function($item){
                extract($item->toArray());
                return compact('price', 'window_from', 'window_to');
            })->first();

            return $address;

        });

        return view('order::create', compact('product', 'user', 'merchant', 'variants', 'addresses'));
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        //validation for request, currency, if in stock

        $product = Product::findorFail($request->input('product_id'));

        $merchant = $product->owner;

        $user = auth()->user();

        $address = $user->addresses()->findOrFail($request->input('address'));

        $shipping_prices = ShippingPrice::where('user_id', $merchant->id)->get();

        $shipping = $shipping_prices->filter(function($item) use ($address) {
            return ($item->type == 'city' && $item->location_id == $address->city_id);
        });

        if(!$shipping){
            $shipping = $shipping_prices->filter(function($item) use ($address) {
                return ($item->type == 'country' && $item->location_id == $address->country_id);
            });
        }

        $shipping = $shipping->first();

        $quantity = $request->input('quantity');
        $variant = $request->input('variant');
        $comment = $request->input('comment');

        $product_price = Price::buildByGross($product->price, 0, $product->currency);

        $products_price =  $product_price->multiply($quantity);

        $shipping_cost = Price::buildByGross($shipping->price, 0, $merchant->currency);

        $amount = $products_price->add($shipping_cost)->getGross();

        $shipping_window_from = Carbon::now()->addDays($shipping->window_from);
        $shipping_window_to = Carbon::now()->addDays($shipping->window_to);

        $order = Order::create([
            'status' => 'created',
            'amount' => $amount,
            'currency' => $user->currency,
            'quantity' => $quantity,
            'address_id' => $address->id,
            'shipping_cost' => $shipping_cost->getGross(),
            'shipping_window_from' => $shipping_window_from,
            'shipping_window_to' => $shipping_window_to,
            'payment_method' => 'COD',
            'user_id' => $user->id,
            'product_id' => $product->id,
            'product_variant' => $variant,
            'merchant_id' => $merchant->id,
            'note' => $comment
        ]);

        return redirect()->route('order.show', ['id' => $order->id]);
        
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show($id)
    {
        $user = auth()->user();

        $order = Order::where('id', $id)->where('user_id', $user->id)->with('product', 'product.owner', 'address')->firstOrFail();

        $orders = Order::where('user_id', $user->id)->orWhere('merchant_id', $user->id)->with('product')->latest()->paginate();

        return view('order::show', compact('user', 'order', 'orders'));
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
        return view('order::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update($id, Request $request)
    {
        $user = auth()->user();

        $order = Order::where('id', $id)->where('user_id', $user->id)->firstOrFail();

        $status = $request->input('status');

        if($user->can('update-status', [$order, $status])){
            $order->update([
                'status' => $status
            ]);
        }

        return redirect()->route('order.show', ['id' => $order->id]);
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy()
    {
    }
}
