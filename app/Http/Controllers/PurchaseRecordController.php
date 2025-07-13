<?php

namespace App\Http\Controllers;

use App\Constants\Constant;
use App\Exports\PurchaseAmountSearchExport;
use App\Exports\PurchaseConfirmOrderExport;
use App\Exports\PurchaseOrderFormReissue;
use App\Exports\PurchaseOrderListExport;
use App\Exports\PurchaseOrderProcessingExport;
use App\Exports\PurchaseRequisitionApprovalSearchExport;
use App\Exports\PurchaseRequisitionSearchExport;
use App\Exports\PurchaseOrderReissueExport;
use App\Exports\PurchaseRecordExport;
use App\Exports\PurchaseHistoryExport;
use App\Helpers\RequestHelper;
use App\Models\Code;
use App\Models\Customer;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Line;
use App\Models\MachineNumber;
use App\Models\Product;
use App\Models\ProductNumber;
use App\Models\Project;
use App\Models\Purchase;
use App\Models\PurchaseApproval;
use App\Models\PurchaseApprovalRoute;
use App\Models\PurchaseApprovalRouteDetail;
use App\Models\PurchaseArrival;
use App\Models\PurchaseRecord;
use App\Models\PurchaseRequisition;
use App\Transformers\PurchaseApprovalRouteDetailTransformer;
use App\Transformers\PurchaseApprovalRouteTransformer;
use App\Transformers\PurchaseApprovalTransformer;
use App\Services\Purchase\PurchaseApproverEmailNotification as EmailNotification;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;
use Fractal;
use PDO;
use Illuminate\Support\Facades\DB;
use Predis\Command\Redis\PUBSUB;


class PurchaseRecordController extends Controller
{
    protected $EmailNotification;
    public function __construct(EmailNotification $EmailNotification) 
    {
        $this->EmailNotification = $EmailNotification;
    }

