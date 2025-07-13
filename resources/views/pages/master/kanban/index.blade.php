@extends('layouts.app')

@push('styles')
    @vite('resources/css/index.css')
    @vite('resources/css/search-modal.css')
    @vite('resources/css/master/kanban/index.css')
@endpush

@section('title', 'かんばんマスタ一覧')

@section('content')
    <div class="content">
        <div class="contentInner">

            <div class="accordion">
                <h1><span>かんばんマスタ一覧</span></h1>
            </div>

            @if(session('success'))
                <div id="flash-message">
                    {{ session('success') }}
                </div>
            @endif
            
            <div class="pagettlWrap">
                <h1><span>検索</span></h1>
            </div>

            <div class="section mt-4">
                <form  accept-charset="utf-8" accept-charset="utf-8" class="overlayedSubmitForm" data-disregard-empty="true">
                    @csrf
                    <div class="box">
                        <div class="d-flex mb-4">
                            <div class="mr-4">
                                <label class="form-label dotted indented">管理No.</label>
                                <div class="d-flex">
                                    <input type="text" id="management_no" name="management_no" 
                                        value="{{ Request::get("management_no") }}"
                                        class="acceptNumericOnly"
                                        maxlength="6"
                                        style="width: 120px;"
                                    >
                                </div>
                            </div>

                            <div class="mr-4">
                                <label for="part_number" class="form-label dotted indented">品番</label>
                                <div class="d-flex">
                                    <input type="text" id="part_number" name="part_number"
                                        value="{{ Request::get('part_number') }}"
                                        class="w-100px fetchQueryName mr-1"
                                        data-model="ProductNumber"
                                        data-query="part_number"
                                        data-query-get="product_name"
                                        data-reference="product_name"
                                    >
                                    <input type="text" readonly
                                        id="product_name"
                                        name="product_name"
                                        value="{{ Request::get('product_name') }}"
                                        class="middle-name mr-1"
                                    >
                                    <button type="button" class="btnSubmitCustom js-modal-open"
                                            data-target="searchProductModal">
                                        <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                alt="magnifying_glass.svg">
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex mb-4">
                            <div class="mr-4">
                                <label class="form-label dotted indented">かんばん区分</label>
                                <div class="d-flex">
                                    <select name="kanban_classification" id="kanban_classification">
                                        <option value="1" {{ Request::get("kanban_classification") == 1 ? 'selected' : '' }}>支給材</option>
                                        <option value="2" {{ Request::get("kanban_classification") == 2 ? 'selected' : '' }}>外注加工</option>
                                        <option value="3" {{ Request::get("kanban_classification") == 3 ? 'selected' : '' }}>外注支給</option>
                                        <option value="4" {{ Request::get("kanban_classification") == 4 ? 'selected' : '' }}>社内</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="form-label dotted indented">有効/無効</label>
                                <div class="d-flex">
                                    <select name="delete_flag" id="delete_flag">
                                        <option value="0" {{!Request::get("delete_flag") ||  Request::get("delete_flag") == 0 ? 'selected' : '' }}>有効</option>
                                        <option value="1" {{ Request::get("delete_flag") == 1 ? 'selected' : '' }}>無効</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="text-center button-div">
                            <button type="reset" class="btn btn-primary btn-wide">検索条件をクリア</button>
                            <button type="submit" class="btn btn-primary btn-wide">検索</button>

                            <a href="{{ route('master.kanban.excel_export', Request::all()) }}" class="btn btn-success {{ $kanban_records->total() == 0 ? 'btn-disabled' : '' }}">検索結果をEXCEL出力</a>
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
                        @if($kanban_records->total() > 0)
                            {{ $kanban_records->total() }}件中、{{ $kanban_records->firstItem() }}件～{{ $kanban_records->lastItem() }} 件を表示しています
                        @endif
                    </div>
                    <a href="/master/kanban/create" class="btn btn-primary">新規登録</a>
                </div>
                <table class="tableBasic w-50" id="daily-inputs">
                    <thead>
                        <tr>
                            <th>管理No.</th>
                            <th>品番</th>
                            <th>かんばん区分</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kanban_records as $kanban)
                            <tr>
                                <td class="text-center">{{ $kanban->management_no }}</td>
                                <td>
                                    @php
                                        $cleanedPartNumber = preg_replace('/\D/', '', $kanban->part_number);
                                        $partChunks = [
                                            substr($cleanedPartNumber, 0, 4),
                                            substr($cleanedPartNumber, 4, 6),
                                            substr($cleanedPartNumber, 10, 4)
                                        ];

                                        echo implode('-', array_filter($partChunks));
                                    @endphp
                                </td>
                                <td class="text-center">
                                    @php
                                        if($kanban->kanban_classification == 1){
                                            echo "支給材";
                                        } elseif($kanban->kanban_classification == 2){
                                            echo "外注加工";
                                        } elseif($kanban->kanban_classification == 3){
                                            echo "外注支給";
                                        } elseif($kanban->kanban_classification == 4){
                                            echo "社内";
                                        }
                                    @endphp
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('master.kanban.edit', $kanban->id) }}" class="btn btn-primary">編集</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">検索結果はありません</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                {{ $kanban_records->appends(request()->all())->links() }}
            </div>
        </div>
    </div>

    @include('partials.modals.masters._search', [
        'modalId' => 'searchProductModal',
        'searchLabel' => '品番',
        'resultValueElementId' => 'part_number',
        'resultNameElementId' => 'product_name',
        'model' => 'ProductNumber'
    ])
    
@endsection
@push('scripts')
    @vite('resources/js/master/kanban/index.js')
@endpush
