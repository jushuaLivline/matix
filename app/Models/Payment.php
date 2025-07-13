<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'year_and_month',
        'supplier_code',
        'tax_exempt_amount',
        'taxable_amount',
        'tax_amount',
        'offset_amount',
        'total_to_pay',
        'bill_amount',
        'transfer_amount',
        'transfer_fee',
        'created_at',
        'creator_code',
        'updated_at',
        'updator_code',
    ];

    public function supplier()
    {
        return $this->belongsTo(Customer::class, 'supplier_code', 'customer_code');
    }
}
