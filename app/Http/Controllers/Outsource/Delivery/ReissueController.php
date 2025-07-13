<?php

namespace App\Http\Controllers\Outsource\Delivery;

use App\Http\Controllers\Controller;
use App\Models\OutsourcedProcessing;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Constants\Constant;
use App\Models\Customer;

class ReissueController extends Controller
{

    // Outsource processing 41
    public function index(Request $request)
    {
        $filters = array_filter([
            'document_issue_date_from' => $request->input('document_issue_date_from') ? date('Y-m-d', strtotime($request->input('document_issue_date_from'))) : null,
            'document_issue_date_to' => $request->input('document_issue_date_to') ? date('Y-m-d', strtotime($request->input('document_issue_date_to'))) : null,
            'instruction_date_from' => $request->input('instruction_date_from') ? date('Y-m-d', strtotime($request->input('instruction_date_from'))) : null,
            'instruction_date_to' => $request->input('instruction_date_to') ? date('Y-m-d', strtotime($request->input('instruction_date_to'))) : null,
            'incoming_flight_number_start' => $request->input('incoming_flight_number_start'),
            'incoming_flight_number_end' => $request->input('incoming_flight_number_end'),
            'supplier_code' => $request->input('supplier_code') ? (string) $request->input('supplier_code') : null,
            'order_no' => $request->input('order_no'),
        ]);

        $paginationThreshold = Constant::PAGINATION_THRESHOLD;
        $reissueInvoiceLists = OutsourcedProcessing::search($filters)
            ->with(['product', 'kanbanMaster'])
            ->paginateResults($paginationThreshold);

        return view('pages.outsource.delivery.reissue.index', compact('reissueInvoiceLists'));
    }

    //Outsource processing 41
    public function reissueInvoicePdf(Request $request)
    {
        $invoiceIds = json_decode($request->invoice_ids);

        $order = OutsourcedProcessing::whereIn('id', $invoiceIds)
            ->where('order_classification', 2)
            ->first();

        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->set('isRemoteEnabled', true);
        $pdfOptions->set('isHtml5ParserEnabled', true);

        // Prevent new font generation by setting an existing font cache
        $fontPath = storage_path('dompdf/fonts/');
        if (is_dir($fontPath) && is_writable($fontPath)) {
        $pdfOptions->set('fontDir', $fontPath);
        $pdfOptions->set('fontCache', $fontPath);
        } else {
        // If not writable, set read-only mode (no new fonts will be generated)
        $pdfOptions->set('isFontSubsettingEnabled', false);
        }
        
        $dompdf = new Dompdf($pdfOptions);

        $html = view('pages.outsource.delivery.reissue.pdf_template', compact('order'))->render();

        $dompdf->loadHtml($html);
        $dompdf->setPaper('legal', 'portrait');
        $dompdf->render();
        $pdfContent = $dompdf->output();
        
        $fileName = '受領書再発行_'. now()->format('Ymd') .'.pdf';
        $pdfPath = 'temp/受領書再発行_'. now()->format('Ymd') .'.pdf';
        Storage::put($pdfPath, $pdfContent);

        return Storage::download($pdfPath, $fileName);

        return back();
    }

    public function getCustomerName(Request $request){
        $customer_code = $request->input('customer_code');
        $customer = Customer::where('customer_code', $customer_code)->first();

        if ($customer) {
            return response()->json([
                'status' => 'success',
                'message' => 'Process found',
                'customer_name' => $customer->supplier_name_abbreviation
            ]);
        } else {
            return response()->json([
                'status' => 'fail',
                'message' => 'プロセスが見つかりません'
            ], 404);
        }
    }
}