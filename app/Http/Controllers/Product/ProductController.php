<?php

namespace App\Http\Controllers\Product;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Nixler\Sellable\Models\Sellable as Product;
use App\User;
use Mobile_Detect;

class ProductController extends Controller
{
    /**
     * Show the product page
     *
     * @return \Illuminate\Http\Response
     */
    public function find($uid, $id)
    {
        $merchant = User::whereUsername($uid)->firstOrFail();
        $product = $merchant->products()->whereSlug($id)->firstOrFail();
        $media = $product->media('photo')->take(15)->get();

        return view('product.item', compact('product', 'merchant', 'media'));
    }

    /**
     * Show the product page
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = auth()->user();
        $product = $user->createProduct('pln');
        return redirect('/products/'.$product->id.'/edit');
    }

    /**
     * Show the product page
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {        
        $user = auth()->user();
        $product = $user->products()->findOrFail($id);

        $categories = $product->categories();

        $variants = json_encode(collect($product->getMeta('variants'))->mapWithKeys(function ($item, $key) {
            return [$key => ['text' => $item]];
        }));

        $media = $product->getMedia('photo')->map(function($media){
            return [
                'success' => true,
                'id' => $media->id,
                'thumb' => url('media/'.$media->id.'/avatar/profile.jpg')
            ];
        });

        $product->category = $product->getMeta('category');

        return view('product.edit', compact('product', 'media', 'variants', 'categories'));
    }


    /**
     * Show the product page
     *
     * @return \Illuminate\Http\Response
     */
    public function update($id, Request $request)
    {
        $this->validate($request, [
              'title' => 'required|string|max:150',
              'description' => 'string|nullable',
              'variants' => 'json',
              'action' => 'required|in:schedule,publish',
              'media' => 'json',
              'category' => 'required|numeric'
        ]);

        $product = auth()->user()->products()->findOrFail($id);

        $product->fill([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'price' => $request->input('price')
        ]);

        //Media
        $media_sorted = json_decode($request->input('media'));
        $media = $product->getMedia('photo');
        $media = $media->sortBy(function ($photo, $key) use ($media_sorted) {
            foreach ($media_sorted as $key => $value) {
                if($value->id == $photo->id){
                    return $key;
                }
            }
        });
        $product->syncMedia($media, 'photo');

        //Variants
        $variants = collect(json_decode($request->input('variants'), 1))->flatten();
        $product->setMeta('variants', $variants);

        $product->setMeta('category', $request->input('category'));

        $product->save();
        
        if($request->input('action') == 'schedule') {
            
            return redirect($product->link('/edit'))->with('status', 'Your product has been saved, you can anytime publish it from this page or "My Products" section.');

        } else {

            $product->markAsActive();

            return redirect($product->link('/edit'))->with('status', 'Your product has been updated and is now live. Do you want to add another one or go to product page?')->with('buttons', true);
        }
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
     * Show the product page
     *
     * @return \Illuminate\Http\Response
     */
    public function like($id)
    {
        $product = Product::findOrFail($id);
        $detect = new Mobile_Detect;
        $user = auth()->user();

        $liked = $product->like([
            'gender' => $product->getMeta('gender'),
            'is_mobile' => $detect->isMobile(),
            'age_range' => $product->getMeta('age_range'),
            'country' => $user->country,
            'city' => null
        ]);

        return [
            'success' => $liked
        ];
    }
}
