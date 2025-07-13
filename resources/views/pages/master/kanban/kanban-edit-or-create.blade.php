@extends('layouts.app')

@push('styles')
@vite('resources/css/app.css')    

@vite('resources/css/estimates/index.css')

@vite('resources/css/index.css')
@vite('resources/css/materials/received_materials_list.css')
@vite('resources/css/master/index.css')

@vite('resources/css/master/product.css')
@vite('resources/css/search-modal.css')


<style>
    input[type="radio"]:disabled {
        /* Set the desired styles for disabled radio buttons */
        opacity: 0.5; /* Reduce opacity to indicate disabled state */
        pointer-events: none; /* Prevent interactions with disabled elements */
        height: 20px;
    }
    /* Container styles */
    .pagination-container {
    display: flex;
    justify-content: center;
    margin-top: 20px; /* Add margin as needed */
    }

    /* Pagination styles */
    .paginationLinks {
    list-style-type: none;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    }

    .paginationLink {
    margin: 0 5px;
    padding: 8px 12px;
    border: 1px solid #ccc;
    text-decoration: none;
    color: #333;
    cursor: pointer;
    background-color: #fff;
    }

    .paginationLink.active {
    background-color: #007bff;
    color: #fff;
    border-color: #007bff;
    }

    .paginationLink:hover {
    background-color: #f5f5f5;
    }



</style>
@endpush

