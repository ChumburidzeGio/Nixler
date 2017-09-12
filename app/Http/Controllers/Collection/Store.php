<?php

namespace App\Http\Controllers\Collection;

use App\Http\Controllers\Controller;
use App\Entities\CollectionItem;
use App\Entities\Collection;
use Illuminate\Http\Request;
use App\Entities\Product;

class Store extends Controller
{
    /**
     * Save latest changes to collection or create new one
     *
     * @return Response
     */
    public function __invoke(Request $request)
    {
        if($request->id) {

            $collection = Collection::findOrFail($request->id);

        } else {

            $collection = new Collection;

        }

        $collection->name = $request->name;

        $collection->description = $request->description;

        $collection->is_private = ($request->is_private == 2 ? true : false);

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

        return redirect()->route('pages.collections-update.template', [
            'id' => $collection->id
        ]);
    }
}