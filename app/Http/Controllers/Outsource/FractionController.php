<?php
namespace App\Http\Controllers\Outsource;
use App\Http\Controllers\Controller;

use App\Http\Requests\Outsource\FractionRequest;
use App\Models\OutsourcedProcessing;
use App\Models\Process;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FractionController extends Controller
{

    // Outsource processing 38
    public function create(Request $request)
    {
        $previousUrl = URL::previous();
        $currentUrl = $request->fullUrl();
    
        // Clear session data if the user is coming from another link or leave this URL
        if ($previousUrl !== $currentUrl) {
            $request->session()->forget('sessionData');
        }
        
        $sessionData = session('sessionData', []);
        $process = null;
        
        foreach ($sessionData as $data) {
            if (isset($data['process_code'])) {
                $processCode = $data['process_code'];

                $process = Process::selectRaw("process_code, process_name")
                    ->where("process_code", $processCode)
                    ->first();
            }
        }

        return view('pages.outsource.fraction.create', compact('sessionData', 'process'));
    }

    // Outsource processing 38 - bulk Saving data
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $processCode = $request->input('process_code');
            $process = Process::where("process_code", $processCode)->first();
            $sessionData = $request->session()->get('sessionData', []);

            // Get the latest order number
            $latestOrderNumber = OutsourcedProcessing::generateOrderNo();

            // Extract the 6-digit count from the start of the number
            $count = intval(substr($latestOrderNumber, 4, 6));

            // foreach ($sessionData as $index => $data) {
            //     // Calculate the new order number
            //     $newOrderNumber = now()->format('md').'0'.($count + 100 + ($index * 100));

            //     OutsourcedProcessing::create([
            //         'order_no' => $newOrderNumber,
            //         'supplier_process_code' => $process->process_code,
            //         'supplier_code' => $process->customer_code,
            //         'order_classification' => 3,
            //         'product_code' => $data['product_code'],
            //         'instruction_date' => $data['instruction_date'],
            //         'instruction_number' => $data['instruction_number'],
            //         'instruction_kanban_quantity' => $data['instruction_kanban_quantity'],
            //     ]);
            // }

            $insertData = collect($sessionData)
            ->map(function ($data, $index) use ($process, $count, $latestOrderNumber) {
                $index = $index + 1; // Start from 1 for the first item
                return [
                    'order_no'                    => $latestOrderNumber + $index,
                    'supplier_process_code'       => $process->process_code,
                    'supplier_code'               => $process->customer_code,
                    'order_classification'        => 3,
                    'product_code'                => $data['product_code'],
                    'instruction_date'            => $data['instruction_date'],
                    'instruction_number'          => $data['instruction_number'],
                    'instruction_kanban_quantity' => $data['instruction_kanban_quantity'],
                    'creator' => $data['creator'],
                ];
            })
            ->toArray();

            OutsourcedProcessing::insert($insertData);

            DB::commit();
            $request->session()->forget('sessionData');

            return response()->json(['message' => 'データは正常に登録されました']);
        } catch(\Exception $e){
            DB::rollBack();

            Log::error('Error occurred.', [
                'error' => $e->getMessage(),
                'timestamp' => now(),
            ]);

            //return redirect()->back()->with('error', $e->getMessage());
            return response()->json(['message' => $e->getMessage()]);
        }

    }

    // Outsource processing 38 - temp Saving data
    public function tempData(FractionRequest $request)
    {
        $processCode = $request->input('process_code');
        $partNumber = $request->input('product_code');
        $partName = $request->input('product_name');
        $instructionDate = $request->input('instruction_date');
        $instructionNumber = $request->input('instruction_number');
        $instructionKanbanQuantity = $request->input('instruction_kanban_quantity');

        $newData = [
            'id' => uniqid(), // Generate a unique ID
            'process_code' => $processCode,
            'product_code' => $partNumber,
            'product_name' => $partName,
            'instruction_date' => $instructionDate,
            'instruction_number' => $instructionNumber,
            'instruction_kanban_quantity' => $instructionKanbanQuantity,
            'creator' => $request->user()->id,
        ];

        $sessionData = $request->session()->get('sessionData', []);
        $sessionData[] = $newData;

        $request->session()->put('sessionData', $sessionData);

        return response()->json(['message' => 'Data stored successfully']);
    }

    // Outsource processing 38 - Update data
    public function tempUpdateData(FractionRequest $request)
    {
        $instructionDataId = $request->input('instruction_data_id');
        $productCode = $request->input('product_code');
        $productName = $request->input('product_name');
        $instructionDate = $request->input('instruction_date');
        $instructionNumber = $request->input('instruction_number');
        $instructionKanbanQuantity = $request->input('instruction_kanban_quantity');

        $sessionData = $request->session()->get('sessionData', []);

        foreach ($sessionData as &$data) {
            if ($data['id'] === $instructionDataId) {
                $data['product_code'] = $productCode;
                $data['product_name'] = $productName;
                $data['instruction_date'] = $instructionDate;
                $data['instruction_number'] = $instructionNumber;
                $data['instruction_kanban_quantity'] = $instructionKanbanQuantity;
                break;
            }
        }

        $request->session()->put('sessionData', $sessionData);

        return response()->json(['message' => 'Data updated successfully']);
    }

    // Outsource processing 38 - Delete data
    public function deleteData(Request $request, $instructionDataId)
    {
        $sessionData = $request->session()->get('sessionData', []);

        foreach ($sessionData as $key => $data) {
            if ($data['id'] === $instructionDataId) {
                unset($sessionData[$key]);
                break;
            }
        }

        $request->session()->put('sessionData', $sessionData);

        return response()->json(['message' => 'Data deleted successfully']);
    }

    public function getProcessName(Request $request){
        $process_code = $request->input('process_code');
        $process = Process::where('process_code', $process_code)->first();

        if ($process) {
            return response()->json([
                'status' => 'success',
                'message' => 'Process found',
                'process_name' => $process->abbreviation_process_name
            ]);
        } else {
            return response()->json([
                'status' => 'fail',
                'message' => 'プロセスが見つかりません'
            ], 404);
        }
    }
}