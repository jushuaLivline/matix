<?php

namespace App\Http\Controllers\Material;

use App\Http\Controllers\Controller;
use App\Models\ProductNumber;
use App\Models\SupplyMaterialOrder;
use App\Models\Material\SupplyArrival;
use App\Models\Customer;

use App\Services\Material\InspectionService;
use App\Http\Requests\Material\InspectionRequest;


use Exception;
use Carbon\Carbon;
use App\Constants\Constant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;


class InspectionController extends Controller
{
  protected $inspectionService, $supplyArrival;

  public function __construct(InspectionService $inspectionService)
  {
    $this->inspectionService = $inspectionService;
  }

  public function index(Request $request)
  {
    // Get the latest order number
    $previousUrl = URL::previous();
    $currentUrl = $request->fullUrl();

    // Clear session data if the user is coming from another link or leave this URL
    if ($previousUrl !== $currentUrl) {
      $request->session()->forget('sessionReceiptAndInspectionData');
    }

    $recentSupplyArrivals = session('sessionReceiptAndInspectionData', []);
    return view('pages.materials.inspection.index', compact('recentSupplyArrivals'));
  }

  public function store_session(Request $request)
  {
    $sessionData = $this->inspectionService->store_session($request);

    // Store data in session
    session()->push('sessionReceiptAndInspectionData', $sessionData);

    return response()->json(['message' => 'データは正常に登録されました'], 201);
  }

  public function cancel_session(Request $request, $tempDataId)
  {
    session()->put('sessionReceiptAndInspectionData', array_filter(
      session('sessionReceiptAndInspectionData', []),
      fn($data) => $data['id'] !== $tempDataId
    ));

    return response()->json(['message' => 'Data deleted successfully']);
  }


  public function update(Request $request, $id)
  {

    $sessionData = $this->inspectionService->update($request, $id);
    // Update session
    session()->put('sessionReceiptAndInspectionData', $sessionData);

    return response()->json(['message' => 'Data updated successfully']);
  }

  public function store(InspectionRequest $request)
  {
    // Start a transaction
    DB::beginTransaction();
   
    try {
      $this->inspectionService->store($request->validated());
      
      // Commit the transaction
      DB::commit();

      // Clear the session data after insertion
      session()->forget('sessionReceiptAndInspectionData');
      return response()->json(['message' => 'データは正常に登録されました']);

    } catch (\Exception $e) {
      // Rollback the transaction if something went wrong
      DB::rollBack();

      // Log the error with detailed information
      Log::error('Error occurred while adding supply material  order.', [
        'error' => $e->getMessage(),
        'timestamp' => now(),
      ]);

      // Handle the error, log it or display a custom error message
      return redirect()->back()->with('error', 'Error occurred while adding supply material  order.');
    }
  }

}

