<?php

namespace App\Http\Controllers\Purchase;

use App\Http\Controllers\Controller;
use App\Models\PurchaseRequisition;
use App\Models\PurchaseRecord;

use App\Exports\Purchase\Supplier\PurchaseAmountSearchExport;
use App\Exports\Purchase\Supplier\PurchaseRecordExport;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Helpers\RequestHelper;
use Maatwebsite\Excel\Facades\Excel;
use App\Constants\Constant;

class SupplierController extends Controller
{

  public function index(Request $request)
  {
    $purchaseRecord = [];
    if (!$request->query()) {
      // Define default empty values
      $defaultQuery = [
        'purchase_category' => '0',
        'year_month_start' => now()->startOfMonth()->format('Ym'),
        'year_month_end' => now()->endOfMonth()->format('Ym')
      ];
      $request->merge($defaultQuery);
    }

    // Format the arrival_date_start & arrival_date_end | eg 20250130
    if ($request->year_month_start) {
      $request->merge([
        'arrival_date_start' => Carbon::createFromFormat('Ym', $request->year_month_start)->startOfMonth()->format('Ymd'),
      ]);
    }
    if ($request->year_month_end) {
      $request->merge([
        'arrival_date_end' => Carbon::createFromFormat('Ym', $request->year_month_end)->endOfMonth()->format('Ymd'),
      ]);
    }

    $paginationThreshold = Constant::PAGINATION_THRESHOLD;
    $purchaseRecord = PurchaseRecord::filtered($request)->paginateResults($paginationThreshold);
    return view('pages.purchases.supplier.index', compact('purchaseRecord'));
  }

  public function show(Request $request, $id)
  {
    $purchaseRecord = [];
    
    if ($request->query()){
        $purchaseRecord = PurchaseRecord::with(['supplier','requisition','requisition.employee','item'])
          ->filter($request)
          ->orderByDesc('created_at')
          ->paginateResults(100);
    }
    return view('pages.purchases.supplier.show', compact('purchaseRecord'));
  }

  public function excel_export(Request $request)
  {
    $datas = [];
    // Check if there are any query parameters and apply filtering if present
    if ($request->query()) {
      // Retrieve only the data from the current page
      $datas = PurchaseRecord::filtered($request)->paginateResults($request->per_page ?? 20)->items();
    }
    // Generate the file name with the current date
    $fileName = '購買品購入実績入力-' . now()->format('Ymd') . '.xlsx';
    // Export the data using the specified export class and file name
    return Excel::download(
      export: new PurchaseAmountSearchExport($datas),
      fileName: $fileName
    );
  }


  public function excel_export_detail(Request $request)
  {
    $purchaseRecord = [];
    // Check if query parameters exist and fetch records if so
    if(count($request->query()) > 0){
        $purchaseRecord = PurchaseRecord::with(['supplier','requisition','requisition.employee','item'])->filter($request)->paginateResults(100);
    }
    // Set the file name for the Excel export
    $fileName = '購買品購入実績入力-'.now()->format('Ymd').'.xlsx';
    // Export the records to Excel and return the download response
    return Excel::download(
        export: new PurchaseRecordExport($purchaseRecord), 
        fileName: $fileName);
  }

}
