<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SchStudentClassTerm extends Model
{
    use HasFactory;
    protected $fillable = [
        'studentclasspromotionid',
        'term',
        'classterm',
        'reportingdate',
        'createdby',
        'lasteditedby',
        'ipaddress',
        'is_active',
    ];

    public function studentclasspromotion()
    {
        return $this->belongsTo(SchStudentClassPromotion::class);
    }

    public function get_fillable(){
        return $this->fillable;
    }

}
