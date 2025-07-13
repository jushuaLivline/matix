@extends('layouts.app')

@push('styles')
    @vite('resources/css/index.css')
    @vite('resources/css/modals/index.css')
    @vite('resources/css/search-modal.css')
@endpush

@section('title', '内示集計')
@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="pageHeaderBox rounded">
                内示集計
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
            <form action="{{ route('order.forecastSummary.index') }}" id="forecastSummaryForm" 
                class="overlayedSubmitForm with-js-validation"   data-disregard-empty="false"
                method="GET">
                <div class="section">
                    <h1 class="form-label bar indented">検索</h1>
                    <div class="box mb-3">
                        <div class="mb-3">
                            <label class="form-label dotted indented">集計単位</label> <span class="btn-orange badge">必須</span>
                            <div>
                                <label class='radioBasic mr-2'>
                                    <input type="radio" name="category" value="department" @if(Request::get('category') == 'department') checked @else checked @endif> 
                                    <span>
                                        課
                                    </span>
                                </label>
                                <label class='radioBasic mr-2'>
                                    <input type="radio" name="category" value="group" @if(Request::get('category') == 'group') checked @endif> 
                                    <span>
                                        組
                                    </span>
                                </label>
                                <label class='radioBasic mr-2'>
                                    <input type="radio" name="category" value="line" @if(Request::get('category') == 'line') checked @endif> 
                                    <span>
                                        ライン
                                    </span>
                                </label>
                                <label class='radioBasic mr-2'>
                                    <input type="radio" name="category" value="product_code" @if(Request::get('category') == 'product_code') checked @endif> 
                                    <span>
                                        品番
                                    </span>
                                </label>
                            </div>
                        </div>

                        <div class="mr-3 mb-3">
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

                        <div class="mr-3 mb-3">
                            <label class="form-label dotted indented">納入先</label>
                            <div class="d-flex">
                                @php
                                    $customer_code  =  request()->get('customer_code') ?? '';
                                    $customer_name  =  ($customer_code) ? request()->get('customer_name')  : '';
                                @endphp
                                <input type="text" id="customer_code" 
                                            data-field-name="納入先"
                                            data-error-messsage-container="#supplier_code_error"
                                            data-validate-exist-model="customer"
                                            data-validate-exist-column="customer_code"
                                            data-inputautosearch-model="customer"
                                            data-inputautosearch-column="customer_code"
                                            data-inputautosearch-return="customer_name"
                                            data-inputautosearch-reference="customer_name"
                                            name="customer_code" style="width:100px; margin-right: 10px;" 
                                            value="{{ $customer_code }}">
                                <input type="text" id="customer_name" name="customer_name" readonly 
                                        value="{{ $customer_name  }}" style="margin-right: 10px;">
                                <button type="button" class="btnSubmitCustom js-modal-open"
                                        data-target="searchCustomerModal"
                                        data-query-field="">
                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                        alt="magnifying_glass.svg">
                                </button>
                            </div>
                            <div id="supplier_code_error"></div>
                        </div>


                        <div class="mb-3">
                            <label class="form-label dotted indented">部門</label>
                            <div class="d-flex">
                                @php
                                    $department_code  =  request()->get('department_code') ?? '';
                                    $department_name  =  ($department_code) ? request()->get('department_name')  : '';
                                @endphp
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
                                    value="{{ $department_code }}" 
                                    >
                                <input type="text" readonly
                                    name="department_name"
                                    id="department_name" style="margin-right: 10px; width: 290px;"
                                    value="{{ $department_name }}"
                                    class="middle-name text-left">
                                <button type="button" class="btnSubmitCustom js-modal-open"
                                        data-target="searchDepartmentModal"
                                       data-query-field="">
                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                        alt="magnifying_glass.svg">
                                </button>
                            </div>
                            <div id="department_code_error"></div>
                        </div>
                    
                        <div class="mb-3 d-flex">
                            <div class="mr-3">
                                <label class="form-label dotted indented">ライン</label>
                                <div class="d-flex">
                                    @php
                                        $line_code  =  request()->get('line_code') ?? '';
                                        $line_name  =  ($line_code) ? request()->get('line_name')  : '';
                                    @endphp
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
                                            value="{{ $line_code  }}" >
                                    <input type="text" readonly
                                            name="line_name"
                                            id="line_name"
                                            value="{{ $line_name  }}"
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
                            <label class="form-label dotted indented">製品品番</label>
                            <div class="d-flex">
                                @php
                                    $product_code  =  request()->get('product_code') ?? '';
                                    $product_name  =  ($product_code) ? request()->get('product_name')  : '';
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

                    <div class="text-center sc relative">
                            <a href="{{ route('order.forecastSummaryExcelExport', Request::all()) }}" class="btn btn-green absolute-right {{ $unofficialRecords->total() == 0 ? 'btn-disabled' : '' }}">検索結果をEXCEL出力</a>
                            <button type="button"
                                class="btn btn-primary btn-wide"
                                data-clear-inputs
                                data-clear-form-target="#forecastSummaryForm">検索条件をクリア</button>
                            <button class="btn btn-primary btn-wide" type="submit">検索</button>
                        </div>

                    </div>
                </div>
            </form>

            <div class="section">
                <h1 class="form-label bar indented">集計結果</h1>
                <div class="box">
                    <div class="mb-3"> 
                        @if(isset($unofficialRecords) && $unofficialRecords->count() > 0)
                            {{ $unofficialRecords->total() }}件中、{{ $unofficialRecords->firstItem() }}件～{{ $unofficialRecords->lastItem() }} 件を表示しています
                        @endif
                        <table class="table table-bordered table-striped align-middle text-center">
                            <thead>
                                <tr>
                                    <th rowspan="2" class="valign-center" >部門CD</th>
                                    <th rowspan="2" class="valign-center" >部門名</th>

                                    @if(request()->get('category') == 'department')
                                    <th rowspan="2" class="valign-center" >課名</th>
                                    @endif
                                    @if(request()->get('category') == 'group')
                                    <th rowspan="2" class="valign-center" >組名</th>
                                    @endif

                                    @if(request()->get('category') == 'line' || request()->get('category') == 'product_code')
                                    <th rowspan="2" class="valign-center" >ラインCD</th>
                                    <th rowspan="2" class="valign-center" >ライン名</th>
                                    @endif
                                    @if(request()->get('category') == 'product_code')
                                    <th rowspan="2" class="valign-center" >製品品番</th>
                                    <th rowspan="2" class="valign-center" >品名</th>
                                    @endif
                                    <th colspan="3">数量</th>
                                </tr>
                                <tr>
                                    <th>当月</th>
                                    <th>翌月</th>
                                    <th>翌々月</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $total_current_month = 0;
                                    $total_next_month = 0;
                                    $total_two_months_later = 0;
                                @endphp
                                @forelse ($unofficialRecords as $result)
                                    @php
                                        $total_current_month += $result->current_month;
                                        $total_next_month += $result->next_month;
                                        $total_two_months_later += $result->two_months_later;
                                    @endphp
                                    <tr>
                                        <td class="text-left">{{ $result?->department_code ?? '' }}</td>
                                        <td class="text-left">{{ $result?->department_name ?? '' }}</td>
                                        @if(request()->get('category') == 'department')
                                        <td class="valign-center" >{{  $result?->section_name ?? '' }}</td>
                                        @endif
                                        @if(request()->get('category') == 'group')
                                        <td class="valign-center" >{{  $result?->group_name ?? '' }}</td>
                                        @endif

                                        @if(request()->get('category') == 'line' || request()->get('category') == 'product_code')
                                        <td class="text-left">{{  $result?->line_code ?? '' }}</td>
                                        <td class="text-left">{{ $result?->line_name ?? '' }}</td>
                                        @endif

                                        @if( request()->get('category') == 'product_code')
                                        <td class="text-left">{{ $result?->product_number ?? '' }}</td>
                                        <td class="text-left">{{ $result?->product_name ?? ''}}</td>
                                        @endif

                                        <td class="text-right">{{ number_format($result->current_month) }}</td>
                                        <td class="text-right">{{ number_format($result->next_month) }}</td>
                                        <td class="text-right">{{ number_format($result->two_months_later) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center">検索結果はありません</td>
                                    </tr>
                                @endforelse   
                                    
                            </tbody>
                            <tfoot class="bg-light-blue">
                                <tr>
                                    <td class="tr-no-border bg-white"></td>
                                    @if(request()->get('category') == 'department')
                                    <td class="tr-no-border bg-white"></td>
                                    @endif
                                    @if(request()->get('category') == 'group')
                                    <td class="tr-no-border bg-white"></td>
                                    @endif
                                    @if(request()->get('category') == 'line' || request()->get('category') == 'product_code')
                                    <td class="tr-no-border bg-white"></td>
                                    <td class="tr-no-border bg-white"></td>
                                    @endif

                                    @if(request()->get('category') == 'product_code')
                                    <td class="tr-no-border bg-white"></td>
                                    <td class="tr-no-border bg-white"></td>
                                    @endif
                                    
                                    <td class="text-center">合計</td>
                                    <td class="text-right">{{ number_format($total_current_month) }}</td>
                                    <td class="text-right">{{ number_format($total_next_month) }}</td>
                                    <td class="text-right">{{ number_format($total_two_months_later) }}</td>
                                </tr>
                            </tfoot>
                        </table>

                        {{ $unofficialRecords->appends(request()->all())->links() }}
                    </div>
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
        'modalId' => 'searchProductModal',
        'searchLabel' => '製品品番',
        'resultValueElementId' => 'product_code',
        'resultNameElementId' => 'product_name',
        'model' => 'ProductNumber'
    ])
@endsection

