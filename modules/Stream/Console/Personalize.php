<?php

namespace Modules\Stream\Console;

use Illuminate\Console\Command;
use Modules\User\Entities\User;
use Modules\Product\Entities\Product;

class Personalize extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'personalize {user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create personalized list of products for particular user';

    protected $user = null;

    /**
     * Execute the console command.
     * 
     * Get popular cetegories by this criterias:
     * 1. Location (prefered: city) and home city
     * 2. Gender
     * 3. Device (for price range) and browser
     * 4. Age range
     * 5. Weather in the area of user
     * 6. Language used - f.e. if user uses in Poland English offer language learning courses and books
     * 7. Friends activity
     * 8. Relationship status and preferences
     * 9. Religious and political affiliations
     * 10. Education and work history
     * 11. Books user wants to read and showes/movies wants to watch
     * 12. Languages that user knows (to show just products in known languages)
     *
     * Things to define based on this data:
     * 1. Gender
     * 2. Age range 
     * 3. Income level (low, middle, high)
     * 4. Education level (low, middle, high)
     * 5. Relationship status (is parent, is partner)
     * 6. Intelligence
     * 7. Life satisfiction
     *
     * @return mixed
     */
    public function handle()
    {
        $this->setUser();

        return $this->getProducts();
    }

    /**
     * List of products for user based on preferences
     *
     * @return string|null
     */
    public function setUser()
    {
        if(auth()->check()){
            $this->user = auth()->user();
        } else {
            $this->user = User::with('liked', 'liked.meta', 'followings', 'products', 'products.meta')->whereId($this->argument('user'))->firstOrFail();
        }
    }

    /**
     * List of products for user based on preferences
     *
     * @return string|null
     */
    public function getProducts()
    {
        $categories = $this->getCategories();

        $accounts = $this->getAccounts();

        $tags = $this->getTags();

        $by_cat = Product::whereMetaIn('category', $categories)->take(20)->get();

        $by_acc = Product::whereIn('owner_id', $accounts)->take(30)->get();

        $by_tags = Product::withAnyTags($tags)->take(30)->get();

        return $by_cat->merge($by_acc, $by_tags);
    }

    /**
     * Get the list of interesting categories for model - categories of products user likes, categories of products user published
     *
     * @return string|null
     */
    public function getCategories()
    {
        $from_likes = $this->user->liked->map(function($item){
            return $item->getMeta('category');
        });

        $from_products = $this->user->products->map(function($item){
            return $item->getMeta('category');
        });

        return $from_products->merge($from_likes)->unique()->toArray();
    }

    /**
     * Get list of interesting people for model - owners of products that user likes, people who user follows
     *
     * @return string|null
     */
    public function getAccounts()
    {
        $from_likes = $this->user->liked->map(function($item){
            return $item->owner_id;
        });

        $from_followings = $this->user->followings->pluck('id');

        return $from_likes->merge($from_followings)->unique()->toArray();
    }

    /**
     * Get list of interesting tags for model
     *
     * @return string|null
     */
    public function getTags()
    {
        return $this->user->tags()->with('tag')->orderBy('score', 'desc')->get()->pluck('tag.name')->toArray();
    }

    /**
     * Get the gender of user
     *
     * @return string|null
     */
    public function gender($model)
    {
        return $model->getMeta('gender');
    }

    /**
     * Get the age range of user
     *
     * @return integer(0-9)|null
     */
    public function ageRange($model)
    {
        $age = $model->getMeta('birthday')->age;

        $age_range = collect(config('data.age_ranges'))->filter(function($range, $id) use($age) {
            return (array_first($range) <= $age && array_last($range) >= $age);
        })->keys()->first();
    }

    /**
     * Get the income level of user
     *
     * @return integer(1,2,3)|null
     */
    public function incomeLevel($model)
    {
        return null;
    }

    /**
     * Get the education level of user
     *
     * @return integer(1,2,3)|null
     */
    public function eduLevel($model)
    {
        return null;
    }

    /**
     * Check if user is parent
     *
     * @return boolean|null
     */
    public function isParent($model)
    {
        return null;
    }

    /**
     * Check if user is in relationship
     *
     * @return boolean|null
     */
    public function inRelationship($model)
    {
        return null;
    }

    /**
     * Check intelligance level of user
     *
     * @return integer(0-100)|null
     */
    public function intelliganceLevel($model)
    {
        return null;
    }

    /**
     * Check satisfiction level of user
     *
     * @return integer(0-100)|null
     */
    public function satisfictionLevel($model)
    {
        return null;
    }

}



















