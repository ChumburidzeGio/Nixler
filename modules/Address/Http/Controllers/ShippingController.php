<?php

namespace Modules\Address\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Modules\Address\Entities\Country;
use Modules\Address\Entities\ShippingPrice;

class ShippingController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $user = auth()->user();

        $country_code = $user->country;

        $country = Country::where('iso_code', $country_code)->with('cities', 'cities.translations')->first();

        $prices = ShippingPrice::where('user_id', auth()->id())->where('type', 'city')->with('city', 'city.translations')->get();

        if($user->getMeta('delivery_full')){
            $country_price = ShippingPrice::firstOrCreate([
                'user_id' => auth()->id(),
                'location_id' => $country->id,
                'type' => 'country'
            ], [
                'price' => 0,
                'window_from' => 1,
                'window_to' => 3
            ]);
        } else {
            $country_price = [];
        }

        return view('address::settings.shipping', compact('prices', 'country', 'country_price'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('address::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
              'location_id' => 'required',
              'price' => 'nullable|numeric|between:0,150000',
              'window_from' => 'required|numeric|between:0,99',
              'window_to' => 'required|numeric|min:'.$request->input('window_from').'|max:99',
        ]);

        $country = ShippingPrice::updateOrCreate([
            'user_id' => auth()->id(),
            'location_id' => $request->input('location_id'),
            'type' => 'city'
        ], [
            'price' => $request->input('price') ? : 0,
            'window_from' => $request->input('window_from'),
            'window_to' => $request->input('window_to'),
        ]);

        return redirect()->route('shipping.settings');
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
        return view('address::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update($id, Request $request)
    {
        $shipping = ShippingPrice::findOrFail($id);

        $this->validateWithBag($shipping->sid, $request, [
              'location_id' => 'required',
              'price' => 'nullable|numeric|between:0,150000',
              'type' => 'required|in:city,country',
              'window_from' => 'required|numeric|between:0,99',
              'window_to' => 'required|numeric|min:'.$request->input('window_from').'|max:99',
        ]);

        if($request->input('action') == 'save'){

            $updated = $shipping->update([
                'price' => $request->input('price') ? : 0,
                'window_from' => $request->input('window_from'),
                'window_to' => $request->input('window_to'),
            ]);

        } elseif ($request->input('action') == 'delete' && $request->input('type') == 'country'){
            $shipping->delete();
        }

        return redirect()->route('shipping.settings');
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function updateGeneral(Request $request)
    {
        $this->validate($request, [
              'delivery_full' => 'required|in:1,0',
              'has_return' => 'required|in:1,0',
              'policy' => 'nullable|string',
        ]);
        
        $user = auth()->user();
        $user->setMeta('delivery_full', $request->input('delivery_full'));
        $user->setMeta('has_return', $request->input('has_return'));
        $user->setMeta('return_policy', $request->input('policy'));
        $user->save();

        if(!$request->input('delivery_full')){

            $country = Country::where('iso_code', $user->country)->first();

            $country_price = ShippingPrice::where([
                'user_id' => auth()->id(),
                'location_id' => $country->id,
                'type' => 'country'
            ])->delete();

        }

        return redirect()->route('shipping.settings');
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy()
    {
    }
}
