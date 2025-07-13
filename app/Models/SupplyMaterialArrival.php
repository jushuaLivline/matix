<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Traits\PaginateWithLimit;

class SupplyMaterialArrival extends Model
{
    use HasFactory;

    // Trait for paginating with a record limit.
    use PaginateWithLimit;
    
    protected $guarded = [];

    protected $casts = [
        'arrival_day' => 'date',
    ];
    protected $fillable = [
        'supply_material_receipt_no',
        'serial_number',
        'delivery_no',
        'voucher_class',
        'arrival_day',
        'flight_no',
        'supplier_code',
        'product_number',
        'material_manufacturer_code',
        'material_no',
        'line_code',
        'department_code',
        'arrival_quantity',
        'processing_rate',
    ];

    public function product()
    {
        return $this->belongsTo(ProductNumber::class, 'product_number', 'part_number');
    }

    // get the relationship based on 'material_no' column and not 'product_number'
    public function productMaterial()
    {
        return $this->belongsTo(ProductNumber::class, 'material_no', 'part_number');
    }

    public function configuration()
    {
        return $this->belongsTo(Configuration::class, 'product_number', 'child_part_number');
    }

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->created_at = date('Y-m-d H:i:s');
            $model->creator = auth()->user()->id;
        });

        // self::updating(function($model){
        //     $model->updated_at = date('Y-m-d H:i:s');
        //     $model->updator = auth()->user()->id;
        // });

    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        $query = $query
            ->when(!empty($filters['materialManufacturerCode']), function ($query) use ($filters) {
                return $query->where('material_manufacturer_code', $filters['materialManufacturerCode']);
            })
            ->when(!empty($filters['arrivalDateStart']), function ($query) use ($filters) {
                return $query->where('arrival_day', '>=', Carbon::parse($filters['arrivalDateStart'])->format('Y-m-d'));
            })
            ->when(!empty($filters['arrivalDateEnd']), function ($query) use ($filters) {
                return $query->where('arrival_day', '<=', Carbon::parse($filters['arrivalDateEnd'])->format('Y-m-d'));
            })
            ->when(!empty($filters['flightNoFrom']), function ($query) use ($filters) {
                return $query->where('flight_no', '>=', (int)$filters['flightNoFrom']);
            })
            ->when(!empty($filters['flightNoTo']), function ($query) use ($filters) {
                return $query->where('flight_no', '<=', (int)$filters['flightNoTo']);
            })
            ->when(!empty($filters['materialNo']), function ($query) use ($filters) {
                return $query->where('material_no', $filters['materialNo']);
            })
            ->when(!empty($filters['customerCode']), function ($query) use ($filters) {
                return $query->where('supplier_code', $filters['customerCode']);
            })
            ->when(!empty($filters['deliveryNo']), function ($query) use ($filters) {
                return $query->where('delivery_no', 'LIKE', "%{$filters['deliveryNo']}%");
            })
            ->where('voucher_class', 1)
            ->orderBy('created_at', 'DESC');

            return $query;
    }

    public static function generateSupplyMaterialReceiptNo()
    {
        $latest = self::whereRaw('LENGTH(supply_material_receipt_no) = 10')->orderBy('id', 'DESC')->first();
        $prefix = date('ym'); // YYMM format

        return ($latest && substr($latest->supply_material_receipt_no, 0, 4) == $prefix)
            ? $prefix . sprintf("%06d", substr($latest->supply_material_receipt_no, 4) + 1)
            : $prefix . '000001';
    }
    
    public static function generateSerialNumber()
    {
        $latest = self::whereRaw('LENGTH(serial_number) = 6')->orderBy('id', 'DESC')->first();
        $prefix = date('md'); // MMDD format (e.g., "0214" for Feb 14)

        return ($latest && substr($latest->serial_number, 0, 4) == $prefix)
            ? $prefix . sprintf("%02d", substr($latest->serial_number, 4) + 1)
            : $prefix . '01'; // Start from '001' if no existing record
    }

    public function getReturnSummary($request)
    {
        $query = $this->query()
            ->select(
                'arrival_day',
                'flight_no',
                'delivery_no',

                // product
                'product_numbers.product_name as product_name',
                'product_numbers.part_number as product_number',
                'product_numbers.edited_part_number as edited_part_number',

                // department
                'departments.name as department_name',
                'departments.code as department_code',

                // lines
                'lines.line_name as line_name',
                'lines.line_code as line_code',

                // Maximum Arrival Quantity and Processing Rate
                DB::raw('MAX(arrival_quantity) as max_arrival_quantity'),
                DB::raw('MAX(processing_rate) as max_processing_rate'),

                // DB::raw('MAX(mst_product_prices.unit_price) as max_unit_price'),
                // DB::raw('MAX(mst_process_unit_prices.processing_unit_price) as max_processing_unit_rate'),

                // // product price and processing price
                // DB::raw('(MAX(arrival_quantity) * MAX(mst_product_prices.unit_price)) as product_price'),
                // DB::raw('(MAX(mst_process_unit_prices.processing_unit_price) * MAX(processing_rate)) as processing_price'),

                // // calculate total_amount
                // DB::raw('((MAX(arrival_quantity) * MAX(mst_product_prices.unit_price)) + (MAX(mst_process_unit_prices.processing_unit_price) * MAX(processing_rate))) as total_amount')
            )
            ->join('product_numbers', 'product_numbers.part_number', '=', 'supply_material_arrivals.product_number')
            ->join('lines', 'lines.line_code', '=', 'product_numbers.line_code')
            ->join('departments', 'departments.code', '=', 'product_numbers.department_code')
            ->join('product_prices', 'product_prices.part_number', '=', 'product_numbers.part_number')
            ->join('process_unit_prices', 'process_unit_prices.part_number', '=', 'product_numbers.part_number')
            ->where('voucher_class', 3);

        // Apply filters dynamically without separate variables
        $query->when($request->return_date_start, function ($query) use ($request) {
            if ($request->return_date_end) {
                return $query->whereBetween('arrival_day', [
                    Carbon::parse($request->return_date_start)->format('Y-m-d'),
                    Carbon::parse($request->return_date_end)->format('Y-m-d')
                ]);
            } 
            return $query->where('arrival_day', Carbon::parse($request->return_date_start)->format('Y-m-d'));
        });

        $query->when($request->flight_from, function ($query) use ($request) {
            if ($request->flight_to) {
                return $query->whereBetween('flight_no', [$request->flight_from, $request->flight_to]);
            }
            return $query->where('flight_no', $request->flight_from);
        });

        $query->when($request->filled('product_code'), function ($query) use ($request) {
            $query->where('product_number', $request->product_code);
        });

        $query->when($request->filled('delivery_no'), function ($query) use ($request) {
            $query->where('delivery_no', $request->delivery_no);
        });

        $query->when($request->filled('supplier_code'), function ($query) use ($request) {
            $query->where('supplier_code', $request->supplier_code);
        });

        $query->when($request->filled('department_code'), function ($query) use ($request) {
            $query->where('departments.code', $request->department_code);
        });

        $query->when($request->filled('line_code'), function ($query) use ($request) {
            $query->where('lines.line_code', $request->line_code);
        });

        return $query;
    }


}
