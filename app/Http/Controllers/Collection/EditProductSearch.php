<?php

namespace App\Http\Controllers\Collection;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EditProductSearch extends Controller
{
    /**
     * Search for products on collection edit page
     *
     * @return Response
     */
    public function __invoke(Request $request)
    {
        $query = $request->input('query');
        
        return capsule('stream')->search($query)->items();
    }
}