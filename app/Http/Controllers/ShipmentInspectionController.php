<?php

namespace App\Http\Controllers;

use App\Exports\ShipmentsExport;
use App\Http\Requests\AddShipmentRequest;
use App\Http\Requests\CacheShipmentRequest;
use App\Http\Requests\SearchShipmentRequest;
use App\Http\Requests\UpdateShipmentRecordRequest;
use App\Models\Department;
use App\Models\Line;
use App\Models\ProductNumber;
use App\Models\ShipmentRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\URL;
use Maatwebsite\Excel\Facades\Excel;

class ShipmentInspectionController extends Controller
{
    public function shipmentEntry()
    {
        // $entries = ShipmentRecord::paginate(10);
        $entries = [];
        $previousUrl = trim(URL::previousPath(), '/');
        $currentUrl = request()->path();

        // Clear session data if the user is coming from another link or leave this URL
        if ($previousUrl !== $currentUrl) {
            request()->session()->forget('sessionShipmentTempData');
        }

        $sessionShipmentTempData = session('sessionShipmentTempData', []);

        return view('pages.shipmentInspections.shipment_entry', [
            'entries' => $entries,
            'sessionShipmentTempData' => $sessionShipmentTempData,
        ]);
    }

    public function tempFormData(CacheShipmentRequest $request)
    {
        $data = $request->validated();
        $sessionData = $request->session()->get('sessionShipmentTempData', []);
        $sessionData[] = ['id' => uniqid(), ...$data];
        $request->session()->put('sessionShipmentTempData', $sessionData);
        return response()->json(['message' => 'Data stored successfully']);
    }

    public function updateTempFormData(Request $request)
    {
        $dataId = $request->input('temp_data_id');
        $part_no = $request->input('productNumber');
        $part_name = $request->input('productName');
        $quantity = $request->input('quantity');
        $remarks = $request->input('remarks');

        $sessionData = $request->session()->get('sessionShipmentTempData', []);

        foreach ($sessionData as &$data) {
            if ($data['id'] === $dataId) {
                $data['part_no'] = $part_no;
                $data['part_name'] = $part_name;
                $data['quantity'] = $quantity;
                $data['remarks'] = $remarks;
                break;
            }
        }

        $request->session()->put('sessionShipmentTempData', $sessionData);

        return response()->json(['message' => 'Data updated successfully']);
    }

    public function deleteTempFormData(Request $request, $tempDataId)
    {
        $sessionData = $request->session()->get('sessionShipmentTempData', []);

        foreach ($sessionData as $key => $data) {
            if ($data['id'] === $tempDataId) {
                array_splice($sessionData, $key, 1);
                break;
            }
        }

        $request->session()->put('sessionShipmentTempData', $sessionData);

        return response()->json(['message' => 'Data deleted successfully']);
    }

    public function storeFormData(Request $request)
    {
        $sessionData = $request->session()->get('sessionShipmentTempData', []);

        $currentYearMonth = now()->format('ym');
        $latestShipmentNumber = ShipmentRecord::where('shipment_no', 'like', "{$currentYearMonth}%")->max('shipment_no');
    
        if ($latestShipmentNumber) {
            $count = intval(substr($latestShipmentNumber, 4, 6)) + 1;
            $shipmentNumber = $currentYearMonth . str_pad($count, 6, '0', STR_PAD_LEFT);
        } else {
            $shipmentNumber = $currentYearMonth . '000001';
        }
    
        $serialNumber = 1;
        foreach ($sessionData as $index => $data) {
            $product = ProductNumber::where('part_number', $data['part_no'])->first();
            
            ShipmentRecord::create([
                'shipment_no' => $shipmentNumber,  
                'serial_number' => $serialNumber,
                'slip_no' => $request->slipNo,
                'voucher_class' => 1,  
                'delivery_destination_code' => $request->customerCode,
                'due_date' => $request->dueDate,
                'delivery_no' => $request->deliveryNo,
                'acceptance' => $request->acceptance ?? '',
                'drop_ship_code' => $request->dropShip ?? '',
                'product_no' => $data['part_no'],
                'quantity' => $data['quantity'],
                'remarks' => $data['remarks'],
                'plant' => $request->plant ?? '',
                'department_code' => $product->department_code,
                'line_code' => $product->line_code,
            ]);
            
            $serialNumber++;
        }

        $request->session()->forget('sessionShipmentTempData');

        return response()->json(['message' => 'データは正常に登録されました']);
    }

