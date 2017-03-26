<?php

namespace Modules\Address\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Modules\Address\Entities\Country;
use Modules\Address\Entities\UserAddress;

class AddressController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $country = Country::where('iso_code', auth()->user()->country)->with('cities.translations')->first();

        $addresses = UserAddress::where('user_id', auth()->id())->with('city')->get();

        return view('address::index', compact('addresses', 'country'));
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
            'name' => 'required',
            'city_id' => 'required|numeric',
            'post_code' => 'required|numeric',
            'street' => 'required|string',
            'phone' => 'required|numeric',
            'note' => 'nullable|string',
        ]);

        $user = auth()->user();
        $country = Country::where('iso_code', $user->country)->first();

        UserAddress::updateOrCreate([
            'user_id' => $user->id,
            'country_id' => $country->id,
            'city_id' => $request->input('city_id'),
            'post_code' => $request->input('post_code'),
            'street' => $request->input('street'),
            'phone' => $request->input('phone'),
        ], [
            'name' => $request->input('name'),
            'note' => $request->input('note'),
        ]);

        return redirect()->route('settings.addresses');
        
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
        $address = UserAddress::where('user_id', auth()->id())->firstOrFail();

        return view('address::edit', compact('address'));
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update($id, Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'post_code' => 'required|numeric',
            'street' => 'required|string',
            'phone' => 'required|numeric',
            'note' => 'nullable|string',
        ]);

        $user = auth()->user();
        
        $address = UserAddress::where('id', $id)->where('user_id', $user->id)->firstOrFail();

        $address->update([
            'post_code' => $request->input('post_code'),
            'street' => $request->input('street'),
            'phone' => $request->input('phone'),
            'name' => $request->input('name'),
            'note' => $request->input('note'),
        ]);

        return redirect()->route('settings.addresses.update', ['id' => $address->id]);
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy($id)
    {
        $address = UserAddress::where('id', $id)->where('user_id', auth()->id())->firstOrFail();

        return [
            'success' => $address->delete()
        ];
    }
}
