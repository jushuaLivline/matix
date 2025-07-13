@php
    $inputName = isset($inputName) ? $inputName : '';
    //$value = request()->input($inputName) ? request()->input($inputName) : (isset($value) ? $value : '');
    $value = isset($value) && $value !== '' ? $value : (request()->input($inputName) ?? '');
    $isArray = isset($isArray) ? $isArray : false;
    $inputName = $isArray ? $inputName.'[]' : $inputName;
    $dateFormat = isset($dateFormat) ? $dateFormat : 'YYYYMMDD';
    $minlength = isset($minlength) ? $minlength : 8;
    $maxlength = isset($maxlength) ? $maxlength : 8;
    $isRequired = isset($required) && $required !== false; // on blade add 'required' => true, this for optional 
    $attributes = isset($attributes) ? $attributes :  "";
    $disablePrevDates = isset($disabledPreviousDates) && $disabledPreviousDates == 1 ? "true" : "false";
    $disableDates = isset($disableDates) && $disableDates == 1 ? "true" : "false";
    $isEditableField = (isset($isEditable) && $isEditable == 1) ? $isEditable : "";
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
    $input = request()->get($inputName);

    if (!empty($input)) {
        try {
            $formattedValue = \Carbon\Carbon::createFromFormat($phpFormat, $input);
        } catch (\Exception $e) {
            // Invalid date format, handle accordingly
            $formattedValue = null;
        }
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
        @if(!isset($onInput) || $onInput)
            oninput="this.value = this.value.replace(/[^0-9]/g, '')"
        @endif

        style="{{ $inputStyle ?? '' }}"

        @if (!$isEditableField && !empty($isEditableField)) disabled @endif
        
        @if($disablePrevDates == 'true')
          data-validate-past-date="true"
        @endif
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
    <button type="button" class="btnSubmitCustom buttonPickerJS ml-2 {{ $buttonClass ?? '' }}"
        data-target="{{ $inputName }}"
        data-format="{{ $dateFormat }}"
        data-disable-previous-dates = "{{ $disablePrevDates }}"
        data-disable-dates = "{{ $disableDates }}"
        @if (!$isEditableField && !empty($isEditableField)) disabled @endif
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