<?php

namespace App\Http\Controllers;

use App\Exports\ArrivalResultExport;
use App\Exports\OutsourcedProcessingOrderSearchExport;
use App\Exports\OutsourceMaterialDefectExport;
use App\Exports\OutsourceProcessDefectExport;
use App\Exports\OutsourceProcessingNonArrivalExport;
use App\Helpers\RequestHelper;
use App\Models\Code;
use App\Models\Customer;
use App\Models\KanbanMaster;
use App\Models\OutsourcedProcessing;
use App\Models\OutsourceMaterialFailure;
use App\Models\OutsourceProcessFailure;
use App\Models\Process;
use App\Models\ProductNumber;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

class OutsourceController extends Controller
{
    // Outsource processing 38
    // Moved to Outsource/FractionController

    // Outsource 41, transferred to Outsource/Delivery/ReissueController

     // Outsource processing 48
     public function acceptanceInputProcessing(Request $request)
    {
        if (count($request->query()) > 0) {
            $managementNo = $request->input('management_no', []);
            $supplierCode = $request->input('supplier_process_code');
            $arrivalDay = $request->input('arrival_day');
            $incomingFlightNo = $request->input('incoming_flight_number');

            // Check if the request contains data or if there are management numbers selected
            
            $outsourcedDatas = OutsourcedProcessing::query()
                        ->join('product_numbers', 'product_numbers.part_number', '=', 'outsourced_processings.product_code')
                        ->select('outsourced_processings.*', 'product_numbers.edited_part_number')
                        ->where(function ($subquery) use ($managementNo) {
                            foreach ($managementNo as $selectedManagementNo) {
                                //$subquery->orWhere('management_no', $selectedManagementNo);
                            }
                        })
                        ->when($arrivalDay, function ($query) use ($arrivalDay) {
                            //return $query->where('arrival_day', Carbon::parse($arrivalDay)->format('Y-m-d'));
                        })
                        ->when($supplierCode, function ($query) use ($supplierCode) {
                           // return $query->where('supplier_process_code', $supplierCode);
                        })
                        ->when($incomingFlightNo, function ($query) use ($incomingFlightNo) {
                            return $query->where('incoming_flight_number', $incomingFlightNo);
                        })
                        ->paginate(20);

     
                return view('pages.outsources.acceptance_input_processing', [
                    'outsourcedDatas' => $outsourcedDatas,
                ]);
        }
        
        return view('pages.outsources.acceptance_input_processing', [
            'outsourcedDatas' => $outsourcedDatas ?? [],
        ]);
    }

    // Outsource processing 48 updateOutsourceRecords
    public function updateOutsourceRecords(Request $request)
    {
        $outsourcedDataIds = $request->input('outsourced_data_ids', []);
        $arrivalQuantities = $request->input('arrival_quantity', []);
        $arrivalDay = $request->input('arrival_day', []);
        $incomingFlightNumber = $request->input('incoming_flight_number', []);

        // Perform the update for each outsourced data record
        foreach ($outsourcedDataIds as $key => $outsourcedDataId) {
            $outsourcedData = OutsourcedProcessing::find($outsourcedDataId);

            $kanbanMaster = KanbanMaster::where('management_no', $outsourcedData->management_no)->first();

            if ($outsourcedData) {
                $arrivalQuantity = $arrivalQuantities[$key];
                $instructionNumber = $outsourcedData->instruction_number;

                // Update the fields
                if($instructionNumber <= $arrivalQuantity){
                    $outsourcedData->arrival_number = $request->arrival_number;
                    $outsourcedData->arrival_day = $arrivalDay;
                    $outsourcedData->incoming_flight_number = $incomingFlightNumber;
                    $outsourcedData->arrival_quantity = $arrivalQuantity;
                    $outsourcedData->save();
                }

                if ($instructionNumber > $arrivalQuantity) {
                    // Create new OutsourcedProcessing for partial delivery
                    $partialDelivery = new OutsourcedProcessing();
                    $partialDelivery->order_number = $outsourcedData->order_number + 1; // Increment the order number
                    $partialDelivery->management_no = $outsourcedData->management_no;
                    $partialDelivery->supplier_code = $outsourcedData->supplier_code;
                    $partialDelivery->product_code = $outsourcedData->product_code;
                    $partialDelivery->instruction_date = $outsourcedData->instruction_date;
                    $partialDelivery->instruction_number = $instructionNumber - $arrivalQuantity;
                    $partialDelivery->instruction_kanban_quantity = $instructionNumber - $arrivalQuantity / $kanbanMaster->number_of_accomodated;
                    $partialDelivery->lot = 1;
                    $partialDelivery->arrival_number = null;
                    $partialDelivery->arrival_day = null;
                    $partialDelivery->incoming_flight_number = null;
                    $partialDelivery->arrival_quantity = $partialDelivery->instruction_number;
                    $partialDelivery->save();
                }

                
            }
        }

        // Redirect back or to a success page
        return redirect()->back()->with('success', 'Outsource records updated successfully');
    }

