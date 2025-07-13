<?php

namespace App\Http\Controllers\Material;

use App\Http\Controllers\Controller;
use App\Models\ProductNumber;
use App\Models\Material\SupplyOrder;
use App\Models\Customer;

use App\Services\Material\FractionService;
use App\Http\Requests\Material\FractionRequest;


use Exception;
use Carbon\Carbon;
use App\Constants\Constant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;


class FractionController extends Controller
{
  protected $fractionService;

  public function __construct(FractionService $fractionService)
  {
    $this->fractionService = $fractionService;
  }

  public function index(Request $request)
  {
    return redirect()->route('material.fractionCreate.create');
  }

  /**
   * Display the supply fraction instruction form.
   */
  public function create(Request $request)
  {
    $this->fractionService->clearSessionIfNewPage($request);
    $getSessionData = collect(session('sessionSupplyInstructionData', []));

    $supplyOrders = session('sessionSupplyInstructionData', []);
    $supplier = $this->fractionService->getLatestSupplier($supplyOrders);

    return view('pages.material.fraction.create', compact('supplyOrders', 'supplier'));
  }

  /**
   * Store temporary instruction data in session.
   */
  public function store_session(FractionRequest $request)
  {

    $sessionData = $this->fractionService->store_session($request);
    // Store data in session
    session()->push('sessionSupplyInstructionData', $sessionData);

    return response()->json(['message' => 'Data stored successfully'], 201);
  }


  /**
   * Delete temporary instruction data from session.
   */
  public function cancel_session(FractionRequest $request, $tempDataId)
  {
    // Retrieve session data
    $sessionData = session('sessionSupplyInstructionData', []);

    // Filter out the item with the given tempDataId
    $updatedData = array_filter($sessionData, fn($data) => $data['id'] !== $tempDataId);

    // Update session
    session()->put('sessionSupplyInstructionData', array_values($updatedData));

    return response()->json(['message' => 'Data deleted successfully']);
  }

  /**
   * Update temporary instruction data in session.
   */

  public function update(FractionRequest $request, $id)
  {
    $sessionData = $this->fractionService->update($request, $id);
    session()->put('sessionSupplyInstructionData', $sessionData);

    return response()->json(['message' => 'Data updated successfully']);
  }

  /**
   * Store instructions from session into the database.
   */
  public function store(FractionRequest $request)
  {
    DB::beginTransaction();

    try {
      $this->fractionService->store($request->validated());

      DB::commit();

      // Clear the session data after insertion
      session()->forget('sessionSupplyInstructionData');
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

