<?php

namespace Modules\Order\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Modules\Product\Entities\Product;
use Modules\Address\Entities\ShippingPrice;
use Modules\Order\Entities\Order;
use Ayeo\Price\Price;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Modules\Address\Repositories\AddressRepository;
use Modules\User\Repositories\PhoneRepository;

class OrderController extends Controller
{

    /**
     * @var PostRepository
     */
    protected $addressRepo;
    protected $phoneRepo;

    public function __construct(AddressRepository $addressRepo, PhoneRepository $phoneRepo){
        $this->addressRepo = $addressRepo;
        $this->phoneRepo = $phoneRepo;
    }


    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $order = Order::where('user_id', auth()->id())->orWhere('merchant_id', auth()->id())->latest()->first();

        if(!$order){
            return view('order::index');
        }

        return redirect()->route('order.show', ['id' => $order->id]);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create(Request $request)
    {
        $product = Product::findorFail($request->input('product_id'));

        $user = auth()->user();

        $merchant = $product->owner;

        $variants = collect($product->getMeta('variants'));

        $phones = $user->phones()->get()->map(function($item){
            return [
                'id' => $item->id,
                'label' => $item->phone_number,
                'is_verified' => $item->is_verified
            ];
        });

        $hasVerifiedPhone = $phones->filter(function($item){
            return $item['is_verified'];
        })->first();

        $shipping_prices = ShippingPrice::where('user_id', $merchant->id)->get();

        $addresses = $user->addresses()->with('city')->get(['street', 'id', 'city_id', 'country_id']);

        $addresses = $addresses->map(function($address) use ($shipping_prices) {

            $shipping = $shipping_prices->filter(function($item) use ($address) {
                return ($item->type == 'city' && $item->location_id == $address->city_id);
            });

            if(!$shipping){
                $shipping = $shipping_prices->filter(function($item) use ($address) {
                    return ($item->type == 'country' && $item->location_id == $address->country_id);
                });
            }

            $address->shipping = $shipping->map(function($item){
                extract($item->toArray());
                return compact('price', 'window_from', 'window_to');
            })->first();

            return [
                'id' => $address->id,
                'label' => $address->street,
                'shipping' => $address->shipping
            ];

        });

        $country = $user->country()->with('cities.translations')->first();

        return view('order::create', compact('product', 'user', 'merchant', 'variants', 'addresses', 'phones', 'country', 'hasVerifiedPhone'));
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

            $phone = $user->phones()->create([
                'number' => $request->input('phone'),
                'country_code' => $user->country()->first()->calling_code
            ]);

            if(!$phone->verify()){
                $phone->delete();
                return redirect()->back()->withErrors([
                    'phone' => trans('user::settings.phones.created_error_status')
                ]);
            }
        }

        if($request->has('city_id')){
            
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

        if(!$shipping){
            $shipping = $shipping_prices->filter(function($item) use ($address) {
                return ($item->type == 'country' && $item->location_id == $address->country_id);
            });
        }

        $shipping = $shipping->first();

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

        return redirect()->route('order.show', ['id' => $order->id]);
        
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show($id)
    {
        $user = auth()->user();

        $order = Order::where('id', $id)->where('user_id', $user->id)->with('product', 'product.owner', 'address')->firstOrFail();

        $orders = Order::where('user_id', $user->id)->orWhere('merchant_id', $user->id)->with('product')->latest()->paginate();

        return view('order::show', compact('user', 'order', 'orders'));
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
        return view('order::edit');
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

        return redirect()->route('order.show', ['id' => $order->id]);
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy()
    {
    }
}
