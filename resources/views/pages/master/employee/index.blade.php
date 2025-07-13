@extends('layouts.app')

@push('styles')
    @vite('resources/css/index.css')
    @vite('resources/css/search-modal.css')
    @vite('resources/css/master/employee/index.css')
@endpush

@section('title', '社員マスタ一覧')

@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="accordion">
                <h1><span>社員マスタ一覧</span></h1>
            </div>

            <div class="pagettlWrap">
                <h1><span>検索</span></h1>
            </div>

            <div class="section mt-4">
                <form id="employee-search-form" accept-charset="utf-8" accept-charset="utf-8" class="overlayedSubmitForm" data-disregard-empty="true">
                    @csrf
                    <div class="box">
                        <div class="d-flex mb-4">
                            <div class="mr-4">
                                <label for="employee_code" class="form-label dotted indented">社員コード</label>
                                <div class="d-flex">
                                    <input type="text" id="employee_code" name="employee_code" 
                                        value="{{ Request::get("employee_code") }}"
                                        class="w-120px"
                                    >
                                </div>
                            </div>

                            <div class="mr-4">
                                <label for="employee_name" class="form-label dotted indented">社員名</label>
                                <div class="d-flex">
                                    <input type="text" id="employee_name" name="employee_name" 
                                        value="{{ Request::get("employee_name") }}"
                                    >
                                </div>
                            </div>

                            <div class="mr-4">
                                <label for="department_code" class="form-label dotted indented">部門</label>
                                <div class="d-flex">
                                    <input type="text" id="department_code" name="department_code"
                                        value="{{ Request::get('department_code') }}"
                                        class="w-100px fetchQueryName mr-1 acceptNumericOnly w-120px"
                                        data-model="Department"
                                        data-query="code"
                                        data-query-get="department_name"
                                        data-reference="department_name"
                                        maxlength="6"
                                    >
                                    <input type="text" readonly
                                        id="department_name"
                                        name="department_name"
                                        value="{{ Request::get('department_name') }}"
                                        class="middle-name mr-1"
                                    >
                                    <button type="button" class="btnSubmitCustom js-modal-open"
                                            data-target="searchDepartmentModal">
                                        <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                alt="magnifying_glass.svg">
                                    </button>
                                </div>
                            </div>

                            <div class="mr-4">
                                <label for="authorization_code" class="form-label dotted indented">権限</label>
                                <div class="d-flex">
                                    <select name="authorization_code" id="authorization_code" class="classic" data-default="999999" style="height: 40px; width: 190px;">
                                        @foreach ($authority as $item)
                                            <option
                                                value="{{ $item->authorization_code }}"
                                                {{ Request::has('authorization_code') ? (Request::get('authorization_code') == $item->authorization_code ? 'selected' : '') : ($item->authorization_code == 999999 ? 'selected' : '') }}
                                            >
                                                {{ $item->authority_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex mb-4">
                            <div class="mr-4">
                                <label for="delete_flag" class="form-label dotted indented">有効 / 無効</label>
                                <div class="d-flex">
                                    <select name="delete_flag" id="delete_flag" data-default="0">
                                        <option value="0" {{ Request::has('delete_flag') ? (Request::get('delete_flag') == '0' ? 'selected' : '') : 'selected' }}>有効</option>
                                        <option value="1" {{ Request::has('delete_flag') && Request::get('delete_flag') == '1' ? 'selected' : '' }}>無効</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="text-center button-div">
                            <button type="reset" class="btn btn-primary btn-wide">検索条件をクリア</button>
                            <button type="submit" class="btn btn-primary btn-wide">検索</button>

                            <a 
                                href="{{ route('master.employee.excel_export', Request::all()) }}"
                                class="btn btn-success {{ $employee_records->total() == 0 ? 'btn-disabled' : '' }}"
                                {{ $employee_records->total() == 0 ? 'title="表に検索結果が見つかりません"': ''}}
                                onclick="{{ $employee_records->total() == 0 ? 'return false;' : '' }}"
                            >
                                検索結果をEXCEL出力
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <div class="pagettlWrap">
                <h1><span>検索結果</span></h1>
            </div>

            <div class="tableWrap bordertable" style="clear: both;">
                <div class="d-flex justify-content-between align-items-center mb-2 w-50">
                    <div>
                        @if($employee_records->total() > 0)
                            {{ $employee_records->total() }}件中、{{ $employee_records->firstItem() }}件～{{ $employee_records->lastItem() }} 件を表示しています
                        @endif
                    </div>
                    <a href="/master/employee/create" class="btn btn-primary">新規登録</a>
                </div>
                <table class="tableBasic w-50" id="daily-inputs">
                    <thead>
                        <tr>
                            <th>社員コード</th>
                            <th>社員名</th>
                            <th>部門名</th>
                            <th>権限</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employee_records as $employee)
                            <tr>
                                <td class="text-center">{{ $employee->employee_code }}</td>
                                <td>{{ $employee->employee_name }}</td>
                                <td>{{ $employee->department?->department_name }}</td>
                                <td>{{ $employee->authority?->authority_name }}</td>
                                <td class="text-center">
                                    <a href="{{ route('master.employee.edit', $employee->id) }}" class="btn btn-primary">編集</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">検索結果はありません</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                {{ $employee_records->appends(request()->all())->links() }}
            </div>
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
    @vite('resources/js/master/employee/index.js')
@endpush