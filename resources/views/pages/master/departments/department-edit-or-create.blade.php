@extends('layouts.app')

@push('styles')
    @vite('resources/css/index.css')
    @vite('resources/css/materials/received_materials_list.css')
    @vite('resources/css/master/product.css')
    @vite('resources/css/search-modal.css')
    @vite('resources/css/master/index.css')
@endpush

@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="accordion">
                <h1><span>部門マスタ登録・編集</span></h1>
            </div>

            <form action="{{ $department->id ? route('master.department.update', ['id' => $department->id]) : route('master.department.store') }}" id="departmentMasterForm" class="overlayedSubmitForm" method="POST" accept-charset="utf-8">
                @csrf
                <div class="tableWrap borderLesstable inputFormAreaLine">
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
                            {{-- code --}}
                            <tr>
                                <td class="fw-15">
                                    <dl class="formsetBox" style="margin-bottom: -10px; margin-left: -15px;">
                                        <dt class="requiredForm">部門コード</dt>
                                    </dl>
                                </td>
                                <td style="text-align:left">
                                    <dd>
                                        <div class="d-flex">
                                            <input type="text" name="code" value="{{ old('code', $department->code) }}" style="max-width: 200px" required>
                                            <div class="err_msg w-100 ml-1"></div>
                                        </div>
                                    </dd>
                                </td>
                            </tr>
                            
                            {{-- name --}}
                            <tr>
                                <td class="fw-15">
                                    <dl class="formsetBox" style="margin-bottom: -10px; margin-left: -15px;">
                                        <dt class="requiredForm">部門名</dt>
                                    <dl class="formsetBox">
                                </td>
                                <td style="text-align:left">
                                    <dd>
                                        <div class="d-flex">
                                            <input type="text" name="name" value="{{ old('name', $department->name) }}" style="max-width: 200px" required>
                                            <div class="err_msg w-100 ml-1"></div>
                                        </div>
                                    </dd>
                                </td>
                            </tr>

                            {{-- name_abbreviation --}}
                            <tr>
                                <td class="fw-15">
                                    <dl class="formsetBox" style="margin-bottom: -10px; margin-left: -15px;">
                                        <dt class="requiredForm">部門名略</dt>
                                    </dl>
                                </td>
                                <td style="text-align:left">
                                    <dd>
                                        <div class="d-flex">
                                            <input type="text" name="name_abbreviation" value="{{ old('name_abbreviation', $department->name_abbreviation) }}" style="max-width: 200px" required>
                                            <div class="err_msg w-100 ml-1"></div>
                                        </div>
                                    </dd>
                                </td>
                            </tr>

                            {{-- department_name --}}
                            <tr>
                                <td class="fw-15">
                                    <dt>部名</dt>
                                </td>
                                <td style="text-align:left">
                                    <dd>
                                        <p class="formPack">
                                            <input type="text" name="department_name" value="{{ old('department_name', $department->department_name) }}" style="max-width: 200px" required>
                                        </p>
                                    </dd>
                                    @error('department_name')
                                        <span class="err_msg">{{ $message }}</span>
                                    @enderror
                                </td>
                            </tr>

                            {{-- section_name --}}
                            <tr>
                                <td class="fw-15">
                                    <dt>課名</dt>
                                </td>
                                <td style="text-align:left">
                                    <dd>
                                        <p class="formPack">
                                            <input type="text" name="section_name" value="{{ old('section_name', $department->section_name) }}" style="max-width: 200px" required>
                                        </p>
                                    </dd>
                                    @error('section_name')
                                        <span class="err_msg">{{ $message }}</span>
                                    @enderror
                                </td>
                            </tr>

                            {{-- group_name --}}
                            <tr>
                                <td class="fw-15">
                                    <dt>組名</dt>
                                </td>
                                <td style="text-align:left">
                                    <dd>
                                        <p class="formPack">
                                            <input type="text" name="group_name" value="{{ old('group_name', $department->group_name) }}" style="max-width: 200px" required>
                                        </p>
                                    </dd>
                                    @error('group_name')
                                        <span class="err_msg">{{ $message }}</span>
                                    @enderror
                                </td>
                            </tr>

                            {{-- delete_flag --}}
                            @if (!empty($department->id))
                            <tr>
                                <td class="fw-15">
                                    <dt>無効にする</dt>
                                </td>
                                <td>
                                    <div class="formPack" style="display: flex; align-items: left; justify-content: flex-start;">
                                        <input style="max-width: 20px; " type="checkbox" name="delete_flag" value="1" {{ old('delete_flag', $department->delete_flag) ? 'checked' : '' }}>&nbsp;
                                    </div>
                                    <div class="error_msg"></div>
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>

                    <input type="hidden" id="department_id" value="{{ isset($department) ? $department->id : '' }}">
                    <div class="buttonRow" style="display: flex; justify-content: space-between;">
                        <div>
                            <button type="button" id="delete_department" class="btn btn-orange btn-wide {{ !isset($department->id) ? 'isNew' : '' }}">
                                削　除
                            </button>
                        </div>
                        <div style="display: flex; justify-content: flex-end;">
                            @if (session()->has('department_data'))
                                <div style="margin-right: 10px">
                                    <button type="button" id="btn-copy-department" class="buttonCreate button-product btn-blue" style="display: {{ isset($department->id) ? 'none' : '' }}">
                                        複写入力
                                    </button>
                                </div>
                            @endif
                            
                            <div>
                                <button type="submit" class="btn btn-success btn-wide" style="width: 180px">
                                    この内容で登録する
                                </button>
                            </div>
                        </div>
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
        'model' => 'Department'
    ])
@endsection
@push('scripts')
    @vite(['resources/js/master/departments/data-form.js'])
@endpush
