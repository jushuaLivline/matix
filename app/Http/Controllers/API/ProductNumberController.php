<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ProductNumber;
use Illuminate\Http\Request;

class ProductNumberController extends Controller
{
    function checkIfExist(Request $request){
        $productNumber =  ProductNumber::where('part_number', $request->product_number)->first();
        if($productNumber) {
            return [
                'status' => "success",
                'data' => $productNumber
            ];
        }

        return [
            'status' => "failed", 
            'data' => []
        ];
    }
}
