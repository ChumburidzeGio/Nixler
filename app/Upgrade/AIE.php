<?php

namespace App\Upgrade;

use App\Entities\User;
use App\Entities\Order;
use App\Entities\Product;
use Illuminate\Support\Facades\Schema;
use App\Repositories\UserRepository;
use App\Repositories\ProductRepository;
use Illuminate\Database\Schema\Blueprint;

class AIE
{
    /**
     * Upgrade system to version 1.95
     *
     * Add import to products adding page
     * Add media count to products
     * Add city, phone and title to order
     * Add sales, products and followers counters to user
     * Add notifications on like and comment
     * Move to events and listeners
     * Add couple shops to patterns
     * Renew purchase page design
     *
     * @return void
     */
    public function upgrade()
    {
        if (!Schema::hasColumn('products', 'media_count')) {

            Schema::table('products', function (Blueprint $table) {
                $table->integer('media_count')->default(0)->unsigned();
            });

            Product::active()->get()->map(function($product){
                app(ProductRepository::class)->refreshFeaturedMediaForProduct($product);
            });

        }

        if (!Schema::hasColumn('orders', 'city_id') && !Schema::hasColumn('orders', 'phone') && !Schema::hasColumn('orders', 'title')) {

            Schema::table('orders', function (Blueprint $table) {
                $table->integer('city_id')->nullable()->unsigned();
                $table->bigInteger('phone')->nullable()->unsigned();
                $table->string('title')->nullable();
            });

            Order::with('product', 'user')->get()->map(function($order){

                if(!$product) {
                    $order->delete();
                }

                $order->update([
                    'city_id' => $order->user->city_id,
                    'phone' => $order->user->phone,
                    'title' => $order->product->title,
                ]);

            });
        }

        if (!Schema::hasColumn('users', 'products_count') && 
            !Schema::hasColumn('users', 'sales_count') && 
            !Schema::hasColumn('users', 'followers_count')) {

            Schema::table('users', function (Blueprint $table) {
                $table->integer('products_count')->default(0)->unsigned();
                $table->integer('sales_count')->default(0)->unsigned();
                $table->integer('followers_count')->default(0)->unsigned();
            });

            User::get()->map(function($user) {
                app(UserRepository::class)->updateStats($user);
            });

        }
    }

}