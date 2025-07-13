<?php

namespace App\Http\Controllers\Material\Kanban;

use App\Http\Controllers\Controller;
use App\Http\Requests\Material\Kanban\TemporaryRequest;
use App\Models\KanbanMaster;
use App\Models\ProductNumber;
use App\Models\SupplyMaterialOrder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TemporaryController extends Controller
{
    public function __construct()
    {
        $this->supplyMaterialOrder = new SupplyMaterialOrder();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return redirect()->route('material.kanbanTemporary.create');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        // Get the latest order number
        $previousUrl = URL::previous();
        $currentUrl = $request->fullUrl();

        // Clear session data if the user is coming from another link or leave this URL
        if ($previousUrl !== $currentUrl) {
            $request->session()->forget('sessionSupplyTempData');
        }

        $kanbanDatas = KanbanMaster::selectRaw("management_no, number_of_accomodated")->get()->toArray();
        $productDatas = ProductNumber::selectRaw("part_number, uniform_number, material_manufacturer_code")->get()->toArray();

        $sessionSupplyTempData = session('sessionSupplyTempData', []);

        return view('pages.material.kanban.temporary.create', compact('sessionSupplyTempData','productDatas','kanbanDatas'));
    }

    /**
     * Store the temporary form data.
     *
     * This method handles the storage of temporary form data. It validates the incoming request,
     * processes the session data, generates new order numbers, stores the data, and clears the session data.
     *
     * @param TemporaryRequest $request The incoming request containing the temporary form data.
     * @return \Illuminate\Http\RedirectResponse Redirects back with a success message upon successful storage.
     */
    public function store(TemporaryRequest $request)
    {
        // Start a transaction
        DB::beginTransaction();
        try {
            $sessionData = $request->session()->get('sessionSupplyTempData', []);
            $supplyMaterialOrderNo = $this->supplyMaterialOrder->generateSupplyMaterialOrderNo();
            $yearMonths = date('ym');

            $insertData = collect($sessionData)->map(fn($data, $index) => [
                'supply_material_order_no' => $yearMonths . sprintf("%06d", substr($supplyMaterialOrderNo, 4) + $index),
            ] + array_diff_key($data, array_flip(['id', 'number_of_accomodated', 'product_name', 'product_code','uniform_number'])) )->toArray();

            // Insert the data into the database    
            SupplyMaterialOrder::insert($insertData);
            DB::commit();
            
            // Clear session data
            session()->forget('sessionSupplyTempData');

            return redirect()->back()->with('success', 'の登録が完了いたしました。');

        } catch (\Exception $e) {
            // Rollback the transaction if something went wrong
            DB::rollBack();

            // Log the error with detailed information
            Log::error('Error occurred.', [
                'error' => $e->getMessage(),
                'timestamp' => now(),
            ]);

            // Handle the error, log it or display a custom error message
            return redirect()->back()->with('error', 'Error occurred.');
        }
    }

    public function saveTemporaryData(TemporaryRequest $request)
    {
        $request->merge(['instruction_number' =>  $request->instruction_no]);
        $inputData = $request->only([
            'management_no',
            'material_number',
            'product_code',
            'material_manufacturer_code',
            'product_name',
            'uniform_number',
            'instruction_date',
            'instruction_no',
            'number_of_accomodated',
            'instruction_kanban_quantity',
            'arrival_quantity',
            'instruction_number',
          ]);
       
        $supplierCode = ProductNumber::where('part_number', $inputData['material_number'])->selectRaw("supplier_code")->first();
        $newData = array_merge($inputData, [
            'id' => uniqid(),
            'order_classification' => 2,
            'supply_material_order_no' => $this->supplyMaterialOrder->generateSupplyMaterialOrderNo(),
            'supplier_code' => $supplierCode->supplier_code,
            'creator' => $request->user()->id,
        ]);
        $sessionData = $request->session()->get('sessionSupplyTempData', []);
        $sessionData[] = $newData;
        $request->session()->put('sessionSupplyTempData', $sessionData);

        return response()->json(['message' => 'Data stored successfully']);
    }

    public function updateTemporaryData(TemporaryRequest $request)
    {
        $id = $request->input('temp_data_id');
        $request->merge(['supply_material_order_no' =>  $this->supplyMaterialOrder->generateSupplyMaterialOrderNo() + 1]);
        $sessionData =  collect(session('sessionSupplyTempData', []));
        $inputData = $request->all();

         
        $supplierCode = ProductNumber::where('part_number', $request['product_code'])->selectRaw("supplier_code")->first();

        $sessionData = $sessionData->map(function ($data) use ($inputData, $supplierCode, $id) {
            if ($data['id'] === $id) {
              return [
                ...$data,
                'management_no' => $inputData['management_no'],
                'material_manufacturer_code' => $inputData['material_manufacturer_code'],
                'product_name' => $inputData['product_name'],
                'product_code' => $inputData['product_code'],
                'uniform_number' => $inputData['uniform_number'],
                'instruction_date' => $inputData['instruction_date'],
                'instruction_no' => $inputData['instruction_no'],
                'instruction_number' => $inputData['instruction_no'],
                'number_of_accomodated' => $inputData['number_of_accomodated'],
                'instruction_kanban_quantity' => $inputData['instruction_kanban_quantity'],
                'arrival_quantity' => $inputData['arrival_quantity'],
                'supplier_code' => $supplierCode->supplier_code ?? null,
              ];
            }
            return $data;
          })->toArray();

        $request->session()->put('sessionSupplyTempData', $sessionData);

        return response()->json(['message' => '更新が完了いたしました']);
    }

    public function removeTemporaryData(Request $request, $tempDataId)
    {
        $sessionData = $request->session()->get('sessionSupplyTempData', []);

        foreach ($sessionData as $key => $data) {
            if ($data['id'] === $tempDataId) {
                array_splice($sessionData, $key, 1);
                break;
            }
        }

        $request->session()->put('sessionSupplyTempData', $sessionData);

        return response()->json(['message' => '削除が完了いたしました']);
    }

    
    /**
     * Fetches Kanban details based on the provided management number.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetchKanbanDetails(TemporaryRequest $request)
    {
        // Check if management number is provided in the request
        if ($request->management_no != null) {
            $mgtNumber = $request->management_no;
            
            // Find the Kanban record with the given management number and classification of 1
            $kanban = KanbanMaster::where('management_no', $mgtNumber)
                                ->where('kanban_classification', 1)
                                ->first();
            
            // If Kanban record is found
            if ($kanban) {
                // Find the associated product using the part number from the Kanban record
                $product = ProductNumber::where('part_number', $kanban->part_number)->first();
                
                // If the product is found, return the necessary details
                if ($product) {
                    return response()->json([
                        "material_manufacturer_code" => $product->material_manufacturer_code,
                        "product_code" => $kanban->part_number,
                        "product_name" => $product->product_name,
                        "uniform_number" => $kanban->printed_jersey_number,
                        "number_of_accomodated" => $kanban->number_of_accomodated
                    ]);
                }
            }

            // Return an empty response if no Kanban or Product is found
            return response()->json([]);
        }
    }

    /**
     * Fetches product details based on the provided part number.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetchProductDetails(Request $request)
    {
        // Check if part number is provided in the request
        if ($request->part_number != null) {
            $part_number = $request->part_number;
            
            // Find the product record with the given part number
            $product = ProductNumber::where('part_number', $part_number)->first();
            
            // If the product is found, return the relevant details
            if ($product) {
                return response()->json([
                    "material_manufacturer_code" => $product->material_manufacturer_code,
                    "product_name" => $product->product_name,
                    "uniform_number" => $product->uniform_number,
                ]);
            }
            
            // Return an empty response if no product is found
            return response()->json([]);
        }
    }
}
