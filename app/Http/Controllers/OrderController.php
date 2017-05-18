<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Entities\Product;
use App\Entities\ShippingPrice;
use App\Entities\Order;
use Ayeo\Price\Price;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use App\Repositories\AddressRepository;
use App\Repositories\PhoneRepository;
use App\Repositories\UserRepository;
use App\Repositories\ProductRepository;

class OrderController extends Controller
{

    /**
     * @var PostRepository
     */
    protected $addressRepo;

    protected $phoneRepo;

    protected $userRepository;

    protected $productRepository;

    public function __construct(AddressRepository $addressRepo, PhoneRepository $phoneRepo, UserRepository $userRepository, ProductRepository $productRepository){
        $this->addressRepo = $addressRepo;
        $this->phoneRepo = $phoneRepo;
        $this->userRepository = $userRepository;
        $this->productRepository = $productRepository;
    }


    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $order = Order::where('user_id', auth()->id())->orWhere('merchant_id', auth()->id())->latest()->first();

        if(!$order){
            return view('orders.index');
        }

        return redirect()->route('settings.orders', ['id' => $order->id]);
    }


    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update($id, Request $request)
    {
        $user = auth()->user();

        $order = Order::where('id', $id)->where('user_id', $user->id)->firstOrFail();

        $status = $request->input('status');

        if($user->can('update-status', [$order, $status])){
            $order->update([
                'status' => $status
            ]);
        }

        return redirect()->route('settings.orders', ['id' => $order->id]);
    }
}
