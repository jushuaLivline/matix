@extends('layouts.app')

@push('styles')
    @vite('resources/css/index.css')
    @vite('resources/css/modals/index.css')
    @vite('resources/css/sales/sale_plan_search.css') 
@endpush
@section('title', '下請け供給エントリ')

@php
    $isEditable = request('edit', 'false') === 'true'; // Check if the `edit` param is set to true   
@endphp

@section('content')
<div class="content">
    <div class="contentInner">
        <div class="accordion">
            <h1><span>依頼内容編集</span></h1>
        </div>
        @if(session('success'))
            <div id="card" style="background-color: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);margin-top: 20px;">
                <div style="text-align: left;">
                    <p style="font-size: 18px; color: #0d9c38">
                        {{ session('success') }}
                    </p>
                </div>
            </div>
        @endif

        <div class="pagettlWrap">
            <h1><span>依頼内容編集</span></h1>
        </div>

        <form action="{{ route('outsource.supplyUpdate', $subcontract_supply->id) }}" method="POST" id="supplyForm" class="with-js-validation">
            @csrf
            @method('PUT')
            
            <div class="box mb-4">
                <div class="mb-4 d-flex-col space-y-4">
                    <div class="mb-4">
                        <label class="form-label dotted indented">支給No.</label>
                        <div class="d-flex">{{ $subcontract_supply->subcontract_supply_no }}</div>
                    </div>
                    <div class="mb-4 d-flex">
                        <div class="w-10">
                            <label class="form-label dotted indented">支給日</label>
                            <div class="d-flex">{{ \Carbon\Carbon::parse($subcontract_supply->supply_date)->format('Y-m-d') }}</div>
                        </div>
                        <div class="">
                            <label class="form-label dotted indented">便No.</label>
                            <div class="d-flex">{{ $subcontract_supply->supply_flight_no }}</div>
                        </div>
                    </div>
                    <div class="mb-4 d-flex">
                        <div class="w-10">
                            <label class="form-label dotted indented">管理No.</label>
                            <div class="d-flex">{{ $subcontract_supply->management_no }}</div>
                        </div>
                        <div class="">
                            <label class="form-label dotted indented">枝番</label>
                            <div class="d-flex">{{ $subcontract_supply->branch_number }}</div>
                        </div>
                    </div>
                    <div class="mb-4 d-flex">
                        <div class="w-10">
                            <label class="form-label dotted indented">製品品番</label>
                            <div class="d-flex">{{ $subcontract_supply->product_code }}</div>
                        </div>
                        <div class="">
                            <label class="form-label dotted indented">品名</label>
                            <div class="d-flex">{{ optional($subcontract_supply->product_number)->product_name }}</div>
                        </div>
                    </div>
                    <div class="mb-4 d-flex">
                        <div class="w-10">
                            <label class="form-label dotted indented">支給先コード</label>
                            <div class="d-flex">{{ $subcontract_supply->supplier_process_code }}</div>
                        </div>
                        <div class="">
                            <label class="form-label dotted indented">仕入先名</label>
                            <div class="d-flex">{{ optional($subcontract_supply->customer)->customer_name }}</div>
                        </div>
                    </div>
                    <div class="mb-4 d-flex">
                        <div class="w-10">
                            <label class="form-label dotted indented">背番号</label>
                            <div class="d-flex">{{optional($subcontract_supply->product_number)->uniform_number}}</div>
                        </div>
                        <div class="">
                            <label class="form-label dotted indented">サイクル</label>
                            <div class="d-flex"></div>
                        </div>
                    </div>
                    <div class="mb-4 d-flex">
                        <div class="w-30">
                            <label class="form-label dotted indented">枚数</label> <span class="others-frame btn-orange badge">必須</span>
                            <div class="d-flex">
                                <input type="text" name="supply_kanban_quantity"
                                    class="acceptNumericOnly"
                                    value="{{$subcontract_supply->supply_kanban_quantity}}">
                            </div>
                            @error('supply_kanban_quantity')
                                <div class="error_msg text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="w-30">
                            <label class="form-label dotted indented">収容数</label> <span class="others-frame btn-orange badge">必須</span>
                            <div class="d-flex">
                                <input type="text" name="number_of_accomodated"
                                    class="acceptNumericOnly"
                                    value="{{optional($subcontract_supply->kanban)->number_of_accomodated}}">
                            </div>
                            @error('number_of_accomodated')
                                <div class="error_msg text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="w-30">
                            <label class="form-label dotted indented">数量</label> <span class="others-frame btn-orange badge">必須</span>
                            <div class="d-flex">
                                <input type="text" name="supply_quantity"
                                    class="acceptNumericOnly"
                                    value="{{$subcontract_supply->supply_quantity}}">
                            </div>
                            @error('supply_quantity')
                                <div class="error_msg text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            

            <div class="btnListContainer">
                <div class="justify-content-flex-end">
                    <div class="btnContainerMainRight">
                        <a href="/outsource/supply" class="btn btn-blue btn-primary">一覧に戻る</a>
                        <button type="submit" class="btn btn-green" onclick="return confirm('下請け供給を更新してもよろしいですか?');">この内容で更新する </button>
                    </div>
                </div>
            </div>
        </form>

    </div>
</div>
@endsection 