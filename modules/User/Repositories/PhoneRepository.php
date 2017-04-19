<?php

namespace Modules\User\Repositories;

use App\Repositories\BaseRepository;
use Modules\User\Entities\Phone;
use App\Services\Phone as PhoneService;

class PhoneRepository extends BaseRepository {


    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return Phone::class;
    }


    /**
     * Create new phone model
     *
     * @return \Illuminate\Http\Response
     */
    public function create($number, $user = null)
    {
        $user = $user ? : auth()->user();

        $phone = PhoneService::parse($number, $user->country);

        $model = $user->phones()->create([
            'number' => $phone->number,
            'country_code' => $phone->country_code
        ]);

        if(!$model->verify()){
            $model->delete();
            return false;
        }

        return $model;
    }


    /**
     * Verify with code the phone
     *
     * @return \Illuminate\Http\Response
     */
    public function verificationCheck($id, $code, $user = null)
    {
        $user = $user ? : auth()->user();

        $phone = $user->phones()->find($id);

        return $phone->makeVerified($code);
    }

}