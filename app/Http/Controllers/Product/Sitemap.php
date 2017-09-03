<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Entities\Product;

class Sitemap extends Controller
{
    /**
     * Like the product
     *
     * @param  int  $id
     * @return Response
     */
    public function __invoke($id, Request $request)
    {
        $items = Product::active()->select('updated_at', 'slug', 'owner_username')->get();

        $items->transform(function ($item) {

            return [
                'loc' => $item->url(),
                'lastmod' => $item->updated_at->tz('UTC')->toAtomString(),
                'priority' => 0.9,
            ];

        });

        return response()->view('sitemaps.index', compact('items'))->header('Content-Type', 'text/xml');
    }
}