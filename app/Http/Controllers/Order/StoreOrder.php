<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Ayeo\Price\Price;
use Carbon\Carbon;
use App\Entities\Order;
use App\Entities\Product;
use App\Entities\ProductVariant;
use App\Repositories\ProductRepository;
use App\Http\Requests\OrderProduct;
use App\Events\OrderCreated;
use App\Notifications\OrderStatusChanged;

class StoreOrder extends Controller
{
    /**
     * Store the order
     *
     * @param  int  $id
     * @return Response
     */
    public function __invoke($id, OrderProduct $request)
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
            'payment_method' => strtoupper($request->payment_method),
            'user_id' => $user->id,
            'product_id' => $product->id,
            'product_variant' => $variant ? $variant->name : null,
            'merchant_id' => $product->owner_id,
            'city_id' => $request->city_id,
            'phone' => $request->phone,
            'title' => $product->title,
            'payment_status' => 'unpayed',
        ]);

        //Payment 
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

        if($order->payment_method == 'CRD')
        {
            return redirect('orders.payments.cartu.redirect', [
                'id' => $order->id
            ]);
        }

        return redirect()->route('orders.show', ['id' => $order->id])->with('flash', 'thanks');
    }
}