<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Traits\PaginateWithLimit;

class ShipmentRecord extends Model
{
    use HasFactory;
    use PaginateWithLimit;

    public $timestamps = false;

    protected $fillable = [
        'shipment_no',
        'serial_number',
        'slip_no',
        'voucher_class',
        'delivery_destination_code',
        'due_date',
        'delivery_no',
        'acceptance',
        'drop_ship_code',
        'product_no',
        'line_code',
        'department_code',
        'quantity',
        'unit_price',
        'remarks',
        'plant',
        'closing_date',
        'ai_slip_type',
        'classification',
        'uniform_no',
        'accomodation_no',
        'kanban_no',
        'instruction_no',
        'ai_delivery_no',
        'ai_jersey_no',
    ];

    public static function boot()
    {
        parent::boot();

        self::creating(function($model){
            $model->created_at = date('Y-m-d H:i:s');
            $model->creator = auth()?->user()?->id;
            $model->updated_at = date('Y-m-d H:i:s');
            $model->updator = auth()?->user()?->id;
        });

        self::updating(function($model){
            $model->updated_at = date('Y-m-d H:i:s');
            $model->updator = auth()?->user()?->id;
        });
    }

    public function department() {
        return $this->belongsTo(Department::class, 'department_code', 'code');
    }

    public function customer() {
        return $this->belongsTo(Customer::class, 'delivery_destination_code', 'customer_code');
    }

    public function productNumber()
    {
        return $this->belongsTo(ProductNumber::class, 'product_no', 'part_number');
    }

    public function scopeSearch($query, $filters)
    {
        $query->with(['department', 'customer'])
            ->orderByDesc('created_at');

        $filterMappings = [
            'delivery_destination_code' => 'delivery_destination_code',
            'slip_number' => 'slip_number',
            'voucher_class' => 'voucher_class',
            'product_no' => 'product_no',
            'department_code' => 'department_code',
        ];

        foreach ($filterMappings as $filterKey => $dbColumn) {
            if (!empty($filters[$filterKey])) {
                $query->where($dbColumn, $filters[$filterKey]);
            }
        }

        // Apply number range
        $numericFilter = [
            'delivery_no' => ['delivery_number_start', 'delivery_number_end']
        ];

        foreach ($numericFilter as $column => [$startKey, $endKey]) {
            if (!empty($filters[$startKey])) {
                $from = $filters[$startKey];
                $to = !empty($filters[$endKey]) ? $filters[$endKey] : $from;
        
                $query->whereBetween($column, [$from, $to]);
            }elseif(empty($filters[$startKey]) && !empty($filters[$endKey])){
                $from = $filters[$endKey] ;
                $to = $filters[$endKey];
        
                $query->whereBetween($column, [$from, $to]);
            }
        }

        //Note that date on database is formatted as dateTime (YYYY-MM-DD 00:00:00)
        $from = null;
        $to = null;

        // Check both start and end dates are provided
        if (!empty($filters['due_date_start']) && !empty($filters['due_date_end'])) {
            $from = \Carbon\Carbon::createFromFormat('Ymd', $filters['due_date_start'])
                ->startOfDay()->format('Y-m-d H:i:s');

            $to = \Carbon\Carbon::createFromFormat('Ymd', $filters['due_date_end'])
                ->endOfDay()->format('Y-m-d H:i:s');
        }
        // If only start date is provided, use it as both start and end
        elseif (!empty($filters['due_date_start'])) {
            $from = \Carbon\Carbon::createFromFormat('Ymd', $filters['due_date_start'])
                ->startOfDay()->format('Y-m-d H:i:s');

            $to = \Carbon\Carbon::createFromFormat('Ymd', $filters['due_date_start'])
            ->endOfDay()->format('Y-m-d H:i:s');
        }
        // If only end date is provided, use it as the start as well
        elseif (!empty($filters['due_date_end'])) {
            $from = \Carbon\Carbon::createFromFormat('Ymd', $filters['due_date_end'])
                ->startOfDay()->format('Y-m-d H:i:s');

            $to = \Carbon\Carbon::createFromFormat('Ymd', $filters['due_date_end'])
                ->endOfDay()->format('Y-m-d H:i:s');
        }

        if ($from && $to) {
            $query->whereBetween('due_date', [$from, $to]);
        }

        return $query;
    }

    public function getSummaryRecord($request)
    {
        $query = $this->query()
            ->select([
                'shipment_no',
                'serial_number',
                'slip_no',
                'voucher_class',
                'due_date',
                'delivery_no',
                'quantity',
                'unit_price',
                'shipment_records.created_at',

                // Product details
                'product_numbers.product_name as product_name',
                'product_numbers.part_number as product_number',

                // Department details
                'departments.name as department_name',
                'departments.code as department_code',
                'departments.section_name',
                'departments.group_name',

                // Line details
                'lines.line_name as line_name',
                'lines.line_code as line_code',

                // Customer details
                'customers.customer_code as customer_code',
                'customers.customer_name as customer_name',
            ])
            ->join('product_numbers', 'product_numbers.part_number', '=', 'shipment_records.product_no')
            ->join('lines', 'lines.line_code', '=', 'product_numbers.line_code')
            ->join('departments', 'departments.code', '=', 'product_numbers.department_code')
            ->join('customers', 'customers.customer_code', '=', 'shipment_records.delivery_destination_code');

        // Apply dynamic filters
        $filters = [
            'due_date' => 'due_date',
            'product_code' => 'product_numbers.part_number',
            'supplier_code' => 'product_numbers.supplier_code',
            'department_code' => 'departments.code',
            'line_code' => 'lines.line_code',
            'customer_code' => 'customers.customer_code',
        ];

        foreach ($filters as $requestKey => $dbColumn) {
            $query->when($request->filled($requestKey), function ($query) use ($request, $requestKey, $dbColumn) {
                $query->where($dbColumn, $request->input($requestKey));
            });
        }

        // Apply date range filters
        $dateFilters = [
            'due_date' => ['due_date_from', 'due_date_to'],
        ];

        foreach ($dateFilters as $column => [$fromKey, $toKey]) {
            if ($request->filled($fromKey)) {
                $fromDate = Carbon::parse($request->input($fromKey))->startOfDay();
                $toDate = $request->filled($toKey)
                    ? Carbon::parse($request->input($toKey))->endOfDay()
                    : Carbon::parse($request->input($fromKey))->endOfDay();

                $query->whereBetween($column, [$fromDate, $toDate]);
            }
        }

        $query->orderByDesc('shipment_records.created_at');

        return $query;
    }

    public function getConfirmedOrder($request)
    {
        $orderDate = Carbon::parse($request->order_date)->format('Y-m-d');
        $query = $this->select(
            'due_date',
            'product_no as part_number',
            'delivery_no as shipment_delivery_no',
            'kanban_no as shipment_kanban_number',
            'instruction_no as shipment_instruction_number'
          )
            ->where('due_date', $orderDate)
            ->where(function ($query) {
              $query->whereNotNull('kanban_no')
                ->orWhereNotNull('instruction_no');
            })
            ->groupBy('product_no')
            ->orderByDesc('created_at');

        return $query;
    }
}
