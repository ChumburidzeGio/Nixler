<?php

namespace App\Upgrade;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class AIF
{
    /**
     * Upgrade system to version 1.96
     *
     * @return void
     */
    public function upgrade()
    {
    	if (!Schema::hasColumn('orders', 'payment_data') && !Schema::hasColumn('orders', 'payment_status')) {

            Schema::table('orders', function (Blueprint $table) {
		        $table->text('payment_data')->nullable();
		        $table->string('payment_status', 15);
            });

        }

    }
}