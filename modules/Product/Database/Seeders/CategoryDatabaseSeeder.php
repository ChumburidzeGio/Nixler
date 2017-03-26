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
    public function run()
    {
        Model::unguard();
        
        $categories = [
            'Fashion' => [
                'Clothing', 'Shoes & Bags', 'Accessories'
            ],
            'Kids & Babe' => [
                'Car Safety Seats', 'Baby Carriages', 'Kids room', 'Toys', 
                'Babies & Parents', 'Education & Art', 'School'
            ],
            'Electronics' => [
                'Phones & Accessories', 'Cameras', 'Audio & Video', 'Portable Devices', 'Consoles & Games',
                'Car Electronics', 'Scopes', 'Radio Communication'
            ],
            'Computers' => [
                'PC', 'Laptops & Notbooks', 'Parts & Accessories', 'Peripherals', 'Networking', 
                'Office Supplies & Consumables', 'Movies, Music, Software'
            ],
            'Vehicles' => [
                'Cars', 'Moto & Equipment', 'Trucks & Special Vehicles', 'Water Transport', 'Parts & Accessories'
            ],
            'Real Estate' => [
                'Apartments', 'Rooms', 'Houses, Villas, Cottages', 'Land', 'Garages & Car Places',
                'Commercial Property', 'International Real Estate'
            ],
            'Home' => [
                'Appliances', 'Furniture & Decor', 'Kitchen & Dining', 'Textile', 'Household Goods',
                'Building & Repair', 'Country House & Garden'
            ],
            'Beauty & Health' => [
                'Makeup', 'Frangances', 'Skin Care', 'Tools & Accessories', 'Glasses'
            ],
            'Sport & Leisure' => [
                'Outdoors', 'Tourism', 'Hunting & Fishing', 'Gym & Fitness Equipment', 'Games'
            ],
            'Spare Time & Gifts' => [
                'Tickets & Tours', 'Books & Magazines', 'Collectibles', 'Musical Instruments',
                'Table Games', 'Gift Sets & Certificates', 'Gifts & Flowers', 'Crafts'
            ],
            'Pets' => [
                'Dogs', 'Cats', 'Rodents', 'Birds', 'Fish', 'Other Pets', 'Feeding & Accessories'
            ],
            'Food' => [
                'Grocery', 'Organic', 'Baby Food', 'Food to Order', 'Drinks'
            ],
            'Services' => [
                'Photo & Video', 'Freelancers', 'Events', 'Beauty & Health', 'Equipment Service',
                'Home Improvement', 'Education', 'Financial services', 'Consulting'
            ]
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
