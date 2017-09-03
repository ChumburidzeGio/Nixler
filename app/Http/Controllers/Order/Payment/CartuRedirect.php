<?php

namespace App\Http\Controllers\Order\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Entities\Order;

class CartuRedirect extends Controller
{
	protected $baseUrl = 'https://e-commerce.cartubank.ge/servlet/Process3DSServlet/3dsproxy_init.jsp?%s';

	protected $countryCode = 268;

    /**
     * 981 - GEL, 840 - USD, 978 - EUR
     */
	protected $currencyCode = 981;

	protected $merchantCity = 'Tbilisi';

    /**
     * Merchant ID in format 0000000XXXXXXXX-00000001
     */
	protected $merchantId = '000000008001266-00000001';

    /**
     * 01 - GEO,  02 - ENG, 03 - RUS, 04 - DEU, 05 - TUR
     */
	protected $xDDDSProxyLanguage = '01';

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

		$url = sprintf($this->baseUrl, $params);

		return redirect($url);
    }

    /**
     * @return array
     */
    public function params($params)
    {
    	$params = array_merge([
			'CountryCode' => $this->countryCode,
			'CurrencyCode' => $this->currencyCode,
			'MerchantName' => config('app.name'),
			'MerchantURL' => route('orders.payments.cartu.callback'),
			'MerchantCity' => $this->merchantCity,
			'MerchantID' => $this->merchantId,
			'xDDDSProxy.Language' => $this->xDDDSProxyLanguage
    	], $params);

    	return http_build_query($params);
    }

}