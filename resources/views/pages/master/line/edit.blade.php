@extends('layouts.app')

@push('styles')
    @vite('resources/css/index.css')
    @vite('resources/css/search-modal.css')
@endpush

@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="pageHeaderBox rounded">
                ラインマスタ登録・編集
            </div>

            @if(session('success'))
                <div id="card" style="background-color: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);margin-top: 20px;">
                    <div style="text-align: left;">
                        <p style="font-size: 18px; color: #0d9c38">
                            {{ session('success') }}
                        </p>
                    </div>
                </div>
            @endif

            <div class="section">
                <form action="{{ route('master.masterLine.store') }}" id="lineMasterForm" class="overlayedSubmitForm with-js-validation" method="POST" 
                    data-confirmation-message="ラインマスタ情報を更新します、よろしいでしょうか？"
                    accept-charset="utf-8">
                    @csrf                    
                    <input type="hidden" name="updator" value="{{ request()->user()->employee_code }}">
                    <input type="hidden" name="updated_at" value="{{ now()->format('Y-m-d H:i:s') }}">
                    <input type="hidden" id="line_id" name="id" value="{{ $line?->id }}">
                    <table class="tableBasic tableWrap bordered mb-3">
                        <tbody>
                            <tr>
                                <td class="w-15 mr-4">
                                    <dl class="formsetBox">
                                        <dt class="requiredForm pl-1" style="width: auto;">ラインコード</dt>
                                    </dl>
                                </td>
                                <td>
                                    <dd>
                                        <div class="px-1 ml-1 mt-2 mb-2">
                                            <input type="text" name="line_code" class="w-10" value="{{ $line?->line_code }}" 
                                                required
                                                data-field-name="ラインコード"
                                                data-error-messsage-container="#line_code_error">
                                        </div>
                                        <div id="line_code_error"></div>
                                    </dd>
                                </td>
                            </tr>
                            <tr>
                                <td class="w-15 mr-4">
                                    <dl class="formsetBox">
                                        <dt class="requiredForm pl-1" style="width: auto;">ライン名</dt>
                                    </dl>
                                </td>
                                <td>
                                    <dd>
                                        <div class="px-1 ml-1 mt-2 mb-2">
                                            <input type="text" name="line_name" class="w-20" value="{{ $line?->line_name }}" 
                                                required
                                                data-field-name="ライン名"
                                                data-error-messsage-container="#line_name_error">
                                           
                                        </div>
                                        <div id="line_name_error"></div>
                                    </dd>
                                </td>
                            </tr>
                            <tr>
                                <td class="w-15 mr-4">
                                    <dl class="formsetBox">
                                        <dt class="requiredForm pl-1" style="width: auto;">ライン名略</dt>
                                    </dl>
                                </td>
                                <td>
                                    <dd>
                                        <div class="px-1 ml-1 mt-2 mb-2">
                                            <input type="text" 
                                                name="line_name_abbreviation" 
                                                class="w-20" value="{{ $line?->line_name_abbreviation }}" required
                                                data-field-name="ライン名略"
                                                data-error-messsage-container="#line_name_abbreviation_error">
                                            <div class="err_msg w-100 ml-1"></div>
                                        </div>
                                        <div id="line_name_abbreviation_error"></div>
                                    </dd>
                                </td>
                            </tr>
                            <tr>
                                <td class="w-15 mr-4">
                                    <dl class="formsetBox">
                                        <dt class="pl-1">部門コード</dt>
                                    </dl>
                                </td>
                                <td>
                                    <dd>
                                        <div class="d-flex">
                                            <input type="text" name="department_code"
                                                id="department_code" style="margin-right: 10px; width: 100px; ime-mode: disabled"
                                                data-field-name="部門コード"
                                                data-error-messsage-container="#department_code_error"
                                                data-validate-exist-model="Department"
                                                data-validate-exist-column="code"
                                                data-inputautosearch-model="Department"
                                                data-inputautosearch-column="code"
                                                data-inputautosearch-return="name"
                                                data-inputautosearch-reference="department_name"
                                                class="text-left acceptNumericOnly"
                                                minlength="6"
                                                maxlength="6"
                                                onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                                value="{{ $line?->department_code ?? ''}}" 
                                                >
                                            <input type="text" readonly
                                                name="department_name"
                                                id="department_name" style="margin-right: 10px; width: 290px;"
                                                value="{{ $line?->department_name ?? ''}}"
                                                class="middle-name text-left">
                                            <button type="button" class="btnSubmitCustom js-modal-open"
                                                    data-target="searchDepartmentModal">
                                                <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                    alt="magnifying_glass.svg">
                                            </button>
                                        </div>
                                        <div id="department_code_error"></div>
                                        
                                    </dd>
                                </td>
                            </tr>
                            
                            <tr>
                                <td class="w-15 mr-4">
                                    <dl class="formsetBox">
                                        <dt class="pl-1">無効にする</dt>
                                    </dl>
                                </td>
                                <td>
                                    <div class="px-1 ml-1 mt-2 mb-2">
                                        <input type="checkbox" name="delete_flag" value="1" {{ $line?->delete_flag == 1 ? 'checked' : '' }}>
                                    </div>
                                    <div class="error_msg"></div>
                                </td>
                            </tr>

                        </tbody>
                    </table>

                    <div class="d-flex">
                        <button type="button" id="delete_line" class="btn btn-orange w-10">削除</button>
                        <div class="btnListContainer m-0">
                            <a href="{{ route('master.masterLine.copy') }}" class="btn btn-blue w-10 mr-2">複写入力</a>
                            <button type="submit" class="btn btn-success w-10">登録する</button>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>
    @include('partials.modals.masters._search', [
        'modalId' => 'searchDepartmentModal',
        'searchLabel' => '部門',
        'resultValueElementId' => 'department_code',
        'resultNameElementId' => 'department_name',
        'model' => 'Department'
    ])
@endsection
@push('scripts')
    @vite(['resources/js/master/line/edit.js'])
@endpush