<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchVisitorRegister extends Model
{
    use HasFactory;

    protected $fillable = [
        
            'visitortype',
            'name',
            'mobile',
            'gendername',
            'date_visited',
            'classid',
            'visiting_reason',
            'sms',
            'createdby',
            'lasteditedby',
            'ipaddress',
            'is_active',
            'created_at',
            'updated_at'
        ];
        public function get_fillable(){
            return $this->fillable;
        }

}
