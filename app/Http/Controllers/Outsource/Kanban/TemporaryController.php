<?php

namespace App\Http\Controllers\Outsource\Kanban;

use App\Http\Controllers\Controller;

use App\Http\Requests\Outsource\TemporaryRequest;
use App\Models\KanbanMaster;
use App\Models\ProductNumber;
use App\Models\Customer;
use App\Models\OutsourcedProcessing;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;

class TemporaryController extends Controller
{
    public function create(Request $request)
    {
        $previousUrl = URL::previous();
        $currentUrl = $request->fullUrl();

        // Clear session data if the user is coming from another link or leave this URL
        if ($previousUrl !== $currentUrl) {
            $request->session()->forget('sessionKanbanData');
        }



        $kanbanDatas = KanbanMaster::selectRaw("management_no, number_of_accomodated")->get()->toArray();
        $productDatas = ProductNumber::selectRaw("part_number, uniform_number")->get()->toArray();

        $sessionKanbanData = session('sessionKanbanData', []);
        if (empty($sessionKanbanData)) session()->forget('sessionKanbanDataInstructionDate');
        $sessionKanbanDataInstructionDate = session('sessionKanbanDataInstructionDate');

        $supplier = null;
        // dd($sessionKanbanData);

        foreach ($sessionKanbanData as $kanbanData) {
            if (isset($kanbanData['supplier_code'])) {
                $latestSupplierCode = $kanbanData['supplier_code'];

                $supplier = Customer::selectRaw("customer_code, customer_name")
                    ->where("customer_code", $latestSupplierCode)
                    ->first();
            }
        }

        return view('pages.outsource.kanban.temporary.create', compact('kanbanDatas', 'productDatas', 'sessionKanbanData', 'sessionKanbanDataInstructionDate', 'supplier'));
    }
    public function searchByManagementNoAndInstructionDate(Request $request)
    {
        $instructionDate = $request->input('instruction_date');
        $managementNo = $request->input('management_no');

        $data = OutsourcedProcessing::with('product')->where([
            ['instruction_date', Carbon::createFromFormat('Ymd', $instructionDate)->format('Y-m-d')],
            ['management_no', $managementNo],
        ])->get();

        $sessionKanbanData = Session::get('sessionKanbanData', []);

        foreach ($data as $value) {
            $existingData = Arr::where($sessionKanbanData, function ($data) use ($value) {
                return $data['id'] == $value->id;
            });
            if (!empty($existingData)) continue;
            $sessionKanbanData[] = [
                'id' => $value->id,
                'management_no' => $value->management_no,
                'supplier_code' => $value->supplier_code,
                'product_code' => $value->product_code,
                'product_name' => $value->product->product_name,
                'instruction_number' => $value->instruction_number,
                'instruction_kanban_quantity' => $value->instruction_kanban_quantity,
                'arrival_quantity' => $value->arrival_quantity,
                'order_classification' => $value->order_classification,
            ];
        }

        Session::put('sessionKanbanData', $sessionKanbanData);
        Session::put('sessionKanbanDataInstructionDate', $instructionDate);
        return response()->json($data);
    }

    // Outsource processing 37 - bulk Saving data
    public function kanbanStoreData(Request $request)
    {
        $instructionDate = $request->input('instruction_date');
        $supplierCode = $request->input('supplier_code');
        $sessionData = $request->session()->get('sessionKanbanData', []);

        // Get the latest order number
        $latestOrderNumber = OutsourcedProcessing::max('order_no');
        $latestLotNumber = OutsourcedProcessing::max('lot');

        // Extract the 6-digit count from the start of the number
        $count = intval(substr($latestOrderNumber, 4, 6));
        $lotCount = intval(substr($latestLotNumber, 4, 6));

        foreach ($sessionData as $index => $data) {
            // Calculate the new order number
            $newOrderNumber = OutsourcedProcessing::generateOrderNo();
            $lotNumber = now()->format('md').($lotCount + 100 + ($index * 100));
            
            if (is_string($data['id'])) {
                OutsourcedProcessing::create([
                    'order_no' => $newOrderNumber,
                    'supplier_code' => $supplierCode,
                    'lot' => $lotNumber,
                    'management_no' => $data['management_no'],
                    'product_code' => $data['product_code'],
                    'order_classification' => $data['order_classification'],
                    'instruction_date' => Carbon::createFromFormat('Ymd', $instructionDate)->format('Y-m-d'),
                    'instruction_number' => $data['uniform_number'],
                    'instruction_kanban_quantity' => $data['instruction_kanban_quantity'],
                    'arrival_number' => $data['number_of_accomodated'],
                    'incoming_flight_number' => $data['instruction_number'],
                    'arrival_quantity' => $data['arrival_quantity'],
                ]);
                continue;
            }

            OutsourcedProcessing::find($data['id'])?->update([
                ...Arr::only($data, ['management_no', 'product_code', 'instruction_number',
                    'instruction_kanban_quantity', 'arrival_quantity', 'order_classification']),
                'supplier_code' => $supplierCode,
                'instruction_date' => Carbon::createFromFormat('Ymd', $instructionDate)->format('Y-m-d'),
            ]);
        }

        $request->session()->forget('sessionKanbanDataInstructionDate');
        $request->session()->forget('sessionKanbanData');

        return response()->json(['message' => 'データは正常に登録されました']);
    }
    
