<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'arrival_day',
        'supplier_code',
        'voucher_class',
        'data_number',
        'serial_number',
        'data_partition',
        'slip_no',
        'part_no',
        'product_name',
        'expense_item',
        'subsidy_item',
        'quantity',
        'unit_code',
        'unit_price',
        'payment',
        'transfer_amount',
        'bill_amount',
        'tax_classification',
        'edited_part_no',
        'created_at',
        'creator_code',
        'updated_at',
        'updator_code',
    ];

    public function expense_item()
    {
        return $this->belongsTo(Item::class, 'expense_item', 'expense_item');
    }
}
