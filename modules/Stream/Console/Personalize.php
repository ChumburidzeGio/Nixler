<?php

namespace Modules\Stream\Http\Console;

use Illuminate\Console\Command;
use Bouncer;

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
        
    }

    /**
     * Get the gender of user
     *
     * @return string|null
     */
    public function getPreferedCategories($model)
    {
        
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



















