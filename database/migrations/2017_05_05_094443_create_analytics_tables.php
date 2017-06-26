<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAnaliticsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('metrics', function (Blueprint $table) {

            $table->increments('id');
            $table->string('object_type');
            $table->unsignedInteger('object_id');
            $table->string('key')->index();
            $table->string('value', 255);
            $table->string('target', 255)->nullable();
            $table->date('date');

            $table->index(['object_type', 'object_id', 'date']);
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('metrics');
    }
}
