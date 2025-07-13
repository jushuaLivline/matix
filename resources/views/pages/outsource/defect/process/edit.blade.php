@extends('layouts.app')

@push('styles')
    @vite('resources/css/index.css')
    @vite('resources/css/outsources/material_defect_record.css')
    @vite('resources/css/modals/index.css')
    @vite('resources/css/search-modal.css')
@endpush

@section('title', '更新処理失敗記録')
@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="pageHeaderBox rounded">
                更新処理失敗記録
            </div>

            @if(session('success'))
                <div id="card" style="background-color: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); margin: 20px 0;">
                    <div>
                        <p style="font-size: 18px; color: #0d9c38">
                            {{ session('success') }}
                        </p>
                    </div>
                </div>
            @endif
    
            <form action="{{ route('outsource.defect.process.update', array_merge([$query->id], request()->all())) }}" 
                method="POST" id="form"
                class="with-js-validation"
                data-confirmation-message="処理不良記録が更新されます。よろしいですか?"
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
                                <label class="form-label dotted indented">廃却日</label>
                                <div class="d-flex">
                                    <input type="text" value="{{ $query->registration_no }}" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="mb-4 d-flex">
                            <div class="mr-3">
                                <label class="form-label dotted indented">シリアルNo</label> <span id="others-frame" class="others-frame btn-orange badge">必須</span>
                                <div class="d-flex">
                                    <input type="text" value="{{ $query->serial_number }}" 
                                        data-field-name="シリアルNo"
                                        data-error-messsage-container="#serial_number_error"
                                        name="serial_number" style="width: 131px" required>
                                </div>
                                <div id="serial_number_error"></div>
                            </div>
                        </div>
                        <div class="mb-4 d-flex">
                            <div class="mr-2">
                                <label class="form-label dotted indented">伝票No</label> <span id="others-frame" class="others-frame btn-orange badge">必須</span>
                                <div class="d-flex">
                                    <input type="text" value="{{ $query->slip_no }}" 
                                            data-field-name="伝票No"
                                            data-error-messsage-container="#slip_no_error"
                                            name="slip_no" style="width: 131px" required>
                                </div>
                                <div id="slip_no_error"></div>
                            </div>
                        </div>

                        <div class="mb-4 d-flex">
                            <div class="mr-2">
                                <label class="form-label dotted indented">廃却日</label> <span id="others-frame" class="others-frame btn-orange badge">必須</span>
                                <div class="d-flex">
                                    @include('partials._date_picker', [
                                        'inputName' => 'disposal_date',
                                        'attribute' => 'data-error-messsage-container=#disposal_date_error data-field-name=廃却日',
                                        'value' => date('Ymd', strtotime($query->disposal_date)),
                                        'required' => true
                                        ]
                                    )
                                </div>
                                <div id="disposal_date_error"></div>
                            </div>
                        </div>

                        <div class="mb-4 d-flex">
                            <div class="mr-4">
                                <label class="form-label dotted indented">工程</label> <span id="others-frame" class="others-frame btn-orange badge">必須</span>
                                <div class="d-flex">
                                    <p class="formPack fixedWidth fpfw25p mr-half">
                                        <input type="text" id="process_code" name="process_code"
                                            data-field-name="工程"
                                            data-error-messsage-container="#process_code_error"
                                            data-validate-exist-model="Process" 
                                            data-validate-exist-column="process_code"
                                            data-inputautosearch-model="Process"
                                            data-inputautosearch-column="process_code"
                                            data-inputautosearch-return="process_name"
                                            data-inputautosearch-reference="process_name" maxlength="6"
                                            value="{{ $query->process_code }}"
                                            style="width: 200px;" required>
                                    </p>
                                    <p class="formPack fixedWidth fpfw50 box-middle-name mr-half">
                                        <input type="text" readonly id="process_name"
                                            value="{{ optional($query->process)->process_name }}"
                                            class="middle-name" style="width: 230px">
                                    </p>
                                    <div class="formPack fixedWidth fpfw25p">
                                        <button type="button" class="btnSubmitCustom js-modal-open"
                                            data-target="searchProcessModal">
                                            <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                alt="magnifying_glass.svg">
                                        </button>
                                    </div>
                                </div>
                                <div id="process_code_error"></div>
                            </div>
                        </div>
                        
                        <div class="mb-4 d-flex">
                            <div class="mr-4">
                                <label class="form-label dotted indented">製品品番</label> <span id="others-frame" class="others-frame btn-orange badge">必須</span>
                                <div class="d-flex">
                                    <p class="formPack fixedWidth fpfw25p mr-half">
                                        <input type="text" id="product_code" name="part_number"
                                            data-field-name="工程"
                                            data-error-messsage-container="#part_number_error"
                                            data-validate-exist-model="ProductNumber" 
                                            data-validate-exist-column="part_number"
                                            data-inputautosearch-model="ProductNumber"
                                            data-inputautosearch-column="part_number"
                                            data-inputautosearch-return="product_name"
                                            data-inputautosearch-reference="product_name"
                                            value="{{ $query->part_number }}"
                                            style="width: 200px;" required>
                                    </p>
                                    <p class="formPack fixedWidth fpfw50 box-middle-name mr-half">
                                        <input type="text" readonly id="product_name"
                                            value="{{ optional($query->product)->product_name }}"
                                            class="middle-name" style="width: 230px">
                                    </p>
                                    <div class="formPack fixedWidth fpfw25p">
                                        <button type="button" class="btnSubmitCustom js-modal-open"
                                            data-target="searchProductModal">
                                            <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                alt="magnifying_glass.svg">
                                        </button>
                                    </div>
                                </div>
                                <div id="part_number_error"></div>
                            </div>
                        </div>

                        <div class="mb-4 d-flex">
                            <div class="mr-3">
                                <label class="form-label dotted indented">数量</label> <span id="others-frame" class="others-frame btn-orange badge">必須</span>
                                <div class="d-flex">
                                    <input type="text" id="quantity" name="quantity" 
                                        data-field-name="数量"
                                        data-error-messsage-container="#quantity_error"
                                        value="{{ $query->quantity }}" style="width: 100px" required>
                                </div>
                                <div id="quantity_error"></div>
                            </div>

                            <div class="mr-3">
                                <label class="form-label dotted indented">単価</label>
                                <div class="d-flex">
                                    <input type="text" id="process-unit-price" value="{{ number_format($query->product?->latestProductPrice?->unit_price) }}"
                                    style="width: 100px" readonly>
                                </div>
                            </div>

                            <div class="mr-3">
                                <label class="form-label dotted indented">単価</label>
                                <div class="d-flex">
                                    <input type="text" id="total-price" value="{{ number_format($query->product?->latestProductPrice?->unit_price * $query->quantity) }}"
                                    style="width: 100px" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
           
                <div class="text-right">
                    <div>
                        {{-- To Change later if Material 53 is done --}}
                        <a href="{{ route('outsource.defect.process.index', request()->all()) }}" class="btn btn-primary btn-wide"> 一覧に戻る </a>
                        <button type="submit" class="btn btn-success btn-wide"> 更新 </button>
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
        'modalId' => 'searchProductModal',
        'searchLabel' => '製品品番',
        'resultValueElementId' => 'product_code',
        'resultNameElementId' => 'product_name',
        'model' => 'ProductNumber'
    ])
@endsection
@push('scripts')
    @vite(['resources/js/outsource/defect/process/edit.js'])
@endpush