@extends('layouts.app')

@push('styles')
    @vite('resources/css/index.css')
    @vite('resources/css/outsources/material_defect_record.css')
    @vite('resources/css/modals/index.css')
    @vite('resources/css/search-modal.css')
@endpush

@section('title', '加工不良実績入力')
@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="pageHeaderBox rounded">
                加工不良実績入力
            </div>
            <!-- 「登録に必要ないくつかの情報が入力されていません！」 -->
            @if(session('success'))
                <div id="card" style="background-color: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); margin: 20px 0;">
                    <div>
                        <p style="font-size: 18px; color: #0d9c38">
                            {{ session('success') }}
                        </p>
                    </div>
                </div>
            @endif
            <div id="successInputs" style="background-color: #fff; margin-top:20px; padding: 20px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); display:none; color:#0d9c38;">
                <div style="text-align: left;">データが正常に更新されました。</div>
            </div>

            <form 
              action="{{ route('outsource.defect.material.updateDefectRecord', array_merge(['id' => $materialFailureRecord->id], request()->all())) }}" 
                method="POST" 
                id="form"
                class="with-js-validation"
                data-confirmation-message="材料不良記録が更新されます、よろしいでしょうか？"
                >
                @csrf
                @method('PUT')
                <input type="hidden" name="updator" value="{{ request()->user()->id }}">
                <input type="hidden" name="updated_at" value="{{ now()->format('Y-m-d H:i:s') }}">
                <div class="section">
                    <h1 class="form-label bar indented">加工不良実績入力</h1>
                    <div class="box mb-1">
                        <div class="mb-4 d-flex">
                            <div class="mr-3">
                                <label class="form-label dotted indented">返却日</label> <span id="others-frame" class="others-frame btn-orange badge">必須</span>
                                <div class="d-flex">
                                    @include('partials._date_picker', [
                                        'inputName' => 'return_date',
                                        'value' => $materialFailureRecord->return_date->format('Ymd') ?? now()->format('Ymd'),
                                        'isDisabled' => false
                                    ])
                                </div>
                            </div>
    
                            <div class="mr-4">
                                <label class="form-label dotted indented">工程</label> <span
                                    class="others-frame btn-orange badge">必須</span>
                                <div class="d-flex">
                                    <input  type="text"
                                            class="mr-2 fetchQueryName"
                                            data-error-messsage-container="#rprocess_code_message"
                                            data-validate-exist-model="Process" 
                                            data-validate-exist-column="process_code"
                                            data-inputautosearch-model="Process" 
                                            data-inputautosearch-column="process_code"
                                            data-inputautosearch-return="abbreviation_process_name" 
                                            data-inputautosearch-reference="process_name"
                                            maxlength="4"
                                            id="process_code" 
                                            name="process_code" 
                                            value="{{ $materialFailureRecord->process_code ?? '' }}"
                                            style="width:200px" required>
                                    <input type="text" readonly
                                            id="process_name"
                                            name="process_name"
                                            class="mr-2"
                                            value="{{ $materialFailureRecord?->process?->abbreviation_process_name ?? '' }}"
                                            style="margin-left: 2px" disabled>
                                    <button type="button" class="btnSubmitCustom js-modal-open"
                                            data-target="searchProcessModal"
                                            style="margin-left: 2px;">
                                        <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                            alt="magnifying_glass.svg">
                                    </button>
                                </div>
                                <div class="error_msg"></div>
                            </div>
    
                            <div class="mr-3">
                                <label class="form-label dotted indented">製品品番</label> <span
                                    class="others-frame btn-orange badge">必須</span>
                                <div class="d-flex">
                                    <input  type="text"
                                            class="mr-2 fetchQueryName"
                                            data-error-messsage-container="#product_code-error"
                                            data-validate-exist-model="ProductNumber"
                                            data-validate-exist-column="part_number"
                                            data-inputautosearch-model="ProductNumber"
                                            data-inputautosearch-column="part_number"
                                            data-inputautosearch-return="product_name"
                                            data-inputautosearch-reference="product_name"
                                            onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                            id="product_code"
                                            name="product_code"
                                            value="{{ $materialFailureRecord?->product_number ?? '' }}"
                                            style="width:200px" required>
                                    <input type="text" readonly
                                            id="product_name"
                                            name="product_name"
                                            class="mr-2"
                                            value="{{ $materialFailureRecord?->product?->product_name ?? '' }}"
                                            style="margin-left: 2px">
                                    <button type="button" class="btnSubmitCustom js-modal-open"
                                            data-target="searchProductNumberModal"
                                            style="margin-left: 2px;">
                                        <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                            alt="magnifying_glass.svg">
                                    </button>
                                </div>
                                <div class="error_msg"></div>
                            </div>
                        </div>
    
                        <div class="mb-2 d-flex">
                            <div class="mr-3">
                                <label class="form-label dotted indented">伝票No</label> <span
                                    class="others-frame btn-orange badge">必須</span>
                                <div class="d-flex">
                                    <input type="text" value="{{ $materialFailureRecord->slip_no ?? '' }}" id="slip_no" name="slip_no" required >
                                </div>
                                <div class="error_msg"></div>
                            </div>
                        </div>
                    </div>
                    <div class="box">
                        <div class="mt-2 mb-3 d-flex">
                            <div class="mr-3">
                                <label class="form-label dotted indented">材料品番</label>
                                <div class="d-flex">
                                    <input type="text"
                                           readonly
                                           id="part_code"
                                           name="material_number"
                                           class="mr-2"
                                           value="{{ $materialFailureRecord->material_number ?? '' }}"
                                           style="width: 130px;">
                                    <input type="text"
                                           readonly
                                           id="part_name"
                                           name="material_name"
                                           class="mr-2"
                                           value="{{ $materialFailureRecord?->material?->product_name ?? '' }}"
                                           style="width: 220px; margin-left: 2px;">
                                </div>
                            </div>
    
                            <div class="mr-4">
                                <label class="form-label dotted indented">仕入先名</label>
                                <div class="d-flex">
                                    <input type="text" readonly id="supplier_id" style="width: 130px;" 
                                            value="{{ $materialFailureRecord?->supplier_code ?? '' }}"
                                            class="mr-2"
                                            name="supplier_code">
                                    <input type="text" readonly id="supplier_name" style="width: 220px; margin-left: 2px"
                                            value="{{ $materialFailureRecord?->supplier?->supplier_name_abbreviation ?? '' }}"
                                            name="supplier_name">
                                </div>
                            </div>
    
                            <div class="mr-3">
                                <label class="form-label dotted indented">材料メーカー名</label>
                                <div class="d-flex">
                                    <input type="text" readonly id="material_manufacturer_code" style="width: 130px;"
                                            value="{{ $materialFailureRecord?->material_manufacturer_code ?? '' }}"
                                            class="mr-2"
                                            name="material_manufacturer_code">
                                    <input type="text" readonly id="person_in_charge" style="width: 220px; margin-left: 2px"
                                            value="{{ $materialFailureRecord?->manufacturerInfo?->person_in_charge ?? '' }}"
                                            name="person_in_charge">
                                </div>
                            </div>
                        </div>
    
                        <div class="mb-2 d-flex">
                            <div class="mr-3">
                                <label class="form-label dotted indented">加工単価</label>
                                <div class="d-flex">
                                    <input type="text" readonly id="processing_unit_price" style="width: 150px;"
                                            value="{{ $materialFailureRecord?->product?->latestProductPrice?->unit_price ?? 0 ?? '' }}"
                                            name="processing_unit_price" class="text-right">
                                </div>
                            </div>
                        </div>
                        <div class="mt-5">
                          @php
                            $amount =  round($materialFailureRecord->quantity * $materialFailureRecord?->product?->latestProductPrice?->unit_price * ($materialFailureRecord->processing_rate / 100))
                          @endphp
                            <table class="table table-bordered table-striped text-center" style="width: 800px;">
                                <thead>
                                <tr>
                                    <th width="15%">理由</th>
                                    <th width="10%">数量</th>
                                    <th width="5%">加工率</th>
                                    <th width="8%">金額</th>
                                    
                                </tr>
                                </thead>
                                <tbody>
                                    <tr data-id="{{ $materialFailureRecord->id }}" id="row-{{ $materialFailureRecord->id }}">
                                        <td>
                                            <select style="width: 100%; height: 40px"  name="reason_code" id="reason_code">
                                                @foreach ($reasons as $reason)
                                                    <option value="{{ $reason->code }}" @if($materialFailureRecord?->reason_code == $reason->code) selected @endif>
                                                        {{ $reason->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text"
                                                style="width: 100%"
                                                class="numberCharacter"
                                                name="quantity"
                                                id="quantity"
                                                data-input-quantity
                                                required
                                                value="{{ $materialFailureRecord->quantity }}" >
                                        </td>
                                        <td>
                                            <select style="width: 100%; height: 40px" name="processing_rate" id="processing_rate">
                                                @for ($i = 0; $i <= 100; $i += 10)
                                                    <option @if($materialFailureRecord->processing_rate == $i) selected @endif>{{ $i }}</option>
                                                @endfor
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text"  value="{{  $amount  }}" name="itemSubtotal" class="totalAmount" readonly>
                                        </td>
                                        
                                    </tr>
                                
                                </tbody>
                            </table>
                        </div>
    
                        <div>
                            <table class="table table-bordered table-striped text-center"
                                   style="margin-left: 23%;width: 250px;">
                                <thead>
                                <tr>
                                    <th width="15%">合計金額</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td class="text-right totalAmount">{{$amount }}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
           
            <div class="text-right">
                <div>
                    {{-- To Change later if Material 53 is done --}}
                    <a href="{{ route('outsource.defect.material.index', request()->all()) }}" class="btn btn-primary btn-wide"> 一覧に戻る </a>
                    <button type="submit" class="btn btn-success btn-wide" data-input-update> 更新 </button>
                </div>
            </div>
            </form>
          
        </div>
    </div>
    @include('partials.modals.masters._search', [
        'modalId' => 'searchProcessModal',
        'searchLabel' => '工程',
        'resultValueElementId' => 'process_code',
        'resultNameElementId' => 'process_name',
        'model' => 'Process'
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
    @vite('resources/js/outsource/defect/material/edit.js')
@endpush