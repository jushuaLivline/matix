<?php

namespace App\Models;

use App\Constants\Constant;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Traits\PaginateWithLimit;

class PurchaseRequisition extends Model
{
    use HasFactory;

    // Trait for paginating with a record limit.
    use PaginateWithLimit;

    protected $fillable = [
        'requisition_number',
        'requested_date',
        'supplier_code',
        'department_code',
        'line_code',
        'machine_number',
        'part_number',
        'product_name',
        'standard',
        'reason',
        'quantity',
        'unit_code',
        'unit_price',
        'amount_of_money',
        'expense_items',
        'subsidy_items',
        'deadline',
        'tax_classification',
        'project_number',
        'where_used_code',
        'quotation_existence_flag',
        'approval_method_category',
        'approval_route_number',
        'data_type',
        'state_classification',
        'next_approver',
        'remarks',
        'reason_for_denial',
        'purchase_order_number',
        'purchase_order_details_number',
        'order_date',
        'creator',
        'updator',
    ];

    protected $casts = [
        'order_date'        => 'date',
        'deadline'          => 'date',
        'requested_date'    => 'date',
        'created_at'        => 'date'
    ];

    function assignNextApprover(Employee $employee){
        $arrays = $this
                    ->approvals()
                    ->orderBy("order_of_approval", "asc")
                    ->pluck("approver_employee_code", "order_of_approval")
                    ->toArray();

        $currentUserOrder = array_search($employee->employee_code, $arrays);
        $nextApprover = $arrays[$currentUserOrder + 1] ?? null;
    
        if($nextApprover){
            $this->update(['next_approver' =>  $nextApprover]);
            $this->update(['state_classification' => 1]);
        }else{

            if($employee->employee_code == end($arrays) ){
                // Mark 2:approved | STATE_CLASSIFICATION reference
                $this->update(['state_classification' => 2]);
            }else{
                // Mark 9:denial | STATE_CLASSIFICATION reference
                $this->update(['state_classification' => 9]);
            }
        }
    }

    public function get_data_by_id($id){
        return PurchaseRequisition::findOrFail($id);
    }

    public function nextApprover()
    {
        return $this->belongsTo(Employee::class, 'next_approver', 'employee_code');
    }
    
    public function supplier()
    {
        return $this->belongsTo(Customer::class, 'supplier_code', 'customer_code');
    }

