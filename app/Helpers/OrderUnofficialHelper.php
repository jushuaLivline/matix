<?php

namespace App\Helpers;

use App\Models\UnofficialNotice;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class OrderUnofficialHelper
{ 
    public static function searchQuery($request)
    {
        $delivery_destination_code = $request->customer_code;
        $category = $request->category;
        $product_code_from = $request->product_code_from;
        $product_code_to = $request->product_code_to;
        $acceptance = $request->acceptance;

        if ($request->order_yearmonth) {
            $selectedMonth =  Carbon::parse($request->order_yearmonth . "01");
        } else {
            // Handle invalid input, perhaps set a default value or throw an exception.
            $selectedMonth = now();
        }

        $nextMonth = $selectedMonth->copy()->addMonth()->format('Ym');
        $nextTwoMonth = $selectedMonth->copy()->addMonths(2)->format('Ym');

        $query = UnofficialNotice::with(['destination', 'product', 'product.line'])
                ->leftJoin('product_numbers', 'product_numbers.part_number', '=', 'unofficial_notices.product_number')
                ->whereIn('year_and_month', [
                        $selectedMonth->format('Ym'),
                        $nextMonth,
                        $nextTwoMonth
                ])
                ->when($delivery_destination_code, function ($query, $delivery_destination_code) {
                    $query->where("delivery_destination_code", $delivery_destination_code);
                })
                ->when($product_code_from, function ($query, $product_code_from) {
                    $query->where('product_number', $product_code_from);
                })
                ->when($acceptance, function ($query, $acceptance) {
                    $query->where('acceptance', $acceptance);
                })
                ->when($request->department_code, function ($query, $department_code) {
                    $query->whereHas('product', function ($productQuery) use ($department_code) {
                        $productQuery->where('department_code', $department_code);
                    });
                })
                ->when($request->line_code, function ($query, $line_code) {
                    $query->whereHas('product', function ($productQuery) use ($line_code) {
                        $productQuery->where('line_code', $line_code);
                    });
                })
                ->select('unofficial_notices.*', 'product_numbers.edited_part_number');

  
        if ($category == 'kanban'){

            $results = $query->where('unofficial_notices.instruction_class', 1)
                        ->select(
                            'product_number',
                            'acceptance',
                            'delivery_destination_code',
                            'year_and_month',
                            'unofficial_notices.instruction_class',
                            DB::raw('MAX(CASE WHEN year_and_month = "' . $selectedMonth->format('Ym') . '" THEN current_month_order_rate_factored_number ELSE 0 END) AS total_current_factored_month'),
                            DB::raw('MAX(CASE WHEN year_and_month = "' . $nextMonth . '" THEN current_month_order_rate_factored_number ELSE 0 END) AS total_next_factored_month'),
                            DB::raw('MAX(CASE WHEN year_and_month = "' . $nextTwoMonth . '" THEN current_month_order_rate_factored_number ELSE 0 END) AS total_next_two_factored_month'),
                        )
                        ->groupBy('product_number', 'acceptance', 'delivery_destination_code')
                        ->paginate(20);
        } elseif ($category == 'instruction') {
            $results = $query->where('unofficial_notices.instruction_class', 2)
                ->select(
                    'product_number',
                    'acceptance',
                    'delivery_destination_code',
                    'year_and_month',
                    'unofficial_notices.instruction_class',
                    DB::raw('MAX(CASE WHEN year_and_month = "' . $selectedMonth->format('Ym') . '" THEN current_month ELSE 0 END) AS total_current_month'),
                    DB::raw('MAX(CASE WHEN year_and_month = "' . $nextMonth . '" THEN current_month ELSE 0 END) AS total_next_month'),
                    DB::raw('MAX(CASE WHEN year_and_month = "' . $nextTwoMonth . '" THEN current_month ELSE 0 END) AS total_next_two_month'),
                )
                ->groupBy('product_number', 'acceptance', 'delivery_destination_code')
                ->paginate(20);

        }else{
            $results = $query->select(
                'product_number',
                'edited_part_number',
                'acceptance',
                'delivery_destination_code',
                'year_and_month',
                'unofficial_notices.instruction_class',
                //instruction
                DB::raw('MAX(CASE WHEN year_and_month = "' . $selectedMonth->format('Ym') . '" THEN current_month ELSE 0 END) AS total_current_month'),
                DB::raw('MAX(CASE WHEN year_and_month = "' . $nextMonth . '" THEN current_month ELSE 0 END) AS total_next_month'),
                DB::raw('MAX(CASE WHEN year_and_month = "' . $nextTwoMonth . '" THEN current_month ELSE 0 END) AS total_next_two_month'),
                //kanban
                DB::raw('MAX(CASE WHEN year_and_month = "' . $selectedMonth->format('Ym') . '" THEN current_month_order_rate_factored_number ELSE 0 END) AS total_current_factored_month'),
                DB::raw('MAX(CASE WHEN year_and_month = "' . $nextMonth . '" THEN current_month_order_rate_factored_number ELSE 0 END) AS total_next_factored_month'),
                DB::raw('MAX(CASE WHEN year_and_month = "' . $nextTwoMonth . '" THEN current_month_order_rate_factored_number ELSE 0 END) AS total_next_two_factored_month'),
            )
            ->groupBy('product_number', 'acceptance', 'delivery_destination_code')
            ->paginate(20);
        }


        $results->appends(request()->all());
        return $results;
    }
}