    // Outsource processing 49
    public function arrivalResultList(Request $request)
    {
        if (count($request->query()) > 0) {
            $arrivalStart = $request->arrival_day_start;
            $arrivalEnd = $request->arrival_day_end;
            $flightNumberStart = $request->incoming_flight_number_start;
            $flightNumberEnd = $request->incoming_flight_number_end;

            // Eager load any related models used in the view to prevent N+1 issue
            $arrivalResultLists = OutsourcedProcessing::with(['supplier', 'product'])
                ->when($request->input('supplier_code'), fn ($query) => $query->where('supplier_code', $request->input('supplier_code')))
                ->when($request->input('product_code'), fn ($query) => $query->where('product_code', $request->input('product_code')))
                ->when($request->input('order_number'), fn ($query) => $query->where('order_no', $request->input('order_number')))
                ->when($arrivalStart, function ($query) use ($arrivalStart, $arrivalEnd) {
                    $arrivalStartFormatted = Carbon::parse($arrivalStart)->format('Y-m-d');
                    if ($arrivalEnd) {
                        $arrivalEndFormatted = Carbon::parse($arrivalEnd)->format('Y-m-d');
                        return $query->whereBetween('arrival_day', [$arrivalStartFormatted, $arrivalEndFormatted]);
                    } else {
                        return $query->where('arrival_day', $arrivalStartFormatted);
                    }
                })
                ->when($flightNumberStart, function ($query) use ($flightNumberStart, $flightNumberEnd) {
                    if ($flightNumberEnd) {
                        return $query->whereBetween('incoming_flight_number', [$flightNumberStart, $flightNumberEnd]);
                    } else {
                        return $query->where('incoming_flight_number', $flightNumberStart);
                    }
                })
                ->paginate(20);
        }

        return view('pages.outsources.arrival_result_list', [
            'arrivalResultLists' => $arrivalResultLists ?? [],
        ]);
    }

    //Outsource 49 export
    public function arrivalResultListExport(Request $request)
    {
        $arrivalStart = $request->arrival_day_start;
        $arrivalEnd = $request->arrival_day_end;
        $flightNumberStart = $request->incoming_flight_number_start;
        $flightNumberEnd = $request->incoming_flight_number_end;


        $arrivalResultLists = OutsourcedProcessing::query()
            ->when($request->input('supplier_code'), fn ($query) => $query->where('supplier_code', $request->input('supplier_code')))
            ->when($request->input('product_code'), fn ($query) => $query->where('product_code', $request->input('product_code')))
            ->when($request->input('order_number'), fn ($query) => $query->where('order_no', $request->input('order_number')))
            ->when($arrivalStart, function ($query) use ($arrivalStart, $arrivalEnd) {
                if ($arrivalEnd) {
                    return $query->whereBetween('arrival_day', [Carbon::parse($arrivalStart)->format('Y-m-d'), Carbon::parse($arrivalEnd)->format('Y-m-d')]);
                } else {
                    return $query->where('arrival_day', Carbon::parse($arrivalStart)->format('Y-m-d'));
                }
            })
            ->when($flightNumberStart, function ($query) use ($flightNumberStart, $flightNumberEnd) {
                if ($flightNumberEnd) {
                    return $query->whereBetween('incoming_flight_number', [$flightNumberStart, $flightNumberEnd]);
                } else {
                    return $query->where('incoming_flight_number', $flightNumberStart);
                }
            })
            ->get();

        $fileName = '外注加工入荷実績検索_'.now()->format('Ymd').'.xlsx';
        return Excel::download(new ArrivalResultExport($arrivalResultLists), $fileName , \Maatwebsite\Excel\Excel::XLSX);
    }

