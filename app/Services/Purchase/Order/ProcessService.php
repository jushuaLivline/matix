<?php

namespace App\Services\Purchase\Order;

use Request;
use App\Models\Employee;
use App\Models\PurchaseApproval;
use App\Models\Code;
use App\Models\PurchaseRequisition;
use App\Constants\Constant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Dompdf\Dompdf;
use Dompdf\Options;

class ProcessService
{
  /**
   * Approves and retrieves a paginated list of purchase requisitions based on the provided search criteria.
   *
   * @param \Illuminate\Http\Request $request The HTTP request containing search criteria and filters.
   * @return \Illuminate\Pagination\LengthAwarePaginator The paginated list of purchase requisitions.
   */
  public function index($request)
  {
    // Initialize empty data array
    $purchaseRequisition = [];

    // Check if there are query parameters
    if ($request->query()) {
      // Set pagination threshold constant
      $paginationThreshold = Constant::PAGINATION_THRESHOLD;
      $request['state_classification'] = (isset($request['classification'])) ? ['1', '2'] : ['2'];

      // Initialize query builder with related models
      $purchaseRequisition = PurchaseRequisition::with(['supplier', 'department', 'unit', 'employee', 'line'])
                              ->filter($request)
                              ->orderByDesc('created_at')
                              ->groupBy('requisition_number')
                              ->paginateResults($paginationThreshold);
    }
    return $purchaseRequisition;
  }

}