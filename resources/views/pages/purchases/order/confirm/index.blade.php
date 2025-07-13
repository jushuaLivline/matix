@extends('layouts.app')

@push('styles')
    @vite('resources/css/index.css')
@endpush

@section('title', '発注内容確認')
@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="accordion">
                <h1>
                    <span>発注内容確認</span>
                </h1>
            </div>
            @if (session('success'))
                <div id="card"
                    style="background-color: #ffffff; padding: 20px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
                    <div style="text-align: left;">
                        <p style="font-size: 18px; color: #0d9c38;">
                            {{ session('success') }}
                        </p>
                    </div>
                </div>
            @endif

            <div class="pagettlWrap">
                <h1><span>発注内容確認</span></h1>
            </div>

            <div class="tableWrap borderLesstable inputFormArea">
                <style>
                    @media print {
                        .formsetBox dd input {
                            display: block !important; 
                            margin-left: -40px;
                        }
                        #supplier_code {
                            margin-top: 20px !important;
                            margin-bottom: 5px !important;
                        }
                    }
                </style>
                <div>
                    <dl class="formsetBox mb-3">
                        <dt>発注先</dt>
                        <dd style="display: block">
                            <input type="text" name="customer_code"
                                value="{{ $firstSupplierData->supplier_code ?? '' }}" id="supplier_code"
                                style="width: 150px;" readonly>
                
                            <input type="text" readonly name="supplier_name" id="supplier_name"
                                value="{{ $firstSupplierData->supplier->customer_name ?? '' }}" style="width: 250px;"
                                class="middle-name">
                            
                            <div class="error_msg"></div>
                        </dd>
                    </dl>
                </div>

                <table style="width: 100%; border: 1px solid #000;" class="mb-4">
                    <thead style="background-color: #d3d3d3;">
                        <tr>
                            <th style="border: 1px solid #000; height: 20px; padding: 8px;">
                                購買依頼No.
                            </th>
                            <th style="border: 1px solid #000; height: 20px; padding: 8px;">
                                ライン
                            </th>
                            <th style="border: 1px solid #000; height: 20px; padding: 8px;">
                                品番・品名・規格
                            </th>
                            <th style="border: 1px solid #000; height: 20px; padding: 8px;">
                                数量
                            </th>
                            <th style="border: 1px solid #000; height: 20px; padding: 8px;">
                                単位
                            </th>
                            <th style="border: 1px solid #000; height: 20px; padding: 8px;">
                                単価
                            </th>
                            <th style="border: 1px solid #000; height: 20px; padding: 8px;">
                                金額
                            </th>
                            <th style="border: 1px solid #000; height: 20px; padding: 8px;">
                                依頼者
                            </th>
                            <th style="border: 1px solid #000; height: 20px; padding: 8px;">
                                納期
                            </th>
                            <th style="border: 1px solid #000; height: 20px; padding: 8px;">
                                備考
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($items)
                            @foreach ($items as $item)
                                <tr>
                                    <td style="border: 1px solid #000; height: 20px; padding: 10px;" class="tA-cn">
                                        {{ $item->requisition_number }}
                                    </td>
                                    <td style="border: 1px solid #000; height: 20px; padding: 10px;" class="tA-cn">
                                        {{ $item->line?->line_name }}
                                    </td>
                                    <td style="border: 1px solid #000; height: 20px; padding: 10px;" class="tA-cn">
                                        {{  implode("・", array_filter([$item->part_number, $item->product_name, $item->standard]))  }}
                                    </td>
                                    <td style="border: 1px solid #000; height: 20px; padding: 10px;" class="tA-cn">
                                        {{ $item->quantity }}
                                    </td>
                                    <td style="border: 1px solid #000; height: 20px; padding: 10px;" class="tA-cn">
                                        {{ $item->unit?->name }}
                                    </td>
                                    <td style="border: 1px solid #000; height: 20px; padding: 10px;" class="tA-cn">
                                        {{ $item->unit_price }}
                                    </td>
                                    <td style="border: 1px solid #000; height: 20px; padding: 10px;" class="tA-cn">
                                        {{ $item->amount_of_money }}
                                    </td>
                                    <td style="border: 1px solid #000; height: 20px; padding: 10px;" class="tA-cn">
                                        {{ $item->employee?->employee_name }}
                                    </td>
                                    <td style="border: 1px solid #000; height: 20px; padding: 10px;" class="tA-cn">
                                        {{ $item->deadline?->format('Y/m/d') }}
                                    </td>
                                    <td style="border: 1px solid #000; height: 20px; padding: 10px;" class="tA-cn">
                                        {{ $item->remarks }}
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr class="p-2">
                                <td colspan="10" class="tA-cn">
                                    検索結果はありません
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
                </table>
                
            </div>
            <div class="float-right">
                <ul class="buttonlistWrap">
                    <li>
                        <a href="{{ route('purchase.orderProcess.index') }}" class="buttonBasic bColor-ok"
                            style="max-width: 200px;">
                            一覧に戻る
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('purchase.orderConfirm.excelExport', ['selected_items' => Request::input('selected_items')]) }}"
                            class=" bColor-green btn btn-success {{ is_array($items) && count($items) > 0 ? '' : 'btn-disabled' }}" style="min-width: 200px;">
                            EXCEL出力
                        </a>
                    </li>
                    <li>
                        <a href="#" class=" bColor-green btn btn-success" style="min-width: 200px;" id="printButton">印刷</a>
                    </li>
                    <li>
                        <form action="{{ route('purchase.orderConfirm.store', ['selected_items' => Request::input('selected_items')]) }}"
                        method="POST"
                        id="confirmOrderForm">
                        @csrf

                        <button type="button" id="confirmOrder"
                            class="buttonBasic bColor-green btn btn-success" 
                            style="max-width: 200px;">発注</button>
                        </form>
                       
                    </li>
                </ul>
            </div>
        </div>
    </div>
    @include('partials.modals.masters._search', [
        'modalId' => 'searchCustomerModal',
        'searchLabel' => '得意先',
        'resultValueElementId' => 'customer_code',
        'resultNameElementId' => 'customer_name',
        'model' => 'Customer',
    ])
@endsection
@push('scripts')
@vite('resources/js/purchase/order/confirm/index.js')
@endpush
