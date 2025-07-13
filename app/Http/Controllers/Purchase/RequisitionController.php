<?php

namespace App\Http\Controllers\Purchase;

use Exception;
use Carbon\Carbon;
use App\Constants\Constant;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\PurchaseRequisition\PurchaseRequisitionRequest;
use App\Http\Requests\PurchaseRequisition\SearchPurchaseRequisitionsRequest;

//Models
use App\Models\Code;
use App\Models\Customer;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Item;
use App\Models\Line;
use App\Models\PurchaseRecord;
use App\Models\PurchaseRequisition;

//Services
use App\Services\Purchase\PurchaseRequisitionService;

//Exports

use App\Exports\PurchaseRecordExport;
use App\Exports\PurchaseRequisitionSearchExport;

class RequisitionController extends Controller
{
  protected $purchaseRequisitionService;
  protected $emailNotification;
  
  public function __construct(PurchaseRequisitionService $purchaseRequisitionService) {
    $this->purchaseRequisitionService = $purchaseRequisitionService;
  }

  public function index(SearchPurchaseRequisitionsRequest $request){
    // Get validated data from the request
    $filters = $request->validated();
    // Use the model's search method to fetch filtered data
    $datas = PurchaseRequisition::search($filters);
    $approvalMethods = Constant::APPROVAL_METHOD_CATEGORY;
    $stateClasifications = Constant::STATE_CLASSIFICATION;

    return view('pages.purchases.requisition.index', compact('datas', 'approvalMethods', 'stateClasifications'));
  }

  public function create(Request $request)
  {
    $data = null;
    $duplicate_flag = false;

    $approvalMethods = Constant::APPROVAL_METHOD_CATEGORY;
    $quotationExistenceFlags = Constant::QUOTATION_EXISTENCE_FLAG;
    $codes = Code::where('division', '単位')->get();

    // Check if session has 'PurchaseRequisition' and validate it
    if($id = $request->get('id')){
      $duplicate_flag = true;
      $data = PurchaseRequisition::findOrFail($id);
    }

    return view('pages.purchases.requisition.create', compact('codes', 'approvalMethods', 'quotationExistenceFlags', 'data', 'duplicate_flag'));
  }

  public function copy_previous_input(){
    $employee_code = auth()->user()->employee_code;
    $data = PurchaseRequisition::where('creator', $employee_code)->orderBy('created_at', 'desc')->first();

    $approvalMethods = Constant::APPROVAL_METHOD_CATEGORY;
    $quotationExistenceFlags = Constant::QUOTATION_EXISTENCE_FLAG;
    $codes = Code::where('division', '単位')->get();
    $duplicate_flag = false;

    return view('pages.purchases.requisition.create', compact('codes', 'approvalMethods', 'quotationExistenceFlags', 'data', 'duplicate_flag'));
  }

  public function store(PurchaseRequisitionRequest $request){
    // Start a transaction
    DB::beginTransaction();

    try {
      // Call the service to create the requisition
      $purchaseRequisition = $this->purchaseRequisitionService->create($request);

      // Commit the transaction
      DB::commit();

      // Send email notification for the next approver
      // $this->purchaseRequisitionService->send_email_to_next_approver($purchaseRequisition);

      $message = "購買依頼の登録が完了しました";
      return redirect()->route('purchase.requisition.create')->with('success', $message);

    } 
    catch (Exception $e) {
      // Rollback the transaction if something went wrong
      DB::rollBack();

      // Log the error with detailed information
      Log::error('Error occurred while creating purchase requisition', [
        'error' => $e->getMessage(),
        'request_data' => $request->all(),
        'timestamp' => now(),
      ]);

      // Handle the error, log it or display a custom error message
      return redirect()->back()->with('error', 'An error occurred while creating the requisition.');
    }
  }

  public function edit(Request $request, $id){
    $purchaseRequisition = $this->purchaseRequisitionService->edit($id);
    $approvalMethods = Constant::APPROVAL_METHOD_CATEGORY;
    $quotationExistenceFlags = Constant::QUOTATION_EXISTENCE_FLAG;
    $codes = Code::where('division', '単位')->get();
    $requestData = $request->all();

    return view('pages.purchases.requisition.edit', compact(
      'codes',
      'approvalMethods',
      'quotationExistenceFlags',
      'purchaseRequisition',
      'requestData'
    ));
  }

  public function update(PurchaseRequisitionRequest $request)
  {
    // Start a transaction
    DB::beginTransaction();
    try {
      $purchaseRequisition = $this->purchaseRequisitionService->update($request);

      // Commit the transaction
      DB::commit();
      $message = "購買依頼の更新が完了しました";

      return redirect()->back()->with('success', $message);
    } catch (\Exception $e) {
      // Rollback the transaction if something went wrong
      DB::rollBack();

      // Log the error with detailed information
      Log::error('Error occurred while updating purchase requisition', [
        'error' => $e->getMessage(),
        'request_data' => $request->all(),
        'timestamp' => now(),
      ]);

      // Handle the error, log it or display a custom error message
      return redirect()->back()->with('error', 'An error occurred while updating the requisition.');
    }
  }

  /**
   * Export the search results of purchase requisitions to an Excel file.
   *
   * @param \Illuminate\Http\Request $request The incoming request containing search filters.
   * @return \Symfony\Component\HttpFoundation\BinaryFileResponse The response containing the Excel file download.
   */
  public function purchaseRequisitionSearchExport(Request $request)
  {
    $filters = $request->all();

    // Apply pagination and get only the current page data
    $datas = PurchaseRequisition::search($filters, true);
    $perPage = $request->per_page ?? 20;
    $page = $request->page ?? 1;
    $datas = $datas->slice(($page - 1) * $perPage, $perPage)->values();

    $fileName = '購買依頼一覧-' . now()->format('Ymd') . '.xlsx';

    return Excel::download(
      export: new PurchaseRequisitionSearchExport(purchaseRequisitions: $datas),
      fileName: $fileName
    );
  }


  public function requisitionContentExport(Request $request, $id)
  {
    $type = $request->query('type', 'xlsx');
    
    if (!in_array($type, ['pdf', 'xlsx'], true)) {
        throw new \InvalidArgumentException('Invalid export type. Supported types are: pdf, xlsx');
    }

    $requisition  = PurchaseRequisition::find($id);
    $department   = Department::where('code',$requisition->department_code)->value('name') ?? '';
    $employee     = Employee::where('employee_code',$requisition->creator)->value('employee_name') ?? '';
    $customer     = Customer::where('customer_code',$requisition->supplier_code)->value('customer_name') ?? '';
    $line         = Line::where('line_code',$requisition->line_code)->value('line_name') ?? '';
    $code         = Code::where('code',$requisition->unit_code)
                    ->where('division',"単位")
                    ->value('name') ?? '';
    $item         = Item::where('expense_item',$requisition->expense_items)->value('item_name') ?? '';
    
    $fileName = '購入依頼内容'.'_'.now()->format('Ymd').'.'.$type;
      
    $requisition_details = [
      'requisition'     => $requisition,
      'department_name' => $department,
      'employee_name'   => $employee,
      'supplier_name'   => $customer,
      'line_name'       => $line,
      'code_name'       => $code,
      'item_name'       => $item,
      'exportType'      => $type
    ];

    return app(PurchaseRequisitionService::class)->downloadRequisitionContent(
      $type,
      $requisition_details, 
      $fileName
    );
  }
}