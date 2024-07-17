<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubAccountType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'accounttypeid',
        'priority',
        'remarks',
        'ipaddress',
        'createdby',
        'lasteditedby',
        'is_active'
    ];

    public function get_fillable(){
        return $this->fillable;
    }
}
