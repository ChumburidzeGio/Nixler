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
        info(request()->all());
        
    	$path = storage_path(config('payments.cartu.certPath'));

		$fp = fopen($path, "r");

		$cert = fread($fp, 8192);

		fclose($fp);

		//Verify SSL Certificate
		$signature = base64_decode(request('signature'));

		$confirmation = 'ConfirmRequest='.request('ConfirmRequest');

		$cert = openssl_get_publickey($cert);

		if(!openssl_verify($confirmation, $signature, $cert))
		{
            info('Signature problem');

			return redirect()->intended('/')->with('flash', 'thanks');
		}

		//Move Parameters to Identificators
		$xml = xml_parser_create('UTF-8');
        
        xml_parse_into_struct($xml, request('ConfirmRequest'), $vals);
        
        xml_parser_free($xml);
 
        foreach ($vals as $data)
        {
        	switch ($data['tag']) 
        	{
        		case 'STATUS':
        			$status = $data['value'];
        		
        		case 'PAYMENTID':
        			$paymentId = $data['value'];
        		
        		case 'PAYMENTDATE':
        			$PaymentDate = $data['value'];
        		
        		case 'TRANSACTIONID':
        			$transactionId = $data['value']; 
        		
        		case 'AMOUNT':
        			$Amount = $data['value']; 
        		
        		case 'REASON':
        			$Reason = $data['value']; 
        		
        		case 'CARDTYPE':
        			$CardType = $data['value']; 
        		
        	}
        }

        $order = Order::find($transactionId);

        if($order && in_array($status, ['C', 'Y']))
        {
            if($status == 'Y')
            {
                $order->update([
                    'payment_status' => 'payed',
                    'payment_data' => compact('status', 'paymentId', 'PaymentDate', 'transactionId', 'Amount', 'Reason', 'CardType')
                ]);
            }
            
            elseif($status == 'C')
            {
                $order->update([
                    'payment_status' => 'processing',
                    'payment_data' => null
                ]);
            }

        	$this->sendResponse($transactionId, $paymentId, 'ACCEPTED');
        }
        
        $order->update([
            'payment_status' => 'unpayed',
            'payment_data' => null
        ]);

        $this->sendResponse($transactionId, $paymentId, 'DECLINED');
    }

    private function sendResponse($transactionId, $paymentId, $status)
    {
        info('Response '.$status.' on transaction '.$transactionId.' payment '.$paymentId);

    	$xmlstr = "<ConfirmResponse>
    			<TransactionId>{$transactionId}</TransactionId>
    			<PaymentId>{$paymentId}</PaymentId>
    			<Status>{$status}</Status>
    			</ConfirmResponse>";
 
        header('Content-type: text/xml');

        die($xmlstr);
    }

}	