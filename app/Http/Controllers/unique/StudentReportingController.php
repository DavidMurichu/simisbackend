<?php


namespace App\Http\Controllers\unique;
use App\Http\Controllers\Controller;


use App\Models\SchStudent;
use App\Models\SchStudentClassPromotion;
use App\Traits\DemonTrait;
use Illuminate\Http\Request;

class StudentReportingController extends Controller
{
    use DemonTrait;
    public function studentClassTermReporting(Request $request){
    try{
        $studentId=SchStudentClassPromotion::whereIn('studentclasspromotionid', $request->studentclasspromotionid)->get('studentid');
        $prev_term = SchStudent::where('id', $studentId)->value('current_term_id');
        $dataToEdit = [
            "id" => $request->createdby,
            "info" => [
                "id" => $request->studentId,
                "changedFields" => [
                    "academicyearid" => $request->academicyear,
                    "lasteditedby" => $request->createdby,
                    "current_term_id"=>$request->current_term_id,
                    "transfer_term_id"=>$prev_term,
                ]
            ]
        ];

        $response = $this->demonEdit('sch_students', $dataToEdit);
        if ($response->getStatusCode() != 200) {
            throw new \Exception("Failed to update student record", $response->getStatusCode());
        }

        return $this->demonAdd($request, 'sch_student_class-terms');
    } catch (\Exception $e) {
        // Handle exceptions
        $status = $e->getCode();
        $message = $e->getMessage();

        // Log the error
        \Log::error($message);

        // Return error response
        return response()->json(['status' => $status, 'message' => $message], $status);
    }
    }
}
