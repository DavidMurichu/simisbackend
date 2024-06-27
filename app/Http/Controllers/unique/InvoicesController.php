<?php

namespace App\Http\Controllers\unique;

use App\Http\Controllers\Controller;
use App\Models\SchAcademicYear;
use App\Models\SchClass;
use App\Models\SchFeeInvoice;
use App\Models\SchStudent;
use App\Models\SchStudentClassTerm;
use App\Models\test\InvoiceHolder;
use App\Traits\DemonTrait;
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

            foreach ($studentInvoices as $studentInvoiceData) {
                $studentId = $studentInvoiceData['studentid'];
                $invoiceData = $studentInvoiceData['invoiceData'];
                $voteHeadData = $studentInvoiceData['voteheads'];
                $serviceData = $studentInvoiceData['services'];

                // Create an Invoice for each student
                $invoiceResult = $this->createInvoice($invoiceData, $commonData, $studentId);

                if (!$invoiceResult['success']) {
                    return response()->json(['message' => "invoice created"], 500);
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

    private function processVoteHeads($voteHeadData, $commonData, $invoiceId)
    {
        foreach ($voteHeadData as $voteHeadEntry) {
            $voteHeadEntry = [
                "invoiceid" => $invoiceId,
                "feestructurevoteheadsid" => $voteHeadEntry['voteheadid'],
                "amount" => $voteHeadEntry['amount'],
                "createdby" => $commonData["createdby"]
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
}
