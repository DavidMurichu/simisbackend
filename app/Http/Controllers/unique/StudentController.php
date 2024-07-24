<?php

namespace App\Http\Controllers\Unique;

use App\Http\Controllers\Controller;
use App\Models\SchAcademicYearTerm;
use App\Models\SchService;
use App\Models\SchStudent;
use App\Models\SchStudentService;
use App\Traits\DemonTrait;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    use DemonTrait;

    public function RegisterStudent(Request $request)
    {

        $studentData = $request->input('studentData');
        $servicesData = $request->input('servicesData');
       
        try {
            $academicyearterm = SchAcademicYearTerm::where('academicyear', $studentData['academicyearid'])->first();

            if (!$academicyearterm) {
                throw new Exception('Academic Year not found. Contact Admin.');
            }

            DB::beginTransaction();

            $studentId = $this->createStudent($studentData);
            $promotionId = $this->promoteStudent($studentData, $studentId, $academicyearterm->name);
            $reportingId=$this->reportStudent($studentData, $promotionId, $academicyearterm->name);

            if($servicesData){
                $this->addStudentServices($servicesData, $studentId, $reportingId, $studentData);
            }

            DB::commit();

            return $this->successResponse('Student registered successfully', 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage(), 401);
        }
    }

    private function createStudent(array $studentData)
    {
        $response = $this->createData($studentData, 'sch_students');
        if (!$response['success']) {
            throw new Exception($response['message']);
        }

        return $response['dataId'];
    }

    private function promoteStudent(array $studentData, $studentId, $academicyeartermName)
    {
        $promotionData = [
            'studentid' => $studentId,
            'current_class_id' => $studentData['current_class_id'],
            'academicyear' => $studentData['academicyearid'],
            'promotedon' => Carbon::now(),
            'createdby' => $studentData['createdby'],
            'lasteditedby' => $studentData['lasteditedby'],
            'is_active' => $studentData['is_active']
        ];

        $response = $this->createData($promotionData, 'sch_student_class_promotions');
        if (!$response['success']) {
            throw new Exception($response['message']);
        }

        return $response['dataId'];
    }

    private function reportStudent(array $studentData, $promotionId, $academicyeartermName)
    {
        $reportingData = [
            'studentclasspromotionid' => $promotionId,
            'term' => $studentData['current_term_id'],
            'classterm' => $academicyeartermName,
            'reportingdate' => Carbon::now()->format('Y-m-d H:i:s'),
            'createdby' => $studentData['createdby'],
            'lasteditedby' => $studentData['lasteditedby'],
            'is_active' => $studentData['is_active']
        ];

        $response = $this->createData($reportingData, 'sch_student_class_terms');
        if (!$response['success']) {
            throw new Exception($response['message']);
        }
        return $response['dataId'];
    }


    private function addStudentServices($servicesData, $studentId, $reportingId, $commonData)
{
    try {
        // Fetch the services by their IDs
        $services = SchService::whereIn('id', $servicesData)->get();
        \Log::info('check 5', $services->toArray());

        // Iterate through each service
        foreach ($services as $service) {
            // Prepare the service data for insertion
            $serviceRecord = [
                'studentid' => $studentId,
                'studentclasspromotiontermid' => $reportingId,
                'serviceid' => $service->id,
                'status' => $service->status?? '1',
                'createdby' => $commonData['createdby'],
                'lasteditedby' => $commonData['lasteditedby'],
                'is_active' => $service->is_active,
                
            ];

            // Insert the service record into the database
            $response = $this->createData($serviceRecord, 'sch_student_services');

            // Check if the insertion was successful
            if (!$response['success']) {
                throw new Exception($response['message']);
            }
        }
    } catch (Exception $e) {
        // Log the error message or handle the exception as needed
        \Log::error('Error adding services: ' . $e->getMessage());
        throw $e;
    }
}
    private function createData($data, $tableName)
    {
        try {
            $response = $this->demonAdd(new Request($data), $tableName, true);
            if ($response['status'] == 201) {
                $dataId = $response['results'][0]['dataid'];
                return [
                    'success' => true,
                    'dataId' => $dataId
                ];
            } else {
                throw new Exception($response['results'][0]['message']);
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error creating data: ' . $e->getMessage()
            ];
        }
    }

    private function successResponse($message, $status = 200)
    {
        return response()->json([
            'status' => $status,
            'message' => $message
        ], $status);
    }

    private function errorResponse($message, $status = 500)
    {
        return response()->json([
            'status' => 'error',
            'message' => $message
        ], $status);
    }
}
