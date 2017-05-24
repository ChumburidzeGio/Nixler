<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMonitoringTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hosts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('ssh_user')->nullable();
            $table->integer('port')->nullable();
            $table->string('ip')->nullable();
            $table->json('custom_properties')->nullable();
            $table->timestamps();
        });
        
        Schema::create('checks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('host_id')->unsigned();
            $table->foreign('host_id')->references('id')->on('hosts')->onDelete('cascade');
            $table->string('type');
            $table->string('status')->nullable();
            $table->boolean('enabled')->default(true);
            $table->text('last_run_message')->nullable();
            $table->json('last_run_output')->nullable();
            $table->timestamp('last_ran_at')->nullable();
            $table->integer('next_run_in_minutes')->nullable();
            $table->timestamp('started_throttling_failing_notifications_at')->nullable();
            $table->json('custom_properties')->nullable();
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
        Schema::drop('checks');
        Schema::drop('hosts');
    }
}