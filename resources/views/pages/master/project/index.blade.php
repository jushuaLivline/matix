@extends('layouts.app')

@push('styles')
    @vite('resources/css/index.css')
    @vite('resources/css/master/product.css')
    @vite('resources/css/search-modal.css')
    @vite('resources/css/master/project/index.css')
@endpush

@section('title', 'プロジェクトマスタ一覧')

@section('content')
<div class="content">
    <div class="contentInner">
        <div class="accordion">
            <h1><span>プロジェクトマスタ一覧</span></h1>
        </div>
        
        @if(session('success'))
            <div id="flash-message">
                {{ session('success') }}
            </div>
        @endif

        <div class="pagettlWrap">
            <h1><span>検索</span></h1>
        </div>

        <form id="search-form" accept-charset="utf-8" class="overlayedSubmitForm">
            <div class="tableWrap borderLesstable">
                <div class="d-flex mb-4">
                    <div class="p-0 mr-4">
                        <label for="project_name" class="form-label dotted indented">プロジェクト名</label> <span class="others-frame btn-orange badge">必須</span>
                        <div class="search-group">
                            <input type="text" id="project_name" name="project_name"
                                value="{{ Request::get('project_name') }}"
                                required
                            >
                        </div>
                    </div>

                    <div class="flex-row p-0 mr-4">
                        <label for="delete_flag" class="form-label dotted indented">有効/無効</label>
                        <select name="delete_flag" id="delete_flag"
                            >
                            <option value="0" {{ (!Request::get('delete_flag') || Request::get('delete_flag') == 0) ? 'selected' : '' }}>有効</option>
                            <option value="1" {{ Request::get('delete_flag') == 1 ? 'selected' : '' }}>無効</option>
                        </select>
                    </div>
                </div>

                <div class="text-center sc relative button-div">
                    <button type="reset" class="btn btn-primary btn-wide">検索条件をクリア</button>
                    <button type="submit" class="btn btn-primary btn-wide">検索</button>
                    <a 
                        href="{{ route('master.project.excel_export', request()->query()) }}" 
                        href="#"
                        id="export-excel"
                        class="btn btn-success absolute-right {{ $project_records->total() == 0 ? 'btn-disabled' : '' }}"
                        {{ count($project_records) == 0 ? 'onclick="return false;" style=pointer-events:none; opacity:0.5; cursor:not-allowed;"' : '' }}
                    >
                        検索結果をEXCEL出力
                    </a>
                </div>
            </div>
        </form>

        <div class="pagettlWrap">
            <h1><span>検索結果</span></h1>
        </div>
        <div class="tableWrap bordertable" style="clear: both;">
            <div class="d-flex justify-content-between align-items-center mb-2 w-50">
                <div>
                    @if($project_records && $project_records->total() > 0)
                        {{ $project_records->total() }}件中、{{ $project_records->firstItem() }}件～{{ $project_records->lastItem() }} 件を表示しています
                     @endif
                </div>
                <a href="{{ route('master.project.create') }}" class="btn btn-primary">新規登録</a>
            </div>
            <table class="tableBasic w-50" id="project-table">
                <thead>
                    <tr>
                        <th>プロジェクトNo.</th>
                        <th>プロジェクト名</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($project_records as $record)
                        <tr>
                            <td class="text-center">{{ $record->project_number }}</td>
                            <td>{{ $record->project_name }}</td>
                            <td class="text-center">
                                <a href="{{ route('master.project.edit', $record->id) }}" class="btn btn-primary">編集</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">検索結果はありません</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $project_records->appends(request()->all())->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
    @vite(['resources/js/master/project/index.js'])
@endpush