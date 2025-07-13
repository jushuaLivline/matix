<?php

namespace App\Services\Purchase;

use Request;
use App\Models\Employee;
use App\Constants\Constant;
use App\Models\PurchaseApproval;
use App\Models\Code;
use Illuminate\Support\Facades\DB;
use App\Models\PurchaseRequisition;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Models\PurchaseApprovalRouteDetail;
use App\Mail\Purchase\Notification as EmailNotification;
use App\Exports\PurchaseRequisitionContentExport;
use Dompdf\Dompdf;
use Dompdf\Options;

class PurchaseRequisitionService
{
  Private const SUPPORTED_TYPES = ['pdf', 'xlsx'];
  protected $emailNotification;
  protected $purchaseRequisition;
  protected $employee;

  public function __construct(PurchaseApproverEmailNotification $emailNotification, PurchaseRequisition $purchaseRequisition, Employee $employee) 
  {
    $this->emailNotification = $emailNotification;
    $this->purchaseRequisition = $purchaseRequisition;
    $this->employee = $employee;
  }

  public function create($request)
  {
    $purchaseRequisition = PurchaseRequisition::create($request->validated());
    $this->setPurchaseApprovalRoute($request, $purchaseRequisition);
    return $purchaseRequisition;
  }

  public function send_email_to_next_approver($request){

    $next_approver = $this->employee->get_employee_by_code($request->next_approver);
    $purchasing_approval_request_email_notification_flag = $next_approver->purchasing_approval_request_email_notification_flag;

    if($purchasing_approval_request_email_notification_flag) {
      $data = [
        'id' => $request->requisition_number,
        'subject' => '【承認確認】購買依頼について',
        'to_email' => $next_approver->mail_address,
      ];
      Mail::send(new EmailNotification($data));
    }
    return true;
  }

  // Edit requisition - fetch the requisition to edit
  public function edit($id)
  {
    // Fetch the requisition based on its ID or requisition_number
    return PurchaseRequisition::where('requisition_number', $id)->firstOrFail();
  }

   // Update requisition
  public function update($request)
  {
    // Find the requisition by requisition_number
    $purchaseRequisition = PurchaseRequisition::where('requisition_number', $request->requisition_number)->first();

    // Ensure the requisition exists before proceeding
    if (!$purchaseRequisition) {
      return response()->json(['error' => 'Requisition not found.'], 404);
    }

    // Remove current approval route
    PurchaseApproval::where('purchase_record_no', $request->requisition_number)->delete();

    // Set new approval route
    $this->setPurchaseApprovalRoute($request, $purchaseRequisition);
    
    // Update requisition only if validated data is available
    if (method_exists($request, 'validated')) {
      $purchaseRequisition->update($request->validated());
    } else {
      return response()->json(['error' => 'Invalid request data.'], 400);
    }

    return $purchaseRequisition;
  }

  public function setPurchaseApprovalRoute($request, $purchaseRequisition){

    if ($request['approval_route_number'] && $request['approval_method_category'] == 1) {
      $approval_route_details = PurchaseApprovalRouteDetail::where('approval_route_no', $request['approval_route_number'])
        ->orderBy('order_of_approval', 'ASC')
        ->get();

      foreach ($approval_route_details as $index => $approval_route_detail) {
        //  Save the first employee in 
        if ($index == 0) {
          $purchaseRequisition->update(['next_approver' => $approval_route_detail->approver_employee_code]);
        }

        PurchaseApproval::create([
          'purchase_record_no' => $purchaseRequisition->requisition_number,
          'order_of_approval' => $approval_route_detail->order_of_approval,
          'approver_employee_code' => $approval_route_detail->approver_employee_code,
        ]);
      }
    }

    return $purchaseRequisition;
  }

  public function generateRequisitionNumber()
  {
    $getLastestData = PurchaseRequisition::orderBy('id', 'DESC')->first();
    $requisition_number = date('ym');
    if (substr($getLastestData?->requisition_number, 0, 4) == $requisition_number) {
      $requisition_number .= sprintf("%06d", (int) substr($getLastestData->requisition_number, 4) + 1);
    } else {
      $requisition_number .= sprintf("%06d", 1);
    }
    return $requisition_number;
  }

  /**
   * Approves and retrieves a paginated list of purchase requisitions based on the provided search criteria.
   *
   * @param \Illuminate\Http\Request $request The HTTP request containing search criteria and filters.
   * @return \Illuminate\Pagination\LengthAwarePaginator The paginated list of purchase requisitions.
   */
  public function approveSearchList($request)
  {
    // Initialize empty data array
    $data = [];

    // Check if there are query parameters
    if ($request->query()) {
        // Set pagination threshold constant
        $paginationThreshold = Constant::PAGINATION_THRESHOLD;
        $request['state_classification'] = (isset($request['classification'])) ? ['1','2'] : ['2'];

        // Initialize query builder with related models
        $purchaseRequisition = PurchaseRequisition::with(['supplier', 'department', 'unit', 'employee', 'line']);
          
        // Apply filters and sorting
        $purchaseRequisitionQuery = $purchaseRequisition->filter($request)
                                      ->orderBy('created_at', 'desc')
                                      ->groupBy('requisition_number');
        // Paginate the results
        $data = $purchaseRequisitionQuery->paginateResults($paginationThreshold);
    }

    return $data;
  }
  /**
   * Check if the logged-in user is in the approval route for a given purchase requisition.
   *
   * @param \Illuminate\Http\Request $request The current request instance.
   * @param \App\Models\PurchaseRequisition $purchaseRequisition The purchase requisition instance.
   * @return bool True if the logged-in user is in the approval route, false otherwise.
   */
  public function isLoginUserInApprovalRoute($request, $purchaseRequisition) {
    $user = PurchaseApproval::where('approver_employee_code', '=', $request->user()->employee_code)
    ->where('purchase_record_no', $purchaseRequisition->requisition_number)
    ->first();
    return ($user) ? true : false;
  }

  public function downloadRequisitionContent(string $type, array $purchaseRequisitionDetails, string $fileName){
    if (!in_array($type, self::SUPPORTED_TYPES, true)) {
      throw new \InvalidArgumentException('サポートされていない出力形式です。サポートされている形式はpdf, xlsxとなります。');
    }

    $pExport = new PurchaseRequisitionContentExport($purchaseRequisitionDetails);

    if ($type === 'pdf') {
      //return $pExport->view();
      return $this->generatePdf($pExport, $fileName);
    }
  }

  private function generatePdf(PurchaseRequisitionContentExport $export, string $fileName): \Illuminate\Http\Response
  {
    $pdfOptions = new Options();
    $pdfOptions->set('defaultFont', 'Noto Sans JP');
    $pdfOptions->set('isRemoteEnabled', true);
    $pdfOptions->set('isHtml5ParserEnabled', true);

    // Set the font directory and cache directory
    $pdfOptions->set('fontDir', storage_path('dompdf/fonts/'));
    $pdfOptions->set('fontCache', storage_path('dompdf/fonts/'));

    $dompdf = new Dompdf($pdfOptions);
    $dompdf->loadHtml($export->view()->render());
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    return $dompdf->stream($fileName);
  }
}