@extends('layouts.app')

@push('styles')
    @vite('resources/css/index.css')
    @vite('resources/css/modals/index.css')
    <style>
        .calendar-plugin input {
            text-align: left;
            width: 6rem !important;
        }
        .btnExport {
            cursor: pointer;
        }
    </style>
    </style>
    @vite('resources/css/sales/sale_plan_search.css')
@endpush

@section('title', '発注金額明細表発行')

@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="accordion">
                <h1><span>仕入先別購入金額詳細表示</span></h1>
            </div>

            <div class="pagettlWrap">
                <h1><span>仕入先別購入金額詳細表示</span></h1>
            </div>

            <div class="tableWrap bordertable" style="clear: both;">
                <div class="mb-2 ">
                    <table class="table table-bordered text-center table-striped-custom">
                        <thead>
                        <tr>
                            <th>入荷日</th>
                            <th>品番</th>
                            <th colspan="2" width="100px">規格</th>
                            <th width="150px">購入区分</th>
                            <th width="120px">伝票種類</th>
                            <th width="120px">伝票No.</th>
                            <th width="150px">依頼日</th>
                            <th width="150px">依頼者</th>
                        </tr>
                        <tr>
                            <th>仕入先名</th>
                            <th>品名</th>
                            <th>数量</th>
                            <th>単価</th>
                            <th>金額</th>
                            <th>伝票区分</th>
                            <th>承認方法</th>
                            <th>費目</th>
                            <th>購買依頼No.</th>
                        </tr>
                        </thead>
                        <tbody>
                            @php
                                $ctr = 0;
                                $total_amount = 0;
                            @endphp
                            @forelse($purchaseRecord as $record)
                            @php
                                $total_amount += $record->amount_of_money;
                                $purchase_category = '';
                                $slip_type = '';
                                $voucher_class = '';
                                $voucher_class = '';
                                $purchase_requisition_method  = '';
                                
                                if ($ctr == 0 || (($ctr % 2) == 0)) {
                                    $bg = 'white';
                                } else {
                                    $bg = '#f2f2f2';
                                }
                                if ($record->purchase_category == 1) {
                                    $purchase_category = '生産品';
                                } elseif ($record->purchase_category == 2) {
                                    $purchase_category = '購買品';
                                }
                                if ($record->slip_type == 1) {
                                    $slip_type = '納入伝票';
                                } elseif ($record->slip_type == 2) {
                                    $slip_type = '外注加工伝票';
                                } elseif ($record->slip_type == 3) {
                                    $slip_type = '購入材伝票';
                                }
                                if ($record->voucher_class == 1) {
                                    $voucher_class = '購入';
                                } elseif ($record->voucher_class == 6) {
                                    $voucher_class = '返品';
                                } elseif ($record->voucher_class == 9) {
                                    $voucher_class = '値引';
                                }
                                if ($record?->requisition?->approval_method_category == 1) {
                                    $purchase_requisition_method = 'システム';
                                } elseif ($record?->requisition?->approval_method_category == 2) {
                                    $purchase_requisition_method = '依頼書';
                                }

                            @endphp
                            <tr style="background-color: {{ $bg }} !important;">
                                <td class="tA-le">{{ $record->arrival_date->format("Y-m-d") }}</td>
                                <td class="tA-le">{{ $record->part_number }}</td>
                                <td class="tA-ri" colspan="2">{{ $record->standard }}</td>
                                <td class="tA-cn">{{ $purchase_category }}</td>
                                <td class="tA-cn">{{ $slip_type }}</td>
                                <td class="tA-ri">{{ $record->slip_no }} </td>
                                <td class="tA-le">{{ $record?->requisition?->requested_date->format("Y-m-d") }}</td>
                                <td class="tA-le">{{$record?->requisition?->employee?->employee_name}} </td>
                            </tr>
                            <tr style="background-color: {{ $bg }} !important;">
                                <td class="tA-le">{{ $record?->supplier?->customer_name }}</td>
                                <td class="tA-le">{{ $record->product_name }}</td>
                                <td class="tA-ri">{{ $record->quantity }}</td>
                                <td class="tA-ri">{{ $record->unit_price }}</td>
                                <td class="tA-ri">{{ number_format( $record->amount_of_money) }}</td>
                                <td class="tA-cn">{{ $voucher_class }}</td>
                                <td class="tA-cn">{{ $purchase_requisition_method }}</td>
                                <td class="tA-le">{{ $record?->item?->item_name }} </td>
                                <td class="tA-ri">{{ $record?->requisition?->requisition_number }}</td>
                            </tr>
                            @php
                                $ctr += 1;
                            @endphp
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">
                                        検索結果はありません
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            @if ($purchaseRecord)
                            <tr style="border: none;">
                                <td class="tA-le" style="border: none;"></td>
                                <td class="tA-le" style="border: none;"></td>
                                <td class="tA-le" style="background-color: #d9e2f3;" colspan="2">合計</td>
                                <td class="tA-ri" style="background-color: #d9e2f3;">{{ number_format($purchaseRecord->sum('amount_of_money')) }}</td>
                            </tr>
                            @endif
                            
                        </tfoot>
                    </table>
                </div>
            </div>
            @if ($purchaseRecord)
                {{ $purchaseRecord->appends(request()->except("page"))->links() }}
            @endif
            
            <div class="btnListContainer">
                <div class="btnContainerMainRight justify-content-flex-end">
                    <a class="btn btn-primary " href="{{ route('purchase.purchaseAmountSearch.index', request()->query()) }}">
                    一覧に戻る
                    </a>
                    <a href="{{ route('purchase.purchaseSupplierDetail.excel_export', request()->query()) }}" class="btn btn-success">
                    EXCEL出力
                    </a>
                </div>
            </div>
    </div>
@endsection
