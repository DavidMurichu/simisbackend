<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SysGender extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
        'remarks',
        'createdby',
        'lasteditedby',
        'ipaddress',
        'is_active',
        'created_at',
        'updated_at'
    ];
    public function get_fillable(){
        return $this->fillable;
    }
}
