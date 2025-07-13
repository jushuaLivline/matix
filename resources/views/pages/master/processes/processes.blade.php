@extends('layouts.app')

@push('styles')
    @vite('resources/css/master/index.css')
    @vite('resources/css/estimates/data_list.css')
    @vite('resources/css/index.css')
    @vite('resources/css/materials/received_materials_list.css')
@endpush

@section('title', '工程マスタ一覧')
@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="accordion">
                <h1><span>工程マスタ一覧</span></h1>
            </div>

            <div class="pagettlWrap">
                <h1><span>検索</span></h1>
            </div>

            <form action="{{ route('master.process.search')  }}" method="GET" accept-charset="utf-8" class="overlayedSubmitForm">
                @csrf
                <div class="tableWrap borderLesstable inputFormArea">
                    <table class="tableBasic" style="width: 30%">
                        <tbody>
                            <tr>
                                {{-- process_code --}}
                                <td>
                                    <dl class="formsetBox fixedWidth">
                                        <dt class="">工程コード</dt>
                                        <dd>
                                            <p class="formPack">
                                                <input type="text" name="process_code" value="{{ $process_code }}" class="" style="width: 250px">
                                            </p>
                                            <div class="error_msg"></div>
                                        </dd>
                                    </dl>
                                </td>
                                {{-- process_name --}}
                                <td style="text-align:left">
                                    <dl class="formsetBox">
                                        <dt class="">工程名</dt>
                                        <dd>
                                            <p class="formPack">
                                                <input type="text" name="process_name" value="{{ $process_name }}" class="" style="min-width: 450px">
                                            </p>
                                            <div class="error_msg"></div>
                                        </dd>
                                    </dl>
                                </td>
                            </tr>
                            <tr>
                                {{-- inside_and_outside_division --}}
                                <td>
                                    <dl class="formsetBox">
                                        <dt class="">内外区分</dt>
                                        <dd>
                                            <p class="formPack fixedWidth" style="width: 100px;">
                                                <select name="inside_and_outside_division" class="classic">
                                                    <option value="1" @if($inside_and_outside_division === '1') selected @endif>社内</option>
                                                    <option value="2" @if($inside_and_outside_division === '2') selected @endif>社外</option>
                                                </select>
                                            </p>
                                            <div class="error_msg"></div>
                                        </dd>
                                    </dl>
                                </td>
                                {{-- delete_flag --}}
                                <td style="text-align:left">
                                    <dl class="formsetBox">
                                        <dt class="">有効/無効</dt>
                                        <dd>
                                            <p class="formPack fixedWidth ">
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
                        <a id="processDownloadCSV" href="#" class="btn btn-success btn-wide float-right">検索結果をEXCEL出力</a>
                        <div class="text-center">
                            <a href="{{ route("master.processes.index") }}" class="btn btn-primary btn-wide js-btn-reset-reload">検索条件をクリア</a>
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
                    @if (count($processes) > 0)
                        <li>{{ $processes->total() }}件中、{{ $processes->firstItem() }}件～{{ $processes->lastItem() }} 件を表示してます</li>
                    @else
                        <li></li>
                    @endif
                    <li>
                        <a href="{{ route("master.process.createOrUpdate") }}" class="btn btn-primary">
                            新規登録
                        </a>
                    </li>
                </ul>
                <table class="tableBasic list-table">
                    <tbody>
                    <tr>
                        <th>工程コード</th>
                        <th>工程名</th>
                        <th>操作</th>
                    </tr>
                    @if ($processes != [])
                        @foreach($processes as $process)
                            <tr>
                                <td style="text-align: center">{{ $process->process_code }}</td>
                                <td style="text-align: center">{{ $process->process_name }}</td>
                                <td style="text-align: center">
                                    <a class="buttonBasic bColor-ok" href="{{ route("master.process.createOrUpdate", $process) }}">
                                        編集
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    @endif

                    </tbody>
                </table>
                @if ($processes)
                {{ $processes->links() }}
                @endif

            </div>
        </div>
    </div>
@endsection
@push('scripts')
    @vite(['resources/js/master/process/data-form.js'])
@endpush
