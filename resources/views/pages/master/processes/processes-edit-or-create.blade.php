@extends('layouts.app')

@push('styles')
    @vite('resources/css/index.css')
    @vite('resources/css/materials/received_materials_list.css')
    @vite('resources/css/master/index.css')

    @vite('resources/css/master/index.css')

    @vite('resources/css/master/product.css')
    @vite('resources/css/search-modal.css')
@endpush

@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="accordion">
                <h1><span>取引先マスタ登録・編集</span></h1>
            </div>

            <form action="{{ $process->id ? route('master.process.update', ['id' => $process->id]) : route('master.process.store') }}" class="overlayedSubmitForm" id="processMasterForm" method="POST" accept-charset="utf-8">
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
                    <table class="tableBasic" style="width: 100%">
                        <tbody>
                            {{-- process_code --}}
                            <tr>
                                <td class="fw-15">
                                    <dl class="formsetBox" style="margin-bottom: -10px; margin-left: -15px;">
                                        <dt class="requiredForm">工程コード</dt>
                                    </dl>
                                </td>
                                <td>
                                    <dd>
                                        <div class="d-flex">
                                            <input type="text" name="process_code" value="{{ old('process_code', $process->process_code) }}" style="min-width: 300px" required>
                                            <div class="err_msg w-100 ml-1"></div>
                                        </div>
                                    </dd>
                                </td>
                            </tr>
                            
                            {{-- process_name --}}
                            <tr>
                                <td class="fw-15">
                                    <dl class="formsetBox" style="margin-bottom: -10px; margin-left: -15px;">
                                        <dt class="requiredForm">工程名</dt>
                                    <dl class="formsetBox">
                                </td>
                                <td>
                                    <dd>
                                        <div class="d-flex">
                                            <input type="text" name="process_name" value="{{ old('process_name', $process->process_name) }}" style="min-width: 300px" required>
                                            <div class="err_msg w-100 ml-1"></div>
                                        </div>
                                    </dd>
                                </td>
                            </tr>

                            {{-- abbreviation_process_name --}}
                            <tr>
                                <td class="fw-15">
                                    <dl class="formsetBox" style="margin-bottom: -10px; margin-left: -15px;">
                                        <dt class="requiredForm">工程略名</dt>
                                    <dl class="formsetBox">
                                </td>
                                <td>
                                    <dd>
                                        <div class="d-flex">
                                            <input type="text" name="abbreviation_process_name" value="{{ old('abbreviation_process_name', $process->abbreviation_process_name) }}" style="min-width: 300px" required>
                                            <div class="err_msg w-100 ml-1"></div>
                                        </div>
                                    </dd>
                                </td>
                            </tr>

                            {{-- inside_and_outside_division --}}
                            <tr>
                                <td class="fw-15">
                                    <dl class="formsetBox" style="margin-bottom: -10px; margin-left: -15px;">
                                        <dt class="requiredForm">内外区分</dt>
                                    </dl>
                                    
                                </td>
                                <td>
                                    <div style="display: inline-flex; width: 100%;">
                                        <input type="radio" id="inside_and_outside_division1" style="min-width:17px; margin-left: 0px; margin-top:-2px" name="inside_and_outside_division" value="1" {{ (old('inside_and_outside_division') === '1' || $process->inside_and_outside_division === '1') ? 'checked' : '' }}>
                                        <label for="inside_and_outside_division1" style="min-width: 100px; text-align: left">社内</label>
                                    
                                        <input type="radio" id="inside_and_outside_division2" name="inside_and_outside_division" style="min-width:17px; margin-left: 0px; margin-top:-2px" value="2" {{ (old('inside_and_outside_division') === '2' || $process->inside_and_outside_division === '2') ? 'checked' : '' }}>
                                        <label for="inside_and_outside_division2" style="min-width: 100px; text-align: left">外注</label>
                                    </div>
                                    <div class="error_msg"></div>
                                </td>
                            </tr>

                            {{-- customer_code --}}
                            <tr>
                                <td class="fw-15">
                                    <dl class="formsetBox" style="margin-bottom: -10px; margin-left: -15px;"><dt>取引先コード</dt>
                                </td>
                                <td>
                                    <div class="formPack" style="display: flex; align-items: center;">
                                        <input type="text" id="customer_code" name="customer_code" value="{{ old('customer_code', $process->customer_code) }}" class="" style="max-width: 150px">
                                        <input type="text" readonly
                                            id="customer_name"
                                            value=""
                                            class="middle-name"
                                            style="max-width: 250px; margin-left: 10px;">
                                        
                                            <button type="button" class="btnSubmitCustom js-modal-open" style="margin-left: 10px;"
                                                            data-target="searchCustomerModal">
                                                <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                        alt="magnifying_glass.svg">
                                            </button>
                                    </div>
                                    @error('customer_code')
                                        <span class="err_msg">{{ $message }}</span>
                                    @enderror
                                </td>
                                
                            </tr>

                            {{-- backorder_days --}}
                            <tr>
                                <td class="fw-15">
                                    <dl class="formsetBox" style="margin-bottom: -10px; margin-left: -15px;">
                                        <dt class="">入荷待ち日数</dt>
                                    </dl>
                                </td>
                                <td>
                                    <div style="display: inline-flex; float:left;">
                                        <input type="text" id="backorder_days" name="backorder_days" style="max-width: 130px;" value="{{ old('backorder_days', $process->backorder_days) }}" required>
                                        <label for="backorder_days" style="line-height: 30px; margin-left: 10px; font-size: 20px">材料メーカー時のみ入力</label>
                                    </div>
                                    @error('backorder_days')
                                        <span class="error_msg">{{ $message }}</span>
                                    @enderror
                                </td>
                            </tr>


                            {{-- delete_flag --}}
                            @if (!empty($process->id))
                            <tr>
                                <td class="fw-15">
                                    <dt>無効にする</dt>
                                </td>
                                <td>
                                    <dd>
                                        <p class="formPack">
                                            <input style="margin-left: 0px" type="checkbox" name="delete_flag" value="1" {{ old('delete_flag', $process->delete_flag) ? 'checked' : '' }}>&nbsp;
                                        </p>
                                        <div class="error_msg"></div>
                                    </dd>
                                </td>
                            </tr>
                            @endif
                            
                        </tbody>
                    </table>

                    <input type="hidden" id="process_id" value="{{ isset($process) ? $process->id : '' }}">
                    <div class="buttonRow" style="display: flex; justify-content: space-between;">
                        <div>
                            <button type="button" id="delete_process" class="btn-wide btn btn-orange {{ !isset($process->id) ? 'isNew' : '' }}">
                                削　除
                            </button>
                        </div>
                        <div style="display: flex; justify-content: flex-end;">
                            <!-- Hidden field to store session data as JSON -->
                            <input type="hidden" id="session-data" value="{{ json_encode($formData ?? []) }}">
                            @if (session()->has('process_data'))
                                <div style="margin-right: 10px">
                                    <button type="button" id="btn-copy-process" class="btn btn-blue" style="display: {{ isset($process->id) ? 'none' : '' }}">
                                        複写入力
                                    </button>
                                </div>
                            @endif
                            <div>
                                {{-- <button type="submit" class="button-product btn-save" style="width: 180px">
                                    {{ $process->id ? 'アップデート' : '登録する' }}
                                </button> --}}
                                <button type="submit" class="btn btn-success btn-wide">この内容で登録する</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>
    @include('partials.modals.masters._search', [
        'modalId' => 'searchCustomerModal',
        'searchLabel' => '得意先',
        'resultValueElementId' => 'customer_code',
        'resultNameElementId' => 'customer_name',
        'model' => 'Customer'
    ])
@endsection
@push('scripts')
    @vite(['resources/js/master/process/data-form.js'])
@endpush