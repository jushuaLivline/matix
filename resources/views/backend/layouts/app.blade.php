@extends('layouts.app')

@push('styles')
{{-- <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Noto+Sans+JP:300,400,400i,700&display=fallback"> --}}
  <!-- Font Awesome -->
  <link rel="stylesheet" href="/plugins/fontawesome-free/css/all.min.css">
  <!-- daterange picker -->
  {{-- <link rel="stylesheet" href="/plugins/daterangepicker/daterangepicker.css"> --}}
  <!-- iCheck for checkboxes and radio inputs -->
  <link rel="stylesheet" href="/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Tempusdominus Bootstrap 4 -->
  {{-- <link rel="stylesheet" href="/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css"> --}}
  <!-- BS Stepper -->
  {{-- <link rel="stylesheet" href="/plugins/bs-stepper/css/bs-stepper.min.css"> --}}
  <!-- DataTables -->
  <link rel="stylesheet" href="/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  @yield('css')
  <!-- Theme style -->
  <link rel="stylesheet" href="/adminlte/css/adminlte.min.css">
  <!-- styles -->
  @vite('resources/sass/admin.scss')
  <style>
    body {
      font-family: 'Arial','Helvetica Neue','Helvetica','Yu Gothic Medium','YuGothic','Hiragino Kaku Gothic ProN',Meiryo,sans-serif;
      font-size: 1rem;
      background-color: var(--bright-gray);
    }
  </style>
@endpush

@section('content')
<!-- Main content -->
<section class="content">
  <div class="container-fluid">
    @if (isset($breadcrumb))
    <div class="pageHeaderBox rounded">
      {{ isset($breadcrumb['h1']) ? $breadcrumb['h1'] : '' }}
    </div>
    @endif
    @include('partials.alert-message')
    @yield('content_admin')
  </div>
  <!-- /.container-fluid -->
</section>
<!-- /.content -->
@endsection

@push('scripts')
<!-- jQuery -->
<script src="/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
{{-- <script src="/adminlte/js/popper.min.js"></script> --}}
<script src="/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- InputMask -->
<script src="/plugins/moment/moment.min.js"></script>
<script src="/plugins/moment/moment-with-locales.min.js"></script>
{{-- <script src="/plugins/inputmask/jquery.inputmask.min.js"></script> --}}
<!-- Tempusdominus Bootstrap 4 -->
{{-- <script src="/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script> --}}
<!-- BS-Stepper -->
{{-- <script src="/plugins/bs-stepper/js/bs-stepper.min.js"></script> --}}
<!-- DataTables  & Plugins -->
<script src="/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<!-- jquery-validation -->
<script src="/plugins/jquery-validation/jquery.validate.js"></script>
<script src="/plugins/jquery-validation/additional-methods.min.js"></script>
@yield('js')
<!-- AdminLTE App -->
{{-- <script src="/adminlte/js/adminlte.min.js"></script> --}}
<!-- Page specific script -->
<script>
  // $(function () {
  //   //Datemask dd/mm/yyyy
  //   $('#datemask').inputmask('dd/mm/yyyy', { 'placeholder': 'dd/mm/yyyy' })
  //   //Datemask2 mm/dd/yyyy
  //   $('#datemask2').inputmask('mm/dd/yyyy', { 'placeholder': 'mm/dd/yyyy' })

  // });

  function luminousTrigger(el) {
    $(el).addClass('img-enlargable');
    var src = $(el).attr('src');
    $('<div>').css({
        background: 'RGBA(0,0,0,.5) url("'+src+'") no-repeat center',
        backgroundSize: 'contain',
        width:'100%', height:'100%',
        position:'fixed',
        zIndex:'10000',
        top:'0', left:'0',
        cursor: 'zoom-out'
    }).click(function(){
        $(this).remove();
    }).appendTo('body');
  }
</script>
@endpush
