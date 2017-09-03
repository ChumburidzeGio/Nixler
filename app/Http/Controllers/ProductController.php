<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Entities\Product;
use App\Entities\ProductTag;
use App\Entities\ProductSource;
use App\Entities\ProductVariant;
use App\Entities\ProductCategory;
use App\Entities\User;
use App\Entities\Order;
use App\Entities\ShippingPrice;
use App\Repositories\ProductRepository;
use App\Repositories\UserRepository;
use App\Http\Requests\ImportProduct;
use App\Http\Requests\OrderProduct;

class ProductController extends Controller
{

    /**
     * @var PostRepository
     */
    protected $repository;

    protected $userRepository;

    public function __construct(ProductRepository $repository, UserRepository $userRepository){
        parent::__construct();
        $this->repository = $repository;
        $this->userRepository = $userRepository;
    }

    /**
     * Create new product
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $product = $this->repository->create();

        return redirect('/products/'.$product->id.'/edit');
    }

    /**
     * Import product from URL
     *
     * @return \Illuminate\Http\Response
     */
    public function import(ImportProduct $request)
    {
        $import = $this->repository->import($request->input('url'));

        return redirect()->route('stock');
    }

    /**
     * Show the product page
     *
     * @return \Illuminate\Http\Response
     */
    public function changeStatus($id)
    {
        $user = auth()->user();
        $product = $user->products()->findOrFail($id);

        if($product->is_active){
            $product->markAsInactive();
        } elseif($product->is_inactive) {
            $product->markAsActive();
        }

        return redirect($product->link('/edit'));
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function commitOrder($id, Request $request)
    {
        $user = auth()->user();

        $order = Order::where([
                'id' => $id,
                'user_id' => $user->id
            ])->orWhere([
                'id' => $id,
                'merchant_id' => $user->id
            ])->firstOrFail();

        $status = $request->input('status');

        if($user->can('update-status', [$order, $status])){
            $order->update([
                'status' => $status
            ]);
        }

        return redirect()->route('orders.show', ['id' => $order->id]);
    }


    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function stock()
    {
        $user = auth()->user();

        $products = Product::where('owner_id', $user->id)->latest('id')->paginate(20);

        return view('products.stock', compact('products'));
    }

    /**
     * Get the user profile
     *
     * @return \Illuminate\Http\Response
     */
    public function shortlink($id)
    {
        $product = Product::select('slug', 'owner_username')->findOrFail($id);

        return redirect()->route('product', [
            'id' => $product->slug,
            'uid' => $product->owner_username,
        ]);
    }

}
