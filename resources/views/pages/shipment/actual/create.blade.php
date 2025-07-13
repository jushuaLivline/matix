@extends('layouts.app')

@push('styles')
    @vite('resources/css/index.css')
    @vite('resources/css/modals/index.css')
    @vite('resources/css/search-modal.css')
@endpush

@section('title', '出荷実績入力')

@section('content')
    <form action="{{ route('shipment.actual.add') }}" id="update-shipment" method="post" class="overlayedSubmitForm">
        <input type="hidden" name="productNumber">
        <input type="hidden" name="quantity">
        <input type="hidden" name="remarks">
    </form>
    <div class="content">
        <div class="contentInner">
            <div class="pageHeaderBox rounded">
                出荷実績入力
            </div>

            @if(Session::has("success"))
                <div id="successInputs" style="background-color: #fff; margin-top:20px; padding: 20px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
                    <div style="text-align: left;">
                        <p style="font-size: 18px; color: #0d9c38;">出荷実績の登録が完了しました</p>
                    </div>
                </div>
            @endif

            <div class="section">
                <h1 class="form-label bar indented">出荷実績入力</h1>
                <div class="box mb-3">
                    <form id="form" action="{{ route("shipment.actual.store") }}" method="POST" 
                        class="overlayedSubmitForms with-js-validation"
                        data-confirmation-message="出荷実績情報を追加します、よろしいでしょうか？">
                        @csrf
                        <div class="mb-3 d-flex">
                            <div class="mr-4 header-inputs">
                                <label class="form-label dotted indented">納入先 </label> 
                                <span class="others-frame btn-orange badge">必須</span>
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
                                                    value="{{ $customer_code }}" required>
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

                            <div class="mr-4 header-inputs">
                                <label class="form-label dotted indented">伝票No.</label>
                                <span class="btn-orange badge">必須</span>
                                <div class="d-flex">
                                    <input type="text" name="slip_no" value="{{ Request::get('slip_no') }}"
                                        data-field-name="伝票No"
                                        data-error-messsage-container="#slip_no_error"
                                        maxlength="20"
                                        class="full-width" id="_slip-no" required>
                                </div>
                                <div id="slip_no_error"></div>
                            </div>

                            <div class="mr-1">
                                <label class="form-label dotted indented">納入日</label>
                                <span class="others-frame btn-orange badge">必須</span>
                                <div class="d-flex">
                                    @include('partials._date_picker', [
                                        'inputName' => 'instruction_date',
                                        'attributes' => 'data-error-messsage-container=#date_error_message data-field-name=納入日', 
                                        'value' => Request::get('instruction_date'),
                                        'requeue' => true,
                                        'required' => true
                                    ])
                                </div>
                                <div id="date_error_message"></div>
                            </div>

                            <div>
                                <label class="form-label dotted indented">便No.</label>
                                <span class="btn-orange badge">必須</span>
                                <div class="d-flex">
                                    <input type="text" name="delivery_no" value="{{ Request::get('delivery_no') }}" 
                                        data-field-name="伝票No"
                                        data-error-messsage-container="#delivery_no_error"
                                        style="width: 50px;" id="_delivery-no" required>
                                </div>
                                <div id="delivery_no_error"></div>
                            </div>
                        </div>

                        <div class="mb-4 d-flex">
                            <div class="mr-4">
                                <label class="form-label dotted indented">工場</label>
                                <div class="d-flex">
                                    <input type="text" name="plant" value="{{ Request::get('plant') }}" class="full-width" style="width: 50px;">
                                </div>
                            </div>

                            <div class=" mr-4">
                                <label class="form-label dotted indented">受入</label>
                                <div class="d-flex">
                                    <input type="text" name="acceptance" value="{{ Request::get('acceptance') }}" class="full-width" style="width: 50px;">
                                </div>
                            </div>

                            <div class="">
                                <label class="form-label dotted indented">直送先</label>
                                <div class="d-flex">
                                    <input type="text" id="supplier_code" 
                                                data-field-name="直送先"
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
                        </div>

                        <table class="table table-bordered table-striped text-center">
                            <thead>
                                <tr>
                                    <th width="20%">製品品番</th>
                                    <th width="25%">品名</th>
                                    <th width="10%">納入計</th>
                                    <th >備考</th>
                                    <th width="15%">操作</th>
                                </tr>
                            </thead>
                            <tbody id="table-body"></tbody>
                        </table>
                    </form>
                </div>
            </div>
            
            <div class="space-between">
                <div>
                    <p class="text-red" id="warningInputs" style="display:none;">登録に必要ないくつかの情報が入力されていません！</p>
                    <p class="text-red" id="productNumberWarning" style="display:none;">登録されていない品番です</p>
                </div>
                <div class="sc">
                    <a href="#" class="btn btn-primary btn-wide">メニューに戻る</a>
                    <button type="submit" class="btn btn-success btn-wide btn-sumbmit">この内容で登録する</button>
                </div>
            </div>
        </div>
    </div>

    <div id="modalContainer">
    </div>
    @include('partials.modals.masters._search', [
        'modalId' => 'searchCustomerModal',
        'searchLabel' => '納入先',
        'resultValueElementId' => 'customer_code',
        'resultNameElementId' => 'customer_name',
        'model' => 'Customer'
    ])

    @include('partials.modals.masters._search', [
        'modalId' => 'searchSupplierModal',
        'searchLabel' => '直送先',
        'resultValueElementId' => 'supplier_code',
        'resultNameElementId' => 'supplier_name',
        'model' => 'Supplier'
    ])

    @include('partials.modals.masters._search', [
        'modalId' => 'searchPartNumberModal',
        'searchLabel' => '製品品番',
        'resultValueElementId' => '_product-number',
        'resultNameElementId' => '_product-name',
        'model' => 'ProductNumber'
    ])
@endsection

@php
    $dataConfigs['Customer'] = [
        'model' => 'Customer',
        'reference' => 'customer_name'
    ];
    $dataConfigs['Supplier'] = [
        'model' => 'Supplier',
        'reference' => 'supplier_name'
    ];
    $dataConfigs['ProductNumber'] = [
        'model' => 'ProductNumber',
        'reference' => '_product-name'
    ];
@endphp
<x-search-on-input :dataConfigs="$dataConfigs" />
@push("scripts")
@vite(['resources/js/shipment/actual/create.js'])
@endpush