<?php

namespace App\Upgrade;

use App\Entities\User;
use App\Entities\Order;
use App\Entities\Product;
use App\Services\RecommService;
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
     * Add capsules
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

                if(!$order->product) {
                    return $order->delete();
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


        if (!Schema::hasTable('product_sources')) {

            Schema::create('product_sources', function (Blueprint $table) {
                $table->bigIncrements('id')->index();
                $table->integer('product_id')->unsigned()->index();
                $table->integer('merchant_id')->unsigned()->index();
                $table->text('source')->nullable();
                $table->timestamps();
            });

        }

        if (!Schema::hasTable('collections')) {

            Schema::create('collections', function (Blueprint $table) {

                $table->increments('id');
                $table->string('name', 150);
                $table->boolean('is_private')->default(true);
                $table->integer('user_id')->unsigned();
                $table->string('description', 250);
                $table->integer('media_id')->unsigned()->nullable();
                $table->timestamps();

            });

        }

        if (!Schema::hasTable('collection_items')) {

            Schema::create('collection_items', function (Blueprint $table) {

                $table->increments('id');
                $table->integer('order')->unsigned();
                $table->integer('collection_id')->unsigned();
                $table->integer('product_id')->unsigned();
                $table->timestamps();

            });

        }

        if (!Schema::hasColumn('products', 'target')) {

            Schema::table('products', function (Blueprint $table) {
                $table->string('target', 15)->nullable();
            });

        }

        app(RecommService::class)->removeProp('lat', 'product');

        app(RecommService::class)->removeProp('lng', 'product');
        

        if (!Schema::hasColumn('products', 'original_price') && !Schema::hasColumn('product_variants', 'original_price')) {

            Schema::table('products', function (Blueprint $table) {
                $table->decimal('original_price', 8, 2)->nullable();
            });

            Schema::table('product_variants', function (Blueprint $table) {
                $table->decimal('original_price', 8, 2)->nullable();
            });

        }

        if (!Schema::hasColumn('collections', 'category_id') && !Schema::hasColumn('collections', 'products_count')) {

            Schema::table('collections', function (Blueprint $table) {
                $table->integer('category_id')->unsigned()->nullable();
                $table->integer('products_count')->unsigned()->nullable();
            });

        }

        if (!Schema::hasColumn('product_tags', 'type')) {

            Schema::table('product_tags', function (Blueprint $table) {
                $table->string('type', 20)->nullable();
            });

        }

        if (!Schema::hasColumn('product_sources', 'status')) {

            Schema::table('product_sources', function (Blueprint $table) {
                $table->string('status', 50)->default('success');
            });

        }

        if (!Schema::hasColumn('product_sources', 'params')) {

            Schema::table('product_sources', function (Blueprint $table) {
                $table->string('params', 250)->nullable();
            });

        }
        
    }

}