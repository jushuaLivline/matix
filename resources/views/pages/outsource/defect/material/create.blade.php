@extends('layouts.app')

@push('styles')
    @vite('resources/css/index.css')
    @vite('resources/css/common.css')
    @vite('resources/css/outsources/material_defect_record.css')
    @vite('resources/css/modals/index.css')
    @vite('resources/css/search-modal.css')
    @vite('resources/css/outsource/defect/material/create.css')
@endpush

@section('title', '加工不良実績入力')
@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="pageHeaderBox rounded">
                加工不良実績入力
            </div>
            @if(session('success'))
                <div id="flash-message" style="background-color: #fff">
                    {{ session('success') }}
                </div>
            @endif
            <form action="{{ route('outsource.defect.material.dump') }}" method="POST" id="form"
                class="with-js-validation"
                data-confirmation-message="加工不良情報を登録します。よろしいでしょうか？">
                @csrf
                <div class="section">
                    <h1 class="form-label bar indented">加工不良実績入力</h1>
                    <div class="box mb-1">
                        <div class="mb-4 d-flex">
                            <div class="mr-5">
                                <label class="form-label dotted indented">返却日</label> <span id="others-frame" class="others-frame btn-orange badge">必須</span>
                                <div class="d-flex">
                                    @include('partials._date_picker', [
                                        'inputName' => 'return_date',
                                        'attributes' => 'data-error-messsage-container=#date_error_message data-field-name=返却日',
                                        'inputClass' => 'w-100c',
                                        'value' => isset($firstData['return_date']) ? $firstData['return_date'] : now()->format('Ymd'),
                                        'required' => true
                                    ])
                                </div>
                                <div id="date_error_message"></div>
                            </div>
    
                            <div class="mr-4">
                                <label class="form-label dotted indented">工程</label> <span class="others-frame btn-orange badge">必須</span>
                                <div class="d-flex">
                                    <div class="formPack mr-10c">
                                        <input type="text" name="process_code"
                                            data-field-name="工程"
                                            data-error-messsage-container="#rprocess_code_message"
                                            data-validate-exist-model="Process" 
                                            data-validate-exist-column="process_code"
                                            data-inputautosearch-model="Process" 
                                            data-inputautosearch-column="process_code"
                                            data-inputautosearch-return="abbreviation_process_name" 
                                            data-inputautosearch-reference="process_name"
                                            data-custom-required-error-message="材料メーカーを入力してください"
                                            maxlength="4"
                                            required
                                            id="process_code" class="searchOnInput Process w-100c"
                                            value="{{ $firstData['process_code'] ?? '' }}">
                                    </div>
                                    <div class="formPack fixedWidth box-middle-name mr-2  w-200c">
                                        <input type="text" readonly
                                            name="process_name"
                                            id="process_name"
                                            value="{{ $firstData['process_name'] ?? '' }}"
                                            class="middle-name text-left">
                                    </div>
                                    <div class="formPack fixedWidth w-20c ml-2">
                                        <button type="button" class="btnSubmitCustom js-modal-open"
                                                data-target="searchProcessModal"
                                                data-query-field="">
                                            <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                alt="magnifying_glass.svg">
                                        </button>
                                    </div>
                                </div>
                                <div id="rprocess_code_message"></div>

                            </div>
    
                            <div class="mr-3">
                                <label class="form-label dotted indented">製品品番</label> <span
                                    class="others-frame btn-orange badge">必須</span>
                                       <div class="d-flex">
                                            <div class="mr-10c">
                                                <input type="text" name="product_number" id="product_number"
                                                        data-field-name="製品品番"
                                                        data-error-messsage-container="#product_number_error"
                                                        data-validate-exist-model="ProductNumber"
                                                        data-validate-exist-column="part_number"
                                                        data-inputautosearch-model="ProductNumber"
                                                        data-inputautosearch-column="part_number"
                                                        data-inputautosearch-return="product_name"
                                                        data-inputautosearch-reference="product_name"
                                                        class="text-left searchOnInput ProductNumber w-150c"
                                                        required
                                                        onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                                        value="{{  $firstData['material_code'] ?? ''}}">
                                                </div>
                                                <div class="formPack mr-2">
                                                    <input type="text" readonly
                                                        name="product_name"
                                                        id="product_name" style="width: 100%;"
                                                        maxLength="20"
                                                        value="{{ $firstData['material_name'] ?? '' }}"
                                                        class="middle-name text-left">
                                                </div>
                                                <div class="formPack">
                                                    <button type="button" class="btnSubmitCustom js-modal-open"
                                                            data-target="searchProductNumberModal">
                                                        <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                            alt="magnifying_glass.svg">
                                                    </button>
                                                </div>
                                        </div>
                                                
                                            <div id="product_number_error"></div>
                            </div>
                        </div>
    
                        <div class="mb-2 d-flex">
                            <div class="mr-3">
                                <label class="form-label dotted indented">伝票No</label> <span
                                    class="others-frame btn-orange badge">必須</span>
                                <div class="d-flex">
                                    <input type="text" value="{{ $firstData['slip_no'] ?? '' }}" id="slip_no" name="slip_no" 
                                        maxLength="10" class="w-100c mr-2"
                                        data-field-name="伝票No"
                                        data-error-messsage-container="#slip_no_error"
                                        required>
                                </div>
                                <div id="slip_no_error"></div>
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
                                        value="{{ $firstData['material_code'] ?? '' }}"
                                        style="width: 130px;"
                                    >
                                    <input type="text"
                                        readonly
                                        id="part_name"
                                        name="material_name"
                                        class="mr-2"
                                        value="{{ $firstData['material_name'] ?? '' }}"
                                        style="width: 220px; margin-left: 2px;"
                                    >
                                </div>
                            </div>
    
                            <div class="mr-4">
                                <label class="form-label dotted indented">仕入先名</label>
                                <div class="d-flex">
                                    <input type="text" readonly id="supplier_id" style="width: 130px;" 
                                            value="{{ $firstData['supplier_code'] ?? '' }}"
                                            class="mr-2"
                                            name="supplier_code">
                                    <input type="text" readonly id="supplier_name" style="width: 220px; margin-left: 2px"
                                            value="{{ $firstData['supplier_name'] ?? '' }}"
                                            name="supplier_name">
                                </div>
                            </div>
    
                            <div class="mr-3">
                                <label class="form-label dotted indented">材料メーカー名</label>
                                <div class="d-flex">
                                    <input type="text" readonly id="material_manufacturer_code" style="width: 130px;"
                                        value="{{ $firstData['material_manufacturer_code'] ?? '' }}"
                                        class="mr-2"
                                        name="material_manufacturer_code">
                                </div>
                            </div>
                        </div>
    
                        <div class="mb-2 d-flex">
                            <div class="mr-3">
                                <label class="form-label dotted indented">加工単価</label>
                                <div class="d-flex">
                                    <input type="text" readonly id="processing_unit_price" style="width: 150px;"
                                            value="{{ $firstData['processing_unit_price'] ?? '' }}"
                                            name="processing_unit_price" class="text-right">
                                </div>
                            </div>
                        </div>
                        <div class="mt-5">
                            <table class="table table-bordered table-striped text-center" style="width: 800px;">
                                <thead>
                                <tr>
                                    <th width="15%">理由</th>
                                    <th width="10%">数量</th>
                                    <th width="5%">加工率</th>
                                    <th width="8%">金額</th>
                                    <th width="10%">操作</th>
                                </tr>
                                </thead>
                                <tbody class="align-top">
                                @foreach($sessionMaterialDefect as $item)
                                    <tr data-id="{{ $item['id'] }}" id="row-{{ $item['id'] }}">
                                        <td>
                                            <select style="width: 100%; height: 40px" disabled>
                                                @foreach ($reasons as $reason)
                                                    <option value="{{ $reason->code }}" @if($item['reason_code'] == $reason->code) selected @endif>
                                                        {{ $reason->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number"
                                                style="width: 100%"
                                                class="numberCharacter item_quantity"
                                                name="itemQuantity"
                                               
                                                required
                                                value="{{ $item['quantity'] }}" disabled>
                                        </td>
                                        <td>
                                            <select style="width: 100%; height: 40px" class="processing_rate" name="itemProcessRate" disabled>
                                                @for ($i = 0; $i <= 100; $i += 10)
                                                    <option @if($item['processing_rate'] == $i) selected @endif>{{ $i }}</option>
                                                @endfor
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" class="sub_total" disabled value="{{ $item['subTotal'] }}" name="itemSubtotal">
                                          
                                        </td>
                                        <td>
                                            <div class="center" id="EditDelete">
                                                <button type="button" onclick="enableInputs(this)" class="btn btn-block btn-blue" id="edit">編集</button>
                                                <button type="button" onclick="confirmDelete(this)" class="btn btn-block btn-orange" style="margin-left: 6px" id="delete">削除</button>
                                            </div>
                                            
                                            <div class="center" id="UdpateUndo" style="display: none;">
                                                <button type="button" onclick="updateData(this)" data-item-id="{{ $item['id'] }}" class="btn btn-block btn-green" id="update">更新</button>
                                                <button type="button" onclick="cancelEdit(this)" class="btn btn-block btn-gray" style="margin-left: 6px" id="undo">取消</button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                <tr id="default-tr">
                                    <td>
                                        <select class="" name="reason_code" id="" style="width: 100%; height: 40px">
                                            @forelse ($reasons as $reason)
                                                <option value="{{ $reason->code }}">{{ $reason->name }}</option>
                                            @empty
                                                <option>null</option>
                                            @endforelse
                                            
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number"
                                               style="width: 100%"
                                               class="numberCharacter item_quantity"
                                               id="quantity"
                                               @if(!$sessionMaterialDefect)
                                                data-field-name="数量"
                                                data-error-messsage-container="#item_quantity_error"
                                                required
                                               @endif
                                               name="quantity">
                                               
                                               @if(!$sessionMaterialDefect)
                                               <div id="item_quantity_error"></div>
                                               @endif
                                    </td>
                                    <td>
                                        <select id="process_rate" class="processing_rate" name="processing_rate" style="width: 100%; height: 40px">
                                            @for($i = 0; $i <= 100; $i= $i + 10)
                                                <option>{{ $i }}</option>
                                            @endfor
                                        </select>
                                    </td>
                                    <td>
                                        <input readonly id="subTotal" name="subTotal" class="sub_total" readonly></input>
                                    </td>
                                    <td>
                                        <div class="center">
                                            <button class="btn btn-block btn-green" type="submit">追加</button>
                                            <button onclick="clearData(this)" type="button"
                                                    class="btn btn-block btn-gray" style="margin-left: 6px">クリア
                                            </button>
                                        </div>
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
                                    <td class="text-right" id="grand_total">{{ $grandTotal }}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </form>
            <div class="space-between">
                <div>
                    {{-- <p class="text-red">登録に必要ないくつかの情報が入力されていません！</p> --}}
                </div>
                <div>
                    {{-- To Change later if Material 53 is done --}}
                    <a href="{{ route('outsource.defect.material.index') }}" class="btn btn-primary btn-wide"> 一覧に戻る </a>
                    <a href="{{ route('outsource.defect.material.store.get') }}"
                        class="btn btn-success btn-wide"
                        id="validateAndProceed"> この内容で登録する </a>
                </div>
            </div>

            @if(session('error'))
                <div id="card" style="background-color: #f0f0f0; padding: 20px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
                    <div>
                        <p style="font-size: 18px; color: #d81414; margin-bottom: 10px;">
                            {{ session('error') }}
                        </p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Still use same process, but this time, only fetch those that are in the process_orders --}}
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
        'resultValueElementId' => 'product_number',
        'resultNameElementId' => 'product_name',
        'model' => 'ProductNumber'
    ])
@endsection
@push('scripts')
@vite('resources/js/outsource/defect/material/create.js')
@endpush