    // Outsource processing 50
    public function outsourcedProcessingCancel(Request $request)
    {
        if (count($request->query()) > 0) {
            $arrivalStart = $request->arrival_day_start;
            $arrivalEnd = $request->arrival_day_end;
            $flightNumberStart = $request->incoming_flight_number_start;
            $flightNumberEnd = $request->incoming_flight_number_end;

            $query = OutsourcedProcessing::query()
                ->with(['product', 'kanbanMaster.process'])
                // ->whereNull('arrival_number')
                // ->whereNull('arrival_day')
                // ->whereNull('incoming_flight_number')
                ->when($request->input('supplier_code'), fn ($query, $supplierCode) => $query->where('supplier_code', $supplierCode))
                ->when($request->input('product_id'), fn ($query, $productId) => $query->where('product_code', $productId))
                ->when($request->input('order_number'), fn ($query, $orderNumber) => $query->where('order_no', $orderNumber))
                ->when($arrivalStart, function ($query) use ($arrivalStart, $arrivalEnd) {
                    if ($arrivalEnd) {
                        return $query->whereBetween('arrival_day', [Carbon::parse($arrivalStart)->format('Y-m-d'), Carbon::parse($arrivalEnd)->format('Y-m-d')]);
                    } else {
                        return $query->where('arrival_day', Carbon::parse($arrivalStart)->format('Y-m-d'));
                    }
                })
                ->when($flightNumberStart, function ($query) use ($flightNumberStart, $flightNumberEnd) {
                    if ($flightNumberEnd) {
                        return $query->whereBetween('incoming_flight_number', [$flightNumberStart, $flightNumberEnd]);
                    } else {
                        return $query->where('incoming_flight_number', $flightNumberStart);
                    }
                });

            // Get the results
            $arrivalResultLists = $query->paginate(30);
        }
    
        return view('pages.outsources.outsourced_processing_cancel', [
            'arrivalResultLists' => $arrivalResultLists ?? [],
        ]);
    }


    // Outsource processing 50 - update cancel records
    public function updateprocessCancelRecords(Request $request)
    {
        $outsourcedDataIds = $request->input('outsourced_data_ids', []);

        OutsourcedProcessing::whereIn('id', $outsourcedDataIds)
            ->update([
                'arrival_number' => null,
                'arrival_day' => null,
                'incoming_flight_number' => null,
                'arrival_quantity' => null,
            ]);

        return redirect()->back()->with('success', 'Outsource process cancel records updated successfully');
    }

    // Outsource processing 51
    public function outsourcedProcessingNonArrivalSearch(Request $request)
    {
        if (count($request->query()) > 0) {
        //    return  $request->all();
            // Get the input values
            $supplierCode = $request->supplier_code;
            $productId = $request->product_code;
            $orderNumber = $request->order_number;
            $instructionDateFrom = $request->instruction_date_from;
            $instructionDateTo = $request->instruction_date_to;
            $incomingFlightNumberStart = $request->incoming_flight_number_start;
            $incomingFlightNumberEnd = $request->incoming_flight_number_end;

            // Query the OutsourcedProcessing model
            $searchResults = OutsourcedProcessing::query()
                ->with(['product', 'kanbanMaster.process'])
                ->when($supplierCode, fn ($query) => $query->where('supplier_code', $supplierCode))
                ->when($productId, fn ($query) => $query->where('product_code', $productId))
                ->when($orderNumber, fn ($query) => $query->where('order_no', $orderNumber))
                ->when($instructionDateFrom, function ($query) use ($instructionDateFrom, $instructionDateTo) {
                    if ($instructionDateTo) {
                        return $query->whereBetween('instruction_date', [Carbon::parse($instructionDateFrom)->format('Y-m-d'), Carbon::parse($instructionDateTo)->format('Y-m-d')]);
                    } else {
                        return $query->where('instruction_date', Carbon::parse($instructionDateFrom)->format('Y-m-d'));
                    }
                })
                ->when($incomingFlightNumberStart, function ($query) use ($incomingFlightNumberStart, $incomingFlightNumberEnd) {
                    if ($incomingFlightNumberEnd) {
                        return $query->whereBetween('incoming_flight_number', [$incomingFlightNumberStart, $incomingFlightNumberEnd]);
                    } else {
                        return $query->where('incoming_flight_number', $incomingFlightNumberStart);
                    }
                })
                ->whereNull('arrival_day')
                ->paginate(20);
        }

        return view('pages.outsources.outsourced_processing_non_arrival_search', [
            'searchResults' => $searchResults ?? [],
        ]);
    }

