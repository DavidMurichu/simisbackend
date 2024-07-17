<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchFeePayment extends Model
{
    use HasFactory;
    protected $fillable = [
        'studentclasspromotiontermid',
        'amountpaid',
        'paymentdate',
        'actual_payment_date',
        'payidby',
        'paymentmodeid',
        'bankid',
        'description',
        'transactionno',
        'is_reversed',
        'createdon',
        'createdby',
        'lasteditedby',
        'lasteditedon',
        'ipaddress',
        'studentid',
        'is_active',
        'classid',
        'termid',
        'receiptno'
    ];
    
    public function get_fillable(){
        return $this->fillable;
      }
}
