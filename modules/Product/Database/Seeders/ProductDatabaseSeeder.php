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
    public function run(ProductRepository $repository)
    {
        Model::unguard();
        
        if(!Category::count()){
            $this->call(CategoryDatabaseSeeder::class);
        }

        $items = array_merge(
            array_get(Amazon::search('iphone')->json(), 'Items.Item'),
            array_get(Amazon::search('samsung')->json(), 'Items.Item'),
            array_get(Amazon::search('iphone 6')->json(), 'Items.Item'),
            array_get(Amazon::search('macbook')->json(), 'Items.Item'),
            array_get(Amazon::search('canon')->json(), 'Items.Item'),
            array_get(Amazon::search('lexar')->json(), 'Items.Item'),
            array_get(Amazon::search('POLO')->json(), 'Items.Item'),
            array_get(Amazon::search('Robert Kent')->json(), 'Items.Item'),
            array_get(Amazon::search('Hugo Boss')->json(), 'Items.Item'),
        );

        DB::transaction(function () use ($items, $repository) {

            collect($items)->map(function($item) use ($repository) {

                auth()->login(User::inRandomOrder()->whereNotNull('currency')->first());

                $product = $repository->create();

                $features = array_get($item, 'ItemAttributes.Feature');

                $product->fill([
                    'title' => str_limit(array_get($item, 'ItemAttributes.Title'), 180),
                    'description' => is_array($features) ? implode(' ', $features) : $features,
                    'price' => array_get($item, 'ItemAttributes.ListPrice.FormattedPrice'),
                    'category' => Category::inRandomOrder()->whereNotNull('parent_id')->pluck('id')->first(),
                    'in_stock' => rand(1,50)
                ]);

                $product->save();

                print("\nCreated ".$product->title);

                $colors = collect(['Black', 'Indigo', 'Red', 'Blue', 'Yellow', 'Orange', 'Cyan', 'Aero']);

                $product->setMeta('variants', $colors->random(rand(1,4)));

                $product->markAsActive();

                if(array_get($item, 'LargeImage.URL')) $product->uploadPhoto(array_get($item, 'LargeImage.URL'), 'photo');

            });

        });

    }

}
