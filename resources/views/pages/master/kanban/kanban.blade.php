@extends('layouts.app')

@push('styles')
    @vite('resources/css/estimates/index.css')
    @vite('resources/css/estimates/data_list.css')
    @vite('resources/css/search-modal.css')
    @vite('resources/css/index.css')

@endpush
@section('title', 'かんばんマスタ一覧')
@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="accordion">
                <h1><span>かんばんマスタ一覧</span></h1>
            </div>

            <div class="pagettlWrap">
                <h1><span>検索</span></h1>
            </div>

            <form action="{{ route('master.kanbans.search')  }}" method="GET" accept-charset="utf-8" class="overlayedSubmitForm">
                @csrf
                <div class="tableWrap borderLesstable inputFormArea">
                    <table class="tableBasic" style="width: 50%">
                        <tbody>
                            <tr>
                                <td>
                                    <dl class="formsetBox fixedWidth">
                                        <dt class="">管理No.</dt>
                                        <p class="formPack">
                                            <input type="text" name="management_no" value="{{ $management_no }}" class="" style="max-width: 80px" >
                                        </p>
                                        <div class="error_msg"></div>
                                    </dl>
                                </td>
                                <td style="text-align:left;">
                                    <dl class="formsetBox">
                                        <dt class="">品番</dt>
                                        <div class="formPack" style="display: flex; align-items: center;">
                                            <input type="text" id="part_number" name="part_number" value="{{ $part_number }}" class="" style="max-width: 200px">
                                            <input type="text" readonly
                                                id="product_name"
                                                name="product_name"
                                                value="{{ Request::get('product_name') }}"
                                                class="middle-name"
                                                style="max-width: 250px; margin-left: 10px;">

                                                <button type="button" class="btnSubmitCustom js-modal-open" style="margin-left: 10px;"
                                                                data-target="searchPartNumberModal">
                                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                            alt="magnifying_glass.svg">
                                                </button>
                                        </div>
                                        <div class="error_msg"></div>
                                    </dl>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <dl class="formsetBox">
                                        <dt class="">かんばん区分</dt>
                                        <dd>
                                            <p class="formPack fixedWidth" style="width: 120px;">
                                                <select name="kanban_classification" class="classic">
                                                    <option value="0">すべて</option>
                                                    <option value="1" @if($kanban_classification === '1') selected @endif>支給材</option>
                                                    <option value="2" @if($kanban_classification === '2') selected @endif>外注加工</option>
                                                    <option value="3" @if($kanban_classification === '3') selected @endif>外注支給</option>
                                                    <option value="4" @if($kanban_classification === '4') selected @endif>社内</option>
                                                </select>
                                            </p>
                                            <div class="error_msg"></div>
                                        </dd>
                                    </dl>
                                </td>
                                <td>
                                    <dl class="formsetBox">
                                        <dt class="">有効/無効</dt>
                                        <dd>
                                            <p class="formPack fixedWidth" style="width: 180px;">
                                                <select name="delete_flag" class="classic">
                                                    <option value="すべて" @if($delete_flag === 'すべて') selected @endif>すべて</option>
                                                    <option value="0" @if($delete_flag === '0') selected @endif @if($delete_flag == '') selected @endif>有効</option>
                                                    <option value="1" @if($delete_flag === '1') selected @endif>無効</option>
                                                </select>
                                            </p>
                                            <div class="error_msg"></div>
                                        </dd>
                                    </dl>
                                </td>

                            </tr>
                        </tbody>
                    </table>
                    <div class="mt-3">
                        <a id="kanbanDownloadCSV" href="#" class="btn btn-success btn-wide float-right">検索結果をEXCEL出力</a>
                        <div class="text-center">
                            <a href="{{ route("master.kanbans.index") }}" class="btn btn-primary btn-wide js-btn-reset-reload">検索条件をクリア</a>
                            <button class="btn btn-primary btn-wide" type="submit">検索</button>
                        </div>
                    </div>
                    {{-- <div class="buttonRow" style="display:flex; justify-content: space-between; align-items: center">
                        <ul class="buttonlistWrap" style="flex-grow: 1; display: flex; justify-content: center; margin-left: 35%">
                          <li>
                            <a href="{{ route('master.kanbans.index')  }}" class="buttonBasic btn-reset bColor-ok js-btn-reset-reload">検索条件をクリア</a>
                          </li>
                          <li>
                            <input type="submit" value="検索" class="buttonBasic bColor-ok">
                          </li>
                        </ul>
                        <div class="" style="margin-left: auto">
                            <a id="kanbanDownloadCSV" href="#" class="btn bg-light-green">検索結果をEXCEL出力</a>
                        </div>
                    </div> --}}
                </div>
            </form>

            <div class="pagettlWrap">
                <h1><span>検索結果</span></h1>
            </div>
            <div class="tableWrap bordertable" style="clear: both;">
                <ul class="headerList">
                    @if (count($kanban) > 0)
                        <li>{{ $kanban->total() }}件中、{{ $kanban->firstItem() }}件～{{ $kanban->lastItem() }} 件を表示してます</li>
                    @else
                        <li></li>
                    @endif
                    <li>
                        <a href="{{ route("master.kanban.createOrUpdate") }}" class="buttonBasic bColor-ok">
                            新規登録
                        </a>
                    </li>
                </ul>
                <table class="tableBasic list-table">
                    <tbody>
                    <tr>
                        <th>管理No.</th>
                        <th>品番</th>
                        <th>かんばん区分</th>
                        <th>操作</th>
                    </tr>
                    @if ($kanban != [])
                        @foreach($kanban as $kb)
                            <tr>
                                <td style="text-align: center">{{ $kb->management_no }}</td>
                                <td style="text-align: center">{{ $kb->edited_part_number }}</td>
                                <td style="text-align: center">{{ $kb->kanban_classification_text }}</td>
                                <td style="text-align: center">
                                    <a class="buttonBasic bColor-ok" href="{{ route("master.kanban.createOrUpdate", $kb) }}">
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
                @if($kanban)
                {{ $kanban->links() }}
                @endif
            </div>
        </div>
    </div>
    @include('partials.modals.masters._search', [
        'modalId' => 'searchPartNumberModal',
        'searchLabel' => '品番',
        'resultValueElementId' => 'part_number',
        'resultNameElementId' => 'product_name',
        'model' => 'ProductNumber'
    ])
@endsection
@push('scripts')
    @vite(['resources/js/master/kanban/data-form.js'])
@endpush
