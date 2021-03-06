<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('products', function (Blueprint $table) {

            $table->bigIncrements('id');
            $table->integer('owner_id')->unsigned();
            $table->string('owner_username');
            $table->string('slug')->nullable();

            $table->string('title')->nullable();
            $table->text('description')->nullable();

            $table->decimal('original_price', 8, 2)->nullable();
            $table->decimal('price', 8, 2)->default(0);
            $table->string('currency', 5)->default('usd');
            $table->integer('in_stock')->default(0);
            $table->string('buy_link')->nullable();
            $table->string('sku', 40)->nullable();

            $table->integer('category_id')->unsigned()->nullable();
            $table->integer('media_id')->unsigned()->nullable();

            $table->integer('likes_count')->default(0);
            $table->integer('sales_count')->default(0);
            $table->integer('comments_count')->default(0)->unsigned();
            $table->integer('views_count')->default(0)->unsigned();
            $table->integer('media_count')->default(0)->unsigned();
            $table->boolean('is_active')->default(0);
            $table->boolean('has_variants')->default(0);
            $table->string('target', 15)->nullable();
            $table->timestamps();

        });

        Schema::create('product_cats', function (Blueprint $table) {

            $table->increments('id');
            $table->integer('parent_id')->unsigned()->nullable();
            $table->integer('order')->unsigned();
            $table->string('icon')->nullable();
            $table->timestamps();

        });
        

        Schema::create('product_cats_t', function (Blueprint $table) {

            $table->increments('id');
            $table->integer('category_id')->unsigned();
            $table->string('name');
            $table->string('locale')->index();

            $table->unique(['category_id','locale']);
            $table->foreign('category_id')->references('id')->on('product_cats')->onDelete('cascade');

        });

        Schema::create('product_tags', function (Blueprint $table) {

            $table->increments('id');
            $table->string('name');
            $table->integer('user_id');
            $table->string('type', 20)->nullable();
            $table->integer('product_id');
            $table->timestamps();

        });

        Schema::create('product_variants', function (Blueprint $table) {

            $table->increments('id');
            $table->string('name');
            $table->decimal('original_price', 8, 2)->nullable();
            $table->decimal('price', 8, 2)->default(0);
            $table->integer('in_stock')->default(0);
            $table->integer('sales_count')->default(0);
            $table->integer('product_id');
            $table->timestamps();

        });

        Schema::create('orders', function (Blueprint $table) {

            $table->bigIncrements('id');
            $table->string('status', 15);
            $table->decimal('amount', 8, 2)->default(0);
            $table->string('currency', 3);
            $table->tinyInteger('quantity')->unsigned()->default(1);
            $table->string('address');
            $table->decimal('shipping_cost', 8, 2)->default(0);
            $table->timestamp('shipping_window_from')->nullable();
            $table->timestamp('shipping_window_to')->nullable();
            $table->string('payment_method')->nullable();
            $table->text('payment_data')->nullable();
            $table->string('payment_status', 15);
            $table->integer('user_id')->unsigned();
            $table->integer('product_id')->unsigned();
            $table->string('product_variant')->nullable();
            $table->integer('merchant_id')->unsigned();
            $table->integer('city_id')->nullable()->unsigned();
            $table->bigInteger('phone')->nullable()->unsigned();
            $table->string('title')->nullable();
            $table->timestamps();

        });

        Schema::create('product_sources', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('product_id')->unsigned()->index();
            $table->integer('merchant_id')->unsigned()->index();
            $table->text('source')->nullable();
            $table->string('status', 50)->default('success');
            $table->string('params', 250)->nullable();
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_sources');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('product_tags_t');
        Schema::dropIfExists('product_tags'); 
        Schema::dropIfExists('product_cats_t');
        Schema::dropIfExists('product_cats');
        Schema::dropIfExists('products');
    }
}