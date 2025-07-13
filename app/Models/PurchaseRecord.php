<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Traits\PaginateWithLimit;

class PurchaseRecord extends Model
{
    use HasFactory;
    
    // Trait for paginating with a record limit.
    use PaginateWithLimit;
    protected $guarded = [];

    protected $casts = [
        'arrival_date'      => 'datetime',
        'created_at'        => 'date'
    ];

    public static function boot()
    {
        parent::boot();

        self::creating(function($model){
            $model->creator = auth()->user()->id;
        });

        self::updating(function($model){
            $model->updated_at = date('Y-m-d H:i:s');
            $model->updator = auth()->user()->id;
        });

    }

    public function get_latest_data_by_user_id($user_id, $purchase_category){
        
        $data = PurchaseRecord::where('purchase_category', $purchase_category)
            ->where('creator', $user_id)
            ->orderBy('created_at', 'desc')
            ->first();
        
        if (!$data) {
            return null;
        }
        $data->load(['department', 'line', 'product', 'supplier', 'item', 'machine', 'customer']);
        
        return (object)[
            'voucher_class' => $data->voucher_class,
            'arrival_date' => $data->arrival_date?->format('Ymd'),
            'supplier_code' => $data->supplier_code,
            'supplier_name' => $data->supplier->supplier_name_abbreviation ?? null,
            'machine_number' => $data->machine_number,
            'machine_branch_number' => $data?->machine?->branch_number,
            'machine_number_name' => $data?->machine?->machine_number_name ?? null,
            'department_code' => $data->department_code,
            'department_name' => $data->department->name ?? null,
            'purchase_category' => $data->purchase_category,
            'line_code' => $data->line_code,
            'line_name' => $data->line->line_name ?? null,
            'item_code' => $data->expense_item,
            'item_name' => $data->item->item_name ?? null,
            'part_number' => $data->part_number,
            'product_number_number' => $data->part_number ?? null,
            'product_number_name' => $data->product_name ?? null,
            'product_name' => $data->product_name,
            'standard' => $data->standard,
            'where_to_use_code' => $data->where_used_code,
            'where_to_use_name' => $data?->customer?->customer_name ?? null,
            'quantity' => $data->quantity,
            'unit_code' => $data->unit_code,
            'unit_price' => $data->unit_price,
            'amount_of_money' => $data->amount_of_money,
            'tax_classification' => $data->tax_classification,
            'slip_code' => $data->slip_no,
            'slip_type' => $data->slip_type,
            'project_code' => $data->project_number,
            'project_name' => $data->project->project_name ?? null,
            'remarks' => $data->remarks
        ];
    }

    public function get_data_by_id($id){
        $data = PurchaseRecord::findOrFail($id);
        $data->load(['department', 'line', 'product', 'supplier', 'item', 'machine', 'customer']);

        return (object)[
            'voucher_class' => $data->voucher_class,
            'arrival_date' => $data->arrival_date?->format('Ymd'),
            'supplier_code' => $data->supplier_code,
            'supplier_name' => $data->supplier->supplier_name_abbreviation ?? null,
            'machine_number' => $data->machine_number,
            'machine_branch_number' => $data->machine->branch_number ?? null,
            'machine_number_name' => $data->machine->machine_number_name ?? null,
            'department_code' => $data->department_code,
            'department_name' => $data->department->name ?? null,
            'purchase_category' => $data->purchase_category,
            'line_code' => $data->line_code,
            'line_name' => $data->line->line_name ?? null,
            'item_code' => $data->expense_item,
            'item_name' => $data->item->item_name ?? null,
            'part_number' => $data->part_number,
            'product_number_number' => $data->part_number ?? null,
            'product_number_name' => $data->product_name ?? null,
            'product_name' => $data->product_name,
            'standard' => $data->standard,
            'where_to_use_code' => $data->where_used_code,
            'where_to_use_name' => $data->customer->customer_name ?? null,
            'quantity' => $data->quantity,
            'unit_code' => $data->unit_code,
            'unit_price' => $data->unit_price,
            'amount_of_money' => $data->amount_of_money,
            'tax_classification' => $data->tax_classification,
            'slip_code' => $data->slip_no,
            'slip_type' => $data->slip_type,
            'project_code' => $data->project_number,
            'project_name' => $data->project->project_name ?? null,
            'remarks' => $data->remarks
        ];
    }
  
