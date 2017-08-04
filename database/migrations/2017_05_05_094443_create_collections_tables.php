<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCollectionsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('collections', function (Blueprint $table) {

            $table->increments('id');
            $table->string('name', 150);
            $table->boolean('is_private')->default(true);
            $table->integer('user_id')->unsigned();
            $table->string('description', 250);
            $table->integer('media_id')->unsigned()->nullable();
            $table->integer('category_id')->unsigned()->nullable();
            $table->integer('products_count')->unsigned()->nullable();
            $table->timestamps();

        });

        Schema::create('collection_items', function (Blueprint $table) {

            $table->increments('id');
            $table->integer('order')->unsigned();
            $table->integer('collection_id')->unsigned();
            $table->integer('product_id')->unsigned();
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
        Schema::dropIfExists('collection_items');
        Schema::dropIfExists('collections');
    }
}