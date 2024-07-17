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

    public function classTerms()
    {
        return $this->hasManyThrough(
            SchStudentClassTerm::class,
            SchStudentClassPromotion::class,
            'id', // Foreign key on promotions table
            'studentclasspromotionid', // Foreign key on terms table
            'promotion_id', // Local key on service table (optional if different)
            'id' // Local key on promotions table
        );
    }

    public function get_fillable(){
        return $this->fillable;
      }
  
}
