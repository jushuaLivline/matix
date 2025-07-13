<?php

namespace App\Services\Material\Order;

use App\Mail\Purchase\PurchaseApproverNotification;
use App\Models\Employee;
use App\Models\ProductNumber;
use App\Models\SupplyMaterialOrder;
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

class DetailService
{

  public function __construct()
  {
    $this->supplyOrder = new SupplyMaterialOrder();
  }

  public function PDF_export($request)
  {
    $groupedOrders = $this->supplyOrder->orderDetailPDF($request)->cursor();
    $orders = collect();
    $recordIds = [];

    foreach ($groupedOrders as $order) {
      $groupKey = "{$order->customer_name}-{$order->instruction_date}-{$order->instruction_no}";
      $orders->put($groupKey, $orders->get($groupKey, collect())->push($order));
      $recordIds[] = $order->id; // Collect IDs efficiently
    }

    if ($orders->isEmpty()) {
      return response()->json(['error' => '注文が見つかりませんでした'], 404);
    }

    // PDF Generation
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

    $html = view('pages.material.order.detail.pdf_template', compact('orders'))->render();

    $dompdf = new Dompdf($pdfOptions);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    $fileName = "発注明細書発行_" . now()->format('Ymd') . ".pdf";
    $pdfContent = $dompdf->output();

    // Queue the update instead of blocking the request
    if ($request->issue_classification == 'no-issue') {
      dispatch(function () use ($recordIds) {
          SupplyMaterialOrder::whereIn('id', $recordIds)->update(['document_issue_date' => now()]);
      })->delay(now()->addSeconds(2)); // Run asynchronously
    }

    return response()->streamDownload(fn() => print($pdfContent), $fileName, [
      'Content-Type' => 'application/pdf',
      'Content-Disposition' => 'attachment; filename="' . $fileName . '"'
    ])->send();
  }


}
