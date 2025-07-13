@extends('layouts.app')

@push('styles')
    @vite('resources/css/index.css')
    @vite('resources/css/common.css')
    @vite('resources/css/modals/index.css')
    @vite('resources/css/search-modal.css')
    @vite('resources/css/order/style.css')
@endpush

@section('title', '内示情報検索・一覧')
@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="pageHeaderBox rounded">
                内示情報検索・一覧
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
                <div class="box mb-3">
                    <form action="{{ route('order.forecast.index') }}" 
                        id="unofficalForm" class="overlayedSubmitForm with-js-validation" data-disregard-empty="false">
                        @csrf
                        <div class="mb-3">

                            <div class="d-flex  mb-3">
                                <div class='mr-3'>
                                    <label class="form-label dotted indented">年月</label> <span class="btn-orange badge">必須</span>
                                    <div class="d-flex mr-20c" >
                                        @php
                                         $date = (Request::get('year_and_month')) ? Request::get('year_and_month') : now()->format('Ym');
                                        @endphp
                                        @include('partials._date_picker_year_month', [
                                            'inputName' => 'year_and_month', 
                                            'attributes' => 'data-error-messsage-container=#date_error_message data-field-name=必須', 
                                            'dateFormat' => 'YYYYMM', 
                                            'minlength'=>'6', 'maxlength'=>'6', 
                                            'inputClass' => 'text-left datepicker-disabled-dates w-100c', 
                                            'disableDates' => true,
                                            'value' => $date, 
                                            'required' => true])

                                    </div>
                                    <div id="date_error_message" style="width: 100%;"></div>
                                </div>
                                
                                <div class="d-flex-1 mb-3">
                                    <label class="form-label dotted indented">指示区分</label>
                                    <div>
                                        <label class="radioBasic mr-2">
                                            <input type="radio" value=" "name="instruction_class" {{ (request()->input('instruction_class', '') == '') ? 'checked' : 'checked' }}> 
                                            <span>
                                                すべて
                                            </span>
                                        </label>
                                        <label class="radioBasic mr-2">
                                            <input type="radio" name="instruction_class" value="2" {{ (request()->input('instruction_class', '') == '2') ? 'checked' : '' }}> 
                                            <span>
                                                指示
                                            </span>
                                        </label>
                                        <label class="radioBasic">
                                            <input type="radio" name="instruction_class" value="1" {{ (request()->input('instruction_class', '') == '1') ? 'checked' : '' }}> 
                                            <span>
                                                かんばん
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                           
                            <div class="mr-3 mb-3">
                                <label class="form-label dotted indented">納入先</label>
                                <div class="d-flex">
                                    <input type="text" id="customer_code" 
                                                data-field-name="納入先"
                                                data-error-messsage-container="#supplier_code_error"
                                                data-validate-exist-model="customer"
                                                data-validate-exist-column="customer_code"
                                                data-inputautosearch-model="customer"
                                                data-inputautosearch-column="customer_code"
                                                data-inputautosearch-return="customer_name"
                                                data-inputautosearch-reference="customer_name"
                                                name="customer_code" style="width:100px; margin-right: 10px;" value="{{ request()->get('customer_code') }}">
                                    <input type="text" id="customer_name" name="customer_name" readonly value="{{ request()->get('customer_name') }}" style="margin-right: 10px;">
                                    <button type="button" class="btnSubmitCustom js-modal-open"
                                            data-target="searchCustomerModal"
                                            data-query-field="">
                                        <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                            alt="magnifying_glass.svg">
                                    </button>
                                </div>
                                <div id="supplier_code_error"></div>
                            </div>
                            <div>
                                <label class="form-label dotted indented">部門</label>
                                <div class="d-flex">
                                    <input type="text" name="department_code"
                                        id="department_code" style="ime-mode: disabled"
                                        data-field-name="部門"
                                        data-error-messsage-container="#department_code_error"
                                        data-validate-exist-model="Department"
                                        data-validate-exist-column="code"
                                        data-inputautosearch-model="Department"
                                        data-inputautosearch-column="code"
                                        data-inputautosearch-return="name"
                                        data-inputautosearch-reference="department_name"
                                        class="text-left acceptNumericOnly w-100c mr-10c"
                                        minlength="6"
                                        maxlength="6"
                                        onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                        value="{{ request()->get('department_code') }}" 
                                        >
                                    <input type="text" readonly
                                        name="department_name"
                                        id="department_name" style="margin-right: 10px; width: 290px;"
                                        value="{{ request()->get('department_name')}}"
                                        class="middle-name text-left">
                                    <button type="button" class="btnSubmitCustom js-modal-open"
                                            data-target="searchDepartmentModal">
                                        <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                            alt="magnifying_glass.svg">
                                    </button>
                                </div>
                                <div id="department_code_error"></div>
                            </div>
                        </div>
                        <div class="mb-3 d-flex">
                            <div class="mr-3">
                                <label class="form-label dotted indented">ライン</label>
                                <div class="d-flex">
                                    <input type="text" name="line_code"
                                            data-field-name="ライン"
                                            data-error-messsage-container="#line_code_error"
                                            data-validate-exist-model="Line"
                                            data-validate-exist-column="line_code"
                                            data-inputautosearch-model="line"
                                            data-inputautosearch-column="line_code"
                                            data-inputautosearch-return="line_name"
                                            data-inputautosearch-reference="line_name"
                                            id="line_code"
                                            class="text-left w-75c mr-10c"
                                            minlength="3"
                                            maxlength="3"
                                            onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                            value="{{ request()->get('line_code') }}" >
                                    <input type="text" readonly
                                            name="line_name"
                                            id="line_name"
                                            value="{{ request()->get('line_name') }}"
                                            class="middle-name text-left w-290c mr-10c">
                                    <button type="button" class="btnSubmitCustom js-modal-open"
                                            data-target="searchLineModal">
                                        <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                alt="magnifying_glass.svg">
                                    </button>
                                </div>
                                <div id="line_code_error"></div>
                            </div>
                          
                        </div>

                        <div class="mb-3">
                            <div class="d-flex">
                                <div>
                                    <label class="form-label dotted indented">製品品番</label>
                                    <div class="d-flex">
                                        @php
                                            $product_code1  =  request()->get('product_number') ?? '';
                                            $product_name1  =  ($product_code1) ? request()->get('product_name')  : '';
                                        @endphp
                                            <input type="text" name="product_number" id="product_code" 
                                                data-field-name="製品品番"
                                                data-error-messsage-container="#product_code_error"
                                                data-validate-exist-model="ProductNumber"
                                                data-validate-exist-column="part_number"
                                                data-inputautosearch-model="ProductNumber"
                                                data-inputautosearch-column="part_number"
                                                data-inputautosearch-return="product_name"
                                                data-inputautosearch-reference="product_name"
                                                value="{{ $product_code1 }}"
                                                class="w-130c mr-2">

                                            <input type="text" readonly 
                                                value="{{ $product_name1  }}"
                                                class="middle-name mr-2" name="product_name" id="product_name">

                                            <button type="button" class="btnSubmitCustom js-modal-open"
                                                    data-target="searchProductModal1">
                                                <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                        alt="magnifying_glass.svg">
                                            </button>

                                            {{--

                                            <span style="font-size:24px; padding:0px 10px;">
                                                ~
                                            </span>

                                            @php
                                                $product_code2  =  request()->get('product_code_to') ?? '';
                                                $product_name2  =  ($product_code2) ? request()->get('product_name_to')  : '';
                                            @endphp
                                            <input type="text" name="product_code_to" id="product_code_to" 
                                                data-error-messsage-container="#product_code-error"
                                                data-validate-exist-model="ProductNumber"
                                                data-validate-exist-column="part_number"
                                                data-inputautosearch-model="ProductNumber"
                                                data-inputautosearch-column="part_number"
                                                data-inputautosearch-return="product_name"
                                                data-inputautosearch-reference="product_name"
                                                value="{{ $product_code2 }}"
                                                class="w-130c mr-2">

                                            <input type="text" readonly 
                                                value="{{ $product_name2  }}"
                                                class="middle-name mr-2" name="product_name_to" id="product_name_to">

                                            <button type="button" class="btnSubmitCustom js-modal-open"
                                                    data-target="searchProductModal2">
                                                <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                        alt="magnifying_glass.svg">
                                            </button>
                                            --}}
                                    </div>
                                    <div id="product_code_error"></div>
                                </div>
                                
                               
                            </div>
                            <div class="d-flex-1 mt-2">
                                <label class="form-label dotted indented">受入</label>
                                <div>
                                    <input type="text" name="acceptance" value="{{ old('acceptance', Request::get('acceptance') ?? '') }}" class="w-150c">
                                </div>
                            </div>

                        </div>
                        <a href="{{ route('order.forecastExcelExport', Request::all()) }}" class="float-right btn btn-green {{ $unofficialRecords->total() == 0 ? 'btn-disabled' : '' }}">検索結果をEXCEL出力</a>
                        <div class="text-center">
                            <button type="button"
                             class="btn btn-primary btn-wide"
                                data-clear-inputs
                                data-clear-form-target="#unofficalForm">検索条件をクリア</button>
                            <button type="submit" class="btn btn-primary btn-wide">検索</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="section">
                <h1 class="form-label bar indented">検索結果</h1>
                <div class="box">
                    @if(count($unofficialRecords) > 0)
                        {{ $unofficialRecords->total() }}件中、{{ $unofficialRecords->firstItem() }}件～{{ $unofficialRecords->lastItem() }} 件を表示しています
                    @endif
                
                    <table class=" table table-bordered table-striped align-middle ">
                        <thead>
                            <tr>
                                <th rowspan="2" class="valign-center" >納入先CD</th>
                                <th rowspan="2" class="valign-center" >納入先名</th>
                                <th rowspan="2" class="valign-center" >部門CD</th>
                                <th rowspan="2" class="valign-center" >部門名</th>
                                <th rowspan="2" class="valign-center" >ラインCD</th>
                                <th rowspan="2" class="valign-center" >ライン名</th>
                                <th rowspan="2" class="valign-center" >製品品番</th>
                                <th rowspan="2" class="valign-center" >品名</th>
                                <th rowspan="2" class="valign-center" >指示区分</th>
                                <th rowspan="2" class="valign-center" >受入</th>
                                <th colspan="3">数量</th>
                            </tr>
                            <tr>
                                <th>当月</th>
                                <th>翌月</th>
                                <th>翌々月</th>
                            </tr>
                        </thead> 
                        <tbody>
                            @forelse($unofficialRecords as $result)
                                <tr>
                                    <td>{{ $result?->product?->customer?->customer_code ?? ''}}</td>
                                    <td>{{ $result?->product?->customer?->customer_name ?? '' }}</td>
                                    <td>{{ $result?->product?->department_code ?? ''}}</td>
                                    <td>{{ $result?->product?->department->name ?? ''}}</td>
                                    <td>{{ $result?->product?->line_code ?? ''}}</td>
                                    <td>{{ $result?->product?->line->line_name ?? ''}}</td>
                                    <td>{{ $result?->product_number }}</td>
                                    <td>{{ $result?->product?->product_name ?? ''}}</td>
                                    <td>
                                        @if($result->instruction_class == 1)
                                            かんばん
                                        @elseif($result->instruction_class == 2)
                                            指示 
                                        @endif
                                    </td>
                                    <td>{{ $result->acceptance }}</td>
                                    <td>
                                        @if ($result->current_month)
                                        <a href="{{ route('order.forecast.show', array_merge([$result->id], request()->all())) }}">
                                            {{ number_format($result->current_month )}}
                                        </a>
                                        @else
                                            0
                                        @endif
                                    </td>
                                    <td>
                                    @if ($result->next_month)
                                        <a href="{{ route('order.forecast.show', array_merge([$result->id], request()->all())) }}">
                                        {{ number_format($result->next_month )}}
                                        </a>
                                        @else
                                            0
                                        @endif
                                    </td>
                                    <td>
                                    @if ($result->two_months_later)
                                        <a href="{{ route('order.forecast.show', array_merge([$result->id], request()->all())) }}">
                                        {{ number_format($result->two_months_later )}}
                                        </a>
                                        @else
                                            0
                                        @endif
                                    </td>
                                    
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="13" class="text-center">検索結果はありません</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{ $unofficialRecords->appends(request()->all())->links() }}
                </div>
            </div>
           
        </div>
    </div>
