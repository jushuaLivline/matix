@extends('layouts.app')

@push('styles')
    @vite('resources/css/index.css')
    @vite('resources/css/search-modal.css')
@endpush

@section('title', '機番マスタ一覧')
@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="pageHeaderBox rounded">
                機番マスタ一覧
            </div>

            @if(session('success'))
                <div id="card" style="background-color: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);margin-top: 20px;">
                    <div style="text-align: left;">
                        <p style="font-size: 18px; color: #0d9c38;">
                            {{ session('success') }}
                        </p>
                    </div>
                </div>
            @endif

            <div class="section">
                <h1 class="form-label bar indented">検索</h1>

                <form accept-charset="utf-8" class="overlayedSubmitForm with-js-validation" data-disregard-empty="true" id="form">
                    <div class="box mb-3">
                        <div class="d-flex mb-4">
                            <div class="w-15 mr-5">
                                <label class="form-label dotted indented">機番</label>
                                <div class="d-flex">
                                    <input type="text" name="machine_number_from" value="{{ Request::get("machine_number_from") }}" class="w-45">
                                    <span style="font-size:24px; padding:0px 5px;">
                                        ~
                                    </span>
                                    <input type="text" name="machine_number_to" value="{{ Request::get("machine_number_to") }}" class="w-45">
                                </div>
                            </div>
                            <div class="w-15 mr-5">
                                <label class="form-label dotted indented">機番名</label>
                                <div>
                                    <input type="text" name="machine_number_name" value="{{ Request::get("machine_number_name") }}" class="full-width">
                                </div>
                            </div>
                            <div class="w-30 mr-5">
                                <label class="form-label dotted indented">プロジェクトNo.</label>
                                <div class="d-flex">
                                    <input type="text" 
                                            data-field-name="プロジェクトNo"
                                            data-error-messsage-container="#project_number_error"
                                            data-validate-exist-model="Project" 
                                            data-validate-exist-column="project_number"
                                            data-inputautosearch-model="Project" 
                                            data-inputautosearch-column="project_number"
                                            data-inputautosearch-return="project_name" 
                                            data-inputautosearch-reference="project_name"
                                            id="project_number" 
                                            name="project_number" 
                                            style="width: 105px; margin-right: 10px;" 
                                            maxlength="8"
                                            value="{{ $machineNumber->project_number ?? '' }}">
                                        <input type="text" readonly
                                                name="project_name"
                                                id="project_name"
                                                value="{{ $machineNumber?->project?->project_name ?? '' }}"
                                                class="middle-name "
                                                style="width:300px; margin-right: 10px;">
                                    <button type="button" class="btnSubmitCustom js-modal-open"
                                        data-target="searchProjectModal">
                                        <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                            alt="magnifying_glass.svg">
                                    </button>
                                </div>
                                <div id="project_number_error"></div>
                            </div>
                            <div class="w-15">
                                <label class="form-label dotted indented">ライン名</label>
                                <div>
                                    <input type="text" name="line_name" value="{{ Request::get("line_name") }}" class="full-width">
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex mb-4">
                            <div class="w-15 mr-5">
                                <label class="form-label dotted indented">機械区分</label>
                                <select name="machine_division" id="machine_division" class="classic" style="width: 200px">
                                    @php
                                        $machine_division = request()->get('machine_division');
                                    @endphp
                                    <option value="all" @if($machine_division == '') selected @endif>すべて</option>
                                    @foreach ($machineDivision as $key => $division)
                                        <option value="{{ $key }}" @if($machine_division != '' && $machine_division == $key) selected @endif>{{ $division }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="w-15 mr-5">
                                <label class="form-label dotted indented">備考</label>
                                <input type="text" class="row-input-long" id="remarks" name="remarks" value="{{ Request::get('remarks') }}">
                            </div>
                            <div class="w-10 mr-3">
                                <label class="form-label dotted indented">状態</label>
                                <select name="completion_date" id="completion_date" class="classic" style="width: 100px">
                                    @php
                                        $completion_date = request()->get('completion_date');
                                    @endphp
                                    <option value="all" @if(!$completion_date) selected @endif>すべて</option>
                                    <option value="1" @if($completion_date === '1') selected @endif>完成</option>
                                    <option value="2" @if($completion_date === '2') selected @endif>仕掛</option>
                                </select>
                            </div>
                            <div class="w-10 mr-5">
                                <label class="form-label dotted indented">有効/無効</label>
                                <select name="delete_flag" id="delete_flag" class="classic" style="width: 100px">
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
                                <button type="button" class="btn btn-primary btn-wide" data-clear-inputs data-clear-form-target="#form">検索条件をクリア</button>
                                <button class="btn btn-primary btn-wide" type="submit">検索</button>
                                <a href="{{ route("master.masterMachine.export", request()->all()) }}" class="btn btn-success float-right {{ $data->total() == 0 ? 'btn-disabled' : '' }}">検索結果をEXCEL出力</a>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="section">
                    <h1 class="form-label bar indented">検索結果</h1>

                    <div class="box">
                        <ul class="headerList">
                            @if($data && $data->total() > 0)
                                <li>{{ $data->total() }}件中、{{ $data->firstItem() }}件～{{ $data->lastItem() }} 件を表示してます</li>
                            @else
                                <li></li>
                            @endif
                            <li>
                                <a href="{{ route('master.masterMachine.create') }}" class="btn btn-primary">
                                    新規登録
                                </a>
                            </li>
                        </ul>
                        <div class="mb-3 tableWrap bordertable clear-both" style="padding: 0 !important">
                            <table class="tableBasic list-table table-bordered">
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
                                        <td class="tA-cn">{{ $row->machine_number }}</td>
                                        <td class="tA-le">{{ $row->machine_number_name }}</td>
                                        <td class="tA-cn">{{ $row->project_number }}</td>
                                        <td class="tA-le">{{ $row->line_name }}</td>
                                        <td class="tA-cn">{{ $row->machine_division }}</td>
                                        <td class="tA-cn">{{ $row->created_at }}</td>
                                        <td class="tA-cn">{{ $row->drawing_date }}</td>
                                        <td class="tA-cn">{{ $row->completion_date }}</td>
                                        <td class="tA-le">{{ $row->manager }}</td>
                                        <td class="tA-le">{{ $row->remarks }}</td>
                                        <td class="tA-cn">
                                            <a href="{{ route('master.masterMachine.edit', array_merge([$row->id], request()->query()) ) }}" class="buttonBasic bColor-ok">
                                                編集
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                @endif
                                </tbody>
                            </table>
                        </div>
                        @if (count($data) > 0)
                            {{ $data->links() }}
                        @endif
                    </div>
                </div>
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