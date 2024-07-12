<?

namespace App\Service;

use App\Models\SchStudent;

class StudentService
{
    public function getStudentsWithServices()
    {
        $students = SchStudent::with([
            'currentAcademicYear:id,name',
            'currentClass:id,name',
            'currentTerm:id,name',
            'services.service:id,name' // eager load the service name
        ])->get();

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
                        'amount' => $service->amount // Make sure to add 'amount' to SchStudentService fillable
                    ];
                })->toArray()
            ];
        })->toArray();
    }
}
