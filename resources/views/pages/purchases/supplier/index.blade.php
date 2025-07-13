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
    @vite('resources/css/sales/sale_plan_search.css')
@endpush

@section('title', '仕入先別購入金額一覧')

@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="accordion">
                <h1><span>仕入先別購入金額一覧</span></h1>
            </div>

            <div class="pagettlWrap">
                <h1><span>仕入先別購入金額一覧</span></h1>
            </div>
            <form accept-charset="utf-8" class="mt-4 overlayedSubmitForm with-js-validation" data-disregard-empty="true" id="purchaseRecordForm">
                <div class="box mb-3">
                    <div class="mb-4 d-flex">
                        <div class="mr-3" style="width: 30%">
                            <label class="form-label dotted indented ">年月</label>
                            <div class="d-flex ">
                                @include('partials._date_picker_year_month', [
                                        'inputName' => 'year_month_start', 
                                        'attributes' => 'data-error-messsage-container=#arrival_date_error_message', 
                                        'maxlength'=>'6', 'minlength'=>'6'])
                                <span class="symbol-tilde">
                                    ~
                                </span>
                                @include('partials._date_picker_year_month', [
                                        'inputName' => 'year_month_end', 
                                        'attributes' => 'data-error-messsage-container=#arrival_date_error_message',  
                                        'maxlength'=>'6', 'minlength'=>'6',])
                            </div>
                            <div id="arrival_date_error_message"></div>
                        </div>
                        <div class="mr-3">
                            <label class="form-label dotted indented">購入区分</label>
                            <div class="d-flex mt-2">
                                @php
                                    $purchase_category = request()->purchase_category ?? 0;
                                @endphp
                                <p class="formPack">
                                    <label class="radioBasic">
                                        <input type="radio" name="purchase_category" value="0" {{ $purchase_category == 0 ? 'checked' : '' }}>
                                        <span>すべて</span>
                                    </label>
                                </p>
                                <p class="formPack">
                                    <label class="radioBasic">
                                        <input type="radio" name="purchase_category" value="1" {{ $purchase_category == 1 ? 'checked' : '' }}>
                                        <span>生産品</span>
                                    </label>
                                </p>
                                <p class="formPack">
                                    <label class="radioBasic">
                                        <input type="radio" name="purchase_category" value="2" {{ $purchase_category == 2 ? 'checked' : '' }}>
                                        <span>購買品</span>
                                    </label>
                                </p>
                            </div>
                        </div>
                        
                    </div>

                    <a href="{{ route("purchase.purchaseAmountSearch.excel_export", Request::all()) }}" class="float-right btn bg-success">検索結果をEXCEL出力</a>

                    <div class="text-center">
                        <button type="button" class="btn btn-blue" style="min-width: 200px"
                            data-clear-inputs
                            data-clear-form-target="#purchaseRecordForm">検索条件をクリア</button>
                        <button type="submit" class="btn btn-blue" style="min-width: 200px">検索</button>
                    </div>
                </div>
            </form>
            <div class="pagettlWrap mt-2">
                <h1><span>検索結果</span></h1>
            </div>
            <div class="mt-4 tableWrap bordertable" style="clear: both;">
                <div class="mb-2">
                    @if ($purchaseRecord->total() > 0)
                        {{ $purchaseRecord->total()  }}件中、{{ $purchaseRecord->firstItem()  }}件～{{ $purchaseRecord->lastItem()  }}件を表示してます
                    @endif
                    <table class="table table-bordered text-center table-striped-custom w-50">
                        <thead>
                        <tr>
                            <th>仕入先コード</th>
                            <th>仕入先名</th>
                            <th>件数</th>
                            <th>金額</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                            @forelse($purchaseRecord as $data)
                                <tr>
                                    <td class="tA-le pt-15c text-center" >{{ $data->supplier->customer_code }}</td>
                                    <td class="tA-le pt-15c" >{{ $data->supplier->customer_name }}</td>
                                    <td class="tA-ri pt-15c" >{{ $data->count_of_records }}</td>
                                    <td class="tA-ri pt-15c" >{{ number_format($data->sum_amount_of_money, 0, '.', ',')  }}</td>
                                    @php
                                        $redirection = route('purchase.purchaseAmountSearch.show', array_merge(Request::all(), [$data?->supplier]));
                                    @endphp
                                    <td class="tA-le" style="width: 50px;">
                                        <a href="{{ $redirection }}" class="btn btn-blue" style="min-width: 50px">詳細</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">検索結果はありません</td>
                                </tr>
                            @endforelse
                            @if ($purchaseRecord && count($purchaseRecord) > 0)
                                <tr style="border: none;">
                                    <td class="tA-le" style="border: none;"></td>
                                    <td class="tA-le" style="border: none;"></td>
                                    <td class="tA-le text-center" style="background-color: #d9e2f3;">合計</td>
                                    <td class="tA-ri" style="background-color: #d9e2f3;">{{ number_format($purchaseRecord->sum('sum_amount_of_money'), 0, '.', ',')  }}</td>
                                </tr>
                            @endif
                        </tbody>                        
                    </table>
                </div>
                @if ($purchaseRecord)
                    {{ $purchaseRecord->appends(request()->except("page"))->links() }}
                @endif
            </div>
        </div>
    </div>
    @include('partials.modals.masters._search', [
        'modalId' => 'searchSupplierModal',
        'searchLabel' => 'プロジェクトNo.',
        'resultValueElementId' => 'supplier_code',
        'resultNameElementId' => 'supplier_name',
        'model' => 'Supplier'
    ])
@endsection


