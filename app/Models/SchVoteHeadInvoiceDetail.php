<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchVoteHeadInvoiceDetail extends Model
{
    use HasFactory;
    protected $fillable = [
        'invoiceid',
        'feestructurevoteheadsid',
        'amount',
        'createdby',
        'lasteditedby',
        'ipaddress',
        'is_active',
    ];
    public function get_fillable(){
        return $this->fillable;
    }
    
}
