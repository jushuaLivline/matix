<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\PurchaseRecord;
use App\Models\PurchaseRequisition;
use Illuminate\Http\Request;

class PurchaseRecordController extends Controller
{
    // Session based api
    function purchaseRequisitionInput($purchaseRequisitionId){
        $purchaseRequisition = PurchaseRequisition::find($purchaseRequisitionId);
        $purchaseRequisition->load(['department', 'line', 'product', 'supplier', 'expense']);

        return [
            'department_code' => $purchaseRequisition->department_code,
            'department_name' => $purchaseRequisition->department->name,
            'line_code' => $purchaseRequisition->line_code,
            'line_name' => $purchaseRequisition->line->line_name,
            'part_number' => $purchaseRequisition->part_number,
            'product_name' => $purchaseRequisition->product_name,
            'standard' => $purchaseRequisition->standard, 'specification' => $purchaseRequisition->standard, // same item but different name in the blade file input
            'supplier_code' => $purchaseRequisition->supplier_code,
            'supplier_name' => $purchaseRequisition->supplier->supplier_name_abbreviation,
            'quantity' => $purchaseRequisition->quantity,
            'unit_code' => $purchaseRequisition->unit_code,
            'unit_price' => $purchaseRequisition->unit_price,
            'amount_of_money' => $purchaseRequisition->amount_of_money,
            'reason' => $purchaseRequisition->reason,
            'expense_item_code' => $purchaseRequisition->expense_items,
            'expense_item_name' => $purchaseRequisition->expense?->item_name,
            'deadline' => $purchaseRequisition->deadline->format("Ymd"),
            'quotation_existence_flag' =>  $purchaseRequisition->quotation_existence_flag,
            'remarks' => $purchaseRequisition->remarks,
            'approval_method_category' => $purchaseRequisition->approval_method_category,
            'approval_route_number' => $purchaseRequisition->approval_route_number,
        ];
    }

    // Session based api
    function purchaseRecordInputProcess($purchaseRecordInputProcessId){
        $purchaseRecordInput = PurchaseRecord::find($purchaseRecordInputProcessId);
        $purchaseRecordInput->load(['department', 'line', 'product', 'supplier', 'item', 'machine', 'customer']);

        return [
            'voucher_class' => $purchaseRecordInput->voucher_class,
            'arrival_date' => $purchaseRecordInput->arrival_date?->format('Ymd'),
            'supplier_code' => $purchaseRecordInput->supplier_code,
            'supplier_name' => $purchaseRecordInput->supplier->supplier_name_abbreviation ?? null,
            'machine_number' => $purchaseRecordInput->machine_number,
            'machine_number2' => $purchaseRecordInput->subsidy_items,
            'machine_number_name' => $purchaseRecordInput->machine->machine_number_name ?? null,
            'department_code' => $purchaseRecordInput->department_code,
            'department_name' => $purchaseRecordInput->department->name ?? null,
            'line_code' => $purchaseRecordInput->line_code,
            'line_name' => $purchaseRecordInput->line->line_name ?? null,
            'item_code' => $purchaseRecordInput->expense_item,
            'item_name' => $purchaseRecordInput->item->item_name ?? null,
            'part_number' => $purchaseRecordInput->part_number,
            'product_number_number' => $purchaseRecordInput->part_number ?? null,
            'product_number_name' => $purchaseRecordInput->product_name ?? null,
            'product_name' => $purchaseRecordInput->product_name,
            'standard' => $purchaseRecordInput->standard,
            'where_to_use_code' => $purchaseRecordInput->where_used_code,
            'where_to_use_name' => $purchaseRecordInput->customer->customer_name ?? null,
            'quantity' => $purchaseRecordInput->quantity,
            'unit_code' => $purchaseRecordInput->unit_code,
            'unit_price' => $purchaseRecordInput->unit_price,
            'amount' => $purchaseRecordInput->amount_of_money,
            'tax_classification' => $purchaseRecordInput->tax_classification,
            'slip_code' => $purchaseRecordInput->slip_no,
            'project_code' => $purchaseRecordInput->project_number,
            'project_name' => $purchaseRecordInput->project->project_name ?? null,
            'remarks' => $purchaseRecordInput->remarks
        ];
    }
  
