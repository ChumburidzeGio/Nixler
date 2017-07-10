<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Intervention\Image\ImageManagerStatic as Image;
use Cviebrock\EloquentSluggable\Sluggable;
use Plank\Mediable\Mediable;
use Plank\Metable\Metable;
use MediaUploader;
use App\Traits\Searchable;
use App\Entities\User;
use App\Entities\Media;
use App\Entities\ProductSource;
use App\Traits\NPerGroup;
use App\Entities\Activity;
use App\Traits\Actable;
use Illuminate\Notifications\Notifiable;
use App\Services\Markdown;
use App\Entities\ProductCategory;
use App\Entities\Comment;
use App\Services\SystemService;
use App\Services\CurrencyService;
use Illuminate\Support\Collection;
use Cocur\Slugify\Slugify;

class Product extends Model
{
	use Mediable, Metable, Sluggable, Searchable, NPerGroup, Actable, Notifiable;
	
    public $table = 'products';
    
    protected $fillable  = [
        'title', 'description',  'price', 'status', 'currency', 
        'owner_id', 'owner_username', 'category_id', 'in_stock', 
        'buy_link', 'id_used', 'has_variants', 'is_active', 'sku', 
        'sales_count', 'comments_count', 'views_count'
    ];


    /**
     *  Get the avatar
     */
    public function photo($type, $nullable = false){

        $media_id = $this->attributes['media_id'] ?: '-';

        return ($nullable && !$this->attributes['media_id']) ? null : url("media/{$media_id}/product/{$type}.jpg");

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
                'source' => 'title',
                'unique' => true,
                'method' => function ($string, $separator) {
                    return app(Slugify::class)->slugify($string);
                },
                'separator' => '',
                'uniqueSuffix' => function ($slug, $separator, Collection $list) {

                    $owner_id = $this->attributes['owner_id'];

                    $suffix = Product::where('slug', $slug)->count();

                    while (Product::where('slug', $slug.'-'.$suffix)->count() > 0) {
                       $suffix++;
                    }

                    return $suffix ? '-'.$suffix : '';
                }
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
            ->where('mediable_type', get_class($this))->nPerGroup('mediables', 'mediable_id', 1, 'order');
    }


    /**
     *  Relationships
     */
    public function owner()
    {   
        return $this->hasOne(User::class,'id', 'owner_id');
    }
    
    /**
     * One to one relationship for categories
     */
    public function category() {   
        return $this->hasOne(ProductCategory::class, 'id', 'category_id')->with('translations');
    }

    public function sources() {   
        return $this->hasMany(ProductSource::class, 'product_id', 'id');
    }

    public function infeed() {   
        return $this->hasMany(Feed::class, 'object_id', 'id');
    }

    /**
     * Show comments for model
     */
    public function comments() {
        return $this->hasMany(Comment::class, 'target_id', 'id');
    }

    public function setPriceAttribute($value){
        $this->attributes['price'] = filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    }


    public function setDescriptionAttribute($value){
        $this->attributes['description'] = strip_tags($value);
    }


    public function getPriceFormatedAttribute(){
        return app(CurrencyService::class)->get($this->attributes['currency'], $this->attributes['price']);
    }

    public function getIsInactiveAttribute(){
        return !$this->attributes['is_active'];
    }

    public function getJustCreatedAttribute(){
        return ($this->created_at == $this->updated_at);
    }

    public function markAsActive(){

        if($this->is_active) return false;

        $this->is_active = true;

        $this->update();

        return auth()->user()->pushInStreams($this->id, 'user:'.auth()->id());
    }

    public function markAsInactive(){

        if($this->is_inactive) return false;

        $this->is_active = false;

        $this->owner->streamsRemove($this->id);

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
            app(SystemService::class)->reportException($e);
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

        return array_intersect_key($array, array_flip(['id', 'title', 'description']));
    }
    
    
    /**
     * Return description parsed with Markdown
     */
    public function getDescriptionParsedAttribute()
    {   
        return (new Markdown)->text($this->attributes['description']);
    }
    
    /**
     * Show comments for model
     */
    public function comment($text, $rate = null, $actor = null)
    {
        if(is_null($actor)){
            $actor = auth()->user();
        }
        return $this->comments()->create([
            'user_id' => $actor->id,
            'target_type' => $this->getTable(),
            'text' => $text,
            'rate' => $rate
        ]);
    }


    /**
     * Get just active records
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }
    
}