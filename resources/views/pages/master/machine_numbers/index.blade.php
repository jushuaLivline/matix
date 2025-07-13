@extends('layouts.app')

@push('styles')
    @vite('resources/css/estimates/index.css')
    @vite('resources/css/estimates/data_list.css')
    @vite('resources/css/master/product.css')
    @vite('resources/css/master/project.css')
    @vite('resources/css/master/machine_number.css')
    @vite('resources/css/search-modal.css')
@endpush

@section('title', '機番マスタ一覧')
@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="accordion">
                <h1><span>機番マスタ一覧</span></h1>
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

            <div class="pagettlWrap">
                <h1><span>検索</span></h1>
            </div>

            <form action="{{ route('master.machineNumbers.machineNumberSearch') }}" method="GET" accept-charset="utf-8" class="overlayedSubmitForm">
                <div class="tableWrap borderLesstable inputFormArea">
                    <div class="row-mn-content">
                        <!-- 機番 -->
                        <div class="flex-row">
                            <label for="machine_number" class="label_for">機番</label>
                            <div class="row-group-input">
                                <input
                                    type="text"
                                    class="m-input"
                                    value="{{ Request::get('machine_number_from') }}"
                                    name="machine_number_from"
                                    id="machine_number_from"
                                >
                                <input
                                    type="text"
                                    class="b-input"
                                    value="{{ Request::get('branch_number_from') }}"
                                    name="branch_number_from"
                                    id="branch_number_from"
                                >
                                <span style="font-size:24px; padding:5px 10px;">~</span>
                                <input
                                    type="text"
                                    class="m-input"
                                    value="{{ Request::get('machine_number_to') }}"
                                    name="machine_number_to"
                                    id="machine_number_to"
                                >
                                <input
                                    type="text"
                                    class="b-input"
                                    value="{{ Request::get('branch_number_to') }}"
                                    name="branch_number_to"
                                    id="branch_number_to">
                            </div>
                        </div>
                        <!-- 品名 -->
                        <div class="flex-row">
                            <label for="machine_number_name" class="label_for">機番名</label>
                            <input type="text" class="input-mid" id="machine_number_name" name="machine_number_name" value="{{ Request::get('machine_number_name') }}">
                        </div>
                        {{-- プロジェクトNo. --}}
                        <div class="flex-row">
                            <label for="project_number" class="label_for">プロジェクトNo.</label>
                            <div class="search-mn-group">
                                <input type="text" id="project_number" name="project_number" value="{{ Request::get('project_number') }}" class="" style="width: 100px">
                                <input type="text" readonly
                                        id="project_name"
                                        value="{{ Request::get('project_name') }}"
                                        class="middle-name"
                                        style="width: 135px">
                                <button type="button" class="btnSubmitCustom js-modal-open"
                                                data-target="searchProjectModal">
                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                            alt="magnifying_glass.svg">
                                </button>
                            </div>
                        </div>
                        {{-- ライン名 --}}
                        <div class="flex-row">
                            <label for="line_name" class="label_for">ライン名</label>
                            <input type="text" class="input-mid" id="line_name" name="line_name" value="{{ Request::get('line_name') }}">
                        </div>
                    </div>

                    <div class="row-mn-content">
                        <!-- 取引先 -->
                        <div class="flex-row">
                            <label for="machine_division" class="label_for">製品区分</label>
                            <select name="machine_division" id="machine_division" class="classic" style="width: 200px">
                                <option value="">すべて</option>
                                @foreach ($machineDivision as $key => $division)
                                    <option value="{{ $key }}" {{ Request::get('machine_division') && Request::get('machine_division') == $key ? 'selected' : '' }}>{{ $division }}</option>
                                @endforeach
                            </select>
                        </div>
                        {{-- 備考 --}}
                        <div class="flex-row">
                            <label for="product_name" class="label_for">備考</label>
                            <input type="text" class="row-input-long" id="product_name" name="product_name" value="{{ Request::get('product_name') }}">
                        </div>
                        <!-- 状態 -->
                        <div class="flex-row">
                            <label for="completion_date" class="label_for">状態</label>
                            <select name="completion_date" id="completion_date" class="classic">
                                <option value="">すべて</option>
                                <option value="1" {{ Request::get('completion_date') == 1 ? 'selected' : '' }}>完成</option>
                                <option value="2" {{ Request::get('completion_date') == 2 ? 'selected' : '' }}>仕掛</option>
                            </select>
                        </div>
                        <!-- 有効/無効 -->
                        <div class="flex-row">
                            <label for="delete_flag" class="label_for">有効/無効</label>
                            <select name="delete_flag" id="delete_flag" class="classic">
                                <option value="2" {{ Request::get('delete_flag') == 2 ? 'selected' : '' }}>すべて</option>
                                <option value="0" {{ Request::get('delete_flag') == 0 ? 'selected' : '' }}>有効</option>
                                <option value="1" {{ Request::get('delete_flag') == 1 ? 'selected' : '' }}>無効</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a id="export_csv" href="#" class="btn btn-success btn-wide float-right">検索結果をEXCEL出力</a>
                        <div class="text-center">
                            <a href="{{ route("master.machineNumbers.index") }}" class="btn btn-primary btn-wide js-btn-reset-reload">検索条件をクリア</a>
                            <button class="btn btn-primary btn-wide" type="submit">検索</button>
                        </div>
                    </div>
                </div>
            </form>

            <div class="pagettlWrap">
                <h1><span>検索結果</span></h1>
            </div>
            <div class="tableWrap bordertable" style="clear: both;">
                <ul class="headerList">
                    @if (count($data) > 0)
                        <li>{{ $count }}件中、{{ $data->firstItem() }}件～{{ $data->lastItem() }} 件を表示してます</li>
                    @else
                        <li></li>
                    @endif
                    <li>
                        <a href="{{ route('master.masterMachine.create') }}" class="buttonBasic bColor-ok">
                            新規登録
                        </a>
                    </li>
                </ul>
                <table class="tableBasic list-table">
                    <tbody>
                    <tr>
                        <th>機番</th>
                        <th>機械名</th>
                        <th>プロジェクトNo.</th>
                        <th>ライン名</th>
                        <th>機械区分</th>
                        <th>登録日</th>
                        <th>出図日</th>
                        <th>完成日</th>
                        <th>担当者</th>
                        <th>備考</th>
                        <th style="width: 100px">操作</th>
                    </tr>
                    @if (count($data) <= 0)
                        @include('partials._no_record', ['colspan' => 11])
                    @else
                        @foreach($data as $row)
                        <tr>
                            <td class="tA-le">{{ $row->machine_number }}</td>
                            <td class="tA-le">{{ $row->machine_number_name }}</td>
                            <td class="tA-le">{{ $row->project_number }}</td>
                            <td class="tA-le">{{ $row->line_name }}</td>
                            <td class="tA-le">{{ $row->machine_division }}</td>
                            <td class="tA-le">{{ $row->created_at }}</td>
                            <td class="tA-le">{{ $row->drawing_date }}</td>
                            <td class="tA-le">{{ $row->completion_date }}</td>
                            <td class="tA-le">{{ $row->manager }}</td>
                            <td class="tA-le">{{ $row->remarks }}</td>
                            <td class="tA-cn">
                                <a href="{{ route('master.masterMachine.edit', array_merge([$row], request()->query())) }}" class="buttonBasic bColor-ok" style="width: 100px">
                                    編集
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    @endif
                    </tbody>
                </table>
                @if (count($data) > 0)
                    {{ $data->links() }}
                @endif
            </div>
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
    @vite(['resources/js/master/machine-numbers/data-form.js'])
@endpush