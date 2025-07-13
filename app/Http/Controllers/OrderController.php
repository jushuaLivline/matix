<?php

namespace App\Http\Controllers;

use App\Exports\OrderSearchExport;
use App\Exports\TabulationExport;
use App\Exports\UnofficialListExport;
use App\Helpers\OrderUnofficialHelper;
use App\Helpers\RequestHelper;
use App\Models\Customer;
use App\Models\Department;
use App\Models\FirmOrder;
use App\Models\HistoryTemporaryOrder;
use App\Models\HistoryOrder;
use App\Models\Line;
use App\Models\ProductNumber;
use App\Models\SalePlan;
use App\Models\Setting;
use App\Models\ShipmentRecord;
use App\Models\TemporaryUnofficialNotice;
use App\Models\UnofficialNotice;
use App\Services\SettingService;
use App\Services\CalendarService;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Session;
use SebastianBergmann\CodeCoverage\Report\Xml\Totals;
use Svg\Tag\Rect;

class OrderController extends Controller
{
    function kanbanInput(Request $request)
    {
        $yearMonth = $request->year_month ? Carbon::parse($request->year_month . "01") : now();
        $results = DB::table('unofficial_notices')
            ->select(
                'unofficial_notices.id', 
                'unofficial_notices.current_month', 
                'unofficial_notices.next_month', 
                'unofficial_notices.two_months_later', 
                'product_numbers.part_number', 
                'product_numbers.edited_part_number', 
                'product_numbers.product_name', 'unofficial_notices.current_month_order_rate_factored_number')
            ->addSelect(DB::raw("MAX(mst_unofficial_notices.current_month_order_rate_factored_number) as max_factored"))
            ->addSelect(DB::raw("MAX(mst_unofficial_notices.current_month) as current_month_max"))
            ->addSelect(DB::raw("MAX(mst_unofficial_notices.next_month) as next_month_max"))
            ->addSelect(DB::raw("MAX(mst_unofficial_notices.two_months_later) as two_months_later_max"))
            ->join('product_numbers', 'product_numbers.part_number', 'unofficial_notices.product_number')
            ->where('unofficial_notices.year_and_month', $yearMonth->format('Ym'))
            ->when($request->customer_code, function ($q) use ($request) {
                $q->where("unofficial_notices.delivery_destination_code", $request->customer_code);
            })
            ->when($request->acceptance, function ($q) use ($request) {
                $q->where("unofficial_notices.acceptance", $request->acceptance);
            })
            ->when($request->part_number_first && $request->part_number_second, function ($q) use ($request) {
                $q->where(function ($query) use ($request) {
                    $query->where('unofficial_notices.product_number', '>=', $request->part_number_first)
                        ->where('unofficial_notices.product_number', '<=', $request->part_number_second);
                });
            })
            ->groupBy('unofficial_notices.product_number')
            ->groupBy('unofficial_notices.year_and_month')
            ->where("unofficial_notices.instruction_class", 1)
            ->paginate(20);

        $yearMonths = CarbonPeriod::create($yearMonth, '1 month', 3);

        return view("pages.order.kanban_input", [
            'results' => $results,
            'yearMonths' => $yearMonths,
            'monthIndexColumn' => UnofficialNotice::monthIndexColumn
        ]);
    }

    function kanbanInputSave(Request $request)
    {
        foreach ($request->ids as $id) {
            $unofficialNotice = UnofficialNotice::find($id);
            foreach (UnofficialNotice::monthIndexColumn as $column) {
                $inputKey = "value_" . $id . "_" . $column;
                $unofficialNotice->{$column} = $request->{$inputKey} ?? 0;
                $unofficialNotice->save();
            }
        }
        
        return back()->with("success", "データは正常に登録されました");
    }

    function dataAcquisition()
    {
        return view("pages.order.data_acquisition");
    }

