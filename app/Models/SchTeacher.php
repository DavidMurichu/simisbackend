<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchTeacher extends Model
{

    use HasFactory;
    protected $fillable = [
        'name',
        'surname',
        'firstname',
        'lastname',
        'title',
        'mobileno',
        'email',
        'physicaladdress',
        'createdby',
        'lasteditedby',
        'ipaddress',
        'is_active',
    ];
    public function get_fillable(){
        return $this->fillable;
    }

}
