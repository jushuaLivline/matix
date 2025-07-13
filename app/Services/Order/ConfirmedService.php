<?php

namespace App\Services\Order;

use App\Mail\Purchase\PurchaseApproverNotification;
use App\Models\FirmOrder;
use App\Models\ShipmentRecord;

use Carbon\Carbon;
use App\Constants\Constant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;
use Dompdf\Dompdf;
use Dompdf\Options;

class ConfirmedService
{
  public function __construct()
  {
    // Constructor logic if needed
    $this->firmOrder = new FirmOrder();
    $this->shipmentRecord = new ShipmentRecord();
  }

  public function index($request)
  {
    // Fetch Firm Orders
    $firmOrders = $this->firmOrder->getConfirmedOrder($request)->get();

    // Fetch Shipment Records
    $shipmentRecords = $this->shipmentRecord->getConfirmedOrder($request)->get();

    return [$firmOrders, $shipmentRecords];
  }

  public function prepareData($firmOrders, $shipmentRecords)
  {
    $data = $firmOrders->mapWithKeys(function ($firmOrder) {
      return [
        $firmOrder->part_number => [
          [
            'due_date' => $firmOrder->due_date,
            'delivery_destination_code' => $firmOrder->delivery_destination_code,
            'classification' => $firmOrder->classification,
            'part_number' => $firmOrder->product?->customer_edited_product_number,
            'product_name' => $firmOrder->product?->product_name,
            'plant' => $firmOrder->plant,
            'acceptance' => $firmOrder->acceptance,
            'uniform_number' => $firmOrder->uniform_number,
            'number_of_accommodated' => $firmOrder->number_of_accommodated,
            'daily_reports' => [
              $firmOrder->delivery_no => [
                'kanban_number' => $firmOrder->kanban_number,
                'instruction_number' => $firmOrder->instruction_number,
                'shipment_kanban_number' => null,
                'shipment_instruction_number' => null,
              ]
            ]
          ]
        ]
      ];
    })->toArray();

    // Merge shipment records into the daily reports
    foreach ($shipmentRecords as $shipmentRecord) {
      $partNumber = $shipmentRecord->part_number;
      $deliveryNo = $shipmentRecord->shipment_delivery_no;

      if (isset($data[$partNumber][0]['daily_reports'][$deliveryNo])) {
        $data[$partNumber][0]['daily_reports'][$deliveryNo]['shipment_kanban_number'] = $shipmentRecord->shipment_kanban_number;
        $data[$partNumber][0]['daily_reports'][$deliveryNo]['shipment_instruction_number'] = $shipmentRecord->shipment_instruction_number;
      }
    }

    return $data;
  }


  public function getUniqueSortedDeliveryNos($firmOrders, $shipmentRecords): array
  {
    return collect(array_merge(
      $firmOrders->pluck('delivery_no')->toArray(),
      $shipmentRecords->pluck('shipment_delivery_no')->toArray()
    ))->filter()->unique()->sort()->values()->all();
  }

}
