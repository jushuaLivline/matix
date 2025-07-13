<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Traits\PaginateWithLimit;

class UnofficialNotice extends Model
{
    use HasFactory;

    use PaginateWithLimit;

    const monthIndexColumn = ['current_month', 'next_month', 'two_months_later'];

    protected $fillable = [
        "product_number",
        "acceptance",
        "delivery_destination_code",
        "year_and_month",
        "day_1",
        "day_2",
        "day_3",
        "day_4",
        "day_5",
        "day_6",
        "day_7",
        "day_8",
        "day_9",
        "day_10",
        "day_11",
        "day_12",
        "day_13",
        "day_14",
        "day_15",
        "day_16",
        "day_17",
        "day_18",
        "day_19",
        "day_20",
        "day_21",
        "day_22",
        "day_23",
        "day_24",
        "day_25",
        "day_26",
        "day_27",
        "day_28",
        "day_29",
        "day_30",
        "day_31",
        "current_month",
        "next_month",
        "two_months_later",
        "instruction_class",
        "direct_shipping_destination",
        "uniform_number",
        "cycle",
        "number_of_accomodated",
        "aisin_factory",
        "responsible_person",
        "minimum_delivery_unit",
        "number_per_day",
        "number_of_cards",
        "kanban_number",
        "standard_stock",
        "sp_tp_classification",
        "manufactorer_factory",
        "manufactorer_factory_destination",
        "data_partition",
        "current_month_order_rate_factored_number",
        "color_mode",
        "customer_part_number",
        "customer",
        "design_change_code",
        "input_category",
        "creator",
    ];

    public function product()
    {
        return $this->belongsTo(ProductNumber::class, 'product_number', 'part_number');
    }

    public function destination()
    {
        return $this->belongsTo(Customer::class, 'delivery_destination_code', 'customer_code');
    }

    public function productNumber()
    {
        return $this->belongsTo(ProductNumber::class, 'product_number', 'part_number');
    }
  
    public function scopeSearch($query, $filters)
    {   
        $query->with([
            'product', 
            'productNumber',
            'destination'])
            ->orderByDesc('unofficial_notices.created_at');

        // Apply direct filters when values exist
        $filterMappings = [
            'id' => 'id',
            'delivery_destination_code' => 'delivery_destination_code',     // db_field => input_field name
            'instruction_class' => 'instruction_class',
            'year_and_month' => 'year_and_month',
            'product_number' => 'product_number',
        ];

        foreach ($filterMappings as $filterKey => $dbColumn) {
            if (!empty($filters[$filterKey])) {
                $query->where($dbColumn, $filters[$filterKey]);
            }
        }

        // Apply partial search
        $searchableFields  = [
            'acceptance' => 'acceptance', // db_field => input_field name
         ];
 
         foreach ($searchableFields  as $filterKey => $dbColumn) {
            if (!empty($filters[$filterKey])) {
                $query->where($dbColumn, 'like', '%' . $filters[$filterKey] . '%');
            }
         }

        // Apply date range filters
        $dateFilters = [
            // 'document_issue_date' => ['document_issue_date_from', 'document_issue_date_to'], // db_field => input_field name
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
            'product_number' => ['start' => 'product_code_from', 'end' => 'product_code_to'],
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

        $query->whereHas('product', function ($q) use ($filters) {
            $q->when(!empty($filters->customer_code), function ($q) use ($filters) {
                $q->where('customer_code', $filters->customer_code);
            })->when(!empty($filters->department_code), function ($q) use ($filters) {
                $q->where('department_code', $filters->department_code);
            })->when(!empty($filters->line_code), function ($q) use ($filters) {
                $q->where('line_code', $filters->line_code);
            });
        });


        if(!empty($filters->get_sum_current_month)){
            $dayColumns = array_map(fn($i) => "COALESCE(day_$i, 0)", range(1, 31));
            $sumExpression = implode(' + ', $dayColumns);

            $query->select([
                'unofficial_notices.*', 
                DB::raw("($sumExpression) as sum_current_month")
            ]);
        }

        return $query;
    }

    public function getSummaryRecord($request)
    {
        $query = $this->query()
            ->select([
                'year_and_month',
                'current_month',
                'next_month',
                'two_months_later',
                'unofficial_notices.created_at',

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
            ->join('product_numbers', 'product_numbers.part_number', '=', 'unofficial_notices.product_number')
            ->join('lines', 'lines.line_code', '=', 'product_numbers.line_code')
            ->join('departments', 'departments.code', '=', 'product_numbers.department_code')
            ->join('customers', 'customers.customer_code', '=', 'product_numbers.customer_code');

        // Apply dynamic filters
        $filters = [
            'year_and_month' => 'year_and_month',
            'product_code' => 'product_numbers.part_number',
            'supplier_code' => 'product_numbers.supplier_code',
            'department_code' => 'departments.code',
            'line_code' => 'lines.line_code',
            'customer_code' => 'customers.customer_code',
        ];

        foreach ($filters as $requestKey => $dbColumn) {
            $query->when($request->filled($requestKey), function ($query) use ($request, $requestKey, $dbColumn) {
                $query->where($dbColumn, $request->$requestKey);
            });
        }

        $query->orderByDesc('unofficial_notices.created_at');
        return $query;
    }


    public function getOrderForcast($request)
    {   
        $yearMonth = $request->year_month ? Carbon::parse($request->year_month . "01") : null;
        $query = $this->select(
            'unofficial_notices.id', 
            'unofficial_notices.current_month', 
            'unofficial_notices.next_month', 
            'unofficial_notices.two_months_later', 
            'unofficial_notices.created_at', 
            'product_numbers.part_number', 
            'product_numbers.edited_part_number', 
            'product_numbers.product_name', 'unofficial_notices.current_month_order_rate_factored_number')
        ->addSelect(DB::raw("MAX(mst_unofficial_notices.current_month_order_rate_factored_number) as max_factored"))
        ->addSelect(DB::raw("MAX(mst_unofficial_notices.current_month) as current_month_max"))
        ->addSelect(DB::raw("MAX(mst_unofficial_notices.next_month) as next_month_max"))
        ->addSelect(DB::raw("MAX(mst_unofficial_notices.two_months_later) as two_months_later_max"))
        ->join('product_numbers', 'product_numbers.part_number', 'unofficial_notices.product_number')
        ->when($yearMonth, function ($q) use ($yearMonth) {
            $q->where('unofficial_notices.year_and_month', $yearMonth->format('Ym'));
        })
        ->when($request->customer_code, function ($q) use ($request) {
            $q->where("unofficial_notices.delivery_destination_code", $request->customer_code);
        })
        ->when($request->acceptance, function ($q) use ($request) {
            $q->where("unofficial_notices.acceptance", $request->acceptance);
        })
        ->when($request->part_number_first, function ($q) use ($request) {
            $q->where("unofficial_notices.product_number", $request->part_number_first);
        })
        ->groupBy('unofficial_notices.product_number')
        ->groupBy('unofficial_notices.year_and_month')
        ->where("unofficial_notices.instruction_class", 1)
        ->orderByDesc('created_at');

        return $query;
    }
}
