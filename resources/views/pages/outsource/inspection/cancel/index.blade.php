@extends('layouts.app')

@push('styles')
    @vite('resources/css/index.css')
    @vite('resources/css/modals/index.css')
    @vite('resources/css/search-modal.css')
@endpush

@section('title', '検収取消')
@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="pageHeaderBox rounded">
                検収取消
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
                <form       
                    action="{{ route('outsource.inspectionCancel.index') }}"
                    data-disregard-empty="true" 
                    class="overlayedSubmitForm with-js-validation"
                    id="inspectionForm">
                    <div class="box mb-3">
                        
                    <div class="mb-2 d-flex">
                            <div class="mr-3">
                                <label class="form-label dotted indented">入荷日</label>
                                <div class="d-flex mr-3">
                                    @php
                                        $start_date  =  request()->get('arrival_day_from') ?? '';
                                        $end_date  =  request()->get('arrival_day_to')  ?? '';
                                    @endphp

                                    @include('partials._date_picker', [
                                                'inputName' => 'arrival_day_from', 
                                                'attributes' => 'data-error-messsage-container=#date_error_message data-field-name="入荷日"', 
                                                'dateFormat' => 'YYYYMMDD', 
                                                'minlength'=>'8', 'maxlength'=>'8', 
                                                'inputClass' => 'text-left w-100c', 
                                                'value' => $start_date])

                                    <span style="font-size:18px; padding:5px 10px;">
                                        ~
                                    </span>
                                    @include('partials._date_picker', [
                                                'inputName' => 'arrival_day_to', 
                                                'attributes' => 'data-error-messsage-container=#date_error_message data-field-name="入荷日"', 
                                                'dateFormat' => 'YYYYMMDD', 
                                                'minlength'=>'8', 'maxlength'=>'8', 
                                                'inputClass' => 'text-left  w-100c', 
                                                'value' => $end_date])
                                </div>
                                <div id="date_error_message"></div>
                            </div>
    
                            <div class="mr-4">
                                <label class="form-label dotted indented">便No</label>
                                <div class="d-flex">
                                    <input type="text" id="" style="width: 40px" name="incoming_flight_number_start" maxlength="2" onkeypress="return event.charCode >= 48 && event.charCode <= 57" value="{{ request()->get('incoming_flight_number_start') ?? '' }}" data-gtm-form-interact-field-id="0">
                                    <span style="font-size:24px; padding:5px 10px;">
                                        ~
                                    </span>
                                    <input type="text" id="" style="width: 40px" name="incoming_flight_number_end" maxlength="2" onkeypress="return event.charCode >= 48 && event.charCode <= 57" value="{{ request()->get('incoming_flight_number_end') ?? '' }}" data-gtm-form-interact-field-id="0">
                                </div>
                            </div>
    
                            <div class="mr-3">
                                <label class="form-label dotted indented">仕入先</label>
                                <div class="d-flex">
                                        @php
                                            $supplier_code  =  request()->get('supplier_code') ?? '';
                                            $supplier_name  =  ($supplier_code) ? request()->get('supplier_name')  : '';
                                        @endphp
                                        <input type="text" id="supplier_code" 
                                                data-field-name="仕入先"
                                                data-error-messsage-container="#supplier_code_error"
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
                                <div id="supplier_code_error"></div>
                            </div>
                        </div>
                        <br/>
                        <div class="mb-2 d-flex">
                            <div class="mr-3">
                                <label class="form-label dotted indented">発注No.</label>
                                <div class="d-flex">
                                    <input type="text"
                                           id=""
                                           class="tA-le acceptNumericOnly"
                                           style="width: 100%"
                                           name="order_no" value="{{ request()->get('order_no') ?? ''}}">
                                </div>
                            </div>
    
                            <div class="mr-3">
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
                                        onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                        value="<?php echo e(Request::get('product_code')); ?>"
                                        class="w-130c mr-2">

                                    <input type="text" readonly 
                                        value="<?php echo e(Request::get('product_name')); ?>"
                                        maxLength="20"
                                        class="middle-name mr-2" name="product_name" id="product_name">

                                    <button type="button" class="btnSubmitCustom js-modal-open"
                                            data-target="searchProductNumberModal">
                                        <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                            alt="magnifying_glass.svg">
                                    </button>
                                </div>
                                <div id="product_code_error"></div>
                            </div>
                        </div>
                        <br/>
                        <br/>
                        <div class="text-center">
                            <button type="button" 
                                class="btn btn-primary btn-wide" 
                                data-clear-inputs="" 
                                data-clear-form-target="#inspectionForm">検索条件をクリア</button>
                            <button type="submit" class="btn btn-primary btn-wide">検索</button>
                        </div>
                        
                    </div>
                </form>
            </div>

            <div class="section">
                <h1 class="form-label bar indented">検索結果</h1>
                <form 
                    action="{{ route('outsource.inspectionCancel.destroy',1) }}" 
                    class="overlayedSubmitForm with-js-validation"
                    method="POST"
                    accept-charset="utf-8"
                    data-confirmation-message="外注加工検収を取り消します、よろしいでしょうか？">
                    @csrf
                    @method('DELETE') 
                    <div class="box">
                        <div class="mb-2">
                            @if (count($arrivalResultLists) > 0)
                                {{ $arrivalResultLists->total() }}件中、{{ $arrivalResultLists->currentPage() }}件～{{  $arrivalResultLists->lastItem() }} 件を表示してます
                            @endif
                            <table class="table table-bordered text-center table-striped-custom align-middle" style="width: 87%">
                                <thead>
                                    <tr>
                                        <th width="3%">
                                            <input type="checkbox" id="selectAll" style="width: 20px !important;" onclick="toggleCheckboxes()"/>
                                        </th>
                                        <th>発注No</th>
                                        <th>製品品番</th>
                                        <th width="15%">品名</th>
                                        <th width="15%">仕入先名</th>
                                        <th>入荷日</th>
                                        <th>便No.</th>
                                        <th>指示数</th>
                                        <th>入荷数</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($arrivalResultLists as $arrivalResultList)
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="rowCheckbox" id="{{ $arrivalResultList->id }}" name="outsourced_data_ids[]" value="{{ $arrivalResultList->id }}"
                                            style="width: 20px !important;" 
                                            onclick="toggleCheckboxes()">
                                        </td>
                                        <td class="tA-le">{{ $arrivalResultList->order_no }}</td>
                                        <td class="tA-le">{{ $arrivalResultList->product_code }}</td>
                                        <td style="text-align: left">{{ $arrivalResultList->product?->product_name }}</td>
                                        <td style="text-align: left">{{ $arrivalResultList?->supplier?->supplier_name_abbreviation ?? '' }}</td>
                                        <td style="text-align: center">{{ $arrivalResultList->arrival_day?->format('Ymd') }}</td>
                                        <td style="text-align: center">{{ $arrivalResultList->incoming_flight_number }}</td>
                                        <td class="tA-cn">{{ $arrivalResultList->arrival_number }}</td>
                                        <td style="text-align: center">{{ $arrivalResultList->arrival_quantity }}</td>
                                    </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center">検索結果はありません</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if(Request::all())
                            {{ $arrivalResultLists->appends(request()->all())->links() }}
                        @endif
                    </div>
                    <div class="space-between mt-4">
                        <div></div>
                        <div>
                            <button type="submit" class="btn btn-blue btn-disabled" style="width: 15rem" id="submitButton" disabled>選択した検収を取消</button>
                        </div>                        
                    </div>
                </form>
            </div>
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

@push('scripts')
    @vite(['resources/js/outsource/inspection/cancel/index.js'])
@endpush
