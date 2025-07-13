<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Traits\PaginateWithLimit;

class OutsourceMaterialFailure extends Model
{
    use HasFactory;

    // Trait for paginating with a record limit.
    use PaginateWithLimit;

    protected $guarded = [];

    protected $casts = [
        'return_date' => 'date',
        'created_at' => 'date'
    ];

    public function material()
    {
        return $this->belongsTo(ProductNumber::class, 'material_number', 'part_number');
    }

    public function product()
    {
        return $this->belongsTo(ProductNumber::class, 'product_number', 'part_number');
    }

    public function supplier()
    {
        return $this->belongsTo(Customer::class, 'supplier_code', 'customer_code');
    }

    public function process()
    {
        return $this->belongsTo(Process::class, 'process_code', 'process_code');
    }

    public function processUnitPrice()
    {
        return $this->belongsTo(ProcessUnitPrice::class, 'process_code', 'process_code');
    }

    public function manufacturerInfo()
    {
        return $this->belongsTo(ManufacturerInfo::class, 'material_manufacturer_code', 'material_manufacturer_code');
    }

    public function reason()
    {
        return $this->belongsTo(Code::class, 'reason_code', 'code');
    }

    public function scopeSearch($query, $filters)
    {
        $query->with([
            'material', 
            'material.processUnitPrice', 
            'process', 
            'supplier', 
            'reason', 
            'processUnitPrice', 
            'manufacturerInfo', 
            'product',
            ])
            ->orderByDesc('created_at');

        // Apply direct filters when values exist
        $filterMappings = [
            'product_number' => 'product_number',     // db_field => input_field name
            'supplier_code' => 'supplier_code',
            'material_number' => 'material_number',
            'process_code' => 'process_code',
            'outsource_material_failures.id' => 'outsource_material_failures.id',
            'return_date' => 'return_date'
        ];

        foreach ($filterMappings as $filterKey => $dbColumn) {
            if (!empty($filters[$filterKey])) {
                $query->where($dbColumn, $filters[$filterKey]);
            }
        }

        // Apply partial search
        $searchableFields  = [
            'slip_no' => 'slip_no', // db_field => input_field name
         ];
 
         foreach ($searchableFields  as $filterKey => $dbColumn) {
            if (!empty($filters[$filterKey])) {
                $query->where($dbColumn, 'like', '%' . $filters[$filterKey] . '%');
            }
         }

        // Apply date range filters
        $dateFilters = [
            'return_date' => ['return_date_from', 'return_date_to'], // db_field => input_field name
            'created_at' => ['created_at_from', 'created_at_to'],
        ];

        foreach ($dateFilters as $column => [$fromKey, $toKey]) {
            if (!empty($filters[$fromKey])) {
                if ($column === 'created_at') {
                    // Add timestamps for created_at
                    $fromDate = Carbon::parse($filters[$fromKey])->startOfDay(); // 00:00:00
                    $toDate = !empty($filters[$toKey]) 
                        ? Carbon::parse($filters[$toKey])->endOfDay() // 23:59:59
                        : $fromDate->copy()->endOfDay(); // Same day but 23:59:59
                } else {
                    // Keep date-only format for other fields
                    $fromDate = Carbon::parse($filters[$fromKey])->format('Y-m-d');
                    $toDate = !empty($filters[$toKey]) 
                        ? Carbon::parse($filters[$toKey])->format('Y-m-d') 
                        : $fromDate;
                }

                $query->whereBetween($column, $toDate ? [$fromDate, $toDate] : [$fromDate, $fromDate]);
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

    public static function generateRegistrationNumber()
    {
        $containDate = substr(date("Y"), -2) . date("m");
        $latestRegisterNumber = self::where('registration_no', 'LIKE', '%' . $containDate . '%')->max('registration_no');

        return $latestRegisterNumber ? $latestRegisterNumber + 1 : $containDate . '000001';
    }

    public static function storeMaterialFailures($sessionMaterialDefect)
    {
        if (empty($sessionMaterialDefect)) {
            return;
        }

        $containDate = substr(date("Y"), -2) . date("m");
        $latestRegisterNumber = self::where('registration_no', 'LIKE', '%' . $containDate . '%')->max('registration_no');

        if ($latestRegisterNumber) {
            $newRegisterNumber = $latestRegisterNumber + 1;
        } else {
            $newRegisterNumber = substr(date("Y"), -2) . date("m") . '000001';
        }

        foreach ($sessionMaterialDefect as $index => $item) {
            self::create([
                'registration_no' => $newRegisterNumber,
                'serial_number' => $index + 1,
                'process_code' => $item['process_code'],
                'slip_no' => $item['slip_no'],
                'return_date' => Carbon::parse($item['return_date'])->format('Y-m-d'),
                'material_number' => $item['material_code'],
                'supplier_code' => $item['supplier_code'],
                'material_manufacturer_code' => $item['material_manufacturer_code'],
                'reason_code' => $item['reason_code'],
                'quantity' => $item['quantity'],
                'processing_rate' => $item['processing_rate'],
            ]);
        }
    }

}
