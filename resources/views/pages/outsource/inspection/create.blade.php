@extends('layouts.app')

@push('styles')
    @vite('resources/css/index.css')
    @vite('resources/css/common.css')
    {{-- @vite('resources/css/materials/supply_material_kanban_form.css') --}}
@endpush

@section('title', '外注加工検収入力')

@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="pageHeaderBox rounded">
                外注加工検収入力
            </div>
            
                @if(session('success'))
                    <div id="card" style="background-color: #fff; margin-top:20px; padding: 20px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
                        <div style="text-align: left;">
                            <p style="font-size: 18px; color: #0d9c38;">
                                {{ session('success') }}
                            </p>
                        </div>
                    </div>
                @endif  
                <div class="section">
                    <h1 class="form-label bar indented">外注加工検収入力</h1>
                  
                    <form  class="inputFormArea overlayedSubmitForm with-js-validation" id="barcodeForm" data-disregard-empty="true">

                    <div class="box mb-3">
                        <div class="mb-3 d-inline-flex mr-5">
                            <label class="form-label dotted indented">入荷日</label>
                            <span class="btn-orange badge">必須</span>
                            <div class="d-flex">
                                @include('partials._date_picker', ['inputName' => 'arrival_day',
                                    'inputClass' => 'w-130c',
                                    'attributes' => 'data-error-messsage-container=#date_error_message data-field-name=入荷日',  
                                    'required' => true,
                                    'value' => Request::get('arrival_day', date('Ym01'))])
                            </div>
                            <div id="date_error_message"></div> 
                        </div>
                        <div class="mb-3 d-inline-flex mr-5">
                            <label class="form-label dotted indented">便No</label>
                            <span class="btn-orange badge">必須</span>
                            <div class="d-flex">
                                <input type="text" class="updateHidden" id="incoming_flight_number" data-hidden="incoming_flight_number_hidden"
                                       style="width: 70px" 
                                       required
                                       data-field-name="便No"
                                       data-error-messsage-container="#incoming_flight_number_error"
                                       name="incoming_flight_number"
                                       maxlength="2" value="{{ request()->get('incoming_flight_number') }}"
                                       onkeypress="return event.charCode >= 48 && event.charCode <= 57">
                            </div>
                            <div id="incoming_flight_number_error"></div> 
                        </div>
                        <div class="mb-3 d-inline-flex">
                            <label class="form-label dotted indented">仕入先</label>
                            <span class="btn-orange badge">必須</span>
                            <div class="d-flex">
                                {{ request()->get('customer_name')  }}
                            @php
                                $customer_code  =  request()->get('supplier_code') ?? '';
                                $customer_name  =  ($customer_code) ? request()->get('supplier_name')  : '';
                            @endphp
                                <input type="text" id="supplier_code" name="supplier_code"
                                    data-field-name="仕入先"
                                    data-error-messsage-container="#isupplier_code_error"
                                    data-validate-exist-model="supplier" 
                                    data-validate-exist-column="customer_code"
                                    data-inputautosearch-model="supplier"
                                    data-inputautosearch-column="customer_code"
                                    data-inputautosearch-return="supplier_name_abbreviation"
                                    data-inputautosearch-reference="supplier_name"
                                    class="text-left searchOnInput Supplier acceptNumericOnly updateHidden"
                                    minlength="6" value="{{ $customer_code }}"
                                    maxlength="6"
                                    onkeypress="return event.charCode >= 48 && event.charCode <= 57" 
                                    style="width: 120px"
                                    required>
                                <input type="text" readonly id="supplier_name" class="middle-name ml-half" name="supplier_name" style="width: 250px" data-hidden="supplier_name_hidden" value="{{ $customer_name }}">
                                <button type="button" class="btnSubmitCustom js-modal-open ml-half"
                                                data-target="searchSupplierModal"
                                                data-query="searchSupplierModal"
                                                data-reference="customer_code">
                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                            alt="magnifying_glass.svg">
                                </button>
                            </div>
                            <div id="isupplier_code_error"></div> 
                        </div>
                        
                    
                        <input type="hidden" name="arrival_day" id="arrival_day_hidden" value="{{ request()->get('arrival_day') }}">
                        <input type="hidden" name="incoming_flight_number" id="incoming_flight_number_hidden" value="{{ request()->get('incoming_flight_number') }}">
                        <input type="hidden" name="supplier_code" id="supplier_code_hidden" value="{{ request()->get('supplier_code') }}">
                        <input type="hidden" name="supplier_name" id="supplier_name_hidden" value="{{ request()->get('supplier_name') }}">
                        <div class="mb-3">
                            <p href="#" class="float-left">下の入力枠にカーソルをセットした状態でバーコードを読み取ってください</p>
                            <br/>
                            <br/>
                            <input type="text" id="barcode-input" 
                                    name="barcode" 
                                    placeholder="" 
                                    style="width: 300px;"  
                                    class="@if (session('error-noData')) input-error @endif acceptNumericOnly" 
                                    maxlength="9" >
                            
                            <div class="error_msg_barcode validation-error-message"></div>
                            <br/>
                            <div class="mb-5 mt-2">
                                <label class="form-label dotted indented">バーコード情報</label> <span
                                    class="btn-orange badge">必須</span>
                                @php
                                    $selectedManagementNos = request()->get('management_no', []);
                                @endphp
                                <div>
                                    <select class="customScrollbarSelect" name="management_no[]" 
                                    style="width:300px; font-size: 24px" multiple required
                                    data-field-name="バーコード情報"
                                    data-error-messsage-container="#management_no">
                                        @foreach ($selectedManagementNos as $managementNo)
                                            @php
                                                // Extract only the first 5 digits from kanbanMasters management_no
                                                $kanbanFirstFiveDigits = $kanbanMasters->pluck('management_no')->map(fn($no) => substr($no, 0, 5))->toArray();
                                                // Extract first 5 digits from the current managementNo for comparison
                                                $managementNoFirstFive = substr($managementNo, 0, 5);
                                            @endphp
                                            <option value="{{ $managementNo }}" @if(in_array($managementNoFirstFive, $kanbanFirstFiveDigits))selected @endif>{{ $managementNo }}</option>
                                        @endforeach
                                    </select>
                                    <div id="management_no"></div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-center">
                                <button type="button"
                                    class="btn btn-primary btn-blue w-200c mr-10c"
                                    data-clear-inputs
                                    data-clear-form-target="#form"
                                    >検索条件をクリア</button>
                                <button class="btn btn-blue btn-primary w-200c" type="submit">
                                    バーコード情報確認
                                </button>
                            </div>
                        </div>
                    </form>
             </div> 

            <form action="{{ route('outsource.inspectionCreate.store') }}" method="POST" id="barcodeDataForm" class="overlayedSubmitForm with-js-validation"
                data-confirmation-message="外注加工検収情報を登録します、よろしいでしょうか？">
                @csrf
                <div class="section">
                    <h1 class="form-label bar indented">バーコード情報結果</h1>
                    <div class="box">
                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>管理No.</th>
                                <th>製品品番</th>
                                <th>品名</th>
                                <th>指示日</th>
                                <th>指示数</th>
                                <th>入荷数</th>
                            </tr>
                            </thead>
                            <tbody>
                                @forelse ($kanbanMasters as $index => $kanbanMaster)
                                <input type="hidden" name="order_no[]" value="{{ $orderNo +  $index}}">
                                <input type="hidden" name="management_no[]" value="{{ $kanbanMaster->management_no }}">
                                <input type="hidden" name="product_code[]" value="{{ $kanbanMaster->part_number }}">
                                <input type="hidden" name="creator[]" value="{{ request()->user()->id }}">
                                <input type="hidden" name="created_at[]" value="{{ now() }}">
                                <input type="hidden" name="arrival_day[]" id="arrival_day_hidden" value="{{ request()->get('arrival_day') }}">
                                <input type="hidden" name="incoming_flight_number[]" id="incoming_flight_number_hidden" value="{{ request()->get('incoming_flight_number') }}">
                                <input type="hidden" name="supplier_code[]" id="supplier_code_hidden" value="{{ request()->get('supplier_code') }}">
                                    <tr data-row="{{ $index }}">
                                        <td width="13%" style="vertical-align:middle">{{ $kanbanMaster->management_no}}</td>
                                        <td width="13%" style="vertical-align:middle">{{ $kanbanMaster->part_number }}</td>
                                        <td style="vertical-align:middle">{{ $kanbanMaster->product?->product_name }}</td>
                                        <td width="15%" style="text-align:center; vertical-align:middle">
                                            <div style="display: flex; justify-content: center;">
                                                @php   $newDate = now()->format('Ymd'); @endphp
                                                <input type="text" name="instruction_date[]" style="text-align: left"
                                                    class="@if (session('error')) input-error @endif" 
                                                    id="instruction_date_{{ $index }}"
                                                    data-format="YYYYMMDD"
                                                    minlength="8"
                                                    maxlength="8"
                                                    pattern="\d*"
                                                    oninput="updateProcessCodes()"
                                                    value="{{ $newDate }}" 
                                                    data-input-required>
                                                <button type="button" class="btnSubmitCustom buttonPickerJS ml-1"
                                                        data-target="instruction_date_{{ $index }}"
                                                        data-format="YYYYMMDD">
                                                    <img src="{{ asset('images/icons/iconsvg_calendar_w.svg') }}" alt="iconsvg_calendar_w.svg">
                                                </button>
                                            </div>
                                        </td>
                                        <td width="10%" style="text-align:right; vertical-align:middle">
                                            <input type="text" name="arrival_number[]" id="arrival_number{{ $index }}"
                                                onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                                class="@if (session('error'))input-error @endif acceptNumericOnly text-right"
                                                data-accept-zero=true style="text-align: right !important;"  
                                                value="<?= $index + 1 ?>" 
                                                data-input-required>
                                        </td>
                                        <td width="10%">
                                            <input type="text" name="arrival_quantity[]" id="arrival_quantity{{ $index }}"
                                                onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                                class="@if (session('error'))input-error @endif acceptNumericOnly text-right"
                                                data-accept-zero=true style="text-align: right !important;"
                                                value="<?= $index + 1 ?>" 
                                                data-input-required>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="12" class="text-center">検索結果はありません</td>
                                    </tr>
                                @endforelse
                            </tbody>                            
                        </table>
                    </div>
                </div>
                <div class="space-between">
                    <p class="text-red" id="warningInputs">
                        @if (session('error'))
                            {{ session('error') }}
                        @endif
                    </p>
                    <div>
                        <button class="btn btn-green w-200c @if (count($selectedManagementNos) == 0)btn-disabled @else btn-success @endif" 
                                type="submit" 
                                data-register-button
                                @if (count($selectedManagementNos) == 0)disabled @endif
                                >この内容で登録する</button>
                    </div>
                </div>
            </form>
        </div>
    </div> 
@include('partials.modals.masters._search', [
    'modalId' => 'searchSupplierModal',
    'searchLabel' => '仕入先',
    'resultValueElementId' => 'supplier_code',
    'resultNameElementId' => 'supplier_name',
    'model' => 'Supplier',
    'query'=> "searchProductModal",
    'reference' => "supplier_code"
])
@endsection
@push('scripts')
@vite(['resources/js/outsource/inspection/create.js'])
@endpush