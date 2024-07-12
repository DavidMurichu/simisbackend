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

    public function student(){
        return $this->belongsTo(SchStudent::class, 'studentid', 'id');
    }
    public function service(){
        return $this->belongsTo(SchService::class, 'serviceid', 'id' );
    }
    public function promotion(){
        return $this->belongsTo(SchStudentClassPromotion::class, 'studentclasspromotiontermid', 'id');
    }

    public function get_fillable(){
        return $this->fillable;
      }
  
}
