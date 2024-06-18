<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchFeeInvoice extends Model
{
    use HasFactory;
    protected $fillable = [
        'studentid',
        'invoicedate',
        'invoiceno',
        'amount',
        'balance',
        'classid',
        'termid',
        'studentclasstermsid',
        'is_reversed',
        'createdon',
        'createdby',
        'lasteditedon',
        'lasteditedby',
        'ipaddress',
        'is_active',
    ];
    public function get_fillable(){
      return $this->fillable;
    }


}
