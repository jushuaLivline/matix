@extends('layouts.app')

@push('styles')
    @vite('resources/css/index.css')
    @vite('resources/css/modals/index.css')
    @vite('resources/css/search-modal.css')
    @vite('resources/css/estimates/index.css')
@endpush

@section('title', '入荷実績一覧')
@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="accordion">
                <h1><span>入荷実績一覧</span></h1>
            </div>

            <div class="pagettlWrap">
                <h1><span>検索</span></h1>
            </div>

            <form class="with-js-validation" id="outsourceArrivalList" data-disregard-empty="true" accept-charset="utf-8">
                <div class="tableWrap borderLesstable inputFormArea">
                    <table class="tableBasic arrival-search w-100">
                        <tbody>
                            <tr>
                                <td class="w-35">
                                    <dl class="formsetBox mr-3">
                                        <dt>入荷日</dt>
                                        <dd>

                                        <div class="d-flex">
                                            @include('partials._date_picker', ['inputName' => 'arrival_day_start',
                                                'attributes' => 'data-error-messsage-container=#date_error_message data-field-name=入荷日',  
                                                'value' => Request::get('arrival_day_start', date('Ym01'))])
                                            <span style="font-size:24px; padding:5px 10px;">
                                                ~
                                            </span>
                                            @include('partials._date_picker', ['inputName' => 'arrival_day_end', 
                                            'attributes' => 'data-error-messsage-container=#date_error_message data-field-name=入荷日', 
                                            'value' => Request::get('arrival_day_end', date('Ymt'))])
                                            
                                        </div>
                                        <div id="date_error_message"></div>
                                        </dd>
                                    </dl>
                                </td>
                                <td class="w-15 mr-0">
                                    <dl class="formsetBox">
                                        <dt>便No</dt>
                                        <dd>
                                            <p class="formPack w-30">
                                                <input type="text" name="flight_no_from"
                                                       class="text-right acceptNumericOnly" style="width: 100%;"
                                                       value="{{ request()->get('flight_no_from') }}">
                                            </p>
                                            <p class="formPack">～</p>
                                            <p class="formPack w-30">
                                                <input type="text" name="flight_no_to"
                                                       class="text-right acceptNumericOnly" style="width: 100%;"
                                                       value="{{ request()->get('flight_no_to') }}">
                                            </p>
                                        </dd>
                                    </dl>
                                </td>
                                <td class="w-35 mr-0">
                                    <dl class="formsetBox">
                                        <dt>仕入先</dt>
                                        <dd>
                                            <div class="formPack fixedWidth fpfw25p">
                                                <input type="text" name="supplier_code" id="supplier_code"
                                                    data-field-name="仕入先"
                                                    data-error-messsage-container="#supplier_code_error"
                                                    data-validate-exist-model="supplier"
                                                    data-validate-exist-column="customer_code"
                                                    data-inputautosearch-model="supplier"
                                                    data-inputautosearch-column="customer_code"
                                                    data-inputautosearch-return="supplier_name_abbreviation"
                                                    data-inputautosearch-reference="supplier_name"
                                                    class="text-left searchOnInput Supplier acceptNumericOnly" minlength="6"
                                                    maxlength="6"
                                                    onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                                    value="{{ request()->get('supplier_code') }}">

                                                   
                                            </div>

                                            <div class="formPack">
                                                <input type="text" readonly
                                                    name="supplier_name" id="supplier_name"
                                                    value="{{ request()->get('supplier_name') }}"
                                                    class="middle-name text-left">
                                            </div>
                                            <div class="formPack">
                                                <button type="button" class="btnSubmitCustom js-modal-open"
                                                        data-target="searchSupplierModal">
                                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                         alt="magnifying_glass.svg">
                                                </button>
                                            </div>
                                        
                                            <div id="supplier_code_error"></div>
                                        </dd>
                                    </dl>
                                </td>
                            </tr>
                            <tr>
                                <td class="w-20 mr-0">
                                    <dl class="formsetBox">
                                        <dt>発注No</dt>
                                        <dd>
                                            <div class="formPack fixedWidth">
                                                <input type="text" name="order_number"
                                                       class="text-left"
                                                       value="{{ request()->get('order_number') }}">
                                            </div>
                                        </dd>
                                    </dl>
                                </td>
                                <td class="w-45 mr-0">
                                    <dl class="formsetBox">
                                        <dt>製品品番</dt>
                                        <dd>
                                            <div class="formPack fixedWidth fpfw25p">
                                                <input type="text" name="product_code" id="product_code"
                                                    data-field-name="製品品番"
                                                    data-error-messsage-container="#product_code_error"
                                                    data-validate-exist-model="ProductNumber"
                                                    data-validate-exist-column="part_number"
                                                    data-inputautosearch-model="ProductNumber"
                                                    data-inputautosearch-column="part_number"
                                                    data-inputautosearch-return="product_name"
                                                    data-inputautosearch-reference="product_name"
                                                    class="text-left searchOnInput ProductNumber"
                                                    onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                                    value="{{ request()->get('product_code') }}">
                                            </div>
                                            <div class="formPack">
                                                <input type="text" readonly
                                                       name="product_name"
                                                       id="product_name" style="width: 100%;"
                                                       maxLength="20"
                                                       value="{{ request()->get('product_name') }}"
                                                       class="middle-name text-left">
                                            </div>
                                            <div class="formPack">
                                                <button type="button" class="btnSubmitCustom js-modal-open"
                                                        data-target="searchProductNumberModal">
                                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                         alt="magnifying_glass.svg">
                                                </button>
                                            </div>
                                            <div id="product_code_error"></div>
                                        </dd>
                                    </dl>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <br/>
                    <ul class="buttonlistWrap">
                        <li>
                            <button type="button" class="btn btn-primary btn-wide" data-clear-inputs data-clear-form-target="#outsourceArrivalList">検索条件をクリア</button>
                        </li>
                        <li>
                            <button type="submit" class="btn btn-primary btn-wide">検索</button>
                        </li>
                    </ul>

                    <a href="{{ route('outsource.arrivalExport', Request::all()) }}" type="button" class="float-right btn btn-green {{ count($arrivalResultLists) == 0 ? 'btn-disabled' : '' }}" style="margin-top: -40px;" id="exportBtn">検索結果をEXCEL出力</a>

                </div>
            </form>

            <div class="pagettlWrap">
                <h1><span>検索結果</span></h1>
            </div>

            <div class="tableWrap bordertable clear-both">
                @if(Request::all())
                    <ul class="headerList">
                        <li>{{ $arrivalResultLists->total() }}件中、{{ $arrivalResultLists->firstItem() }}件～{{ $arrivalResultLists->lastItem() }} 件を表示してます</li>
                    </ul>
                @else
                @endif
                <table class="tableBasic list-table bordered arrival-list">
                    <thead>
                        <tr>
                            <th class="text-center" width="8%">発注No.</th>
                            <th class="text-center" width="10%">製品品番</th>
                            <th class="text-center">品名</th>
                            <th class="text-center" width="25%">仕入先名</th>
                            <th class="text-center" width="10%">入荷日</th>
                            <th class="text-center" width="7%">便No.</th>
                            <th class="text-center" width="7%">指示数</th>
                            <th class="text-center" width="7%">入荷数</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($arrivalResultLists as $arrivalResultList)
                            <tr>
                                <td class="tA-ri text-left">{{ $arrivalResultList->order_no }}</td>
                                <td class="tA-ri text-left">{{ $arrivalResultList->product_code }}</td>
                                <td style="text-align: left">{{ $arrivalResultList->product?->product_name }}</td>
                                <td style="text-align: left">{{ $arrivalResultList->supplier?->supplier_name_abbreviation }}</td>
                                <td style="text-align: center">{{ $arrivalResultList->arrival_day?->format('Ymd') }}</td>
                                <td style="text-align: center">{{ $arrivalResultList->incoming_flight_number }}</td>
                                <td class="tA-ri text-center">{{ $arrivalResultList->instruction_number }}</td>
                                <td class="text-center">
                                    {{ $arrivalResultList->arrival_quantity }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">
                                    検索結果はありません
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if(Request::all())
                {{ $arrivalResultLists->appends(Request::all())->links() }}
            @endif
        </div>
    </div>
    @include('partials.modals.masters._search', [
        'modalId' => 'searchSupplierModal',
        'searchLabel' => '仕入先',
        'resultValueElementId' => 'supplier_code',
        'resultNameElementId' => 'supplier_name',
        'model' => 'Supplier'
    ])
    @include('partials.modals.masters._search', [
        'modalId' => 'searchProductNumberModal',
        'searchLabel' => '製品品番',
        'resultValueElementId' => 'product_code',
        'resultNameElementId' => 'product_name',
        'model' => 'ProductNumber'
    ])
@endsection
