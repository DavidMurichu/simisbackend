<?php

namespace App\Http\Controllers\unique;

use App\Http\Controllers\Controller;
use App\Models\DeletedMemberPayerable;
use App\Models\MemberPayableArear;
use App\Models\SchFeeInvoice;
use App\Models\SchFeeReversedInvoice;
use App\Models\SchFeeStructureVoteHead;
use App\Models\SchStudent;
use App\Models\SchStudentClassPromotion;
use App\Models\SchStudentClassTerm;
use App\Models\SchStudentService;
use App\Models\test\InvoiceHolder;
use App\Traits\DemonTrait;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class InvoicesController extends Controller
{
    use DemonTrait;

    public function processInvoice(Request $request)
    {
        try {
            // Separate common data from the request
            $commonData = $request->input('commondata');
            $studentInvoices = $request->input('studentinvoices');
    
            // Check if there are selected student invoices
            if (empty($studentInvoices)) {
                return response()->json(['message' => "No selected students."], 400);
            }
    
            // Begin a database transaction
            \DB::beginTransaction();
    
            foreach ($studentInvoices as $studentInvoiceData) {
                $studentId = $studentInvoiceData['studentid'];
                $invoiceData = $studentInvoiceData['invoiceData'];
                $voteHeadData = $studentInvoiceData['voteheads'];
                $serviceData = $studentInvoiceData['services'];
    
                // Create an Invoice for each student
                $invoiceResult = $this->createInvoice($invoiceData, $commonData, $studentId);
              
                if (!$invoiceResult['success']) {
                    throw new \Exception("Error creating invoice for student ID: $studentId");
                }
    
                $invoiceId = $invoiceResult['invoice_id'];
                
                // Process Vote Heads
                if(!empty($voteHeadData)){
                $this->processVoteHeads($voteHeadData, $commonData, $invoiceId);
                }
                
                // Process Services
                if(!empty($serviceData)){
                $this->processServices($serviceData, $commonData, $invoiceId, $studentId);
                }
            }
    
            // Commit the transaction
            \DB::commit();
    
            // Return Success Response
            return response()->json(['message' => 'Invoices processed successfully.'], 200);
    
        } catch (\Exception $e) {
            // Rollback the transaction on error
            \DB::rollBack();
    
            // Log the error
            Log::error('Invoice processing error: ' . $e->getMessage());
    
            // Return error response
            return response()->json(['error' => 'Failed to process invoices. Please try again.'], 500);
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
        try{
            $promotionId = SchStudentClassPromotion::where('studentid', $studentId)->first();

            if ($promotionId) {
                // If promotion ID is found, fetch class terms associated with it
                $classterm = SchStudentClassTerm::with('studentclasspromotion')
                    ->where('studentclasspromotionid', $promotionId->id)
                    ->first();
            } else {
                // Handle case where no promotion ID is found for the student ID
                Log::info("No promotion found for student ID: $studentId");
                throw new \Exception("Student have not reported." );

            }
        }catch(Exception $e){
            throw new \Exception("Student not promoted.".$e );
        }
        // Validate invoice data against student's current details

    



        if (
            $promotionId->current_class_id != $invoiceData['classid']
        ) {
            throw new \Exception("invalid Class. ");
           
        }
        
        if (
            $classterm->term != $invoiceData['termid']
        ) {
            throw new \Exception("invalid term. ");

        }
        
        if (
            $promotionId->academicyear != $invoiceData['academicyearid']
        ) {
            throw new \Exception("invalid academic year. ");
        }
        // Generate Invoice Number
        $invoiceNumber = $this->generateInvoice();
        // Merge invoice number into invoice data
        
        $invoiceData['studentclasstermsid'] =$classterm->id;
        $invoiceData['classid'] =$student->currentClass->id;
        $invoiceData['invoiceno'] = $invoiceNumber;
        $invoiceData['studentid']=$studentId;
        $invoiceData['termid']=$student->currentTerm->id;
        $invoiceData['academicyearid']=$student->currentAcademicYear->is;
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
            $voteHeadEntryData = [
                "invoiceid" => $invoiceId,
                "feestructurevoteheadsid" => $voteHeadEntry['voteheadid'],
                "amount" => $voteHeadEntry['amount'],
                "createdby" => $commonData["createdby"],
                "is_active"=>"1"
            ];
            Log::info('turn', $voteHeadEntryData);
            // Insert Vote Head Data
            $response=$this->demonAdd(new Request($voteHeadEntryData), 'sch_vote_head_invoice_details');
            if($response->status()!=201){
            throw new \Exception($response);
                
            }
        }
    }

    private function processServices($serviceData, $commonData, $invoiceId, $studentId)
    {
        foreach ($serviceData as $serviceEntry) {
            try {
                // Check if the service exists for the student
                $service = SchStudentService::where('id', $serviceEntry["serviceid"])->first();
        
                if (!$service) {
                    Log::error("Service not found for student with ID: {$serviceEntry['serviceid']}");
                    continue;
                }
        
                // Prepare service data for insertion
                $serviceData = [
                    "invoiceid" => $invoiceId,
                    "studentid" => $studentId,
                    "studentserviceid" => $service->id, // Assuming service id is needed
                    "amount" => $serviceEntry["amount"],
                    "balance" => $serviceEntry["balance"],
                    "createdby" => $commonData["createdby"],
                    "is_active" => "1"
                ];
        
                // Insert Service Data
                $response = $this->demonAdd(new Request($serviceData), 'sch_student_service_invoices');
        
                // Check if insertion was successful
                if ($response->status() != 201) {
                    throw new \Exception("Failed to insert service invoice data.");
                }
        
            } catch (\Exception $e) {
                Log::error("Error processing service invoice: " . $e->getMessage());
                continue; // Skip to the next iteration on error
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
            $paymentTerms=$request->input('paymentterms');
            $commonData=$request->input('commondata');
            if(!$studentArears ){
                $data=[
                    "message"=>"No student Selected"
                ];
                return response()->json($data, 400);
            }

            if(!$paymentTerms ){
                $data=[
                    "message"=>"No Service Selected"
                ];
                return response()->json($data, 400);
            }
            foreach($paymentTerms as $paymentTerm){
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
                    // send message 
                    $documentNo=$this->generateInvoice();
                    
                    $studentArear['is_active']=$commonData['is_active'];
                    $studentArear['laseditedby']=$commonData['laseditedby'];
                    $studentArear['remarks']=$commonData['remarks'];
                    $studentArear['createdby']=$commonData['createdby'];
                    $studentArear['documentno']=$documentNo;
                    $studentArear['paymenttermid']=$paymentTerm;
                    $studentArear['invoicedon']=strval(Carbon::now());
                    //process services
                    $response=$this->demonAdd(new Request($studentArear), 'member_payable_arears');
                    
                } 

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

    public function delete_arear(Request $request)
    {
        try {
            // Validate the request
            $validatedData = $request->validate([
                'id' => 'required|integer|exists:member_payable_arears,id',
            ]);
    
            $id = $validatedData['id'];
            
    
            // Find the arrear data
            $arrearData = MemberPayableArear::findOrFail($id)->toArray();
            unset($arrearData['id']);
            unset($arrearData['updated_at']);
            unset($arrearData['created_at']);
           
            $inserted=DeletedMemberPayerable::insert($arrearData);
            
            
            if (!$inserted) {
                return response()->json(['message' => 'Error inserting data'], 500);
            }
    
            // Assuming demonDelete is a function that performs the actual deletion
            $this->demonDelete('member_payable_arears', $id);
    
            // Return success response
            return response()->json(['message' => 'Arrear deleted successfully'], 200);
    
        } catch (ValidationException $e) {
            // Catch validation errors
            return response()->json(['message' => 'Invalid Data', 'errors' => $e->errors()], 400);
        } catch (ModelNotFoundException $e) {
            // Catch model not found errors
            return response()->json(['message' => 'Invalid ID, arrear not found'], 404);
        } catch (Exception $e) {
            // Catch any other exceptions
            Log::error('Delete arrear error: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }
    

    public function recordPaymentTerm($paymentData){
        try{
        $data=$this->demonAdd(new Request($paymentData), 'sch_payment_terms');
        $data=(array)$data;
        Log::info('payment', $data);
        return $data['original'][0]['dataid'];
        }catch(Exception $e){
            Log::info("error". $e);
            throw new Exception("error creating payment term");
        }
        
    }

    public function reverseInvoice(Request $request)
{
    try {
        // Validate input
        $request->validate([
            'id' => 'required|exists:sch_fee_invoices,id',
        ]);

        $id = $request->input('id');

        // Fetch the invoice data to be reversed
        $invoiceData = SchFeeInvoice::find($id);

        if (!$invoiceData) {
            return response()->json(['error' => 'Invoice not found'], 404);
        }

        // Begin a database transaction
        \DB::beginTransaction();

        // Create a reversed invoice record
        $reversedInvoice = new SchFeeReversedInvoice();
        $reversedInvoice->fill($invoiceData->toArray());
        $reversedInvoice->save();

        // Delete the original invoice
        $invoiceData->delete();

        // Commit the transaction
        \DB::commit();

        // Return success response
        return response()->json(['message' => 'Invoice reversed successfully'], 200);
    } catch (\Exception $e) {
        // Rollback the transaction on error
        \DB::rollBack();

        // Log the error for debugging
        \Log::error('Error reversing invoice: ' . $e->getMessage());

        // Return error response
        return response()->json(['error' => 'Failed to reverse invoice. Please try again.'], 500);
    }
}

public function getInvoicingData(Request $request)
{
    try {
        // Validate the input parameters
        $request->validate([
            'classid' => 'required|string',
            'termid' => 'required|string',
            'academicyearid' => 'required|string',
        ]);

        $classId = $request->input('classid');
        $termId = $request->input('termid');
        $academicYearId = $request->input('academicyearid');

        // Fetch vote heads data
        $voteHeads = SchFeeStructureVoteHead::where('classid', $classId)
            ->where('termid', $termId)
            ->get();

        // Fetch students who are not yet invoiced and match the given class, term, and academic year
        $invoiced = SchFeeInvoice::pluck('studentid')->toArray();
        $promoted = SchStudentClassPromotion::pluck('studentid')->toArray();
        $notInvoicedPromoted = !empty($invoiced) ? array_diff($promoted, $invoiced) : $promoted;

        $students = SchStudent::with([
            'currentAcademicYear:id,name',
            'currentClass:id,name',
            'currentTerm:id,name',
            'services.service:id,name,cost',

        ])
            ->where('current_class_id', $classId)
            ->where('current_term_id', $termId)
            ->where('academicyearid', $academicYearId)
            ->whereIn('id', $notInvoicedPromoted)
            ->get();

        // Map student data to the desired format
        $studentsData = $students->map(function ($student) {
            return [
                'id' => $student->id,
                'academicyearid' => $student->currentAcademicYear->name ?? null,
                'prev_class_id' => $student->previousClass->name ?? null,
                'current_class_id' => $student->currentClass->name ?? null,
                'current_term_id' => $student->currentTerm->name ?? null,
                'transfer_term_id' => $student->transferTerm->name ?? null,
                'name' => $student->name,
                'admission_no' => $student->admission_no,
                'services' => $student->services->map(function ($service) {
                    return [
                        'studentserviceid' => $service->id,
                        'name' => $service->service->name,
                        'amount' => $service->service->cost
                    ];
                })->toArray()
            ];
        })->toArray();

        // Return combined data as JSON response
        return response()->json([
            'success' => true,
            'vote_heads' => $voteHeads,
            'students' => $studentsData
        ]);

    } catch (\Exception $e) {
        // Handle any exceptions
        \Log::error('Error fetching invoicing data: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' =>  $e->getMessage()
        ], 500);
    }
}

    
}
