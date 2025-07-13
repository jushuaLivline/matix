<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Payment;
use App\Models\PaymentDetail;
use App\Models\PurchaseRecord;
use Illuminate\Http\Request;

class MonthyController extends Controller
{
    function CollectAIPurchaseData(Request $request){
        return view("pages.monthly.collect_ai_purchase_data");
    }


    function salesClosingProcess(Request $request){
        return view("pages.monthly.sales_closing_process");
    }

    function toyobillingDataImport(Request $request){
        return view("pages.monthly.toyobilling_data_import");
    }


    // MONTHLY-127 購入実績アンマッチ一覧
    function listUnmatchedPurchaseResults(Request $request){
        if (count($request->query()) > 0) {
            $query = PurchaseRecord::query();

            if($request->supplier_code){
                $query->where("supplier_code", $request->supplier_code);
            }
            
            $purchaseRecords = $query->paginate();
        }
        return view("pages.monthly.list_unmatched_purchase_results", [
            'purchaseRecords' => $purchaseRecords ?? [],
        ]);
    }

    function ToyoBillingDataOutput(Request $request){
        return view("pages.monthly.toyo_billing_data_output");
    }

    function PurchasingClosingProcess(Request $request){
        return view("pages.monthly.purchasing_closing_process");
    }

    function paymentScheduleList(Request $request){
        $banks = Customer::where('supplier_tag', '1')->whereNotNull('transfer_source_bank_code')->groupBy('transfer_source_bank_code')->get()->pluck('transfer_source_bank_code')->toArray();
        // dd($banks);
        if (count($request->query()) > 0) {
            $data = Payment::where('year_and_month', $request->date)->whereHas('supplier', function ($q) use ($request) {
                $q->where('supplier_tag', 1);
                if ($request->bank != '_all') {
                    $q->where('transfer_source_bank_code', $request->bank);
                }
            } )->groupBy('supplier_code')->paginate(20);
            // dd($data);
        }
        return view("pages.monthly.payment_schedule_list", [
            'data' => $data ?? [],
            'banks' => $banks ?? [],
        ]);
    }

    function paymentScheduleDetails(Request $request){
        if ((($request->supplier ?? '') == '') || (($request->period ?? '') == '')) {
            abort(404);
        }
        $year = substr($request->period, 0, 4);
        $month = substr($request->period, -2);

        $data = PaymentDetail::where('supplier_code', $request->supplier)->whereMonth('arrival_day', $month)->whereYear('arrival_day', $year)->paginate(20);
        return view("pages.monthly.payment_schedule_details", [
            'data' => $data ?? [],
        ]);
    }

    function paymentTermsChange(Request $request, PaymentDetail $paymentDetailId){
        $exploded_date = explode('-', $paymentDetailId->arrival_day);
        $date = $exploded_date[0] . $exploded_date[1];

        return view("pages.monthly.payment_terms_change", [
            'data' => $paymentDetailId,
            'date' => $date,
        ]);
    }

    function paymentTermsChangeProcess(Request $request, PaymentDetail $paymentDetailId){
        $update = [];
        if (($request->transfer_amount_edit ?? '') != '') {
            $update['transfer_amount'] = $request->transfer_amount_edit;
        }
        if (($request->bill_amount_edit ?? '') != '') {
            $update['bill_amount'] = $request->bill_amount_edit;
        }
        if (($request->tax_classification ?? '') != '') {
            $update['tax_classification'] = $request->tax_classification;
        }
        
        $paymentDetailId->update($update);

        return redirect()->back();
    }

    function accountingClosing(Request $request){
        return view("pages.monthly.accounting_closing");
    }
}
