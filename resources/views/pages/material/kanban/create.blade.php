@extends('layouts.app')

@push('styles')
    @vite('resources/css/index.css')
    {{-- @vite('resources/css/materials/supply_material_kanban_form.css') --}}
@endpush

@section('title', 'かんばん入力')

@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="pageHeaderBox rounded">
                かんばん入力
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
                    <h1 class="form-label bar indented">かんばん入力</h1>
                    <form method="GET" class="inputFormArea overlayedSubmitForm with-js-validation" id="barcodeForm" >
                        <div class="box mb-3">
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
                            <br/>
                            <div class="mb-2 mt-2">
                                <label class="form-label dotted indented">バーコード情報</label> <span
                                    class="btn-orange badge">必須</span>
                                @php
                                    $selectedManagementNos = request()->get('management_no', []);
                                    
                                @endphp
                                <div>
                                    <select class="customScrollbarSelect" name="management_no[]" style="width:300px; font-size: 24px" multiple required
                                    data-error-messsage-container="#management_no"
                                    data-field-name="バーコード情報">
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
            
            

            <form action="{{ route('material.kanbanCreate.store') }}" method="POST" id="barcodeDataForm" 
                class="overlayedSubmitForm with-js-validations">
                @csrf
                <div class="section">
                    <h1 class="form-label bar indented">バーコード情報結果</h1>
                    <div class="box">
                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>管理No.</th>
                                <th>材料品番</th>
                                <th>品名</th>
                                <th>材料メーカー</th>
                                <th>背番号</th>
                                <th>サイクル</th>
                                <th>指示日</th>
                                <th width="3%">便</th>
                                <th>収容数</th>
                                <th>枚数</th>
                                <th>数量</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                                @forelse ($kanbanMasters as $index => $kanbanMaster)
                                <input type="hidden" name="supply_material_order_no[]" value="{{ $supplyMaterialOrderNo +  $index}}">
                                <input type="hidden" name="management_no[]" value="{{ $kanbanMaster->management_no }}">
                                <input type="hidden" name="material_number[]" value="{{ $kanbanMaster->part_number }}">
                                <input type="hidden" name="order_classification[]" value="1">
                                <input type="hidden" name="supplier_code[]" value="{{ $kanbanMaster->supplier_code }}">
                                <input type="hidden" name="material_manufacturer_code[]" value="{{ $kanbanMaster->material_manufacturer_code }}">
                                <input type="hidden" name="instruction_kanban_quantity[]" value="{{ $kanbanMaster->number_of_cycles }}">
                                <input type="hidden" name="instruction_number[]" value="{{ $kanbanMaster->number_of_accomodated * $kanbanMaster->number_of_cycles }}">
                                <input type="hidden" name="creator[]" value="{{ request()->user()->id }}">
                                <input type="hidden" name="arrival_quantity[]" value="0">
                                <input type="hidden" name="created_at[]" value="{{ now() }}">
                                    <tr data-row="{{ $index }}">
                                        <td>{{ $kanbanMaster->management_no }}</td>
                                        <td>{{ $kanbanMaster->edited_part_number }}</td>
                                        <td>{{ $kanbanMaster->product?->product_name }}</td>
                                        <td>{{ $kanbanMaster->product?->supplier_code }}</td>
                                        <td>{{ $kanbanMaster->printed_jersey_number }}</td>
                                        <td>{{ $kanbanMaster->cycle_day . '-' . $kanbanMaster->number_of_cycles . '-' . $kanbanMaster->cycle_interval }}</td>
                                        <td width="16%">
                                            <div style="display: flex; justify-content: center;">

                                                @php   $newDate = now()->format('Ymd'); @endphp
                                                <input type="text" name="instruction_date[]" style="text-align: center"
                                                    class="@if (session('error')) input-error @endif" 
                                                    id="instruction_date_{{ $index }}"
                                                    data-format="YYYYMMDD"
                                                    data-validate-date-format="YYYYMMDD"
                                                    minlength="8"
                                                    maxlength="8"
                                                    pattern="\d*"
                                                    soninput="addDateValidationToRules($('#barcodeDataForm'))"
                                                    value="{{ $newDate }}" 
                                                    data-input-required>
                                                <button type="button" class="btnSubmitCustom buttonPickerJS ml-1"
                                                        data-target="instruction_date_{{ $index }}"
                                                        data-format="YYYYMMDD">
                                                    <img src="{{ asset('images/icons/iconsvg_calendar_w.svg') }}" alt="iconsvg_calendar_w.svg">
                                                </button>
                                               	

                                            </div>
                                            @error('instruction_date.' . $index)
                                                <div class="error_msg text-danger">{{ $message }}</div>
                                            @enderror
                                          
                                        </td>
                                        <td width="2%">
                                            <input type="text" name="instruction_no[]" id="instruction_no_{{ $index }}"
                                                style="width: 80px !important; text-align: center"
                                                minlength="1"
                                                maxlength="2"
                                                onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                                class="@if (session('error'))input-error @endif acceptNumericOnly"
                                                data-accept-zero=true   
                                                value="<?= $index + 1 ?>" 
                                                data-input-required>
                                                @error('instruction_no.' . $index)
                                                    <div class="error_msg text-danger">{{ $message }}</div>
                                                @enderror
                                        </td>
                                        <td class="valign-middle text-right"></td>
                                        <td class="valign-middle text-right">{{ $kanbanMaster->number_of_cycles }}</td>
                                        <td class="valign-middle text-right">
                                            {{ $kanbanMaster->number_of_accomodated * $kanbanMaster->number_of_cycles }}
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-block btn-orange delete-row"
                                            data-management-no="{{$kanbanMaster->id}}" data-delete-button>削　除</button>
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
                    <div class="text-red" id="warningInputs">
                        @if (session('error'))
                            {{ session('error') }}
                        @endif
                    </div>
                    <div>
                        {{-- <a href="{{ route('material.supplyMaterialKanbanForm') }}" class="btn btn-blue" style="width: 15rem"> 一覧に戻る </a> --}}
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
@endsection
@push('scripts')
@vite(['resources/js/material/kanban/create.js'])
@endpush