@include('partials.modals.masters._search', [
    'modalId' => 'searchCustomerModal',
    'searchLabel' => '納入先',
    'resultValueElementId' => 'customer_code',
    'resultNameElementId' => 'customer_name',
    'model' => 'Customer'
])
@include('partials.modals.masters._search', [
    'modalId' => 'searchDepartmentModal',
    'searchLabel' => '部門',
    'resultValueElementId' => 'department_code',
    'resultNameElementId' => 'department_name',
    'model' => 'Department'
])
@include('partials.modals.masters._search', [
    'modalId' => 'searchLineModal',
    'searchLabel' => 'ライン',
    'resultValueElementId' => 'line_code',
    'resultNameElementId' => 'line_name',
    'model' => 'Line'
])
@include('partials.modals.masters._search', [
    'modalId' => 'searchProductModal1',
    'searchLabel' => '製品品番',
    'resultValueElementId' => 'product_code',
    'resultNameElementId' => 'product_name',
    'model' => 'ProductNumber'
])
@include('partials.modals.masters._search', [
    'modalId' => 'searchProductModal2',
    'searchLabel' => '製品品番',
    'resultValueElementId' => 'product_code_to',
    'resultNameElementId' => 'product_name_to',
    'model' => 'ProductNumber'
])
@endsection
@push('scripts')
    <script>
        $('#form').validate({
        rules: {
            order_yearmonth: {
                required: true
            },
        },
        messages: {
            order_yearmonth: {
                required: '入力してください'
            },
        },
        errorElement : 'div',
        errorPlacement: function(error, element) {
            $(element).siblings('div').html(error);
        },
        invalidHandler: function(event, validator) {
            setInterval(() => {
                $('.submit-overlay').css('display', "none");
            }, 0);
        }
    })
    </script>
@endpush
