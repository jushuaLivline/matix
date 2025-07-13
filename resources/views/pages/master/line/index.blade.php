@extends('layouts.app')

@push('styles')
    @vite('resources/css/estimates/index.css')
    @vite('resources/css/estimates/data_list.css')
    @vite('resources/css/search-modal.css')
    @vite('resources/css/index.css')
@endpush

@section('title', '取引先マスタ一覧')
@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="pageHeaderBox rounded">
                取引先マスタ一覧
            </div>

            @if(session('success'))
                <div id="card" style="background-color: #f0f0f0; padding: 20px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);margin-top: 20px;">
                    <div style="text-align: left;">
                        <p style="font-size: 18px; color: #0d9c38; margin-bottom: 10px;">
                            {{ session('success') }}
                        </p>
                    </div>
                </div>
            @endif

            <div class="section">
                <h1 class="form-label bar indented">検索</h1>
                <form accept-charset="utf-8" class="overlayedSubmitForm with-js-validation" data-disregard-empty="true" 
                    id="form"
                    data-confirmation-message="ラインマスタ情報を登録します、よろしいでしょうか？">
                    <div class="box mb-5">
                        <div class="d-flex mb-4">
                            <div class="w-15 mr-4">
                                <label class="form-label dotted indented">取引先コード</label>
                                <div>
                                    <input type="text" name="line_code" value="{{ Request::get("line_code") }}" class="full-width">
                                </div>
                            </div>
                            <div class="w-30">
                                <label class="form-label dotted indented">部門コード</label>
                                <div class="d-flex">
                                    <p class="mr-1 w-30">
                                        <input type="text" name="department_code" value="{{ Request::get('department_code') }}" id="department_code" class="full-width searchOnInput"
                                            maxlength="10"
                                            data-inputautosearch-model="department"
                                            data-inputautosearch-reference="department_name"
                                            data-inputautosearch-column="code"
                                            data-inputautosearch-return="name_abbreviation">
                                    </p>
                                    <p class="mr-2 w-70">
                                        <input type="text" readonly
                                            id="department_name"
                                            value="{{ Request::get('department_name') }}"
                                            name="department_name" class="middle-name full-width">
                                    </p>
                                    <button type="button" class="btnSubmitCustom js-modal-open" data-target="searchDepartmentModal">
                                        <img src="{{ asset('images/icons/magnifying_glass.svg') }}" alt="magnifying_glass.svg">
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label dotted indented">有効/無効</label>
                            <div>
                                <select name="delete_flag" class="classic">
                                    @php
                                        $delete_flag = request()->get('delete_flag');
                                    @endphp
                                    <option value="all" @if(!$delete_flag) selected @endif>すべて</option>
                                    <option value="0" @if($delete_flag === '0') selected @endif>有効</option>
                                    <option value="1" @if($delete_flag === '1') selected @endif>無効</option>
                                </select>                                
                            </div>
                        </div>
                        
                        <div>
                            <div class="text-center">
                                <a href="#" class="btn btn-primary btn-wide" data-clear-inputs data-clear-form-target="#form">検索条件をクリア</a>
                                <button class="btn btn-primary btn-wide" type="submit">検索</button>
                                <a href="{{ route("master.masterLine.export") }}" class="btn btn-success float-right {{ $lines->total() == 0 ? 'btn-disabled' : '' }}">検索結果をEXCEL出力</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="section">
                <h1 class="form-label bar indented">検索結果</h1>

                <div class="box">
                    <ul class="headerList">
                        @if($lines && $lines->total() > 0)
                            <li>{{ $lines->total() }}件中、{{ $lines->firstItem() }}件～{{ $lines->lastItem() }} 件を表示してます</li>
                        @else
                            <li></li>
                        @endif
                        <li>
                            <a href="{{ route("master.masterLine.create") }}" class="btn btn-primary">
                                新規登録
                            </a>
                        </li>
                    </ul>
                    <div class="mb-3 tableWrap bordertable clear-both" style="padding: 0 !important">
                        <table class="tableBasic list-table table-bordered">
                            <tbody>
                                <tr>
                                    <th width="20%">ラインコード</th>
                                    <th>ライン名</th>
                                    <th width="20%">部門コード</th>
                                    <th width="15%">操作</th>
                                </tr>
                                @if (count($lines) > 0)
                                    @foreach($lines as $line)
                                        <tr>
                                            <td style="text-align: center">{{ $line->line_code }}</td>
                                            <td style="text-align: left">{{ $line->line_name }}</td>
                                            <td style="text-align: center">{{ $line->department_code }}</td>
                                            <td style="text-align: center">
                                                <a class="buttonBasic bColor-ok" href="{{ route("master.masterLine.edit", $line) }}">
                                                    編集
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="4" style="text-align: center">検索結果はありません</td>
                                    </tr>
                                @endif

                            </tbody>
                        </table>
                    </div>
                    @if ($lines)
                    {{ $lines->appends(request()->query())->links() }}
                @endif
                </div>
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
    @vite(['resources/js/master/lines/data-form.js'])
@endpush
