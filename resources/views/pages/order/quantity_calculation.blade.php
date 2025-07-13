@extends('layouts.app')

@push('styles')
    @vite('resources/css/order/style.css')
@endpush

@section('title', '所要量計算')	
@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="pageHeaderBox rounded">
                所要量計算
            </div>
            @if (session('success'))
                <div id="card" style="background-color: #ffff; padding: 20px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); margin: 20px 0;">
                    <div style="text-align: left;">
                        <p style="font-size: 18px; color: #0d9c38">
                            {{ session('success') }}
                        </p>
                    </div>
                </div>
                @php
                    session()->forget('success');
                @endphp
            @endif
            <div class="section">
                <h1 class="form-label bar indented">所要量計算</h1>
                <div class="box">
                    <label class="mb-2">指定した年月の所要量計算を行います（内示データから販売計画データが作成されます）</label>
                    <div style="display:flex">
                        <div class="mr-2">
                            <label class="form-label dotted indented">年月</label> <span class="btn-orange badge">必須</span>
                            <div>
                                <form id="form" class="overlayedSubmitForm" action="{{ route("order.quantity.calculation.post") }}" id="form" method="POST">
                                    @csrf
                                    <input type="text" required  name="month" value="{{ old('month') ?? now()->format('Ym') }}" placeholder="YYYYMM" style="width:150px">
                                    <div class="error_msg"></div>
                                </form>
                            </div>
                        </div>
                    </div>
            </div>
            <div class="float-right mt-3">
                <button type="submit" form="form" @disabled($setting->number_1 == 1) class="btn btn-success btn-wide">処理実行</button>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $('#form').validate({
        rules: {
            month: {
                required: true
            },
        },
        messages: {
            month: {
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

