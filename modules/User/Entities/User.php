<?php

namespace Modules\User\Entities;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Modules\Product\Traits\Merchant;
use Modules\Messages\Traits\HasMessages;
use Modules\Stream\Traits\HasStream;
use Intervention\Image\ImageManagerStatic as Image;
use Overtrue\LaravelFollow\FollowTrait;
use Plank\Mediable\Mediable;
use Plank\Metable\Metable;
use MediaUploader;
use Cviebrock\EloquentSluggable\Services\SlugService;
use Illuminate\Contracts\Events\Dispatcher;
use Cviebrock\EloquentSluggable\Sluggable;
use Modules\Address\Entities\UserAddress;
use Modules\Stream\Entities\UserTag;
use Modules\User\Entities\Session;
use Modules\Address\Entities\Country;
use Modules\Product\Entities\Product;

class User extends Authenticatable
{
    use Notifiable, Mediable, Metable, FollowTrait, Merchant, HasMessages, HasStream, Sluggable;

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable()
    {
        return [
            'username' => [
                'source' => 'name',
                'unique' => true
            ]
        ];
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'currency', 'country', 'locale', 'timezone'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    
    /**
     *  Relationships
     */
    public function profiles()
    {   
        return $this->hasMany(Profile::class, 'user_id');
    }


    public function emails()
    {   
        return $this->hasMany(Email::class, 'user_id');
    }


    public function phones()
    {   
        return $this->hasMany(Phone::class, 'user_id');
    }


    public function addresses()
    {   
        return $this->hasMany(UserAddress::class, 'user_id');
    }
    

    public function sessions()
    {   
        return $this->hasMany(UserSession::class, 'user_id');
    }


    public function country()
    {   
        return $this->hasOne(Country::class, 'iso_code', 'country');
    }

    
    /**
     *  Relationships
     */
    public function products()
    {   
        return $this->hasMany(Product::class, 'owner_id');
    }


    /**
     *  Change the avatar of model
     */
    public function changeAvatar($source){
       return $this->uploadPhoto($source, 'avatar');
    }



    /**
     *  Upload photo to model
     */
    public function uploadPhoto($source, $prop){

        try {

            Image::configure(array('driver' => 'gd'));
            $image = Image::make($source)->resize(null, 900, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })->encode('jpg');

            $new_media = MediaUploader::fromString($image)
            ->toDirectory('users/'.$prop)
            ->useHashForFilename()
            ->onDuplicateReplace()
            ->setAllowedAggregateTypes(['image'])
            ->setStrictTypeChecking(true)
            ->upload();

            $media = $this->getMedia($prop);
            $media = $media->prepend($new_media);
            $this->syncMedia($media, $prop);

            //(new Dispatcher)->dispatch('eloquent.photoUploaded: ' . get_class($this), [$this, $media]);

        } catch (\Exception $e){
            dd($e->getMessage());
            return null;
        }

    }




    /**
     *  Get the avatar
     */
    public function avatar($place){
        return url('avatars/'.$this->attributes['id'].'/'.$place);
    }

    /**
     *  Get the cover
     */
    public function cover($place){
        $media = $this->firstMedia('cover');
        return url('media/'.($media ? $media->id : '-').'/cover/'.$place.'.jpg');
    }

    /**
     *  Get the cover
     */
    public function photo($media, $type, $place){
        return url('media/'.($media ? $media->id : '-').'/'.$type.'/'.$place.'.jpg');
    }


    /**
     *  Get the profile link
     */
    public function link($tab = ''){
        $url = route('user', ['id' => $this->username]);
        return $tab ? $url.'?tab='.$tab : $url;
    }

}