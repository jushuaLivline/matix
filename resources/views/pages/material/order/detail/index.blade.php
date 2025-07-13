@extends('layouts.app')

@push('styles')
    @vite('resources/css/index.css')
    @vite('resources/css/modals/index.css')
    @vite('resources/css/search-modal.css') 
@endpush

@section('title', '発注明細書発行')
@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="pageHeaderBox rounded">
                発注明細書発行
            </div>
            @if(session('success'))
                <div class="tableWrap borderLesstable">
                    <div class="success">
                        {{ session('success') }}
                    </div>
                </div>
            @endif
            @if(session('error'))
                <div class="tableWrap borderLesstable message">
                    <div class="error">
                        {{ session('error') }}
                    </div>
                </div>
            @endif
            <form action="{{ route('material.order.detail.pdf_export') }}" method="get" class="with-js-validation"
            data-confirmation-message="発注明細書を出力します、よろしいでしょうか？"
            data-disabled-overlay="true">
                <div class="section">
                    <h1 class="form-label bar indented">発注明細書発行</h1>
                    <div class="box mb-3">
                        <div class="mb-2 d-flex" style="flex-wrap:wrap; gap: 20px;">
                            <div class="mr-6">
                                <label class="form-label dotted indented">発行区分</label> <span class="btn-orange badge">必須</span>
                                <div>
                                    <label class="radioBasic mr-2">
                                        <input type="radio" name="issue_classification" value="no-issue" checked/>
                                        <span>未発行分</span>
                                    </label>
                                    <label class="radioBasic">
                                        <input type="radio" name="issue_classification" value="reissue"/> 
                                        <span>再発行</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label dotted indented">材料メーカー </label> <span style="display: none !important;" class="others-frame btn-orange badge">必須</span>
                            <div class="d-flex">
                                <input type="text" id="manufacturer_code" name="manufacturer_code"
                                    data-field-name="材料メーカー"
                                    data-error-messsage-container="#manufacturer_code_error"
                                    data-validate-exist-model="Customer"
                                    data-validate-exist-column="customer_code"
                                    data-inputautosearch-model="Customer"
                                    data-inputautosearch-column="customer_code"
                                    data-inputautosearch-return="customer_name"
                                    data-inputautosearch-reference="manufacturer_name"
                                    class="w-100c"
                                    value="{{ Request::get('manufacturer_code') }}">
                                <input type="text" readonly
                                    id="manufacturer_name"
                                    name="manufacturer_name"
                                    value="{{ Request::get('manufacturer_name') }}"
                                    class="middle-name ml-half mr-half w-250c"
                                    disabled>
                                <button type="button" class="btnSubmitCustom js-modal-open"
                                        data-target="searchManufacturerInfo">
                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                            alt="magnifying_glass.svg">
                                </button>
                            </div>
                            <div id="manufacturer_code_error"></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label dotted indented">指示日</label> <span id="others-frame" style="display: none !important;" class="others-frame btn-orange badge">必須</span>
                            <div class="d-flex">
                                @include('partials._date_picker', [
                                        'inputName' => 'instruction_date_from', 
                                        'attributes' => 'data-error-messsage-container=#request_error_message data-field-name=指示日', 
                                        'value' => request()->get('instruction_date_from'),
                                        'inputClass' => 'w-100c',
                                        ])
                                <span style="font-size:24px; padding:5px 10px;">
                                    ~
                                </span>
                                @include('partials._date_picker', [
                                        'inputName' => 'instruction_date_to', 
                                        'attributes' => 'data-error-messsage-container=#request_error_message data-field-name=指示日', 
                                        'value' => request()->get('instruction_date_to'),
                                        'inputClass' => 'w-100c',
                                        ])
                            </div>
                            <div id="request_error_message"></div>
                        </div>
                        <div class="mr-3">
                            <label class="form-label dotted indented">便No</label>
                            <div class="d-flex">
                                <input type="text"
                                    id=""
                                    style="width: 70px"
                                    name="instruction_no_from">
                                <span style="font-size:24px; padding:5px 10px;">
                                    ~
                                </span>
                                <input type="text"
                                    id=""
                                    style="width: 70px"
                                    name="instruction_no_to">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-center">
                    <button type="submit" class="float-right btn btn-green" style="width: 200px">発注明細書発行</button>
                </div>
            </form>
        </div>
    </div>
    @include('partials.modals.masters._search', [
        'modalId' => 'searchManufacturerInfo',
        'searchLabel' => '材料メーカー',
        'resultValueElementId' => 'manufacturer_code',
        'resultNameElementId' => 'manufacturer_name',
        'model' => 'Supplier'
    ])

@endsection
@push('scripts')
@vite(['resources/js/material/order/detail/index.js'])
@endpush
