<?php

namespace App\Http\Controllers\Material;

use App\Http\Controllers\Controller;
use App\Models\ProductNumber;
use App\Models\SupplyMaterialOrder;
use App\Models\Process;
use App\Models\UnofficialNotice;
use App\Models\ManufacturerInfo;
use App\Models\Authority;

use App\Services\Material\ProcurementService;
use App\Http\Requests\Material\ProcurementRequest;

use App\Exports\Material\ProcurementExcelExport;

use Exception;
use Carbon\Carbon;
use App\Constants\Constant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Dompdf\Dompdf;
use Dompdf\Options;
use Barryvdh\DomPDF\Facade\Pdf;

class ProcurementController extends Controller
{
  protected $procurementService;
  public function __construct(ProcurementService $procurementService)
  {
    $this->procurementService = $procurementService;
  }

  public function index(Request $request)
  { 
    if (!$request->all()) {
      // Define default empty values
      $request->merge([
        'part_classification' => '0',
      ]);
      
    }
    // Build the query based on request data
    $paginationThreshold = Constant::PAGINATION_THRESHOLD;
    $query = ProductNumber::getSuppliedList($request);
    $results = (count($request->all()) > 1) ? $query->paginate($paginationThreshold) : [];
    $manufacturerInfo = ManufacturerInfo::where('material_manufacturer_code', $request->process_code)->first();

    // Return the view with the results and aggregated data
    return view('pages.material.procurement.index', compact(
    'results',
    'manufacturerInfo'
    ));
  }

  public function create(Request $request)
  {
    if($request->part_number){
      $request->merge([
        'edited_part_nubmer' => $request->part_number,
      ]);
    }
    $yearMonth = $request->input('year_month')
      ? Carbon::createFromFormat('Ym', $request->input('year_month'))
      : Carbon::now();
    $nextMonth = $yearMonth->copy()->addMonth();
    $nextTwoMonths = $yearMonth->copy()->addMonths(2);
    $product = ProductNumber::getSuppliedList($request)->first();
    $procurement = [];

    return view('pages.material.procurement.create', compact(
      'product',
      'procurement'
    ));
  }

  public function store(ProcurementRequest $request)
  {
    // Initialize database transaction for creating new records
    DB::beginTransaction();
    try {
      $this->procurementService->store($request->validated());
      DB::commit();
      return redirect()->route('material.procurement.index', $request->query())->with('success', '材料調達計画の登録が完了しました。');

    } catch (\Exception $e) {
      // Log the error with detailed information
      Log::error('Error occurred while registering procurement.', [
        'error' => $e->getMessage(),
        'request_data' => $request->all(),
        'timestamp' => now(),
      ]);
      return redirect()->back()->with('error', 'エラーが発生しました: ' . $e->getMessage());
    }
  }

  public function update(ProcurementRequest $request, $id)
  {
    // Initialize database transaction for creating new records
    DB::beginTransaction();
    try{
      $this->procurementService->update($request->validated(), $id, $request['instruction_number']);
      
      DB::commit();
      return redirect()->route('material.procurement.index', $request->query())->with('success', '材料調達計画の更新が完了しました。');

    } catch (\Exception $e) {
      // Log the error with detailed information
      Log::error('Error occurred while updating procurement.', [
        'error' => $e->getMessage(),
        'request_data' => $request->all(),
        'timestamp' => now(),
      ]);
      return redirect()->back()->with('error', 'エラーが発生しました: ' . $e->getMessage());
    }
  }

  public function destroy(Request $request, $id)
  {
   // Initialize database transaction for creating new records
   DB::beginTransaction();
   try {
     $this->procurementService->destroy( $request, $id);

     DB::commit();
     return redirect()->route('material.procurement.index', $request->query())->with('success', '材料調達計画の削除が完了しました。');

   } catch (\Exception $e) {
     // Log the error with detailed information
     Log::error('Error occurred while deletin procurement.', [
       'error' => $e->getMessage(),
       'request_data' => $request->all(),
       'timestamp' => now(),
     ]);
     return redirect()->back()->with('error', 'エラーが発生しました: ' . $e->getMessage());
   }
  }

  public function excel_export(Request $request)
  {
    if (!$request->all()) {
      // Define default empty values
      $request->merge([
        'part_classification' => '0',
      ]);
    }

    $query = ProductNumber::getSuppliedList($request)->get();
    $yearMonth = $request->year_month;
    $dateInput = $yearMonth ? Carbon::createFromFormat('Ym', $yearMonth)->startOfMonth() : now();
    $firstDateOfMonth = $dateInput->copy()->startOfMonth();
    $lastDateOfMonth = $dateInput->copy()->endOfMonth();

    $dates = collect(range(0, $firstDateOfMonth->diffInDays($lastDateOfMonth)))
              ->map(fn($i) => [
                  'date' => $firstDateOfMonth->copy()->addDays($i)->format('Y-m-d'),
                  'day' => (int) $firstDateOfMonth->copy()->addDays($i)->format('d'),
                  'isWeekend' => in_array($firstDateOfMonth->copy()->addDays($i)->format('w'), [0, 6]),
              ])->toArray();

    $fileName = '材料調達計画表一覧-'.now()->format('Ymd').'.xlsx';
    return Excel::download(
      new ProcurementExcelExport($query, $firstDateOfMonth, $lastDateOfMonth, $dates),
      $fileName
      );
  }

  public function pdf_export(Request $request)
  {
    if (!$request->all()) {
      // Define default empty values
      $request->merge([
        'part_classification' => '0',
      ]);
    }

    $procurementPlanLists = ProductNumber::getSuppliedList($request)->get();
    $yearMonth = $request->year_month;
    $dateInput = $yearMonth ? Carbon::createFromFormat('Ym', $yearMonth)->startOfMonth() : now();
    $firstDateOfMonth = $dateInput->copy()->startOfMonth();
    $lastDateOfMonth = $dateInput->copy()->endOfMonth();
    $authorizationName = Authority::where('authorization_code',$request->user()->authorization_code)->first();

    $dates = collect(range(0, $firstDateOfMonth->diffInDays($lastDateOfMonth)))
              ->map(fn($i) => [
                  'date' => $firstDateOfMonth->copy()->addDays($i)->format('Y-m-d'),
                  'day' => (int) $firstDateOfMonth->copy()->addDays($i)->format('d'),
                  'isWeekend' => in_array($firstDateOfMonth->copy()->addDays($i)->format('w'), [0, 6]),
              ])->toArray();
      
    $pdfOptions = new Options();
    $pdfOptions->set('defaultFont', 'Arial');
    $pdfOptions->set('isRemoteEnabled', true);
    $pdfOptions->set('isHtml5ParserEnabled', true);

    // Prevent new font generation by setting an existing font cache
    $fontPath = storage_path('dompdf/fonts/');
    if (is_dir($fontPath) && is_writable($fontPath)) {
      $pdfOptions->set('fontDir', $fontPath);
      $pdfOptions->set('fontCache', $fontPath);
    } else {
      // If not writable, set read-only mode (no new fonts will be generated)
      $pdfOptions->set('isFontSubsettingEnabled', false);
    }


    $html = view('pages.material.procurement.pdf_template', compact(
      'procurementPlanLists', 
    'firstDateOfMonth', 
                'lastDateOfMonth', 
                'yearMonth',
                'authorizationName',
                'dates'))->render();

    $dompdf = new Dompdf($pdfOptions);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();
    
    $fileName = '材料調達計画表一覧-'.now()->format('Ymd').'.pdf';
    return $dompdf->stream($fileName);
  }
}
