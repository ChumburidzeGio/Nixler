<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductCatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('product_cats', function (Blueprint $table) {

            $table->increments('id');
            $table->integer('parent_id')->unsigned()->nullable();
            $table->integer('order')->unsigned();
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
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {       
        Schema::dropIfExists('product_cats_t');
        Schema::dropIfExists('product_cats'); 
    }
}