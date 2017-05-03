<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {

            $table->increments('id');
            $table->integer('user_id');
            $table->string('slug')->nullable()->unique();
            $table->timestamps();

        });
        

        Schema::create('articles_t', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('article_id')->unsigned();
            $table->string('title');
            $table->longText('body');
            $table->string('locale')->index();

            $table->unique(['article_id','locale']);
            $table->foreign('article_id')->references('id')->on('articles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('articles_t');
        Schema::dropIfExists('articles');
    }
}