@extends('layouts.app')

@push('styles')
    @vite('resources/css/index.css')
    @vite('resources/css/outsource/delivery/reissue.css')
@endpush

@section('title', '納品書・受領書再発行')
@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="pageHeaderBox rounded">
                納品書・受領書再発行
            </div>
            @if(session('success'))
                <div class="tableWrap borderLesstable">
                    <div class="success">
                        {{ session('success') }}
                    </div>
                </div>
            @endif
            @if(session('error'))
                <div class="tableWrap borderLesstable">
                    <div class="error">
                        {{ session('error') }}
                    </div>
                </div>
            @endif
            
            <div class="section">
                <h1 class="form-label bar indented">検索</h1>
                <form data-disregard-empty="true" class="overlayedSubmitForm with-js-validation" id="search-form">
                    <div class="box mb-3">
                        <div class="mb-3 d-flex">
                            <div class="mr-3">
                                <label class="form-label dotted indented">発注日</label>
                                <div class="d-flex">
                                    @include('partials._date_picker', [
                                        'inputName' => 'document_issue_date_from', 
                                        'attributes' => 'data-error-messsage-container=#date_issue_error_message data-field-name=発注日',
                                        'value' => Request::get('document_issue_date_from', date('Ym01') ?? '')])
                                    <span style="font-size:24px; padding:5px 10px;">
                                        ~
                                    </span>
                                    @include('partials._date_picker',[
                                        'inputName' => 'document_issue_date_to',
                                        'attributes' => 'data-error-messsage-container=#date_issue_error_message data-field-name=発注日',
                                        'value' => Request::get('document_issue_date_to', date('Ymt'))])
                                    
                                </div>
                                <div id="date_issue_error_message"></div>
                            </div>
    
                            <div class="mr-4">
                                <label class="form-label dotted indented">指示日</label>
                                <div class="d-flex">
                                    @include('partials._date_picker', ['inputName' => 'instruction_date_from', 
                                    'attributes' => 'data-error-messsage-container=#date_instruction_error_message data-field-name=指示日',
                                    'value' => Request::get('instruction_date_from', date('Ym01'))])
                                    <span style="font-size:24px; padding:5px 10px;">
                                        ~
                                    </span>
                                    @include('partials._date_picker', ['inputName' => 'instruction_date_to', 
                                    'attributes' => 'data-error-messsage-container=#date_instruction_error_message data-field-name=指示日',
                                    'value' => Request::get('instruction_date_to', date('Ymt'))])
                                </div>
                                <div id="date_instruction_error_message"></div>
                            </div>
    
                            <div class="mr-3">
                                <label class="form-label dotted indented">便No</label>
                                <div class="d-flex">
                                    <input type="text"
                                        class="acceptNumericOnly"
                                        id=""
                                        style="width: 40px"
                                        name="incoming_flight_number_start"
                                        value={{ Request::get('incoming_flight_number_start') }}>
                                    <span style="font-size:24px; padding:5px 10px;">
                                        ~
                                    </span>
                                    <input type="text"
                                        class="acceptNumericOnly"
                                        id=""
                                        style="width: 40px"
                                        name="incoming_flight_number_end"
                                        value={{ Request::get('incoming_flight_number_end') }}>
                                </div>
                            </div>
                        </div>
    
                        <div class="mb-3 d-flex">
                            <div class="mr-4">
                                <label class="form-label dotted indented">仕入先</label>
                                <div class="d-flex">
                                    <input type="text"
                                        id="supplier_code"
                                        data-field-name="仕入先"
                                        data-validate-exist-model="supplier"
                                        data-validate-exist-column="customer_code"
                                        data-inputautosearch-model="supplier"
                                        data-inputautosearch-column="customer_code"
                                        data-inputautosearch-return="supplier_name_abbreviation"
                                        data-inputautosearch-reference="supplier_name"
                                        name="supplier_code"
                                        value="{{ Request::get('supplier_code') ?? '' }}"
                                        class="mr-2 acceptNumericOnly" style="width:100px;">
                                    <input type="text" readonly
                                        id="supplier_name"
                                        name="supplier_name"
                                        value=""
                                        class="mr-2"
                                        style="margin-left:2px;" disabled>
                                    <button type="button" class="btnSubmitCustom js-modal-open ml-half"
                                        data-target="searchSupplierModal"
                                        data-query="searchProductNumberModal"
                                        data-reference="supplier_code">
                                        <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                        alt="magnifying_glass.svg">
                                    </button>
                                </div>
                                <div data-error-container="supplier_code"></div>
                            </div>
    
                            <div class="mr-3">
                                <label class="form-label dotted indented">発注No.</label>
                                <div class="d-flex">
                                    <input class="acceptNumericOnly" type="text" id="" name="order_no"
                                        maxlength="20"
                                        value="{{ Request::get('order_no') }}">
                                </div>
                            </div>
                        </div>
                        <br/>
                        <div class="text-center">
                            <button type="reset" class="btn btn-primary btn-wide"> 検索条件をクリア</button>
                            <button class="btn btn-primary btn-wide">検索</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="section">
                <h1 class="form-label bar indented">検索結果</h1>
                <div class="box">
                    @if(isset($reissueInvoiceLists))
                        <div class="mb-3">
                            @if($reissueInvoiceLists && $reissueInvoiceLists->total() > 0)
                                {{ $reissueInvoiceLists->total() }}件中、{{ $reissueInvoiceLists->firstItem() }}件～{{ $reissueInvoiceLists->lastItem() }} 件を表示してます
                            @endif
                            <table class="table table-bordered text-center table-striped-custom" style="width:100%">
                                <thead>
                                <tr>
                                    <th rowspan="2">
                                        <label class="checkBasic">
                                            <input type="checkbox" id="selectAll"/>
                                        </label>
                                    </th>
                                    <th rowspan="2">発注No.</th>
                                    <th>発注日</th>
                                    <th>指示日</th>
                                    <th>管理No.</th>
                                    <th>製品品番</th>
                                    <th>仕入先コード</th>
                                    <th rowspan="2">背番号</th>
                                    <th rowspan="2">枚数</th>
                                    <th rowspan="2">収容数</th>
                                    <th rowspan="2">数量</th>
                                </tr>
                                <tr>
                                    <th>発注区分</th>
                                    <th>便No.</th>
                                    <th>枝番</th>
                                    <th>品名</th>
                                    <th>仕入先名</th>
                                </tr>
                                </thead>
                                
                                <tbody>
                                    @forelse($reissueInvoiceLists as $reissueInvoice)
                                        <tr>
                                            <td rowspan="2">
                                                <label class="checkBasic">
                                                    <input type="checkbox" id="{{ $reissueInvoice->id }}"/>
                                                </label>
                                            </td>
                                            <td rowspan="2" class="text-left">{{ $reissueInvoice->order_no }}</td>
                                            <td class="text-left">{{ $reissueInvoice->document_issue_date?->format('Ymd') ?? null}}</td>
                                            <td class="text-left">{{ $reissueInvoice->instruction_date?->format('Ymd') ?? null }}</td>
                                            <td class="text-left">{{ $reissueInvoice->management_no }}</td>
                                            <td class="text-left">{{ $reissueInvoice->edited_part_number }}</td>
                                            <td class="text-left">{{ $reissueInvoice->supplier_code }}</td>
                                            <td rowspan="2" class="text-center">{{ optional($reissueInvoice->product)->uniform_number }}</td>
                                            <td rowspan="2" class="text-center">{{ $reissueInvoice->instruction_kanban_quantity }}</td>
                                            <td rowspan="2" class="text-center">{{ $reissueInvoice->instruction_number }}</td>
                                            <td rowspan="2" class="text-center">{{ $reissueInvoice->arrival_quantity }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-left">
                                                @if ($reissueInvoice->order_classification == 1)
                                                    通常
                                                @elseif ($reissueInvoice->order_classification == 2)
                                                    臨時
                                                @elseif ($reissueInvoice->order_classification == 3)
                                                    端数指示
                                                @elseif ($reissueInvoice->order_classification == 4)
                                                    随時
                                                @endif
                                            </td>
                                            <td class="text-left">{{ $reissueInvoice->incoming_flight_number }}</td>
                                            <td class="text-left">{{ $reissueInvoice->branch_number }}</td>
                                            <td class="text-left">{{ optional($reissueInvoice->product)->product_name }}</td>
                                            <td class="text-left">{{ $reissueInvoice->supplier?->customer_name }}</td>
                                        </tr>
                                    @empty

                                    @endforelse
                                    <form id="reissueForm" action="" method="GET" style="display: none;">
                                        @csrf
                                        <input type="hidden" name="invoice_ids" id="invoiceIdsInput">
                                    </form>
                                </tbody>
                            </table>
                        </div>
                        {{-- {{ $reissueInvoiceLists->links() }} --}}
                        {{ $reissueInvoiceLists->appends(request()->all())->links() }}
                    @else
                        <div class="mb-3">
                            0 件中、0 件～ 0 件を表示してます
                            <table class="table table-bordered text-center table-striped-custom" style="width: 70%">
                                <thead>
                                <tr>
                                    <th rowspan="2">
                                        <input type="checkbox" id="selectAll"/>
                                    </th>
                                    <th rowspan="2">発注No.</th>
                                    <th>発注日</th>
                                    <th>指示日</th>
                                    <th>管理No.</th>
                                    <th>製品品番</th>
                                    <th>仕入先コード</th>
                                    <th rowspan="2">背番号</th>
                                    <th rowspan="2">枚数</th>
                                    <th rowspan="2">収容数</th>
                                    <th rowspan="2">数量</th>
                                </tr>
                                <tr>
                                    <th>発注区分</th>
                                    <th>便No.</th>
                                    <th>枝番</th>
                                    <th>品名</th>
                                    <th>仕入先名</th>
                                </tr>
                                </thead>
                                
                                <tbody>
                                    <tr>
                                        <td colspan="11" class="text-center">検索結果はありません</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
            <div class="text-right">
                <div>
                    <a href="{{ route('outsource.order.slip.index') }}" class="btn btn-primary"> 発注伝票発行画面に戻る </a>
                    <a id="orderSlip" class="btn btn-success"> 納品書・受領書再発行 </a>
                </div>
            </div>
        </div>
    </div>

    @include('partials.modals.masters._search', [
        'modalId' => 'searchSupplierModal',
        'searchLabel' => '仕入先',
        'resultValueElementId' => 'supplier_code',
        'resultNameElementId' => 'supplier_name',
        'model' => 'Supplier',
        'query'=> "searchProductNumberModal",
        'reference' => "supplier_code"
    ])
@endsection

<style>
/* テーブルのセルを縦方向中央揃えにするスタイル */
.table-vertical-center th,
.table-vertical-center td {
  vertical-align: middle !important;
}

/* 既存のテーブルクラスに対する追加スタイル */
.table-bordered th,
.table-bordered td,
.table-striped-custom th,
.table-striped-custom td {
  vertical-align: middle !important;
}

/* チェックボックスを含むセルの調整 */
.checkBasic {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100%;
}

/* テキスト位置の調整 */
.text-right, 
.text-left, 
.text-center {
  vertical-align: middle !important;
}
</style>

@push('scripts')
    @vite(['resources/js/outsource/delivery/reissue.js'])
@endpush
