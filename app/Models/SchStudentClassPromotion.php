<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchStudentClassPromotion extends Model
{
    use HasFactory;
    protected $fillable = [
        'studentid',
        'current_class_id',
        'academicyear',
        'promotedon',
        'createdby',
        'lasteditedby',
        'ipaddress',
        'is_active',
    ];

    public function classTerm()
    {
        return $this->hasMany(SchStudentClassTerm::class, 'studentclasspromotionid');
    }
    public function get_fillable(){
        return $this->fillable;
    }
}
