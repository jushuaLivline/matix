@php
    $inputName = isset($inputName) ? $inputName : '';
    $value = isset($value) ? $value : '';
    $isArray = isset($isArray) ? $isArray : false;
    $inputName = $isArray ? $inputName.'[]' : $inputName;
    $dateFormat = isset($dateFormat) ? $dateFormat : 'YYYYMMDD';
    $inputWidth = isset($input_width) ? $input_width : ''; 

    // Convert the 'data-value' from request to proper date format for pickerJS
    $formattedValue = '';
    if (!empty(request()->get($inputName))) {
        $formattedValue = \Carbon\Carbon::createFromFormat('Ymd', request()->get($inputName));
    }
@endphp
   
    <input type="text"
            minlength="8"
            maxlength="8"
            class="pickerJS"
            id="{{ $inputName }}"
            data-format="{{ $dateFormat }}"
            data-value="{{ $formattedValue }}"
            value="{{ $value }}"
            name="{{ $inputName }}"
            pattern="\d*"
            oninput="this.value = this.value.replace(/[^0-9]/g, '')"
            @if(isset( $inputWidth))
                style="width: {{ $inputWidth }}rem;"
            @endif
            @if(isset($required))
                @if($required != false)
                required
                @endif
            @else
                required
            @endif>
 
    <div class="formPack fixedWidth fpfw25p">
        <button type="button" class="btnSubmitCustom buttonPickerJS ml-1"
                data-target="{{ $inputName }}"
                data-format="{{ $dateFormat }}">
            <img src="{{ asset('images/icons/iconsvg_calendar_w.svg') }}"
                alt="iconsvg_calendar_w.svg">
        </button>
    </div>
@if (isset($targetFormat))
<script>
    var dateweek = '{{ $targetFormat }}';
    var target = '{{ $targetFormat }}';
</script>
@endif
