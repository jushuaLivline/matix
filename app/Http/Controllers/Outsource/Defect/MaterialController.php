<?php

namespace App\Http\Controllers\Outsource\Defect;

use Exception;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Helpers\RequestHelper;
use App\Models\Code;
use App\Models\OutsourceMaterialFailure;
use App\Models\ProductNumber;
use App\Models\ProcessOrder;
use App\Models\ProcessUnitPrice;
use App\Models\Process;

use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Constants\Constant;

use App\Exports\Outsource\Defect\Material\MaterialDefectExport;
use App\Http\Requests\Outsource\Defect\MaterialRequest;
use App\Services\Outsource\Defect\MaterialService;

use Maatwebsite\Excel\Facades\Excel;

class MaterialController extends Controller
{
    protected $materialService;
    public function __construct(MaterialService $materialService)
    {
        $this->materialService = $materialService;
    }

    //Outsource 53
    public function index(Request $request)
    {
        $items = [];
        $reasons = [];

        if ($request->all()) {

            RequestHelper::processRequest($request);
            $request->merge([
                'product_number' => $request->product_code,
            ]);

            $paginationThreshold = Constant::PAGINATION_THRESHOLD;
            $items = OutsourceMaterialFailure::query()
                ->search($request)
                ->paginateResults($paginationThreshold);

            $reasons = Code::selectRaw('division,code, name')
                ->whereDivision('材不理由')
                ->get();
        }

        return view('pages.outsource.defect.material.index', compact(
            'items',
            'reasons'
        ));
    }

    // Outsource 52 Inputs
    public function create(Request $request)
    {
        RequestHelper::processRequest($request);
        $reasons = Code::getDefectReasons();

        $previousUrl = URL::previous();
        $currentUrl = $request->fullUrl();

        // Clear session data if the user is coming from another link or leave this URL
        if ($previousUrl !== $currentUrl) {
            $request->session()->forget('sessionMaterialDefect');
        }

        $sessionMaterialDefect = session('sessionMaterialDefect', []);
        $firstData = end($sessionMaterialDefect);
        $grandTotal = array_reduce($sessionMaterialDefect, function ($carry, $item) {
            return $carry + $item['subTotal'];
        }, 0);

        return view('pages.outsource.defect.material.create', compact(
            'reasons',
            'sessionMaterialDefect',
            'firstData',
            'grandTotal'
        ));
    }

    public function edit(Request $request, $id)
    {
        $materialFailureRecord = $this->materialService->edit($request, $id);
        $reasons = Code::getDefectReasons();

        if (!$materialFailureRecord) {
            return redirect()->route('outsource.defect.material.index', $request->all())->with('error', 'レコードが見つかりません。');
        }

        return view('pages.outsource.defect.material.edit', compact(
            'reasons',
            'materialFailureRecord',
        ));
    }

