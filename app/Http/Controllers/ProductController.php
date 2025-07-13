<?php

namespace App\Http\Controllers;

use App\Models\Configuration;
use App\Models\Process;
use App\Models\ProcessOrder;
use App\Models\ProcessUnitPrice;
use App\Models\ProductNumber;
use App\Models\ProductPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{

    public function getDatePickerContent($effective_date = null, $key = 0)
    {
        return view('partials._date_picker', [
            'inputName' => 'effective_date_' . $key,
            'dateFormat' => 'YYYY/MM/DD',
            'value' => $effective_date
        ])->render();
    }

    public function clearSession(Request $request)
    {
        if (session()->has('items')) {
            session()->forget('items');
        }
    }

    
    public function getProcessSettingModalContent($part_number = '', $process_code = '')
    {
        $process = Process::where('process_code', $process_code)->first();
        $product = ProductNumber::where('part_number', $part_number)->first();
        return view('partials.modals.masters.products.process_setting_modal', [
            'modalId' => 'p'.$process->process_code,
            'dataId' => $process->process_code,
            'partNumber' => $part_number,
            'productName' => $product->product_name,
            'processName' => $process->process_name,
            'processCode' => $process->process_code,
        ])->render();
    }
}