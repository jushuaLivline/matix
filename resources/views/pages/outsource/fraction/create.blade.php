@extends('layouts.app')

@push('styles')
    @vite('resources/css/index.css')
    @vite('resources/css/modals/index.css')
    @vite('resources/css/search-modal.css')
    @vite('resources/css/outsources/fraction/create.css')
@endpush

@section('title', '端数指示入力')

@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="pageHeaderBox rounded">
                端数指示入力
            </div>

            <div class="text-red" id="warningInputs"  style="background-color: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);margin-top: 20px;display:none;">登録に必要ないくつかの情報が入力されていません！</div>
            <div id="successInputs" style="background-color: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);margin-top: 20px; display:none; color:#0d9c38;">データは正常に登録されました</div>
            <div id="successUpdate" style="background-color: #fff; margin-top:20px; padding: 20px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); display:none; color:#0d9c38;">
                <div style="text-align: left;">データは正常に更新されました</div>
            </div>

            <div class="section">
                <h1 class="form-label bar indented">端数指示入力</h1>
                <div class="box mb-3">
                    <div class="mb-2">
                        <div class="mr-3">
                            <label class="form-label dotted indented">仕入先
                                <span class="btn-orange badge">必須</span>
                            </label>
                            <div class="d-flex">
                                <input type="text" id="process_code" name="process_code"
                                    value="{{ $process->process_code ?? '' }}" class="w-10">
                                <input type="text" readonly
                                        id="supplier_name"
                                        name="supplier_name"
                                        value="{{ $process->process_name ?? '' }}"
                                        class="middle-name ml-half"
                                        style="width: 170px">
                                <button type="button" class="btnSubmitCustom js-modal-open ml-half"
                                                data-target="searchProcessModal"
                                            data-reference="process_code">
                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                            alt="magnifying_glass.svg">
                                </button>
                            </div>
                            <div id="process_code_error" class="error_message text-left"></div>
                        </div>
                        <div class="mt-2">
                            @if(isset($message))
                                <div id="card" style="background-color: #f0f0f0; padding: 20px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
                                    <div style="text-align: center;">
                                        <p style="font-size: 18px; color: #0d9c38; margin-bottom: 10px;">
                                            {{ $message }}
                                        </p>
                                    </div>
                                </div>
                            @endif

                            <table class="table table-bordered table-striped text-center table-fraction">
                                <thead>
                                <tr>
                                    <th style="width: 250px;">製品品番</th>
                                    <th style="width: 300px;">品名</th>
                                    <th style="width: 150px;">指示日</th>
                                    <th style="width: 100px;">便</th>
                                    <th style="width: 100px;">数量</th>
                                    <th style="width: 200px;">操作</th>
                                </tr>
                                </thead>
                                <tbody>

                                    @foreach ($sessionData as $data)
                                        <tr data-temp-id="{{ $data['id'] }}">
                                            <td>
                                                <div style="display:flex">
                                                    <input type="text" id="product_code_{{ $data['id'] }}" name="product_code_{{ $data['id'] }}" 
                                                            value="{{ $data['product_code'] }}" 
                                                            disabled>
                                                    <button type="button" class="btnSubmitCustom js-modal-open ml-2"
                                                            data-target="searchProductModal_{{ $data['id'] }}">
                                                        <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                                alt="magnifying_glass.svg">
                                                    </button>
                                                </div>
                                                <div id="product_code_error" class="error_message text-left"></div>
                                            </td>
                                            <td>
                                                <input type="text"
                                                    data-name="product_name";
                                                    id="product_name_{{ $data['id'] }}"
                                                    name="product_name_{{ $data['id'] }}"
                                                    value="{{ $data['product_name'] }}"
                                                    class="middle-name text-left"
                                                    readonly>
                                            </td>
                                            <td>
                                                <div class="center">
                                                    <input type="text" name="instruction_date" style="text-align: center" 
                                                        id="instruction_date_{{ $data['id'] }}" 
                                                        data-format="YYYYMMDD"
                                                        minlength="8"
                                                        maxlength="8"
                                                        pattern="\d*" 
                                                        oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                                        value="{{ $data['instruction_date'] }}"
                                                        disabled>
                                                    <button type="button" class="btnSubmitCustom buttonPickerJS ml-1" 
                                                            data-target="instruction_date_{{ $data['id'] }}"
                                                            data-format="YYYYMMDD">
                                                        <img src="{{ asset('images/icons/iconsvg_calendar_w.svg') }}" alt="iconsvg_calendar_w.svg">
                                                    </button>
                                                </div>
                                                <div id="instruction_date_error" class="error_message text-left"></div>
                                            </td>
                                            <td>
                                                <input type="text" class="numberCharacter" name="instruction_number" value="{{ $data['instruction_number'] }}" disabled>
                                                <div id="instruction_number_error" class="error_message text-left"></div>
                                            </td>
                                            <td>
                                                <input type="text" class="numberCharacter" name="instruction_kanban_quantity" value="{{ $data['instruction_kanban_quantity'] }}" disabled>
                                                <div id="instruction_kanban_quantity_error" class="error_message text-left"></div>
                                            </td>
                                            <td>
                                                <div class="center" id="EditDelete">
                                                    <button onclick="enableInputs(this)" class="btn btn-block btn-blue" id="edit">編集</button>
                                                    <button onclick="confirmDelete(this)" class="btn btn-block btn-orange" style="margin-left: 2px" id="delete">削除</button>
                                                </div>
                                                
                                                <div class="center" id="UdpateUndo" style="display: none;">
                                                    <button onclick="updateData(this)" class="btn btn-block btn-green" id="update">更新</button>
                                                    <button onclick="cancelEdit(this)" class="btn btn-block btn-gray" style="margin-left: 1px" id="undo">取消</button>
                                                </div>
                                            </td>
                                        </tr>
                                        @include('partials.modals.masters._search', [
                                            'modalId' => 'searchProductModal_'. $data['id'],
                                            'searchLabel' => '品番',
                                            'resultValueElementId' => 'product_code_'. $data['id'],
                                            'resultNameElementId' => 'product_name_'. $data['id'],
                                            'model' => 'ProductNumber'
                                        ])
                                    @endforeach
                                    </tbody>
                                    <tfoot>

                                    <tr>
                                        <td>
                                            <div class="center">
                                                <input type="text" id="product_code" value="{{ old('product_code', Request::get('product_code') ?? '') }}" class="" style="margin-right:5px;">
                                                <button type="button" class="btnSubmitCustom js-modal-open"
                                                        data-target="searchProductNumberModal">
                                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                            alt="magnifying_glass.svg">
                                                </button>
                                            </div>
                                            <div id="product_code_error" class="error_message text-left"></div>
                                        </td>
                                        <td>
                                            <input type="text" readonly
                                                id="product_name"
                                                value=""
                                                class="middle-name"
                                                style="width: 170px">
                                        </td>
                                        <td>
                                            <div class="center">
                                                <input type="text" style="text-align: center" 
                                                    id="date_instruction" 
                                                    data-format="YYYYMMDD"
                                                    minlength="8"
                                                    maxlength="8"
                                                    pattern="\d*" 
                                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                                <button type="button" class="btnSubmitCustom buttonPickerJS buttonPickerJSDate ml-1" 
                                                        data-target="date_instruction"
                                                        data-format="YYYYMMDD">
                                                    <img src="{{ asset('images/icons/iconsvg_calendar_w.svg') }}" alt="iconsvg_calendar_w.svg">
                                                </button>
                                            </div>
                                            <div id="instruction_date_error" class="error_message text-left"></div>
                                        </td>
                                        <td>
                                            <input type="text" class="numberCharacter" id="number_instruction" value="">
                                            <div id="instruction_number_error" class="error_message text-left"></div>
                                        </td>
                                        <td>
                                            <input type="text" class="numberCharacter" id="instruction_kanban_quantity" value="">
                                            <div id="instruction_kanban_quantity_error" class="error_message text-left"></div>
                                        </td>
                                        <td>
                                            <div class="center">
                                                <button onclick="storeData(this)" class="btn btn-block btn-green">追加</button>
                                                <button onclick="clearData(this)" class="btn btn-block btn-gray" style="margin-left: 1px">クリア</button>
                                            </div>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-end mt-3 mb-3">
                <div>
                @php
                        $class= (count($sessionData) > 0) ? "" :"btn-disabled";
                        $attr =  (count($sessionData) > 0) ? "" :"disabled";    
                
                    @endphp
                    <a href="{{ route('outsource.order.index') }}" class="btn btn-primary"> 一覧に戻る </a>
                    <button  onclick="bulkSavingData(this)"  class="btn btn-success btn-bulk-saving {{  $class }}" {{  $attr }}> この内容で登録する </button>
                </div>
            </div>
        </div>
    </div>
    @include('partials.modals.masters._search', [
        'modalId' => 'searchProductNumberModal',
        'searchLabel' => '材料',
        'resultValueElementId' => 'product_code',
        'resultNameElementId' => 'product_name',
        'model' => 'ProductMaterial'
    ])

    @include('partials.modals.masters._search', [
        'modalId' => 'searchProcessModal',
        'searchLabel' => '仕入先',
        'resultValueElementId' => 'process_code',
        'resultNameElementId' => 'supplier_name',
        'model' => 'Process',
        'query'=> "searchProductModal",
        'reference' => "process_code"
    ])
@endsection
@push('scripts')
    <script>
        window.sessionData = @json(session('sessionData', []));
    </script>
    @vite('resources/js/outsource/fraction/create.js');
@endpush
