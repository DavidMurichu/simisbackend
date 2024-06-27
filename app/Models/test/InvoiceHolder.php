<?php

namespace App\Models\test;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceHolder extends Model
{
    use HasFactory;
    protected $fillable = ['last_invoice_number'];
}
