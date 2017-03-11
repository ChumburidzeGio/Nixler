<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductStatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('product_stats', function (Blueprint $table) {

            $table->bigIncrements('id');
            $table->string('action');
            $table->integer('actor')->unsigned();
            $table->integer('object')->unsigned();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->boolean('is_mobile');
            $table->tinyInteger('age_range')->unsigned()->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
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
        Schema::dropIfExists('product_stats');
    }
}