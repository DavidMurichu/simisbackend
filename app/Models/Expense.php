<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'expenseType',
        'code',
        'description',
        'createdby',
        'createdon',
        'lasteditedby',
        'lasteditedon',
        'ipaddress',
        'is_active',
        'branch_id',
    ];
    public function get_fillable(){
        return $this->fillable;
    }
}
