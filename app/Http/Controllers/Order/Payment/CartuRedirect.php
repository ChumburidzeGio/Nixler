<?php

namespace App\Http\Controllers\Order\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Entities\Order;

class CartuRedirect extends Controller
{
    /**
     * Redirect to payment system
     *
     * @return Response
     */
    public function __invoke($id)
    {
    	$order = Order::find($id);

		$params = $this->params([
			'PurchaseDesc' => ($order->id + 2489),
			'PurchaseAmt' => $order->amount,
		]);

		$url = sprintf(config('payments.cartu.baseUrl'), $params);

		return redirect($url);
    }

    /**
     * @return array
     */
    public function params($params)
    {
    	$params = array_merge([
			'CountryCode' => config('payments.cartu.countryCode'),
			'CurrencyCode' => config('payments.cartu.currencyCode'),
			'MerchantName' => config('app.name'),
			'MerchantURL' => route('orders.payments.cartu.callback'),
			'MerchantCity' => config('payments.cartu.merchantCity'),
			'MerchantID' => config('payments.cartu.merchantId'),
			'xDDDSProxy.Language' => config('payments.cartu.xDDDSProxyLanguage')
    	], $params);

    	return http_build_query($params);
    }

}