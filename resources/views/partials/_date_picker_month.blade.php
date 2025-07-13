@php
    $inputName = isset($inputName) ? $inputName : '';
    $value = isset($value) ? $value : '';
    $isArray = isset($isArray) ? $isArray : false;
    $inputName = $isArray ? $inputName.'[]' : $inputName;
@endphp
    <input type="text" 
            minlength="8"
            maxlength="8"
            class="pickerJS w-50"
           id="{{ $inputName }}"
           data-format="YYYYMM"
           data-value="{{ request()->get($inputName) }}"
           value="{{ $value }}"
           name="{{ $inputName }}" 
           pattern="\d*" 
           oninput="this.value = this.value.replace(/[^0-9]/g, '')"
         required>
    <button type="button" class="ml-2 btnSubmitCustom buttonPickerJS"
            data-target="{{ $inputName }}"
            data-format="YYYYMM">
        <img src="{{ asset('images/icons/iconsvg_calendar_w.svg') }}"
             alt="iconsvg_calendar_w.svg">
    </button>
@if (isset($targetFormat))
<script>
    var dateweek = '{{ $targetFormat }}';
    var target = '{{ $targetFormat }}';
</script>
@endif
