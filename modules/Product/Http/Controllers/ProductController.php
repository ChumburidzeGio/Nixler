<?php

namespace Modules\Product\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Modules\Product\Entities\Product;
use Modules\User\Entities\User;
use Modules\Address\Entities\ShippingPrice;
use Modules\Product\Repositories\ProductRepository;

class ProductController extends Controller
{

    /**
     * @var PostRepository
     */
    protected $repository;

    public function __construct(ProductRepository $repository){
        $this->repository = $repository;
    }


    /**
     * Show the product page
     *
     * @return \Illuminate\Http\Response
     */
    public function find($uid, $id)
    {
        $merchant = User::whereUsername($uid)->firstOrFail();
        $product = $merchant->products()->whereSlug($id)->firstOrFail();

        if(!$product->is_active && auth()->id() !== $merchant->id){
            abort(404);
        }

        $shipping_prices = ShippingPrice::where('user_id', $merchant->id)->get();
        $shipping_prices->load('location');

        if(auth()->check()){

            $user = auth()->user();

            $addresses = $user->addresses()->with('city')->get(['street', 'id', 'city_id', 'country_id']);

            $addresses = $addresses->map(function($address) use ($shipping_prices) {

                $shipping = $shipping_prices->filter(function($item) use ($address) {
                    return ($item->type == 'city' && $item->location_id == $address->city_id);
                });

                if(!$shipping->count()){
                    $shipping = $shipping_prices->filter(function($item) use ($address) {
                        return ($item->type == 'country' && $item->location_id == $address->country_id);
                    });
                }

                $address->shipping = $shipping->map(function($item){
                    extract($item->toArray());
                    return compact('price', 'currency', 'window_from', 'window_to');
                })->first();

                return [
                    'id' => $address->id,
                    'label' => $address->street,
                    'shipping' => $address->shipping
                ];

            });

        } elseif (auth()->guest() && $shipping_prices->count() > 2) {
            $shipping_prices->take(2);
        }

        $product->setRelation('media', $product->media('photo')->take(10)->get());
        $product->setRelation('comments', $product->comments()->sortBy('most_recent')->paginate());
        $product->setRelation('tags', $product->tags('tags')->take(3)->get());
        $product->setRelation('variants', $product->tags('variants')->get());
        $product->load('category');

        $product->trackActivity('product:viewed');

        $jComments = $product->comments->map(function($comment){
            return [
                'id' => $comment->id,
                'avatar' => $comment->author->avatar('comments'),
                'author' => $comment->author->name,
                'text' => nl2br(str_limit($comment->text, 1000)),
                'time' => $comment->created_at->format('c'),
                'can_delete' => auth()->check() && auth()->user()->can('delete', $comment) ? 1 : 0
            ];
        })->toJson();

        $similar = $this->repository->similar($product->id);

        return view('product::item', compact('product', 'merchant', 'jComments', 'similar', 'shipping_prices', 'addresses'));
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
        return view('product::edit', $this->repository->edit($id));
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
              'category' => 'required|string',
              'in_stock' => 'required|numeric',
              'buy_link' => 'required|url',
        ]);

        $this->repository->update($request->all(), $id);

        $isPublish = ($request->input('action') == 'publish');
        
        $status = trans('product::update.'.($isPublish ? 'published_message' : 'scheduled_message')); 
       
        return redirect()->route('product.edit', ['id' => $id])->with('status', $status)->with('buttons', $isPublish);
    }

    /**
     * Show the product page
     *
     * @return \Illuminate\Http\Response
     */
    public function uploadPhoto($id, Request $request)
    {
        $user = auth()->user();
        $product = $user->products()->findOrFail($id);
        $media = $product->uploadPhoto($request->file('file'), 'photo');


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
    public function removePhoto($id, $media_id, Request $request)
    {
        $user = auth()->user();
        $product = $user->products()->findOrFail($id);
        $media = $product->media()->findOrFail($media_id);
        $media->delete();

        return [
            'success' => true,
        ];
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

}
