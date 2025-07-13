<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ProcessUnitPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'part_number',
        'process_code',
        'effective_date',
        'processing_unit_price',
        'creator',
        'updator',
    ];

    public function product()
    {
        return $this->belongsTo(ProductNumber::class, 'part_number', 'part_number');
    }

    public function process()
    {
        return $this->belongsTo(Process::class, 'process_code', 'process_code');
    }

    public function scopeSearch($query, $filters)
    {
        $query->with([
            'product', 'process'
        ]);

        // Apply direct filters when values exist
        $filterMappings = [
            'process_code' => 'process_code',
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
            // 'incoming_flight_number' => ['start' => 'incoming_flight_number_start', 'end' => 'incoming_flight_number_end'],
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

    public function scopeFilterByPartAndProcess($query, $request)
    {
        $today = date('Y-m-d 00:00:00');
        return $query->select('process_unit_prices.processing_unit_price')
            ->leftJoin('processes', 'processes.process_code', '=', 'process_unit_prices.process_code')
            ->where(function ($query) use ($today) {
                $query->where('process_unit_prices.effective_date', '>=', $today)
                    ->orWhere('process_unit_prices.effective_date', '=', $today);
            })
            ->where('process_unit_prices.part_number', $request->part_number)
            ->where('processes.process_code', $request->process_code)
            ->where('processes.delete_flag', 0)
            ->orderBy('process_unit_prices.effective_date', 'desc');
    }

    public static function  GetUnitPriceByDivision($dateToday, $part_number)
    {
        return self::select('process_unit_prices.processing_unit_price', 'processes.inside_and_outside_division')
        ->leftJoin('processes', 'processes.process_code', '=', 'process_unit_prices.process_code')
        ->where('process_unit_prices.effective_date', '>=', $dateToday) // Combined OR condition
        ->where('process_unit_prices.part_number', $part_number)
        ->where('processes.delete_flag', 0)
        ->whereIn('processes.inside_and_outside_division', [1, 2]) // Fetch both inside & outside at once
        ->orderBy('process_unit_prices.effective_date', 'desc') // latest first
        ->get()
        ->groupBy('inside_and_outside_division');
    }
}