    // Outsource processing 51 Export
    public function outsourcedProcessingNonArrivalExport(Request $request)
    {
        // Get the input values
        $supplierCode = $request->supplier_code;
        $productId = $request->product_code;
        $orderNumber = $request->order_number;
        $instructionDateFrom = $request->instruction_date_from;
        $instructionDateTo = $request->instruction_date_to;
        $incomingFlightNumberStart = $request->incoming_flight_number_start;
        $incomingFlightNumberEnd = $request->incoming_flight_number_end;

        // Query the OutsourcedProcessing model
        $searchResults = OutsourcedProcessing::query()
            ->with(['product', 'kanbanMaster.process'])
            ->when($supplierCode, fn ($query) => $query->where('supplier_code', $supplierCode))
            ->when($productId, fn ($query) => $query->where('product_code', $productId))
            ->when($orderNumber, fn ($query) => $query->where('order_number', $orderNumber))
            ->when($instructionDateFrom, function ($query) use ($instructionDateFrom, $instructionDateTo) {
                if ($instructionDateTo) {
                    return $query->whereBetween('instruction_date', [Carbon::parse($instructionDateFrom)->format('Y-m-d'), Carbon::parse($instructionDateTo)->format('Y-m-d')]);
                } else {
                    return $query->where('instruction_date', Carbon::parse($instructionDateFrom)->format('Y-m-d'));
                }
            })
            ->when($incomingFlightNumberStart, function ($query) use ($incomingFlightNumberStart, $incomingFlightNumberEnd) {
                if ($incomingFlightNumberEnd) {
                    return $query->whereBetween('incoming_flight_number', [$incomingFlightNumberStart, $incomingFlightNumberEnd]);
                } else {
                    return $query->where('incoming_flight_number', $incomingFlightNumberStart);
                }
            })
            ->whereNull('arrival_day')
            ->get();

        $fileName = '外注加工未入荷検索_'.now()->format('Ymd').'.xlsx';
        return Excel::download(new OutsourceProcessingNonArrivalExport($searchResults), $fileName , \Maatwebsite\Excel\Excel::XLSX);

       
    }
    
