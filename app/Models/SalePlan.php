<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalePlan extends Model
{
    use HasFactory;

    protected $table = 'sales_plans';

    protected $guarded = [
        ''
    ];

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
        "amount",
        "creator",
        "updator",
    ];

    public function product()
    {
        $this->belongsTo(ProductNumber::class, 'part_number', 'part_number');
    }

    public function group()
    {
        $this->belongsTo(SupplyMaterialGroup::class, 'part_number', 'part_number');
    }

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

    public function scopeSearch($query, $request)
    {
        if (($request->year_month ?? '') != '') {
            $query->where('year_month', $request->year_month);
        }
    }
}
