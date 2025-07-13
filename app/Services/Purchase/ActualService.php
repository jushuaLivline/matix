<?php

namespace App\Services\Purchase;

use Carbon\Carbon;
use App\Models\PurchaseRecord;

class ActualService
{
    private $purchaseRecord;

    public function __construct(PurchaseRecord $purchaseRecord){
        $this->purchaseRecord = $purchaseRecord;
    }

    public function store($data){
        $arrivalDate = Carbon::parse($data->arrival_date);
        $currentYearMonth = now()->format('ym');
        $latestPurchaseRecordNumber = PurchaseRecord::where('purchase_record_no', 'like', "{$currentYearMonth}%")->max('purchase_record_no');
    
        if ($latestPurchaseRecordNumber) {
            $count = intval(substr($latestPurchaseRecordNumber, 4, 6)) + 1;
            $purchaseRecordNumber = $currentYearMonth . str_pad($count, 6, '0', STR_PAD_LEFT);
        } else {
            $purchaseRecordNumber = $currentYearMonth . '000001';
        }
    
        $purchasingItemPurchaseRecordInput = PurchaseRecord::create([
            'purchase_record_no' => $purchaseRecordNumber,
            'voucher_class' => $data->voucher_class,
            'slip_type' => $data->slip_type,
            'purchase_category' => $data->purchase_category,
            'arrival_date' => $arrivalDate,
            'supplier_code' => $data->supplier_code,
            'machine_number' => $data->machine_number,
            'department_code' => $data->department_code,
            'line_code' => $data->line_code,
            'expense_item' => $data->item_code,
            'subsidy_items' => $data->machine_number2,
            'part_number' => $data->part_number,
            'product_name' => $data->product_name,
            'standard' => $data->standard,
            'quantity' => $data->quantity,
            'unit_price' => $data->unit_price,
            'tax_classification' => $data->tax_classification,
            'project_number' => $data->project_code,
            'remarks' => $data->remarks,
            'where_used_code' => $data->where_to_use_code,
            'amount_of_money' => $data->amount_of_money,
            'slip_no' => $data->slip_code,
            'unit_code' => $data->unit_code,
            'creator' => auth()->user()->id,
        ]);
        return $purchasingItemPurchaseRecordInput;
    }

    public function check_previous_input($purchase_category){
        $user_id = auth()->user()->id;
        $data = $this->purchaseRecord->get_latest_data_by_user_id($user_id, $purchase_category);
        return $data;
    }

    public function copy_previous_input($purchase_category){
        $user_id = auth()->user()->id;
        $data = $this->purchaseRecord->get_latest_data_by_user_id($user_id, $purchase_category);
        return $data;
    }

    public function edit($id){
        $data = $this->purchaseRecord->get_data_by_id($id);
        return $data;
    }
    public function update($data, $id){
        $arrivalDate = Carbon::parse($data->arrival_date);
        $purchaseRecord = PurchaseRecord::findOrFail($id);
        $purchaseRecord->update([
            'voucher_class' => $data->voucher_class,
            'slip_type' => $data->slip_type,
            'purchase_category' => $data->purchase_category,
            'arrival_date' => $arrivalDate,
            'supplier_code' => $data->supplier_code,
            'machine_number' => $data->machine_number,
            'department_code' => $data->department_code,
            'line_code' => $data->line_code,
            'expense_item' => $data->item_code,
            'subsidy_items' => $data->machine_number2,
            'part_number' => $data->part_number,
            'product_name' => $data->product_name,
            'standard' => $data->standard,
            'quantity' => $data->quantity,
            'unit_price' => $data->unit_price,
            'tax_classification' => $data->tax_classification,
            'project_number' => $data->project_code,
            'remarks' => $data->remarks,
            'where_used_code' => $data->where_to_use_code,
            'amount_of_money' => $data->amount_of_money,
            'slip_no' => $data->slip_code,
            'unit_code' => $data->unit_code,
            'updator' => auth()->user()->id,
        ]);
        return $purchaseRecord;
    }

    public function destroy($id){
        $purchaseRecord = PurchaseRecord::findOrFail($id);
        $purchaseRecord->delete();
        return $purchaseRecord;
    }
}