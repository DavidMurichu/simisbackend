<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchStudentServiceInvoiceLog extends Model
{
    use HasFactory;
    protected $fillable = [
        'invoiceid',
        'studentserviceid',
        'amount',
        'balance',
        'createdby',
        'lasteditedby',
        'ipaddress',
        'is_active',
        'action',
    ];
    public function get_fillable(){
        return $this->fillable;
      }
}
