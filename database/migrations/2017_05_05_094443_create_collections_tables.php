<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGeosTables extends Migration
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
            $table->integer('user_id')->unsigned();
            $table->string('description', 250);
            $table->integer('media_id')->unsigned()->nullable();
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
        Schema::dropIfExists('collections');
    }
}