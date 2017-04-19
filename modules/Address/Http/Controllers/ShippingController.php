<?php

namespace Modules\Address\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Modules\Address\Entities\Country;
use Modules\Address\Entities\ShippingPrice;
use Modules\Address\Repositories\ShippingRepository;

class ShippingController extends Controller
{

    /**
     * @var PostRepository
     */
    protected $repository;

    public function __construct(ShippingRepository $repository){
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('address::settings.shipping', $this->repository->all());
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
        
        $this->repository->settingsUpdate($request->all());

        return redirect()->route('shipping.settings');
    }

}
