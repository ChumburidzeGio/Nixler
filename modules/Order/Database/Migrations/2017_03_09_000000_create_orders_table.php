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
            $table->string('status', 15);
            $table->decimal('amount', 8, 2)->default(0);
            $table->string('currency', 3);
            $table->tinyInteger('quantity')->unsigned()->default(1);
            $table->bigInteger('address_id')->unsigned()->nullable();
            $table->decimal('shipping_cost', 8, 2)->default(0);
            $table->timestamp('shipping_window_from')->nullable();
            $table->timestamp('shipping_window_to')->nullable();
            $table->string('payment_method')->nullable();
            $table->integer('user_id')->unsigned();
            $table->integer('product_id')->unsigned();
            $table->string('product_variant')->nullable();
            $table->integer('merchant_id')->unsigned();
            $table->text('note')->nullable();
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