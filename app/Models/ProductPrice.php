<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'part_number',
        'effective_date',
        'sell_price',
        'unit_price',
        'updator',
        'updated_at',
    ];

    // public function product(){
    //     return $this->belongsTo(Product::class);
    // }

    public function product()
    {
        return $this->belongsTo(ProductNumber::class, 'part_number', 'part_number');
    }



    public function scopeSearch($query, $filters)
    {
        $query->with([
            'product', 
        ]);

        // Apply direct filters when values exist
        $filterMappings = [
            'part_number' => 'part_number',
        ];

        foreach ($filterMappings as $filterKey => $dbColumn) {
            if (!empty($filters[$filterKey])) {
                $query->where($dbColumn, $filters[$filterKey]);
            }
        }

        // Apply partial search
        $searchableFields  = [
            // 'order_no' => 'order_no', // db_field => input_field name
         ];
 
         foreach ($searchableFields  as $filterKey => $dbColumn) {
            if (!empty($filters[$filterKey])) {
                $query->where($dbColumn, 'like', '%' . $filters[$filterKey] . '%');
            }
         }

        // Apply date range filters
        $dateFilters = [
            'effective_date' => ['effective_date_from', 'effective_date_to'], // db_field => input_field name
        ];

        foreach ($dateFilters as $column => [$fromKey, $toKey]) {
            if (!empty($filters[$fromKey])) {
                $fromDate = Carbon::parse($filters[$fromKey])->startOfDay();
                $toDate = Carbon::parse($filters[$fromKey])->endOfDay();
            
                if ($fromDate && $toDate) {
                    $query->whereBetween($column, [$fromDate, $toDate]);
                } elseif ($fromDate) {
                    $query->where($column, '>=', $fromDate);
                } elseif ($toDate) {
                    $query->where($column, '<=', $toDate);
                }
            }
        }

        // Apply numeric range filter for order number
        $numericRanges = [
            'unit_price' => ['start' => 'unit_price_start', 'end' => 'unit_price_end'],
            'sell_price' => ['start' => 'sell_price_start', 'end' => 'sell_price_end'],
        ];
        
        foreach ($numericRanges as $column => $rangeKeys) {
            $from = $filters[$rangeKeys['start']] ?? null;
            $to = $filters[$rangeKeys['end']] ?? null;

            if ($from !== null && $to !== null) {
                $query->whereBetween($column, [$from, $to]);
            } elseif ($from !== null) {
                $query->where($column, '>=', $from);
            } elseif ($to !== null) {
                $query->where($column, '<=', $to);
            }
        }
        
        return $query;

    }
}
