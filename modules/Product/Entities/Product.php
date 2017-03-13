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

class Product extends Model
{
	use Mediable, Metable, Sluggable, HasComments, Searchable;
	
    public $table = 'products';
    
    protected $fillable  = [
        'title', 'description',  'price'
    ];

    /**
     *  Get the avatar
     */
    public function photo($type){
        $media = $this->firstMedia('photo');
        return url('media/'.($media ? $media->id : '-').'/product/'.$type.'.jpg');
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
     *  Relationships
     */
    public function likes()
    {   
        return $this->hasMany(ProductLike::class, 'object');
    }

    
    /**
     *  Relationships
     */
    public function stats()
    {   
        return $this->hasMany(ProductStats::class, 'object');
    }

    
    /**
     *  Relationships
     */
    public function statistics()
    {   
        return (new ProductStats)->calculate($this->stats()->get());
    }
    
    /**
     *  Relationships
     */
    public function likers()
    {   
        $table = (new ProductLike)->getTable();
        return $this->belongsToMany(config('auth.providers.users.model'), $table, 'object', 'actor');
    }


    public function setPriceAttribute($value){
        $this->attributes['price'] = preg_replace('/[^0-9.]+/', '', $value);
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


    public function like($data, $actor = null){

        if(is_null($actor)) $actor = auth()->user();

        $like = $this->likes()->firstOrCreate([
            'actor' => $actor->id
        ]);

        if($like->wasRecentlyCreated){
            $data['actor'] = $actor->id;
            $data['action'] = 'like';
            $this->stats()->create($data);
            return true;
        }

        return !$like->delete();
    }



    public function isLiked($actor = null){

        if(is_null($actor)) $actor = auth()->user();
 
        return $actor ? $this->likes()->where('actor', $actor->id)->exists() : false;
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
            ->onDuplicateReplace()
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
     *  Upload photo to model
     */
    public function categories(){

        return config('data.product_categories');

    }
    
}