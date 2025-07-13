<?php

namespace App\Services\Outsource\Delivery;

use App\Mail\Purchase\PurchaseApproverNotification;
use App\Models\Employee;
use App\Models\ProductNumber;
use App\Models\OutsourcedProcessing;
use App\Models\Customer;

use Carbon\Carbon;
use App\Constants\Constant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;
use Dompdf\Dompdf;
use Dompdf\Options;
use Milon\Barcode\Facades\DNS1DFacade;

class SpecifiedService
{

  public function pdf_export($request)
  {
    $issuanceInvoiceList = OutsourcedProcessing::query()
         ->search($request)
        ->first();
    if(!$issuanceInvoiceList){
      return redirect()->route('outsource.delivery.specified.index', $request->query())->with('error', '該当するデータが見つかりませんでした。');
    }

    $noCopy = $request->number_of_copies?? 1;
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

    $barcode = DNS1DFacade::getBarcodePNG($issuanceInvoiceList->order_no ?? "0", 'C128');
    $html = view('pages.outsource.delivery.specified.pdf_template', compact(
      'issuanceInvoiceList', 'barcode', 'noCopy'))->render();


    $dompdf = new Dompdf($pdfOptions);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    
    $fileName = '材料調達計画表一覧-'.now()->format('Ymd').'.pdf';
    $dompdf->stream($fileName);
    return redirect()->route('outsource.delivery.specified.index', $request->query())->with('success', 'PDFファイルをダウンロードしました。');
  }
}
