<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Entities\Product;
use App\Entities\ShippingPrice;
use App\Entities\Order;
use Ayeo\Price\Price;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update($id, Request $request)
    {
        $user = auth()->user();

        $order = Order::where('id', $id)->where('user_id', $user->id)->firstOrFail();

        $status = $request->input('status');

        if($user->can('update-status', [$order, $status])){
            $order->update([
                'status' => $status
            ]);
        }

        return redirect()->route('settings.orders', ['id' => $order->id]);
    }
}
