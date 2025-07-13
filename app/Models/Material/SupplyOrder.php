<?php

namespace App\Models\Material;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\KanbanMaster;
use App\Models\ProductNumber;
use App\Models\Customer;

class SupplyOrder extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'supply_material_orders';

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
        return $this->belongsTo(KanbanMaster::class, 'management_no');
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
                'lot' => 1,
                'instruction_kanban_quantity' => $kanbanMaster->number_of_cycles,
                'instruction_number' => $kanbanMaster->number_of_accomodated * $kanbanMaster->number_of_cycles,
                'where_to_use_department_code' => $kanbanMaster->supplier_code,
                'document_issue_date' => $kanbanMaster->create_date,
                'created_at' => now(),
                'creator' => $kanbanMaster->auth_user_id, // authenticated user
            ]);
        }

        return $supplyMaterialOrder;
    }

    public function generateSupplyMaterialOrderNo()
    {
        $latest = self::whereRaw('LENGTH(supply_material_order_no) = 10')->orderBy('id', 'DESC')->first();
        $prefix = date('ym'); // YYMM format

        return ($latest && substr($latest->supply_material_order_no, 0, 4) == $prefix)
            ? $prefix . sprintf("%06d", substr($latest->supply_material_order_no, 4) + 1)
            : $prefix . '000001';
    }
}
