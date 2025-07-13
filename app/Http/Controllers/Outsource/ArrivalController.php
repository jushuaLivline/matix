<?php

namespace App\Http\Controllers\Outsource;

use App\Exports\ArrivalResultExport;
use App\Http\Controllers\Controller;
use App\Models\OutsourcedProcessing;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ArrivalController extends Controller
{
    public function __construct()
    {
        $this->outsourcedProcessing = new OutsourcedProcessing();
    }
    public function index(Request $request)
    {
        if (count($request->query()) > 0) {
            // Eager load any related models used in the view to prevent N+1 issue
            $arrivalResultLists = OutsourcedProcessing::getStocksArrivals($request)->paginateResults();
        }

            return view('pages.outsource.arrival.index', [
            'arrivalResultLists' => $arrivalResultLists ?? [],
        ]);
    }
    public function export(Request $request)
    {
        $arrivalStart = $request->arrival_day_start;
        $arrivalEnd = $request->arrival_day_end;
        $flightNumberStart = $request->incoming_flight_number_start;
        $flightNumberEnd = $request->incoming_flight_number_end;


        $arrivalResultLists = OutsourcedProcessing::query()
            ->when($request->input('supplier_code'), fn($query) => $query->where('supplier_code', $request->input('supplier_code')))
            ->when($request->input('product_code'), fn($query) => $query->where('product_code', $request->input('product_code')))
            ->when($request->input('order_number'), fn($query) => $query->where('order_no', $request->input('order_number')))
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
            ->paginateResults();

        $fileName = '外注加工入荷実績検索_' . now()->format('Ymd') . '.xlsx';
        return Excel::download(new ArrivalResultExport($arrivalResultLists), $fileName, \Maatwebsite\Excel\Excel::XLSX);
    }
}
