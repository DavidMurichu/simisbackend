<?php

namespace App\Http\Controllers\unique;

use App\Http\Controllers\Controller;

use App\Models\SchFeeInvoice;
use App\Models\SchStudent;
use App\Models\SchStudentClassPromotion;
use Exception;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    

   

    public function index()
    {
        try{

            $students = $this->getStudentsWithServices();

        return response()->json($students);
        }catch(Exception $e){
            \Log::info($e->getMessage());
        }
        
    }


    public function getStudentsWithServices()
    {   $invoiced = SchFeeInvoice::pluck('studentid')->toArray();
        $promoted = SchStudentClassPromotion::pluck('studentid')->toArray();
        !empty($invoiced)? $notInvoicedPromoted = array_diff($promoted, $invoiced): $notInvoicedPromoted =$promoted;
        $students = SchStudent::with([
            'currentAcademicYear:id,name',
            'currentClass:id,name',
            'currentTerm:id,name',
            'services.service:id,name,cost'
        ])->whereIn('id', $notInvoicedPromoted)->get();
        return $students->map(function ($student) {
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
                        'serviceid' => $service->serviceid,
                        'name' => $service->service->name,
                        'amount' => $service->service->cost
                    ];
                })->toArray()
            ];
        })->toArray();
    }
}
