<?php

namespace App\Http\Controllers\unique;

use App\Http\Controllers\Controller;
use App\Models\StudentFinanceRecord;
use App\Traits\DemonTrait;
use Exception;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    use DemonTrait;
    public function processPayment(Request $request){
        $paymentData=$request->input('paymentdata');
        $serviceData=$request->input('sevicesinfo');
      
        //return if no data
        if(empty($paymentData)){
            $data=[
                'status'=>400,
                'message'=>'payment data not set'
            ];
            return response()->json($data, 400);
        }

        $PaymentResponse=$this->createPayment($paymentData);
        if(!$PaymentResponse['success']){
            $response=[
                'status'=>'error',
                'message'=>$PaymentResponse['message']
            ];
            return response()->json($response, 401);
        }

        $paymentId=$PaymentResponse['paymentid'];

        $processInvoicePayment=$this->processInvoicePayment($paymentData);

        if(!empty($serviceData)){
            $serviceData['paymentid']=$paymentId;
            $serviceStatus=$this->processServicePayment($serviceData);
        }



    }


    public function createPayment($paymentData){
        try{
          
            $payresponse=$this->demonAdd(new Request($paymentData), 'sch_fee_payments', true);
            if($payresponse['status']==201){
                $paymentId=$payresponse['results'][0]['dataid'];
            }
            $response=[
                'success'=>true,
                'paymentid'=>$paymentId
            ];
            return $response;
        }catch(Exception $e){
            $response=[
                'success'=>false,
                'message'=>'error creating payment'
            ];
            return $response;
        }
       
    }


    public function processInvoicePayment($paymentData){
        try{
            
            


        }catch(Exception $e){
            $response=[
                'success'=>false,
                'message'=>'Error making invoice adjustment'
            ];
            return $response;
        }

    }

    public function processServicePayment($serviceData){
        try{



            $response=[
                'status'=>'success'
            ];
            return $response;

        }catch(Exception $e){
            $response=[
                'status'=>'error'
            ];
            return $response;
        }
    }
}


