<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberPayableArear extends Model
{
    use HasFactory;
    protected $fillable = [
        'studentid',
        'paymenttermid',
        'studentclasspromotiontermid',
        'documentno',
        'invoicedon',
        'amount',
        'remarks',
        'createdby',
        'lasteditedby',
        'ipaddress',
        'is_active',
    ];
    public function get_fillable(){
        return $this->fillable;
    }
}
