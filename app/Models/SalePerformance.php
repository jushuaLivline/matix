<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalePerformance extends Model
{
    use HasFactory;

    protected $table = 'sales_performances';

    protected $guarded = [
        ''
    ];

    protected $fillable = [
        "product_number",
        "part_number",
        "amount_category",
        "supplier_code",
        "customer_code",
        "department_code",
        "line_code",
        "quantity",
        "unit_price",
        "amount",
        "ai_slip_type",
        "creator",
    ];
}
