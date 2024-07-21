<?php


namespace App\Http\Controllers\unique;

use App\Http\Controllers\Controller;
use App\Models\SchAcademicYearTerm;
use App\Models\SchStudentClassPromotion;
use App\Models\SchStudentClassTerm;
use App\Models\SchStudent;
use App\Traits\DemonTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PromotedStudentsController extends Controller
{
    use DemonTrait;

    public function getAllPromotedStudents(Request $request)
    {
        try {
                $class = $request->input('classid');
                $academicYear = $request->input('academicyearid');
                $academicYearTermName = SchAcademicYearTerm::where('academicyear', $academicYear)->value('name');
                // Fetching all promoted student data for the given class and academic year
                $promotionData = SchStudentClassPromotion::with(['student'])
                    ->where('current_class_id', $class)
                    ->where('academicyear', $academicYear)
                    ->get();
            
                // Extracting the IDs of all promoted students
                $promotionIds = $promotionData->pluck('id')->toArray();
            
                // Fetching the IDs of reported students for the given term
                $reported = SchStudentClassTerm::whereIn('studentclasspromotionid', $promotionIds)
                    ->where('classterm', $academicYearTermName)
                    ->pluck('studentclasspromotionid')
                    ->unique()
                    ->toArray();
            
                // Finding the IDs of non-reported students
                $nonReportedPromotions = array_values(array_diff($promotionIds, $reported));
            
                // Filtering the already fetched promotion data to get non-reported promotions
                $nonReportedStudents = $promotionData->filter(function ($promotion) use ($nonReportedPromotions) {
                    return in_array($promotion->id, $nonReportedPromotions);
                });
                \Log::info('date', $nonReportedStudents->toArray());
                return response()->json($nonReportedStudents->values(), 200);
        } catch (\Exception $e) {
            \Log::error('Error fetching promoted students: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch promoted students'], 500);
        }
    }

    public function getAllPromoted(Request $request, $tableName = null)
    {
        try {
            if (!$tableName) {
                $promotions = SchStudentClassPromotion::get(['id', 'studentid']);
            } else {
                $classTerms = SchStudentClassTerm::with('studentclasspromotion')
                    ->get(['id', 'studentclasspromotionid']);

                $promotionIds = $classTerms->pluck('studentclasspromotionid')->filter()->unique();

                $promotions = SchStudentClassPromotion::whereIn('id', $promotionIds)
                    ->get(['id', 'studentid']);
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
        } catch (\Exception $e) {
            \Log::error('Error fetching all promoted students: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch promoted students'], 500);
        }
    }

    public function promoteOrDemoteStudent(Request $request, $tableName)
    {
        \Log::info("Promotion request data", $request->all());

        if (!$this->isAllowed($tableName, 'create')) {
            return response()->json(["message" => "Unauthorized"], 401);
        }

        $data = $request->all();

        if (!is_array(reset($data))) {
            // If the data is not an array of arrays, wrap it in an array
            $data = [$data];
        }

        try {
            // Start a database transaction
            DB::beginTransaction();

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
                            "current_class_id" => $entryRequest->current_class_id,
                            "prev_class_id" => $previousClass,
                            "current_term_id" => $entryRequest->current_term_id,
                        ]
                    ]
                ];

                // Handle promotion within the transaction
                $dataresponse = $this->handlePromotion($entryRequest, $studentId, $tableName);
                \Log::info('Promotion handled successfully', $dataresponse);

                if($dataresponse['status']!=201){
                    throw new \Exception($dataresponse['results'][0]['message']);
                }

                // Demon edit operation within the transaction
                $response = $this->demonEdit('sch_students', $dataToEdit);
                \Log::info('Student record updated successfully');

                if ($response->getStatusCode() != 200) {
                    \Log::info('Error response from demonEdit', $response);
                    throw new \Exception("Failed to update student record", $response->getStatusCode());
                }
            }

            // Commit the transaction if all operations are successful
            DB::commit();

            return $dataresponse;
        } catch (\Exception $e) {
            // Rollback the transaction in case of any exception
            DB::rollBack();

            $status = $e->getCode();
            $message = $e->getMessage();

            \Log::error('Error promoting/demoting student: ' . $message);

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
        $currentClassId = $request->input('current_class_id');
        $academicYear = $request->input('academicyear');

        $existingPromotion = SchStudentClassPromotion::where([
            ['studentid', '=', $studentId],
            ['current_class_id', '=', $currentClassId],
            ['academicyear', '=', $academicYear],
        ])->first();

        if ($existingPromotion) {
            throw new \Exception('Promotion already exists for this student, class, and academic year', 409);
        }

        return $this->demonAdd($request, $tableName, true);
    }
}



     
    

        // private function handleDemotion(Request $request, $studentId, $tableName)
        // {   
        //     $response=$this->promotionTableEdit($studentId, $request);

        //     if ($response->getStatusCode() != 200) {
        //     \Log::info($response);
        //     throw new \Exception("Failed to update promotion record", $response->getStatusCode());
        // }
        //     return $this->demonAdd($request, $tableName);
        // }
        

        // private function promotionTableEdit($studentId, $request){
        //     $promotionTableId=SchStudentClassPromotion::where('studentid', $studentId)->first();
        //     \Log::info( $promotionTableId);
        //     $dataToEdit = [
        //         "id" => $request->createdby,
        //         "info" => [
        //             "id" => $promotionTableId->id,
        //             "changedFields" => [
        //                 "academicyear" => $request->academicyear,
        //                 "lasteditedby" => $request->createdby,
        //                 "current_class_id" => $request->current_class_id,
        //             ]
        //         ]
        //     ];
             
        //     return $this->demonEdit("sch_student_class_promotions", $dataToEdit);
        // }