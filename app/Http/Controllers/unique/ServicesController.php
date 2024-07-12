<?php

namespace App\Http\Controllers\unique;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ServicesController extends InvoicesController
{
   
    public function addService(Request $request){
        $serviceData=$request->input('service');
        $commonData=$request->input('commondata');
        $incomeData=[
            'name'=>$serviceData['name'],
            'createdby' => $commonData['createdby'] ?? null,
            'laseditedby' => $commonData['laseditedby'] ?? null,
            'branch_name' => $commonData['branchid'] ?? null,
        ];

        $incomeNumber=$this->addDataGetID($incomeData, 'incomes');

        $paymentTermData = [
            'name' => $serviceData['name'] ?? null,
            'remarks' => $commonData['remarks'] ?? null,
            'createdby' => $commonData['createdby'] ?? null,
            'laseditedby' => $commonData['laseditedby'] ?? null,
            'branch_name' => $commonData['branchid'] ?? null,
            'incomeno'=>$incomeNumber
        ]; 
        $paymentTermId=$this->addDataGetID($paymentTermData, 'sch_payment_terms');

        $serviceData['paymenttermid']=$paymentTermId;

        $response = $this->demonAdd(new Request($serviceData), 'sch_services');
        return response()->json($response);
        
    }

    public function addDataGetID($insertData, $tableName)
    {
        try {
            
            $data = $this->demonAdd(new Request($insertData), $tableName, true);
            
            if (!isset($data['status']) || $data['status'] != 201) {
                $message = isset($data['results'][0]['message']) ? $data['results'][0]['message'] : 'Unknown error';
                throw new \Exception($message);
            }
        
            if (!isset($data['results'][0]['dataid'])) {
                throw new \Exception('dataid key not found in the response.');
            }
        
            return $data['results'][0]['dataid'];
        } catch (\Exception $e) {
            \Log::error("Error: " . $e->getMessage());
            throw new \Exception($e->getMessage());
        }
        
    }
    



    
   
}
