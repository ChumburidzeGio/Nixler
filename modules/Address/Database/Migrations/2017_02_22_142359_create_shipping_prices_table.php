<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShippingPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

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
    }
}