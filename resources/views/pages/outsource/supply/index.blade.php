@extends('layouts.app')

@push('styles')
    @vite('resources/css/index.css')
    @vite('resources/css/outsources/supply/index.css')
@endpush

@section('title', '外注加工支給品データ検索・一覧')

@section('content')
<div class="content">
    <div class="contentInner">
        <div class="pageHeaderBox rounded">
            外注加工支給品データ検索・一覧
        </div>

        <div class="section">
            <h1 class="form-label bar indented">検索</h1>

            <form data-disregard-empty="true" class="overlayedSubmitForm with-js-validation" id="supply-form">
                <div class="box mb-3">
                    <div class="mb-3 d-flex">
                        <div class="mr-3">
                            <label class="form-label dotted indented">支給日</label>
                            <div class="d-flex">
                                @include('partials._date_picker', ['inputName' => 'supply_date_from',
                                    'attributes' => 'data-error-messsage-container=#date_error_message data-field-name=支給日',  
                                    'value' => Request::get('supply_date_from', date('Ym01'))])
                                <span style="font-size:24px; padding:5px 10px;">
                                    ~
                                </span>
                                @include('partials._date_picker', ['inputName' => 'supply_date_to', 
                                'attributes' => 'data-error-messsage-container=#date_error_message data-field-name=支給日', 
                                'value' => Request::get('supply_date_to', date('Ymt'))])
                                <div class="error_msg"></div>
                            </div>
                            <div id="date_error_message" style="width: 100%;"></div>
                        </div>

                        <div class="mr-3">
                            <label class="form-label dotted indented">便No</label>
                            <div class="d-flex">
                                <input type="text"
                                    class="acceptNumericOnly"
                                    id=""
                                    data-error-messsage-container="#supply_flight_number_error_message"
                                    style="width: 40px"
                                    name="supply_flight_number_from"
                                    value="{{ Request::get('supply_flight_number_from') }}">
                                <span style="font-size:24px; padding:5px 10px;">
                                    ~
                                </span>
                                <input type="text"
                                    class="acceptNumericOnly"
                                    id=""
                                    data-error-messsage-container="#supply_flight_number_error_message"
                                    style="width: 40px"
                                    name="supply_flight_number_to"
                                    value="{{ Request::get('supply_flight_number_to') }}">
                            </div>
                            <div id="supply_flight_number_error_message" style="width: 100%;"></div>
                            
                        </div>

                        <div class="mr-4">
                            <label class="form-label dotted indented">支給先</label>
                            <div class="d-flex">
                                <input type="text" id="supplier_code" 
                                            data-field-name="支給先"
                                            data-error-messsage-container="#supplier_code-error"
                                            data-validate-exist-model="supplier"
                                            data-validate-exist-column="customer_code"
                                            data-inputautosearch-model="supplier"
                                            data-inputautosearch-column="customer_code"
                                            data-inputautosearch-return="supplier_name_abbreviation"
                                            data-inputautosearch-reference="supplier_name"
                                            name="supplier_code" style="width:100px; margin-right: 10px;" value="{{ request()->get('supplier_code') }}">
                                <input type="text" id="supplier_name" name="supplier_name" readonly value="{{ request()->get('supplier_name') }}" style="margin-right: 10px;">
                                <button type="button" class="btnSubmitCustom js-modal-open"
                                        data-target="searchSupplierModal"
                                        data-query-field="">
                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                        alt="magnifying_glass.svg">
                                </button>
                            </div>
                            <div id="supplier_code-error"></div>

                        </div>

                        <div class="mr-3">
                            <label class="form-label dotted indented">支給No.</label>
                            <div class="d-flex">
                                <input class="acceptNumericOnly" type="text" id="" name="subcontract_supply_no"
                                    maxLength="20"
                                    value="{{ Request::get('subcontract_supply_no') }}">
                            </div>
                        </div>
                    </div>
                    <br/>
                    <div class="d-flex justify-content-center align-items-center gap-2 position-relative">
                        <button type="button"
                            class="btn btn-primary btn-wide"
                            style="margin-right:5px;"
                            data-clear-inputs
                            data-clear-form-target="#supply-form"
                        >検索条件をクリア</button>
                        <button class="btn btn-primary btn-wide">検索</button>
                    
                        <a type="button" href="{{ route('outsource.supply.excel_export', Request::all()) }}""
                            class="btn btn-success btn-wide position-absolute right-0 {{ $subcontract_supply->total() == 0 ? 'btn-disabled' : '' }}"
                            id="exportBtn">
                            検索結果をEXCEL出力
                        </a>
                    </div>
                </div>
            </form>
        </div>
        <div class="pagettlWrap">
            <h1><span>検索結果</span></h1>
        </div>

        <div class="tableWrap bordertable" style="clear: both;">
            @if($subcontract_supply && $subcontract_supply->total() > 0)
                <ul class="headerList">
                    <li>{{ $subcontract_supply->total() }}件中、{{ $subcontract_supply->firstItem() }}件～{{ $subcontract_supply->lastItem() }} 件を表示してます</li>
                </ul>
            @endif
            <table class="table table-bordered text-center table-striped-custom">
                <thead>
                    <tr>
                        <th rowspan="2">支給No.</th>
                        <th>支給日</th>
                        <th>管理No.</th>
                        <th>製品品番</th>
                        <th>支給先コード</th>
                        <th>背番号</th>
                        <th rowspan="2">枚数</th>
                        <th rowspan="2">収容数</th>
                        <th rowspan="2">数量</th>
                        <th rowspan="2">操作</th>
                    </tr>
                    <tr>
                        <th>便No.</th>
                        <th>枝番</th>
                        <th>品名</th>
                        <th>仕入先名</th>
                        <th>サイクル</th>
                    </tr>
                </thead>
                
                <tbody>
                    @if (count($subcontract_supply) > 0)
                        @forelse($subcontract_supply as $supply)
                            <tr>
                                <td rowspan="2">{{ $supply->subcontract_supply_no }}</td>
                                <td class="text-center">{{ \Carbon\Carbon::parse($supply->supply_date)->format('Ymd') ?? null}}</td>
                                <td class="text-center">{{ $supply->management_no ?? null }}</td>
                                <td class="text-left">{{ $supply->product_code }}</td>
                                <td>{{ $supply->supplier_process_code }}</td>
                                <td class="text-left">{{ optional($supply->product_number)->uniform_number }}</td>
                                <td rowspan="2" class="text-left">{{ $supply->supply_kanban_quantity }}</td>
                                <td rowspan="2" class="text-left">{{ optional($supply->kanban)->number_of_accomodated }}</td>
                                <td rowspan="2" class="text-right">{{ $supply->supply_quantity }}</td>
                                <td rowspan="2" class="text-center">
                                    <a href="{{ route('outsource.supplyEdit', $supply->subcontract_supply_no) }}" class="btn btn-blue" id="edit" data-input-enable>編集</a>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-right">
                                    {{ $supply->supply_flight_no }}
                                </td>
                                <td class="text-center">{{ $supply->branch_number }}</td>
                                <td class="text-left">{{ optional($supply->product_number)->product_name }}</td>
                                <td>{{ optional($supply->customer)->customer_name }}</td>
                                <td class="text-center"></td>
                            </tr>
                        @empty
                        @endforelse
                    @else
                        <tr>
                            <td colspan="11" class="text-center">検索結果はありません</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        {{ $subcontract_supply->appends(request()->all())->links() }}
    </div>
</div>

@include('partials.modals.masters._search', [
    'modalId' => 'searchSupplierModal',
    'searchLabel' => '仕入先',
    'resultValueElementId' => 'supplier_process_code',
    'resultNameElementId' => 'supplier_process_name',
    'model' => 'Supplier',
    'query'=> "searchProductNumberModal",
    'reference' => "supplier_code"
])
@endsection
