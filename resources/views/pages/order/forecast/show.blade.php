@extends('layouts.app')

@push('styles')
    @vite('resources/css/order/style.css')
@endpush

@section('title', '内示情報詳細表示')	
@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="pageHeaderBox rounded">
                内示情報詳細表示
            </div>

            <form action="{{ route('order.forecast.update', array_merge(['id'=> 1], request()->all())) }}" method="POST">
                @csrf
                @method("PUT")
                <input type="hidden" name="updator" value="{{  request()->user()->employee_code }}">
                <input type="hidden" name="updated_at" value="{{  now()->format(format: 'Y-m-d H:i:s')}}">
                <input type="hidden" name="current_month" class="totalCurrentMonthHidden" value="{{ $unofficialRecord->current_month ?? ''}}">
                <input type="hidden" name="next_month" value="{{ $unofficialRecord->next_month ?? ''}}">
                <input type="hidden" name="two_months_later" value="{{ $unofficialRecord->two_months_later ?? ''}}">

                <div class="section">
                    <h1 class="form-label bar indented">内示情報詳細</h1>
                    <div class="box mb-3">
                        <div style="display:flex">
                            <div class="mb-4 mr-4">
                                <label class="form-label dotted indented">年月</label>
                                <div>
                                    <input type="text" value="{{ $unofficialRecord->year_and_month ?? '' }}" style="width:90px" readonly>
                                </div>
                            </div>
                            <div class="mb-4 mr-4">
                                <div>
                                    <label class="form-label dotted indented">納入先</label>
                                    <div>
                                        <input type="text" value="{{ $unofficialRecord->delivery_destination_code ?? '' }}" readonly style="width:90px">
                                        <input type="text" value="{{ $unofficialRecord?->product?->customer?->customer_name  }}" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-4 mr-4">
                                <div>
                                    <label class="form-label dotted indented">部門</label>
                                    <div>
                                        <input type="text" value="{{ $unofficialRecord?->product?->department->code ?? 'null'}}" readonly style="width:90px">
                                        <input type="text" value="{{ $unofficialRecord?->product?->department->name ?? 'null'}}" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div style="display:flex">
                            <div class="mb-4 mr-4">
                                <div>
                                    <label class="form-label dotted indented">ライン</label>
                                    <div>
                                        <input type="text" value="{{ $unofficialRecord?->product?->line_code ?? 'null'}}" readonly style="width:90px">
                                        <input type="text" value="{{ $unofficialRecord?->product?->line->line_name ?? 'null'}}" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-4 mr-3">
                                @php
                                    $instructionTypes = [
                                        1 => 'かんばん',
                                        2 => '指示',
                                    ];
                                @endphp
                                <div>
                                    <label class="form-label dotted indented">指示区分</label>
                                    <div>
                                        <input type="text" 
                                            value="{{ isset($unofficialRecord->instruction_class) ? $instructionTypes[$unofficialRecord->instruction_class] : 'すべて' }}" 
                                            readonly style="width:90px">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div style="display:flex">
                            <div class="mb-4 mr-4">
                                <div>
                                    <label class="form-label dotted indented">製品品番</label>
                                    <div>
                                        <input type="text" value="{{ $unofficialRecord->product_number ?? 'null'}}" readonly>
                                        <input type="text" value="{{ $unofficialRecord->product?->product_name ?? ''}}" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="mr-2">
                                <div>
                                    <label class="form-label dotted indented">受入</label>
                                    <div>
                                        <input type="text" value="{{ $unofficialRecord->acceptance ?? ''}}" readonly style="width:75px">
                                    </div>
                                </div>
                            </div>
                        </div>
                    
                        @php
                            $yearMonth = request()->input('year_and_month');
                            $dateInput = $yearMonth ? \Carbon\Carbon::createFromFormat('Ym', $yearMonth)->startOfMonth() : \Carbon\Carbon::now();
                            $firstDateOfMonth = $dateInput->copy()->startOfMonth();
                            $lastDateOfMonth = $dateInput->copy()->endOfMonth();
                            $counter = 1;
                        @endphp
                        <div class="section">

                            <div class="mt-5" >
                                <div style="width: 90%; display: flex; flex-wrap: wrap;">
                                    @for ($date = $firstDateOfMonth; $date <= $lastDateOfMonth; $date->modify('+1 day'))
                                        @php
                                            $isWeekend = is_weekend($date->format('Y-m-d'));
                                            $dailyValue =  $unofficialRecord->{'day_'. $counter} ?? '';
                                            $dialyInput =  'day_'. $counter;

                                            $counter++;
                                        @endphp
                                    
                                        <table style="width: 10% !important" class="table table-bordered table-striped">
                                            <thead>
                                                <th class="text-center" style="width: 2%; color: {{ $isWeekend ? 'red' : 'black' }}">
                                                    {{ $date->format('j') }}日
                                                </th>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <input type="text" value="{{  $dailyValue ? number_format($dailyValue) : '' }}" class="text-right dailyValue"
                                                        maxlength="9"
                                                        name="{{  $dialyInput  }}">
                                                    
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    @endfor
                                </div>
                            </div>
                    
                        </div>

                        <div class="box" style="padding-left:0;display:flex;">
                            @php 
                            $subtotal = 
                                    ($unofficialRecord->current_month ?? 0) + 
                                    ($unofficialRecord->next_month ?? 0) + 
                                    ($unofficialRecord->two_months_later ?? 0);
                                    
                                $total =   $daysInThreeMonths > 0 ? floor($subtotal / $daysInThreeMonths + 0.5) : 0 ;
                                   
                            @endphp
                            <table style="width: 10%; !important" class="table table-bordered table-striped mr-2">
                                <thead>
                                    <th>当月</th>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <input type="hidden" id="orig-curr-month" value="{{ number_format($unofficialRecord->current_month ?? 0) }}" >
                                            <input type="text" class="full-width text-right totalCurrentMonth" value="{{ number_format($unofficialRecord->current_month ?? 0) }}" 
                                            readonly
                                            style="border: 0; background: transparent; height: 48px;">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <table style="width: 10%; !important" class="table table-bordered table-striped mr-2">
                                <thead>
                                    <th>翌月</th>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <input type="text" class="full-width text-right totalNextMonth" value="{{ number_format($unofficialRecord->next_month ?? 0 ) }}" 
                                            readonly
                                            style="border: 0; background: transparent; height: 48px;">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <table style="width: 10%; !important" class="table table-bordered table-striped mr-2">
                                <thead>
                                    <th>翌々月</th>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <input type="text" class="full-width text-right totalNextTwoMonth" value="{{ number_format($unofficialRecord->two_months_later ?? 0) }}" 
                                            readonly
                                            style="border: 0; background: transparent; height: 48px;">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <table style="width: 10%; !important" class="table table-bordered table-striped mr-2">
                                <thead>
                                    <th>日当たり数量</th>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <input type="text" class="full-width text-right grandTotal" value="{{ $total }}" 
                                            readonly
                                            data-days-in-three-months = "{{ $daysInThreeMonths }}"
                                                style="border: 0; background: transparent; height: 48px;">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            
                <div class="float-right">
                    <button type="submit" class="btn btn-blue text-white">一覧に戻る</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@vite('resources/js/order/forecast/show.js')