    /**
     * Fetch firm orders and shipment records based on request.
     *
     * @param Request $request
     * @return array
     */
    private function fetchOrdersAndShipments(Request $request): array
    {
        $orderDate = Carbon::parse($request->order_date)->format('Y-m-d');

        // Fetch Firm Orders
        $firmOrders = FirmOrder::select(
            'due_date',
            'delivery_destination_code',
            'classification',
            'part_number',
            'plant',
            'acceptance',
            'uniform_number',
            'number_of_accommodated',
            'delivery_no',
            'kanban_number',
            'instruction_number'
        )
        ->with(['product:id,part_number,product_name,customer_edited_product_number'])
        ->where('due_date', $orderDate)
        ->when($request->input('acceptance'), fn($q, $acceptance) => $q->where('acceptance', $acceptance))
        ->when($request->input('customer_code'), fn($q, $deliveryDestination) => $q->where('delivery_destination_code', $deliveryDestination))
        ->when($request->input('delivery_no'), fn($q, $deliveryNo) => $q->where('delivery_no', $deliveryNo))
        ->when($request->input('plant'), fn($q, $plant) => $q->where('plant', $plant))
        ->when($request->input('category'), fn($q, $category) => $q->where('classification', $category))
        ->when($request->input('department_code'), function ($query, $departmentCode) {
            $query->whereHas('product', fn($productQuery) => $productQuery->where('department_code', $departmentCode));
        })
        ->when($request->input('supplier_code'), function ($query, $supplierCode) {
            $query->whereHas('product', fn($productQuery) => $productQuery->where('supplier_code', $supplierCode));
        })
        ->when($request->input('line_code'), function ($query, $lineCode) {
            $query->whereHas('product', fn($productQuery) => $productQuery->where('line_code', $lineCode));
        })
        ->where(function ($query) {
            $query->whereNotNull('kanban_number')
                ->orWhereNotNull('instruction_number')
                ->orWhereNotNull('part_number');
        })
        ->groupBy('part_number')
        ->get();

        // Fetch Shipment Records
        $shipmentRecords = ShipmentRecord::select(
            'due_date',
            'product_no as part_number',
            'delivery_no as shipment_delivery_no',
            'kanban_no as shipment_kanban_number',
            'instruction_no as shipment_instruction_number'
        )
        ->where('due_date', $orderDate)
        ->where(function ($query) {
            $query->whereNotNull('kanban_no')
                ->orWhereNotNull('instruction_no');
        })
        ->groupBy('product_no')
        ->get();

        return [$firmOrders, $shipmentRecords];
    }

    /**
     * Prepare data by merging firm orders and shipment records.
     *
     * @param \Illuminate\Support\Collection $firmOrders
     * @param \Illuminate\Support\Collection $shipmentRecords
     * @return array
     */
    private function prepareData($firmOrders, $shipmentRecords): array
    {
        $data = $firmOrders->mapWithKeys(function ($firmOrder) {
            return [
                $firmOrder->part_number => [
                    [
                        'due_date' => $firmOrder->due_date,
                        'delivery_destination_code' => $firmOrder->delivery_destination_code,
                        'classification' => $firmOrder->classification,
                        'part_number' => $firmOrder->product?->customer_edited_product_number,
                        'product_name' => $firmOrder->product?->product_name,
                        'plant' => $firmOrder->plant,
                        'acceptance' => $firmOrder->acceptance,
                        'uniform_number' => $firmOrder->uniform_number,
                        'number_of_accommodated' => $firmOrder->number_of_accommodated,
                        'daily_reports' => [
                            $firmOrder->delivery_no => [
                                'kanban_number' => $firmOrder->kanban_number,
                                'instruction_number' => $firmOrder->instruction_number,
                                'shipment_kanban_number' => null,
                                'shipment_instruction_number' => null,
                            ]
                        ]
                    ]
                ]
            ];
        })->toArray();

        // Merge shipment records into the daily reports
        foreach ($shipmentRecords as $shipmentRecord) {
            $partNumber = $shipmentRecord->part_number;
            $deliveryNo = $shipmentRecord->shipment_delivery_no;

            if (isset($data[$partNumber][0]['daily_reports'][$deliveryNo])) {
                $data[$partNumber][0]['daily_reports'][$deliveryNo]['shipment_kanban_number'] = $shipmentRecord->shipment_kanban_number;
                $data[$partNumber][0]['daily_reports'][$deliveryNo]['shipment_instruction_number'] = $shipmentRecord->shipment_instruction_number;
            }
        }

        return $data;
    }

