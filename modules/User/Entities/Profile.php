<?php

namespace Modules\User\Entities;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{

    public $table = 'user_profiles';
    
    protected $fillable  = [
        'user_id', 'provider', 'external_id'
    ];

    public function user()
    {   
        return $this->belongsTo(config('auth.providers.users.model'));
    }

    public function findBySocialAccount($provider, $id, $name, $email, $gender, $birthday, $photo){

        $account = $this->firstOrCreate([
            'provider' => $provider,
            'external_id' => $id
        ]);

        $account = $account->attachToUser($name, $email, $gender, $birthday, $photo);

        return $account->user;
    }


    public function attachToUser($name, $email, $gender, $birthday, $photo){

        $user = null;
        
        //fixme, saving of bd and gender needed
        if($this->user_id){
            $user = User::find($this->user_id);
        }

        if(!$user){
            $user = $email ? User::whereEmail($email)->first() : null;
        }

        if (!$user) {

            $user = $this->user()->create([
                'email' => $email,
                'name' => $name
            ]);

            $user->save();
            
            if(!is_null($photo)){
                $user->changeAvatar($photo);
            }
        }

        if($birthday && !$user->hasMeta('birthday')){
            $carbon = new \Carbon\Carbon;
            list($month,$day,$year) = explode('/', $birthday);
            $user->setMeta('birthday', $carbon->createFromDate($year, $month, $day));
        }

        $user->setMeta('gender', $gender);

        $this->update([
            'user_id' => $user->id
        ]);

        return $this;

    }
}