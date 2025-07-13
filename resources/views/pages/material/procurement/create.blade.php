@extends('layouts.app')

@push('styles')
    @vite('resources/css/materials/form_procurement_plan.css')
    @vite('resources/css/index.css')
@endpush

@section('title', '指示部品内示情報入力')
@section('content')
@php
    $yearMonth = request()->input('year_month');
    $dateInput = $yearMonth ? \Carbon\Carbon::createFromFormat('Ym', $yearMonth)->startOfMonth() : \Carbon\Carbon::now();
    $firstDateOfMonth = $dateInput->copy()->startOfMonth();
    $lastDateOfMonth = $dateInput->copy()->endOfMonth();
@endphp
    <div class="content">
        <div class="contentInner">
            <div class="pageHeaderBox rounded">
                材料調達計画入力
            </div>

            <div class="section">
                <h1 class="form-label bar indented">基本情報</h1>
                <div class="box mb-3">
                    <div class="mb-2" style="display:flex">
                        <div style='margin-right:2rem'>
                            <label class="form-label dotted indented">年月</label>
                            <div>
                                <input type="text" style="width:80px" disabled value="{{ Request::get('year_month') }}">
                            </div>
                        </div>
                        <div style='margin-right:2rem'>
                            <label class="form-label dotted indented">材料メーカー</label>
                            <div>
                                <input type="text" class="w-170c"  name="process_code" disabled value="{{ Request::get('process_code') }}">
                                <input type="text" class="w-300c" name="process_name" value="{{ Request::get('process_name') }}" disabled>
                            </div>
                        </div>
                        <div style='margin-right:3rem'>
                            <label class="form-label dotted indented">材料品番</label>
                            <div>
                                <input type="text" class="w-170c" name="product_code" value="{{ Request::get('part_number') }}" disabled>
                                <input type="text" class="w-300c"  name="product_name" value="{{ $product?->product_name }}" disabled>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        
            @php
                $procurement = $product?->supplyMaterialOrders->first();
                $formAction = $procurement
                    ? route('material.procurement.store', array_merge([$procurement->id], request()->query()))
                    : route('material.procurement.store', request()->query());
                $confirmationMessage = $procurement
                    ? '資材調達計画を更新しますが、よろしいでしょうか？'
                    : '材料調達計画を登録します、よろしいでしょうか？';
                $buttonText = $procurement
                    ? '更新'
                    : 'この内容で登録する';

                $deleteButtonClass = $procurement ? '' : 'btn-disabled';
                $deleteButtonAttribute = $procurement ? '' :  'disabled';

                $counter = 1;
            @endphp
            <form action="{{ $formAction }}" 
                    method="POST"
                    class="with-js-validation"
                    data-confirmation-message="{{ $confirmationMessage }}"
                >
                @csrf
                <input type="hidden" name="material_number" value="{{ $product->part_number }}">
                <input type="hidden" name="supplier_code" value="{{ $product->supplier_code }}">
                <input type="hidden" name="department_code" value="{{ $product->department_code }}">
                <input type="hidden" name="material_manufacturer_code" value="{{ $product->material_manufacturer_code  }}">
                <!-- <input type="hidden" name="year_and_month" value="{{ Request::get('year_month') ?? now()->format('Ym') }}">
                <input type="hidden" name="current_month" value="{{ $product?->salePlans->sum('quantity') ?? 0 }}">
                <input type="hidden" name="next_month" value="{{ $product?->salePlansNextMonth->sum('quantity') ?? 0 }}">
                <input type="hidden" name="two_months_later" value="{{ $product?->salePlansTwoMonthsLater->sum('quantity') ?? 0 }}"> -->
                <input type="hidden" name="order_classification" value="4">
                <input type="hidden" name="instruction_class" value="2">
                <input type="hidden" name="input_category" value="1">

                <input type="hidden" name="creator" value="{{ request()->user()->employee_code }}">

                {{--  
                @if ($procurement)
                    @method('PUT')
                    <input type="hidden" name="updated_at" value="{{ now() }}">
                    <input type="hidden" name="updator" value="{{ request()->user()->employee_code }}">
                @else                
                    <input type="hidden" name="creator" value="{{ request()->user()->employee_code }}">
                @endif
                --}}
                <div class="section">
                    <h1 class="form-label bar indented">調達計画入力</h1>
                    <div class="box" >
                        <div style="width: 90%; display: flex; flex-wrap: wrap;">
                            @for ($date = $firstDateOfMonth; $date <= $lastDateOfMonth; $date->modify('+1 day'))
                                @php
                                    $isWeekend = is_weekend($date->format('Y-m-d'));
                                    $dailyValue = $procurement ? $procurement->{'day_' . $counter} : '';
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
                                            @php
                                                $dateMatches = false;
                                            @endphp
                                            
                                            @foreach ($product?->supplyMaterialOrders as $key => $item)
                                                @if ($item->instruction_date->format('Y-m-d') === $date->format('Y-m-d'))
                                                    <input type="date" name="instruction_date[]" value="{{ $date->format('Y-m-d') }}" hidden/>
                                                    <!-- <input type="text" name="material_number" value="{{ $item?->material_number }}" shidden/> -->
                                                    <!-- <input type="text" name="material_manufacturer_code" value="{{ $item?->material_manufacturer_code }}" hidden/> -->
                                                    <input class="numberCharacter full-width" name="instruction_number[]" type="text" value="{{ $item?->instruction_number }}"
                                                    onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                                    maxlength="20">
                                                    @php
                                                        $dateMatches = true;
                                                    @endphp
                                                @endif
                                            @endforeach
                                            

                                            @if (!$dateMatches)                                          
                                                <input type="date" name="instruction_date[]" value="{{ $date->format('Y-m-d') }}" hidden/>
                                                <!-- <input type="text" name="material_number" value="{{ $product?->part_number }}" shidden/> -->
                                                <!-- <input type="text" name="material_manufacturer_code" value="{{ Request::get('process_code') }}" shidden/> -->
                                                <input class="numberCharacter full-width" name="instruction_number[]" type="text" value="{{  $dailyValue }}"
                                                onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                                maxlength="20">
                                            @endif
                                            
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            @endfor
                        </div>
                    </div>
                    <div class="box" >
                        <div class="boxFooter">
                            <table style="width: 20%; !important" class="table table-bordered table-striped float-right">
                                <thead>
                                <th>計画</th>
                                <th>合計</th>
                                </thead>
                                <tbody>
                                <tr class="full-width">
                                    <td class="bg-white text-right valign-middle" style="max-width:20%;">{{ $product?->current_month ?? 0}}</td>
                                    <td class="bg-white text-right valign-middle" id="total"  style="max-width:150px;">
                                    {{ $product?->supplyMaterialOrders->sum('instruction_number')}}
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="float-right">
                    <button type="button" class="btn px-3 btn-orange deleteRecord {{ $deleteButtonClass }}"
                    {{ $deleteButtonAttribute }}
                    >
                        削　除
                    </button>
                    <button type="submit" href="#" class="btn btn-green"> {{ $buttonText }}</button>
                </div>
            </form>
            <div class="float-left">
                <a href="{{ route('material.procurement.index', request()->query()) }}" onclick="" class="btn px-3 btn-blue">一覧に戻る</a>
            </div>

            <form action="{{ route('material.procurement.destroy', array_merge([$procurement?->id ?? 0], request()->query())) }}" method="POST" id="deleteForm">
                @csrf
                @method('DELETE')
                <input type="hidden" name="material_number" value="{{ $product->part_number }}">
            </form>
        </div>

@endsection
@vite('resources/js/material/procurement/create.js')
