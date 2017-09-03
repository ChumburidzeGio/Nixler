<?php

namespace App\Http\Controllers;

use App\Entities\User;
use App\Entities\Order;
use App\Entities\Article;
use App\Entities\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Crawler\BasePattern;

class ManagementController extends Controller
{
    public function users()
    {
       $users = User::latest()->paginate();

       return view('management.users', compact('users'));
    }

    public function products()
    {
       $products = Product::latest()->paginate();

       return view('management.products', compact('products'));
    }

    public function orders()
    {
       $orders = Order::latest()->with('product')->whereHas('product')->paginate();

       return view('management.orders', compact('orders'));
    }

    public function articles()
    {
       $articles = Article::latest()->paginate();

       return view('management.articles', compact('articles'));
    }

    public function calculators()
    {
        $pattern = app(BasePattern::class);

        return $pattern->calculatePrice(
            request('from'), 
            request('to'), 
            request('price')
        );
    }
}