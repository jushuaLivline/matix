<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Traits\PaginateWithLimit;

class SupplyMaterialOrder extends Model
{
    use HasFactory;
    
    // Trait for paginating with a record limit.
    use PaginateWithLimit;
    protected $guarded = [];

    protected $casts = [
        'arrival_date' => 'date',
        'instruction_date' => 'date',
    ];
    protected $fillable = [
        'supply_material_order_no',
        'management_no',
        'branch_number',
        'material_number',
        'order_classification',
        'supplier_code',
        'material_manufacturer_code',
        'instruction_date',
        'instruction_no',
        'lot',
        'instruction_kanban_quantity',
        'instruction_number',
        'arrival_quantity',
        'where_to_use_department_code',
        'document_issue_date',
        'creator',
    ];

    public function kanban()
    {
        return $this->belongsTo(KanbanMaster::class, 'management_no', 'management_no');
    }

    public function product()
    {
        return $this->belongsTo(ProductNumber::class, 'material_number', 'part_number');
    }

    public function supplier()
    {
        return $this->belongsTo(Customer::class, 'supplier_code', 'customer_code');
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

    public function createSupplyMaterialOrder($kanbanMaster)
    {
        // Check if SupplyMaterialOrder already exists
        $supplyMaterialOrder = $this->where('management_no', $kanbanMaster->management_no)
            ->where('instruction_date', $kanbanMaster->create_date)
            ->first();

        // If SupplyMaterialOrder doesn't exist, create a new one
        if (!$supplyMaterialOrder) {
            // Generate the supply material order number
            $last_data = $this->whereNotNull('management_no')
                ->whereRaw('LENGTH(supply_material_order_no) = 10')
                ->orderBy('id', direction: 'DESC')
                ->first();

            $supply_material_order_no = date('ym');
            if (substr($last_data?->supply_material_order_no, 0, 4) == $supply_material_order_no) {
                $supply_material_order_no .= sprintf("%06d", (int) substr($last_data->supply_material_order_no, 4) + 1);
            } else {
                $supply_material_order_no .= sprintf("%06d", 1);
            }

            // Create the SupplyMaterialOrder
            $supplyMaterialOrder = $this->create([
                'supply_material_order_no' => $supply_material_order_no,
                'management_no' => $kanbanMaster->management_no,
                'branch_number' => $kanbanMaster->supplier_code,
                'material_number' => $kanbanMaster->part_number,
                'order_classification' => 1,
                'supplier_code' => $kanbanMaster->supplier_code,
                'material_manufacturer_code' => $kanbanMaster->material_manufacturer_code,
                'instruction_date' => $kanbanMaster->create_date,
                'instruction_no' => $kanbanMaster->process_code,
                'instruction_kanban_quantity' => $kanbanMaster->number_of_cycles,
                'instruction_number' => $kanbanMaster->number_of_accomodated * $kanbanMaster->number_of_cycles,
                'document_issue_date' => $kanbanMaster->create_date,
                'creator' => $kanbanMaster->auth_user_id, // authenticated user
            ]);
        }

        return $supplyMaterialOrder;
    }

    public static function search(array $filters)
    {
        $query = self::with(['kanban', 'product', 'supplier'])
            ->when(!empty($filters['manufacturer_code']), function ($query) use ($filters) {
                return $query->where('supplier_code', $filters['manufacturer_code']);
            })
            ->when(!empty($filters['instruction_no_from']), function ($query) use ($filters) {
                return $query->where('instruction_no', '>=', $filters['instruction_no_from']);
            })
            ->when(!empty($filters['instruction_no_to']), function ($query) use ($filters) {
                return $query->where('instruction_no', '<=', $filters['instruction_no_to']);
            })
            ->when(!empty($filters['instruction_date_start']), function ($query) use ($filters) {
                return $query->where('instruction_date', '>=', Carbon::parse($filters['instruction_date_start'])->format('Y-m-d'));
            })
            ->when(!empty($filters['instruction_date_end']), function ($query) use ($filters) {
                return $query->where('instruction_date', '<=', Carbon::parse($filters['instruction_date_end'])->format('Y-m-d'));
            })
            ->when(!empty($filters['arrival_day_from']), function ($query) use ($filters) {
                return $query->where('instruction_date', '>=', Carbon::parse($filters['arrival_day_from'])->format('Y-m-d'));
            })
            ->when(!empty($filters['arrival_day_to']), function ($query) use ($filters) {
                return $query->where('instruction_date', '<=', Carbon::parse($filters['arrival_day_to'])->format('Y-m-d'));
            })
            ->whereNotIn('order_classification', [4]); // exclude 4: 材料調達計画表 | Material Procurement Plan

        return $query->orderByDesc('created_at');
    }

    private static function parseDateRange($query, string $column, string $startDate, ?string $endDate = null)
    {
        $startDate = Carbon::parse($startDate)->format('Y-m-d');
        if ($endDate) {
            $endDate = Carbon::parse($endDate)->format('Y-m-d');
            return $query->whereBetween($column, [$startDate, $endDate]);
        }
        return $query->where($column, $startDate);
    }

    public function generateSupplyMaterialOrderNo()
    {
        $latest = self::whereRaw('LENGTH(supply_material_order_no) = 10')->orderBy('id', 'DESC')->first();
        $prefix = date('ym'); // YYMM format

        return ($latest && substr($latest->supply_material_order_no, 0, 4) == $prefix)
            ? $prefix . sprintf("%06d", substr($latest->supply_material_order_no, 4) + 1)
            : $prefix . '000001';
    }


    public function orderDetailPDF($request)
    {
        $issueClassification = $request->issue_classification;

        // Define common select columns
        $query = SupplyMaterialOrder::query()
        ->select([
            'smo.id',
            'smo.supply_material_order_no',
            'smo.instruction_date',
            'smo.instruction_number',
            'smo.material_number',
            'smo.arrival_quantity',
            'smo.supplier_code',
            'smo.management_no',
            'smo.instruction_no',
            'smo.material_manufacturer_code',
            'smo.instruction_kanban_quantity',
            DB::raw('MAX(mst_pn.product_name) as product_name'),
            DB::raw('MAX(mst_pn.uniform_number) as uniform_number'),
            DB::raw('MAX(mst_km.number_of_accomodated) as number_of_accomodated'),
            DB::raw('MAX(mst_c.customer_name) as customer_name')
        ])
        ->from('supply_material_orders as smo')
        ->leftJoin('product_numbers as pn', function ($join) {
            $join->on('pn.part_number', '=', 'smo.material_number');
        })
        ->leftJoin('kanban_masters as km', function ($join) {
            $join->on('km.part_number', '=', 'smo.material_number');
        })
        ->leftJoin('customers as c', function ($join) {
            $join->on('c.customer_code', '=', 'smo.supplier_code');
        })
        ->where(function ($q) {
            $q->whereNotIn('smo.order_classification', [4])
            ->orWhereNull('smo.order_classification');
        })
        ->when($request->manufacturer_code, fn($q) => $q->where('smo.supplier_code', $request->manufacturer_code))
        ->when($request->instruction_date_from || $request->instruction_date_to, function ($q) use ($request) {
            $startDate = $request->instruction_date_from ? Carbon::parse($request->instruction_date_from)->startOfDay()->format('Y-m-d') : null;
            $endDate = $request->instruction_date_to ? Carbon::parse($request->instruction_date_to)->endOfDay()->format('Y-m-d') : null;

            if ($startDate || $endDate) {
                if ($startDate && !$endDate) {
                    $q->where('smo.instruction_date', '>=', $startDate);
                } elseif (!$startDate && $endDate) {
                    $q->where('smo.instruction_date', '<=', $endDate);
                } else {
                    $q->whereBetween('smo.instruction_date', [$startDate, $endDate]);
                }
            }
        })
        ->when($issueClassification === 'no-issue', fn($q) => $q->whereNull('smo.document_issue_date'))
        ->when($issueClassification === 'reissue', function ($q) use ($request) {
            $q->whereNotNull('smo.document_issue_date')
            ->when(filled($request->instruction_no_from) || filled($request->instruction_no_to), function ($q) use ($request) {
                $startNo = filled($request->instruction_no_from) ? $request->instruction_no_from : null;
                $endNo = filled($request->instruction_no_to) ? $request->instruction_no_to : null;

                if ($startNo && !$endNo) {
                    $q->where('smo.instruction_no', '>=', $startNo);
                } elseif (!$startNo && $endNo) {
                    $q->where('smo.instruction_no', '<=', $endNo);
                } elseif ($startNo && $endNo) {
                    $q->whereBetween('smo.instruction_no', [$startNo, $endNo]);
                }
            });
        })
        ->groupBy('smo.id')
        ->orderBy('customer_name')
        ->orderBy('smo.instruction_date')
        ->orderBy('smo.instruction_no');
        return $query;
    }

}
