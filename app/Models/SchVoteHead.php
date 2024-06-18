<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchVoteHead extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'paymentterm',
        'description',
        'createdby',
        'lasteditedby',
        'ipaddress',
        'is_active',
        'branch_name',
    ];
    public function get_fillable(){
        return $this->fillable;
    }


}
