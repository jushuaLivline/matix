@extends('layouts.app') @push('styles')
    @vite('resources/css/index.css')
    @vite('resources/css/modals/index.css')
@vite('resources/css/search-modal.css') @endpush 

@section('title', '外注加工発注伝票発行') 
@section('content') 
<div class="content">
        <div class="contentInner">
            <div class="pageHeaderBox rounded"> 外注加工発注伝票発行 </div>
            @if(session('error'))
                <div class="tableWrap borderLesstable message">
                    <div class="error">
                        {{ session('error') }}
                    </div>
                </div>
            @endif

            @php
                $defaultOption =(request()->get('order_classification') != 2  || request()->missing('order_classification')) ? 'checked' : '';
                $disabled = (request()->get('order_classification') != 2 || request()->missing('order_classification')) ? 'disabled' : '';
            @endphp
            <form 
                action="{{ route("outsource.order.slip.pdf.export", request()->except('_token')) }}" 
                class="with-js-validation"
                id="orderSlipIssuanceForm"
                data-confirmation-message="発注伝票を出力します、よろしいでしょうか？"
                > @csrf 
                <div class="section">
                    <h1 class="form-label bar indented">発注伝票発行</h1>
                    <div class="box mb-3">
                        <div class="mb-2 d-flex" style="flex-direction: column">
                            <div class="mr-10">
                                <label class="form-label dotted indented">発行区分</label> <span
                                    class="btn-orange badge">必須</span>
                                <div>
                                    <label class="radioBasic">
                                        <input type="radio" name="order_classification" value="" 
                                            {{$defaultOption}}
                                            class="issue-option-radio" />
                                        <span>未発行分（発注明細書および納品書・受領書）</span>
                                    </label>
                                    <label class="radioBasic">
                                        <input type="radio" name="order_classification" value="2"
                                        @if(request()->get('order_classification') ==2 )checked @endif
                                            class="issue-option-radio" />
                                        <span>再発行</span>
                                    </label>
                                </div>
                            </div>
                            <div class="mr-4 mt-3">
                                <label class="form-label dotted indented">仕入先</label> <span
                                    style="display: none !important; height: 21px;" class="others-frame btn-orange badge">必須</span>
                                    
                                    <div class="d-flex">
                                        <input type="text" name="supplier_code"
                                            id="supplier_code" style="margin-right: 10px; width: 100px;"
                                            data-field-name="仕入先"
                                            data-validate-exist-model="supplier"
                                            data-validate-exist-column="customer_code"
                                            data-inputautosearch-model="supplier"
                                            data-inputautosearch-column="customer_code"
                                            data-inputautosearch-return="supplier_name_abbreviation"
                                            data-inputautosearch-reference="supplier_name"
                                            class="text-left searchOnInput Supplier acceptNumericOnly"
                                            minlength="6"
                                            maxlength="6"
                                            onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                            value="{{ request()->get('supplier_code') }}" 
                                            required
                                            
                                            {{ $disabled }}
                                            >
                                        <input type="text" readonly
                                            name="supplier_name"
                                            id="supplier_name" style="margin-right: 10px; width: 200px;"
                                            value="{{ request()->get('supplier_name')}}"
                                            class="middle-name text-left">
                                        <button type="button" class="btnSubmitCustom js-modal-open"
                                                data-target="searchSupplierModal" 
                                                {{ $disabled }}
                                                >
                                            <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                alt="magnifying_glass.svg">
                                        </button>
                                    </div>
                                    <div data-error-container="supplier_code"></div>
                            </div>
                            <div class="mr-3 mt-3">
                                <label class="form-label dotted indented">指示日</label> <span id="others-frame"
                                    style="display: none !important; height: 21px;" class="others-frame btn-orange badge">必須</span>
                                <div class="d-flex">
                                    @include('partials._date_picker', [
                                            'inputName' => 'instruction_date_from', 
                                            'attributes' => 'data-error-messsage-container=#date_error_message data-field-name=指示日', 
                                            'inputClass' => 'w-130c mr-2',
                                            'isDisabled' => $disabled,
                                            'enableDateStart' => true,
                                            'enableDateEnd' => true,
                                            'required' => true
                                            ])
                                    <span style="font-size:24px; padding:5px 10px;"> ~ </span>
                                    @include('partials._date_picker', [
                                            'inputName' => 'instruction_date_to', 
                                            'attributes' => 'data-error-messsage-container=#date_error_message data-field-name=指示日', 
                                            'inputClass' => 'w-130c mr-2',
                                            'isDisabled' => $disabled,
                                            'enableDateStart' => true,
                                            'enableDateEnd' => true,
                                            'required' => true
                                            ])
                                    </div>
                                    <div id="date_error_message" style="width: 100%;"></div>
                            </div>
                            <div class="mr-3 mt-3">
                                <label class="form-label dotted indented">便No</label>
                                <div class="d-flex">
                                    <input type="text" id="" style="width: 70px" 
                                        name="instruction_number_from" 
                                        class="acceptNumericOnly"
                                        maxLength="6"
                                        value="{{ request()->get('instruction_number_from') }}"
                                        {{ $disabled }} >
                                    <span style="font-size:24px; padding:5px 10px;"> ~ </span>
                                    <input type="text" id="" style="width: 70px" 
                                            name="instruction_number_to" 
                                            class="acceptNumericOnly"
                                            maxLength="6"
                                            value="{{ request()->get('instruction_number_to') }}"
                                            {{ $disabled }} >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-end d-flex" style="justify-content: end">
                    <button type="submit" id="orderSlip" class="float-right btn btn-success btn-wide">発注伝票発行</button>
                </div>
            </form>
        </div>
    </div>
    @include('partials.modals.masters._search', [
        'modalId' => 'searchSupplierModal',
        'searchLabel' => '仕入先',
        'resultValueElementId' => 'supplier_code',
        'resultNameElementId' => 'supplier_name',
        'model' => 'Supplier'
    ])

@endsection



@push('scripts')
@vite(['resources/js/outsource/order/slip/index.js'])
@endpush
