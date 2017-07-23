<?php

namespace App\Http\Controllers;

use App\Entities\Product;
use App\Entities\Collection;
use App\Entities\CollectionItem;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

class CollectionsController extends Controller
{
    /**
     * Show the collection create page
     *
     * @return \Illuminate\Http\Response
     */
    public function update($id = null)
    {
        if(is_null($id)) {

            $collection = new Collection;

            $collection->id = null;

            $collection->name = old('name');

            $collection->is_private = old('is_private', 1);

            $collection->description = old('description');

            $items = json_decode(old('items')) ?? [];

            if(count($items)) {
                $items = capsule('stream')->whereIds($items)->items();
            }

        } else {

            $collection = Collection::findOrFail($id);

            $items = capsule('stream')->whereInCollection($id)->items();
        }

        $privacyOptions = [
            [
                'key' => 0,
                'label' => __('Public')
            ],
            [
                'key' => 1,
                'label' => __('Private')
            ]
        ];

        $collection->items = $items;

        return view('collections.update', compact('collection', 'privacyOptions'));
    }


    /**
     * Show the collection create page
     *
     * @return \Illuminate\Http\Response
     */
    public function productSearch()
    {
        return capsule('stream')->search(request('query'))->items();
    }


    /**
     * Save latest changes to collection or create new one
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request->id) {

            $collection = Collection::findOrFail($request->id);

        } else {

            $collection = new Collection;

        }

        $collection->name = $request->name;

        $collection->description = $request->description;

        $collection->is_private = $request->is_private;

        $collection->user_id = $request->user()->id;

        $collection->save();

        $items = json_decode($request->items) ?? [];

        foreach ($items as $key => $value) {
            
            CollectionItem::updateOrCreate([
                'collection_id' => $collection->id,
                'product_id' => $value
            ], [
                'order' => $key
            ]);

        }

        CollectionItem::where('collection_id', $collection->id)->whereNotIn('product_id', $items)->delete();

        $firstProduct = CollectionItem::where('collection_id', $collection->id)->first();

        if($firstProduct) {

            $product = Product::where('id', $firstProduct->product_id)->first();

            $collection->update([
                'media_id' => $product->media_id
            ]);

        }

        return redirect()->route('collections.update', [
            'id' => $collection->id
        ]);
    }


    /**
     * Delete collection
     *
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        $collection = Collection::where('id', $request->id)->where('user_id', $request->user()->id)->firstOrFail();

        CollectionItem::where('collection_id', $collection->id)->delete();

        $collection->delete();

        return redirect('/');
    }


    /**
     * Show the collection page
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $collection = Collection::with('owner')->findOrFail($id);

        $collection->products = capsule('stream')->whereInCollection($id)->get();

        $this->meta('title', $collection->name);
        $this->meta('description', $collection->description);
        //$this->meta('image', $product->photo('full'));
        $this->meta('type', 'collection');

        return view('collections.show', compact('collection'));
    }

}