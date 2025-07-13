@extends('layouts.app')
@push('styles')
    @vite([
        'resources/css/modals/index.css',
        'resources/css/search-modal.css',
        'resources/css/index.css',
      
    ])
@endpush
@section('title', '注文書再発行')
@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="accordion">
                <h1>
                    <span>注文書再発行</span>
                </h1>
            </div>

            <div class="pagettlWrap">
                <h1><span>検索</span></h1>
            </div>

            
            <form accept-charset="utf-8" class="overlayedSubmitForm with-js-validation" id="form_request"
                data-disregard-empty="true">
                <div class="tableWrap">
                    <div class="box" style="padding: 30px">
                        <div class="mb-3">
                            <label class="form-label dotted indented">注文書No</label>
                            <div>
                                <input type="text"
                                name="purchase_order_number"
                                value="{{ Request::get('purchase_order_number') }}"
                                class="middle-name"
                                style="width: 20%;"
                                >
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label dotted indented">発注日</label>
                            <div class="d-flex" style="width:30%">
                                @include('partials._date_picker', ['inputName' => 'order_date_from', 'value' => Request::get("order_date_from"), 
                                    'attributes' => 'data-error-messsage-container=#request_error_message  data-field-name=発注日'])
                                <span style="font-size:24px; padding:5px 10px;">
                                    ~
                                </span>
                                @include('partials._date_picker', ['inputName' => 'order_date_to',  'value' => Request::get("order_date_to"),
                                    'attributes' => 'data-error-messsage-container=#request_error_message  data-field-name=発注日'])
                            </div>

                            <div id ="request_error_message"></div>
                        </div>
                        <div class="">
                            <label class="form-label dotted indented">依頼者</label>
                            <div class="d-flex">
                                <input type="text" id="supplier_code" 
                                    data-field-name="依頼者"
                                    data-validate-exist-model="customer"
                                    data-validate-exist-column="customer_code"
                                    data-inputautosearch-model="supplier"
                                    data-inputautosearch-column="customer_code"
                                    data-inputautosearch-return="supplier_name_abbreviation"
                                    data-inputautosearch-reference="supplier_name"
                                    name="supplier_code" style="width:100px; margin-right: 10px;" value="{{ request()->get('supplier_code') }}">
                                <input type="text" id="supplier_name" name="supplier_name" readonly value="{{ request()->get('supplier_name') }}" style="margin-right: 10px;">
                                <button type="button" class="btnSubmitCustom js-modal-open search-btn text-white"
                                        data-target="searchSupplierModal" role="button">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                        fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                        <path
                                            d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z" />
                                    </svg>
                                </button>
                            </div>
                            <div data-error-container="supplier_code"></div>
                        </div>
                    </div>
    
                    <div class="text-center mb-2">
                        <button type="button" class="btn btn-primary" style="min-width: 200px" 
                            id="resetForm" 
                            data-clear-inputs
                            data-clear-form-target="#form_request">検索条件をクリア</button>
                        <button type="submit" class="btn btn-primary" style="min-width: 200px">検索</button>
                    </div>
                </div>

            </form>
            <div class="pagettlWrap">
                <h1><span>検索結果</span></h1>
            </div>
            <div class="tableWrap bordertable p-3" style="clear: both;">
                
                <ul class="headerList">
                    @if($items && $items->total() > 0)
                        {{ $items->total() }}件中、{{ $items->firstItem() }}件～{{ $items->lastItem() }} 件を表示しています
                    @endif
                </ul>
                <table class="tableBasic list-table bordered mt-3 mb-3" style="width: 1000px;">
                    <thead>
                        <tr class="p-2">
                            <th style="width: 180px;">注文書No.</th>
                            <th style="width: 100px;">発注日</th>
                            <th style="width: 180px;">発注先</th>
                            <th style="width: 120px;">操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $item)
                            <tr>
                                <td style="border: 1px solid #000;" class="tA-cn">
                                    {{ $item->purchase_order_number }}
                                </td>
                                <td style="border: 1px solid #000;" class="tA-cn">
                                    {{ $item->order_date ? $item->order_date->format('Y-m-d') : '' }}
                                </td>
                                <td style="border: 1px solid #000;" class="tA-cn">
                                    {{ $item?->supplier?->customer_name }}
                                </td>
                                <td style="border: 1px solid #000;" class="tA-cn">
                                    <a href="{{ route('purchase.orderReissue.excelExport', $item->id) }}?type=pdf&purchase_order_number={{$item->purchase_order_number}}" class="btn btn-sm btn-success">
                                    印刷
                                    </a>
                                    <a href="{{ route('purchase.orderReissue.excelExport', $item->id) }}?type=xlsx&purchase_order_number={{$item->purchase_order_number}}" class="btn btn-sm btn-success">
                                        EXCEL出力
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">検索結果はありません</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                @if ($items)
                    {{ $items->appends(request()->all())->links() }}
                @endif
            </div>
    
        </div>
    </div>

   
<script>
document.addEventListener('DOMContentLoaded', function() {
    const monthPicker = document.getElementById('monthPicker');
    
    if(!monthPicker) return;
    // 月が変更されたときのイベントリスナー
    monthPicker.addEventListener('change', function(e) {
        const selectedMonth = e.target.value;
        console.log('Selected month:', selectedMonth);
        
        // ここにAjaxリクエストなどの処理を追加できます
        // 例:
        /*
        fetch('/update-month', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                month: selectedMonth
            })
        })
        .then(response => response.json())
        .then(data => {
            console.log('Success:', data);
        })
        .catch(error => {
            console.error('Error:', error);
        });
        */
    });
});
</script>
    @include('partials.modals.masters._search', [
        'modalId' => 'searchSupplierModal',
        'searchLabel' => '発注先',
        'resultValueElementId' => 'supplier_code',
        'resultNameElementId' => 'supplier_name',
        'model' => 'Supplier'
    ])
@endsection
@push('scripts')
    @vite(['resources/js/purchase/order/reissue/index.js'])
@endpush
