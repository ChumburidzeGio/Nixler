<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessagesTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('threads', function (Blueprint $table) {

            $table->bigIncrements('id');
            $table->string('subject')->nullable();
            $table->boolean('is_private');
            $table->timestamps();

        });

        Schema::create('messages', function (Blueprint $table) {

            $table->bigIncrements('id');
            $table->bigInteger('thread_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->text('body');
            $table->timestamps();

        });
        
        Schema::create('thread_participants', function (Blueprint $table) {

            $table->bigIncrements('id');
            $table->bigInteger('thread_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->timestamp('last_read')->nullable();
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
        Schema::dropIfExists('thread_participants');
        Schema::dropIfExists('messages');
        Schema::dropIfExists('threads');
    }
}