<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchAcademicYearTerm extends Model
{
    use HasFactory;
    protected $fillable = [
        'academicyear',
        'term',
        'name',
        'description',
        'startdate',
        'enddate',
        'is_active',
    ];
    public function get_fillable(){
        return $this->fillable;
    }

}
