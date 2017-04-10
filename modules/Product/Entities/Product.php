<?php

namespace Modules\Product\Entities;

use Illuminate\Database\Eloquent\Model;
use Intervention\Image\ImageManagerStatic as Image;
use Cviebrock\EloquentSluggable\Sluggable;
use Plank\Mediable\Mediable;
use Plank\Metable\Metable;
use MediaUploader;
use Laravel\Scout\Searchable;
use Modules\Comment\Traits\HasComments;
use Modules\User\Entities\User;
use App\Traits\NPerGroup;
use Modules\Stream\Entities\Activity;
use Modules\Stream\Traits\Actable;

class Product extends Model
{
	use Mediable, Metable, Sluggable, HasComments, Searchable, NPerGroup, Actable;
	
    public $table = 'products';
    
    protected $fillable  = [
        'title', 'description',  'price', 'status', 'currency', 'owner_id', 'owner_username', 'category', 'in_stock'
    ];


    /**
     *  Get the avatar
     */
    public function photo($type, $nullable = false){
        $media = $this->firstMedia('photo');
        return ($nullable && !$media) ? null : url('media/'.($media ? $media->id : '-').'/product/'.$type.'.jpg');
    }


    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }
    
    /**
     *  Relationships
     */
    public function link($ending = '')
    {   
        return url('/products/'.$this->id.$ending);
    }
    
    /**
     *  Relationships
     */
    public function url($ending = '')
    {   
        return url('@'.$this->owner_username.'/'.$this->slug.'/'.$ending);
    }


    /**
     * Get first media relation for product
     *
     * @return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function firstPhoto()
    {
        return $this->belongsToMany(config('mediable.model'), 'mediables', 'mediable_id', 'media_id')
            ->where('mediable_type', get_class($this))->nPerGroup('mediables', 'media_id', 1);
    }

    
    /**
     *  Relationships
     */
    public function owner()
    {   
        return $this->hasOne(User::class,'id', 'owner_id');
    }


    public function setPriceAttribute($value){
        $this->attributes['price'] = filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    }


    public function setDescriptionAttribute($value){
        $this->attributes['description'] = strip_tags($value);
    }


    public function getIsActiveAttribute(){
        return !!($this->attributes['status'] == 'active');
    }

    public function getIsInactiveAttribute(){
        return !!($this->attributes['status'] == 'inactive');
    }

    public function getIsSoldoutAttribute(){
        return !!($this->attributes['status'] == 'soldout');
    }

    public function getJustCreatedAttribute(){
        return ($this->created_at == $this->updated_at);
    }

    public function markAsSold(){

        if(!$this->is_active) return false;

        $this->status = 'soldout';

        return $this->update();
    }

    public function markAsActive(){

        if($this->is_active) return false;

        $this->status = 'active';

        auth()->user()->pushInStreams($this->id, 'user:'.auth()->id());

        return $this->update();
    }

    public function markAsInactive(){

        if($this->is_inactive) return false;

        $this->status = 'inactive';

        auth()->user()->streamsRemove($this->id);

        return $this->update();
    }



    public function isLiked($actor = null){

        if(is_null($actor)) $actor = auth()->user();
 
        return $actor ? $this->getActivities('product:liked')->where('actor', $actor->id)->exists() : false;
    }



    public function view($data, $actor = null){

        if(is_null($actor)) $actor = auth()->user();

        $data['actor'] = $actor->id;
        $data['action'] = 'like';
        $this->stats()->create($data);
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

            $media = MediaUploader::fromString($image)
            ->toDirectory('users/'.$prop)
            ->useHashForFilename()
            ->setAllowedAggregateTypes(['image'])
            ->setStrictTypeChecking(true)
            ->upload();

            $this->attachMedia($media, $prop);

            return $media;
            
        } catch (\Exception $e){
            return null;
        }

    }


    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $array = $this->toArray();

        return array_intersect_key($array, array_flip(['title']));
    }
    
}