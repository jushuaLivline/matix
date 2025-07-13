<?php

namespace App\Http\Controllers\API;

use App\Exports\ProductMaterialHierarchyExport;
use App\Http\Controllers\Controller;
use App\Models\ProductNumber;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ProductMaterialHierarchyController extends Controller
{
    public function fetchHierarchy(Request $request)
    {
        $partNumber = $request->input('partNumber');

        $product = ProductNumber::where('part_number', $partNumber)->first();

        if ($product) {
            $hierarchy = $product->getHierarchy();
            return response()->json($hierarchy, 200);
        }

        return response()->json([
            'error' => true,
            'message' => 'No data found for the provided part number.'
        ], 200);
    }

    public function exportHierarchy(Request $request)
    {
        $partNumber = $request->input('partNumber');
        $product = ProductNumber::where('part_number', $partNumber)->first();

        $productName = $product->product_name ?? 'Unknown';

        $fileName = sprintf('%s_%s_%s.xlsx', $partNumber, $productName, '構成');

        return Excel::download(new ProductMaterialHierarchyExport($partNumber), $fileName);
    }

}
