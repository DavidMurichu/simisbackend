<?php

namespace App\Http\Controllers\unique;

use App\Http\Controllers\Controller;
use App\Models\SchStudentClassPromotion;
use App\Models\SchStudentClassTerm;
use App\Models\SchStudent;
use App\Traits\DemonTrait;
use Illuminate\Http\Request;

class PromotedStudentsController extends Controller
{
    use DemonTrait;

    public function getAllPromotedStudents()
    {
        $classTerms = SchStudentClassTerm::with('studentclasspromotion')->get();
        $studentIds = $classTerms->pluck('studentclasspromotion.studentid')->filter();
        $records = SchStudent::whereIn('id', $studentIds)->get();
        return $records;
    }

    public function getAllPromoted(Request $request, $tableName = null)
    {
        if (!$tableName) {
            $promotions = SchStudentClassPromotion::get(['id', 'studentid']);
            
        } else {
            $classTerms = SchStudentClassTerm::with('studentclasspromotion')
            ->get(['id', 'studentclasspromotionid']);

            // Step 2: Extract unique studentclasspromotion IDs from class terms
            $promotionIds = $classTerms->pluck('studentclasspromotionid')->filter()->unique();

            // Step 3: Fetch the corresponding promotions with their id and studentid
            $promotions = SchStudentClassPromotion::whereIn('id', $promotionIds)
                ->get(['id', 'studentid']);

            // Step 4: Create a map of promotion ID to student ID
            $promotionIdToStudentId = $promotions->pluck('studentid', 'id');
            

            // Step 5: Create an array of classTerm IDs with their corresponding student IDs
            // Step 5: Create an array of objects with id from classTerms and corresponding studentid
            $promotions = $classTerms->map(function ($classTerm) use ($promotionIdToStudentId) {
                return (object)[
                    'id' => $classTerm->id,
                    'studentid' => $promotionIdToStudentId->get($classTerm->studentclasspromotionid)
                ];
            });

          

            


        }

        $studentIds = $promotions->pluck('studentid')->unique();
        $students = SchStudent::whereIn('id', $studentIds)->get();

        $result = $promotions->map(function ($promotion) use ($students, $tableName) {
            $student = $students->firstWhere('id', $promotion->studentid);
            if ($student) {
                $studentArray = $student->toArray();
                $studentArray[$tableName ? 'classterm_id' : 'promotion_id'] = $promotion->id;
                return $studentArray;
            }
            return null;
        })->filter()->unique('id')->values();

        return $result;
    }

    

    public function promoteOrDemoteStudent(Request $request, $tableName)
        {  
        
           \Log::info("data", $request->all());
        $isPromote = $tableName === 'sch_student_class_promotions' ? true : false;
        if (!$this->isAllowed($tableName, 'create')) {
            return response()->json(["message" => "Unauthorized"], 401);
        }

        $data = $request->all();

        if (!is_array(reset($data))) {
            // If the data is not an array of arrays, wrap it in an array
            $data = [$data];
        }

        try {
            foreach ($data as $entry) {
                $entryRequest = new Request($entry);
                $studentId = $entryRequest->studentid ?? $this->getStudentIdFromRequest($entryRequest->studentclasstermid);
                $student = SchStudent::find($studentId);
                if (!$student) {
                    throw new \Exception("Student not found", 404);
                }


            $previousClass = $student->current_class_id;
            $dataToEdit = [
                "id" => $entryRequest->createdby,
                "info" => [
                    "id" => $studentId,
                    "changedFields" => [
                        "academicyearid" => $entryRequest->academicyear,
                        "lasteditedby" => $entryRequest->createdby,
                        "current_class_id" =>$entryRequest->current_class_id,
                        "prev_class_id" => $previousClass,
                        "current_term_id" => $entryRequest->current_term_id,
                    ]
                ]
            ];

            


          {      if ($isPromote) {
                    $dataresponse=$this->handlePromotion($entryRequest, $studentId, $tableName);
                } else {
                    $dataresponse=$this->handleDemotion($entryRequest, $studentId, $tableName);
                }}
            }
            $response = $this->demonEdit('sch_students', $dataToEdit);

            if ($response->getStatusCode() != 200) {
                \Log::info($response);
                throw new \Exception("Failed to update student record", $response->getStatusCode());
            }
            return $dataresponse;
        } catch (\Exception $e) {
            $status = $e->getCode();
            $message = $e->getMessage();

            \Log::error($message);

            if (!is_int($status) || $status < 100 || $status > 599) {
                $status = 500;
            }

            return response()->json(['status' => $status, 'message' => $message], $status);
        }
    }

    private function getStudentIdFromRequest($classTermId)
    {
        if (!$classTermId) {
            throw new \Exception("Bad request: class term ID is missing", 400);
        }

        $promotionId = SchStudentClassTerm::where('id', $classTermId)->pluck('studentclasspromotionid')->first();
        $studentId = SchStudentClassPromotion::where('id', $promotionId)->pluck('studentid')->first();

        if (!$studentId) {
            throw new \Exception("Student not found", 404);
        }

        return $studentId;
    }
        
private function handlePromotion(Request $request, $studentId, $tableName)
        {
            $existingPromotion = SchStudentClassPromotion::where('studentid', $studentId)->first();
            if (!$existingPromotion) {
                return $this->demonAdd($request, $tableName);
            } else {
                return $this->promotionTableEdit($studentId, $request);
            }
        }
        
        private function handleDemotion(Request $request, $studentId, $tableName)
        {   
            $response=$this->promotionTableEdit($studentId, $request);

            if ($response->getStatusCode() != 200) {
            \Log::info($response);
            throw new \Exception("Failed to update promotion record", $response->getStatusCode());
        }
            return $this->demonAdd($request, $tableName);
        }
        

        private function promotionTableEdit($studentId, $request){
            $promotionTableId=SchStudentClassPromotion::where('studentid', $studentId)->first();
            \Log::info( $promotionTableId);
            $dataToEdit = [
                "id" => $request->createdby,
                "info" => [
                    "id" => $promotionTableId->id,
                    "changedFields" => [
                        "academicyear" => $request->academicyear,
                        "lasteditedby" => $request->createdby,
                        "current_class_id" => $request->current_class_id,
                    ]
                ]
            ];
             
            return $this->demonEdit("sch_student_class_promotions", $dataToEdit);
        }
        
    }


        