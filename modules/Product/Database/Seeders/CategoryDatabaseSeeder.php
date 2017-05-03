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
            $this->sss('Phones & accessories', [
                $this->sss('Computer & office')
            ], null, null, null, 'wc'), //AE
            $this->sss('Computer & office', false, null, null, null, 'wc'), //AE
            $this->sss('Consumer electronics', false, null, null, null, 'wc'), //AE
            $this->sss('Automobiles & Motorcycles', false, null, null, null, 'wc'), //AE
            $this->sss('Home & Garden, Furniture', false, null, null, null, 'wc'), //AE
            $this->sss('Home Improvement', false, null, null, null, 'wc'), //AE
            $this->sss('Sports & Outdoors', false, null, null, null, 'wc'), //AE
        ];

        foreach ($categories as $key => $value) {
            $value['order'] = $key;
            $subcategories = isset($value['subcategories']) ? $value['subcategories'] : [];
            unset($value['subcategories']);
            $category = Category::create(array_filter($value, 'strlen'));

            foreach ($subcategories as $key => $value) {
                $value['order'] = $key;
                $category->children()->create(array_filter($value, 'strlen'));
            }
        }
    }

}
