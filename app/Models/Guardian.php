<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guardian extends Model
{
    use HasFactory;
    protected $fillable = [
        "parent_name",
        "mobile",
        "secondary_mobile",
        "city",
        "town",
        "address",
    ];
}
