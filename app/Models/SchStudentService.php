<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchStudentService extends Model
{
    use HasFactory;

    protected $fillable = [
        'studentid',
        'studentclasspromotiontermid',
        'serviceid',
        'status',
        'createdby',
        'lasteditedby',
        'ipaddress',
        'is_active',
    ];

    public function get_fillable(){
        return $this->fillable;
      }
  
}
