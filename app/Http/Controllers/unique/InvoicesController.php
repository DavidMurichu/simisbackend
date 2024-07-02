<?php

namespace App\Http\Controllers\unique;

use App\Http\Controllers\Controller;
use App\Models\MemberPayableArear;
use App\Models\SchFeeInvoice;
use App\Models\SchStudent;
use App\Models\test\InvoiceHolder;
use App\Traits\DemonTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class InvoicesController extends Controller
{
    use DemonTrait;

    public function processInvoice(Request $request)
    {
        try {
            // Separate common data from the request
            $commonData = $request->input('commondata');
            $studentInvoices = $request->input('studentinvoices');
            if(!$studentInvoices){
                $data=[
                    "message"=>"No selected student"
                ];
                return response()->json($data, 401);
            }
            foreach ($studentInvoices as $studentInvoiceData) {
                $studentId = $studentInvoiceData['studentid'];
                $invoiceData = $studentInvoiceData['invoiceData'];
                $voteHeadData = $studentInvoiceData['voteheads'];
                $serviceData = $studentInvoiceData['services'];
                

               
                // Create an Invoice for each student
                $invoiceResult = $this->createInvoice($invoiceData, $commonData, $studentId);

                if (!$invoiceResult['success']) {
                    return response()->json(['message' => "error creating  invoices"], 500);
                }

                $invoiceId = $invoiceResult['invoice_id'];

                // Process Vote Heads
                $this->processVoteHeads($voteHeadData, $commonData, $invoiceId);

                // Process Services
                $this->processServices($serviceData, $commonData, $invoiceId, $studentId);
            }

            // Return Success Response
            return response()->json(['message' => 'Invoices processed successfully.'], 200);

        } catch (\Exception $e) {
            // Log the error
            Log::error('Invoice processing error: ' . $e->getMessage());

            // Return error response
            $data = [
                "message" =>  $e->getMessage()
            ];
            return response()->json($data, 500);
        }
    }

    public function createInvoice($invoiceData,$commonData, $studentId)
    {
        // Retrieve the student with related data eagerly loaded
        $student = SchStudent::with(['currentClass', 'currentTerm', 'currentAcademicYear'])->find($studentId);

        // Check if student exists
        if (!$student) {
            throw new \Exception("Student with ID $studentId not found.");
        }

        // Validate invoice data against student's current details
        if (
            $student->currentClass->id != $invoiceData['classid'] ||
            $student->currentTerm->id != $invoiceData['termid'] ||
            $student->currentAcademicYear->id != $invoiceData['academicyearid']
        ) {
            throw new \Exception("Error creating invoice. Check your data. ");
        }

        // Generate Invoice Number
        $invoiceNumber = $this->generateInvoice();

        // Merge invoice number into invoice data
        $invoiceData['invoiceno'] = $invoiceNumber;
        $invoiceData['studentid']=$studentId;
        $invoiceData['invoicedate']=strval(Carbon::now());
        $invoiceData['createdby']=$commonData['createdby'];

        // Create Invoice Record
        $response = $this->demonAdd(new Request($invoiceData), 'sch_fee_invoices');
        // Check if Invoice Creation was Successful
        if ($response->status() != 201) {
            return [
                'success' => false,
                'message' => $response->getContent() // Assuming message is in the response body
            ];
        }

        // Get the ID of the created invoice
        $invoice = SchFeeInvoice::where("invoiceno", $invoiceNumber)->first();

        if (!$invoice) {
            throw new \Exception("Invoice with number $invoiceNumber not found.");
        }

        return [
            'success' => true,
            'invoice_id' => $invoice->id
        ];
    }


    public function validateStudent($studentId, $invoiceData){
         // Retrieve the student with related data eagerly loaded
         $student = SchStudent::with(['currentClass', 'currentTerm', 'currentAcademicYear'])->find($studentId);

         // Check if student exists
         if (!$student) {
             throw new \Exception("Student with ID $studentId not found.");
         }
 
         // Validate invoice data against student's current details
         if (
             $student->currentClass->name != $invoiceData['classid'] ||
             $student->currentTerm->name != $invoiceData['termid'] ||
             $student->currentAcademicYear->name != $invoiceData['academicyearid']
         ) {
             throw new \Exception("Error creating invoice. Check your data. ");
         }
    }

    private function processVoteHeads($voteHeadData, $commonData, $invoiceId)
    {
        foreach ($voteHeadData as $voteHeadEntry) {
            $voteHeadEntry = [
                "invoiceid" => $invoiceId,
                "feestructurevoteheadsid" => $voteHeadEntry['voteheadid'],
                "amount" => $voteHeadEntry['amount'],
                "createdby" => $commonData["createdby"],
                "is_active"=>"1"
            ];

            // Insert Vote Head Data
            $response=$this->demonAdd(new Request($voteHeadEntry), 'sch_vote_head_invoice_details');
            if($response->status()!=201){
            throw new \Exception($response);
                
            }
        }
    }

    private function processServices($serviceData, $commonData, $invoiceId, $studentId)
    {
        foreach ($serviceData as $serviceEntry) {
            $serviceEntry = [
                "invoiceid" => $invoiceId,
                "studentid" => $studentId,
                "studentserviceid" => $serviceEntry["serviceid"],
                "amount" => $serviceEntry["amount"],
                "balance" => $serviceEntry['balance'], 
                "createdby" => $commonData["createdby"],
                "is_active"=>"1"
            ];

            // Insert Service Data
            $response=$this->demonAdd(new Request($serviceEntry), 'sch_student_service_invoices');
            if($response->status()!=201){
            throw new \Exception($response);
                
            }
        }
    }

    private function generateInvoice()
    {
        $counter = InvoiceHolder::firstOrCreate(['id' => 1], ['last_invoice_number' => 0]);
        $invoiceNumber = $counter->last_invoice_number + 1;
        $counter->last_invoice_number = $invoiceNumber;
        $counter->save();
        // more logic needed
        return strval($invoiceNumber);

    }


    public function create_arear(Request $request){

        try{
            Log::info($request->all());
            $studentArears = $request->input('studentarears');
            $services=$request->input('studentservices');
            $commonData=$request->input('commondata');
            if(!$studentArears){
                $data=[
                    "message"=>"No student Selected"
                ];
                return response()->json($data, 400);
            }
            foreach($studentArears as $studentArear){
                $studentId=$studentArear['studentid'];
                if(!$studentId){
                    $data=[
                        "message"=>"You must select a student"
                    ];
                    return response()->json($data, 400);
                }
                // get class details
                $this->validateStudent($studentId, $studentArear);
                $invoiceId=$this->generateInvoice();
                $studentArear['documentno']=$invoiceId;
                $studentArear['invoicedon']=strval(Carbon::now());
                $studentArear['paymenttermid']=1;
                //process services
                $response=$this->demonAdd(new Request($studentArear), 'member_payable_arears');
                
            } 
            return response()->json($response, $response->status());
        }catch(\Exception $e){
            \Log::error($e->getMessage());

            $data=[
                "message"=>"error".' '. $e->getMessage(),
                "status"=>401
            ];
            return response()->json($data, 401);
        }
    }

    public function delete_arear(Request $request){
        try{
            $id=$request->input('id');
            if(!$id){
                $data=[
                    "message"=>"Invalid Data"
                ];
                return response()->json($data, 400);
            }
            $arrearData=MemberPayableArear::findOrFail($id);
            if(!$arrearData){
                $data=[
                    "message"=>"invalid id"
                ];
                return response()->json($data, 400);
            }
            $response=$this->demonAdd(new Request($arrearData), 'deleted_member_payerables');
            if($response->status()!=201){
                $data=[
                    "message"=>"error deleting arear contact admin"
                ];
                return response()->json($data, 401);
            }
            $this->demonDelete('member_payable_arears', $id);
            

        }catch(Exception $e){
            Log::error('Delete arear error: ' .$e->getMessage());
            $data=[
                "message"=>'error: ' . $e->getMessage()
            ];
            return response()->json($data, 401);
        }
    }
    
}
