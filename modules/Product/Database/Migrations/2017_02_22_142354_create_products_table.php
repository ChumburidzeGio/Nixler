<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
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

            $table->string('status')->nullable();
            $table->enum('type', ['product', 'ticket'])->default('product');
            $table->string('title')->nullable();
            $table->text('description')->nullable();

            $table->decimal('price', 8, 2)->default(0);
            $table->string('currency', 5)->default('usd');
            $table->integer('in_stock')->default(0);
            $table->string('buy_link')->nullable();

            $table->integer('category_id')->unsigned()->nullable();

            $table->integer('likes_count')->default(0);
            $table->boolean('is_used')->default(0);
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
        Schema::dropIfExists('products');
    }
}