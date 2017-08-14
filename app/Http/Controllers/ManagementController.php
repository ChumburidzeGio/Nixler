<?php

namespace App\Http\Controllers;

use App\Entities\User;
use App\Entities\Order;
use App\Entities\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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
}