    public function kanbanUpdateData(Request $request)
    {
        $kanbanDataId = $request->input('kanban_data_id');
        $managementNumber = $request->input('management_no');
        $productCode = $request->input('product_code');
        $instructionNumber = $request->input('instruction_number');
        $instructionKanbanQuantity = $request->input('instruction_kanban_quantity');
        $arrivalQuantity = $request->input('arrival_quantity');

        OutsourcedProcessing::find($kanbanDataId)?->update([
            'management_no' => $managementNumber,
            'product_code' => $productCode,
            'instruction_number' => $instructionNumber,
            'instruction_kanban_quantity' => $instructionKanbanQuantity,
            'arrival_quantity' => $arrivalQuantity,
        ]);

        return response()->json(['message' => 'Data updated successfully']);
    }

    // Outsource processing 37 kanban temp data
    public function kanbanTempData(TemporaryRequest $request)
    {
        $supplierCode = $request->input('supplier_code');
        $managementNumber = $request->input('management_no');
        $productCode = $request->input('product_code');
        $productName = $request->input('product_name');
        $uniformNumber = $request->input('uniform_number');
        $instructionDate = $request->input('instruction_date');
        $instructionNumber = $request->input('instruction_number');
        $numberOfAccommodated = $request->input('number_of_accomodated');
        $instructionKanbanQuantity = $request->input('instruction_kanban_quantity');
        $arrivalQuantity = $request->input('arrival_quantity');

        $newData = [
            'id' => uniqid(), // Generate a unique ID
            'management_no' => $managementNumber,
            'supplier_code' => $supplierCode,
            'product_code' => $productCode,
            'product_name' => $productName,
            'uniform_number' => $uniformNumber,
            'instruction_number' => $instructionNumber,
            'number_of_accomodated' => $numberOfAccommodated,
            'instruction_kanban_quantity' => $instructionKanbanQuantity,
            'arrival_quantity' => $arrivalQuantity,
            'order_classification' => 2
        ];

        $sessionData = $request->session()->get('sessionKanbanData', []);
        $sessionData[] = $newData;
        $request->session()->put('sessionKanbanDataInstructionDate', $instructionDate);
        $request->session()->put('sessionKanbanData', $sessionData);

        return response()->json(['message' => 'Data stored successfully']);
    }

    // Outsource processing 37 kanban delete data
    public function kanbanDeleteData(Request $request, $instructionDataId)
    {
        $sessionData = $request->session()->get('sessionKanbanData', []);

        foreach ($sessionData as $key => $data) {
            if ($data['id'] == $instructionDataId) {
                array_splice($sessionData, $key, 1);
                break;
            }
        }

        $request->session()->put('sessionKanbanData', $sessionData);

        return response()->json(['message' => 'Data deleted successfully']);
}

    // Outsource processing 37 kanban update data
    public function kanbanTempUpdateData(TemporaryRequest $request)
    {
        $kanbanDataId = $request->input('kanban_data_id');
        $managementNumber = $request->input('management_no');
        $productCode = $request->input('product_code');
        $productName = $request->input('product_name');
        $uniformNumber = $request->input('uniform_number');
        $instructionDate = $request->input('instruction_date');
        $instructionNumber = $request->input('instruction_number');
        $numberOfAccommodated = $request->input('number_of_accomodated');
        $instructionKanbanQuantity = $request->input('instruction_kanban_quantity');
        $arrivalQuantity = $request->input('arrival_quantity');

        $sessionData = $request->session()->get('sessionKanbanData', []);

        foreach ($sessionData as &$data) {
            if ($data['id'] == $kanbanDataId) {
                $data['management_no'] = $managementNumber;
                $data['product_code'] = $productCode;
                $data['product_name'] = $productName;
                $data['uniform_number'] = $uniformNumber;
                $data['instruction_number'] = $instructionNumber;
                $data['number_of_accomodated'] = $numberOfAccommodated;
                $data['instruction_kanban_quantity'] = $instructionKanbanQuantity;
                $data['arrival_quantity'] = $arrivalQuantity;
                break;
            }
        }

        $request->session()->put('sessionKanbanData', $sessionData);
        $request->session()->put('sessionKanbanDataInstructionDate', $instructionDate);

        return response()->json(['message' => 'Data updated successfully']);
    }

    public function kanbanFetchUniformCapacity(Request $request)
    {
        $kanban = KanbanMaster::where([
            'management_no' => $request->input('management_no'),
            'part_number' => $request->input('product_code')
        ])
        ->orderBy('created_at', 'desc')
        ->first();

        if($kanban){
            return response()->json([
                'status' => 'success',
                'result' => $kanban
            ]);
        }else{
            return response()->json([
                'status' => 'fail',
                'message' => '管理番号と製品コードが無効です'
            ]);
        }
    }

}