@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="accordion">
                <h1><span>ラインマスタ登録・編集</span></h1>
            </div>

            <form action="{{ $kanban->id ? route('master.kanban.update', ['id' => $kanban->id]) : route('master.kanban.store') }}" id="kanbanMasterForm" class="overlayedSubmitForm" method="POST" accept-charset="utf-8">
                @csrf
                <div class="tableWrap borderLesstable inputFormAreaCustomer">
                    @if (session('success'))
                        <div id="card" style="background-color: #f0f0f0; padding: 20px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
                            <div style="text-align: center;">
                                <p style="font-size: 18px; color: #0d9c38; margin-bottom: 10px;">
                                    {{ session('success') }}
                                </p>
                            </div>
                        </div>
                        @php
                            session()->forget('success');
                        @endphp
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif
                    <table class="tableBasic" style="width: 100%;">
                        <tbody>
                            {{-- management_no --}}
                            <tr>
                                <td class="fw-15">
                                    <dl class="formsetBox" style="margin-bottom: -10px; margin-left: -15px;">
                                        <dt class="requiredForm">管理No.</dt>
                                    </dl>
                                </td>
                                <td>
                                    <dd>
                                        <div class="d-flex">
                                            <input type="text" name="management_no" value="{{ old('management_no', $kanban->management_no) }}" style="max-width: 100px" required>
                                            <div class="err_msg w-100 ml-1"></div>
                                        </div>
                                    </dd>
                                </td>
                            </tr>
                            
                            {{-- kanban_classification --}}
                            <tr>
                                <td class="fw-15">
                                    <dl class="formsetBox" style="margin-bottom: -10px; margin-left: -15px;">
                                        <dt class="requiredForm">かんばん区分</dt>
                                    <dl class="formsetBox">
                                </td>
                                <td style="text-align:left">
                                    <div style="display: inline-flex; width: 100%;">
                                        <input type="radio" id="kanban_classification1" style="min-width:17px; margin-left: 0px; margin-top:-2px" name="kanban_classification" required value="1" {{ (old('kanban_classification') === '1' || $kanban->kanban_classification === '1') ? 'checked' : '' }}>
                                        <label for="kanban_classification1" style="min-width: 100px; text-align: left">支給材</label>
                                    
                                        <input type="radio" id="kanban_classification2" name="kanban_classification" style="min-width:17px; margin-left: 0px; margin-top:-2px" value="2" {{ (old('kanban_classification') === '2' || $kanban->kanban_classification === '2') ? 'checked' : '' }}>
                                        <label for="kanban_classification2" style="min-width: 100px; text-align: left">外注加工</label>

                                        <input type="radio" id="kanban_classification3" name="kanban_classification" value="3" style="min-width:17px; margin-left: 0px; margin-top:-2px" {{ (old('kanban_classification') === '3' || $kanban->kanban_classification === '3') ? 'checked' : '' }}>
                                        <label for="kanban_classification3" style="min-width: 100px; text-align: left">外注支給</label>

                                        <input type="radio" id="kanban_classification4" name="kanban_classification" value="4" style="min-width:17px; margin-left: 0px; margin-top:-2px" {{ (old('kanban_classification') === '4' || $kanban->kanban_classification === '4') ? 'checked' : '' }}>
                                        <label for="kanban_classification4" style="min-width: 100px; text-align: left">社内</label>
                                    </div>
                                    <div class="err_msg w-100 ml-1"></div>
                                </td>
                            </tr>

                            {{-- part_number --}}
                            <tr>
                                <td class="fw-15">
                                    <dl class="formsetBox" style="margin-bottom: -10px; margin-left: -15px;">
                                        <dt class="requiredForm">品番</dt>
                                    <dl class="formsetBox">
                                </td>
                                <td style="text-align:left">
                                    <dd>
                                        <div class="formPack" style="display: flex; align-items: center;">
                                            <input type="text" id="part_number" name="part_number" required value="{{ old('part_number', $kanban->part_number) }}" style="max-width: 250px">
                                            <input type="text" readonly
                                                id="product_name"
                                                name="product_name"
                                                value=""
                                                style="max-width: 250px; margin-left: 10px;">
                                            
                                            <button type="button" class="btnSubmitCustom js-modal-open" style="margin-left: 10px;"
                                                            data-target="searchPartNumberModal">
                                                <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                        alt="magnifying_glass.svg">
                                            </button>
                                            <div class="err_msg w-100 ml-1"></div>
                                        </div>
                                    </dd>
                                </td>
                            </tr>

                            {{-- process_code --}}
                            <tr>
                                <td class="fw-15">
                                    <dl class="formsetBox" style="margin-bottom: -10px; margin-left: -15px;">
                                        <dt class="requiredForm">工程コード</dt>
                                    <dl class="formsetBox">
                                </td>
                                <td style="text-align:left">
                                    <dd>
                                        <div class="formPack" style="display: flex; align-items: center;">
                                            <input type="text" id="process_code" name="process_code" required value="{{ old('process_code', $kanban->process_code) }}" class="" style="max-width: 250px">
                                            <input type="text" readonly
                                                id="process_name"
                                                name="process_name"
                                                value=""
                                                class="middle-name"
                                                style="max-width: 250px; margin-left: 10px;">
                                            
                                                <button type="button" class="btnSubmitCustom js-modal-open" style="margin-left: 10px;"
                                                                data-target="searchProcessModal">
                                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                            alt="magnifying_glass.svg">
                                                </button>
                                                <div class="err_msg w-100 ml-1"></div>
                                        </div>
                                    </dd>
                                </td>
                            </tr>

                            {{-- customer_acceptance --}}
                            <tr>
                                <td class="fw-15">
                                    <dt>得意先受入</dt>
                                </td>
                                <td style="text-align:left">
                                    <dd>
                                        <p class="formPack">
                                            <input type="text" name="customer_acceptance" value="{{ old('customer_acceptance', $kanban->customer_acceptance) }}" style="max-width: 150px">
                                        </p>
                                        @error('customer_acceptance')
                                            <span class="err_msg">{{ $message }}</span>
                                        @enderror
                                    </dd>
                                </td>
                            </tr>

                            {{-- next_process_code --}}
                            <tr>
                                <td class="fw-15">
                                    <dl class="formsetBox" style="margin-bottom: -10px; margin-left: -15px;">
                                        <dt class="">次工程コード</dt>
                                    <dl class="formsetBox">
                                </td>
                                <td style="text-align:left">
                                    <dd>
                                        <div class="formPack" style="display: flex; align-items: center;">
                                            <input type="text" id="next_process_code" name="next_process_code" value="{{ old('next_process_code', $kanban->next_process_code) }}" class="" style="max-width: 150px">
                                            <input type="text" readonly
                                                id="next_process_name"
                                                name="next_process_name"
                                                value=""
                                                class="middle-name"
                                                style="max-width: 250px; margin-left: 10px;">
                                            
                                                <button type="button" class="btnSubmitCustom js-modal-open" style="margin-left: 10px;"
                                                                data-target="searchNextProcessModal">
                                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                            alt="magnifying_glass.svg">
                                                </button>
                                        </div>
                                        @error('next_process_code')
                                            <span class="err_msg">{{ $message }}</span>
                                        @enderror
                                    </dd>
                                </td>
                            </tr>

                            {{-- cycle_day --}}
                            {{-- number_of_cycles --}}
                            {{-- cycle_interval --}}
                            <tr>
                                <td class="fw-15">
                                    <dt>サイクル</dt>
                                </td>
                                <td>
                                    <div style="display: inline-flex; width: 100%; text-align:left; line-height: -10px">
                                        {{-- cycle_day --}}
                                        <label for="cycle_day" style="display: inline-block; min-width: 100px; width: 100px;">サイクル日：</label>
                                        <input type="text" id="cycle_day" name="cycle_day" style="max-width: 100px; min-width:100px; margin-left: 0px; height: 38px" value="{{ old('cycle_day', $kanban->cycle_day) }}">
                                        
                                        {{-- number_of_cycles --}}
                                        <label for="number_of_cycles" style="display: inline-block; min-width: 100px; min-width: 120px; margin-left: 10px;">サイクル回数：</label>
                                        <input type="text" id="number_of_cycles" name="number_of_cycles" style="max-width: 100px; min-width: 100px; margin-left: 0px; height: 38px" value="{{ old('number_of_cycles', $kanban->number_of_cycles) }}">
                                        
                                        {{-- cycle_interval --}}
                                        <label for="cycle_interval" style="display: inline-block; min-width: 100px; min-width: 85px; margin-left: 10px;">口座番号：</label>
                                        <input type="text" id="cycle_interval" name="cycle_interval" style="min-width: 100px; max-width: 100px; margin-left: 0px; height: 38px" value="{{ old('cycle_interval', $kanban->cycle_interval) }}">
                                    </div>
                                    <div class="error_msg"></div>
                                </td>
                            </tr>

                            {{-- number_of_accomodated --}}
                            <tr>
                                <td class="fw-15">
                                    <dl class="formsetBox" style="margin-bottom: -10px; margin-left: -15px;">
                                        <dt class="requiredForm">収容数</dt>
                                    </dl>
                                </td>
                                <td style="text-align:left">
                                    <dd>
                                        <div class="d-flex">
                                            <input type="text" name="number_of_accomodated"  value="{{ old('number_of_accomodated', $kanban->number_of_accomodated) }}" style="width: 100px" required>
                                            <div class="err_msg w-100 ml-1"></div>
                                        </div>
                                    </dd>
                                </td>
                            </tr>

                            {{-- box_type --}}
                            <tr>
                                <td class="fw-15">
                                    <dl class="formsetBox" style="margin-bottom: -10px; margin-left: -15px;">
                                        <dt class="">箱種</dt>
                                    </dl>
                                </td>
                                <td style="text-align:left">
                                    <dd>
                                        <p class="formPack">
                                            <input type="text" name="box_type" value="{{ old('box_type', $kanban->box_type) }}" style="max-width: 100px" required>
                                        </p>
                                        @error('box_type')
                                            <span class="err_msg">{{ $message }}</span>
                                        @enderror
                                    </dd>
                                </td>
                            </tr>

                            {{-- acceptance --}}
                            <tr>
                                <td class="fw-15">
                                    <dl class="formsetBox" style="margin-bottom: -10px; margin-left: -15px;">
                                        <dt class="">受入/返却先</dt>
                                    </dl>
                                </td>
                                <td style="text-align:left">
                                    <dd>
                                        <p class="formPack">
                                            <input type="text" name="acceptance" value="{{ old('acceptance', $kanban->acceptance) }}" style="max-width: 100px" required>
                                        </p>
                                        @error('acceptance')
                                            <span class="err_msg">{{ $message }}</span>
                                        @enderror
                                    </dd>
                                </td>
                            </tr>

                            {{-- printed_jersey_number --}}
                            <tr>
                                <td class="fw-15">
                                    <dl class="formsetBox" style="margin-bottom: -10px; margin-left: -15px;">
                                        <dt class="">印刷背番号</dt>
                                    </dl>
                                </td>
                                <td style="text-align:left">
                                    <dd>
                                        <p class="formPack">
                                            <input type="text" name="printed_jersey_number" value="{{ old('printed_jersey_number', $kanban->printed_jersey_number) }}" style="max-width: 100px" required>
                                        </p>
                                        @error('printed_jersey_number')
                                            <span class="err_msg">{{ $message }}</span>
                                        @enderror
                                    </dd>
                                </td>
                            </tr>

                            {{-- remark_1 --}}
                            <tr>
                                <td class="fw-15">
                                    <dl class="formsetBox" style="margin-bottom: -10px; margin-left: -15px;">
                                        <dt class="">備考1</dt>
                                    </dl>
                                </td>
                                <td>
                                    <dd>
                                        <div class="formPack" style="display: flex; align-items: center;">
                                            <textarea rows="5" cols="50" type="text" id="remark_1" name="remark_1">{{ old('remark_1', $kanban->remark_1)  }}</textarea>
                                            <button 
                                                class="btn btn-primary ml-2"
                                                style="
                                                        align-self: flex-start; 
                                                        display: flex;
                                                        justify-content: center;"
                                                type="button" 
                                                class="js-modal-open"
                                                data-target="remark_1_modal"
                                                id="show_remark_1_modal">製品品番参照</button>
                                        </div>
                                        @error('remark_1')
                                            <span class="err_msg">{{ $message }}</span>
                                        @enderror
                                    </dd>
                                </td>
                            </tr>

                            {{-- remark_2 --}}
                            <tr>
                                <td class="fw-15">
                                    <dl class="formsetBox" style="margin-bottom: -10px; margin-left: -15px;">
                                        <dt class="">備考2</dt>
                                    </dl>
                                </td>
                                <td>
                                    <dd>
                                        <p class="formPack">
                                            <textarea rows="5" cols="50" type="text" id="remark_2" name="remark_2" 
                                                placeholder="">{{ old('remark_2', $kanban->remark_2)  }}</textarea>
                                        </p>
                                        @error('remark_2')
                                            <span class="err_msg">{{ $message }}</span>
                                        @enderror
                                    </dd>
                                </td>
                            </tr>

                            {{-- remark_qr_code --}}
                            <tr>
                                <td class="fw-15">
                                    <dl class="formsetBox" style="margin-bottom: -10px; margin-left: -15px;">
                                        <dt class="">QRコード面備考</dt>
                                    </dl>
                                </td>
                                <td>
                                    <dd>
                                        <p class="formPack">
                                            <textarea rows="5" cols="50" type="text" id="remark_qr_code" name="remark_qr_code"
                                                placeholder="">{{ old('remark_qr_code', $kanban->remark_qr_code)  }}</textarea>
                                        </p>
                                        @error('remark_qr_code')
                                            <span class="err_msg">{{ $message }}</span>
                                        @enderror
                                    </dd>
                                </td>
                            </tr>

                            {{-- issued_sequence_number --}}
                            <tr>
                                <td class="fw-15">
                                    <dl class="formsetBox" style="margin-bottom: -10px; margin-left: -15px;">
                                        <dt class="">発行済連番</dt>
                                    </dl>
                                </td>
                                <td style="text-align:left">
                                    <dd>
                                        <p class="formPack">
                                            <input type="text" name="issued_sequence_number" value="{{ old('issued_sequence_number', $kanban->issued_sequence_number) }}" style="max-width: 100px" required>
                                        </p>
                                        @error('issued_sequence_number')
                                            <span class="err_msg">{{ $message }}</span>
                                        @enderror
                                    </dd>
                                </td>
                            </tr>

                            {{-- paid_category --}}
                            {{-- Activated only when "Kanban Classification" is selected as "Subcontract Supply" --}}
                            <tr >
                                <td class="fw-15">
                                    <dl class="formsetBox" style="margin-bottom: -10px; margin-left: -15px;">
                                        <dt class="requiredForm">有償無償区分</dt>
                                    </dl>
                                </td>
                                <td style="text-align:left" class="paid-category-row">
                                    <div style="display: inline-flex; width: 100%;" >
                                        <input type="radio" id="paid_category1" style="min-width:30px; margin-left: 0px;" name="paid_category" value="1" {{ (old('paid_category') === '1' || $kanban->paid_category === '1') ? 'checked' : '' }}>
                                        <label for="paid_category1" style="min-width: 100px; text-align: left">無償</label>
                                    
                                        <input type="radio" id="paid_category2" name="paid_category" style="min-width:30px;" value="2" {{ (old('paid_category') === '2' || $kanban->paid_category === '2') ? 'checked' : '' }}>
                                        <label for="paid_category2" style="min-width: 100px; text-align: left">有償</label>
                                        <div class="err_msg w-100 ml-1"></div>
                                    </div>
                                </td>
                            </tr>

                            {{-- delete_flag --}}
                            @if (!empty($kanban->id))
                            <tr>
                                <td class="fw-15">
                                    <dt>無効にする</dt>
                                </td>
                                <td>
                                    <dd>
                                        <p class="formPack">
                                            <input style="margin-left: 0px" type="checkbox" name="delete_flag" value="1" {{ old('delete_flag', $kanban->delete_flag) ? 'checked' : '' }}>&nbsp;
                                        </p>
                                        <div class="error_msg"></div>
                                    </dd>
                                </td>
                            </tr>
                                
                            @endif
                        </tbody>
                    </table>
                    
                    <input type="hidden" id="kanban_id" value="{{ isset($kanban) ? $kanban->id : '' }}">
                    
                    <div class="buttonRow" style="display: flex; justify-content: space-between;">
                        <div>
                            <button type="button" id="delete_kanban" class="btn btn-orange btn-wide {{ !isset($kanban->id) ? 'isNew' : '' }}">
                                削　除
                            </button>
                        </div>
                        <div style="display: flex; justify-content: flex-end;">
                            <div style="margin-right: 10px">
                                <button
                                    class="btn btn-primary btn-wide" 
                                    type="button" 
                                    class="btnPrint js-modal-open" 
                                    data-target="print_modal"
                                    id="showPrintModal">
                                        かんばん印刷
                                </button>
                            </div>

                            @if (session()->has('kanban_data'))
                                <div style="margin-right: 10px">
                                    <button type="button" id="btn-copy-kanban" class="buttonCreate button-product btn-blue" style="display: {{ isset($kanban->id) ? 'none' : '' }}">
                                        複写入力
                                    </button>
                                </div>
                            @endif
                            
                            <div>
                                <button type="submit" class="btn btn-success btn-wide">
                                    この内容で登録する
                                </button>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </form>

        </div>
        @include('pages.master.kanban.remarks_1_modal')
        @include('pages.master.kanban.print_modal')
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    @include('partials.modals.masters._search', [
        'modalId' => 'searchPartNumberModal',
        'searchLabel' => '品番',
        'resultValueElementId' => 'part_number',
        'resultNameElementId' => 'product_name',
        'model' => 'ProductNumber'
    ])
    @include('partials.modals.masters._search', [
        'modalId' => 'searchProcessModal',
        'searchLabel' => '工程コード',
        'resultValueElementId' => 'process_code',
        'resultNameElementId' => 'process_name',
        'model' => 'Process'
    ])
    @include('partials.modals.masters._search', [
        'modalId' => 'searchNextProcessModal',
        'searchLabel' => '工程コード',
        'resultValueElementId' => 'next_process_code',
        'resultNameElementId' => 'next_process_name',
        'model' => 'Process'
    ])
@endsection
@push('scripts')
    @vite(['resources/js/master/kanban/data-form.js'])
@endpush