    /**
     * Get unique and sorted delivery numbers.
     *
     * @param \Illuminate\Support\Collection $firmOrders
     * @param \Illuminate\Support\Collection $shipmentRecords
     * @return array
     */
    private function getUniqueSortedDeliveryNos($firmOrders, $shipmentRecords): array
    {
        return collect(array_merge(
            $firmOrders->pluck('delivery_no')->toArray(),
            $shipmentRecords->pluck('shipment_delivery_no')->toArray()
        ))->filter()->unique()->sort()->values()->all();
    }

    // End of Order 18

    function specifiedPart(Request $request, CalendarService $calendar)
    {
        $month = $request->year_month ? Carbon::parse($request->year_month . "01") : now();
        $dates = $calendar->groupDatesPerMonth($month);

        $productCode = $request->product_code;
        $customerCode = $request->customer_code;
        $acceptance = $request->acceptance;

        if($productCode && $customerCode) {
            $unofficialNotice = DB::table('unofficial_notices')
                            ->where("delivery_destination_code", $customerCode)
                            ->where("product_number", $productCode)
                            ->where("year_and_month", $request->year_month)
                            ->when($acceptance, function ($q) use ($acceptance) {
                                $q->where("acceptance", $acceptance);
                            })
                            ->where('instruction_class', 2)
                            ->first();
        } else {
            $unofficialNotice = [];
        }
        
        return view("pages.order.specified_part", [
            'dates' => $dates,
            'month' => $month,
            'unofficialNotice' => $unofficialNotice,
        ]);
    }

    function specifiedPartPost(Request $request){
        $yearMonth = $request->year_month;
        $productCode = $request->product_code;
        $customerCode = $request->customer_code;
        $acceptance = $request->acceptance;

        if(!$yearMonth && !$productCode && !$customerCode){
            return back();
        }

        $unofficialNotice = UnofficialNotice::query()
                        ->where("delivery_destination_code", $customerCode)
                        ->where("product_number", $productCode)
                        ->where("year_and_month", $yearMonth)
                        ->when($acceptance, function($q) use ($acceptance){
                            $q->where("acceptance", $acceptance);
                        })
                        ->first();

        if(!$unofficialNotice){
            return back();
        }
        
        $total = 0;
        for($i = 1; $i < 32; $i++){
            $day = "day_" . $i;
            if($request->{$day}){
                $number = $request->{$day} ?? 0;
                $unofficialNotice->{$day} = $number;
                $total += $number;
            }
        }

        $unofficialNotice->current_month = $total;
        $unofficialNotice->save();
        return back();
    }


    function dataImportContentConfirmation($input_id)
    {
        $data = TemporaryUnofficialNotice::where('input_id', $input_id)->get();
        if (count($data) < 1) {
            return abort(404);
        }
        return view('pages.order.data_import_content_confirmation', [
            'kanbans' => TemporaryUnofficialNotice::where('input_id', $input_id)->where('instruction_class', 1)->get(),
            'instructions' => TemporaryUnofficialNotice::where('input_id', $input_id)->where('instruction_class', 2)->get(),
            'input_id' => $input_id,
        ]);
    }

