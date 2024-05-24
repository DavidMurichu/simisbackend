<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;
    protected $fillable = [
		'name',
        'gender',
        'tribe',
        'nationality',
        'dob',
        'address_area',
        'town',
        'adm_year',
        'parent_id',
        'image',
        'nemis_number',
        'disabled',
        'is_activate',
		'email'
    ];
}