    public function getFormattedArrivalDateAttribute()
    {
        return $this->arrival_date ? $this->arrival_date->format('Ymd') : null;
    }
    // Used in PURCHASE-65-66
    public function scopeFilter($query, $request)
    {
        // Apply Exact Match Filters
        $filters = [
            'purchase_category' => 'purchase_category',
            'voucher_class' => 'voucher_class',
            'supplier_code' => 'supplier_code',
            'slip_no' => 'slip_no',
            'project_number' => 'project_code',
            'creator_code' => 'employee_code',
        ];
        
        foreach ($filters as $column => $key) {
            if (!empty($request->{$key})) {
                $query->where($column, $request->{$key});
            }
        }

        // Apply Numeric Ranges
        $numericRanges = [
            'machine_number' => ['start' => 'machine_number_start', 'end' => 'machine_number_end'],
            'department_code' => ['start' => 'department_code_start', 'end' => 'department_code_end'],
            'line_code' => ['start' => 'line_code_start', 'end' => 'line_code_end'],
            'expense_item' => ['start' => 'expense_item_start', 'end' => 'expense_item_end'],
            'part_number' => ['start' => 'part_number_start', 'end' => 'part_number_end'],
            'amount_of_money' => ['start' => 'amount1', 'end' => 'amount2']
        ];
        
        foreach ($numericRanges as $column => $keys) {
            if (!empty($request->{$keys['start']})) {
                $query->where($column, '>=', $request->{$keys['start']});
            }
            if (!empty($request->{$keys['end']})) {
                $query->where($column, '<=', $request->{$keys['end']});
            }
        }

        // Apply Date Ranges
        $dateRanges = [
            'created_at' => ['start' => 'input_date_start', 'end' => 'input_date_end'],
            'arrival_date' => ['start' => 'arrival_date_start', 'end' => 'arrival_date_end'],
        ];
        
        foreach ($dateRanges as $column => $keys) {
            $startDate = $request->{$keys['start']} ?? null;
            $endDate = $request->{$keys['end']} ?? null;

            if ($startDate && !$endDate) {
                $query->where($column, '>=', Carbon::parse($startDate)->startOfDay());
            } elseif (!$startDate && $endDate) {
                $query->where($column, '<=', Carbon::parse($endDate)->endOfDay());
            } elseif ($startDate && $endDate) {
                $query->whereBetween($column, [
                    Carbon::parse($startDate)->startOfDay(),
                    Carbon::parse($endDate)->endOfDay()
                ]);
            }
        }

        return $query;
    }
    
    public function scopeSearch($query, $request)
    {
        $filters = [
            'purchase_category' => $request->category ?? null,
            'voucher_class' => $request->voucher_class ?? null,
            'supplier_code' => $request->supplier_code ?? null,
            'machine_number' => [$request->machine_number_start ?? null, $request->machine_number_end ?? null],
            'department_code' => [$request->department_code_start ?? null, $request->department_code_end ?? null],
            'line_code' => [$request->line_code_start ?? null, $request->line_code_end ?? null],
            'expense_item' => [$request->expense_item_start ?? null, $request->expense_item_end ?? null],
            'part_number' => [$request->part_number_start ?? null, $request->part_number_end ?? null],
            'product_name' => $request->product_name ?? null,
            'standard' => $request->standard ?? null,
            'slip_no' => $request->slip_no ?? null,
            'project_number' => $request->project_code ?? null,
            'amount_of_money' => [$request->amount1 ?? null, $request->amount2 ?? null],
            'purchase_records.creator' => $request->employee_code ?? null,
            'purchase_records.created_at' => [$request->input_date_start ?? null, $request->input_date_end ?? null],
        ];

        // Convert arrival dates format from YYYYMMDD to YYYY-MM-DD 00:00:00
        $arrival_start_date = !empty($request->start_date) ? date('Y-m-d 00:00:00', strtotime($request->start_date)) : null;
        $arrival_end_date = !empty($request->end_date) ? date('Y-m-d 23:59:59', strtotime($request->end_date)) : null;
        
        $filters['arrival_date'] = [$arrival_start_date, $arrival_end_date];

        foreach ($filters as $column => $value) {
            if (is_array($value)) {
                if (!empty($value[0])) {
                    $query->where($column, '>=', $value[0]);
                }
                if (!empty($value[1])) {
                    $query->where($column, '<=', $value[1]);
                }
            } elseif (!empty($value)) {
                if (in_array($column, ['product_name', 'standard', 'slip_no'])) {
                    $query->where($column, 'LIKE', "%$value%");
                } else {
                    $query->where($column, $value);
                }
            }
        }

        $query->leftJoin('codes', function ($join) {
            $join->on('codes.code', '=', 'purchase_records.unit_code')
                ->where('codes.division', '単位');
        });

        $query->select('purchase_records.*', 'codes.name as unit_name')
            ->orderBy('created_at', 'desc');

        return $query;
    }

    public function supplier()
    {
        return $this->belongsTo(Customer::class, 'supplier_code', 'customer_code');
    }

