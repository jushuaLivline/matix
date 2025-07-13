<?php

namespace App\Http\Controllers\Outsource\Supply;

use App\Http\Controllers\Controller;
use App\Http\Requests\Material\Kanban\TemporaryRequest;
use App\Models\KanbanMaster;
use App\Models\ProductNumber;


use App\Services\Outsource\Supply\ReplenishmentService;
use App\Http\Requests\Outsource\Supply\ReplenishmentRequest;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;

class ReplenishmentController extends Controller
{
  protected $replenishmentService;
  public function __construct(ReplenishmentService $replenishmentService)
  {
    $this->replenishmentService = $replenishmentService;
    
  }
  public function index(Request $request)
  {
    return redirect()->route('outsource.supplyReplenishment.create');
  }

  /**
   * Display the supply fraction instruction form.
   */
  public function create(Request $request)
  {
    $this->replenishmentService->clearSessionIfNewPage($request);
    $getSessionData = collect(session('sessionSupplyReplenishmentData', []));

    $supplyReplenishment = session('sessionSupplyReplenishmentData', []);
    
    $supplier = $this->replenishmentService->getLatestSupplier($supplyReplenishment);
    return view('pages.outsource.supply.replenishment.create', compact('supplyReplenishment', 'supplier'));
  }

  /**
   * Store temporary instruction data in session.
   */
  public function store_session(ReplenishmentRequest $request)
  {

    $sessionData = $this->replenishmentService->store_session($request);
    // Store data in session
    session()->push('sessionSupplyReplenishmentData', $sessionData);

    return response()->json(['message' => 'Data stored successfully'], 201);
  }


  /**
   * Delete temporary instruction data from session.
   */
  public function cancel_session(Request $request, $tempDataId)
  {
    // Retrieve session data
    $sessionData = session('sessionSupplyReplenishmentData', []);

    // Filter out the item with the given tempDataId
    $updatedData = array_filter($sessionData, fn($data) => $data['id'] !== $tempDataId);

    // Update session
    session()->put('sessionSupplyReplenishmentData', array_values($updatedData));

    return response()->json(['message' => 'Data deleted successfully']);
  }

  /**
   * Update temporary instruction data in session.
   */

  public function update(ReplenishmentRequest $request, $id)
  {
    $sessionData = $this->replenishmentService->update($request, $id);
    session()->put('sessionSupplyReplenishmentData', $sessionData);

    return response()->json(['message' => 'Data updated successfully']);
  }

  /**
   * Store instructions from session into the database.
   */
  public function store(ReplenishmentRequest $request)
  {
    DB::beginTransaction();
    try {

      $this->replenishmentService->store($request->validated());
      DB::commit();
      // Clear the session data after insertion
      session()->forget('sessionSupplyReplenishmentData');
      return response()->json(['message' => 'Data successfully stored'], 201);

    } catch (Exception $e) {
      DB::rollBack();
      Log::error('処理でエラーが発生しました: ' . $e->getMessage());

      return redirect()->back()
        ->withInput()
        ->with('error', '処理に失敗しました。' . $e->getMessage());
    }
  }
}