    function processDataImportContentConfirmation($input_id)
    {
        $data = TemporaryUnofficialNotice::where('input_id', $input_id)->get();
        if (count($data) < 1) {
            return abort(404);
        }

        foreach ($data as $order) {
            UnofficialNotice::create([
                "product_id" => $order->product_number,
                "acceptance" => $order->acceptance,
                "delivery_destination_code" => $order->delivery_destination_code,
                "year_and_month" => $order->year_and_month,
                "day_1" => $order->day_1,
                "day_2" => $order->day_2,
                "day_3" => $order->day_3,
                "day_4" => $order->day_4,
                "day_5" => $order->day_5,
                "day_6" => $order->day_6,
                "day_7" => $order->day_7,
                "day_8" => $order->day_8,
                "day_9" => $order->day_9,
                "day_10" => $order->day_10,
                "day_11" => $order->day_11,
                "day_12" => $order->day_12,
                "day_13" => $order->day_13,
                "day_14" => $order->day_14,
                "day_15" => $order->day_15,
                "day_16" => $order->day_16,
                "day_17" => $order->day_17,
                "day_18" => $order->day_18,
                "day_19" => $order->day_19,
                "day_20" => $order->day_20,
                "day_21" => $order->day_21,
                "day_22" => $order->day_22,
                "day_23" => $order->day_23,
                "day_24" => $order->day_24,
                "day_25" => $order->day_25,
                "day_26" => $order->day_26,
                "day_27" => $order->day_27,
                "day_28" => $order->day_28,
                "day_29" => $order->day_29,
                "day_30" => $order->day_30,
                "day_31" => $order->day_31,
                "current_month" => $order->current_month,
                "next_month" => $order->next_month,
                "two_months_later" => $order->two_months_later,
                "instruction_class" => $order->instruction_class,
                "direct_shipping_destination" => $order->direct_shipping_destination,
                "uniform_number" => $order->uniform_number,
                "cycle" => $order->cycle,
                "number_of_accomodated" => $order->number_of_accomodated,
                "aisin_factory" => $order->aisin_factory,
                "responsible_person" => $order->responsible_person,
                "minimum_delivery_unit" => $order->minimum_delivery_unit,
                "number_per_day" => $order->number_per_day,
                "number_of_cards" => $order->number_of_cards,
                "kanban_number" => $order->kanban_number,
                "standard_stock" => $order->standard_stock,
                "sp_tp_classification" => $order->sp_tp_classification,
                "manufactorer_factory" => $order->manufactorer_factory,
                "manufactorer_factory_destination" => $order->manufactorer_factory_destination,
                "data_partition" => $order->data_partition,
                "current_month_order_rate_factored_number" => $order->current_month_order_rate_factored_number,
                "color_mode" => $order->color_mode,
                "customer_part_number" => $order->customer_part_number,
                "customer" => $order->customer,
                "design_change_code" => $order->design_change_code,
                "input_category" => $order->input_category,
                "creator" => $order->creator,
            ]);
        }
        return response()->json([
            'status' => true,
        ]);
    }

    function detailed(Request $request)
    {
        // $orders = HistoryOrder::where('aisin_plant_code', $request->order_plant)
        //     ->where('item_code', '>=',  (int) $request->order_min_product_number ?? 0)
        //     ->where('item_code', '<=',  (int) $request->order_max_product_number ?? 0);

        // if (($request->order_date ?? '') != '') {
        //     $orders = $orders->whereDate('created_at', '<=', $request->order_date);
        // }

        if (count($request->query()) > 0) {
            $this->validate($request,[
                'order_date'=>'required'
             ]);
             $orders = FirmOrder::with('product')
             ->leftJoin('product_numbers', 'product_numbers.part_number', '=', 'firm_orders.part_number')
             ->select('firm_orders.*', 'product_numbers.edited_part_number')
             ->search($request)
             ->paginate(20);

        }
        // dd($orders);
        return view("pages.order.detailed", [
            'orders' => $orders ?? [],
        ]);
    }

