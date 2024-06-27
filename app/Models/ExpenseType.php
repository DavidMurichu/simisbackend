<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseType extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'createdby',
        'createdon',
        'lasteditedby',
        'lasteditedon',
        'ipaddress',
        'is_enabled',
    ];
    public function get_fillable(){
        return $this->fillable;
    }

}
