<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SysYear extends Model
{
    use HasFactory;

    protected $fillable = [
        'year',
        'is_active'
    ];

    public function get_fillable(){
        return $this->fillable;
    }
}
