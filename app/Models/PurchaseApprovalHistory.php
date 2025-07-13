<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseApprovalHistory extends Model
{
    use HasFactory;
    public $timestamps = false; // Fix updated_at issue
    protected $fillable = [
        'purchase_requisition_id',
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
        'deadline',
        'quotation_existence_flag',
        'approval_method_category',
        'remarks',
        'reason_for_denial',
        'creator',
        'created_at'
    ];


    public function createRecord($request, $dataType = false) {
        $requestParams = ($dataType) ? $request : $request->validated();
    
        $allowedFields = [
            'purchase_requisition_id', 'department_code', 'line_code',
            'machine_number', 'part_number', 'product_name', 'standard',
            'reason', 'quantity', 'unit_code', 'unit_price', 'amount_of_money',
            'expense_items', 'deadline', 'quotation_existence_flag',
            'approval_method_category', 'remarks', 'reason_for_denial', 'creator'
        ];
      
        $filteredData = array_intersect_key($requestParams, array_flip($allowedFields));
        $filteredData['purchase_requisition_id'] = (int) $request['requisition_number'];

        return $this->create($filteredData);
    }
}
