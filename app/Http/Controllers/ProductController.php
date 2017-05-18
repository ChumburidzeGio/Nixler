<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Entities\Product;
use App\Entities\User;
use App\Entities\ShippingPrice;
use App\Repositories\ProductRepository;
use App\Repositories\UserRepository;

class ProductController extends Controller
{

    /**
     * @var PostRepository
     */
    protected $repository;

    protected $userRepository;

    public function __construct(ProductRepository $repository, UserRepository $userRepository){
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
    public function update($id, Request $request)
    {
        $this->validate($request, [
              'title' => 'required|string|max:180',
              'description' => 'string|nullable',
              'variants' => 'json',
              'action' => 'required|in:schedule,publish',
              'media' => 'json',
              'tags' => 'json',
              'variants' => 'json',
              'category' => 'required|string',
              'in_stock' => 'required|numeric',
              'buy_link' => 'nullable|url',
        ]);

        $this->repository->update($request->all(), $id);

        $isPublish = ($request->input('action') == 'publish');
        
        $status = trans('products.'.($isPublish ? 'published_message' : 'scheduled_message')); 
       
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
        $user = auth()->user();
        $product = $user->products()->findOrFail($id);
        $product->delete();

        return redirect($user->link());
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
    public function order($id, Request $request)
    {
        $user = auth()->user();

        if(!$request->has('step')) {

            $data = $this->repository->getWithShippingByCity($id, $request->all());

            return view('products.order', $data);

        } elseif($request->input('step') == 2) {

            $this->validate($request, [
                'phone' => ['phone:'.$user->country, 'phone_unique:'.$user->country],
                'city_id' => 'required|numeric',
                'address' => 'required|string',
                'quantity' => 'required|numeric',
                'variant' => 'required|nullable|numeric',
            ]);

            $this->userRepository->update($request->all());

            if($user->verified) {

                $order = $this->repository->order($id, $request->input('quantity'), $request->input('variant'));

                return redirect()->route('settings.orders', ['id' => $order->id]);

            }

            return view('products.order-step2', compact('id'));

        } elseif($request->input('step') == 3) {

            $this->validate($request, [
                'pcode' => 'required|numeric|digits:6',
            ]);

            $user = $this->userRepository->update($request->all());

            $order = $this->repository->order($id, $request->input('quantity'), $request->input('variant'));

            return redirect()->route('settings.orders', ['id' => $order->id]);

        }
    }

}
