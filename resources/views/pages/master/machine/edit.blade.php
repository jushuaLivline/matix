@extends('layouts.app')

@push('styles')
    @vite('resources/css/estimates/index.css')
    @vite('resources/css/common.css')
    @vite('resources/css/estimates/data_form.css')
    @vite('resources/css/master/product.css')
    @vite('resources/css/master/machine_number.css')
@endpush

@section('title', '機番マスタ登録・編集')

@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="accordion">
                <h1><span>機番マスタ登録・編集</span></h1>
            </div>
            @if(session('success'))
                <div id="card" style="background-color: #fff; margin-bottom:20px; padding: 20px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
                    <div style="text-align: left;">
                        <p style="font-size: 18px; color: #0d9c38;">
                            {{ session('success') }}
                        </p>
                    </div>
                </div>
            @endif
            @php
                $confirmationMessage = (isset($requestMethod) && $requestMethod == 'edit') ? '機番マスタを更新します、よろしいですか?' : '機番マスタを登録します、よろしいですか?';
                $action = (isset($requestMethod) && $requestMethod == 'edit') 
                    ? route("master.masterMachine.update", array_merge([$machineNumber], request()->query())) 
                    : route("master.masterMachine.store");

                $createdAt = isset($machineNumber) 
                        ? \Carbon\Carbon::parse($machineNumber->created_at)->format('Ymd') 
                        : '';
                $drawingDate = isset($machineNumber)
                        ? \Carbon\Carbon::parse($machineNumber->drawing_date)->format('Ymd') 
                        : '';
                $completionDate = isset($machineNumber)
                        ? \Carbon\Carbon::parse($machineNumber->completion_date)->format('Ymd') 
                        : '';
            @endphp
            <form action="{{ $action}}" 
                accept-charset="utf-8"
                method="POST" 
                id="createReqFrm" 
                enctype="multipart/form-data"
                data-mode="{{ isset($machineNumber) ? 'edit' : 'create' }}"
                data-id="{{ isset($machineNumber) ? $machineNumber->id : '' }}"
                data-confirmation-message="{{ $confirmationMessage ?? '' }}">

                @csrf
                @if (isset($requestMethod) && $requestMethod == 'edit')
                    @method('PUT')
                    <input type="hidden" name="updator" value="{{ Auth::user()->employee_code }}" >
                    <input type="hidden" name="updated_at" value="{{ now() }}" >
                @else
                <input type="hidden" name="creator" value="{{ Auth::user()->employee_code }}" >
                @endif
                <input type="hidden" id="machine_number_id" value="{{ isset($machineNumber) ? $machineNumber->id : '' }}">
                <div class="bg-white">
                    {{-- 機番（仕掛番号）--}}
                    {{-- sign + machine_number + branch_number --}}
                    <div class="row-field">
                        <div class="label-row">
                            <span class="input-label">
                                機番（仕掛番号）
                            </span>
                            <div class="required-label">
                                必須
                            </div>
                        </div>
                        <div class="input-row">
                            <div class="form-group-input">
                                <input type="text" class="bn-input text-center acceptNumericOnly" id="branch_number" name="branch_number" 
                                    value="{{ $machineNumber->branch_number ?? '' }}"
                                    maxlength="1">
                                
                                <input type="text" class="mn-input text-left acceptNumericOnly" id="machine_number" name="machine_number" 
                                    value="{{ $machineNumber->machine_number ?? '' }}"
                                    maxlength="6">
                                <input type="text" class="sign-input text-center acceptNumericOnly" id="sign" name="sign" 
                                    value="{{ $machineNumber->sign ?? '' }}"
                                    maxlength="2"
                                    >
                                
                            </div>
                            <div class="error_msg"></div>
                        </div>
                       
                    </div>
                    {{-- 機番名 --}}
                    {{-- machine_number_name --}}
                    <div class="row-field">
                        <div class="label-row">
                            <span class="input-label">
                                機械名
                            </span>
                            <div class="required-label">
                                必須
                            </div>
                        </div>
                        <div class="input-row">
                            <input type="text" class="row-input-long" id="machine_number_name" 
                                name="machine_number_name"
                                value="{{ $machineNumber->machine_number_name ?? '' }}">

                        
                            <div class="error_msg"></div>
                        </div>
                        
                    </div>
                    {{-- プロジェクトNo. --}}
                    {{-- project_number --}}
                    <div class="row-field">
                        <div class="label-row">
                            <span class="input-label">
                                プロジェクトNo.
                            </span>
                        </div>
                        <div class="input-row flex-gap-8">
                            <input type="text" 
                                    data-validate-exist-model="Project" 
                                    data-validate-exist-column="project_number"
                                    data-inputautosearch-model="Project" 
                                    data-inputautosearch-column="project_number"
                                    data-inputautosearch-return="project_name" 
                                    data-inputautosearch-reference="project_name"
                                    id="project_number" 
                                    name="project_number" 
                                    style="width: 105px;" 
                                    maxlength="8"
                                    value="{{ $machineNumber->project_number ?? '' }}">
                                <input type="text" readonly
                                        name="project_name"
                                        id="project_name"
                                        value="{{ $machineNumber?->project?->project_name ?? '' }}"
                                        class="middle-name"
                                        style="width: 145px;">
                            <button type="button" class="btnSubmitCustom js-modal-open"
                                data-target="searchProjectModal">
                                <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                    alt="magnifying_glass.svg">
                            </button>
                            <div class="error_msg"></div>
                        </div>

                       
                    </div>
                    {{-- ライン名 --}}
                    {{-- line_name --}}
                    <div class="row-field">
                        <div class="label-row">
                            <span class="input-label">
                                ライン名
                            </span>
                        </div>
                        <div class="input-row">
                            <input type="text" class="row-input-long" 
                                id="line_name" 
                                name="line_name" 
                                value="{{ $machineNumber->line_name ?? '' }}">
                            <div class="error_msg"></div>
                        </div>
                    </div>
                    {{-- 製品区分 --}}
                    {{-- machine_division --}}
                    <div class="row-field">
                        <div class="label-row">
                            <span class="input-label">
                                製品区分
                            </span>
                            <div class="required-label">
                                必須
                            </div>
                        </div>
                        <div class="input-row">
                            <select name="machine_division" id="machine_division" class="classic" style="width: 200px">
                                @foreach ($machineDivision as $key => $division)
                                    <option value="{{ $key }}" {{ isset($machineNumber) && $machineNumber->machine_division == $key ? 'selected' : '' }}>{{ $division }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="error_msg"></div>
                    </div>
                    {{-- 登録日 --}}
                    {{-- created_at --}}
                    <div class="row-field">
                        <div class="label-row">
                            <span class="input-label">
                                登録日
                            </span>
                        </div>
                        <div class="input-row form-calender-input">
                            @include('partials._date_picker', [
                                    'inputName' => 'created_at', 
                                    'attributes' => 'data-field-name=登録日 data-error-messsage-container=#created_at_error',
                                    'value' => $createdAt, 
                                    'required' => false, 
                                    'onInput' => false, 
                                    'inputClass' => 'w-150c'])
                                    <div class="error_msg"></div>
                        </div>
                    </div>
                    {{-- 出図日 --}}
                    {{-- ding_date --}}
                    <div class="row-field">
                        <div class="label-row">
                            <span class="input-label">
                                出図日
                            </span>
                        </div>
                        <div class="input-row form-calender-input">
                            @include('partials._date_picker', [
                                'inputName' => 'drawing_date', 
                                'attributes' => 'data-field-name=出図日 data-error-messsage-container=#drawing_date_error',
                                'value' => $drawingDate,
                                 'required' => false, 
                                 'onInput' => false, 
                                 'inputClass' => 'w-150c'])
                                 <div class="error_msg"></div>
                        </div>
                    </div>
                    {{-- 完成日 --}}
                    {{-- completion_date --}}
                    <div class="row-field">
                        <div class="label-row">
                            <span class="input-label">
                                完成日
                            </span>
                        </div>
                        <div class="input-row form-calender-input">
                            @include('partials._date_picker', [
                                    'inputName' => 'completion_date', 
                                    'attributes' => 'data-field-name=完成日 data-error-messsage-container=#completion_date_error',
                                    'value' => $completionDate, 
                                    'required' => false, 
                                    'onInput' => false, 
                                    'inputClass' => 'w-150c'])
                                    <div class="error_msg"></div>
                        </div>
                    </div>
                    {{-- 担当者 --}}
                    {{-- manager --}}
                    <div class="row-field">
                        <div class="label-row">
                            <span class="input-label">
                                担当者
                            </span>
                        </div>
                        <div class="input-row">
                            <input type="text" class="row-input-mid" id="manager" name="manager" value="{{ $machineNumber->manager ?? '' }}">
                            <div class="error_msg"></div>
                        </div>
                    </div>
                    {{-- 備考 --}}
                    {{-- remarks --}}
                    <div class="row-field {{ isset($machineNumber->id) ? '' : 'last-row-field' }}">
                        <div class="label-row">
                            <span class="input-label">
                                備考
                            </span>
                        </div>
                        <div class="input-row">
                            <input type="text" class="row-input-xl" id="remarks" name="remarks" value="{{ $machineNumber->remarks ?? '' }}">
                            <div class="error_msg"></div>
                        </div>
                    </div>

                    {{-- 無効にする --}}
                    {{-- instruction_class --}}
                    <div class="row-field last-row-field {{ isset($machineNumber->id) ? '' : 'dis-none' }}">
                        <div class="label-row">
                            <span class="input-label">
                                無効にする
                            </span>
                        </div>
                        <div class="input-row-checkbox flex-radio">
                            <label class="container-checkbox">
                            <input type="checkbox" name="delete_flag" id="delete_flag" value="1" 
                            {{ old('delete_flag', $machineNumber->delete_flag ?? 0) == 1 ? 'checked' : '' }}>
                                <span class="checkmark-checkbox"></span>
                            </label>
                            <div class="error_msg"></div>
                        </div>
                    </div>
                </div>
                <div class="mt-3 d-flex justify-content-between">
                        <button type="button" id="hard_delete_machine_number" class="btn btn-orange btn-wide {{ !isset($machineNumber) ? 'isNew' : '' }}">
                            削 除
                        </button>
                        <div>
                            <button type="button" id="btn-copy-mn" 
                                class="buttonCreate btn btn-blue {{ !empty($last_input) ? '' : 'isNew' }}">
                                複写入力
                            </button>
                            <button type="submit" class="btn btn-success btn-register btn-wide">
                                @if (!isset($machineNumber))
                                    登録する
                                @else
                                更新
                                @endif
                            </button>
                        </div>
                        
                </div>
            </form>

            <form action="{{ $action}}" 
                accept-charset="utf-8"
                method="POST" 
                id="deleteReqFrm" 
                enctype="multipart/form-data"
                data-confirmation-message="{{ $confirmationMessage ?? '' }}">

                @csrf
                @method('DELETE')
                <input type="hidden" name="id" value="{{ $machineNumber->id?? '' }}">
            </form>
        </div>
    </div>
    @include('partials.modals.masters._search', [
        'modalId' => 'searchProjectModal',
        'searchLabel' => 'プロジェクト',
        'resultValueElementId' => 'project_number',
        'resultNameElementId' => 'project_name',
        'model' => 'Project'
    ])
@endsection
@push('scripts')
    @vite(['resources/js/master/machine/edit.js'])
@endpush
