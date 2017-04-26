<?php

namespace Modules\Product\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Faker\Factory;
use Faker\Generator;
use Modules\Product\Entities\Product;
use Modules\Product\Entities\Category;
use Modules\User\Entities\User;
use Modules\Product\Repositories\ProductRepository;
use Amazon, DB;

class ProductDatabaseSeeder extends Seeder
{
    
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function getCategory($binding)
    {
        $matching = [
            'Electronics' => 24,
            'Wireless Phone Accessory' => 14,
            'Unlocked Phone' => 14,
            'Personal Computers' => 23,
            'Wireless Phone' => 14,
            'Camera' => 15,
            'Apparel' => 2,
            'Shoes' => 3,
            'Kindle Edition' => 66,
            'Hardcover' => 66,
            'Paperback' => 66,
            'Health and Beauty' => 55,
            'Kitchen' => 47,
            'Misc.' => 87,
        ];

        return array_get($matching, $binding);

    }


    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(ProductRepository $repository)
    {
        Model::unguard();
        
        $items = array_merge(
            array_get(Amazon::search('iphone')->json(), 'Items.Item'),
            array_get(Amazon::search('samsung')->json(), 'Items.Item'),
            array_get(Amazon::search('iphone 6')->json(), 'Items.Item'),
            array_get(Amazon::search('macbook')->json(), 'Items.Item'),
            array_get(Amazon::search('canon')->json(), 'Items.Item'),
            array_get(Amazon::search('lexar')->json(), 'Items.Item'),
            array_get(Amazon::search('POLO')->json(), 'Items.Item'),
            array_get(Amazon::search('Robert Kent')->json(), 'Items.Item'),
            array_get(Amazon::search('Hugo Boss')->json(), 'Items.Item')
        );

        DB::transaction(function () use ($items, $repository) {

            collect($items)->map(function($item) use ($repository) {

                auth()->login(User::inRandomOrder()->whereNotNull('currency')->first());

                $product = $repository->create();

                $features = array_get($item, 'ItemAttributes.Feature');
                $colors = collect(['Black', 'Indigo', 'Red', 'Blue', 'Yellow', 'Orange', 'Cyan', 'Aero']);
                $title = str_limit(array_get($item, 'ItemAttributes.Title'), 180);
                $price = array_get($item, 'ItemAttributes.ListPrice.FormattedPrice');
                $category = $this->getCategory(array_get($item, 'ItemAttributes.Binding'));

                if(!$price) {
                    return false;
                }
                
                print("\nCreating ".$title.' for '.$price);

                $product = $repository->update([
                    'title' => $title,
                    'description' => is_array($features) ? implode(' ', $features) : $features,
                    'price' => $price,
                    'category' => $category,
                    'in_stock' => rand(1,50),
                    'variants' => json_encode($colors->random(rand(1,4))),
                    'action' => 'publish'
                ], $product->id);

                if(array_get($item, 'LargeImage.URL')) $product->uploadPhoto(array_get($item, 'LargeImage.URL'), 'photo');

            });

        });

    }

}
