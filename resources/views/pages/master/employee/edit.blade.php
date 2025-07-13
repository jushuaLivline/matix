@extends('layouts.app')

@push('styles')
    @vite('resources/css/index.css')
    @vite('resources/css/materials/received_materials_list.css')
    @vite('resources/css/master/employee/edit.css')
    @vite('resources/css/search-modal.css')
@endpush

@section('title', '社員マスタ登録・編集')

@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="accordion">
                <h1><span>社員マスタ登録・編集</span></h1>
            </div>

            <form id='submit-employee-form' data-action='{{ isset($data) ? $data->id : 'store' }}' class='overlayedSubmitForm' accept-charset="utf-8">
                @csrf
                <div class="bg-white">
                    <div class="row">
                        <div class="col-2 label-div">
                            社員コード &nbsp;<span class="others-frame btn-orange badge">必須</span>
                        </div>
                        <div class="col-10">
                            <input type="text" name="employee_code" id="employee_code"
                                class="w-150px"
                                value = "{{ isset($data) ? $data->employee_code : Request::get('employee_code') }}"
                            >
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-2 label-div">
                            社員名 &nbsp;<span class="others-frame btn-orange badge">必須</span>
                        </div>
                        <div class="col-10">
                            <input type="text" name="employee_name" id="employee_name"
                                value = "{{ isset($data) ? $data->employee_name : Request::get('employee_name') }}"
                            >
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-2 label-div">
                            部門 &nbsp;<span class="others-frame btn-orange badge">必須</span>
                        </div>
                        <div class="col-10">
                            <div class="d-flex p-0">
                                <input type="text" id="department_code" name="department_code"
                                    value="{{ isset($data) ? $data->department_code : Request::get('department_code') }}"
                                    class="fetchQueryName mr-2 w-150px acceptNumericOnly"
                                    data-model="Department"
                                    data-query="code"
                                    data-query-get="department_name"
                                    data-reference="department_name"
                                    maxlength="6"
                                    >
                                <input type="text" readonly
                                    id="department_name"
                                    name="department_name"
                                    value="{{ isset($data) ? $data->department?->department_name : Request::get('department_name') }}"
                                    class="middle-name mr-2"
                                    >
                                <button type="button" class="btnSubmitCustom js-modal-open"
                                        data-target="searchDepartmentModal">
                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                            alt="magnifying_glass.svg">
                                </button>
                            </div>
                            <div class="display-error"></div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-2 label-div">
                            パスワード &nbsp;<span class="others-frame btn-orange badge">必須</span>
                        </div>
                        <div class="col-10 ">
                            <div class="d-flex pl-0">
                                <div class="password-container">
                                    <input type="password" name="password" id="password"
                                        value = ""
                                        class="password-input"
                                    >
                                    <span class="toggle-password" id="toggle-password">
                                        <i class="fas fa-eye-slash"></i>
                                    </span>

                                    
                                </div>
                                <div id="password-strength">
                                    <div id="strength-bar" class="strength-bar"></div>
                                </div>
                            </div>
                            <p id="strength-message" class="strength-message"></p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-2 label-div">
                            メールアドレス &nbsp;<span class="others-frame btn-orange badge">必須</span>
                        </div>
                        <div class="col-10">
                            <input type="email" name="mail_address" id="mail_address"
                                value="{{ isset($data) ? $data->mail_address : Request::get('mail_address') }}"
                            >
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-2 label-div">
                            権限 &nbsp;<span class="others-frame btn-orange badge">必須</span>
                        </div>
                        <div class="col-10">
                            <select name="authorization_code" id="authorization_code" class="classic" style="height: 40px; width: 190px;">
                                @foreach ($authority as $item)
                                    <option
                                        value="{{ $item->authorization_code }}"
                                        {{ (Request::has('authorization_code') ? (Request::get('authorization_code') == $item->authorization_code) : ($item->authorization_code == 999999)) || (isset($data) && $data->authorization_code == $item->authorization_code) ? 'selected' : '' }}
                                    >
                                        {{ $item->authority_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-2 label-div">
                            無効にする
                        </div>
                        <div class="col-10 d-flex">
                            <input type="hidden" name="delete_flag" value='0'>
                            <input type="checkbox" id="delete_flag" name="delete_flag"
                                value="1"
                                {{-- check if user employee is active --}}
                                {{ isset($data) && $data->delete_flag == 1 ? 'checked' : '' }}
                                >
                        </div>
                    </div>

                    <div class="row" style="display: none;">
                        <div class="col-2 label-div">
                            メール通知
                        </div>
                        <div class="col-10 d-flex">
                            <input type="hidden" name="purchasing_approval_request_email_notification_flag" value='0'>
                            <input type="checkbox" id="purchasing_approval_request_email_notification_flag" name="purchasing_approval_request_email_notification_flag"
                                value="1"
                                {{-- check if user employee is active --}}
                                {{ isset($data) && $data->purchasing_approval_request_email_notification_flag == 1 ? 'checked' : '' }}
                                >
                        </div>
                    </div>

                </div>
                <div class="row f-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-success w-150px" data-post-type="{{ $post_type ?? ''}}">登録する</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @include('partials.modals.masters._search', [
        'modalId' => 'searchDepartmentModal',
        'searchLabel' => '部門',
        'resultValueElementId' => 'department_code',
        'resultNameElementId' => 'department_name',
        'model' => 'Department_name'
    ])

@endsection

@push('scripts')
@vite(['resources/js/master/employee/edit.js'])
@vite(['resources/js/master/employee/password-strength.js'])
@endpush