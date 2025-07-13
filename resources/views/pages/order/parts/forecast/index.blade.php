@extends('layouts.app')

@push('styles')
    @vite('resources/css/index.css')
    @vite('resources/css/common.css')
    @vite('resources/css/modals/index.css')
    @vite('resources/css/search-modal.css')
    @vite('resources/css/order/parts/forecast/index.css')
@endpush

@section('title', '指示部品内示入力')	
@section('content')
    <div class="content">
        <div class="contentInner">

            <div class="pageHeaderBox rounded">
                指示部品内示情報入力
            </div>

            <div class="section mt-5">
                <form id="forecast-form" accept-charset="utf-8" class="overlayedSubmitForm with-js-validation" data-disregard-empty="false">
                    <h1 class="form-label bar indented" id="head-label">検索</h1>
                    <div class="box mb-3">
                        <div class="row">
                            <div class="col-md-6">
                                <div class=" mr-3">
                                    <label class="form-label dotted indented">年月</label> <span
                                        class="others-frame btn-orange badge">必須</span>
                                    <div class="d-flex">
                                        @include('partials._date_picker_year_month', [
                                            'inputName' => 'year_month', 
                                            'attributes' => 'data-error-messsage-container=#date_error_message data-field-name=年月', 
                                            'dateFormat' => 'YYYYMM', 
                                            'minlength'=>'6', 'maxlength'=>'6', 
                                            'inputClass' => 'text-left datepicker-disabled-dates w-100c', 
                                            'disableDates' => true,
                                            'value' => $notice['year_and_month'] ?? $data['year_month'], 
                                            'required' => true
                                        ])
                                    </div>
                                </div>
                                <div id="date_error_message"></div>
                            </div>
                            <div class="col-md-6 text-right">
                                <a href="#">ファイルでの取込はこちら</a>
                            </div>
                        </div>

                        <div class="mt-3" style="display:flex">
                            <div class="mr-3">
                                <label class="form-label dotted indented">納入先 </label> <span
                                    class="others-frame btn-orange badge">必須</span>

                                    <div class="d-flex">
                                    <input type="text" id="supplier_code" 
                                                data-field-name="納入先"
                                                data-error-messsage-container="#supplier_code-error"
                                                data-validate-exist-model="supplier"
                                                data-validate-exist-column="customer_code"
                                                data-inputautosearch-model="supplier"
                                                data-inputautosearch-column="customer_code"
                                                data-inputautosearch-return="supplier_name_abbreviation"
                                                data-inputautosearch-reference="supplier_name"
                                                name="supplier_code" style="width:100px; margin-right: 10px;" 
                                                required
                                                value="{{ $data['supplier_code']  ?? request()->get('supplier_code')}}"
                                                >
                                    <input type="text" id="supplier_name" name="supplier_name" readonly value="{{ $data['supplier_name'] ?? request()->get('supplier_name') }}" style="margin-right: 10px;">
                                    <button type="button" class="btnSubmitCustom js-modal-open"
                                            data-target="searchSupplierModal"
                                            data-query-field="">
                                        <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                            alt="magnifying_glass.svg">
                                    </button>
                                </div>
                                <div id="supplier_code-error"></div>
                                
                            </div>
                            <div style="flex:1">
                                <label class="form-label dotted indented">受入</label>
                                <div>
                                    <input type="text" value="{{ $data['acceptance'] }}" name="acceptance" style="width:50px">
                                </div>
                                <div class="error_msg"></div>
                            </div>
                        </div>

                        <div class="mr-4 mt-4">
                            <label class="form-label dotted indented">製品品番 </label> <span
                                class="others-frame btn-orange badge">必須</span>

                                <div class="d-flex">
                                    @php
                                        $product_code  =  $data['product_code'] ??  request()->get('product_code');
                                        $product_name  =  ($product_code) ? $data['product_name']  :  request()->get('product_name');
                                    @endphp
                                        <input type="text" name="product_code" id="product_code" 
                                            data-field-name="製品品番"
                                            data-error-messsage-container="#product_code_error"
                                            data-validate-exist-model="ProductNumber"
                                            data-validate-exist-column="part_number"
                                            data-inputautosearch-model="ProductNumber"
                                            data-inputautosearch-column="part_number"
                                            data-inputautosearch-return="product_name"
                                            data-inputautosearch-reference="product_name"
                                            value="{{ $product_code }}"
                                            required
                                            class="w-130c mr-2">

                                        <input type="text" readonly 
                                            value="{{ $product_name  }}"
                                            class="middle-name mr-2" name="product_name" id="product_name">

                                        <button type="button" class="btnSubmitCustom js-modal-open"
                                                data-target="searchProductModal">
                                            <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                    alt="magnifying_glass.svg">
                                        </button>
                                </div>
                                <div id="product_code_error"></div>
                        </div>
                        <div class="text-center mt-5">
                            <button type="reset" class="btn btn-primary btn-wide mr-2">検索条件をクリア</a>
                            <button type="submit" class="btn btn-primary btn-wide">
                                検索
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="section mt-4" id="search-label">
                <h1 class="form-label bar indented">検索結果</h1>
            </div>
            <div class="tableWrap bordertable" style="clear: both;">
                @php
                    $year_month = $notice['year_and_month'] ?? $data['year_month'];
                    $year = $year_month ? substr($year_month, 0, 4) : '----';
                    $month = $year_month ? substr($year_month, 4, 2) : '--';
                    
                @endphp
                <ul class="headerList mb-2 mt-2">
                    <li>
                        <span class="year">{{$year}}</span>
                        年
                        <span class="month">{{$month}}</span>
                        月の内示情報を表示してます
                        <input type="hidden" id="year-and-month" value="{{ $year_month}}">
                        <button class="btn btn-primary" id="previous-month" {{ $year_month ? '' : 'disabled' }}>＜前月</button>
                        <button class="btn btn-primary" id="next-month" {{ $year_month ? '' : 'disabled' }}>翌月＞</button>
                    </li>
                </ul>
                <table class="tableBasic" id="daily-inputs">
                    <tbody>
                        @for ($row = 0; $row < ceil(31 / 10); $row++)  {{-- Loop for rows (each row has 10 columns) --}}
                            @if($row !== 0)
                                <tr class="h30"></tr>
                            @endif
                            <tr class="bg-gray">
                                @for ($col = 1; $col <= 10; $col++)
                                    @php
                                        $day = $row * 10 + $col;
                                    @endphp
                                    @if ($day <= 31)
                                        <td class="text-center">{{ $day }}日</td> {{-- Date Label --}}
                                    @endif
                                @endfor
                            </tr>
                            <tr>
                                @for ($col = 1; $col <= 10; $col++)
                                    @php
                                        $day = $row * 10 + $col;
                                    @endphp
                                    @if ($day <= 31)
                                        <td>
                                            <input type="text" name="day_{{ $day }}" id="day_{{ $day }}"
                                                value="{{ $notice['day_'.$day] ?? ''}}"
                                                class="acceptNumericOnly"
                                            >
                                        </td>
                                    @endif
                                @endfor
                            </tr>
                        @endfor
                    </tbody>
                </table>
            </div>

            <div class="float-right mt-4">
                <button id="clear-daily-inputs" class="btn px-3 btn-orange">削　除</button>
                <button type="button" id="register-daily-form" class="btn btn-green"> この内容で登録する </button>
            </div>
        </div>
    </div>

    @include('partials.modals.masters._search', [
        'modalId' => 'searchProductModal',
        'searchLabel' => '製品品番',
        'resultValueElementId' => 'product_code',
        'resultNameElementId' => 'product_name',
        'model' => 'ProductNumber'
    ])

    @include('partials.modals.masters._search', [
        'modalId' => 'searchSupplierModal',
        'searchLabel' => '納入先',
        'resultValueElementId' => 'supplier_code',
        'resultNameElementId' => 'supplier_name',
        'model' => 'Supplier'
    ])
@endsection
@push('scripts')
    @vite('resources/js/order/parts/forecast/index.js')
@endpush