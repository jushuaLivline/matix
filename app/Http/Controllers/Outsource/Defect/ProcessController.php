<?php

namespace App\Http\Controllers\Outsource\Defect;

use App\Exports\Outsource\Defect\ProcessDefectExport;
use App\Helpers\RequestHelper;
use App\Http\Controllers\Controller;
use App\Models\OutsourceProcessFailure;
use App\Models\ProductNumber;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\Material\Kanban\TemporaryRequest;
use App\Models\KanbanMaster;
use App\Services\Outsource\Defect\ProcessService;
use App\Http\Requests\Outsource\Defect\ProcessRequest;
use Exception;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;

class ProcessController extends Controller
{
    protected $processService;

    public function __construct(ProcessService $processService)
    {
        $this->processService = $processService;
        
    }
    /**
     * Display a listing of the resource.
     *
     * This method handles the incoming request to display a list of outsource process failures.
     * It processes the request, filters the data based on the provided criteria, and paginates the results.
     * If no request data is provided, it returns all.
     *
     * @param \Illuminate\Http\Request $request The incoming request instance.
     * @return \Illuminate\View\View The view displaying the list of outsource process failures.
     */
    public function index(Request $request)
    {   
        RequestHelper::processRequest($request);
        $startDate = now()->startOfMonth()->format('Ymd');
        $endDate = now()->endOfMonth()->format('Ymd');

        $disposalDateFrom = $request->disposal_date_from ? $request->disposal_date_from : $startDate;
        $disposalDateTo = $request->disposal_date_to? $request->disposal_date_to : $endDate;
        $inputDateFrom = $request->input_date_from ? $request->input_date_from : $startDate;
        $inputDateTo = $request->input_date_to? $request->input_date_to : $endDate;

        $request->merge([
            'disposal_date_from' => $disposalDateFrom,
            'disposal_date_to' => $disposalDateTo,
            'input_date_from' => $inputDateFrom,
            'input_date_to' => $inputDateTo,
        ]);

        $filters = $request->only([
            'disposal_date_from', 'disposal_date_to', 'input_date_from', 'input_date_to',
            'product_code', 'process_code', 'slip_no'
        ]);

        // Remove empty filter values
        $filters = array_filter($filters, function ($value) {
            return $value !== null && $value !== '';
        });

        // Start a query builder instance
        $query = OutsourceProcessFailure::with(['product', 'product.customer', 'product.processUnitPrice', 'process']);

        // Apply filters only if they exist
        if (!empty($filters)) {
            $query = $query->filter($filters);
        }

        // Ensure pagination is applied
        $items = $query->paginateResults();

        $datas = ProductNumber::getFilteredProducts();

        return view('pages.outsource.defect.process.index', compact('items', 'datas'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $this->processService->clearSessionIfNewPage($request);
        $getSessionData = collect(session('sessionDefectProcessData', []));

        $sessionDefectProcessData = session('sessionDefectProcessData', []);
        
        $supplier = $this->processService->getLatestSupplier($sessionDefectProcessData);
        return view('pages.outsource.defect.process.create', compact('sessionDefectProcessData', 'supplier'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $query = OutsourceProcessFailure::where('id', $id)
        ->with('product', 'product.customer', 'product.processUnitPrice', 'process')
        ->first();
        return view('pages.outsource.defect.process.edit', compact('query'));
    }

    /**
   * Update defect record
   */

  public function update(Request $request, $id)
  {
    $filter = $request->only([
        'updator', 'updated_at', 'updated_at', 'slip_no',
        'disposal_date', 'process_code', 'part_number', 'quantity', 'serial_number'
    ]);
    $sessionData = OutsourceProcessFailure::where('id', $id)
    ->update($filter);

    return redirect()->back()->with('success', 'データが正常に更新されました');
  }


  /**
   * Store instructions from session into the database.
   */
    public function store(ProcessRequest $request)
    {
        DB::beginTransaction();
        try {

        $this->processService->store($request->validated());
        DB::commit();
        // Clear the session data after insertion
        session()->forget('sessionDefectProcessData');
        return response()->json(['message' => 'Data successfully stored'], 201);

        } catch (Exception $e) {
        DB::rollBack();
        Log::error('処理でエラーが発生しました: ' . $e->getMessage());

        return redirect()->back()
            ->withInput()
            ->with('error', '処理に失敗しました。' . $e->getMessage());
        }
    }

    public function getProductUnitPrice($productCode) {
        $price = $this->processService->getProductUnitPrice($productCode);

        return response()->json([
            'success' => true,
            'product_code' => $productCode,
            'unit_price' => $price
        ]);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Update the defect item with the provided data.
     *
     * @param \Illuminate\Http\Request $request The request object containing the data to update.
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the success or failure of the update operation.
     *
     * @throws \Illuminate\Validation\ValidationException If the validation of the request data fails.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If the specified defect item is not found.
     * @throws \Exception If any other error occurs during the update process.
     */
    public function updateDefectItem(Request $request)
    {
        try {
            $data = $request->validate([
                'id' => 'required|integer',
                'part_number' => 'required',
                'quantity' => 'required|numeric',
                'slip_no' => 'required|numeric',
            ]);

            // Call the model method
            $updated = OutsourceProcessFailure::updateDefectItem($data);

            if ($updated) {
                return response()->json(['message' => 'Data updated successfully']);
            } else {
                return response()->json(['message' => 'Failed to update data'], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to update data', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Export machining defect data to an Excel file.
     *
     * This method processes the incoming request, applies the necessary filters,
     * retrieves the filtered data, and exports it to an Excel file.
     *
     * @param \Illuminate\Http\Request $request The incoming HTTP request containing filter parameters.
     * 
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse The response containing the Excel file download.
     */
    public function machiningDefectExport(Request $request)
    {
        RequestHelper::processRequest($request);
        $startDate = now()->startOfMonth()->format('Ymd');
        $endDate = now()->endOfMonth()->format('Ymd');
        $disposalDateFrom = $request->disposal_date_from ? $request->disposal_date_from : $startDate;
        $disposalDateTo = $request->disposal_date_to? $request->disposal_date_to : $endDate;
        $inputDateFrom = $request->input_date_from ? $request->input_date_from : $startDate;
        $inputDateTo = $request->input_date_to? $request->input_date_to : $endDate;

        $request->merge([
            'disposal_date_from' => $disposalDateFrom,
            'disposal_date_to' => $disposalDateTo,
            'input_date_from' => $inputDateFrom,
            'input_date_to' => $inputDateTo,
        ]);
        $filters = $request->only([
            'disposal_date_from', 'disposal_date_to', 
            'input_date_from', 'input_date_to', 
            'product_code', 'process_code', 'slip_no'
        ]);

        // Retrieve filtered data using the scope method
        $items = OutsourceProcessFailure::machiningDefectExport($filters)->paginateResults();

        $fileName = '加工不良記録_' . now()->format('Ymd') . '.xlsx';
        return Excel::download(new ProcessDefectExport($items), $fileName, \Maatwebsite\Excel\Excel::XLSX);
    }

    /**
     * Store temporary data in session.
     */
    public function store_session(ProcessRequest $request)
    {
        $sessionData = $this->processService->store_session($request);
        // Store data in session
        session()->push('sessionDefectProcessData', $sessionData);

        return response()->json(['message' => 'Data stored successfully'], 201);
    }

    /**
     * Delete temporary data from session.
     */
    public function cancel_session(Request $request, $tempDataId)
    {
        // Retrieve session data
        $sessionData = session('sessionDefectProcessData', []);

        // Filter out the item with the given tempDataId
        $updatedData = array_filter($sessionData, fn($data) => $data['id'] !== $tempDataId);

        // Update session
        session()->put('sessionDefectProcessData', array_values($updatedData));

        return response()->json(['message' => 'Data deleted successfully']);
    }
    /**
     * Update temporary data in session.
    */

    public function update_session(ProcessRequest $request, $id)
    {
        $sessionData =  $this->processService->update_session($request, $id);
        session()->put('sessionDefectProcessData', $sessionData);

        session()->flash('success', 'データが正常に更新されました');
        return response()->json([
            'success' => true,
            'message' => 'データが正常に更新されました'
        ]);
        // return redirect()->route('outsource.defect.process.create')->with('success', 'データが正常に更新されました');
    }
}