    function approvalRouteSetting($approvalRouteSetting){
        $approvalRouteSetting = PurchaseRecord::find($approvalRouteSetting);
        $approvalRouteSetting->load(['department', 'line', 'product', 'supplier', 'item', 'machine', 'customer']);

        return [
            'voucher_class' => $approvalRouteSetting->voucher_class,
            'slip_type' => $approvalRouteSetting->slip_type,
            'due_date_start' => $approvalRouteSetting->arrival_date?->format('Ymd'),
            'supplier_code' => $approvalRouteSetting->supplier_code,
            'supplier_name' => $approvalRouteSetting->supplier->supplier_name_abbreviation ?? null,
            'machine_number' => $approvalRouteSetting->machine_number,
            'machine_number2' => $approvalRouteSetting->subsidy_items,
            'machine_number_name' => $approvalRouteSetting->machine->machine_number_name ?? null,
            'department_code' => $approvalRouteSetting->department_code,
            'department_name' => $approvalRouteSetting->department->name ?? null,
            'line_code' => $approvalRouteSetting->line_code,
            'line_name' => $approvalRouteSetting->line->line_name ?? null,
            'item_code' => $approvalRouteSetting->expense_item,
            'item_name' => $approvalRouteSetting->item->item_name ?? null,
            'product_number_number' => $approvalRouteSetting->part_number ?? null,
            'product_number_name' => $approvalRouteSetting->product_name ?? null,
            'product_name' => $approvalRouteSetting->product_name,
            'standard' => $approvalRouteSetting->standard,
            'where_to_use_code' => $approvalRouteSetting->where_used_code,
            'where_to_use_name' => $approvalRouteSetting->customer->customer_name ?? null,
            'quantity' => $approvalRouteSetting->quantity,
            'unit_code' => $approvalRouteSetting->unit_code,
            'unit_price' => $approvalRouteSetting->unit_price,
            'amount' => $approvalRouteSetting->amount_of_money,
            'tax_classification' => $approvalRouteSetting->tax_classification,
            'slip_code' => $approvalRouteSetting->slip_no,
            'project_code' => $approvalRouteSetting->project_number,
            'project_name' => $approvalRouteSetting->project->project_name ?? null,
            'remarks' => $approvalRouteSetting->remarks
        ];
    }
          
    function purchasingItemPurchaseRecordInput($purchasingItemPurchaseRecordInput){
        $purchasingItemPurchaseRecordInput = PurchaseRecord::find($purchasingItemPurchaseRecordInput);
        $purchasingItemPurchaseRecordInput->load(['department', 'line', 'product', 'supplier', 'item', 'machine', 'customer']);

        return [
            'voucher_class' => $purchasingItemPurchaseRecordInput->voucher_class,
            'slip_type' => $purchasingItemPurchaseRecordInput->slip_type,
            'due_date_start' => $purchasingItemPurchaseRecordInput->arrival_date?->format('Ymd'),
            'supplier_code' => $purchasingItemPurchaseRecordInput->supplier_code,
            'supplier_name' => $purchasingItemPurchaseRecordInput->supplier->supplier_name_abbreviation ?? null,
            'machine_number' => $purchasingItemPurchaseRecordInput->machine_number,
            'machine_number2' => $purchasingItemPurchaseRecordInput->subsidy_items,
            'machine_number_name' => $purchasingItemPurchaseRecordInput->machine->machine_number_name ?? null,
            'department_code' => $purchasingItemPurchaseRecordInput->department_code,
            'department_name' => $purchasingItemPurchaseRecordInput->department->name ?? null,
            'line_code' => $purchasingItemPurchaseRecordInput->line_code,
            'line_name' => $purchasingItemPurchaseRecordInput->line->line_name ?? null,
            'item_code' => $purchasingItemPurchaseRecordInput->expense_item,
            'item_name' => $purchasingItemPurchaseRecordInput->item->item_name ?? null,
            'product_number_number' => $purchasingItemPurchaseRecordInput->part_number ?? null,
            'product_number_name' => $purchasingItemPurchaseRecordInput->product_name ?? null,
            'product_name' => $purchasingItemPurchaseRecordInput->product_name,
            'standard' => $purchasingItemPurchaseRecordInput->standard,
            'where_to_use_code' => $purchasingItemPurchaseRecordInput->where_used_code,
            'where_to_use_name' => $purchasingItemPurchaseRecordInput->customer->customer_name ?? null,
            'quantity' => $purchasingItemPurchaseRecordInput->quantity,
            'unit_code' => $purchasingItemPurchaseRecordInput->unit_code,
            'unit_price' => $purchasingItemPurchaseRecordInput->unit_price,
            'amount' => $purchasingItemPurchaseRecordInput->amount_of_money,
            'tax_classification' => $purchasingItemPurchaseRecordInput->tax_classification,
            'slip_code' => $purchasingItemPurchaseRecordInput->slip_no,
            'project_code' => $purchasingItemPurchaseRecordInput->project_number,
            'project_name' => $purchasingItemPurchaseRecordInput->project->project_name ?? null,
            'remarks' => $purchasingItemPurchaseRecordInput->remarks
        ];
    }
}
