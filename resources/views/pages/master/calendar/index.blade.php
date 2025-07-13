@extends('layouts.app')

@push('styles')
    @vite('resources/css/estimates/index.css')
    @vite('resources/css/estimates/data_list.css')
    @vite('resources/css/search-modal.css')
    @vite('resources/css/index.css')
    @vite('resources/css/master/product.css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css" />
    @vite('resources/css/master/calendar/index.css')
@endpush

@section('title', '休日一覧')

@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="accordion mb-4">
                <h1><span>休日一覧</span></h1>
            </div>
            <div class="outsideDiv ">
                <div class="calendarContainer">
                    <span class="label-top">※休日をクリックしてください</span>
                    <div id='calendar'></div>
                </div>

                <div class="buttonRow">
                    <div class="info" id="info-div">
                        <div>
                            <label for="total_working_days">年間出勤日数：</label>
                            <input type="text" name="total_working_days" id="total_working_days" readonly>
                        </div>
                        <div>
                            <label for="actual_working_hours">年間実労働時間：</label>
                            <input type="text" name="actual_working_hours" id="actual_working_hours" readonly>
                        </div>
                        <div>
                            <label for="total_holidays">年間休日日数：</label>
                            <input type="text" name="total_holidays" id="total_holidays" readonly>
                        </div>
                    </div>
                    
                </div>
            </div>
            <div class="saveAllDiv">
                <div>
                    <button type="button" id="saveAll" class="btn btn-success btn-wide" >
                        登録する
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/6.1.8/index.global.min.js'></script>
    @vite(['resources/js/master/calendar/index.js'])
@endpush
