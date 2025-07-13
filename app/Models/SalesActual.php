<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesActual extends Model
{
    use HasFactory;

    protected $fillable = [
        "year_month",
        "product_number",
        "part_number",
        "amount_category",
        "supplier_code",
        "customer_code",
        "department_code",
        "line_code",
        "quantity",
        "unit_price",
        "amount_of_money",
        "ai_slip_type",
        "creator",
        "updator",
    ];

    public static function boot()
    {
        parent::boot();

        self::creating(function($model){
            $model->created_at = date('Y-m-d H:i:s');
            $model->creator = auth()?->user()?->id;
        });

        self::updating(function($model){
            $model->updated_at = date('Y-m-d H:i:s');
            $model->updator = auth()?->user()?->id;
        });
    }
}
