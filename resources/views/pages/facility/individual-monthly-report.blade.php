@extends('layouts.app')

@push('styles')
    @vite('resources/css/index.css')
    @vite('resources/css/modals/index.css')
    @vite('resources/css/search-modal.css')
    {{-- @vite('resources/css/shipments/shipment_result_search.css') --}}
@endpush

@section('title', '個人別作業月報')
@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="pageHeaderBox rounded">
                個人別作業月報
            </div>

            <div class="section">
                <h1 class="form-label bar indented">検索</h1>
                <form accept-charset="utf-8" class="overlayedSubmitForm" data-disregard-empty="true">
                <div class="box mb-3">
                    <div class="mb-2 d-flex">
                        <div class="mr-3">
                            <label class="form-label dotted indented">作業年月</label>
                            <div class="d-flex">
                                @include('partials._date_picker_month', ['value' => request()->year_month,'inputName' => 'year_month', 'dateFormat' => 'YYYYMM'])
                            </div>
                        </div>

                        <div class="mr-3">
                            <label class="form-label dotted indented">作業者</label>
                            <div class="d-flex">
                                <p class="formPack fixedWidth fpfw25p">
                                    <input type="text" id="employee_code" name="employee_code" value="" style="width: 100px;" class="mr-25">
                                </p>
                                <p class="formPack fixedWidth fpfw25p">
                                    <input type="text" id="employee_name" name="employee_name" disabled class="mr-25">
                                </p>
                                <p class="formPack fixedWidth fpfw25p">
                                    <button type="button" class="btnSubmitCustom js-modal-open"
                                            data-target="searchEmployeeModal">
                                        <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                alt="magnifying_glass.svg">
                                    </button>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="text-center sc relative">
                        <!-- <a href="#" class="btn btn-success btn-wide absolute-right">検索結果をEXCEL出力</a> -->
                        <a class="btn btn-primary btn-wide" href="{{ route('facility.work.list') }}">検索条件をクリア</a>
                        <button class="btn btn-primary btn-wide">検索</button>
                    </div>
                </div>
                </form>
                
            </div>

            <div class="section">
                
                <h1 class="form-label bar indented">検索結果</h1>
                <div class="box">
                    <div class="mb-3 relative">
                        @if ($prev_month != '' && $next_month != '')
                            <a class="btn btn-primary" href="{{ route('facility.work.list') }}?year_month={{$prev_month}}"><前日</a>
                            <a class="btn btn-primary" href="{{ route('facility.work.list') }}?year_month={{$next_month}}">翌日></a>
                        @endif
                    </div>
                    <div>
                        <table class="table table-bordered text-center table-striped" style="margin-bottom: 0">
                            <thead>
                            <tr>
                                <th rowspan="2" width="50">日</th>
                                <th rowspan="2" width="50">曜日</th>
                                <th colspan="4">作業時間(H)</th>
                                <th rowspan="2">勤務区分</th>
                                <th rowspan="2">残業時間</th>
                                <th rowspan="2">備考</th>
                                <th rowspan="2" width="100">操作</th>
                            </tr>
                            <tr>
                                <th>プロジェクト</th>
                                <th>ライン</th>
                                <th>その他</th>
                                <th>合計</th>
                            </tr>
                            </thead>
                            <tbody>
                                @forelse ($rows as $row)
                                <tr>
                                    <td>{{ $row['day'] }}</td>
                                    <td>{{ $row['day_of_the_week'] }}</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td><a class="btn btn-primary" href="javascript:void(0)">詳細</a></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center">検索結果はありません</td>
                                </tr>
                                @endforelse
                                
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- @include('partials._pagination') -->
            </div>
        </div>
    </div>
    @include('partials.modals.masters._search', [
        'modalId' => 'searchEmployeeModal',
        'searchLabel' => '作業者',
        'resultValueElementId' => 'employee_code',
        'resultNameElementId' => 'employee_name',
        'model' => 'Employee'
    ])
@endsection

