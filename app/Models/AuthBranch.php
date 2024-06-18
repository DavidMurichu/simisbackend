<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuthBranch extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_name',
        'location',
        'remarks',
        'createdby',
        'lasteditedby',
        'lasteditedon',
        'ipaddress',
        'is_active',
        
    ];

    public function get_fillable(){
        return $this->fillable;
    }
}
