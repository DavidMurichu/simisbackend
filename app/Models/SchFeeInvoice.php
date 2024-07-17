<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchFeeInvoice extends Model
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

    public function classterm(){
      return $this->belongsTo(SchStudentClassTerm::class, 'studentclasstermsid', 'id');
    }
    public function get_fillable(){
      return $this->fillable;
    }


}