    //Outsource 53
    public function materialDefectList(Request $request)
    {   
        if($request->all())
        {

            RequestHelper::processRequest($request);
            $returnDateFrom = $request->return_date_from;
            $returnDateTo = $request->return_date_to;
            $inputDateFrom = $request->input_date_from;
            $inputDateTo = $request->input_date_to;
            $materialCode = $request->product_code;
            $supplierCode = $request->supplier_code;
            $processCode = $request->process_code;
            $slipNo = $request->slip_no;
    
            
            $items = OutsourceMaterialFailure::query()
                ->with(['material', 'material.processUnitPrice', 'process', 'supplier'])
                ->when($supplierCode, function ($query) use ($supplierCode){
                        return $query->where('supplier_code', $supplierCode);
                })
                ->when($materialCode, function ($query) use ($materialCode){
                        return $query->where('material_number', $materialCode);
                })
                ->when($processCode, function ($query) use ($processCode){
                        return $query->where('process_code', $processCode);
                })
                ->when($slipNo, function ($query) use ($slipNo){
                        return $query->where('slip_no', $slipNo);
                })
                ->when($returnDateFrom, function ($query) use ($returnDateFrom, $returnDateTo) {
                    if ($returnDateTo) {
                        return $query->whereBetween('return_date', [Carbon::parse($returnDateFrom)->format('Y-m-d'), Carbon::parse($returnDateTo)->format('Y-m-d')]);
                    } else {
                        return $query->whereDate('return_date', Carbon::parse($returnDateFrom)->format('Y-m-d'));
                    }
                })
                ->when($inputDateFrom, function ($query) use ($inputDateFrom, $inputDateTo) {
                    if ($inputDateTo) {
                        return $query->whereBetween('created_at', [Carbon::parse($inputDateFrom)->format('Y-m-d'), Carbon::parse($inputDateTo)->format('Y-m-d')]);
                    } else {
                        return $query->whereDate('created_at', Carbon::parse($inputDateFrom)->format('Y-m-d'));
                    }
                })  
                ->paginate(20);

            $reasons = Code::selectRaw('
                            division,
                            code,
                            name    
                        ')
                        ->whereDivision('材不理由')
                        ->get();
    
            return view('pages.outsources.material_defect_list', [
                        'items' => $items,
                        'reasons' => $reasons
                    ]);
        }else{
            $items = [];
            $reasons = [];
            return view('pages.outsources.material_defect_list', [
                'items' => $items,
                'reasons' => $reasons
            ]);
        }   
    }

    public function materialDefectUpdate(Request $request)
    {
        try {
            // Validate and retrieve the data from the request
            $data = $request->validate([
                'id' => 'required|integer',
                'reason_code' => 'required', // Add any validation rules as needed
                'quantity' => 'required|numeric',
                'processing_rate' => 'required|numeric',
            ]);

            // Update the corresponding record in the database
            $outsourceMaterialFailure = OutsourceMaterialFailure::findOrFail($data['id']);
            $outsourceMaterialFailure->update([
                'reason_code' => $data['reason_code'],
                'quantity' => $data['quantity'],
                'processing_rate' => $data['processing_rate'],
            ]);

            // You can optionally return a success response
            return response()->json(['message' => 'Data updated successfully']);
        } catch (\Exception $e) {
            // Handle any exceptions or errors that may occur during the update
            return response()->json(['message' => 'Failed to update data'], 500);
        }
    }

    //Outsource 53 export
    public function materialDefectExport(Request $request)
    {
        $returnDateFrom = $request->return_date_from;
            $returnDateTo = $request->return_date_to;
            $inputDateFrom = $request->input_date_from;
            $inputDateTo = $request->input_date_to;
            $materialCode = $request->product_code;
            $supplierCode = $request->supplier_code;
            $processCode = $request->process_code;
            $slipNo = $request->slip_no;
    
            
            $items = OutsourceMaterialFailure::query()
                ->with(['material', 'process', 'supplier'])
                ->when($supplierCode, function ($query) use ($supplierCode){
                        return $query->where('supplier_code', $supplierCode);
                })
                ->when($materialCode, function ($query) use ($materialCode){
                        return $query->where('material_number', $materialCode);
                })
                ->when($processCode, function ($query) use ($processCode){
                        return $query->where('process_code', $processCode);
                })
                ->when($slipNo, function ($query) use ($slipNo){
                        return $query->where('slip_code', $slipNo);
                })
                ->when($returnDateFrom, function ($query) use ($returnDateFrom, $returnDateTo) {
                    if ($returnDateTo) {
                        return $query->whereBetween('return_date', [Carbon::parse($returnDateFrom)->format('Y-m-d'), Carbon::parse($returnDateTo)->format('Y-m-d')]);
                    } else {
                        return $query->where('return_date', Carbon::parse($returnDateFrom)->format('Y-m-d'));
                    }
                })
                ->when($inputDateFrom, function ($query) use ($inputDateFrom, $inputDateTo) {
                    if ($inputDateTo) {
                        return $query->whereBetween('created_at', [Carbon::parse($inputDateFrom)->format('Y-m-d'), Carbon::parse($inputDateTo)->format('Y-m-d')]);
                    } else {
                        return $query->whereDate('created_at', Carbon::parse($inputDateFrom)->format('Y-m-d'));
                    }
                })  
                ->get();

        $fileName = '材料欠陥記録リスト_'.now()->format('Ymd').'.xlsx';
        return Excel::download(new OutsourceMaterialDefectExport($items), $fileName , \Maatwebsite\Excel\Excel::XLSX);
    }


    //Outsource 55 index
    public function machiningDefectList(Request $request)
    {
        if($request->all())
        {
            RequestHelper::processRequest($request);
            $disposalDateFrom = $request->disposal_date_from;
            $disposalDateTo = $request->disposal_date_to;
            $inputDateFrom = $request->input_date_from;
            $inputDateTo = $request->input_date_to;
            $partNumber = $request->product_code;
            $processCode = $request->process_code;
            $slipNo = $request->slip_no;
    
            
            $items = OutsourceProcessFailure::query()
                ->with(['product', 'product.customer', 'product.processUnitPrice', 'process'])
                ->when($partNumber, function ($query) use ($partNumber){
                        return $query->where('part_number', $partNumber);
                })
                ->when($processCode, function ($query) use ($processCode){
                        return $query->where('process_code', $processCode);
                })
                ->when($slipNo, function ($query) use ($slipNo){
                        return $query->where('slip_no', $slipNo);
                })
                ->when($disposalDateFrom, function ($query) use ($disposalDateFrom, $disposalDateTo) {
                    if ($disposalDateTo) {
                        return $query->whereBetween('disposal_date', [Carbon::parse($disposalDateFrom)->format('Y-m-d'), Carbon::parse($disposalDateTo)->format('Y-m-d')]);
                    } else {
                        return $query->whereDate('disposal_date', Carbon::parse($disposalDateFrom)->format('Y-m-d'));
                    }
                })
                ->when($inputDateFrom, function ($query) use ($inputDateFrom, $inputDateTo) {
                    if ($inputDateTo) {
                        return $query->whereBetween('created_at', [Carbon::parse($inputDateFrom)->format('Y-m-d'), Carbon::parse($inputDateTo)->format('Y-m-d')]);
                    } else {
                        return $query->whereDate('created_at', Carbon::parse($inputDateFrom)->format('Y-m-d'));
                    }
                }) 
                ->paginate(20);

            $datas = ProductNumber::selectRaw("
                    mst_product_numbers.part_number as product_code, 
                    mst_product_numbers.product_name as product_name,
                    mst_process_unit_prices.processing_unit_price as process_unit_price
                ")
                ->where('product_category', 1)
                ->leftJoin('process_unit_prices', function ($join) {
                    $join->on('process_unit_prices.part_number', 'LIKE', 'product_numbers.part_number');
                })
                ->get();

            return view('pages.outsources.machining_defect_list', [
                        'items' => $items,
                        'datas' => $datas
                    ]);
        }else{
            $items = [];
            $datas = [];
            return view('pages.outsources.machining_defect_list', [
                'items' => $items,
                'datas' => $datas
            ]);
        }
    }

    public function machiningDefectItemUpdate(Request $request)
    {
        try {
            // Validate and retrieve the data from the request
            $data = $request->validate([
                'id' => 'required|integer',
                'part_number' => 'required', // Add any validation rules as needed
                'quantity' => 'required|numeric',
                'slip_no' => 'required|numeric',
            ]);

            // Update the corresponding record in the database
            $outsourceProcessFailure = OutsourceProcessFailure::findOrFail($data['id']);
            $outsourceProcessFailure->update([
                'part_number' => $data['part_number'],
                'quantity' => $data['quantity'],
                'slip_no' => $data['slip_no'],
            ]);

            // You can optionally return a success response
            return response()->json(['message' => 'Data updated successfully']);
        } catch (\Exception $e) {
            // Handle any exceptions or errors that may occur during the update
            return response()->json(['message' => 'Failed to update data'], 500);
        }
    }

    public function machiningDefectExport(Request $request)
    {
        RequestHelper::processRequest($request);
        $disposalDateFrom = $request->disposal_date_from;
        $disposalDateTo = $request->disposal_date_to;
        $inputDateFrom = $request->input_date_from;
        $inputDateTo = $request->input_date_to;
        $partNumber = $request->product_code;
        $processCode = $request->process_code;
        $slipNo = $request->slip_no;

        
        $items = OutsourceProcessFailure::query()
                ->with(['product', 'process'])
                ->when($partNumber, function ($query) use ($partNumber){
                        return $query->where('part_number', $partNumber);
                })
                ->when($processCode, function ($query) use ($processCode){
                        return $query->where('process_code', $processCode);
                })
                ->when($slipNo, function ($query) use ($slipNo){
                        return $query->where('slip_no', $slipNo);
                })
                ->when($disposalDateFrom, function ($query) use ($disposalDateFrom, $disposalDateTo) {
                    if ($disposalDateTo) {
                        return $query->whereBetween('disposal_date', [Carbon::parse($disposalDateFrom)->format('Y-m-d'), Carbon::parse($disposalDateTo)->format('Y-m-d')]);
                    } else {
                        return $query->whereDate('disposal_date', Carbon::parse($disposalDateFrom)->format('Y-m-d'));
                    }
                })
                ->when($inputDateFrom, function ($query) use ($inputDateFrom, $inputDateTo) {
                    if ($inputDateTo) {
                        return $query->whereBetween('created_at', [Carbon::parse($inputDateFrom)->format('Y-m-d'), Carbon::parse($inputDateTo)->format('Y-m-d')]);
                    } else {
                        return $query->whereDate('created_at', Carbon::parse($inputDateFrom)->format('Y-m-d'));
                    }
                })    
                ->get();

        $fileName = '加工不良記録_'.now()->format('Ymd').'.xlsx';
        return Excel::download(new OutsourceProcessDefectExport($items), $fileName , \Maatwebsite\Excel\Excel::XLSX);
    }
}