    public function machine()
    {
        return $this->belongsTo(MachineNumber::class, 'machine_number', 'machine_number');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'creator', 'employee_code');
    }

    public function unit()
    {
        return $this->belongsTo(Code::class, 'unit_code', 'code')
                                ->where('division', '=', '単位');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_code', 'code');
    }

    public function line()
    {
        return $this->belongsTo(Line::class, 'line_code', 'line_code');
    }

    public function expense()
    {
        return $this->belongsTo(Item::class, 'expense_items', 'expense_item');
    }

    public function product()
    {
        return $this->belongsTo(ProductNumber::class, 'part_number', 'part_number');
    }

    public function arrival()
    {
        return $this->belongsTo(PurchaseArrival::class, 'purchase_order_number', 'purchase_order_no');
    }

    public function record()
    {
        return $this->belongsTo(PurchaseRecord::class, 'purchase_order_number', 'purchase_record_no');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_number', 'project_number');
    }
  
    public function approvalRoute()
    {
        return $this->hasMany(PurchaseApprovalRoute::class, 'approval_route_no', 'approval_route_number');
    }

    public function approvals()
    {
        return $this->hasMany(PurchaseApproval::class, 'purchase_record_no', 'requisition_number');
    }

    public function lastApprover()
    {
        return  $this->hasMany(PurchaseApproval::class, 'purchase_record_no', 'requisition_number')->orderBy("order_of_approval", "desc")->first();
    }

    public function unfinishedApprovals()
    {
        return $this->hasMany(PurchaseApproval::class, 'purchase_record_no', 'requisition_number')
                    ->whereNull('approval_date')->whereNull('denial_date');
    }

    public function approvedApprovals()
    {
        return $this->hasMany(PurchaseApproval::class, 'purchase_record_no', 'requisition_number')
                    ->whereNotNull('approval_date');
    }

    public function scopeWhereDateRangeOrEqual($query, $columnName, $from, $to)
    {
        if ($from && $to) {
            return $query->whereBetween($columnName, [
                Carbon::parse($from)->format('Y-m-d'),
                Carbon::parse($to)->format('Y-m-d')
            ]);
        } elseif ($from) {
            return $query->whereDate($columnName, '>=', Carbon::parse($from)->format('Y-m-d'));
        } elseif ($to) {
            return $query->whereDate($columnName, '<=', Carbon::parse($to)->format('Y-m-d'));
        }
        return $query;
    }

    public function scopeWhereColumnBetweenOrEqual($query, $columnName, $from, $to)
    {
        if ($from && $to) {
            return $query->whereBetween($columnName, [ $from,$to ]);
        } elseif ($from) {
            return $query->where($columnName, '>=', $from);
        } elseif ($to) {
            return $query->where($columnName, '<=', $to);
        }
        return $query;
    }
  
    public function arrivalsSummary()
    {
        return $this->hasMany(PurchaseArrival::class, 'purchase_order_no', 'purchase_order_number')
            ->selectRaw('purchase_order_no, purchase_order_details_no, arrival_day, SUM(arrival_quantity) AS total_arrival_quantity')
            ->groupBy('purchase_order_no', 'purchase_order_details_no', 'arrival_day');

    }

    public function recordsSummary()
    {
        return $this->hasMany(PurchaseRecord::class, 'purchase_record_no', 'purchase_order_number')
            ->whereColumn('part_number', 'part_number')
            ->selectRaw('purchase_record_no, part_number, arrival_date as record_arrival_date, SUM(quantity) AS total_record_quantity')
            ->groupBy('purchase_record_no', 'part_number', 'arrival_date');
    }

    /**
     * Scope a query to filter purchase requisitions based on various criteria.
     *
     * The filter criteria include:
     * - Date Ranges: Filters based on start and end dates.
     * - Numeric Ranges: Filters based on start and end values for supplier_code, department_code ...
     * - Direct Filters: Filters based on exact matches for creator, supplier_code ...
     * - Approval Filtering: Filters based on the purpose of the request, checking for approvals by the current user.
     * - Status Filters: Filters based on the status of the purchase requisition (non-stock, in-stock, arrive-stock).
     * - Acceptance Filters: Filters based on the acceptance status of the purchase requisition (incomplete, complete).
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Http\Request $request
     */
    public function scopeFilter($query, $request)
    {   
        // Apply Date Ranges
        $dateRanges = [
            'requested_date' => ['start' => 'request_date_start', 'end' => 'request_date_end'],
            'order_date' => ['start' => 'order_date_start', 'end' => 'order_date_end'],
            'deadline' => ['start' => 'deadline_date_start', 'end' => 'deadline_date_end'],
        ];

        foreach ($dateRanges as $column => $keys) {
            $startDate = $request->{$keys['start']} ?? null;
            $endDate = $request->{$keys['end']} ?? null;

            if ($startDate && !$endDate) {
                // If only start date is provided, filter from the beginning of that day
                $query->where($column, '>=', Carbon::parse($startDate)->startOfDay());
            } elseif (!$startDate && $endDate) {
                // If only end date is provided, filter up to the end of that day
                $query->where($column, '<=', Carbon::parse($endDate)->endOfDay());
            } elseif ($startDate && $endDate) {
                // If both start and end dates are provided, filter between full-day ranges
                $query->whereBetween($column, [
                    Carbon::parse($startDate)->startOfDay(),
                    Carbon::parse($endDate)->endOfDay()
                ]);
            }
        }

        // Fix arrival_day filtering (Use relation `arrivalsSummary`)
        if (!empty($request->arrival_date_start) || !empty($request->arrival_date_end)) {
            $query->whereHas('arrivalsSummary', function ($q) use ($request) {
                if (!empty($request->arrival_date_start)) {
                    $q->where('arrival_day', '>=', Carbon::parse($request->arrival_date_start)->startOfDay());
                }
                if (!empty($request->arrival_date_end)) {
                    $q->where('arrival_day', '<=', Carbon::parse($request->arrival_date_end)->endOfDay());
                }
            });
        }

        // Apply Numeric Ranges
        $numericRanges = [
            'supplier_code' => ['start' => 'supplier_code_start', 'end' => 'supplier_code_end'],
            'department_code' => ['start' => 'department_code_start', 'end' => 'department_code_end'],
            'line_code' => ['start' => 'line_code_start', 'end' => 'line_code_end'],
            'machine_number' => ['start' => 'machine_code_start', 'end' => 'machine_code_end'],
            'expense_items' => ['start' => 'expense_item_start', 'end' => 'expense_item_end'],
        ];
        foreach ($numericRanges as $column => $keys) {
            if (!empty($request->{$keys['start']})) {
                $query->where($column, '>=', $request->{$keys['start']});
            }
            if (!empty($request->{$keys['end']})) {
                $query->where($column, '<=', $request->{$keys['end']});
            }
        }

        // Apply Direct Filters
        $filters = [
            'creator' => 'employee_code',
            'supplier_code' => 'supplier_code',
            'purchase_requisitions.part_number' => 'part_number',
            'product_name' => 'product_name',
            'product_name' => 'requistion_product_name',
            'standard' => 'standard',
            'requisition_number' => 'requisition_number',
            'purchase_order_number' => 'purchase_order_number',
        ];
        foreach ($filters as $column => $key) {
            if (!empty($request->{$key})) {
                $query->where($column, 'LIKE', '%' . $request->{$key} . '%');
            }
        }

        // Apply Filters for Multiple Values (`whereIn`)
        $multipleValues = ['approval_method_category', 'state_classification'];
        foreach ($multipleValues as $column) {
            if (!empty($request->$column)) {
                $query->whereIn($column, (array) $request->$column);
            }
        }
        if (!empty($request->purpose)) {
            switch ($request->purpose) {
                case 3:
                    $query->whereHas('approvals', function ($q) use ($request) {
                        $q->where('approver_employee_code', $request->user()->employee_code)
                            ->whereNotNull('approval_date');
                    });
                    break;
                case 2:
                    $query->whereRelation('approvals', 'approver_employee_code', $request->user()->employee_code);
                    break;
                default:
                    $query->where('next_approver', $request->user()->employee_code);
                    break;
            }
        }

        // Status Filtering (Use `arrivalsSummary`)
        if (!empty($request->status)) {
            if ($request->status == 'non-stock') {
                $query->doesntHave('arrivalsSummary');
            } elseif ($request->status == 'in-stock') {
                $query->whereHas('arrivalsSummary', function ($query) {
                    $query->whereColumn('arrival_quantity', '<=', 'purchase_requisitions.quantity');
                });
            } elseif ($request->status == 'arrive-stock') {
                $query->whereHas('arrivalsSummary', function ($query) {
                    $query->whereColumn('arrival_quantity', '>=', 'purchase_requisitions.quantity');
                });
            }
        }

        // Acceptance Filtering (Use `recordsSummary`)
        if (!empty($request->acceptance)) {
            if ($request->acceptance == 'incomplete') {
                $query->whereHas('recordsSummary', function ($query) {
                    $query->whereColumn('purchase_records.quantity', '<=', 'purchase_requisitions.quantity');
                });
            } elseif ($request->acceptance == 'complete') {
                $query->whereHas('recordsSummary', function ($query) {
                    $query->whereColumn('purchase_records.quantity', '>=', 'purchase_requisitions.quantity');
                });
            }
        }

        // Slip Number Filtering (Use `arrivalsSummary`)
        if (!empty($request->slip_no)) {
            $query->whereHas('arrivalsSummary', function ($query) use ($request) {
                $query->where('slip_no', 'LIKE', '%' . $request->slip_no . '%');
            });
        }

        return $query;
    }

    public function updatePurchaseRequisition($id, $request)
    {
        // Find the existing purchase requisition record by ID
        $purchaseRequisition = $this->findOrFail($id);

        // Prepare the data array to be updated
        $updateData = [
            'supplier_code' => $request->supplier_code,
            'department_code' => $request->department_code,
            'line_code' => $request->line_code,
            'part_number' => $request->part_no,
            'product_name' => $request->product_name,
            'standard' => $request->specification,
            'reason' => $request->reason,
            'quantity' => $request->quantity,
            'unit_code' => $request->unit_code,
            'unit_price' => $request->price,
            'amount_of_money' => $request->amount_of_money,
            'expense_items' => $request->expense_item_code,
            'deadline' => self::formatDate($request->deadline),
            'approval_method_category' => $request->method,
            'remarks' => $request->remarks,
            'quotation_existence_flag' => $request->quotation,
            'state_classification' => ($request->state_classification == 9) ? 1 : $request->state_classification,
            'updator' => $request->user()->employee_code,
        ];

        // Filter out any null or empty values from the data array
        $updateData = array_filter($updateData, function($value) {
            return !is_null($value) && $value !== '';
        });

        // Update the record with the filtered data
        $purchaseRequisition->update($updateData);

        return $purchaseRequisition;
    }

    //Format the deadline date
    private static function formatDate($deadline)
    {
        return $deadline ? Carbon::createFromFormat("Ymd", $deadline)->format("Y-m-d") : null;
    }

    /**
     * Auto-generates a requisition number when creating a new record.
     * Format: YYMMXXXXXX (e.g., '2502000001' for Feb 2025).
     * Resets monthly, starting from '000001'.
     */
    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->requisition_number) {
                $model->requisition_number = self::generateRequisitionNumber();
            }

            // Automatically set the "creator" field to the logged-in user's ID
            if (!$model->creator) {
                $model->creator = auth()->user()->employee_code; // Set creator
            }
        });
    }

    /**
     * Generates a unique requisition number based on the latest entry.
     */
    public static function generateRequisitionNumber()
    {
        $latest = self::whereRaw('LENGTH(requisition_number) = 10')->orderBy('id', 'DESC')->first();
        $prefix = date('ym'); // YYMM format

        return ($latest && substr($latest->requisition_number, 0, 4) == $prefix)
            ? $prefix . sprintf("%06d", substr($latest->requisition_number, 4) + 1)
            : $prefix . '000001';
    }

    /**
     * Converts requested_date from 'Ymd' to 'Y-m-d H:i:s' before saving.
     */
    public function setRequestedDateAttribute($value)
    {
        $this->attributes['requested_date'] = $value 
            ? Carbon::createFromFormat('Ymd', $value)->format('Y-m-d H:i:s') 
            : null;
    }

    /**
     * Converts deadline from 'Ymd' to 'Y-m-d H:i:s' before saving.
     */
    public function setDeadlineAttribute($value)
    {
        $this->attributes['deadline'] = $value 
            ? Carbon::createFromFormat('Ymd', $value)->format('Y-m-d H:i:s') 
            : null;
    }
    
    /**
     * Search for purchase requisitions based on the provided filters.
     *
     * @param array $filters An associative array of filters to apply to the search query.
     *     - 'request_date_from' (string|null): The start date for the requested date range.
     *     - 'request_date_to' (string|null): The end date for the requested date range.
     *     - 'deadline_from' (string|null): The start date for the deadline date range.
     *     - 'deadline_to' (string|null): The end date for the deadline date range.
     *     - 'department_code_start' (string|null): The start of the department code range.
     *     - 'department_code_end' (string|null): The end of the department code range.
     *     - 'line_code_start' (string|null): The start of the line code range.
     *     - 'line_code_end' (string|null): The end of the line code range.
     *     - 'supplier_code' (string|null): The supplier code to filter by.
     *     - 'part_number' (string|null): The part number to filter by (supports partial match).
     *     - 'product_name' (string|null): The product name to filter by (supports partial match).
     *     - 'standard' (string|null): The standard to filter by (supports partial match).
     *     - 'approval_method_category' (array|string|null): The approval method categories to filter by.
     *     - 'state_classification' (array|string|null): The state classifications to filter by.
     *     - 'purchase_requisition_no' (string|null): The purchase requisition number to filter by (supports partial match).
     * @param bool $isDownload Whether to return all results (for download) or paginate the results.
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
     *     The paginated results or the full collection of results if $isDownload is true.
     */
    public static function search($filters, $isDownload = false)
    {
       $query = self::with(['department', 'line', 'supplier'])
           ->whereDateRangeOrEqual('requested_date', $filters['request_date_from'] ?? null, $filters['request_date_to'] ?? null)
           ->whereDateRangeOrEqual('deadline', $filters['deadline_from'] ?? null, $filters['deadline_to'] ?? null)
           ->whereColumnBetweenOrEqual('department_code', $filters['department_code_start'] ?? null, $filters['department_code_end'] ?? null)
           ->whereColumnBetweenOrEqual('line_code', $filters['line_code_start'] ?? null, $filters['line_code_end'] ?? null)
           ->when($filters['employee_code'] ?? null, fn($query, $employeeCode) => $query->where('creator', $employeeCode))
           ->when($filters['supplier_code'] ?? null, fn($query, $supplierCode) => $query->where('supplier_code', $supplierCode))
           ->when($filters['part_number'] ?? null, fn($query, $partNumber) => $query->where('part_number', 'LIKE', "%{$partNumber}%"))
           ->when($filters['product_name'] ?? null, fn($query, $productName) => $query->where('product_name', 'LIKE', "%{$productName}%"))
           ->when($filters['standard'] ?? null, fn($query, $standard) => $query->where('standard', 'LIKE', "%{$standard}%"))
           ->when(!empty($filters['approval_method_category']), fn($query) => $query->whereIn('approval_method_category', (array) $filters['approval_method_category']))
           ->when(!empty($filters['state_classification']), fn($query) => $query->whereIn('state_classification', (array) $filters['state_classification']))
           ->when($filters['purchase_requisition_no'] ?? null, fn($query, $purchaseRequisitionNo) => $query->where('requisition_number', 'LIKE', "%{$purchaseRequisitionNo}%"));
    
       if (isset($filters['purpose']) && $filters['purpose'] != '') {
           switch ($filters['purpose']) {
               case 3:
                   $query->whereHas("approvals", function ($query) use ($filters) {
                       $query->where('approver_employee_code', $filters['current_user'])
                           ->whereNotNull('approval_date');
                   });
                   break;
    
               case 2:
                   $query->whereHas("approvals", function ($query) use ($filters) {
                       $query->where('approver_employee_code', $filters['current_user']);
                   });
    
                   $purchaseRequisitions = $query->get();
    
                   $filteredRequisitions = $purchaseRequisitions->filter(function ($requisition) use ($filters) {
                       $approvals = $requisition->approvals;
    
                       $currentApproval = $approvals->firstWhere('approver_employee_code', $filters['current_user']);
    
                       if (!$currentApproval) {
                           return false;
                       }
    
                       $currentOrder = $currentApproval->order_of_approval;
    
                       $lowerApprovals = $approvals->where('order_of_approval', '<', $currentOrder);
    
                       if ($lowerApprovals->count() == 0) {
                           return false;                            
                       }
    
                       $hasApprovalDateInLowerOrders = $lowerApprovals->contains(function ($approval) {
                           return !is_null($approval->approval_date);
                       });
    
                       if ($hasApprovalDateInLowerOrders) {
                           return false;
                       }
    
                       return true;
                   });
    
                   $query->whereIn('requisition_number', $filteredRequisitions->pluck('requisition_number'));
                   break;
                   
               default:
                   $query->where('state_classification', '!=', 9)
                       ->whereHas("approvals", function ($query) use ($filters) {
                           $query->where('approver_employee_code', $filters['current_user']);
                       });
    
                   $purchaseRequisitions = $query->get();
    
                   $filteredRequisitions = $purchaseRequisitions->filter(function ($requisition) use ($filters) {
                       $approvals = $requisition->approvals;
    
                       $currentApproval = $approvals->firstWhere('approver_employee_code', $filters['current_user']);
    
                       if ($currentApproval->approval_date) {
                           return false;
                       }
    
                       if (!$currentApproval) {
                           return false;
                       }
    
                       $currentOrder = $currentApproval->order_of_approval;
    
                       $lowerApprovals = $approvals->where('order_of_approval', '<', $currentOrder);
    
                       $hasApprovalDateInLowerOrders = $lowerApprovals->contains(function ($approval) {
                           return is_null($approval->approval_date);
                       });
    
                       if ($hasApprovalDateInLowerOrders) {
                           return false;
                       }
    
                       return true;
                   });
    
                   $query->whereIn('requisition_number', $filteredRequisitions->pluck('requisition_number'));
                   break;
           }
       }
    
       $query->orderByDesc('created_at');

       return $isDownload ? $query->limit(config('search.max_records', 100))->get() : $query->paginateResults(10);
    }

    /**
     * Retrieve all order forms for reissue based on the provided filters.
     *
     * This method fetches order forms with related supplier, line, unit, employee, and department data.
     * It applies date range filters, purchase order number, and supplier code filters if provided.
     * The results are grouped by purchase order number and paginated.
     *
     * @param array $filters An associative array of filters to apply. Possible keys:
     *                       - 'order_date_from' (string|null): The start date for the order date range filter.
     *                       - 'order_date_to' (string|null): The end date for the order date range filter.
     *                       - 'purchase_order_number' (string|null): The purchase order number to filter by.
     *                       - 'supplier_code' (string|null): The supplier code to filter by.
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator Paginated result set of order forms.
     */
    public static function getAllOrderFormReissue($filters)
    {
        return self::with('supplier', 'line', 'unit', 'employee', 'department')
            ->whereDateRangeOrEqual('order_date', $filters['order_date_from'] ?? null, $filters['order_date_to'] ?? null)
            ->when($filters['purchase_order_number'] ?? null, fn($query, $purchaseOrderNumber) => $query->where('purchase_order_number', $purchaseOrderNumber))
            ->when($filters['supplier_code'] ?? null, fn($query, $supplierCode) => $query->where('supplier_code', $supplierCode))
            ->whereNotNull('purchase_order_number')
            ->groupBy('purchase_order_number')
            ->orderByDesc('created_at')
            ->paginateResults();
    }

    /**
     * Retrieve a Purchase Requisition record along with its associated supplier.
     *
     * @param int $id The ID of the Purchase Requisition record to retrieve.
     * @return \Illuminate\Database\Eloquent\Model|null The Purchase Requisition record with its supplier, or null if not found.
     */
    public static function getRecordWithSupplier($id)
    {
        return self::with('supplier')
            ->where('id', '=', $id)
            ->first();
    }

    /**
     * Retrieve all records for export based on the given purchase order number.
     *
     * This method fetches all records associated with the specified purchase order number,
     * including related supplier, line, unit, employee, and department information.
     *
     * @param string $purchaseOrderNumber The purchase order number to filter records by.
     * @return \Illuminate\Database\Eloquent\Collection The collection of records matching the purchase order number.
     */
    public static function getAllRecordsForExport($purchaseOrderNumber)
    {
        return self::with('supplier')
            ->with('line')
            ->with('unit')
            ->with('employee')
            ->with('department')
            ->where('purchase_order_number', '=', $purchaseOrderNumber)
            ->get();
    }


    /**
     * Scope a query to get the order data list with related models and summaries.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Http\Request $request
     */
    public function scopeGetOrderDataList($query, $request) {

        return PurchaseRequisition::with([
            'line', 'department', 'product', 'supplier', 'arrivalsSummary', 'recordsSummary'
        ])
        ->withSum('arrivalsSummary as total_arrival_quantity', 'arrival_quantity')
        ->withSum('recordsSummary as total_record_quantity', 'quantity')
        ->filter($request)
        ->addSelect('purchase_requisitions.*');
    }

    public static function getDataList($filters)
    {
        $query = self::with([
            'line', 'unit', 'department', 'product', 'supplier', 'arrivalsSummary', 'recordsSummary', 'employee'
        ])->whereHas('arrival', function($query) use ($filters) {
            $query->whereDateRangeOrEqual('arrival_day', $filters['arrival_date_start'] ?? null, $filters['arrival_date_end'] ?? null);
        })
        ->whereDateRangeOrEqual('requested_date', $filters['request_date_start'] ?? null, $filters['request_date_end'] ?? null)
        ->whereDateRangeOrEqual('order_date', $filters['order_date_start'] ?? null, $filters['order_date_end'] ?? null)
        ->whereDateRangeOrEqual('deadline', $filters['deadline_date_start'] ?? null, $filters['deadline_date_end'] ?? null)
        ->whereColumnBetweenOrEqual('supplier_code', $filters['supplier_code_start'] ?? null, $filters['supplier_code_end'] ?? null)
        ->whereColumnBetweenOrEqual('department_code', $filters['department_code_start'] ?? null, $filters['department_code_end'] ?? null)
        ->whereColumnBetweenOrEqual('line_code', $filters['line_code_start'] ?? null, $filters['line_code_end'] ?? null)
        ->whereColumnBetweenOrEqual('machine_number', $filters['machine_code_start'] ?? null, $filters['machine_code_end'] ?? null)
        ->whereColumnBetweenOrEqual('expense_items', $filters['expense_item_start'] ?? null, $filters['expense_item_end'] ?? null)
        ->when($filters['employee_code'] ?? null, fn($query, $employeeCode) => $query->where('creator', $employeeCode))
        ->when($filters['supplier_code'] ?? null, fn($query, $supplierCode) => $query->where('supplier_code', $supplierCode))
        ->when($filters['part_number'] ?? null, fn($query, $partNumber) => $query->where('part_number', $partNumber))
        ->when($filters['product_name'] ?? null, fn($query, $productName) => $query->where('product_name', $productName))
        ->when($filters['standard'] ?? null, fn($query, $standard) => $query->where('standard', 'LIKE', "%{$standard}%"))
        ->when($filters['requisition_number'] ?? null, fn($query, $requisitionNumber) => $query->where('requisition_number', 'LIKE', "%{$requisitionNumber}%"))
        ->when($filters['purchase_order_number'] ?? null, fn($query, $purchaseOrderNumber) => $query->where('purchase_order_number', $purchaseOrderNumber))
        ->withSum('arrivalsSummary as total_arrival_quantity', 'arrival_quantity')
        ->withSum('recordsSummary as total_record_quantity', 'quantity');

        if (!empty($filters->status)) {
            if ($filters->status == 'non-stock') {
                $query->doesntHave('arrivalsSummary');
            } elseif ($filters->status == 'in-stock') {
                $query->whereHas('arrivalsSummary', function ($query) {
                    // $query->where('purchase_receipt_date', null);
                    $query->whereColumn('arrival_quantity', '<', 'purchase_requisitions.quantity');
                });
            } elseif ($filters->status == 'arrive-stock') {
                $query->whereHas('arrivalsSummary', function ($query) {
                    // $query->where('purchase_receipt_date', null);
                    $query->whereColumn('arrival_quantity', '>=', 'purchase_requisitions.quantity');
                });
            }
        }

        if (!empty($filters->acceptance)) {
            if ($filters->acceptance == 'all') {
                $query->whereHas('arrivalsSummary', function ($query) {
                    $query->where('purchase_receipt_date','!=', null);
                });
            } elseif ($filters->acceptance == 'incomplete') {
                $query->whereHas('arrivalsSummary', function ($query) {
                    // $query->where('purchase_receipt_date','!=', null);
                    $query->whereColumn('arrival_quantity', '<', 'purchase_requisitions.quantity');
                });
            } elseif ($filters->acceptance == 'complete') {
                $query->whereHas('arrivalsSummary', function ($query) {
                    $query->where('purchase_receipt_date','!=', null);
                    $query->whereColumn('arrival_quantity', '>=', 'purchase_requisitions.quantity');
                });
            }
        }

        if (!empty($filters->slip_no)) {
            $query->whereHas('arrivalsSummary', function ($query) use ($filters) {
                $query->where('slip_no', 'LIKE', "%{$filters->slip_no}%");
            });
        }

        $query->orderByDesc('created_at');
        
        return $query;
    }
  
    public function processApproval($approval, $employee, $approvalType)
    {
        if ($approvalType === "unapprove") {
            $approval->update([
                "approval_date" => null,
                "denial_date" => null,
            ]); // Process cancellation of requisition approval regardless of $nextApproval
            // $nextApproval = $approval->nextApproval();
            // if ($nextApproval?->approval_date === null) {
            //     $approval->update([
            //         "approval_date" => null,
            //         "denial_date" => null,
            //     ]);
            // } else {
            //     return;
            // }
        } else {
            $approval->update([
                "approval_date" => now(),
                "denial_date" => null,
            ]);

            $currentApproval = $approval->firstWhere('approver_employee_code', $employee->employee_code);

            if ($currentApproval) {
                $currentOrder = $currentApproval->order_of_approval;
    
                $this->approvals()
                    ->where('order_of_approval', '<', $currentOrder)
                    ->whereNull('approval_date')
                    ->update(['approval_date' => now()]);
            }
            $this->update(['state_classification' => 1]);
        }
    
        if (method_exists($this, 'assignNextApprover')) {
            $this->assignNextApprover($employee);
        }
    }

    /**
     * Retrieve a purchase requisition record by order number and order details number.
     *
     * This method fetches the first record that matches the given purchase order number
     * and purchase order details number, including the related supplier information.
     *
     * @param string $orderNumber The purchase order number to search for.
     * @param string $orderDetailNumber The purchase order details number to search for.
     * @return \Illuminate\Database\Eloquent\Model|null The first matching purchase requisition record, or null if no match is found.
     */
    public static function getRecordByOrderNumberOrderDetailsNumber($orderNumber, $orderDetailNumber)
    {
        return self::with('supplier')
                ->where('purchase_order_number', $orderNumber)
                ->where('purchase_order_details_number', $orderDetailNumber)
                ->first();
    }
}