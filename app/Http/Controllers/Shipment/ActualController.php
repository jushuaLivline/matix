<?php

namespace App\Http\Controllers\Shipment;

use App\Http\Controllers\Controller;
use App\Exports\ShipmentsExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Constants\Constant;
use Illuminate\Support\Facades\Log;

use App\Http\Requests\AddShipmentRequest;
use App\Http\Requests\CacheShipmentRequest;
use App\Http\Requests\UpdateShipmentRecordRequest;
use App\Models\ProductNumber;
use App\Models\ShipmentRecord;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\URL;

class ActualController extends Controller
{
    public function index(Request $request)
    {
        if(!$request->all()){
            $request->merge([
                'due_date_start' => $request->input('due_date_start'),
                'due_date_end' => $request->input('due_date_end'),
                'delivery_number_start' => $request->input('delivery_number_start'),
                'delivery_number_end' => $request->input('delivery_number_end'),
                'delivery_destination_code' => $request->input('delivery_destination_code'),
                'delivery_destination_name' => $request->input('delivery_destination_name'),
                'slip_no' => $request->input('slip_no'),
                'voucher_class' => $request->input('voucher_class'),
                'product_no' => $request->input('product_no'),
                'product_name' => $request->input('product_name'),
                'department_code' => $request->input('department_code'),
                'department_name' => $request->input('department_name'),
            ]);
        }

        //$paginationThreshold = Constant::PAGINATION_THRESHOLD;
        $shipment_record = ShipmentRecord::search($request)->paginateResults(10);

        return view('pages.shipment.actual.index', compact('shipment_record'));
    }

    public function exportExcel(Request $request)
    {
        $currentPage = $request->input('page', 1);

        $data = array_filter([
            'due_date_start' => $request->input('due_date_start'),
            'due_date_end' => $request->input('due_date_end'),
            'delivery_number_start' => $request->input('delivery_number_start'),
            'delivery_number_end' => $request->input('delivery_number_end'),
            'delivery_destination_code' => $request->input('delivery_destination_code'),
            'slip_no' => $request->input('slip_no'),
            'voucher_class' => $request->input('voucher_class'),
            'product_no' => $request->input('product_no'),
            'department_code' => $request->input('department_code'),
        ]);
        //$shipments = ShipmentRecord::search($data)->get();
        //Log::info('Records Fetched:', ['data' => $shipments->toArray()]);

        $shipments = ShipmentRecord::with('productNumber')
            ->search($data)
            ->paginateResults(10); // Ensures only 10 records per page

        // Get only the records for the current page
        $currentPageRecords = collect($shipments->items());

        // Map records for export
        $exportData = $currentPageRecords->map(function ($shipment) {
            return [
                $shipment->due_date ? \Carbon\Carbon::parse($shipment->due_date)->format('Y/m/d') : '-',
                $shipment->delivery_no,
                $shipment->customer?->customer_name,
                $shipment->slip_no,
                $shipment->acceptance,
                $shipment->drop_ship_code,
                $shipment->product_no,
                optional($shipment->productNumber)->product_name,
                $shipment->quantity,
                $shipment->department?->department_name,
                $shipment->remarks,
            ];
        });

        // Log::info('Exporting Excel with filters:', $data); // 
        $fileName = '出荷実績一覧' . now()->format('Ymd') . '.xlsx';
        return Excel::download(new ShipmentsExport($exportData), $fileName, \Maatwebsite\Excel\Excel::XLSX);
    }
    
    public function create()
    {
        $entries = [];
        $previousUrl = trim(URL::previousPath(), '/');
        $currentUrl = request()->path();

        // Clear session data if the user is coming from another link or leave this URL
        if ($previousUrl !== $currentUrl) {
            request()->session()->forget('sessionShipmentTempData');
        }

        $sessionShipmentTempData = session('sessionShipmentTempData', []);

        return view('pages.shipment.actual.create', compact($entries, $sessionShipmentTempData));
    }

    public function tempFormData(CacheShipmentRequest $request)
    {
        $data = $request->validated();
        $sessionData = $request->session()->get('sessionShipmentTempData', []);
        $sessionData[] = ['id' => uniqid(), ...$data];
        $request->session()->put('sessionShipmentTempData', $sessionData);
        return response()->json(['message' => 'Data stored successfully']);
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
        
        // Ensure product_number is an array before using foreach
        $productNumbers = $request->product_number ?? [];

        if (!is_array($productNumbers) || empty($productNumbers)) {
            return back()->with('error', '商品番号がありません。');
        }
        
        $serialNumber = 1;
        foreach ($request->product_number as $index => $productNumber) {
            $product = ProductNumber::where('part_number', $productNumber)->first();
            if($product){
                ShipmentRecord::create([
                    'shipment_no' => $shipmentNumber + $index + 1,  
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
        dd($request->all(), $request->validated(), 3333);

        ShipmentRecord::create([
            'slip_no' => $request->slipNo,
            'delivery_destination_code' => $request->customerCode,
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
}
