<?php
namespace App\Http\Controllers\Order\Parts;
use App\Http\Controllers\Controller;

use App\Models\UnofficialNotice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
class ForecastController extends Controller
{
    public function __construct() {
    }

    public function index(Request $request) {
        $data = array_filter([
            'year_month' => $request->input('year_month') ?? date('Ym'),
            'delivery_destination_code' => $request->input('delivery_destination_code') ?? '',
            'delivery_destination_name' => $request->input('delivery_destination_name') ?? '',
            'acceptance' => $request->input('acceptance') ?? '',
            'product_number' => $request->input('product_number') ?? '',
            'product_name' => $request->input('product_name') ?? '',
        ],fn($value) => $value !== null);

        $search_data = array_filter([
            'year_and_month' => $request->input('year_month'),
            'delivery_destination_code' => $request->input('delivery_destination_code'),
            'acceptance' => $request->input('acceptance'),
            'product_number' => $request->input('product_number')
        ], function ($value) {
            return !is_null($value) && $value !== ''; 
        });
        
        $notice = null;
        //Get the latest, just update the year_and_month when back or next is pressed
        if (isset($search_data['year_and_month'], $search_data['delivery_destination_code'], $search_data['product_number'])) {
            $notice = UnofficialNotice::where($search_data)
            ->orderBy('created_at','desc')
            ->first();
        }

        //Log::info('Unofficial Notice Result:', ['notice' => $notice]);
        return view('pages.order.parts.forecast.index', compact(['data','notice']));
    }

    public function searchMonth(Request $request) {
        $action = $request->input('action');
        $year_and_month = $request->input('year_and_month');

        $year = (int) substr($year_and_month, 0, 4);
        $month = (int) substr($year_and_month, 4, 2);

        if ($action == "previous") {
            $month--; // Decrease the month
            if ($month == 0) {
                $month = 12;
                $year--; // Decrease the year if month goes below 1
            }
        } else {
            $month++; // Increase the month
            if ($month == 13) {
                $month = 1;
                $year++; // Increase the year if month goes above 12
            }
        }

        $new_year_and_month = sprintf('%04d%02d', $year, $month);

        $search_data = array_filter([
            'year_and_month' => $new_year_and_month,
            'delivery_destination_code' => $request->input('delivery_destination_code'),
            'acceptance' => $request->input('acceptance'),
            'product_number' => $request->input('product_number')
        ], function ($value) {
            return !is_null($value) && $value !== '';
        });

        $new_notice = UnofficialNotice::where($search_data)
        ->orderBy('created_at','desc')
        ->first();

        //Log::info('New Unofficial Notice Result:', ['notice' => $new_notice]);

        if($new_notice){
            return response()->json([
                'status' => 'success',
                'new_year_and_month' => $new_year_and_month,
                'notice' => $new_notice
            ]);
        }else{
            return response()->json([
                'status' => 'fail',
                'new_year_and_month' => $new_year_and_month,
                'notice' => 'No record found'
            ]);
        }
    }

    public function addUpdate(Request $request){
        DB::beginTransaction();
       
        try {
        // Retrieve all input values except the CSRF token
        $data = $request->except('_token'); 

        // Extract static fields
        $check_exist = [
            'year_and_month' => $request->input('year_and_month', null),
            'delivery_destination_code' => $request->input('delivery_destination_code', null),
            'acceptance' => $request->input('acceptance', null),
            'product_number' => $request->input('product_number', null)
        ];
        
        // Extract dynamic "days" fields
        $days = $request->input('days', []);

        // Combine all fields into a single data array
        $full_data = array_merge($check_exist, $days);


        // Check if the record already exists
        $notice = UnofficialNotice::where($check_exist)->first();

        Log::info('Unofficial Notice Result:', ['notice' => $notice]);

        if ($notice) {
            // Update existing record
            $notice->update($full_data);
            $message = '指示部品内示情報の更新が完了しました';
        } else {
            // register new record
            UnofficialNotice::create($full_data);
            $message = '指示部品内示情報の登録が完了しました';
        }

        DB::commit();

        return response()->json([
            'status' => 'success',
            'message' => $message
        ], 200, [], JSON_PRETTY_PRINT);
        

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in addUpdate:', [
                'error' => $e->getMessage(),
                'request' => $request->all(),
                'timestamp' => now(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'エラーが発生しました: ' . $e->getMessage()
            ]);
        }
    }
}
