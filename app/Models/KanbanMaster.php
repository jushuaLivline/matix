<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\PaginateWithLimit;

class KanbanMaster extends Model
{
    use HasFactory;
    use PaginateWithLimit;

    protected $fillable = [
        'management_no',
        'kanban_classification',
        'part_number',
        'process_code',
        'customer_acceptance',
        'process_order',
        'next_process_code',
        'cycle_day',
        'number_of_cycles',
        'cycle_interval',
        'number_of_accomodated',
        'box_type',
        'acceptance',
        'shipping_location',
        'printed_jersey_number',
        'remark_1',
        'remark_2',
        'remark_qr_code',
        'issued_sequence_number',
        'paid_category',
        'delete_flag',
        'creator',
        'updator'
    ];

    protected $casts = [
        'acceptance' => 'integer',
        'part_number' => 'string',
    ];

    protected $guarded = [];

    public function supplyMaterialOrder()
    {
        return $this->hasMany(SupplyMaterialOrder::class);
    }

    public function process()
    {
        return $this->belongsTo(Process::class, 'process_code', 'process_code');
    }

    public function next_process()
    {
        return $this->belongsTo(Process::class, 'next_process_code', 'process_code');
    }

    public function product()
    {
        return $this->belongsTo(ProductNumber::class, 'part_number', 'part_number');
    }

    public function removeKabanMaster($id) {
        // Find the record with the given ID
        $kanbanMaster = $this->find($id);
        if ($kanbanMaster) {
            // Soft delete the record using Laravel's built-in functionality
            $kanbanMaster->delete_flag = 1;
            $kanbanMaster->save(); // Save the updated record
            return $kanbanMaster;
        }

        return false; // Explicitly return false if the record is not found
    }
    
    public function supplyMaterialKanban($request, $selectedManagementNos)
    {
        return $this->with('product', 'process', 'product.customer')
            // ->leftjoin('product_numbers', 'kanban_masters.part_number', '=', 'product_numbers.part_number')
            // ->leftJoin('processes', 'product_numbers.material_manufacturer_code', '=', 'processes.process_code')
            // ->leftJoin('customers', 'product_numbers.supplier_code', '=', 'customers.customer_code')
            ->whereIn('kanban_masters.management_no', $selectedManagementNos)
            ->where('kanban_masters.kanban_classification', $request->kanban_classification)
            ->where('kanban_masters.delete_flag', 0)
            ->select(
                'kanban_masters.id',
                'kanban_masters.management_no',
                'kanban_masters.part_number',
                'kanban_masters.printed_jersey_number',
                'kanban_masters.cycle_day',
                'kanban_masters.number_of_cycles',
                'kanban_masters.cycle_interval',
                'kanban_masters.number_of_accomodated',
                'kanban_masters.acceptance',
                'kanban_masters.process_code',
                'kanban_masters.delete_flag',
                // 'processes.backorder_days',
                // 'product_numbers.supplier_code',
                // 'product_numbers.material_manufacturer_code',
                // 'product_numbers.edited_part_number',
                // 'product_numbers.department_code',
                // 'customers.customer_name',
            )
            ->orderBy('kanban_masters.management_no')->get();
    }

    /**
     * Check if a barcode exists in the database.
     *
     * @param string $barcode
     * @return bool
     */
    public static function existsByBarcode($request, string $barcode): bool
    {
        return self::where('management_no', $barcode)
            ->where('kanban_classification', $request->kanban_classification)
            ->exists();
    }
}
