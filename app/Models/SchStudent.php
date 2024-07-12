<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchStudent extends Model
{
    use HasFactory;
    protected $fillable = [
    "name",
    "admission_no",
    "nemis_number",
    "assessment_number",
    "parentname",
    "parent_mobile",
    "secondary_mobile",
    "dob",
    "city",
    "town",
    "streetaddress",
    "mobile",
    "email",
    "remarks",
    "createdby",
    "is_active",
    "ipaddress",
    "lasteditedby",
    "teachers_student",
    "deactivated",
    "deactivate_reason",
    "fathers_name",
    "fathers_phone",
    "mothers_name",
    "mothers_phone",
    "guardians_name",
    "guardians_phone",
    "last_school_attended",
    "birth_cert_no",
    "upi_no",
    "academicyearid",
    "prev_class_id",
    "current_class_id",
    "current_term_id",
    "transfer_term_id",
    "genderid",
    "branch_id"
    ];

    public function currentAcademicYear()
{
    return $this->belongsTo(SchAcademicYear::class, 'academicyearid', 'name');
}

public function currentClass()
{
    return $this->belongsTo(SchClass::class, 'current_class_id', 'name');
}
public function currentTerm()
{
    return $this->belongsTo(SchTerm::class, 'current_term_id', 'name');
}

public function services()
{
    return $this->hasMany(SchStudentService::class, 'studentid', 'id');
}

    public $relations=['currentAcademicYear', 'currentClass', 'currentTerm'];
    
    public function get_fillable(){
        return $this->fillable;
    }
}
