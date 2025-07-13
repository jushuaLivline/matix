{{-- REPLICATE FROM _date_picker.blade.php --}}

@php
    $inputName = isset($inputName) ? $inputName : '';
    $value = request()->input($inputName) ? request()->input($inputName) : (isset($value) ? $value : '');
    $isArray = isset($isArray) ? $isArray : false;
    $inputName = $isArray ? $inputName.'[]' : $inputName;
    $dateFormat = 'YYYYMM'; // Don't change the format since this is only used for Year and month
    $minlength = isset($minlength) ? $minlength : 8;
    $maxlength = isset($maxlength) ? $maxlength : 8;
    $isRequired = isset($required) && $required !== false; // on blade add 'required' => true, this for optional 
    $attributes = isset($attributes) ? $attributes :  "";
    $date_start  =   now()->startOfMonth()->format('Ymd') ;
    $date_end  =  now()->endOfMonth()->format('Ymd');

    $phpFormat = match($dateFormat) {
        'YYYYMMDD' => 'Ymd',
        'YYYY-MM-DD' => 'Y-m-d',
        'YYYYMM' => 'Ym',
        'YYYYDD' => 'Yd',
        default => 'Ymd'
    };
    // Convert the 'data-value' from request to proper date format for pickerJS
    $formattedValue = '';
    if (!empty(request()->get($inputName))) {
        $formattedValue = \Carbon\Carbon::createFromFormat($phpFormat, request()->get($inputName));
    }
@endphp

    <input type="text"
        {{ $attributes }}
        data-validate-date-format="{{ $dateFormat }}"
        minlength="{{$minlength}}"
        maxlength="{{$maxlength}}"
        class="pickerJS w-50 {{ $inputClass ?? '' }}"
        id="{{ $inputName }}"
        data-format="{{ $dateFormat }}"
        data-value="{{ $formattedValue }}"
        old="{{$old ?? ''}}"
        value="{{ $value }}"
        name="{{ $inputName }}"
        pattern="\d*"
        oninput="this.value = this.value.replace(/[^0-9]/g, '')"
        style="{{ $inputStyle ?? '' }}"
        
      
        @if($isRequired)
            required
        @endif
        @if($isDisabled ?? false)
            disabled
        @endif


        @if($enableDateStart ?? false)
        data-date-start="{{ $date_start }}"
        @endif
        @if($enableDateEnd ?? false)
        data-date-end="{{ $date_end }}"
        @endif
        />
    <button type="button" class="btnSubmitCustom year-month-date-picker ml-2 {{ $buttonClass ?? '' }}"
        data-target="{{ $inputName }}"
        data-format="{{ $dateFormat }}"
        @if($isDisabled ?? false)
            disabled
        @endif
        style="{{ $buttonStyle ?? '' }}">
        
        <img src="{{ asset('images/icons/iconsvg_calendar_w.svg') }}"
            alt="iconsvg_calendar_w.svg">
    </button>

@if (isset($targetFormat))
<script>
    var dateweek = '{{ $targetFormat }}';
    var target = '{{ $targetFormat }}';
</script>
@endif

 <!-- Custom Modal for Month Picker -->
 <div id="calendarModal" class="calendar-modal" style="display: none;">
    <div class="calendar-content">
        <span class="close-modal" onclick="closeCalendarModal()">&times;</span>
        <!-- Year Adjustment Controls -->
        <div class="year-controls">
            <button class="year-btn" onclick="changeYear(-1, event)">&#9664;</button>
            <h3 id="calendarYear"></h3>
            <button class="year-btn" onclick="changeYear(1, event)">&#9654;</button>
        </div>

        <div id="calendar" class="calendar-grid"></div>
    </div>
</div>

@push('styles')
@vite('resources/css/purchase/purchase_amount_search.css')
@endpush
@push('scripts')
@vite(['resources/js/purchase/purchase_amount_search.js'])
@endpush

