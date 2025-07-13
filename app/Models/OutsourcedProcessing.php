<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Traits\PaginateWithLimit;

class OutsourcedProcessing extends Model
{
    use HasFactory;

    // Trait for paginating with a record limit.
    use PaginateWithLimit;
    protected $guarded = [];

    protected $casts = [
        'instruction_date' => 'date',
        'arrival_day' => 'date',
        'document_issue_date' => 'date',
    ];

    public function product()
    {
        return $this->belongsTo(ProductNumber::class, 'product_code', 'part_number');
    }

    public function kanbanMaster()
    {
        return $this->belongsTo(KanbanMaster::class, 'management_no', 'management_no');
    }

    public function supplier()
    {
        return $this->belongsTo(Customer::class, 'supplier_code', 'customer_code');
    }

    public static function boot()
    {
        parent::boot();

        self::creating(function($model){
            $model->created_at = date('Y-m-d H:i:s');
            $model->creator = auth()->user()->id ?? null;
        });
        self::updating(function($model){
            $model->updated_at = date('Y-m-d H:i:s');
            $model->updator = auth()->user()->id ?? null;
        });

    }

    public function scopeSearch($query, $filters)
    {
        $query->with(['product', 'kanbanMaster', 'supplier']);

        // Apply direct filters when values exist
        $filterMappings = [
            'supplier_code' => 'supplier_code',     // db_field => input_field name
            'product_code' => 'product_code',
            'order_classification' => 'order_classification',
        ];

        foreach ($filterMappings as $filterKey => $dbColumn) {
            if (!empty($filters[$filterKey])) {
                $query->where($dbColumn, $filters[$filterKey]);
            }
        }

        // Apply partial search
        $searchableFields  = [
            'order_no' => 'order_no', // db_field => input_field name
         ];
 
         foreach ($searchableFields  as $filterKey => $dbColumn) {
            if (!empty($filters[$filterKey])) {
                $query->where($dbColumn, 'like', '%' . $filters[$filterKey] . '%');
            }
         }

        // Apply date range filters
        $dateFilters = [
            'document_issue_date' => ['document_issue_date_from', 'document_issue_date_to'], // db_field => input_field name
            'instruction_date' => ['instruction_date_from', 'instruction_date_to'],
            'arrival_day' => ['arrival_day_from', 'arrival_day_to'],
        ];

        foreach ($dateFilters as $column => [$fromKey, $toKey]) {
            if (!empty($filters[$fromKey])) {
                $fromDate = Carbon::parse($filters[$fromKey])->format('Y-m-d');
                $toDate = !empty($filters[$toKey]) ? Carbon::parse($filters[$toKey])->format('Y-m-d') : null;

                $query->whereBetween($column, $toDate ? [$fromDate, $toDate] : [$fromDate, $fromDate]);
            }
        }

        // Apply numeric range filter for order number
        $numericRanges = [
            'incoming_flight_number' => ['start' => 'incoming_flight_number_start', 'end' => 'incoming_flight_number_end'],
            'instruction_number' => ['start' => 'instruction_number_from', 'end' => 'instruction_number_to'],
        ];
        
        foreach ($numericRanges as $column => $rangeKeys) {
            $from = isset($filters[$rangeKeys['start']]) ? (int)$filters[$rangeKeys['start']] : null;
            $to   = isset($filters[$rangeKeys['end']])   ? (int)$filters[$rangeKeys['end']]   : null;

            if ($from !== null && $to !== null) {
                $query->whereBetween($column, [$from, $to]);
            } elseif ($from !== null) {
                $query->where($column, '>=', $from);
            } elseif ($to !== null) {
                $query->where($column, '<=', $to);
            }
        }
        $query->orderByDesc('created_at');

        return $query;

    }

    public static function generateOrderNo()
    {
      $latest = self::whereRaw('LENGTH(order_no) = 10')->orderBy('id', 'DESC')->first();
      $prefix = date('ym'); // YYMM format
  
      return ($latest && substr($latest->order_no, 0, 4) == $prefix)
        ? $prefix . sprintf("%06d", substr($latest->order_no, 4) + 1)
        : $prefix . '000001';
    }

    public function orderSlipPDF($request)
    {
        $query = $this->search($request)
            ->search($request)
            ->when($request->order_classification != 2, function ($query) {
                $query->whereNull('document_issue_date'); 
            })
            ->when($request->order_classification == 2, function ($query) {
                $query->whereNotNull('document_issue_date'); // When order_classification == 2
            })
            ->orderBy('product_code')
            ->orderBy('supplier_code')
            ->orderBy('management_no');
        return $query;
    }

    public static function getStocksArrivals($request)
    {
        $arrivalStart = $request->arrival_day_start;
        $arrivalEnd = $request->arrival_day_end;
        $flightNumberStart = (int) $request->flight_no_from;
        $flightNumberEnd = (int) $request->flight_no_to;

        $query = self::with(['supplier', 'product'])
        ->when($request->input('supplier_code'), fn($query) => $query->where('supplier_code', $request->input('supplier_code')))
        ->when($request->input('product_code'), fn($query) => $query->where('product_code', $request->input('product_code')))
        ->when($request->input('order_number'), fn($query) => $query->where('order_no', 'like', '%' . $request->input('order_number') . '%'))
        ->when($arrivalStart, function ($query) use ($arrivalStart, $arrivalEnd) {
            $arrivalStartFormatted = Carbon::parse($arrivalStart)->format('Y-m-d');
            if ($arrivalEnd) {
                $arrivalEndFormatted = Carbon::parse($arrivalEnd)->format('Y-m-d');
                return $query->whereBetween('arrival_day', [$arrivalStartFormatted, $arrivalEndFormatted]);
            } else {
                return $query->where('arrival_day', $arrivalStartFormatted);
            }
        })
        ->when($flightNumberStart, function ($query) use ($flightNumberStart, $flightNumberEnd) {
            if ($flightNumberEnd) {
                return $query->whereBetween(DB::raw('CAST(incoming_flight_number AS SIGNED)'), [$flightNumberStart, $flightNumberEnd]);
            } else {
                return $query->where(DB::raw('CAST(incoming_flight_number AS SIGNED)'), $flightNumberStart);
            }
        })
        ->orderByDesc('created_at');

        return $query;
    }

}
