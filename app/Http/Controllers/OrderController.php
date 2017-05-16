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

        return redirect()->route('order.show', ['id' => $order->id]);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create(Request $request)
    {
        $product = Product::findOrFail($request->input('product_id'));

        info('Product order clicked', ['product_id' => $product->id]);

        $user = auth()->user();

        $phones = $this->userRepository->getContactDetails('phones', $user)->map(function($item){
            return [
                'id' => $item->id,
                'label' => $item->phone_number,
                'is_verified' => $item->is_verified
            ];
        });

        $hasVerifiedPhone = $this->userRepository->hasVerifiedContactDetails('phones', $user);

        $this->productRepository->calculateShippingPriceForProduct($product);

        $country = $user->country()->with('cities.translations')->first();

        return view('orders.create', compact('product', 'user', 'merchant', 'addresses', 'phones', 'country', 'hasVerifiedPhone', 'shipping_prices'));
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {

        $user = auth()->user();

        if($request->has('phone')){

            $this->validate($request, [
                  'phone' => ['required', 'numeric', Rule::unique('user_phones', 'number')]
            ]);

            $phone = $this->phoneRepo->create($request->input('phone'));

            if(!$phone){
                return redirect()->back()->withErrors([
                    'phone' => trans('user::settings.phones.created_error_status')
                ]);
            }
        }

        if($request->has('city_id')) {
            
            $this->validate($request, [
                'city_id' => 'required|numeric',
                'street' => 'required|string',
            ]);

            $this->addressRepo->create($request->all());

        }

        if($request->has('phone') || $request->has('city_id')){
            return redirect()->back();
        }

        if($request->has('phone_id')){

            $phone = $this->phoneRepo->find($request->input('phone_id'));

            if(!$phone->is_verified){

                $phone_verified = $this->phoneRepo->verificationCheck(
                    $request->input('phone_id'),
                    $request->input('code')
                );

                if(!$phone_verified){
                    return redirect()->back()->withErrors([
                        'code' => trans('user::settings.phones.wrong_code_status')
                    ]);
                }

            }

        }

        $this->validate($request, [
              'product_id' => 'required',
              'quantity' => 'required|numeric|between:1,50',
              'variant' => 'nullable|string',
              'address_id' => 'required|numeric',
              'comment' => 'nullable|string',
        ]);

        $product = Product::findorFail($request->input('product_id'));

        if($user->currency !== $product->currency){
            redirect()->back();
        }

        $merchant = $product->owner;

        $address = $user->addresses()->findOrFail($request->input('address_id'));

        $shipping_prices = ShippingPrice::where('user_id', $merchant->id)->get();

        $shipping = $shipping_prices->filter(function($item) use ($address) {
            return ($item->type == 'city' && $item->location_id == $address->city_id);
        });

        if(!$shipping->count()){
            $shipping = $shipping_prices->filter(function($item) use ($address) {
                return ($item->type == 'country' && $item->location_id == $address->country_id);
            });
        }

        $shipping = $shipping->first();

        if(!$shipping){
            return redirect()->back();
        }

        $quantity = $request->input('quantity');
        $variant = $request->input('variant');
        $comment = $request->input('comment');

        $product_price = Price::buildByGross($product->price, 0, $product->currency);

        $products_price =  $product_price->multiply($quantity);

        $shipping_cost = Price::buildByGross($shipping->price, 0, $merchant->currency);

        $amount = $products_price->add($shipping_cost)->getGross();

        $shipping_window_from = Carbon::now()->addDays($shipping->window_from);
        $shipping_window_to = Carbon::now()->addDays($shipping->window_to);

        $order = Order::create([
            'status' => 'created',
            'amount' => $amount,
            'currency' => $user->currency,
            'quantity' => $quantity,
            'address_id' => $address->id,
            'shipping_cost' => $shipping_cost->getGross(),
            'shipping_window_from' => $shipping_window_from,
            'shipping_window_to' => $shipping_window_to,
            'payment_method' => 'COD',
            'user_id' => $user->id,
            'product_id' => $product->id,
            'product_variant' => $variant,
            'merchant_id' => $merchant->id,
            'note' => $comment
        ]);

        $product->decrement('in_stock', $quantity);
        $product->update();
        
        return redirect()->route('order.show', ['id' => $order->id]);
        
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
