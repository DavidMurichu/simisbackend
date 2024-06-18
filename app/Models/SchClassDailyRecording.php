<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchClassDailyRecording extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacheronduty',
        'recording_date',
        'general_comment',
        'createdby',
        'lasteditedby',
        'ipaddress',
        'is_active',
        ];

        public function get_fillable(){
            return $this->fillable;
        }
}
