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

        Schema::create('geo_countries', function (Blueprint $table) {

            $table->increments('id');
            $table->integer('calling_code');
            $table->integer('gini');
            $table->string('capital');
            $table->string('continent');
            $table->integer('population');
            $table->integer('area');
            $table->string('iso_code', 2);
            $table->string('currency', 3);
            $table->string('currency_symbol', 20);
            $table->string('language', 2);

            $table->integer('geonames_id');
            $table->timestamps();

        });

        Schema::create('geo_countries_translations', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('country_id')->unsigned();
            $table->string('name');
            $table->string('locale')->index();

            $table->unique(['country_id','locale']);
            $table->foreign('country_id')->references('id')->on('geo_countries')->onDelete('cascade');
        });




        Schema::create('geo_regions', function (Blueprint $table) {

            $table->increments('id');
            $table->integer('country_id');
            $table->integer('population');
            $table->string('iso_code', 10);
            $table->integer('geonames_id');
            $table->timestamps();

        });

        Schema::create('geo_regions_translations', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('region_id')->unsigned();
            $table->string('name');
            $table->string('locale')->index();

            $table->unique(['region_id','locale']);
            $table->foreign('region_id')->references('id')->on('geo_regions')->onDelete('cascade');
        });





        Schema::create('geo_cities', function (Blueprint $table) {

            $table->increments('id');
            $table->integer('population');
            $table->integer('country_id');
            $table->integer('region_id');
            $table->integer('geonames_id');
            $table->float('lat', 10, 6)->nullable();
            $table->float('lng', 10, 6)->nullable();
            $table->timestamps();

        });

        Schema::create('geo_cities_translations', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('city_id')->unsigned();
            $table->string('name');
            $table->string('locale')->index();

            $table->unique(['city_id','locale']);
            $table->foreign('city_id')->references('id')->on('geo_cities')->onDelete('cascade');
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('geo_cities_translations');
        Schema::dropIfExists('geo_cities');
        Schema::dropIfExists('geo_regions_translations');
        Schema::dropIfExists('geo_regions');
        Schema::dropIfExists('geo_countries_translations');
        Schema::dropIfExists('geo_countries');
    }
}