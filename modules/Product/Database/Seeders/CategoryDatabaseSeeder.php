<?php

namespace Modules\Product\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Product\Entities\Category;

class CategoryDatabaseSeeder extends Seeder
{
    
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function sss($en, $sub = false, $pl = null, $ka = null, $ru = null, $icon = null)
    {
        $data = [
            'name:en' => $en,
            'name:pl' => $pl,
            'name:ka' => $ka,
            'name:ru' => $ru,
            'icon' => $icon,
        ];

        if($sub){
            $data['subcategories'] = $sub;
        }

        return $data;
    }


    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        
        $categories = [

        //ELECTRONICS
            //TV
            //


            //Toys & Games
            //

            //Toys, Kids & Babe
            //Health & Beauty
            //Electronics & Computers
            $this->sss('Phones & accessories', false, null, null, null, 'wc'), //AE
            $this->sss('Computer & office', false, null, null, null, 'wc'), //AE
            $this->sss('Consumer electronics', false, null, null, null, 'wc'), //AE
            
            //Cars & Vehicles   /   Automotive & Industrial         
            $this->sss('Automobiles & Motorcycles', false, null, null, null, 'wc'), //AE

            //Home, Garden & Tools
            $this->sss('Home & Garden, Furniture', false, null, null, null, 'wc'), //AE
            $this->sss('Home Improvement', false, null, null, null, 'wc'), //AE

            //Sports & Outdoors
            $this->sss('Sports & Outdoors', false, null, null, null, 'wc'), //AE

            //Food


            //Handmade


            //Books & Audible


            //Services

        ];

        $order = 0;

        foreach ($categories as $key => $value) {
            $category = Category::create(['name:en' => $key, 'order' => $order]);

            foreach ($value as $key => $value) {
                $category->children()->create([ 'name:en' => $value, 'order' => $key ]);
            }

            $order++;
        }
    }

}
