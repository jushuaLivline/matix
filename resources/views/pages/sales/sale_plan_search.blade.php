@extends('layouts.app')

@push('styles')
    @vite('resources/css/modals/index.css')
    @vite('resources/css/search-modal.css')
    @vite('resources/css/index.css')
    @vite('resources/css/sales/sale_plan_search.css')
@endpush

@section('title', '販売計画表検索・一覧')
@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="accordion">
                <h1><span>販売計画表検索・一覧</span></h1>
            </div>

            <div class="pagettlWrap">
                <h1><span>検索</span></h1>
            </div>

            <form accept-charset="utf-8" class="overlayedSubmitForm" data-disregard-empty="true" id="sale_plan_form">
                <div class="tableWrap borderLesstable inputFormArea">
                    <table class="tableBasic tableBasicPlan">
                        <tbody>
                        <!-- 支給日 -->
                        <td class="w-15">
                            <dl class="formsetBox">
                                <dt class="requiredForm">年月</dt>
                                <dd>
                                    <input class="width-auto" type="text" placeholder="YYYYMM" name="year_month" value="{{ request()->year_month }}">
                                    <div class="error_msg"></div>
                                </dd>
                            </dl>
                        </td>
                        <td class="w-30">
                            <dl class="formsetBox">
                                <dt class="requiredForm">内訳</dt>
                                <dd>
                                    <label class="radioBasic mr-3">
                                        <input type="radio" name="type" value="1" {{ (request()->type ?? 1) == 1 ? 'checked' : '' }}>
                                        <span>得意先別</span>
                                    </label>
                                    <label class="radioBasic mr-3">
                                        <input type="radio" name="type" value="2" {{ (request()->type ?? 1) == 2 ? 'checked' : '' }}>
                                        <span>課別</span>
                                    </label>
                                    <label class="radioBasic mr-3">
                                        <input type="radio" name="type" value="3" {{ (request()->type ?? 1) == 3 ? 'checked' : '' }}>
                                        <span>組別</span>
                                    </label>
                                    <label class="radioBasic mr-3">
                                        <input type="radio" name="type" value="4" {{ (request()->type ?? 1) == 4 ? 'checked' : '' }}>
                                        <span>ライン別</span>
                                    </label>
                                    <label class="radioBasic mr-3">
                                        <input type="radio" name="type" value="5" {{ (request()->type ?? 1) == 5 ? 'checked' : '' }}>
                                        <span>全ライン別</span>
                                    </label>
                                </dd>
                            </dl>
                        </td>
                        <!-- 支給先 -->
                        <td class="w-30">
                            <dl class="formsetBox">
                                <dt>部門</dt>
                                <dd>
                                    <p class="formPack fixedWidth w-15">
                                        <input type="text" name="department_code" value="" class="tA-ri">
                                    </p>
                                    <p class="formPack fixedWidth fpfw50 box-middle-name">
                                        <input type="text" readonly
                                               name="department_name"
                                               value=""
                                               class="middle-name">
                                    </p>
                                    <p class="formPack fixedWidth fpfw25p">
                                        <button type="button" class="btnSubmitCustom js-modal-open"
                                                data-target="searchDepartmentModal">
                                            <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                 alt="magnifying_glass.svg">
                                        </button>
                                    </p>
                                    <div class="error_msg"></div>
                                </dd>
                            </dl>
                        </td>
                        </tbody>
                    </table>
                    <button id="export-excel" type="button" class="btn btn-success btn-wide float-right">
                        検索結果をEXCEL出力
                    </button>
                    <div class="text-center">
                        <a href="{{ route("sales.salePerformanceSearch") }}" class="btn btn-primary btn-wide">検索条件をクリア</a>
                        <button type="submit" class="btn btn-primary btn-wide">検索</button>
                    </div>
                    {{-- <div class="btnListContainer">
                        <div class="btnContainerMain">
                            <div class="btnContainerMainLeft">
                                <input type="button" value="検索条件をクリア"
                                       class="btn-reset buttonBasic bColor-ok js-btn-reset">
                                <input type="submit" value="検索"
                                       class="buttonBasic bColor-ok">
                            </div>
                            <div class="btnContainerMainRight">
                                <input type="submit" value="検索結果をEXCEL出力"
                                       class="btnExport mr-2">
                            </div>
                        </div>
                    </div> --}}
                </div>
            </form>

            <div class="pagettlWrap mt-2">
                <h1><span>検索結果</span></h1>
            </div>
            <div class="tableWrap bordertable" style="clear: both;">
                <div class="mb-2">
                    @if ($sales)
                        {{ $sales->total()  }}件中、{{ $sales->firstItem()  }}件～{{ $sales->lastItem()  }}件を表示してます
                    @endif
                    <table class="table table-bordered text-center table-striped-custom">
                        <thead>
                        <tr>
                            @if((request()->type ?? 1) == 2 || (request()->type ?? 1) == 3)
                                <th rowspan="2">部門CD</th>
                                <th rowspan="2">部門名</th>
                            @elseif((request()->type ?? 1) == 4 || (request()->type ?? 1) == 5)
                                <th rowspan="2">ラインCD</th>
                                <th rowspan="2">ライン名</th>
                            @else
                                <th rowspan="2">得意先名</th>
                                <th rowspan="2">得意先工場</th>
                            @endif
                            <th rowspan="2">売上高 </br> A</th>
                            <th rowspan="2">売上高比率(%)</th>
                            <th rowspan="2">支給材料費 </br> B</th>
                            <th rowspan="2">購入材料費 </br> C</th>
                            <th rowspan="2">加工費 </br> A-B-C＝D</th>
                            <th rowspan="2">加工費比率(%)</th>
                            <th rowspan="2">外注加工費 </br> E</th>
                            <th rowspan="2">付加価値 </br> D-E=F</th>
                            <th rowspan="2">付加価値比率(%) </br> F/A</th>
                        </tr>
                        </thead>
                        <tbody>
                            @php
                                $sum_d = $sum_a - $sum_b - $sum_c;
                                $total_a = 0;
                                $total_b = 0;
                                $total_c = 0;
                                $total_d = 0;
                                $total_e = 0;
                                $total_f = 0;
                            @endphp
                            @forelse($sales ?? [] as $data)
                            @php
                                $a = $data->price_a;
                                $b = $data->price_b;
                                $c = $data->price_c;
                                $e = $data->price_e;
                                $d = $a - $b - $c;
                                $f = $d - $e;
                        
                                $total_a += $a;
                                $total_b += $b;
                                $total_c += $c;
                                $total_d += $d;
                                $total_e += $e;
                                $total_f += $f;
                            @endphp
                            <tr>
                                @if ((request()->type ?? 1) == 1)
                                    <td class="tA-le">{{ $data->supplier_name_abbreviation }}</td>
                                @elseif ((request()->type ?? 1) == 2 || (request()->type ?? 1) == 3)
                                    <td class="tA-le">{{ $data->code }}</td>
                                @else
                                    <td class="tA-le">{{ $data->line_code }}</td>
                                @endif
                                @if ((request()->type ?? 1) == 4 || (request()->type ?? 1) == 5)
                                <td class="tA-le">{{ $data->line_name }}</td>
                                @elseif ((request()->type ?? 1) == 1)
                                <td class="tA-le">{{ $data->branch_factory_name }}</td>
                                @else
                                <td class="tA-le">{{ $data->name }}</td>
                                @endif
                                
                                <td class="tA-ri">{{ number_format($a ?? 0, 0) }}</td>
                                <td class="tA-ri">{{ number_format((($sum_a > 0) ? (($a / $sum_a) * 100) : 0), 2, '.', '') }}</td>
                                <td class="tA-ri">{{ number_format($b ?? 0, 0) }}</td>
                                <td class="tA-ri">{{ number_format($c ?? 0, 0) }}</td>
                                <td class="tA-ri">{{ number_format(($a - $b - $c), 0) }}</td>
                                <td class="tA-ri">{{ number_format((($sum_d > 0) ? (($d / $sum_d) * 100) : 0), 2, '.', '') }}</td>
                                <td class="tA-ri">{{ number_format($e ?? 0, 0) }}</td>
                                <td class="tA-ri">{{ number_format($f ?? 0, 0) }}</td>
                                <td class="tA-ri">{{ number_format((($a > 0) ? (($f / $a) * 100) : 0), 2, '.', '') }}</td>
                            </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="text-center">検索結果はありません</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2" class="text-center">合計</td>
                                <td class="tA-ri">{{ number_format($total_a, 0) }}</td>
                                <td class="tA-ri"></td>
                                <td class="tA-ri">{{ number_format($total_b, 0) }}</td>
                                <td class="tA-ri">{{ number_format($total_c, 0) }}</td>
                                <td class="tA-ri">{{ number_format($total_d, 0) }}</td>
                                <td class="tA-ri"></td>
                                <td class="tA-ri">{{ number_format($total_e, 0) }}</td>
                                <td class="tA-ri">{{ number_format($total_f, 0) }}</td>
                                <td class="tA-ri">{{ number_format((($total_a > 0) ? (($total_f / $total_a) * 100) : 0), 2, '.', '') }}</td>
                            </tr>
                        </tfoot>                        
                    </table>
                    @if ($sales)
                        {{ $sales->appends(request()->input())->links() }}
                    @endif
                </div>
            </div>
        </div>
    </div>
{{--    @include('pages.customers._search')--}}
    @include('partials.modals.masters._search', [
        'modalId' => 'searchDepartmentModal',
        'searchLabel' => '部門',
        'resultValueElementId' => 'department_code',
        'resultNameElementId' => 'department_name',
        'model' => 'Department'
    ])
@endsection
@push('scripts')
    @vite(['resources/js/sales/sales-plan.js'])
    <script>
        $('#sale_plan_form').validate({
            rules: {
                year_month: {
                    required: true
                },
            },
            messages: {
                year_month: {
                    required: '入力してください'
                },
            },
            errorElement : 'div',
            errorPlacement: function(error, element) {
                $(element).siblings('div').html(error);
            },
            invalidHandler: function(event, validator) {
                setInterval(() => {
                    $('.submit-overlay').css('display', "none");
                }, 0);
            }
        })
    </script>
@endpush
