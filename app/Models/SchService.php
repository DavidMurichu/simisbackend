<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchService extends Model
{
    use HasFactory;
    protected $fillable = [
        'servicedurationid',
        'paymenttermid',
        'name',
        'cost',
        'is_transport_route',
        'invoiced_once',
        'description',
        'createdby',
        'lasteditedby',
        'ipaddress',
        'is_active',
        'branch_id',
    ];

    public function studentServices()
    {
        return $this->hasMany(SchStudentService::class, 'serviceid', 'id');
    }
    public function get_fillable(){
        return $this->fillable;
      }
  
}
