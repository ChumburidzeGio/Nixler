<?php

namespace Modules\Product\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Faker\Factory;
use Faker\Generator;
use Modules\Product\Entities\Product;
use Modules\User\Entities\User;

class ProductDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        
        foreach (config('test.locales') as $locale) {
           
            $faker = Factory::create($locale);

            for ($i=0; $i < config('test.models_per_locale.products'); $i++) { 
                $product = $this->createProduct($faker);
                $this->uploadPhotos($product, $faker);
            }
        }
    }


    /**
     * Create product
     *
     * @return void
     */
    public function createProduct($faker)
    {
        $user = User::inRandomOrder()->first();

        auth()->login($user);

        $product = $user->createProduct($faker->currencyCode);

        $product->update([
            'title' => $faker->sentence(),
            'description' => $faker->paragraph(),
            'price' => $faker->randomFloat(2, 2, 500)
        ]);

        $product->setMeta('variants', $faker->words);

        $product->setMeta('category', key($product->categories()[rand(1,13)]['items']));

        $product->markAsActive();

        return $product;

    }


    /**
     * Upload photos
     *
     * @return void
     */
    public function uploadPhotos($product, $faker)
    {
        if($faker->boolean(95)){
            for ($i=0; $i < rand(1,4); $i++) { 
                $product->uploadPhoto($faker->image('/tmp', 400, 400), 'photo');
            }
        }
    }
}
