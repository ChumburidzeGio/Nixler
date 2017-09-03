<?php

namespace App\Http\Controllers\Collection;

use App\Http\Controllers\Controller;
use App\Entities\CollectionItem;
use App\Entities\Collection;
use Illuminate\Http\Request;

class Delete extends Controller
{
    /**
     * Delete the collection
     *
     * @return Response
     */
    public function __invoke(Request $request)
    {
        $collection = Collection::where('id', $request->id)->where('user_id', $request->user()->id)->firstOrFail();

        CollectionItem::where('collection_id', $collection->id)->delete();

        $collection->delete();

        return redirect('/');
    }
}