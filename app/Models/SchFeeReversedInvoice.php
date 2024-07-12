<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchFeeReversedInvoice extends Model
{
    use HasFactory;
    protected $fillable = [
        'studentid',
        'invoicedate',
        'invoiceno',
        'amount',
        'balance',
        'classid',
        'termid',
        'studentclasstermsid',
        'is_reversed',
        'createdby',
        'lasteditedby',
        'ipaddress',
        'is_active',
    ];

    public function student(){
      return $this->belongsTo(SchStudent::class, 'studentid', 'id');
    }
    public function get_fillable(){
      return $this->fillable;
    }
}
