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

        return [
            '1' => [
                'name' => 'Fashion',
                'items' => [
                    '11' => 'Women\'s Clothing',
                    '12' => 'Men\'s Clothing',
                    '13' => 'Children\'s Clothing',
                    '14' => 'Shoes & Bags',
                    '15' => 'Accessories',
                ]
            ],
            '2' => [
                'name' => 'Kids & Babe',
                'items' => [
                    '21' => 'Car Safety Seats',
                    '22' => 'Baby Carriages',
                    '23' => 'Kids room',
                    '24' => 'Toys',
                    '25' => 'Babies & Parents',
                    '26' => 'Education & Art',
                    '27' => 'School',
                ]
            ],
            '3' => [
                'name' => 'Electronics',
                'items' => [
                    '31' => 'Phones & Accessories',
                    '32' => 'Cameras',
                    '33' => 'Audio & Video',
                    '34' => 'Portable Devices',
                    '35' => 'Consoles & Games',
                    '36' => 'Car Electronics',
                    '37' => 'Scopes',
                    '38' => 'Radio Communication',
                ]
            ],
            '4' => [
                'name' => 'Computers',
                'items' => [
                    '41' => 'PC',
                    '42' => 'Laptops & Notbooks',
                    '43' => 'Parts & Accessories',
                    '44' => 'Peripherals',
                    '45' => 'Networking',
                    '46' => 'Office Supplies & Consumables',
                    '47' => 'Movies, Music, Software',
                ]
            ],
            '5' => [
                'name' => 'Vehicles',
                'items' => [
                    '51' => 'Cars',
                    '52' => 'Moto & Equipment',
                    '53' => 'Trucks & Special Vehicles',
                    '54' => 'Water Transport',
                    '55' => 'Parts & Accessories',
                ]
            ],
            '6' => [
                'name' => 'Real Estate',
                'items' => [
                    '61' => 'Apartments',
                    '62' => 'Rooms',
                    '63' => 'Houses, Villas, Cottages',
                    '64' => 'Land',
                    '65' => 'Garages & Car Places',
                    '66' => 'Commercial Property',
                    '67' => 'International Real Estate',
                ]
            ],
            '7' => [
                'name' => 'Home',
                'items' => [
                    '71' => 'Appliances',
                    '72' => 'Furniture & Decor',
                    '73' => 'Kitchen & Dining',
                    '74' => 'Textile',
                    '75' => 'Household Goods',
                    '76' => 'Building & Repair',
                    '77' => 'Country House & Garden',
                ]
            ],
            '8' => [
                'name' => 'Beauty & Health',
                'items' => [
                    '81' => 'Makeup',
                    '82' => 'Frangances',
                    '83' => 'Skin Care',
                    '84' => 'Tools & Accessories',
                    '85' => 'Glasses',
                ]
            ],
            '9' => [
                'name' => 'Sport & Leisure',
                'items' => [
                    '91' => 'Outdoors',
                    '92' => 'Tourism',
                    '93' => 'Hunting & Fishing',
                    '94' => 'Gym & Fitness Equipment',
                    '95' => 'Games'
                ]
            ],
            '10' => [
                'name' => 'Spare Time & Gifts',
                'items' => [
                    '101' => 'Tickets & Tours',
                    '102' => 'Books & Magazines',
                    '103' => 'Collectibles',
                    '104' => 'Musical Instruments',
                    '105' => 'Table Games',
                    '106' => 'Gift Sets & Certificates',
                    '107' => 'Gifts & Flowers',
                    '108' => 'Crafts',
                ]
            ],
            '11' => [
                'name' => 'Pets',
                'items' => [
                    '111' => 'Dogs',
                    '112' => 'Cats',
                    '113' => 'Rodents',
                    '114' => 'Birds',
                    '115' => 'Fish',
                    '116' => 'Other Pets',
                    '117' => 'Feeding & Accessories',
                ]
            ],
            '12' => [
                'name' => 'Food',
                'items' => [
                    '121' => 'Grocery',
                    '122' => 'Organic',
                    '123' => 'Baby Food',
                    '124' => 'Food to Order',
                    '125' => 'Drinks',
                ]
            ],
            '13' => [
                'name' => 'Services',
                'items' => [
                    '131' => 'Photo & Video',
                    '132' => 'Freelancers',
                    '133' => 'Events',
                    '134' => 'Beauty & Health',
                    '135' => 'Equipment Service',
                    '136' => 'Home Improvement',
                    '137' => 'Education',
                    '138' => 'Financial services',
                    '139' => 'Consulting',
                ]
            ],
        ];

    }
    
}