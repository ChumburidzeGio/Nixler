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

class ProductDatabaseSeeder extends Seeder
{
    /**
     * @var PostRepository
     */
    protected $repository;

    protected $categories;

    public function __construct(ProductRepository $repository){
        $this->repository = $repository;
    }

    
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        
        if(!Category::count()){
            $this->call(CategoryDatabaseSeeder::class);
        }

        $this->categories = Category::pluck('id');

        foreach (config('test.locales') as $locale) {
           
            $faker = Factory::create($locale);

            for ($i=0; $i < 3000; $i++) { 
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
        $user = User::inRandomOrder()->whereNotNull('currency')->first();

        auth()->login($user);

        $product = $this->repository->create();

        $product->update([
            'title' => $faker->sentence(),
            'description' => $faker->paragraph(),
            'price' => $faker->randomFloat(2, 2, 500),
            'category' => $this->categories->random(),
            'in_stock' => $faker->randomDigit()
        ]);

        $product->setMeta('variants', $faker->words);

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
                $product->uploadPhoto($faker->image('/tmp', 400, 500), 'photo');
            }
        }
    }
}