    public function updateDefectRecord(MaterialRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $this->materialService->updateDefectRecord($request->validated(), $id);

            DB::commit();
            return back()->with('success', 'データは正常に更新されました。');

        } catch (\Exception $e) {
            DB::rollBack();
            // Handle other unexpected exceptions
            Log::error('Error occurred while creating defect materials.', [
                'error' => $e->getMessage(),
                'request' => $request->all(),
                'timestamp' => now(),
            ]);
            return back()->with('error', 'An unexpected error occurred: ' . $e->getMessage());
        }
    }

    public function fetchRecord(Request $request)
    {
        $process = ProcessOrder::where([
            'process_code' => $request->input('process_code'),
            'part_number' => $request->input('product_code')
        ])
            ->orderBy('created_at', 'desc')
            ->first();

        $product = ProductNumber::where('part_number', 'LIKE', '%' . $request->input('product_code') . '%')
            ->where('product_category', 0)
            ->with('supplier')
            ->first();

        $price = ProcessUnitPrice::where([
            'part_number' => $request->input('product_code')
        ])
            ->orderBy('created_at', 'asc')
            ->first();

        //Log::info('Record fetched:', ['record' => $product]);
        if ($process || $price) {
            return response()->json([
                'status' => 'success',
                'process' => $process,
                'product' => $product,
                'price' => $price
            ]);
        } else {
            return response()->json([
                'status' => 'fail',
                'message' => 'No matching record found'
            ]);
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $sessionMaterialDefect = session('sessionMaterialDefect', []);
            OutsourceMaterialFailure::storeMaterialFailures($sessionMaterialDefect);

            $request->session()->forget('sessionMaterialDefect');

            DB::commit();
            return back()->with('success', 'データは正常に登録されました');
        } catch (QueryException $e) {
            DB::rollBack();
            // Handle database query exceptions duplicate key violation
            Log::error('Error occurred while creating defect materials.', [
                'error' => $e->getMessage(),
                'request' => $request->all(),
                'timestamp' => now(),
            ]);
            return back()->with('error', 'Database error: ' . $e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();
            // Handle other unexpected exceptions
            Log::error('Error occurred while creating defect materials.', [
                'error' => $e->getMessage(),
                'request' => $request->all(),
                'timestamp' => now(),
            ]);
            return back()->with('error', 'An unexpected error occurred: ' . $e->getMessage());
        }
    }

    public function deleteTemp(Request $request, $id)
    {
        $sessionData = $request->session()->get('sessionMaterialDefect', []);

        foreach ($sessionData as $key => $data) {
            if ($data['id'] === $id) {
                array_splice($sessionData, $key, 1);
                break;
            }
        }

        $request->session()->put('sessionMaterialDefect', $sessionData);

        return response()->json(['message' => 'Data deleted successfully']);
    }

    public function defectRecordDumpData(Request $request)
    {
        try {
            $returnDate = $request->input('return_date');
            $processCode = $request->input('process_code');
            $processName = $request->input('process_name');
            $materialCode = $request->input('product_number');
            $materialName = $request->input('product_name');
            $supplierCode = $request->input('supplier_code');
            $supplierName = $request->input('supplier_name');
            $slipNo = $request->input('slip_no');
            $materialManufacturerCode = $request->input('material_manufacturer_code');
            $personInCharge = $request->input('person_in_charge');
            $processingUnitPrice = $request->input('processing_unit_price');
            $reasonCode = $request->input('reason_code');
            $quantity = $request->input('quantity');
            $proccessingRate = $request->input('processing_rate');
            $subTotal = $request->input('subTotal');

            $newData = [
                'id' => uniqid(), // Generate a unique ID
                'return_date' => $returnDate,
                'process_code' => $processCode,
                'process_name' => $processName,
                'material_code' => $materialCode,
                'material_name' => $materialName,
                'supplier_code' => $supplierCode,
                'supplier_name' => $supplierName,
                'slip_no' => $slipNo,
                'material_manufacturer_code' => $materialManufacturerCode,
                'person_in_charge' => $personInCharge,
                'processing_unit_price' => $processingUnitPrice,
                'reason_code' => $reasonCode,
                'quantity' => $quantity,
                'processing_rate' => $proccessingRate,
                'subTotal' => $subTotal,
            ];

            $sessionData = $request->session()->get('sessionMaterialDefect', []);
            $sessionData[] = $newData;
            $request->session()->put('sessionMaterialDefect', $sessionData);

            return back();
        } catch (\Exception $e) {
            // Handle other unexpected exceptions
            return back()->with('error', 'An unexpected error occurred: ' . $e->getMessage());
        }
    }

    public function defectRecordDumpUpdate(Request $request)
    {
        try {
            $updatedData = $request->json()->all();

            $sessionData = $request->session()->get('sessionMaterialDefect', []);

            foreach ($sessionData as &$data) {
                if ($data['id'] === $updatedData['id']) {
                    $data['reason_code'] = $updatedData['reason_code'];
                    $data['quantity'] = $updatedData['quantity'];
                    $data['processing_rate'] = $updatedData['processing_rate'];
                    $data['subTotal'] = $updatedData['subTotal'];
                    break;
                }
            }

            $request->session()->put('sessionMaterialDefect', $sessionData);

            return response()->json(['message' => 'Data updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to update data'], 500);
        }
    }

    public function fetchQueryName(Request $request)
    {
        $modelName = ucfirst($request->input('model'));
        $queryField = $request->input('query');
        $queryGet = $request->input('get');
        $value = $request->input('value');
        $compare = $request->input('compare');

        $modelClass = "App\\Models\\$modelName";
        if (!class_exists($modelClass)) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Invalid model provided'
            ], 400);
        }

        if (isset($compare) && $compare == "process_order") {
            $queryBuilder = $modelClass::where($queryField, $value)
                ->whereHas('process_order', function ($query) use ($value, $queryField) {
                    $query->where($queryField, $value);
                });

            $record = $queryBuilder->first();
        } else {
            $record = $modelClass::where($queryField, $value)->first();
        }

        if ($record) {
            if (!isset($record->$queryGet)) {
                return response()->json([
                    'status' => 'fail',
                    'message' => "Field '$queryGet' does not exist in model '$modelName'."
                ], 400);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Record found',
                'result' => $record->$queryGet
            ]);
        } else {
            return response()->json([
                'status' => 'fail',
                'message' => 'Record not found'
            ], 404);
        }
    }



    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $outsourceMaterialDefect = OutsourceMaterialFailure::find($id);
            $outsourceMaterialDefect->update($request->all());

            // Commit the transaction if everything is successful
            DB::commit();

            return response()->json(['message' => 'データは正常に更新されました。']);

        } catch (\Exception $e) {
            // Rollback the transaction if something goes wrong
            DB::rollBack();
            // Log the error with detailed information
            Log::error('Error occurred while updating defect materials.', [
                'error' => $e->getMessage(),
                'request' => $request->all(),
                'timestamp' => now(),
            ]);
            // Return error response
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function excel_export(Request $request)
    {
        $outsourceMaterialDefects = OutsourceMaterialFailure::query()->search($request)->get();
        $reasons = Code::selectRaw('division,code, name')
            ->whereDivision('材不理由')
            ->get();
        return Excel::download(new MaterialDefectExport($outsourceMaterialDefects, $reasons), '材料不良実績一覧_' . now()->format('Ymd') . '.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }
}
