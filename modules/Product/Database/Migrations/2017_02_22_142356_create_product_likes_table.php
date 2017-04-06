<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductLikesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('product_likes', function (Blueprint $table) {

            $table->bigIncrements('id');
            $table->integer('actor')->unsigned();
            $table->integer('object')->unsigned();
            $table->timestamps();

            $table->foreign('object')->references('id')->on('products')
                ->onDelete('cascade');

        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_likes');
    }
}