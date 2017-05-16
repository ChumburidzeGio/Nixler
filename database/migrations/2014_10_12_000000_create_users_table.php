<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email')->nullable()->unique();
            $table->string('password')->nullable();
            $table->string('username', 150)->nullable()->unique();
            $table->string('country', 5)->nullable();
            $table->integer('city_id')->nullable();
            $table->string('locale', 5)->nullable();
            $table->string('currency', 5)->nullable();
            $table->string('timezone')->nullable();
            $table->boolean('verified')->default(0);
            $table->integer('response_time')->nullable();
            $table->bigInteger('phone')->nullable()->index();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('user_profiles', function (Blueprint $table) {

            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned()->nullable()->index();
            $table->string('provider')->index();
            $table->string('external_id')->index();
            $table->timestamps();

        });

        Schema::create('password_resets', function (Blueprint $table) {
            $table->string('email')->index();
            $table->string('token')->index();
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('user_sessions', function (Blueprint $table) {

            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned()->index();
            $table->string('device');
            $table->string('platform');
            $table->string('browser');
            $table->boolean('is_phone');
            $table->string('ip');
            $table->string('country_code');
            $table->timestamps();

        });

        Schema::create('followers', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('follow_id');
            $table->timestamps();
        });

        Schema::create('user_addresses', function (Blueprint $table) {

            $table->bigIncrements('id');
            $table->integer('user_id')->unsigned()->index();
            $table->integer('country_id')->unsigned()->index();
            $table->integer('city_id')->unsigned()->index();
            $table->string('street');
            $table->boolean('is_business')->default('0');
            $table->float('lat', 10, 6)->nullable();
            $table->float('lng', 10, 6)->nullable();
            $table->timestamps();

        });

        Schema::create('shipping_prices', function (Blueprint $table) {

            $table->increments('id');
            $table->integer('user_id');
            $table->enum('type', ['country', 'region', 'city']);
            $table->integer('location_id');
            $table->decimal('price', 8, 2)->default(0);
            $table->string('currency', 3);
            $table->tinyInteger('window_from');
            $table->tinyInteger('window_to');
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
        Schema::dropIfExists('shipping_prices');
        Schema::dropIfExists('user_addresses');
        Schema::dropIfExists('followers');
        Schema::dropIfExists('user_sessions');
        Schema::dropIfExists('password_resets');
        Schema::dropIfExists('user_profiles');
        Schema::dropIfExists('users');
    }
}
