<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class SchDemotedStudent extends Model
{
    use HasFactory;

    protected $fillable = [
        'studentclasstermid',
        'datedemoted',
        'current_class_id',
        'current_term_id',
        'academicyear',
        'demotedby',
        'createdby',
        'lasteditedby',
        'ipaddress',
        'is_active',
    ];
    public function get_fillable(){
        return $this->fillable;
    }

    public function invoke(Request $request){
        return $this->fillable;
    }




}
