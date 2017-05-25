<?php

namespace App\Entities;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Traits\Merchant;
use App\Traits\HasMessages;
use App\Traits\HasStream;
use Intervention\Image\ImageManagerStatic as Image;
use Overtrue\LaravelFollow\FollowTrait;
use Plank\Mediable\Mediable;
use Plank\Metable\Metable;
use MediaUploader;
use Cviebrock\EloquentSluggable\Services\SlugService;
use Illuminate\Contracts\Events\Dispatcher;
use Cviebrock\EloquentSluggable\Sluggable;
use App\Entities\UserAddress;
use App\Entities\UserTag;
use App\Entities\Session;
use App\Entities\Country;
use App\Entities\City;
use App\Entities\Product;
use App\Entities\ShippingPrice;
use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Silber\Bouncer\Database\HasRolesAndAbilities;
use Lab404\Impersonate\Models\Impersonate;
use App\Services\SystemService;


class User extends Authenticatable
{
    use Notifiable, Mediable, Metable, FollowTrait, Merchant, HasMessages, HasStream, Sluggable, Searchable, SoftDeletes, HasRolesAndAbilities, Impersonate;

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
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $array = $this->toArray();

        return array_intersect_key($array, array_flip(['name', 'email']));
    }
    
    /**
     * Route notifications for the Nexmo channel.
     *
     * @return string
     */
    public function routeNotificationForNexmo()
    {
        return $this->phone;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'currency', 'country', 'locale', 'timezone', 'city_id'
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
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    
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


    public function sessions()
    {   
        return $this->hasMany(Session::class, 'user_id');
    }


    public function addresses()
    {   
        return $this->hasMany(UserAddress::class, 'user_id');
    }

    public function shippingPrices()
    {   
        return $this->hasMany(ShippingPrice::class, 'user_id');
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
     *  Change the avatar of model
     */
    public function city(){
       return $this->hasMany(City::class, 'id');
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
            app(SystemService::class)->reportException($e);
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


    /**
     *  Get the profile link
     */
    public function getResponseTimeAttribute(){

        $avg = round($this->attributes['response_time'] / 60, -1);

        if($avg > 20 || !$this->attributes['response_time']) return null;

        $times = [
            15 => 'Very responsive to messages',
            60 => 'Response within '.$avg.' minutes.',
            3600 => 'Response within '.round(($avg / 60), -1).' hours.'
        ];

        foreach ($times as $mins => $text) {
            if($avg < $mins)  {
                $rating = $text; break;
            }
        }

        return $rating;
    }


    /**
     * @return bool
     */
    public function canImpersonate()
    {
        return $this->can('impersonate');
    }


}