    public function storeShipmentRecord(Request $request)
    {
        $currentYearMonth = now()->format('ym');
        $latestShipmentNumber = ShipmentRecord::where('shipment_no', 'like', "{$currentYearMonth}%")->max('shipment_no');
    
        if ($latestShipmentNumber) {
            $count = intval(substr($latestShipmentNumber, 4, 6)) + 1;
            $shipmentNumber = $currentYearMonth . str_pad($count, 6, '0', STR_PAD_LEFT);
        } else {
            $shipmentNumber = $currentYearMonth . '000001';
        }
    
        $serialNumber = 1;
        foreach ($request->product_number as $index => $productNumber) {
            $product = ProductNumber::where('part_number', $productNumber)->first();
            if($product){
                ShipmentRecord::create([
                    'shipment_no' => $shipmentNumber,  
                    'serial_number' => $serialNumber,
                    'slip_no' => $request->slip_no,
                    'voucher_class' => 1,  
                    'delivery_destination_code' => $request->customer_code,
                    'due_date' => $request->instruction_date,
                    'delivery_no' => $request->delivery_no,
                    'acceptance' => $request->acceptance ?? '',
                    'drop_ship_code' => $request->supplier_code ?? '',
                    'product_no' => $productNumber,
                    'quantity' => $request->quantity[$index],
                    'remarks' => $request->remarks[$index],
                    'plant' => $request->plant ?? '',
                    'department_code' => $product->department_code,
                    'line_code' => $product->line_code,
                ]);
                
                $serialNumber++;
            }
        }

        return back()->with('success', 'データは正常に登録されました');
    }

    public function addShipmentEntry(AddShipmentRequest $request)
    {
        ShipmentRecord::create([
            'slip_no' => $request->slipNo,
            'customer_code' => $request->customerCode,
            'due_date' => $request->dueDate,
            'delivery_no' => $request->deliveryNo,
            'acceptance' => $request->acceptance,
            'drop_ship_code' => $request->dropShipCode,
            'part_no' => $request->productNumber,
            'quantity' => $request->quantity,
            'remarks' => $request->remarks,
            'plant' => $request->plant,
        ]);

        return response()->json(['status' => true]);
    }

    public function updateShipmentEntry(UpdateShipmentRecordRequest $request, $id)
    {
        $data = $request->validated();
        ShipmentRecord::find($id)->update($data);
        return Response::noContent();
    }
    
    public function shipmentSummary(Request $request) {
        $type = $request->type ?? 1;
        $entries = null;
    
        if (count($request->query()) > 0) {
            switch ($type) {
                case 1: // By Department
                    $department_code_prefix = substr($request->department_code ?? '', 0, 4) . '00';
    
                    $entries = Department::selectRaw('substr(code,1,4) as alpha, id, code, name')
                        ->when($department_code_prefix != '0000', function ($query) use ($department_code_prefix) {
                            $query->where('code', $department_code_prefix);
                        })
                        ->groupByRaw('substr(code,1,4)')
                        ->withSum(['shipments' => function($query) use ($request) {
                            $query->search($request);
                        }], 'quantity')
                        ->having('shipments_sum_quantity', '>', 0) 
                        ->paginate(20);
                    break;
    
                case 2: // By Department (using shipments)
                    $entries = Department::selectRaw('id, code, name')
                        ->whereHas('shipments', function ($q) use ($request) {
                            $q->search($request);
                        })
                        ->withSum(['shipments' => function($query) use ($request) {
                            $query->search($request);
                        }], 'quantity')
                        ->paginate(20);
                    break;
    
                case 3: // By Line
                    $entries = Line::with('department')
                        ->whereHas('shipments', function ($q) use ($request) {
                            $q->search($request);
                        })
                        ->withSum(['shipments' => function($query) use ($request) {
                            $query->search($request);
                        }], 'quantity')
                        ->paginate(20);
                    break;
    
                case 4: // By Product Number
                    $entries = ProductNumber::whereHas('shipments', function ($q) use ($request) {
                            $q->search($request);
                        })
                        ->withSum(['shipments' => function($query) use ($request) {
                            $query->search($request);
                        }], 'quantity')
                        ->paginate(20);
                    break;
    
                default:
                    break;
            }
        }
    
        return view('pages.shipmentInspections.shipment_summary', [
            'entries' => $entries ?? [],
            'type' => $type,
        ]);
    }    

    public function destroy($id)
    {
        ShipmentRecord::find($id)->delete();
        return Response::noContent();
        return view('pages.shipmentInspections.shipment_result_search', [
            'entries' => $entries,
            'total' => $total,
        ]);
    }
}
