<?php

namespace App\Http\Controllers\Order\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Entities\Order;

class CartuCallback extends Controller
{
    /**
     * Redirect to payment system
     *
     * @return Response
     */
    public function __invoke()
    {
		return request()->all();
    }

}