    public function purchasingItemPurchaseRecordInputProcess(Request $request)
    {
        $arrivalDate = Carbon::parse($request->arrival_date);
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
            'voucher_class' => $request->voucher_class,
            'slip_type' => 1,
            'arrival_date' => $arrivalDate,
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
        ]);

        if ($request->session()->has('purchasingItemPurchaseRecordInput')) {
            $request->session()->forget('purchasingItemPurchaseRecordInput');
        }

        $request->session()->put(['purchasingItemPurchaseRecordInput' => $purchasingItemPurchaseRecordInput->id]);

        return redirect()->back();
    }

    public function purchaseRecordInputProcess(Request $request)
    {
        // Start database transaction
        DB::beginTransaction();
        
        try {
            $arrivalDate = Carbon::parse($request->arrival_date);
            request()->merge(['arrival_date' => $arrivalDate]);
            
            // Create a new purchase record
            $purchaseRecordInputProcess = new  PurchaseRecord();
            $purchaseRecordData = $purchaseRecordInputProcess->createPurchaseRecord($request);

            // Remove any existing session data and store the new purchase record ID
            if ($request->session()->has('purchaseRecordInputProcess')) {
                $request->session()->forget('purchaseRecordInputProcess');
            }

            // Store its ID in session
            $request->session()->put(['purchaseRecordInputProcess' => $purchaseRecordData->id]);
            
        } catch (\Exception $e) {
            // Rollback the transaction if there is an error
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to create purchase record: ' . $e->getMessage());
        }

        // Commit the transaction if everything is successful
        DB::commit();
        
        return redirect()->back()->with('success', '購買品 購入実績の登録が完了しました');
    }

    public function purchasingItemPurchaseRecordInputEditProcess(Request $request, PurchaseRecord $purchaseRecord)
    {
        $purchaseRecord->update(
            [
                'voucher_class' => $request->voucher_class,
                'slip_type' => $request->slip_type,
                'arrival_date' => Carbon::createFromFormat('Ymd', $request->arrival_date),
                'supplier_code' => $request->supplier_code,
                'machine_number' => $request->machine_number,
                'department_code' => $request->department_code,
                'line_code' => $request->line_code,
                'expense_item' => $request->item_code,
                'part_number' => $request->product_number_number ?? '',
                'product_name' => $request->product_number_name,
                'standard' => $request->standard,
                'where_used_code' => $request->where_to_use_code,
                'quantity' => $request->quantity,
                'unit_code' => $request->unit_code,
                'unit_price' => $request->unit_price,
                'amount_of_money' => $request->amount,
                'slip_no' => $request->slip_no,
                'tax_classification' => $request->tax_classification,
                'project_number' => $request->project_code,
                'remarks' => $request->remarks,
                'purchase_category' => 1,
            ]
        );

        if ($request->session()->has('purchaseRecordInputProcess')) {
            $request->session()->forget('purchaseRecordInputProcess');
        }

        $request->session()->put(['purchaseRecordInputProcess' => $purchaseRecord->id]);

        return redirect()->back();
    }

    public function purchaseRecordInputEditProcess(Request $request, PurchaseRecord $id)
    {
        $id->update(
            [
                'voucher_class' => $request->voucher_class,
                'slip_type' => 1,
                'arrival_date' => $request->due_date_start,
                'supplier_code' => $request->supplier_code,
                'machine_number' => $request->machine_number,
                'department_code' => $request->department_code,
                'line_code' => $request->line_code,
                'expense_item' => $request->item_code,
                'part_number' => $request->product_number_number,
                'product_name' => $request->product_name,
                'standard' => $request->standard,
                'quantity' => $request->quantity,
                'unit_code' => $request->unit_code,
                'unit_price' => $request->unit_price,
                'tax_classification' => $request->tax_classification,
                'project_number' => $request->project_code,
                'remarks' => $request->remarks,
                'purchase_category' => 2,
                'where_used_code' => $request->where_used_code,
            ]
        );

        return redirect()->back();
    }


    

    public function purchaseHistoryExport(Request $request)
    {
        $datas = [];
        // Check if there are any query parameters and apply filtering if present
        if ($request->query()) {
            $datas = PurchaseRecord::search($request)->get();
        }
        // Generate the file name with the current date
        $fileName = '購入実績検索・一覧-'.now()->format('Ymd').'.xlsx';
        // Export the data using the specified export class and file name
        return Excel::download(
            export: new PurchaseHistoryExport( datas: $datas), 
            fileName: $fileName);
    }   

    public function deletePurchaseHistory($id){
        // Start database transaction
        DB::beginTransaction();
        try {
            $purchaseRecord = new PurchaseRecord();
            $purchaseRecord->removePurchaseHistory($id);
            
        } catch (Exception $e) {
            // Rollback the transaction if there is an error
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to create purchase record: ' . $e->getMessage());
        }
        // Commit the transaction if everything is successful
        DB::commit();
        return back();
    }

    public function purchaseRequisitionInputUpdate(Request $request, $id){
        // Start the transaction
        DB::beginTransaction();

        try {
            // Instantiate the PurchaseRequisition model and update the requisition record
            $purchaseRequisition = new PurchaseRequisition();
            $purchaseRequisition->updatePurchaseRequisition($id, $request);

        } catch (\Exception $e) {
            // If there was an error, roll back the transaction
            DB::rollBack();
            // Return an error message
            return redirect()->back()->with('error', 'There was an issue with the transaction. Please try again.');
        }
        // Commit the transaction if everything is successful
        DB::commit();
        // Return success message
        return redirect()->back()->with('success', config("messages.success_message"));
    }

    
    public function purchaseRequisitionApprovalSearchExport(Request $request)
    {
        $purchaseRequisitions = [];
        if(count($request->query()) > 0){
            if (($request->purpose ?? 1) == 1 || ($request->purpose ?? 1) == 2) {
                $purchaseRequisitions = PurchaseRequisition::whereHas('approvals')->whereHas('unfinishedApprovals')->search($request)->paginate(20);
            } else {
                $purchaseRequisitions = PurchaseRequisition::whereHas('approvals')->whereHas('approvedApprovals')->search($request)->paginate(20);
            }
        }

        $fileName = '購買依頼(承認)検索・一覧'.now()->format('Ymd').'.xlsx';
        return Excel::download(
            export: new PurchaseRequisitionApprovalSearchExport(purchaseRequisitions: $purchaseRequisitions), 
            fileName: $fileName);
    }

    public function purchaseRequisitionApprovalProcess(Request $request)
    {
        $update_query = [];
        if ($request->approval_type == "unapprove") {
            $update_query = [
                "approval_date" => null,
                "denial_date" => null,
            ];
            $message = "承認取消処理が完了しました";
        } elseif ($request->approval_type == "approve") {
            $update_query = [
                "approval_date" => date('Y-m-d H:i:s'),
                "denial_date" => null
            ];
            $message = "承認処理が完了しました";
        }
        DB::beginTransaction();

        try{
            if ($update_query != []) {
                foreach ($request->requisitionNumbers ?? [] as $requisitionNumber) {
                    $purchaseRequisition = PurchaseRequisition::where("requisition_number", $requisitionNumber)->first();
                    $employee = Employee::where('employee_code' , request()->user()->employee_code)->first();
                    $approval = PurchaseApproval::where('purchase_record_no', $requisitionNumber)
                                ->where('approver_employee_code', $employee->employee_code)
                                ->first();
    
                    if($approval && $purchaseRequisition && $employee){
                        if($request->approval_type == "unapprove"){
                            $nextApproval = $approval->nextApproval();
                            if($nextApproval?->approval_date == null){
                                $approval->update($update_query);
                            }else{
                                continue;
                            }
                        }else{
                            $approval->update($update_query);
                            // Mark 1:approving | STATE_CLASSIFICATION reference
                            $purchaseRequisition->update(['state_classification' => 1]);
                        }
    
                        if(method_exists($purchaseRequisition,'assignNextApprover')){
                            $purchaseRequisition->assignNextApprover($employee);
                        }
                    }
                    
                }
            }

            DB::commit();

            // Send email notification the next approver
            if(in_array($purchaseRequisition->state_classification, ['0','1'])) {
                $purchaseNotificationData = $purchaseRequisition;
                $this->EmailNotification->purchaseNotification($request, $purchaseNotificationData);
            }

            return back()->with('success', $message);
        } catch (Exception $exception){
            DB::rollBack();
            return back()->with('error', $exception->getMessage());
        }
    }


    public function approvalRouteSetting()
    {
        $codes = Code::where('division', '単位')->get();
        return view('pages.purchases.approval-route-setting', [
            'codes' => $codes
        ]);
    }

    public function approvalRouteSettingStore(Request $request)
    {
        $arrivalDate = Carbon::parse($request->arrival_date);
        $currentYearMonth = now()->format('ym');
        $latestPurchaseRecordNumber = PurchaseRecord::where('purchase_record_no', 'like', "{$currentYearMonth}%")->max('purchase_record_no');

        if ($latestPurchaseRecordNumber) {
            $count = intval(substr($latestPurchaseRecordNumber, 4, 6)) + 1;
            $purchaseRecordNumber = $currentYearMonth . str_pad($count, 6, '0', STR_PAD_LEFT);
        } else {
            $purchaseRecordNumber = $currentYearMonth . '000001';
        }

        $approvalRouteSetting = PurchaseRecord::create([
            'purchase_record_no' => $purchaseRecordNumber,
            'voucher_class' => $request->voucher_class,
            'slip_type' => $request->slip_type,
            'arrival_date' => $request->due_date_start,
            'slip_type' => 1,
            'arrival_date' => $arrivalDate,
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
            'purchase_category' => 1,
            'where_used_code' => $request->where_used_code,
            'purchase_category' => 2,
            'where_used_code' => $request->where_to_use_code,
            'amount_of_money' => $request->amount,
            'slip_no' => $request->slip_code,
            'unit_code' => $request->unit_code,
        ]);

        if ($request->session()->has('approvalRouteSetting')) {
            $request->session()->forget('approvalRouteSetting');
        }

        $request->session()->put(['approvalRouteSetting' => $approvalRouteSetting->id]);

        return redirect()->back();
    }


    
    //Purchase 77
    public function orderDataList(Request $request)
    {
        if (count($request->query()) > 0)
        {
            RequestHelper::processRequest($request);
            $orderDateFrom = $request->order_date_from;
            $orderDateTo = $request->order_date_to;
            $arrivalDateFrom = $request->arrival_date_from;
            $arrivalDateTo = $request->arrival_date_to;
            $dueDateFrom = $request->due_date_from;
            $dueDateTo = $request->due_date_to;
            $supplierCodeFrom = $request->supplier_code_from;
            $supplierCodeTo = $request->supplier_code_to;
            $departmentCodeFrom = $request->department_code_from;
            $departmentCodeTo = $request->department_code_to;
            $lineCodeFrom = $request->line_code_from;
            $lineCodeTo = $request->line_code_to;
            $machineCodeFrom = $request->machine_code_from;
            $machineCodeTo = $request->machine_code_to;
            $expenseItemFrom = $request->expense_item_from;
            $expenseItemTo = $request->expense_item_to;
            $requestor = $request->requestor;
            $orderFormNo = $request->order_form_no;
            $purchaseRequisitionNo = $request->purchase_requisition_no;
            $slipNo = $request->slip_no;
            $partNumber = $request->part_number;
            $productName = $request->requisition_product_name;
            $standard = $request->standard;

            // Subquery to calculate the sum of arrival_quantity grouped by purchase_order_no and purchase_order_details_number
            $purchaseArrivalsSum = DB::table('purchase_arrivals')
                ->select('purchase_order_no', 'purchase_order_details_no', DB::raw('SUM(arrival_quantity) as total_arrival_quantity'), 'arrival_day')
                ->groupBy('purchase_order_no', 'purchase_order_details_no');

             // Subquery to calculate the sum of arrival_quantity grouped by purchase_order_no and purchase_order_details_number
            $purchaseRecordsSum = DB::table('purchase_records')
             ->select('purchase_record_no', 'purchase_records.part_number', DB::raw('SUM(quantity) as total_record_quantity'), 'arrival_date as record_arrival_date')
             ->groupBy('purchase_record_no', 'purchase_records.part_number');

            // Main query with the subquery join
            $query = PurchaseRequisition::query()
                ->with(['line', 'department', 'product', 'supplier'])
                ->whereDateRangeOrEqual('order_date', $orderDateFrom, $orderDateTo)
                ->whereDateRangeOrEqual('arrival_day', $arrivalDateFrom, $arrivalDateTo)
                ->whereDateRangeOrEqual('deadline', $dueDateFrom, $dueDateTo)
                ->whereColumnBetweenOrEqual('supplier_code', $supplierCodeFrom, $supplierCodeTo)
                ->whereColumnBetweenOrEqual('department_code', $departmentCodeFrom, $departmentCodeTo)
                ->whereColumnBetweenOrEqual('line_code', $lineCodeFrom, $lineCodeTo)
                ->whereColumnBetweenOrEqual('machine_number', $machineCodeFrom, $machineCodeTo)
                ->whereColumnBetweenOrEqual('expense_items', $expenseItemFrom, $expenseItemTo)
                ->when($requestor, function ($query) use ($requestor) {
                    return $query->where('creator', $requestor);
                })
                ->when($partNumber, function ($query) use ($partNumber) {
                    return $query->where('purchase_requisitions.part_number', $partNumber);
                })
                ->when($productName, function ($query) use ($productName) {
                    return $query->where('product_name', $productName);
                })
                ->when($orderFormNo, function ($query) use ($orderFormNo) {
                    return $query->where('purchase_order_number', $orderFormNo);
                })
                ->when($standard, function ($query) use ($standard) {
                    return $query->where('standard', $standard);
                })
                ->when($purchaseRequisitionNo, function ($query) use ($purchaseRequisitionNo) {
                    return $query->where('requisition_number', $purchaseRequisitionNo);
                })
                ->when($slipNo, function ($query) use ($slipNo) {
                    return $query->whereHas('arrival', function ($query) use ($slipNo) {
                        $query->where('slip_no', $slipNo);
                    });
                })
                ->leftJoinSub($purchaseArrivalsSum, 'arrivals', function ($join) {
                    $join->on('purchase_requisitions.purchase_order_number', '=', 'arrivals.purchase_order_no')
                        ->on('purchase_requisitions.purchase_order_details_number', '=', 'arrivals.purchase_order_details_no');
                })
                ->leftJoinSub($purchaseRecordsSum, 'records', function ($join) {
                    $join->on('purchase_requisitions.purchase_order_number', '=', 'records.purchase_record_no')
                        ->on('purchase_requisitions.part_number', '=', 'records.part_number');
                })
                ->addSelect('purchase_requisitions.*', 'arrivals.total_arrival_quantity', 'records.total_record_quantity');
            $items = $query;

            if ($request->status == "non-stock") {
                $items = $items->doesntHave('arrival');
            } elseif ($request->status == "in-stock") {
                $items = $items->whereHas('arrival', function ($query) {
                    $query->whereColumn('arrival_quantity', '<', 'purchase_requisitions.quantity');
                });
            } elseif ($request->status == "arrive-stock") {
                $items = $items->whereHas('arrival', function ($query) {
                    $query->whereColumn('arrival_quantity', '=', 'purchase_requisitions.quantity');
                });
            }
            
            if ($request->acceptance == "incomplete") {
                $items = $items->whereHas('record', function ($query) {
                    $query->whereColumn('purchase_records.quantity', '<', 'purchase_requisitions.quantity');
                });
            } elseif ($request->acceptance == "complete") {
                $items = $items->whereHas('record', function ($query) {
                    $query->whereColumn('purchase_records.quantity', '=', 'purchase_requisitions.quantity');
                });
            }
            
            $items = $items->paginate(20);
            
            $sumAmountMoney = $items->sum('amount_of_money');
            return view('pages.purchases.order.index', [
                        'items' => $items,
                        'amount_of_money' => $sumAmountMoney
                    ]);
        }else{
            $items = [];
            $sumAmountMoney = 0;
            return view('pages.purchases.order.index', [
                        'items' => $items,
                        'amount_of_money' => $sumAmountMoney
                    ]);
        }
    }

    //Purchase 77 export
    public  function orderDataListExport(Request $request)
    {
        RequestHelper::processRequest($request);
        $orderDateFrom = $request->order_date_from;
        $orderDateTo = $request->order_date_to;
        $arrivalDateFrom = $request->arrival_date_from;
        $arrivalDateTo = $request->arrival_date_to;
        $dueDateFrom = $request->due_date_from;
        $dueDateTo = $request->due_date_to;
        $supplierCodeFrom = $request->supplier_code_from;
        $supplierCodeTo = $request->supplier_code_to;
        $departmentCodeFrom = $request->department_code_from;
        $departmentCodeTo = $request->department_code_to;
        $lineCodeFrom = $request->line_code_from;
        $lineCodeTo = $request->line_code_to;
        $machineCodeFrom = $request->machine_code_from;
        $machineCodeTo = $request->machine_code_to;
        $expenseItemFrom = $request->expense_item_from;
        $expenseItemTo = $request->expense_item_to;
        $requestor = $request->requestor;
        $orderFormNo = $request->order_form_no;
        $purchaseRequisitionNo = $request->purchase_requisition_no;
        $slipNo = $request->slip_no;
        $partNumber = $request->part_number;
        $productName = $request->requisition_product_name;
        $standard = $request->standard;

        // Subquery to calculate the sum of arrival_quantity grouped by purchase_order_no and purchase_order_details_number
        $purchaseArrivalsSum = DB::table('purchase_arrivals')
            ->select('purchase_order_no', 'purchase_order_details_no', DB::raw('SUM(arrival_quantity) as total_arrival_quantity'), 'arrival_day')
            ->groupBy('purchase_order_no', 'purchase_order_details_no');

         // Subquery to calculate the sum of arrival_quantity grouped by purchase_order_no and purchase_order_details_number
        $purchaseRecordsSum = DB::table('purchase_records')
         ->select('purchase_record_no', 'purchase_records.part_number', DB::raw('SUM(quantity) as total_record_quantity'), 'arrival_date as record_arrival_date')
         ->groupBy('purchase_record_no', 'purchase_records.part_number');

        // Main query with the subquery join
        $query = PurchaseRequisition::query()
            ->with(['line', 'department', 'product', 'supplier'])
            ->whereDateRangeOrEqual('order_date', $orderDateFrom, $orderDateTo)
            ->whereDateRangeOrEqual('arrival_day', $arrivalDateFrom, $arrivalDateTo)
            ->whereDateRangeOrEqual('deadline', $dueDateFrom, $dueDateTo)
            ->whereColumnBetweenOrEqual('supplier_code', $supplierCodeFrom, $supplierCodeTo)
            ->whereColumnBetweenOrEqual('department_code', $departmentCodeFrom, $departmentCodeTo)
            ->whereColumnBetweenOrEqual('line_code', $lineCodeFrom, $lineCodeTo)
            ->whereColumnBetweenOrEqual('machine_number', $machineCodeFrom, $machineCodeTo)
            ->whereColumnBetweenOrEqual('expense_items', $expenseItemFrom, $expenseItemTo)
            ->when($requestor, function ($query) use ($requestor) {
                return $query->where('creator', $requestor);
            })
            ->when($partNumber, function ($query) use ($partNumber) {
                return $query->where('purchase_requisitions.part_number', $partNumber);
            })
            ->when($productName, function ($query) use ($productName) {
                return $query->where('product_name', $productName);
            })
            ->when($orderFormNo, function ($query) use ($orderFormNo) {
                return $query->where('purchase_order_number', $orderFormNo);
            })
            ->when($standard, function ($query) use ($standard) {
                return $query->where('standard', $standard);
            })
            ->when($purchaseRequisitionNo, function ($query) use ($purchaseRequisitionNo) {
                return $query->where('requisition_number', $purchaseRequisitionNo);
            })
            ->when($slipNo, function ($query) use ($slipNo) {
                return $query->whereHas('arrival', function ($query) use ($slipNo) {
                    $query->where('slip_no', $slipNo);
                });
            })
            ->leftJoinSub($purchaseArrivalsSum, 'arrivals', function ($join) {
                $join->on('purchase_requisitions.purchase_order_number', '=', 'arrivals.purchase_order_no')
                    ->on('purchase_requisitions.purchase_order_details_number', '=', 'arrivals.purchase_order_details_no');
            })
            ->leftJoinSub($purchaseRecordsSum, 'records', function ($join) {
                $join->on('purchase_requisitions.purchase_order_number', '=', 'records.purchase_record_no')
                    ->on('purchase_requisitions.part_number', '=', 'records.part_number');
            })
            ->addSelect('purchase_requisitions.*', 'arrivals.total_arrival_quantity', 'records.total_record_quantity');
        $items = $query;

        if ($request->status == "non-stock") {
            $items = $items->doesntHave('arrival');
        } elseif ($request->status == "in-stock") {
            $items = $items->whereHas('arrival', function ($query) {
                $query->whereColumn('arrival_quantity', '<', 'purchase_requisitions.quantity');
            });
        } elseif ($request->status == "arrive-stock") {
            $items = $items->whereHas('arrival', function ($query) {
                $query->whereColumn('arrival_quantity', '=', 'purchase_requisitions.quantity');
            });
        }
        
        if ($request->acceptance == "incomplete") {
            $items = $items->whereHas('record', function ($query) {
                $query->whereColumn('purchase_records.quantity', '<', 'purchase_requisitions.quantity');
            });
        } elseif ($request->acceptance == "complete") {
            $items = $items->whereHas('record', function ($query) {
                $query->whereColumn('purchase_records.quantity', '=', 'purchase_requisitions.quantity');
            });
        }
        
        $items = $items->get();

        $fileName = '注文データ一覧_'.now()->format('Ymd').'.xlsx';
        return Excel::download(new PurchaseOrderListExport($items), $fileName , \Maatwebsite\Excel\Excel::XLSX);
    }

    //Purchase 78
    public function purchaseOrderInput($id)
    {
        $units = Code::selectRaw('
                            division,
                            code,
                            name    
                        ')
                        ->whereDivision('単位')
                        ->get();

        $item = PurchaseRequisition::findOrFail($id);

        return view('pages.purchases.order.show', [
                'units' => $units,
                'item' => $item,
            ]);
    } 
    
    //Purchase 78 update
    public function purchaseOrderUpdate(Request $request, $id)
    {
        $item = PurchaseRequisition::findOrFail($id);
        $request['deadline'] =  Carbon::parse($request['deadline'])->format('Y-m-d');
        $item->update($request->except('_token'));
        return back()->with('success', 'データは正常に登録されました');
    }

    //Purchase 79 Arrival inputs save
    public function purchaseArrivalStore(Request $request)
    {
        $orderNumber = $request->purchase_order_no;
        $orderDetailNumber = $request->purchase_order_details_no;
        $arrivalDay = $request->input('arrival_day');

        $latest = PurchaseArrival::where('purchase_order_no', $orderNumber)
                            ->where('purchase_order_details_no', $orderDetailNumber)
                            ->first();

        $newPurchaseRecordNo = '';

        if ($latest) {
            $branchNo = $latest->branch_number + 1;
            $lastSixDigits = intval(substr($latest->purchase_record_no, -6)) + 1;
            $newPurchaseRecordNo = sprintf('%04d%06d', date('ym'), $lastSixDigits);
        } else {
            $branchNo = 1;
            $newPurchaseRecordNo = date('ym') . '000001';
        }

        $purchaseArrival = PurchaseArrival::create([
                                'purchase_order_no' => $request->get('purchase_order_no'),
                                'purchase_order_details_no' => $request->get('purchase_order_details_no'),
                                'branch_number' => $branchNo,
                                'arrival_day'   =>  Carbon::parse($arrivalDay),
                                'arrival_quantity'  => $request->get('arrival_quantity'),
                                'slip_no'   => $request->get('slip_no'),
                                'unable_to_resharpen_flag' => $request->get('unable_to_resharpen_flag'),
                                'remarks'   => $request->get('remarks'),
                                'purchase_record_no'    => $newPurchaseRecordNo
                            ]);
        
        return  back()->with('success', 'データは正常に登録されました');
    }

    //Purchase 80
    

    public function getApprovalRouteList(Request $request)
    {
        $data = PurchaseApprovalRoute::orderBy('display_order', 'ASC')
                        ->when($request->employee_code, fn($query) => $query->where("employee_code", $request->employee_code))
                        ->withCount('details')->get();
        return response()->json(['data' => $data]);
    }

    public function deleteApprovalRoute($id)
    {
        // Start database transaction
        DB::beginTransaction();
        
        try {
            // Instantiate the model
            $purchaseApprovalRoute = new PurchaseApprovalRoute();

            // Delete related details and the approval route itself
            $purchaseApprovalRoute->deleteApprovalRoute($id);
           
        } catch (\Exception $e) {
            // Rollback the transaction if an error occurs
            DB::rollback();
            return response()->json(['status' => false, 'error' => $e->getMessage()]);
        }

        // Commit the transaction
        DB::commit();
        
        return response()->json(['data' => '']);
    }


    private function resequenceDisplayOrderPurchaseApprovalRoute($employee_code) {
        if($employee_code) {
            $purchangeApprovalRoute = PurchaseApprovalRoute::orderBy('display_order', 'ASC')
                                            ->where('employee_code', $employee_code)->get();
            foreach ($purchangeApprovalRoute as $index => $route) {
                $route->display_order = $index + 1;
                $route->save();
            }
        }
        
    }
    public function deleteApprovalRouteDetail($id)
    {
        $purchangeApprovalRouteDetail = PurchaseApprovalRouteDetail::find($id);
        $purchangeApprovalRouteDetail->delete();
        return response()->json(['data' => '']);
    }

    public function getApprovalRouteDetails(PurchaseApprovalRoute $id)
    {
        // $details = $id->details->with('employee')->get();
        $details = Fractal::collection($id->details()->orderBy('order_of_approval', 'ASC')->get())->transformWith(new PurchaseApprovalRouteDetailTransformer)->parseIncludes(['employee'])->toArray();
        // $details = new Collection($id->details(), new PurchaseApprovalRouteDetailTransformer)->toArray();
        
        // $data = Fractal::collection($id->get())->transformWith(new PurchaseApprovalRouteTransformer)->toArray();
        return response()->json(['data' => $id, 'details' => $details]);
    }

    public function saveApprovalRoute(Request $request)
    {
        // Start database transaction
        DB::beginTransaction();

        try {
            // Instantiate the models
            $purchaseApprovalRoute = new PurchaseApprovalRoute();
            $purchaseApprovalRouteDetail = new PurchaseApprovalRouteDetail();

            // Remove null values from the request values
            $request->values = array_values(array_filter($request->values));
            // Create a new approval route
            $approval_route = $purchaseApprovalRoute->createApprovalRoute($request);
            // Create approval route details for each approver
            foreach ($request->values ?? [] as $value) {
                $purchaseApprovalRouteDetail->createApprovalRouteDetail($approval_route, $value, $request, true);
            }
        } catch (\Exception $e) {
            // Rollback the transaction in case of an error
            DB::rollBack();
            return response()->json(['status' => false, 'error' => $e->getMessage()]);
        }

        // Commit the transaction
        DB::commit();
        return response()->json(['status' => true]);
    }

    public function updateApprovalRoute(Request $request)
    {
        // Start database transaction
        DB::beginTransaction();
    
        try {
            // Instantiate the models
            $purchaseApprovalRoute = new PurchaseApprovalRoute();
            $purchaseApprovalRouteDetail = new PurchaseApprovalRouteDetail();

            // Update the approval route
            $approval_route = $purchaseApprovalRoute->updateApprovalRoute($request);

            // Insert new approval route details
            foreach ($request->values ?? [] as $value) {
                $purchaseApprovalRouteDetail->createApprovalRouteDetail($approval_route, $value, $request);
            }
        } catch (\Exception $e) {
            // Rollback the transaction in case of an error
            DB::rollBack();
            return response()->json(['status' => false, 'error' => $e->getMessage()]);
        }

        // Commit the transaction
        DB::commit();

        return response()->json(['status' => true]);
          
    }

    public function reorderApprovalRoute(Request $request)
    {
        $approval_route = PurchaseApprovalRoute::where('id', $request->id)->first();

        if ($request->type == 'down') {
            PurchaseApprovalRoute::where('display_order', ($approval_route->display_order + 1))->update([
                'display_order' => $approval_route->display_order,
            ]);
    
            $approval_route->update([
                'display_order' => ($approval_route->display_order + 1),
            ]);
        } else {
            if ($approval_route->display_order > 1) {
                PurchaseApprovalRoute::where('display_order', ($approval_route->display_order - 1))->update([
                    'display_order' => $approval_route->display_order,
                ]);
        
                $approval_route->update([
                    'display_order' => ($approval_route->display_order - 1),
                ]);
            }
        }
        
        return response()->json(['status' => true]);
    }
    public function orderProcessing(Request $request){
        $items = !$request->query() 
                    ? []
                    : PurchaseRequisition::query()
                        ->with(['line', 'department', 'product', 'supplier', 'unit'])
                        ->when($request->supplier_code, 
                            fn($query) => $query->where("supplier_code", $request->supplier_code))
                        ->whereDateRangeOrEqual('requested_date', $request->request_date_from, $request->request_date_to)
                        ->when($request->department_code, 
                            fn($query) => $query->where("department_code", $request->department_code))
                        ->whereDateRangeOrEqual('deadline', $request->deadline_date_from, $request->deadline_date_to)
                        ->when($request->employee_code, 
                            fn($query) => $query->where("creator", $request->employee_code))
                        ->when($request->approval_method_category, function($query) use ($request){
                            if($request->approval_method_category != "all"){
                                $query->where("approval_method_category", $request->approval_method_category);
                            }
                        })
                        ->when($request->line_code, 
                            fn($query) => $query->where("line_code", $request->line_code))
                        ->when($request->state_classification != "all", 
                            fn($query) => $query->where("state_classification", $request->state_classification))
                        ->when($request->requisition_number, 
                            fn($query) => $query->where("requisition_number", $request->requisition_number))
                        ->when($request->forced_order, 
                            fn($query) 
                                => $query
                                        ->where("approval_method_category", 1))
                                        ->whereIn('state_classification', [0,1])
                        ->whereNotIn("state_classification",[3,4,9])
                        ->paginate(20);

        return view("pages.purchases.order-processing", [
            'items' => $items
        ]);
    }

    public  function orderProcessingExport(Request $request)
    {
        $query = !$request->query() 
                ? []
                : PurchaseRequisition::query()
                ->with(['line', 'department', 'product', 'supplier', 'unit'])
                ->when($request->supplier_code, 
                    fn($query) => $query->where("supplier_code", $request->supplier_code))
                ->whereDateRangeOrEqual('requested_date', $request->request_date_from, $request->request_date_to)
                ->when($request->department_code, 
                    fn($query) => $query->where("department_code", $request->department_code))
                ->whereDateRangeOrEqual('deadline', $request->deadline_date_from, $request->deadline_date_to)
                ->when($request->employee_code, 
                    fn($query) => $query->where("creator", $request->employee_code))
                ->when($request->approval_method_category, function($query) use ($request){
                    if($request->approval_method_category != "all"){
                        $query->where("approval_method_category", $request->approval_method_category);
                    }
                })
                ->when($request->line_code, 
                    fn($query) => $query->where("line_code", $request->line_code))
                ->when($request->state_classification != "all", 
                    fn($query) => $query->where("state_classification", $request->state_classification))
                ->when($request->requisition_number, 
                    fn($query) => $query->where("requisition_number", $request->requisition_number))
                ->when($request->forced_order, 
                    fn($query) 
                        => $query
                                ->where("approval_method_category", 1))
                                ->whereIn('state_classification', [0,1])
                ->whereNotIn("state_classification",[3,4,9])
                ->get();

            $fileName = 'purchase-ordering-'.now()->format('Ymd').'.xlsx';
            return Excel::download(
                export: new PurchaseOrderProcessingExport(purchaseRequisitions: $query), 
                fileName: $fileName);
    }

    public function orderProcessingDetail(PurchaseRequisition $purchaseRequisition){
        $codes = Code::where('division', '単位')->get();
        $purchaseRequisition->load(['department', 'line']);
        return view("pages.purchases.purchase-requisition-update", [
            'codes' => $codes,
            'purchaseRequisition' => $purchaseRequisition
        ]);
    }

    public function orderProcessingUpdate(PurchaseRequisition $purchaseRequisition, Request $request){
        $purchaseRequisition->update([
            'requisition_number' => $request->requisition_number,
            'requested_date' => $request->requested_date,
            'supplier_code' => $request->supplier_code,
            'department_code' => $request->department_code,
            'line_code' => $request->line_code,
            'part_number' => $request->part_number,
            'product_name' => $request->product_name,
            'reason' => $request->reason,
            'quantity' => $request->quantity,
            'unit_code' => $request->unit_code,
            'unit_price' => $request->unit_price,
            'amount_of_money' => $request->amount_of_money,
            'expense_items' => $request->expense_item_code,
            'deadline' => $request->deadline,
            'approval_method_category' => $request->approval_method_category,
            'approval_route_number' => $request->approval,
            'remarks' => $request->remarks,
            'state_classification' => 1,
            'standard' => $request->standard,
            'quotation_existence_flag' => $request->quotation_existence_flag,
            'subsidy_items' => $request->subsidy_items
        ]);

        return  back()->with('success', 'データは正常に登録されました');
    }
    public function orderProcessingDelete(PurchaseRequisition $purchaseRequisition){
        $purchaseRequisition->approvals()->delete();
        $purchaseRequisition->delete();
        return redirect()->route("purchase.order.processing");
    }
}