@extends('layouts.app')

@push('styles')
    @vite('resources/css/estimates/answer_data_form.css')
    @vite('resources/css/estimates/index.css')
@endpush

@section('title', 'ダッシュボード')
@section('content')
    @if (\Session::has('remember'))
        <input type="hidden" name="remember" id="remember" value="{!! \Session::get('remember') !!}" data-value="{{ request()->user()->employee_code }}">
    @endif
    <!-- inputs here -->
@endsection

@push('scripts')
    <script>
        if ($('#remember').length) {
            if ($('#remember').val() == '1') {
                localStorage.setItem('saved_user_id', $('#remember').attr('data-value'));
            } else {
                localStorage.removeItem('saved_user_id')
            }
        }
    </script>
@endpush