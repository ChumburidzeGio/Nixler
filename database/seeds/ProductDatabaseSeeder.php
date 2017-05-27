<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Faker\Factory;
use Faker\Generator;
use App\Entities\User;
use App\Repositories\ProductRepository;

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

        $faker = Factory::create('en_US');
        
        //DB::transaction(function () use ($repository, $faker) {

            auth()->login(User::inRandomOrder()->whereNotNull('currency')->first());

            for ($i=0; $i < 100; $i++) { 
                
                $product = $repository->create();

                $product = $repository->update([
                    'title' => $faker->sentence(),
                    'description' => $faker->text(),
                    'price' => rand(1,2000),
                    'category' => rand(1,20),
                    'in_stock' => rand(1,50),
                    'action' => 'publish'
                ], $product->id);
                
                print("\n{$i}. Created ".str_limit($product->title, 40).' for '.$product->price);

            }

        //});

    }

}
