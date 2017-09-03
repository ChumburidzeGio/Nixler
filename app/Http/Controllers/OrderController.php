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

        capsule('frontend')->addJs(compact('cities', 'price', 'city_id'));

        return view('products.order', compact('product'));
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