    function quantityCalculation(SettingService $settingService)
    {
        $setting = $settingService->settingCategory("排他")
            ->settingId("更新処理")
            ->get();

        return view("pages.order.quantity_calculation", [
            'setting' => $setting
        ]);
    }
    function quantityCalculationPost(SettingService $settingService, Request $request)
    {
        
        DB::beginTransaction();
        
        try { 
            $yearMonth = $request->month;
            $setting = $settingService->settingCategory("排他")
                ->settingId("更新処理")
                ->get();

            // Get the UnofficialNotice for the specified year_and_month
            $notices = UnofficialNotice::query()
                ->select(
                    'product_prices.sell_price',
                    'product_prices.unit_price',
                    'unofficial_notices.acceptance',
                    'unofficial_notices.product_number',

                    // months 
                    'unofficial_notices.current_month',
                    'unofficial_notices.next_month',
                    'unofficial_notices.two_months_later',
                    
                    'product_numbers.customer_code',
                    'product_numbers.product_category',
                    'product_numbers.supplier_code',
                    'product_numbers.department_code',
                    'product_numbers.line_code',
                    'configurations.number_used',

                    // customer related for rounding
                    'customers.sales_amount_rounding_indicator',
                    'customers.purchase_amount_rounding_indicator',
                    'processes.inside_and_outside_division',
                )
                ->join('product_numbers', 'product_numbers.part_number', 'unofficial_notices.product_number')
                ->join('product_prices', 'product_prices.part_number', 'unofficial_notices.product_number')
                ->join('configurations', 'configurations.parent_part_number', 'unofficial_notices.product_number')
                ->join('customers', 'customers.customer_code', 'product_numbers.customer_code') 
                ->join('process_orders', 'process_orders.part_number', 'unofficial_notices.product_number')
                ->join('processes', 'processes.process_code', 'process_orders.process_code')
                ->where('unofficial_notices.year_and_month', $yearMonth)
                ->get();

            foreach ($notices as $notice) {
                if (in_array($notice->product_category, [0, 3])) {
                    $quantity = (int) $notice->current_month * (int) $notice->number_used;
                    $unitPrice = $notice->unit_price;
                } else {
                    $unitPrice = $notice->sell_price;
                    $quantity = (int) $notice->current_month;
                }

                // Getting the Amount Category. Ref: ORDER-15 Specs #5-iv-4
                $amountCategory = match ($notice->product_category) {
                    0 => 3,
                    1 => 1,
                    3 => 2,
                    default => 0,
                };

                // Getting Outsourced processing. Ref: ORDER-15 Specs #6-i-2
                if($notice->inside_and_outside_division == 2){
                    $amountCategory = 2;
                }

                // Getting Rounding Indicator. Ref: ORDER-15 Specs #5-iii-11
                if (in_array($amountCategory, [2, 3])) {
                    $roundingIndicator = $notice->purchase_amount_rounding_indicator;
                } else {
                    $roundingIndicator = $notice->sales_amount_rounding_indicator;
                }

                // get the amount of money. Ref: ORDER-15 Specs #5-iii-11
                $subTotal = $notice->current_month * $unitPrice;
             
                $amount = match ( (int) $roundingIndicator) {
                    1 => (int) floor($subTotal),
                    2 => (int) ceil($subTotal),
                    3 => (int) round($subTotal),
                };

                
                // for the current month
                DB::table('sales_plans')->insert([
                    'year_month' => Carbon::parse($yearMonth . "01")->format("Ym"),
                    'product_number' => $notice->product_number,
                    'part_number' => $notice->product_number,
                    'amount_category' => $amountCategory,
                    'supplier_code' => $notice->supplier_code,
                    'customer_code' => $notice->customer_code,
                    'department_code' => $notice->department_code,
                    'line_code' => $notice->line_code,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'amount' => $amount,
                    'creator' => Auth::user()->id, // Assuming have an authenticated user->id
                ]);

                //create for next month. Ref: ORDER-15 Specs #5-iii-1~3 (second table)
                if (in_array($notice->product_category, [0, 3])) {
                    $quantity = (int) $notice->next_month * (int) $notice->number_used;
                    $unitPrice = $notice->unit_price;
                } else {
                    $unitPrice = $notice->sell_price;
                    $quantity = $notice->next_month;
                }

                $subTotal = $notice->next_month * $unitPrice;
                $amount = match ( (int) $roundingIndicator) {
                    1 => (int) floor($subTotal),
                    2 => (int) ceil($subTotal),
                    3 => (int) round($subTotal),
                };


                // Saving Sales plan for next month
                DB::table('sales_plans')->insert([
                    'year_month' => Carbon::parse($yearMonth. "01")->addMonths(1)->format("Ym"),
                    'product_number' => $notice->product_number,
                    'part_number' => $notice->product_number,
                    'amount_category' => $amountCategory,
                    'supplier_code' => $notice->supplier_code,
                    'customer_code' => $notice->customer_code,
                    'department_code' => $notice->department_code,
                    'line_code' => $notice->line_code,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'amount' => $amount,
                    'creator' => Auth::user()->id, // Assuming have an authenticated user->id
                ]);

                //create for next month. Ref: ORDER-15 Specs #5-iii-1~3 (third table)
                if (in_array($notice->product_category, [0, 3])) {
                    $quantity = $notice->two_months_later * $notice->number_used;
                    $unitPrice = $notice->unit_price;
                } else {
                    $unitPrice = $notice->sell_price;
                    $quantity = $notice->two_months_later;
                }

                $subTotal = $notice->two_months_later * $unitPrice;
                $amount = match ( (int) $roundingIndicator) {
                    1 => (int) floor($subTotal),
                    2 => (int) ceil($subTotal),
                    3 => (int) round($subTotal),
                };

                // Saving Sales plan for next 2 month
                DB::table('sales_plans')->insert([
                    'year_month' => Carbon::parse($yearMonth . "01")->addMonths(2)->format("Ym"),
                    'product_number' => $notice->product_number,
                    'part_number' => $notice->product_number,
                    'amount_category' => $amountCategory,
                    'supplier_code' => $notice->supplier_code,
                    'customer_code' => $notice->customer_code,
                    'department_code' => $notice->department_code,
                    'line_code' => $notice->line_code,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'amount' => $amount,
                    'creator' => Auth::user()->id, // Assuming have an authenticated user->id
                ]);
            }

            $settingService->settingCategory("締め処理")
                ->settingId("内示")
                ->updateSetting([
                    'number_1' => 0,
                    'string_1' => ''
                ]);

            DB::commit();

            return back()->with("success", "データは正常に登録されました");

        } catch (Exception $exception) {
            DB::rollBack();
            $settingService->settingCategory("締め処理")
                ->settingId("内示")
                ->updateSetting([
                    'number_1' => 9,
                    'string_2' => date("Y-m-d"),
                    'string_3' => $exception->getMessage(),
                    'string_4' => "counter measure"
                ]);

            return back()->with("error", "データは正常に登録されました");
        }

    }

