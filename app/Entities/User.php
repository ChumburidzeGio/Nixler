<?php

namespace App\Entities;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
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
use App\Entities\Order;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Silber\Bouncer\Database\HasRolesAndAbilities;
use Lab404\Impersonate\Models\Impersonate;
use App\Services\SystemService;
use DB;


class User extends Authenticatable
{
    use Notifiable, Mediable, Metable, FollowTrait, HasStream, Sluggable, Searchable, SoftDeletes, HasRolesAndAbilities, Impersonate;

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

        return array_intersect_key($array, array_flip(['id', 'name', 'email']));
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
        'username', 'name', 'email', 'password', 'currency', 'country', 'locale', 'timezone', 'city_id',
        'products_count', 'sales_count', 'followers_count'
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

    public function sales()
    {   
        return $this->hasMany(Order::class, 'merchant_id');
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
    
    public function threads()
    {
        return $this->belongsToMany(Thread::class, 'thread_participants', 'user_id', 'thread_id')->withPivot('last_read');
    }

    public function products()
    {   
        return $this->hasMany(Product::class, 'owner_id');
    }
    
    public function liked()
    {   
        return $this->belongsToMany(Product::class, 'activities', 'actor', 'object')->where('verb', 'product:liked');
    }

    public function stream()
    {   
        return $this->belongsToMany(Product::class, 'feeds', 'user_id', 'object_id');
    }

    public function city(){
       return $this->hasOne(City::class, 'id', 'city_id');
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
            15 => __('Very responsive to messages.'),
            60 => __('Response within :avg minutes.', compact('avg')),
            3600 => __('Response within :hour hours.', ['hour' => round(($avg / 60), -1)])
        ];

        foreach ($times as $mins => $text) {
            if($avg < $mins)  {
                $rating = $text; break;
            }
        }

        return $rating;
    }




    /**
     *  Terms & Conditions of merchant
     */
    public function getMerchantTermsAttribute(){
        return $this->getMeta('policy');
    }


    /**
     * @return bool
     */
    public function canImpersonate()
    {
        return $this->can('impersonate');
    }


    /**
     * Get about all users if we follow
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIsFollowing($query)
    {
        if(auth()->guest()) {
            return $query->select('users.id', 'name', 'username', DB::raw('0 as following'));
        }

        return $query->leftJoin('followers as me', function ($join) {
                    $join->on('me.follow_id', '=', 'followers.follow_id')
                         ->where('me.user_id', '=', auth()->id());
                })
                ->select('users.id', 'name', 'username', DB::raw('case when me.id is null then 0 else 1 end as following'));
    }


}