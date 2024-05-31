<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchStudentClassPromotion extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'studentid',
        'class_name',
        'academicyear',
        'promotedon',
        'createdby',
        'lasteditedby',
        'ipaddress',
        'is_active',
        'updated_at'
    ];
    public function get_fillable(){
        return $this->fillable;
    }
}
