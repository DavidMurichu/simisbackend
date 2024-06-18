<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchServiceDuration extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'name',
        'description',
        'createdby',
        'lasteditedby',
        'ipaddress',
        'is_active',
    ];

    public function get_fillable(){
        return $this->fillable;
    }
}
