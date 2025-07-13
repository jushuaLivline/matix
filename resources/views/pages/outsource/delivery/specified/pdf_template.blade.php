<!DOCTYPE html>
<html lang="ja">

  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>納品書</title>
    <style>
      body {
        font-family: 'Noto Sans JP', sans-serif;
        font-size: 11pt;
      }

      .title {
        font-size: 18pt;
        margin-bottom: 40px;
      }

      .header {
        display: flex;
        justify-content: space-between;
      }

      .box {
        border: 2px solid black;
        padding: 5px;
        margin-top: 5px;
      }

      .table {
        width: 100%;
        border-collapse: collapse;
      }

      .table th,
      .table td {
        padding: 2px;
        text-align: center;
      }

      .text-left {
        text-align: left !important;
      }

      .text-center {
        text-align: center;
      }

      .text-right {
        text-align: right;
      }

      .barcode {
        text-align: center;
        margin-top: 10px;
      }

      .clear {
        clear: both;
      }

      .col-heading .col-left {
        float: left;
      }

      .col-heading .col-right {
        float: right;
      }

      .col-heading .col-right .date {
        text-align: right;
        margin-bottom: 20px;
      }

      .mb-10 {
        margin-bottom: 10px;
      }

      .mb-20 {
        margin-bottom: 20px;
      }

      .mb-30 {
        margin-bottom: 30px;
      }

      .mb-40 {
        margin-bottom: 40px;
      }

      .mb-50 {
        margin-bottom: 50px;
      }

      .col-left {
        float: left
      }

      .col-right {
        float: right
      }

      .border,
      .border td,
      .border th {
        border: 2px solid black;
        vertical-align: top;
      }

      .v-top,
      .v-top * {
        vertical-align: top;
      }

      .v-middle {
        vertical-align: middle;
      }

      .v-bottom {
        vertical-align: bottom;
      }

      .bt {
        border-top: 2px solid;
      }

      .bb {
        border-bottom: 2px solid;
      }

      .bl {
        border-left: 2px solid;
      }

      .br {
        border-right: 2px solid;
      }

      .w-10 {
        width: 10%
      }

      .w-20 {
        width: 20%
      }

      .w-30 {
        width: 30%
      }

      .w-40 {
        width: 40%
      }

      .w-50 {
        width: 50%
      }

      .w-60 {
        width: 60%
      }

      .w-70 {
        width: 70%
      }

      .w-80 {
        width: 80%
      }

      .w-90 {
        width: 90%
      }

      .w-100 {
        width: 100%
      }

      .pr-10 {
        padding-right: 10px;
      }

      .p-0 {
        padding: 0 !important;
      }
    </style>
  </head>

  <body>
    @for ($i = 1; $i <= $noCopy; $i++)
    <div class="container">
      <div class="col-heading mb-20">
      <div class="col-left w-40">
        <div class="title">外製 納品書</div>
        <div style="font-size: 15pt">メイティックス株式会社 御中</div>
      </div>
      <div class="col-right w-60 ">
        <div class="date">発行日：{{ now()->format('Y/m/d H:i') }}</div>
        <div class="box ">
        <div>
          納入者<br>{{ $issuanceInvoiceList->supplier_process_code ?? '' }} {{  $issuanceInvoiceList?->supplier?->customer_name ?? '' }}
        </div>
        </div>
      </div>
      </div>
      <div class="clear mb-20"></div>
      <table class="table border mb-20">
      <tr>
        <th>発注No.</th>
        <th>品番</th>
        <th>納期</th>
        <th>発注数</th>
        <th>収容数</th>
        <th>枚数</th>
      </tr>
      <tr>
        <td>{!! $issuanceInvoiceList->order_no ?? '&nbsp;' !!}</td>
        <td>{{ $issuanceInvoiceList->product_code ?? '' }}</td>
        <td></td>
        <td>{{ $issuanceInvoiceList->arrival_number ?? '' }}</td>
        <td>{{ $issuanceInvoiceList?->instruction_kanban_quantity ?? '' }}</td>
        <td>{{ $issuanceInvoiceList?->kanban_master?->number_of_accomodated ?? '' }}</td>
      </tr>
      </table>
      <table class="table w-100">
      <tr>
        <td rowspan="2" style="width: 5px; vertical-align: middle; line-height: 1;" class="bt bb bl">備<br>考</td>
        <td rowspan="2" class="text-left border v-middle" style="width: 150px;"></td>
        <td style="width: 5px;"></td>
        <td style="width: 50px;" class="border">納品日</td>
        <td colspan="4" style="width: 150px;" class="border text-left"></td>
        <td style="width: 5px;"></td>
        <td style="width: 150px;" class="border">受入部署</td>
      </tr>
      <tr>
        <td style="width: 5px;"></td>
        <td style="width: 50px;" class="border">納品数</td>
        <td colspan="4" class="border  text-left"></td>
        <td></td>
        <td rowspan="3" class="border v-top"></td>
      </tr>
      <tr>
        <td colspan="2"></td>
        <td></td>
        <td></td>
        <td colspan="4"></td>
        <td colspan="2"></td>
      </tr>
      <tr>
        <td colspan="7">
        @if (!empty($issuanceInvoiceList) && !empty($issuanceInvoiceList->order_no) && !empty($barcode))
            <img src="data:image/png;base64,{{ $barcode }}" alt="Barcode">
        @endif



        <p style="padding: 0;margin: 0; line-height: 1; font-size: 8pt"> {{ $issuanceInvoiceList->order_no ?? ''}}
        </p>
        </td>
        <td colspan="1" class="border" style="height: 120px;"> 受領日付印 </td>
        <td colspan="2"></td>
      </tr>
      </table>
      <div class="">
      <div class="col-heading" style="margin-top: 100px;">
        <div class="col-left w-60 ">
        <div class="title mb-10">外製 納品書</div>
        <table class="w-100 bb">
          <tr class="">
          <td class="v-bottom">納入者 </td>
          <td class="">{{ $issuanceInvoiceList->supplier_process_code ?? '' }} {{  $issuanceInvoiceList?->supplier?->customer_name ?? '' }}</td>
          <td class="v-bottom text-right">御中</td>
          </tr>
        </table>
        </div>
      </div>
      <div class="col-right w-30 text-right">
        <div class="date mb-10">発行日： {{ now()->format('Y/m/d H:i') }}</div>
        <div class="text-right" style="font-size: 15pt">メイティックス株式会社</div>
      </div>
      </div>
      <div class="clear mb-30"></div>
      <div class="">
      <div class="col-heading">
        <div class="col-left w-60 ">
        <table class="table mb-20 w-90">
          <tr>
          <th class="border w-30">発注No.</th>
          <th class="border w-30">品番</th>
          <th class="border w-40">納期</th>
          </tr>
          <tr>
          <td class="border">{!! $issuanceInvoiceList->order_no ?? '&nbsp;' !!}</td>
          <td class="border">{{ $issuanceInvoiceList->product_code ?? '' }}</td>
          <td class="border"></td>
          </tr>
          <tr>
          <td colspan="3">&nbsp;</td>
          </tr>
          <tr>
          <td class="border">納品日</td>
          <td class="border"></td>
          <td></td>
          </tr>
          <tr>
          <td class="border">納品数</td>
          <td class="border"></td>
          <td></td>
          </tr>
        </table>
        <table class="table mb-20 w-50">
          <tr>
          <td class="border" style="width: 10px; line-height: 1;">備<br />考</td>
          <td class="border"></td>
          </tr>
        </table>
        </div>
      </div>
      <div class="col-right w-30 text-right">
        <div class="border text-center w-70" style="height: 120px;">受領日付印</div>
      </div>
      </div>
      <div class="clear mb-20"></div>
    </div>
    <div class="clear mb-20"></div>


    @if ($i < $noCopy)
        <div style="page-break-before: always;"></div>
    @endif

   @endfor
  </body>

</html>