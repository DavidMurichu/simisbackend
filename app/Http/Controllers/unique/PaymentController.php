<?php

namespace App\Http\Controllers\unique;

use App\Http\Controllers\Controller;
use App\Models\SchAcademicYearTerm;
use App\Models\SchFeeInvoice;
use App\Models\SchStudent;
use App\Models\SchStudentClassPromotion;
use App\Models\SchStudentClassTerm;
use App\Models\SchStudentServiceInvoice;
use App\Models\StudentFinanceRecord;
use App\Traits\DemonTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class PaymentController extends Controller
{
    
    use DemonTrait;
    public function processPayment(Request $request) {
        $paymentData = $request->input('paymentdata');
        $serviceData = $request->input('servicesinfo');

        \Log::info('check 1');
    
        // Return if no payment data
        if (empty($paymentData)) {
            return response()->json([
                'status' => 400,
                'message' => 'Payment data not set'
            ], 400);
        }
    
        // Process the payment
        $PaymentResponse = $this->createPayment($paymentData);

        if (!$PaymentResponse['success']) {
            return response()->json([
                'status' => 'error',
                'message' => $PaymentResponse['message']
            ], 401);
        }
        \Log::info('check 2');

    
        $paymentId = $PaymentResponse['paymentid'];
        // Process the invoice payment
        $processInvoicePayment = $this->processInvoicePayment($paymentData);
        if (!$processInvoicePayment['success']) {
            return response()->json([
                'status' => 'error',
                'message' => $processInvoicePayment['message']
            ], 401);
        }
        \Log::info('check 3');

        // Process the service payment if service data is provided
        if (!empty($serviceData)) {
            $serviceStatus = $this->processServicePayment($serviceData,  $paymentId);
            if ($serviceStatus['status'] === 'error') {
                return response()->json([
                    'status' => 'error',
                    'message' => $serviceStatus['message']
                ], 401);
            }
        }
        \Log::info('check 4');
    
        // Return success response
        return response()->json([
            'status' => 'success',
            'message' => 'Payment processed successfully'
        ], 200);
    }
    


    public function createPayment($paymentData) {
        try {
            // Attempt to add the payment data using demonAdd
            $payresponse = $this->demonAdd(new Request($paymentData), 'sch_fee_payments', true);
            // Check if the payment creation was successful
            if ($payresponse['status'] == 201) {
                $paymentId = $payresponse['results'][0]['dataid'];
                return [
                    'success' => true,
                    'paymentid' => $paymentId
                ];
            } else {
                // Handle the case where demonAdd did not return the expected status
                return [
                    'success' => false,
                    'message' => 'Failed to create payment'
                ];
            }
    
        } catch (Exception $e) {
            // Return a detailed error message if an exception occurs
            return [
                'success' => false,
                'message' => 'Error creating payment: ' . $e->getMessage()
            ];
        }
    }
    


    public function processInvoicePayment($paymentData) {
        try {
            // Validate required payment data fields
            if (!isset($paymentData['studentclasspromotiontermid']) || !isset($paymentData['amountpaid'])) {
                throw new Exception('Missing required payment data fields');
            }
    
            $class_term = $paymentData['studentclasspromotiontermid'];
            $Debit_Amount = $paymentData['amountpaid'];
    
            // Fetch the student's invoice
            $Student_Invoice = SchFeeInvoice::where('studentclasstermsid', $class_term)->first();
    
            // Check if the invoice exists
            if (!$Student_Invoice) {
                throw new Exception('Invoice not found for the given student class term ID');
            }
    
            $Credit_Amount = $Student_Invoice->balance;
    
            // Calculate the new balance
            $balance = $Credit_Amount - $Debit_Amount;

    
            // Prepare data for updating the invoice balance
            $data = [
                "id" => $Student_Invoice->id,
                "info" => [
                    "id" => $Student_Invoice->id,
                    "changedFields" => [
                        "balance" => $balance,
                        
                    ]
                ]
            ];
    
            // Update the invoice using the demonEdit method
            $response = $this->demonEdit('sch_fee_invoices', $data, true);
          

            if ($response['success']) {
                return [
                    'success' => true,
                    'message' => 'Invoice updated successfully'
                ];
            } else {
                throw new Exception('Error updating the invoice');
            }
    
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    public function processServicePayment($serviceData, $paymentId) {
        try {
            foreach($serviceData as $service){
                // Validate required service data fields
            if (!isset($service['studentserviceid']) || !isset($service['amount'])) {
                throw new Exception('Missing required service data fields');
            }

            $service['paymentid']=$paymentId;
            $student_service_id = $service['studentserviceid'];
            $Debit_Amount = $service['amount'];
    
            // Fetch the student's service invoice
            $Student_service_Invoice = SchStudentServiceInvoice::where('studentserviceid', $student_service_id)->first();
            

            // Check if the service invoice exists
            if (!$Student_service_Invoice) {
                throw new Exception('Service invoice not found for the given student service ID');
            }
    
            $Credit_Amount = $Student_service_Invoice->balance;
    
            // Calculate the new balance
            $balance = $Credit_Amount - $Debit_Amount;
    
            // Prepare data for updating the service invoice balance
            $data = [
                "id" => $Student_service_Invoice->id,
                "info" => [
                    "id" => $Student_service_Invoice->id,
                    "changedFields" => [
                        "balance" => $balance
                    ]
                ]
            ];
            \Log::info('check 3.1');
    
            // Update the service invoice using the demonEdit method
            $responseEdit = $this->demonEdit('sch_student_service_invoices', $data, true);
        \Log::info('check 3.5');
            $service['balance']=$balance;
    
            if (!$responseEdit['success']) {
                throw new Exception('Error updating the service invoice');
            }
    
            // Add the service payment record
            $responseAdd = $this->demonAdd($service, 'sch_service_payments', true);

            \Log::info('check 3.6', $responseAdd);
    
            if ($responseAdd['status']!=201) {
                throw new Exception('Error adding the service payment');
            }
            \Log::info('check 3.7', $responseAdd);
    
            return [
                'status' => 'success'
            ];

            }
            
    
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    public function getstudents(Request $request)
{
    try {
        // Validate incoming request data
        $request->validate([
            'classid' => 'required|string',
            'termid' => 'required|string',
            'academicyearid' => 'required|string',
        ]);

        // Retrieve inputs from request
        $classId = $request->input('classid');
        $termId = $request->input('termid');
        $academicYearId = $request->input('academicyearid');

        // Fetch student IDs from SchFeeInvoice
        $studentIds = SchFeeInvoice::all()->pluck('studentid');

        // Fetch student promotions
        $studentPromotionIds = SchStudentClassPromotion::whereIn('studentid', $studentIds)->pluck('id');
        $academicYearTerm = SchAcademicYearTerm::where('academicyear', $academicYearId)->first();
        $studentClassTerms = SchStudentClassTerm::whereIn('studentclasspromotionid', $studentPromotionIds)
            ->where('term', $termId)
            ->where('classterm', $academicYearTerm->classterm)
            ->get()
            ->keyBy('studentclasspromotionid');

        $studentPromotionIds = $studentClassTerms->pluck('studentclasspromotionid');
        $studentsIds = SchStudentClassPromotion::whereIn('id', $studentPromotionIds)->pluck('studentid');

        // Query students with relationships
        $students = SchStudent::with([
            'currentAcademicYear:id,name',
            'currentClass:id,name',
            'currentTerm:id,name',
            'services.service:id,name,cost',
        ])
            ->where('current_class_id', $classId)
            ->where('current_term_id', $termId)
            ->where('academicyearid', $academicYearId)
            ->whereIn('id', $studentsIds)
            ->get();

        // Create the response data structure
        $responseData = $students->map(function($student) use ($studentClassTerms) {
            $promotion = SchStudentClassPromotion::where('studentid', $student->id)
                ->where('current_class_id', $student->current_class_id)
                ->where('academicyear', $student->academicyearid)
                ->first();

            $currentClassTerm = $studentClassTerms->get(optional($promotion)->id);

            return [
                'student_info' => $student,
                'current_class_term' => $currentClassTerm
            ];
        });

        // Return response with students data
        return response()->json([
            'success' => true,
            'students' => $responseData,
        ], 200);

    } catch (ValidationException $e) {
        // Handle validation errors
        return response()->json([
            'status' => 'error',
            'message' => 'Validation failed',
            'errors' => $e->errors(),
        ], 422);

    } catch (ModelNotFoundException $e) {
        // Handle model not found errors
        return response()->json([
            'status' => 'error',
            'message' => 'Data not found',
        ], 404);

    } catch (\Exception $e) {
        // Handle other unexpected errors
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to retrieve students',
            'error' => $e->getMessage(),
        ], 500);
    }
}

    
}


