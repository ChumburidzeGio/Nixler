<?php

namespace Modules\Address\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Modules\Address\Entities\Country;
use Modules\Address\Entities\UserAddress;
use Modules\Address\Repositories\AddressRepository;

class AddressController extends Controller
{

    /**
     * @var PostRepository
     */
    protected $repository;

    public function __construct(AddressRepository $repository){
        $this->repository = $repository;
    }


    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return view('address::index', $this->repository->all());
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  Request $request
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'city_id' => 'required|numeric',
            'post_code' => 'required|postcode',
            'street' => 'required|string',
            'phone' => 'required|numeric',
            'note' => 'nullable|string',
        ]);

        $this->repository->create($request->all());

        return redirect()->route('settings.addresses');
        
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @return Response
     */
    public function edit($id)
    {
        return view('address::edit', $this->repository->edit($id));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  Request $request
     *
     * @return Response
     */
    public function update($id, Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'post_code' => 'required|postcode',
            'street' => 'required|string',
            'phone' => 'required|numeric',
            'note' => 'nullable|string',
        ]);

        $this->repository->update($request->all(), $id);

        return redirect()->route('settings.addresses.update', ['id' => $id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return Response
     */
    public function destroy($id)
    {
        return [
            'success' => $this->repository->destroy($id)
        ];
    }
}
