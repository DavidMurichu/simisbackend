<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchClassDailyRecording extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'teacheronduty',
        'recording_date',
        'general_comment',
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
