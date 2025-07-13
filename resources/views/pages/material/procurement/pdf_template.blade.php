<!DOCTYPE html>
<html lang="en">

  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>材料調達計画表一覧</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP&display=swap" rel="stylesheet">
    <style>
   

      body {
        font-family: 'Noto Sans JP', sans-serif;
        font-size: 10pt;
      }

      p {
        margin: 0 !important;
        line-height: 1;
      }

      .theading {
        float: left;
        font-size: 15pt;
      }

      .plan {
        text-align: center;
        width: 100%;
        float: left;
      }
      .ml-auto p {
        margin-bottom: 10px;
      }

      .date_now,
      .pages {
        text-align: right;
      }
      .bottom_tontent {
        width: 240px;
        float: right;
      }
      table {
        width: 100%;
      }

      table,
      th,
      td {
        border: 1px solid #ccc;
        border-collapse: collapse;
        border-spacing: 0;
        vertical-align: middle;
      }
      .text-center{ text-align: center;}
      .text-left{ text-align: left;}
      .text-right{ text-align: right;}

      @page {
        margin: 5mm 5mm 5mm 5mm;
        /* Adjust margins as needed */
      }
    </style>
  </head>

  <body>
    <div class="container">
    <div class="heading">
      <div class="theading">{{ $authorizationName->authority_name ?? null}}</div>
      <div class="ml-auto">
        <p class="date_now">作成: &nbsp; {{ now() }}</p>
        <p class="pages">ページ &nbsp; 1 / 1</p>
        <div class="bottom_tontent">
          <p class="">メイティックス株式会社</p>
          <p class="creator">作成: &nbsp; {{ Auth::user()->employee_name }}</p>
          <p class="tel">TEL 0563-59-1771 &nbsp; FAX 0563-59-2213</p>
        </div>
        <div style="clear: both;"></div>
      </div>
      <div class="plan">
        <span class="">{{ now()->format('Y')}}</span> <span>年</span>
        <span class="">{{now()->format('m')}}</span> <span>　月</span>
        <span class=""> 材料調達計画表 </span>
      </div>
      <div style="clear: both;"></div>
    </div>
    <table class="tableBasic list-table">
      <thead>
        <tr>
          <td class="" style="width: 70px">材料品番</td>
          <td class="">グループ</td>
          <td class="">計画</td>
          <td class="">計画数</td>
          <td class="">区分</td>
          @foreach($dates as $date)
            <th class="text-center" style="min-width: 10px; color: {{ $date['isWeekend'] ? 'red' : 'black' }}; font-weight: bold;">
              {{ $date['day'] }}
            </th>
          @endforeach
          <td class="text-center">翌月内示</td>
          <td class="text-center">翌々月内示</td>
        </tr>
      </thead>
      <tbody>
        @forelse($procurementPlanLists as $key => $result)
          @php
            $orders = $result->supplyMaterialOrders->keyBy(fn($order) => $order->instruction_date->format('Y-m-d'));
            $arrivals = $result->supplyMaterialArrivalManufacturers->keyBy(fn($arrival) => $arrival->arrival_day->format('Y-m-d'));
            $current_month = $result->current_month;
            $instruction_no = $result?->supplyMaterialOrders?->sum('instruction_number');
            
            if($result->current_month == 0){
                $current_month = ($result->kanban_status == 'shiji') ? 0 : '';
            }
            if($instruction_no == 0){
                $instruction_no = ($result->kanban_status == 'shiji') ? 0 : '';
            }
          @endphp

        {{-- Plan Row --}}
        <tr data-kanban-status="{{ $result->kanban_status }} " data-part-number=" {{ $result->part_number }}">
          <td class="text-left" rowspan="2">
            @if($result->kanban_status == 'shiji')
              <a
              href="{{ route('material.procurement.create', array_merge(['part_number' => $result->edited_part_number], Request::all())) }}">
              {{ $result->edited_part_number }}
              </a>
            @else
              {{ $result->edited_part_number }}
            @endif
            @if ($result->edited_part_number)
              <br />{{ $result->product_name }}
            @endif
          </td>
          <td class="text-center" rowspan="2">
            {{ $result?->group?->supply_material_group ?? '__' }}
          </td>
          <td class="text-right" rowspan="2">
              {{ $current_month }} 
          </td>
          <td class="text-right" rowspan="2">
              {{$instruction_no}}
          </td>

          <td>計画</td>
          @php $counter = 0 @endphp
          @foreach($dates as $date)
            <td class="text-right">
              @if($result->kanban_status == 'shiji')
              @php $counter++; @endphp
                {{-- {{ $orders[$date['date']]->instruction_number ?? '' }} --}} {{ $result['day_' . $counter] }}
                @endif
            </td>
          @endforeach
          <td class="text-right" rowspan="2">
              {{ $result->next_month ?? 0}}
          </td>
          <td class="text-right" rowspan="2">
              {{ $result->two_months_later ?? 0}}
          </td>
          </tr>
          {{-- Arrival Row --}}
          <tr>
          <td>入荷</td>
          @foreach($dates as $date)
            <td class="text-right">
              @if($result->kanban_status == 'shiji')
                {{ $arrivals[$date['date']]->arrival_quantity ?? '' }}
              @endif
            </td>
            @endforeach
          </tr>

     
       @empty
          <tr>
              <td colspan="{{ 7 + count($dates) }}" style="text-align:center;">検索結果はありません</td>
          </tr>
        </tbody>

        @endforelse
      </table>


     

    </div>



  </body>

</html>