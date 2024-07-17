<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'createdby',
        'lasteditedby',
        'ipaddress',
        'description',
    ];

    public function get_fillable(){
        return $this->fillable;
    }
}
