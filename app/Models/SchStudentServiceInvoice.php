<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchStudentServiceInvoice extends Model
{
    use HasFactory;
    protected $fillable = [
        'amount',
        'balance',
        'createdby',
        'lasteditedby',
        'ipaddress',
        'is_active',
        'invoiceid',
        'studentserviceid',
    ];

    public function studentservice(){
        return $this->belongsTo(SchStudentService::class, 'studentserviceid', 'id');
    }
    public function get_fillable(){
        return $this->fillable;
      }
    
}
