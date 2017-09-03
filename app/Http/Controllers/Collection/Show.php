<?php

namespace App\Http\Controllers\Collection;

use App\Http\Controllers\Controller;
use App\Entities\Collection;
use Illuminate\Http\Request;

class Show extends Controller
{
    /**
     * Show the collection
     *
     * @return Response
     */
    public function __invoke($id)
    {
        $collection = Collection::with('owner')->findOrFail($id);

        $collection->products = capsule('stream')->whereInCollection($id)->get();

        $this->meta('title', $collection->name);
        $this->meta('description', $collection->description);
        $this->meta('type', 'collection');

        return view('collections.show', compact('collection'));
    }
}