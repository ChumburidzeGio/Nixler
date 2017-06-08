<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Entities\Product;
use App\Entities\User;
use App\Entities\Order;
use App\Entities\ShippingPrice;
use App\Repositories\ProductRepository;
use App\Repositories\UserRepository;
use App\Http\Requests\UpdateProduct;
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
     * Show the product page
     *
     * @return \Illuminate\Http\Response
     */
    public function find($uid, $id)
    {
        $product = $this->repository->findBySlug($id, $uid);

        $this->meta('title', "{$product->title} · {$product->price_formated}");
        $this->meta('description', $product->description);
        $this->meta('image', $product->photo('full'));
        $this->meta('type', 'product');

        return view('products.show', compact('product'));
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
     * Show the product page
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {   
        return view('products.edit', $this->repository->edit($id));
    }


    /**
     * Show the product page
     *
     * @return \Illuminate\Http\Response
     */
    public function update($id, UpdateProduct $request)
    {
        $this->repository->update($request->all(), $id);

        $isPublish = ($request->input('action') == 'publish');
        
        $status = $isPublish ? 
            __('Your product has been saved, you can anytime publish it from this page or "My Products" section.') : 
            __('Your product has been updated and is now live. Do you want to add another one or go to product page?');

        return redirect()->route('product.edit', ['id' => $id])->with('status', $status)->with('buttons', $isPublish);
    }

    /**
     * Show the product page
     *
     * @return \Illuminate\Http\Response
     */
    public function uploadPhoto($id, Request $request)
    {
        $media = $this->repository->uploadMediaForProduct($id, $request->file('file'));

        return [
            'success' => true,
            'id' => $media->id,
            'thumb' => $media->photo('thumb')
        ];
    }

    /**
     * Show the product page
     *
     * @return \Illuminate\Http\Response
     */
    public function removePhoto($product_id, $media_id)
    {
        $success = $this->repository->removeMediaFromProductById($product_id, $media_id);

        return compact('success');
    }

    /**
     * Show the product page
     *
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        $this->repository->delete($id);

        return redirect('/');
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
     * Like product
     *
     * @return \Illuminate\Http\Response
     */
    public function like($id)
    {
        return [
            'success' => $this->repository->like($id)
        ];
    }

    /**
     * Order product
     *
     * @return \Illuminate\Http\Response
     */
    public function order($id, OrderProduct $request)
    {
        $user = auth()->user();

        if(!$request->has('step')) {

            $data = $this->repository->getWithShippingByCity($id, $request->all());

            return view('products.order', $data);

        } elseif($request->input('step') == 2) {

            $this->userRepository->update(
                $request->only(['phone', 'city_id', 'address', 'quantity', 'variant'])
            );

            if($user->verified) {

                $order = $this->repository->order($id, $request->input('quantity'), $request->input('variant'));

                return redirect()->route('settings.orders', ['id' => $order->id]);
            }

            return view('products.order-step2', compact('id'));

        } elseif($request->input('step') == 3) {

            $user = $this->userRepository->update($request->only(['pcode']));

            $order = $this->repository->order($id, $request->input('quantity'), $request->input('variant'));

            return redirect()->route('settings.orders', ['id' => $order->id]);

        }
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function commitOrder($id, Request $request)
    {
        $user = auth()->user();

        $order = Order::where('id', $id)->where('user_id', $user->id)->firstOrFail();

        $status = $request->input('status');

        if($user->can('update-status', [$order, $status])){
            $order->update([
                'status' => $status
            ]);
        }

        return redirect()->route('settings.orders', ['id' => $order->id]);
    }

}
