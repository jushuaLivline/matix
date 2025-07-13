<?php

namespace App\Models;

use App\Traits\HasModelUtility;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Traits\PaginateWithLimit;
class ProductNumber extends Model
{
    use HasFactory, HasModelUtility;
    // Trait for paginating with a record limit.
    use PaginateWithLimit;

    public $timestamps = false;

    protected $fillable = [
        'part_number',
        'product_name',
        'name_abbreviation',
        'product_category',
        'customer_code',
        'supplier_code',
        'department_code',
        'line_code',
        'secondary_line_code',
        'standard',
        'material_manufacturer_code',
        'unit_code',
        'uniform_number',
        'part_number_editing_format',
        'edited_part_number',
        'instruction_class',
        'customer_part_number',
        'customer_part_number_edit_format',
        'customer_edited_product_number',
        'production_division',
        'delete_flag',
        'created_at',
        'creator',
        'updated_at',
        'updator',
    ];

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->created_at = date('Y-m-d H:i:s');
            $model->creator = auth()?->user()?->id;
        });

        self::updating(function ($model) {
            $model->updated_at = date('Y-m-d H:i:s');
            $model->updator = auth()?->user()?->id;
        });

    }

    public function productPrice()
    {
        return $this->belongsTo(ProductPrice::class, 'part_number', 'part_number');
    }

    public function latestProductPrice()
    {
        return $this->hasOne(ProductPrice::class, 'part_number', 'part_number')->latest('id');
    }

    public function processUnitPrice()
    {
        return $this->belongsTo(ProcessUnitPrice::class, 'part_number', 'part_number');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_code', 'code');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_code', 'customer_code');
    }

    public function supplier()
    {
        return $this->belongsTo(Customer::class, 'supplier_code', 'customer_code');
    }

    public function line()
    {
        return $this->belongsTo(Line::class, 'line_code', 'line_code');
    }

    public function shipments()
    {
        return $this->hasMany(ShipmentRecord::class, 'product_no', 'part_number');
    }

    public function process_order()
    {
        return $this->hasMany(ProcessOrder::class, 'part_number', 'part_number');
    }

    public function salePlans()
    {
        return $this->hasMany(SalePlan::class, 'part_number', 'part_number');
    }

    public function manufacturer()
    {
        return $this->belongsTo(ManufacturerInfo::class, 'material_manufacturer_code', 'material_manufacturer_code');
    }

    public function group()
    {
        return $this->belongsTo(SupplyMaterialGroup::class, 'part_number', 'part_number');
    }

    public function supplyMaterialOrders()
    {
        return $this->hasMany(SupplyMaterialOrder::class, 'material_number', 'part_number');
    }

    public function supplyMaterialArrivals()
    {
        return $this->hasMany(SupplyMaterialArrival::class, 'product_number', 'part_number');
    }
    //relation for material 30 supply arrival via manufacturer
    public function supplyMaterialArrivalManufacturers()
    {
        return $this->hasMany(SupplyMaterialArrival::class, 'material_no', 'part_number');
    }

    public function scopeSearch($query, $request)
    {
        if (($request->product_number ?? '') != '') {
            $query->where('part_number', $request->product_number);
        }
    }

    // Product Category Mapping
    protected $productCategory = [
        0 => '材料',
        1 => '製品',
        2 => '試作品',
        3 => '購入材',
        4 => '仕掛品',
    ];

    public function getProductCategoryAttribute()
    {
        return $this->productCategory[$this->attributes['product_category']] ?? '（該当なし）';
    }

    public function childrenConfigurations()
    {
        return $this->hasMany(Configuration::class, 'parent_part_number', 'part_number');
    }

    public function parentConfigurations()
    {
        return $this->hasMany(Configuration::class, 'child_part_number', 'part_number');
    }

    public function getHierarchy()
    {
        return Configuration::getHierarchy($this->part_number);
    }

    public function process()
    {
        return $this->belongsTo(Process::class, 'material_manufacturer_code', 'process_code');
    }
    
    /**
     * Get sale plans for the next month.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function salePlansNextMonth()
    {
        return $this->hasMany(SalePlan::class, 'part_number', 'part_number');
    }

    /**
     * Get sale plans for two months later.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function salePlansTwoMonthsLater()
    {
        return $this->hasMany(SalePlan::class, 'part_number', 'part_number');
    }

    /**
     * Get supply material orders for the next month.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function supplyMaterialOrdersNextMonth()
    {
        return $this->hasMany(SupplyMaterialOrder::class, 'material_number', 'part_number');
    }

    /**
     * Get supply material orders for two months later.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function supplyMaterialOrdersTwoMonthsLater()
    {
        return $this->hasMany(SupplyMaterialOrder::class, 'material_number', 'part_number');
    }


    public function scopeGetSuppliedList($query, $request)
    {
        // Extract filter values from the request
        $processCode = $request->process_code;
        $partClassification = $request->part_classification;
        $supplyMaterialGroup = $request->supplied_group;
        $editedPartNumber = $request->edited_part_nubmer;
        $yearMonth = Carbon::createFromFormat('Ym', $request->year_month ?? now()->format('Ym'));
        $startDate = Carbon::parse($yearMonth)->startOfMonth()->format('Y-m-d');
            $endDate = Carbon::parse($yearMonth)->endOfMonth()->format('Y-m-d');
        $dayColumns = array_map(fn($i) => "COALESCE(day_$i, 0)", range(1, 31));
        $sumExpression = implode(' + ', $dayColumns);

        return $this->with([
            'supplyMaterialOrders' => fn($q) => $q->whereMonth('instruction_date', $yearMonth->format('m'))
                ->whereYear('instruction_date', $yearMonth->format('Y'))
                ->where('order_classification', 4),
            'supplyMaterialArrivalManufacturers' => fn($q) => $q->whereMonth('arrival_day', $yearMonth->format('m'))
                ->whereYear('arrival_day', $yearMonth->format('Y'))
                ->where('voucher_class', 1)
                ->whereNotNull('material_manufacturer_code'),
            'process' => function ($query) use ($request) {
                $query->where('process_code', $request->process_code);
            },
            'group',
        ])        
        ->select([
            'supply_material_orders.order_classification',
            'product_numbers.edited_part_number',
            'product_numbers.product_name',
            'product_numbers.part_number',
            'product_numbers.supplier_code',
            'product_numbers.department_code',
            'product_numbers.line_code',
            'unofficial_notices.*',
            
            // 'process_code',
            DB::raw("($sumExpression) AS total_days"),
            DB::raw("
                CASE WHEN EXISTS (
                    SELECT 1 FROM mst_kanban_masters km 
                    WHERE km.part_number = mst_product_numbers.part_number
                ) THEN 'kanban' ELSE 'shiji' END AS kanban_status
            ")
        ])
        
        ->leftJoin('supply_material_orders', function ($join) use ($yearMonth) {
            $join->on('supply_material_orders.material_number', '=', 'product_numbers.part_number')
           ->whereMonth('instruction_date', Carbon::parse($yearMonth)->format('m'))
            ->whereYear('instruction_date', Carbon::parse($yearMonth)->format('Y'));
        })
        ->leftJoin('unofficial_notices', 'unofficial_notices.product_number', '=', 'product_numbers.part_number')
        ->leftJoin('supply_material_groups', 'supply_material_groups.part_number', '=', 'product_numbers.part_number')
        
        ->when(in_array($partClassification, [1, 2]), function ($q) use ($partClassification) {
            $kanban_status = ($partClassification == 1) ? 'kanban' : 'shiji';
            $q->havingRaw("kanban_status = ?", [$kanban_status]);
        })
        ->when($processCode, fn($q) => $q->where('supply_material_orders.material_manufacturer_code', $processCode))
        ->when($editedPartNumber, fn($q) => $q->where('product_numbers.edited_part_number', $editedPartNumber))
        ->when($supplyMaterialGroup, fn($q) => $q->whereHas('group', fn($q) => $q->where('supply_material_group', $supplyMaterialGroup)))
        ->whereBetween('supply_material_orders.instruction_date',  [$startDate, $endDate])
        ->whereNotNull('supply_material_orders.material_number')
        ->groupBy('product_numbers.part_number')
        ->orderByDesc('product_numbers.created_at');
    }

    public function scopeGetSuppliedListOld($query, $request)
    {
        // Extract filter values from the request
        $processCode = $request->process_code;
        $partClassification = $request->part_classification;
        $supplyMaterialGroup = $request->supplied_group;
        $yearMonth = Carbon::createFromFormat('Ym', $request->year_month ?? now()->format('Ym'));
        $nextMonth = $yearMonth->copy()->addMonth();
        $nextTwoMonths = $yearMonth->copy()->addMonths(2);
        $dayColumns = array_map(fn($i) => "COALESCE(day_$i, 0)", range(1, 31));
        $sumExpression = implode(' + ', $dayColumns);

        // Load relationships and apply conditions
        return $this->with([
            'supplyMaterialOrders' => fn($q) => $q->whereMonth('instruction_date', $yearMonth->format('m'))
                ->whereYear('instruction_date', $yearMonth->format('Y')),

            'supplyMaterialArrivalManufacturers' => fn($q) => $q->whereMonth('arrival_day', $yearMonth->format('m'))
                ->whereYear('arrival_day', $yearMonth->format('Y')),

            'salePlans' => fn($q) => $q->where('year_month', $yearMonth->format('Ym')),

            'supplyMaterialOrdersNextMonth' => fn($q) => $q->whereMonth('instruction_date', $nextMonth->format('m'))
                ->whereYear('instruction_date', $nextMonth->format('Y')),

            'supplyMaterialOrdersTwoMonthsLater' => fn($q) => $q->whereMonth('instruction_date', $nextTwoMonths->format('m'))
                ->whereYear('instruction_date', $nextTwoMonths->format('Y')),

            'salePlansNextMonth' => fn($q) => $q->where('year_month', $nextMonth->format('Ym')),
            'salePlansTwoMonthsLater' => fn($q) => $q->where('year_month', $nextTwoMonths->format('Ym')),
            'group',
        ])
            ->when($processCode, fn($q) => $q->where('supply_material_orders.material_manufacturer_code', $processCode))
            ->when($yearMonth, fn($q) => $q->whereMonth('supply_material_orders.instruction_date', $yearMonth->format('m'))
            ->whereYear('instruction_date', $yearMonth->format('Y')))
            ->whereIn('product_numbers.product_category', [0, 1, 2])
            ->when($supplyMaterialGroup, fn($q) => $q->whereHas('group', fn($q) => $q->where('supply_material_group', $supplyMaterialGroup)))
            // ->select([
            //     'product_numbers.*',
            //     DB::raw("CASE WHEN EXISTS (
            //     SELECT 1 FROM mst_kanban_masters 
            //     WHERE mst_kanban_masters.part_number = mst_product_numbers.part_number
            // ) THEN 'kanban' ELSE 'shiji' END AS kanban_status"),
            // ])

            ->select([
                'unofficial_notices.*',
                'day_1',
                'day_2',
                'day_3',
                'day_4',
                'day_5',
                'day_6',
                'day_7',
                'day_8',
                'day_9',
                'day_10',
                'day_11',
                'day_12',
                'day_13',
                'day_14',
                'day_15',
                'day_16',
                'day_17',
                'day_18',
                'day_19',
                'day_20',
                'day_21',
                'day_22',
                'day_23',
                'day_24',
                'day_25',
                'day_26',
                'day_27',
                'day_28',
                'day_29',
                'day_30',
                'day_31',
                DB::raw("($sumExpression) AS total_days"),
                DB::raw("IF(mst_kanban_masters.part_number IS NOT NULL, 'kanban', 'shiji') AS kanban_status")
            ])
            ->leftJoin('kanban_masters', 'kanban_masters.part_number', '=', 'product_numbers.part_number')
            ->leftJoin('supply_material_orders', 'supply_material_orders.material_number', '=','product_numbers.part_number' )
            ->leftJoin('unofficial_notices', function ($join) use ($yearMonth) {
                $join->on('unofficial_notices.product_number', '=', 'supply_material_orders.material_number')
                    ->where('unofficial_notices.year_and_month', '=', $yearMonth->format('Ym'));
            })
            ->when(in_array($partClassification, [1, 2]), function ($q) use ($partClassification) {
                $kanban_status = ($partClassification == 1) ? 'kanban' : 'shiji';
                $q->havingRaw("kanban_status = ?", [$kanban_status]);
            })
            ->groupBy('product_numbers.part_number');
    }
   
    public function getAggregatedData($model, $field, $partNumbers, $dateField,  $sumField, $date)
    {
        // Convert partNumbers to an array if it's a string
        $partNumbers = is_string($partNumbers) ? explode(',', $partNumbers) : (array) $partNumbers;

        // Build query with grouping and aggregation
        $query = $model::whereIn($field, $partNumbers)
            ->selectRaw("$field, SUM($sumField) as total")
            ->groupBy($field);

        // Apply date filtering logic
        if ($dateField === 'year_month') {
            $query->where($dateField, $date);
        } else {
            $query->whereMonth($dateField, Carbon::parse($date)->format('m'))
                ->whereYear($dateField, Carbon::parse($date)->format('Y'));
        }

        // Fetch and return aggregated results indexed by the field
        return $query->pluck('total', $field)->toArray();
    }

    public function getByPartNumber($partNumber)
    {
        return $this->where('part_number', $partNumber)->first();
    }

    public function getWithMaterialClassification($materials)
    {
        $childPartNumbers = $materials->pluck('child_part_number');

        return $this->whereIn('part_number', $childPartNumbers)->get()
            ->map(function ($product) use ($materials) {
                return (object) array_merge($product->toArray(), [
                    'material_classification' => $materials
                        ->firstWhere('child_part_number', $product->part_number)
                        ->material_classification ?? null,
                ]);
            });
    }

    public static function getFilteredProducts()
    {
        return self::selectRaw("mst_product_numbers.part_number as product_code, 
                                mst_product_numbers.product_name as product_name, 
                                mst_process_unit_prices.processing_unit_price as process_unit_price")
            ->where('product_category', 1)
            ->leftJoin('process_unit_prices', 'process_unit_prices.part_number', '=', 'product_numbers.part_number')
            ->get();
    }
}
