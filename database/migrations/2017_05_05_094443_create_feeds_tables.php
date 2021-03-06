<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFeedsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activities', function (Blueprint $table) {

            $table->bigIncrements('id');
            $table->string('verb')->index();
            $table->integer('actor')->index();
            $table->integer('object')->index();
            $table->string('object_type')->index();
            $table->boolean('new')->default(1);
            $table->timestamps();

        });
        
        Schema::create('feeds', function (Blueprint $table) {

            $table->bigIncrements('id');
            $table->integer('user_id')->index();
            $table->integer('object_id');
            $table->string('source')->index();
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
        Schema::dropIfExists('feeds');
        Schema::dropIfExists('activities');
    }
}