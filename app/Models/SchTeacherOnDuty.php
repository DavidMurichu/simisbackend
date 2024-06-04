<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchTeacherOnDuty extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'teacherid',
        'duty_start',
        'duty_end',
        'comments',
        'createdby',
        'lasteditedby',
        'ipaddress',
        'is_active',
        'created_at',
        'updated_at'
    ];
    public function get_fillable(){
        return $this->fillable;
    }
}
