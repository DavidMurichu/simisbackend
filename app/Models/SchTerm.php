<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchTerm extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'ipaddress',
        'is_active',
        'active_term'
    ];

    public function get_fillable(){
        return $this->fillable;
    }


}
