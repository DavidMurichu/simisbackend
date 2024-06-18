<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchTeacherOnDuty extends Model
{
    use HasFactory;
    protected $fillable = [
        'teacherid',
        'duty_start',
        'duty_end',
        'comments',
        'createdby',
        'lasteditedby',
        'ipaddress',
        'is_active',
    ];
    public function get_fillable(){
        return $this->fillable;
    }
}
