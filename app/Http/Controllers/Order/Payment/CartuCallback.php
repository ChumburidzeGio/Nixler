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
			die("signature error");
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
        			$Status = $data['value'];
        		
        		case 'PAYMENTID':
        			$PaymentId = $data['value'];
        		
        		case 'PAYMENTDATE':
        			$PaymentDate = $data['value'];
        		
        		case 'TRANSACTIONID':
        			$TransactionId = $data['value']; 
        		
        		case 'AMOUNT':
        			$Amount = $data['value']; 
        		
        		case 'REASON':
        			$Reason = $data['value']; 
        		
        		case 'CARDTYPE':
        			$CardType = $data['value']; 
        		
        	}
        }
        
        if(in_array($Status, ['C', 'Y']))
        {
        	$this->sendResponse($transactionId, $paymentId, 'ACCEPTED');
        }
        
        $this->sendResponse($transactionId, $paymentId, 'DECLINED');
    }

    private function sendResponse($transactionId, $paymentId, $status)
    {
    	$xmlstr = "<ConfirmResponse>
    			<TransactionId>{$transactionId}</TransactionId>
    			<PaymentId>{$paymentId}</PaymentId>
    			<Status>{$status}</Status>
    			</ConfirmResponse>";
 
        header('Content-type: text/xml');

        die($xmlstr);
    }

}	