    public function machine()
    {
        return $this->belongsTo(MachineNumber::class, 'machine_number', 'machine_number');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_code', 'code');
    }

    public function line()
    {
        return $this->belongsTo(Line::class, 'line_code', 'line_code');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_number', 'project_number');
    }

    public function expense_item()
    {
        return $this->belongsTo(Item::class, 'expense_item', 'expense_item');
    }

    public function expenseItem()
    {
        return $this->belongsTo(Item::class, 'expense_item', 'expense_item');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'expense_item', 'expense_item');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'where_used_code', 'customer_code');
    }

    public function product()
    {
        return $this->belongsTo(ProductNumber::class, 'part_number', 'part_number');
    }

    public function arrival()
    {
        return $this->hasMany(PurchaseArrival::class, 'purchase_record_no', 'purchase_record_no');
    }
    public function requisition()
    {
        // A PurchaseRecord connects to PurchaseRequisition through PurchaseArrival
        return $this->hasOneThrough(
            PurchaseRequisition::class,       // The target model
            PurchaseArrival::class,          // The intermediary model
            'purchase_record_no',            // Foreign key on PurchaseArrival
            'purchase_order_number',         // Foreign key on PurchaseRequisition
            'purchase_record_no',            // Local key on PurchaseRecord
            'purchase_order_no'              // Local key on PurchaseArrival
        )->whereColumn(
            'purchase_arrivals.purchase_order_details_no',
            'purchase_requisitions.purchase_order_details_number'
        );
    }
    public function purchaseRequisitions()
    {
        return $this->hasMany(PurchaseRequisition::class, 'purchase_order_number', 'purchase_record_no');
    }
    // Filter the purchase records based on the request parameters and return aggregated data.
    public function scopeFiltered($query, $request)
    {
        return $query->select(['supplier_code'])
                    ->selectRaw('SUM(amount_of_money) as sum_amount_of_money, COUNT(mst_purchase_records.id) as count_of_records')
                    ->with(['supplier'])
                    ->join('customers as c', 'purchase_records.supplier_code', '=', 'c.customer_code')
                    ->where('c.supplier_tag', 1)
                    ->where('c.delete_flag', 0)
                    ->groupBy('supplier_code')
                    ->havingRaw('COUNT(mst_purchase_records.id) > 0') 
                    ->orderByDesc('purchase_records.created_at')
                    ->filter($request);

    }

    // Create a new purchase record.
    public function createPurchaseRecord($request)
    {
        // Generate purchase record number
        $purchaseRecordNumber = $this->generatePurchaseRecordNumber();

        // Prepare data for creation
        $data = $this->preparePurchaseRecordData($request, $purchaseRecordNumber);

        // Create and return the record
        return $this->create($data);
    }

    // Generate the next purchase record number based on the current year and month.
    private function generatePurchaseRecordNumber()
    {
        $currentYearMonth = now()->format('ym');
        $latestPurchaseRecordNumber = $this->where('purchase_record_no', 'like', "{$currentYearMonth}%")
            ->max('purchase_record_no');

        if ($latestPurchaseRecordNumber) {
            $count = intval(substr($latestPurchaseRecordNumber, 4, 6)) + 1;
            return $currentYearMonth . str_pad($count, 6, '0', STR_PAD_LEFT);
        }

        return $currentYearMonth . '000001';
    }

    // Prepare the data array for creating a purchase record.
    private function preparePurchaseRecordData($request, $purchaseRecordNumber)
    {
        return [
            'purchase_record_no' => $purchaseRecordNumber,
            'voucher_class' => $request->voucher_class,
            'slip_type' => 1,
            'arrival_date' => $request->arrival_date,
            'supplier_code' => $request->supplier_code,
            'machine_number' => $request->machine_number,
            'department_code' => $request->department_code,
            'line_code' => $request->line_code,
            'expense_item' => $request->item_code,
            'subsidy_items' => $request->machine_number2,
            'part_number' => $request->product_number_number,
            'product_name' => $request->product_name,
            'standard' => $request->standard,
            'quantity' => $request->quantity,
            'unit_price' => $request->unit_price,
            'tax_classification' => $request->tax_classification,
            'project_number' => $request->project_code,
            'remarks' => $request->remarks,
            'purchase_category' => 2,
            'where_used_code' => $request->where_to_use_code,
            'amount_of_money' => $request->amount,
            'slip_no' => $request->slip_code,
            'unit_code' => $request->unit_code,
        ];
    }

    // Removes a purchase history record by its ID
    public function removePurchaseHistory($id) {
        if ($purchaseHistory = $this->find($id)) {
            $purchaseHistory->delete();
            return true;
        }
        return false; // Explicitly return false if the record is not found.
    }
}