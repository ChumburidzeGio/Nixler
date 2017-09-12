<?php

namespace App\Http\Controllers\Collection;

use App\Http\Controllers\Controller;
use App\Entities\Collection;
use Illuminate\Http\Request;

class Edit extends Controller
{
    /**
     * Show the collection create/update page
     *
     * @param $id {null|int}
     * @return Response
     */
    public function __invoke($id = null)
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

            $collection->is_private = $collection->is_private ? 2 : 1;

            $items = capsule('stream')->whereInCollection($id)->items();
        }

        $privacyOptions = [
            [
                'key' => 1,
                'label' => __('Public')
            ],
            [
                'key' => 2,
                'label' => __('Only me')
            ]
        ];

        $collection->items = $items;

        return $this->view('collections-update', compact('collection', 'privacyOptions'));
    }
}