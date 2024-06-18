<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchClient extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'city',
        'town',
        'neighbourhood',
        'contactname',
        'website',
        'streetaddress',
        'mobile',
        'email',
        'remarks',
        'createdby',
        'createdon',
        'is_active',
        'ipaddress',
        'lasteditedby',
        'lasteditedon',
        'plotno',
        'clientno',
        'contactmobile',
    ];
    public function get_fillable(){
        return $this->fillable;
    }
}
