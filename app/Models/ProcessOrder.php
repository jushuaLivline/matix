<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcessOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'process_order_id',
        'process_code',
        'process_details',
        'packing',
        'updator',
        'updated_at',
    ];
    
    public function product()
    {
        return $this->belongsTo(ProductNumber::class, 'part_number', 'part_number');
    }

    public function process()
    {
        return $this->belongsTo(Process::class, 'process_code', 'process_code');
    }
    
    public function processUnitPrice()
    {
        return $this->hasOne(ProcessUnitPrice::class, 'process_code', 'process_code')->latest('id');
    }

    public function scopeSearch($query, $filters)
    {
        $query->with([
            'product', 'process', 
            'processUnitPrice' => function ($query) use ($filters) {
                $query->where('part_number', $filters->part_number);
            }
        ]);

        // Apply direct filters when values exist
        $filterMappings = [
            'part_number' => 'part_number',     // db_field => input_field name
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
            // 'document_issue_date' => ['document_issue_date_from', 'document_issue_date_to'], // db_field => input_field name
            // 'instruction_date' => ['instruction_date_from', 'instruction_date_to'],
            // 'arrival_day' => ['arrival_day_from', 'arrival_day_to'],
        ];

        foreach ($dateFilters as $column => [$fromKey, $toKey]) {
            $fromDate = !empty($request[$fromKey]) ? Carbon::parse($filters[$fromKey])->startOfDay() : null;
            $toDate = !empty($request[$toKey]) ? Carbon::parse($filters[$toKey])->endOfDay() : null;
        
            if ($fromDate && $toDate) {
                $query->whereBetween($column, [$fromDate, $toDate]);
            } elseif ($fromDate) {
                $query->where($column, '>=', $fromDate);
            } elseif ($toDate) {
                $query->where($column, '<=', $toDate);
            }
        }

        // Apply numeric range filter for order number
        $numericRanges = [
            // 'incoming_flight_number' => ['start' => 'incoming_flight_number_start', 'end' => 'incoming_flight_number_end'],
            // 'instruction_number' => ['start' => 'instruction_number_from', 'end' => 'instruction_number_to'],
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
