<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchServicePayment extends Model
{
    use HasFactory;

    protected $fillable=[
        'studentserviceid',
        'paymentid',
        'amount',
        'balance',
        'createdon',
        'createdby',
        'lasteditedon',
        'lasteditedby',
        'ipaddress',
        'is_active'




    ];

    public function get_fillable(){
        return $this->fillable;
    }
}
