<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paymentmode extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'acctypeid',
        'is_bank',
        'remarks',
        'createdby',
        'createdon',
        'lasteditedby',
        'lasteditedon',
        'ipaddress',
        'is_active'
    ];

    public function get_fillable(){
        return $this->fillable;
    }
}
