<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {

            $table->bigIncrements('id');
            $table->string('status');

            $table->decimal('amount', 8, 2)->default(0);
            $table->string('currency', 5);
            $table->tinyInteger('quantity')->nullable();

            $table->bigInteger('address_id')->unsigned()->nullable();
            $table->decimal('shipping_cost', 8, 2)->default(0);
            $table->string('payment_method')->nullable();

            $table->integer('user_id');
            $table->integer('product_id');
            $table->integer('owner_id');
            
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
        Schema::dropIfExists('orders');
    }
}