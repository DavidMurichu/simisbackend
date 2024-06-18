<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchAcademicYear extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'startdate',
        'enddate',
        'createdby',
        'lasteditedby',
        'ipaddress',
        'is_active',
        'year'
    ];
    public function get_fillable(){
        return $this->fillable;
    }

}
