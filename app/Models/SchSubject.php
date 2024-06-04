<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchSubject extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'name',
        'subject_code',
        'description',
        'createdby',
        'lasteditedby',
        'ipaddress',
        'is_active',
        'created_at',
        'updated_at',
    ];
    public function get_fillable(){
        return $this->fillable;
    }
}
