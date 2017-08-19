<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Entities\Product;
use App\Entities\ProductTag;
use App\Entities\ProductVariant;
use App\Entities\User;
use App\Entities\Order;
use App\Entities\ShippingPrice;
use App\Repositories\ProductRepository;
use App\Repositories\UserRepository;
use App\Http\Requests\UpdateProduct;
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
     * Show the product page
     *
     * @return \Illuminate\Http\Response
     */
    public function show($owner_username, $slug)
    {
        $product = Product::where(compact('slug', 'owner_username'))->firstOrFail();

        if(!$product->is_active) {
            abort_if(auth()->guest() || auth()->user()->cannot('view', $product), 404);
        }

        $product->tags = ProductTag::where('product_id', $product->id)->get();

        $product->setRelation('similar', $this->repository->similar($product));

        $comments = capsule('comments')->whereTarget($product->id)->latest()->get()->items();

        $quantities = [1,2,3,4,5,6,7,8];

        $productFields = $product->setVisible(['id', 'price', 'likes_count', 'comments_count']);

        $liked = $product->isLiked();

        $media = $this->getMedia($product);

        $variants = $this->getVariants($product);

        $jsVars = array_merge($productFields->toArray(), compact('liked', 'quantities', 'variants', 'comments', 'media'));

        capsule('frontend')->addJs(['product' => $jsVars])->addMeta([
            'title' => $product->title.' · '.$product->price_formated,
            'description' => $product->description,
            'image' => $product->photo('full'),
            'type' => 'product'
        ]);

        return view('products.show', compact('product'));
    }

    private function getMedia(Product $product)
    {
        return $product->media('photo')->get()->map(function($item, $key){
            return [
                'thumb' => $item->photo('thumb_s'),
                'full' => $item->photo('full'),
            ];
        });
    }

    private function getVariants(Product $product)
    {
        $variants = ProductVariant::where('product_id', $product->id)->where('in_stock', '>', 0)->orderBy('price')->get();

        return $variants->map(function($item) use ($product) {
            return [
                'id' => $item->id,
                'name' => $item->name.' - '.money($product->currency, $item->price)
            ];
        });
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
        $product = Product::where('owner_id', $request->user()->id)->findOrFail($id);

        $this->repository->sortMedia($request->media, $product);

        $this->repository->syncVariants($request->variants, $product);

        $this->repository->syncTags($request->tags, $product);

        $product->fill([
            'title' => $request->title,
            'description' =>$request->description,
            'category_id' =>$request->category,
            'buy_link' =>$request->buy_link,
            'is_used' =>$request->input('is_used', 0),
            'sku' =>$request->sku,
        ]);

        if(!$product->has_variants)
        {
            $product->price = $request->price;

            $product->in_stock = $request->in_stock;
        }

        $product->save();
        
        if($request->action == 'publish' && $this->authorize('create', $product)) 
        {
            $product->markAsActive();
        } 
        else 
        {
            $product->markAsInactive();
        }

        $isPublish = ($request->action == 'publish');
        
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
    public function import($id, ImportProduct $request)
    {
        $import = $this->repository->import($request->input('url'), $id);

        $status = __('Product from url successfully imported.');

        if(is_null($import)) 
        {
            $status = __('Product import is canceled, you already published it once.');
        }
        elseif(!$import) 
        {
            $status = __('The page doesn\'t contain product information and it can\'t be parsed.');
        }

        return redirect()->route('product.edit', ['id' => $import ? $import->id : $id])->with('status', $status);
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

        event(new ProductDeleted($product, $user));

        $product->delete();

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
    public function sitemap()
    {
        $products = Product::active()->select('updated_at', 'slug', 'owner_username')->get()->map(function($item){

            return [
                'loc' => $item->url(),
                'lastmod' => $item->updated_at->tz('UTC')->toAtomString(),
                'priority' => 0.9,
            ];

        });

        return response()->view('sitemaps.index', [
            'items' => $products,
        ])->header('Content-Type', 'text/xml');
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