    function processDataAcquisition(Request $request)
    {
        if (! $request->hasFile('file')) {
            return redirect()->back()->withErrors(['msg' => '取込ファイルを選択してください']);
        }
        $rows = explode(PHP_EOL, file_get_contents($request->file('file')->getRealPath()));
        $data = [];
        $ctr = 0;
        $latest_row = TemporaryUnofficialNotice::latest()->first();
        $latest = 0;
        if ($latest_row) {
            $latest = $latest_row->input_id;
        }
        foreach ($rows as $row) {
            if ($row == '') {
                break;
            }
            $ctr++;
            if ($ctr >= 6) {
                $str = preg_replace('/\t/', '@', $row);
                // dd(explode('@', $str));
                $exploded = explode('@', $str);
                $data[] = $exploded;
                $delivery_destination_code = Setting::select('string_1')->where('setting_id', $exploded[13])->first();
                TemporaryUnofficialNotice::create([
                    "product_id" => (int) $exploded[5],
                    "acceptance" => $exploded[6],
                    "delivery_destination_code" => $delivery_destination_code->string_1,
                    "year_and_month" => (strlen($exploded[11]) == 4) ? $exploded[11] : substr($exploded[11], 0, -3),
                    "current_month" => (strlen($exploded[11]) == 4) ? (int) $exploded[12] : 0,
                    "next_month" => (int) $exploded[18],
                    "two_months_later" => (int) $exploded[19],
                    "instruction_class" => $exploded[9],
                    "uniform_number" => $exploded[15],
                    "cycle" => $exploded[16],
                    "aisin_factory" => $exploded[13],
                    "minimum_delivery_unit" => (int) $exploded[20],
                    "number_per_day" => (int) $exploded[21],
                    "number_of_cards" => (int) $exploded[22],
                    "kanban_number" => (int) $exploded[23],
                    "standard_stock" => (int) $exploded[24],
                    "sp_tp_classification" => (int) $exploded[28],
                    "input_id" => ($latest + 1),
                ]);
            }
        }
        return redirect()->route('order.data.import.content.confirmation', [($latest + 1)]);
    }
}
