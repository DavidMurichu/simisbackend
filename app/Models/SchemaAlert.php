<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchemaAlert extends Model
{
    use HasFactory;

    protected $fillable=[
        'table_name',
        'data'
    ];
    public function get_fillable(){
        return $this->fillable;
    }

}
