<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Audit extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_name',
        'activity_type',
        'activity_time',
        'ip_address',
        'user_agent',
        'additional_info',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function get_fillable(){
        return $this->fillable;
    }
}
