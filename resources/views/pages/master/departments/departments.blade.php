@extends('layouts.app')

@push('styles')
    @vite('resources/css/estimates/index.css')
    @vite('resources/css/estimates/data_list.css')
    @vite('resources/css/search-modal.css')
    @vite('resources/css/index.css')

@endpush

@section('title', '部門マスタ一覧')
@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="accordion">
                <h1><span>部門マスタ一覧</span></h1>
            </div>

            <div class="pagettlWrap">
                <h1><span>検索</span></h1>
            </div>

            <form action="{{ route('master.departments.search')  }}" method="GET" accept-charset="utf-8" class="overlayedSubmitForm">
                @csrf
                <div class="tableWrap borderLesstable inputFormArea">
                    <table class="tableBasic" style="width: 90%">
                        <tbody>
                            {{-- code --}}
                            <tr>
                                <td>
                                    <dl class="formsetBox fixedWidth">
                                        <dt class="">部門コード</dt>
                                        <p class="formPack">
                                            <input type="text" name="code" value="{{ $code }}" class="" style="width: 200px" >
                                        </p>
                                        <div class="error_msg"></div>
                                    </dl>
                                </td>
                                {{-- name --}}
                                <td>
                                    <dl class="formsetBox fixedWidth">
                                        <dt class="">部門名</dt>
                                        <p class="formPack">
                                            <input type="text" name="name" value="{{ $name }}" class="" style="width: 200px" >
                                        </p>
                                        <div class="error_msg"></div>
                                    </dl>
                                </td>
                                {{-- name_abbreviation --}}
                                <td>
                                    <dl class="formsetBox fixedWidth">
                                        <dt class="">部門名略</dt>
                                        <p class="formPack">
                                            <input type="text" name="name_abbreviation" value="{{ $name_abbreviation }}" class="" style="width: 200px" >
                                        </p>
                                        <div class="error_msg"></div>
                                    </dl>
                                </td>
                                {{-- department_name --}}
                                <td>
                                    <dl class="formsetBox fixedWidth">
                                        <dt class="">部名</dt>
                                        <p class="formPack">
                                            <input type="text" name="department_name" value="{{ $department_name }}" class="" style="width: 200px" >
                                        </p>
                                        <div class="error_msg"></div>
                                    </dl>
                                </td>
                            </tr>
                            <tr>
                                {{-- section_name --}}
                                <td>
                                    <dl class="formsetBox fixedWidth">
                                        <dt class="">課名</dt>
                                        <p class="formPack">
                                            <input type="text" name="section_name" value="{{ $section_name }}" class="" style="width: 200px" >
                                        </p>
                                        <div class="error_msg"></div>
                                    </dl>
                                </td>
                                {{-- group_name --}}
                                <td>
                                    <dl class="formsetBox fixedWidth">
                                        <dt class="">組名</dt>
                                        <p class="formPack">
                                            <input type="text" name="group_name" value="{{ $group_name }}" class="" style="width: 200px" >
                                        </p>
                                        <div class="error_msg"></div>
                                    </dl>
                                </td>
                                {{-- delete_flag --}}
                                <td>
                                    <dl class="formsetBox">
                                        <dt class="">有効/無効</dt>
                                        <dd>
                                            <p class="formPack fixedWidth" style="width: 100px;">
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
                        <a id="departmentDownloadCSV" href="#" class="btn btn-success btn-wide float-right">検索結果をEXCEL出力</a>
                        <div class="text-center">
                            <a href="{{ route("master.departments.index") }}" class="btn btn-primary btn-wide js-btn-reset-reload">検索条件をクリア</a>
                            <button class="btn btn-primary btn-wide" type="submit">検索</button>
                        </div>
                    </div>
                    {{-- <div class="buttonRow" style="display:flex; justify-content: space-between; align-items: center">
                        <ul class="buttonlistWrap" style="flex-grow: 1; display: flex; justify-content: center; margin-left: 35%">
                          <li>
                            <a href="{{ route("master.departments.index") }}" class="buttonBasic btn-reset bColor-ok js-btn-reset-reload">検索条件をクリア</a>
                          </li>
                          <li>
                            <input type="submit" value="検索" class="buttonBasic bColor-ok">
                          </li>
                        </ul>
                        <div class="" style="margin-left: auto">
                            <a id="departmentDownloadCSV" href="#" class="btn bg-light-green">検索結果をEXCEL出力</a>
                        </div>
                    </div> --}}
                </div>
            </form>

            <div class="pagettlWrap">
                <h1><span>検索結果</span></h1>
            </div>
            <div class="tableWrap bordertable" style="clear: both;">
                <ul class="headerList">
                    @if (count($departments) > 0)
                        <li>{{ $departments->total() }}件中、{{ $departments->firstItem() }}件～{{ $departments->lastItem() }} 件を表示してます</li>
                    @else
                        <li></li>
                    @endif
                    <li>
                        <a href="{{ route("master.department.createOrUpdate") }}" class="btn btn-primary">
                            新規登録
                        </a>
                    </li>
                </ul>
                <table class="tableBasic list-table">
                    <tbody>
                    <tr>
                        <th>部門コード</th>
                        <th>部門名</th>
                        <th>部門名略</th>
                        <th>部名</th>
                        <th>課名</th>
                        <th>組名</th>
                        <th>有効/無効</th>
                        <th>操作</th>
                    </tr>
                    @if ($departments != [])
                        @foreach($departments as $department)
                            <tr>
                                <td style="text-align: center">{{ $department->code }}</td>
                                <td style="text-align: center">{{ $department->name }}</td>
                                <td style="text-align: center">{{ $department->name_abbreviation }}</td>
                                <td style="text-align: center">{{ $department->department_name }}</td>
                                <td style="text-align: center">{{ $department->section_name }}</td>
                                <td style="text-align: center">{{ $department->group_name }}</td>
                                <td style="text-align: center">{{ $department->delete_flag ? '無効' : '有効' }}</td>

                                <td style="text-align: center">
                                    <a class="buttonBasic bColor-ok" href="{{ route("master.department.createOrUpdate", $department) }}">
                                        編集
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="8" style="text-align: center">検索結果はありません</td>
                        </tr>
                    @endif

                    </tbody>
                </table>
                @if($departments)
                {{ $departments->links() }}
                @endif
            </div>
        </div>
    </div>

@endsection
@push('scripts')
    @vite(['resources/js/master/departments/data-form.js'])
@endpush
