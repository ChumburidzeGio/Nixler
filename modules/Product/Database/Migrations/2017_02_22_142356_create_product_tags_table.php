<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('product_tags', function (Blueprint $table) {

            $table->increments('id');
            $table->integer('user_id');
            $table->timestamps();

        });
        

        Schema::create('product_tags_t', function (Blueprint $table) {

            $table->increments('id');
            $table->integer('tag_id')->unsigned();
            $table->string('name');
            $table->string('locale')->index();

            $table->unique(['tag_id','locale']);
            $table->foreign('tag_id')->references('id')->on('product_tags')->onDelete('cascade');

        });
        

        Schema::create('product_tags_r', function (Blueprint $table) {

            $table->bigIncrements('id');
            $table->integer('tag_id')->unsigned();
            $table->integer('product_id')->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {       
        Schema::dropIfExists('product_tags_r');
        Schema::dropIfExists('product_tags_t');
        Schema::dropIfExists('product_tags'); 
    }
}