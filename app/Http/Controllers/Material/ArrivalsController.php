<?php

namespace App\Http\Controllers\Material;

use App\Http\Controllers\Controller;
use App\Exports\Material\ArrivalExcelExport;
use App\Models\SupplyMaterialArrival;
use App\Services\Material\Order\ArrivalsService;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ArrivalsController extends Controller
{
    protected $arrivalsService;
    public function __construct(ArrivalsService $arrivalsService)
    {
        $this->arrivalsService = $arrivalsService;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $filters = [
            'arrivalDateStart' => $request->input('arrival_day_from'),
            'arrivalDateEnd' => $request->input('arrival_day_to'),
            'flightNoFrom' => $request->input('flight_from'),
            'flightNoTo' => $request->input('flight_to'),
            'materialManufacturerCode' => $request->input('manufacturer_code'),
            'materialNo' => $request->input('product_code'),
            'deliveryNo' => $request->input('delivery_no'),
            'customerCode' => $request->input('customer_code')
        ];
    
        $supplyMaterialArrivals = SupplyMaterialArrival::with('product')
            ->filter($filters)
            ->orderByDesc('created_at')
            ->paginateResults();

    
        return view('pages.material.order.arrivals.index', compact(
            'supplyMaterialArrivals'));
    }

    //Material 25 export excel
    public function excel_export(Request $request)
    {
        ini_set('max_execution_time', 0);
        $supplyMaterialArrivals = $this->arrivalsService->excel_export($request);
        $perPage = $request->per_page ?? 20;
        $page = $request->page ?? 1;
        $supplyMaterialArrivals = $supplyMaterialArrivals->slice(($page - 1) * $perPage, $perPage)->values();

        if ($supplyMaterialArrivals->isEmpty()) {
            dd('No data found', $supplyMaterialArrivals);
        }
            
        $fileName = '支給材入荷実績データ_'.now()->format('Ymd').'.xlsx';
        return Excel::download(new ArrivalExcelExport($supplyMaterialArrivals), $fileName , \Maatwebsite\Excel\Excel::XLSX);
    }
}
