@php
$linkList = route('daily.list');
$link = route('daily.store');
$linkBasicList = route('inspection-item.list');
$linkEquipmentList = route('equipment-inspection.list');
@endphp

@extends('backend.layouts.app')
@section('title', '日々生産管理表 入力')
@section('css')
<link rel="stylesheet" href="/plugins/select2/css/select2.min.css">
<link rel="stylesheet" href="/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
<link rel="stylesheet" href="/plugins/sweetalert2/sweetalert2.css">
<link rel="stylesheet" href="/plugins/datatables-fixedcolumns/css/fixedColumns.bootstrap4.css">
{{-- <link rel="stylesheet" href="/pluginssweetalert2-theme-bootstrap-4/bootstrap-4.min.css"> --}}
@vite('resources/css/order/style.css')
<style>
  input[type="number"]::-webkit-inner-spin-button,
  input[type="number"]::-webkit-outer-spin-button {
      -webkit-appearance: none;
      margin: 0;
  }
  .bg-computation {
    background-color: #bcd5ea;
  }
</style>
@endsection
@section('content_admin')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title"><blockquote class="bd-card-title m-0" style="padding: 0px; padding-left: 0.5rem;">日々生産管理表 入力</blockquote></h3>

        {{-- <div class="card-tools">
          <div class="input-group input-group-md">
            <div class="input-group-append">
              <button data-href="{{ $link }}" type="button" class="btn btn-primary ml-3" onclick="showModalCreate(this)">
                <i class="fa fa-list"></i> 新規登録
              </button>
            </div>
          </div>
        </div> --}}
      </div>
      <!-- /.card-header -->
      <div id="res-message-equipment"></div>
      <div data-href="{{ $link }}" id="app-daily-production"></div>
      <!-- /.card-body -->
    </div>
    <!-- /.card -->
  </div>
</div>
<!-- /.row -->
<div class="modal fade" id="modal-xl-create" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl w_percent_30 modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-body" id="modal-body-create">
        <div class="card card-default">
          <div class="card-header bg-gray-1">
            <div class="card-tools">
              {{-- <button type="button" data-dismiss="modal" aria-label="Close" class="btn btn-danger bg-colse">
                <i class="fa fa-times"></i>
              </button> --}}
              <button type="button" data-dismiss="modal" aria-label="Close" class="modalCloseBtn js-modal-close">x</button>
            </div>
          </div>
          <div class="card-body">
            <div id="res-message-process"></div>
            <form id="quickForm" method="post" accept-charset="utf-8" enctype="multipart/form-data">
              <div class="row" id="product-set" style="display: none;">
                <div class="col-sm-12">
                  <div class="form-group">
                    <label class="form-label dotted indented" for="exampleProduct1">品番選択</label>
                    <select id="exampleProduct1" class="form-control select2" style="width: 100%;">
                      @if (isset($products))
                      <option style="background-color: gray;" value="">品番</option>
                      @foreach ($products as $key => $value)
                      <option data-code="{{ $value->code }}" data-model="{{ $value->model }}" data-name="{{ $value->name }}" data-short-name="{{ $value->short_name }}" value="{{ $value->id }}">{{ '['.$value->model .'] '. $value->short_name }}</option>
                      @endforeach
                      @endif
                    </select>
                  </div>
                  <div class="form-group mb-0" style="margin-top: 1rem;">
                    <button onclick="btnProduct(this)" type="button" class="col-sm-2 btn btn-primary bg-search">選択</button>
                  </div>
                </div>
              </div>
              <div class="row" id="comment-set" style="display: none;">
                <div class="col-sm-12">
                  <div class="form-group">
                    <label class="form-label dotted indented" for="exampleComment1">備考</label>
                    <textarea maxlength="255" id="exampleComment1" class="form-control" rows="3"></textarea>
                  </div>
                  <div class="form-group mb-0" style="margin-top: 1rem;">
                    <button onclick="btnComment(this)" type="button" class="col-sm-2 btn btn-primary bg-search">入力</button>
                  </div>
                </div>
              </div>
              <div class="row" id="line-set" style="display: none;">
                <div class="col-sm-12">
                  <div class="form-group">
                    <label class="form-label dotted indented" for="exampleLineCode1">ライン名（ラインコード）</label>
                    <select id="exampleLineCode1" class="form-control select2" style="width: 100%;">
                      @if (isset($lines))
                      <option style="background-color: gray;" value="">ライン名</option>
                      @foreach ($lines as $key => $value)
                      <option data-code="{{ $value->line_code }}" data-name="{{ $value->line_name }}" value="{{ $value->id }}">{{ $value->line_name .' ('. $value->line_code . ')' }}</option>
                      @endforeach
                      @endif
                    </select>
                  </div>
                  <div class="form-group mb-0" style="margin-top: 1rem;">
                    <button onclick="btnLineCode(this)" type="button" class="col-sm-2 btn btn-primary bg-search">選択</button>
                  </div>
                </div>
              </div>
              <div class="row" id="reset-cutlery-set" style="display: none;">
                <div class="col-sm-12">
                  <div class="form-group text-center">
                    <label for="exampleLineCode1">回数をリセットします。</label>
                  </div>
                  <div class="form-group mb-0 text-center" style="margin-top: 1rem;">
                    <button onclick="btnResetCutlery(this)" type="button" class="col-sm-2 btn btn-primary bg-search">リセットする</button>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
@endsection

@section('js')
<script src="/plugins/inputmask/jquery.inputmask.min.js"></script>
<script src="/plugins/sweetalert2/sweetalert2.min.js"></script>
<script src="/plugins/select2/js/select2.full.min.js"></script>
{{-- <script src="/plugins/datatables-fixedcolumns/js/fixedColumns.bootstrap4.js"></script> --}}
<script src="/plugins/datatables-fixedcolumns/js/dataTables.fixedColumns.js"></script>
@vite('resources/js/tablet/daily-production-control-table-edit.js')
<script>
  var validator;
  var validatorCreate;
  var dataItems = [];
  const user_name = '{{ $user_name }}';
  window.user_name = user_name;
  const _year = '{{ $year }}';
  const _month = '{{ $month }}';
  var _line_id = '{{ $line_id }}';
  const linkList = '{{ $linkList }}';
  const link = '{{ $link }}';
  var daily_production_control_id = '{{ $id }}';
  const linkBasicList = '{{ $linkBasicList }}';
  const linkEquipmentList = '{{ $linkEquipmentList }}';
  const daily_date = '{{ $daily_date }}';
  const listDaily = '{{ json_encode($listDaily) }}';
  const messages = {
    process_id: {
      required: getMessage('required'),
      digits: getMessage('digits'),
      min: getMessage('min1'),
      max: getMessage('max99'),
    },
    _basic_set_id: {
      required: getMessage('required'),
    },
  };
  const rules = {
    process_id: { required: true, digits: true, min: 1, max: 99 },
    _basic_set_id: { required: true },
  };
  const makeMessage = "";
  // const makeMessage = (status, message) => {
  //   var text = '<div class="alert alert-'+getElStatus(status)+' alert-dismissible">'+
  //                 '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'+
  //                 // '<h5><i class="icon fas fa-check"></i> Alert!</h5>'+
  //                 getMessage(message)+
  //               '</div>';
  //   return text;
  // }
  // window.makeMessage = makeMessage;

  $(function () {
    //Initialize Select2 Elements
    // initProductSearch();
    initLineSearch();

    $.validator.addMethod("validDateYear", function(value, element) {
        return this.optional(element) || moment(value,"YYYY").isValid();
    }, getMessage("Please enter a valid date in the format YYYY"));
    $.validator.addMethod("validDateMonth", function(value, element) {
        return this.optional(element) || moment(value,"MM").isValid();
    }, getMessage("Please enter a valid date in the format MM"));

    // _initialDataTable();
    initialDataTableFixed();
  });

  function initProductSearch() {
    let lineCODE = document.getElementById('lineCODE').value;

    $('#exampleProduct1').select2({
      language: {
        searching: function() {
          return '検索中...';
        },
        noResults: function(){
          return '結果が見つかりません';
        }
      },
      maximumInputLength: 255,
      ajax: {
        method: 'POST',
        url: '{{route('daily.list')}}',
        delay: 250,
        data: function (params) {
          var data = {
            _token: $('meta[name="csrf-token"]').attr('content'),
            search: params.term,
            page: params.page,
            method: 'SEARCH-PRODUCT',
            line_code: lineCODE ?? null
          }

          // Query parameters will be ?search=[term]&type=public
          return data;
        },
        // processResults: function (data, params) {
        //   params.page = params.page || 1;

        //   return {
        //     results: data.items,
        //     pagination: {
        //       more: (params.page * 30) < data.total_count
        //     }
        //   };
        // },
        cache: false,
      },
      templateSelection: function(container) {
        $(container.element).attr("data-source", container.source);
        return container.text;
      },
      placeholder: '品番選択',
    });
  }

  function initLineSearch() {
    // exampleLineCode1
    $('#exampleLineCode1').select2({
      language: {
        searching: function() {
          return '検索中...';
        },
        noResults: function(){
          return '結果が見つかりません';
        }
      },
      maximumInputLength: 255,
      ajax: {
        method: 'POST',
        url: '{{route('daily.list')}}',
        delay: 250,
        data: function (params) {
          var data = {
            _token: $('meta[name="csrf-token"]').attr('content'),
            search: params.term,
            page: params.page,
            method: 'SEARCH-LINE'
          }

          // Query parameters will be ?search=[term]&type=public
          return data;
        },
        // processResults: function (data, params) {
        //   params.page = params.page || 1;

        //   return {
        //     results: data.items,
        //     pagination: {
        //       more: (params.page * 30) < data.total_count
        //     }
        //   };
        // },
        cache: false,
      },
      templateSelection: function(container) {
        $(container.element).attr("data-source", container.source);
        return container.text;
      },
      placeholder: 'ライン名',
    });
  }

  function initialDataTableFixed() {
    setTimeout(() => {
      var _th = $('#head-equipment-inspection-12').find('tr:first');
      var _th3 = $('#head-equipment-inspection-12').find('tr:eq(1)');
      if (_th) {
        var _h = _th.height();
          // console.log(_h);
          var _th2 = $('#head-equipment-inspection-11').find('tr:first');
          _th2.children('th').each(function () {
            $(this).css('height', `${_h}px`);
          });
      }
      if (_th3) {
        var _h2 = _th3.height();
          // console.log(_h2);
          var _th4 = $('#head-equipment-inspection-11').find('tr:eq(1)');
          _th4.children('th').each(function () {
            $(this).css('height', `${_h2}px`);
          });
      }
    // 
      // tr-row-all
      if ($('.tr-row-all').find('.bg-td-danger') && $('.tr-row-all').find('.bg-td-danger').length) {
        $('.bg-row-all').removeClass('bg-td-danger').addClass('bg-td-danger');
      } else if ($('.tr-row-all').find('.bg-td-warning') && $('.tr-row-all').find('.bg-td-warning').length) {
        $('.bg-row-all').removeClass('bg-td-warning').addClass('bg-td-warning');
      }
      var _row = $('#body-machine-32 .tr-row');
      if (_row && _row.length) {
        for (var i = 0;i < _row.length; i++) {
          if ($(_row[i]).find('.bg-td-danger') && $(_row[i]).find('.bg-td-danger').length) {
            $('.bg-row-'+i).removeClass('bg-td-danger').addClass('bg-td-danger');
          } else if ($(_row[i]).find('.bg-td-warning') && $(_row[i]).find('.bg-td-warning').length) {
            $('.bg-row-'+i).removeClass('bg-td-warning').addClass('bg-td-warning');
          }
        }
      }

    }, 1000);
    // var table = $('#table-equipment-inspection-2').DataTable( {
    //     // scrollY:        '400px',
    //     scrollY:        false,
    //     scrollX:        true,
    //     scrollCollapse: true,
    //     paging:         false,
    //     ordering: false,
    //     info:     false,
    //     searching: false,
    //     responsive: false,
    //     fixedColumns:   {
    //         left: 2,
    //         // right: 1
    //     },
    //     initComplete: function( settings, json ) {
    //       $('#rowspan_achievement').attr('rowspan', 8);
    //       $('#rowspan_deadtime').attr('rowspan', 9);
    //       $('[id^=row_achievement]').each(function(e) {
    //         $(this).find('td:first').hide();
    //         // $(this).find('td:nth-child(2)').find('div').css('width', '8rem');
    //       });
    //       $('[id^=row_deadtime]').each(function(e) {
    //         $(this).find('td:first').hide();
    //         // $(this).find('td:nth-child(2)').find('div').css('width', '8rem');
    //       });
    //       $('.colspan-2').each(function(e) {
    //         $(this).find('td:nth-child(1)').attr('colspan', 2);
    //         // $(this).find('td:nth-child(1)').attr('colspan', 2).find('div').css('width', '8rem');
    //         $(this).find('td:nth-child(2)').hide();
    //       });
    //       // const w1 = $('#th-input2').css('left');
    //       // const style = $('#th-input2').attr('style');
    //       // console.log(w1);
    //       // console.log(style);
    //     },
    // } );
  }

  function _initialDataTable() {
    var _token = $('meta[name="csrf-token"]').attr('content');
    $.ajax({
      type: 'POST',
      url: linkList,
      data : {method: 'SEARCH', year: _year, month: _month, id: daily_production_control_id, line_id: _line_id ,_token },
      beforeSend: function () {
      },
      success: function(data) {
        if (data.status == 'error') {
          $('#res-message-equipment').html(makeMessage(data.status, data.message));
          setTimeout(function(){ $('#res-message-equipment').html('') }, 5000);
          return;
        } else {
          window.app_equipment_inspection.initialDataTable(data.data);
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        // window.location.reload(true);
      }
    });
  }

  function btnLineCode(el) {
    var input = $('#exampleLineCode1').find(":selected");
    var id = input.val();
    _line_id = id;
    var source = input.data();
    if (source && source.source) {
      source = source.source;
    }
    if (! id) {
      $('#res-message-process').html(makeMessage('error', '「この項目は必須です」'));
      setTimeout(function(){ $('#res-message-process').html('') }, 1000);
      return;
    }
    var code = (source && source.code) ? source.code : '';
    var name = (source && source.name) ? source.name : '';
    var data = {id, code, name};
    $('#lineID').removeClass('is-invalid');
    $('#lineID-error').remove();
    window.app_equipment_inspection.btnLineCode(data, 'lines');
  }

  function btnDepartment(el) {
    var input = $('#exampleDepartment1').find(":selected");
    var id = input.val();
    var code = input.attr('data-code');
    var name = input.attr('data-name');
    var data = {id, code, name};
    $('#departmentID').removeClass('is-invalid');
    $('#departmentID-error').remove();
    window.app_equipment_inspection.btnLineCode(data, 'department');
  }

  function btnComment(el) {
    var input = $('#exampleComment1');
    var comment = input.val();
    var p_code = input.attr('data-p-code');
    var code = input.attr('data-code');
    var index = input.attr('data-index');
    var data = {index, p_code, code, comment};
    window.app_equipment_inspection.btnLineCode(data, 'comment');
  }

  function btnProduct(el) {
    var input = $('#exampleProduct1').find(":selected");
    var id = input.val();
    var source = input.data();
    if (source && source.source) {
      source = source.source;
    }
    var code = (source && source.code) ? source.code : '';
    var name = (source && source.name) ? source.name : '';
    var model = (source && source.model) ? source.model : '';
    var short_name = (source && source.short_name) ? source.short_name : '';
    var department_name = (source && source.department_name) ? source.department_name : '';
    var data = {id, code, name, model, short_name, department_name};

    $('#departmentID').removeClass('is-invalid');
    $('#departmentID-error').remove();
    window.app_equipment_inspection.btnLineCode(data, 'product');
  }

  function btnResetCutlery(el) {
    var input = $('#reset-cutlery-set');
    var index = input.attr('data-index');
    var i = input.attr('data-i');
    var data = {index, i};

    window.app_equipment_inspection.btnLineCode(data, 'resetCutlery');
  }

  function btnProcess(el) {
    if (validator) {
      validator.destroy();
    }
    $('#res-message-process').html('');
    validator = $('#quickForm').validate({
      rules,
      messages,
      errorElement: 'span',
      errorPlacement: function (error, element) {
        error.addClass('invalid-feedback');
        element.closest('.form-group').append(error);
      },
      highlight: function (element, errorClass, validClass) {
        $(element).addClass('is-invalid');
      },
      unhighlight: function (element, errorClass, validClass) {
        $(element).removeClass('is-invalid');
      }
    });
    if (! validator.form()) {
      return;
    }
    var input = $('#exampleBasicSet1').find(":selected");
    var id = input.val();
    var processId = $('#exampleProcess1').val();
    var datas = {id, processId, dataItems: null};
    var _token = $('meta[name="csrf-token"]').attr('content');
    $.ajax({
      type: 'POST',
      url: linkBasicList,
      data : {method: 'SEARCH', id, _token },
      beforeSend: function () {
      },
      success: function(data) {
        if (data.status == 'error') {
          // $('#res-message-process').html(makeMessage(data.status, data.message));
          setTimeout(function(){ $('#res-message').html('') }, 5000);
          return;
        } else {
          if (data.data.json_data && data.data.json_data.length) {
            dataItems = [];
            for (var i = 0; i < data.data.json_data.length; i++) {
              dataItems.push(Object.create(data.data.json_data[i]));
            }
            datas.dataItems = dataItems;
            window.app_equipment_inspection.btnProcess(datas);
          }
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        // window.location.reload(true);
      }
    });
  }

  function getRules() {
    var _rules = {};
    _rules['year'] = {
      required: true,
      validDateYear: true,
      // minlength: 4
    };
    _rules['month'] = {
      required: true,
      validDateMonth: true,
      // minlength: 2
    };
    _rules['department_id'] = {
      required: true,
    };
    _rules['base64_image'] = {
      required: true,
    };
    _rules['line_id'] = {
      required: true,
    };
    var _messages = {};
    _messages['year'] = {
      required: getMessage('required'),
      // minlength: getMessage('minlength3'),
    };
    _messages['month'] = {
      required: getMessage('required'),
      // minlength: getMessage('minlength3'),
    };
    _messages['department_id'] = {
      required: getMessage('required'),
    };
    _messages['base64_image'] = {
      required: getMessage('required'),
    };
    _messages['line_id'] = {
      required: getMessage('required'),
    };
    _rules['inspection_item[]'] ={
      required: true,
    };
    _messages['inspection_item[]'] = {
      required: getMessage('required'),
    };
    _rules['inspection_point[]'] ={
      required: true,
    };
    _messages['inspection_point[]'] = {
      required: getMessage('required'),
    };
    _rules['inspection_period[]'] ={
      required: true,
    };
    _messages['inspection_period[]'] = {
      required: getMessage('required'),
    };
    _rules['typeof_inspector[]'] ={
      required: true,
    };
    _messages['typeof_inspector[]'] = {
      required: getMessage('required'),
    };
    return {_rules, _messages};
  }

  function submitForm(event, el) {
    return;
    if (validatorCreate) {
      validatorCreate.destroy();
    }
    var {_rules, _messages} = getRules();
    validatorCreate = $('#createForm').validate({
      rules: _rules,
      messages: _messages,
      errorElement: 'span',
      errorPlacement: function (error, element) {
        error.addClass('invalid-feedback');
        element.closest('.form-group').append(error);
      },
      highlight: function (element, errorClass, validClass) {
        $(element).addClass('is-invalid');
      },
      unhighlight: function (element, errorClass, validClass) {
        $(element).removeClass('is-invalid');
      }
    });
    if (! validatorCreate.form()) {
      return;
    }
    return window.app_equipment_inspection.submitForm(event);
  }

  const uploadFile = (el) => {
      $('#exampleInputFileName').val('');
      const fileInput = document.getElementById('exampleInputFile');
      const file = fileInput.files[0];
      if (! (typeof(file) !== "undefined" && file !== null) ) {
        $('#exampleInputFileName').val('');
        return;
      }
      const filename = file.name;
      $('#exampleInputFileName').val(filename);
    };

    const preview = (el) => {
      const fileInput = document.getElementById('exampleInputFile');
      const file = fileInput.files[0];
      if (! (typeof(file) !== "undefined" && file !== null) ) {
        document.getElementById('exampleInputFile').value = null;
        return;
      }
      const filename = file.name;
      const fileSize = file.size / 1024 / 1024; // in MiB
      const limit = 5; // MiB
      const imageType = /image.*/;
      if (! file.type.match(imageType)) {
        document.getElementById('exampleInputFile').value = null;
        document.getElementById('base64Image').value = null;
        $('#examplePreview').attr('src', '#');
        $('#examplePreview1').hide();
        $('#res-message').html(makeMessage('warning', 'Image only allows file types of PNG, JPG, JPEG and BMP.'));
        setTimeout(function(){ $('#res-message').html('') }, 5000);
        return;
      }
      var extension = filename.substring(filename.lastIndexOf('.') + 1).toLowerCase();
      if (!(extension == "gif" || extension == "png" || extension == "bmp"
        || extension == "jpeg" || extension == "jpg")) {
        document.getElementById('exampleInputFile').value = null;
        document.getElementById('base64Image').value = null;
        $('#examplePreview').attr('src', '#');
        $('#examplePreview1').hide();
        $('#res-message').html(makeMessage('warning', 'Image only allows file types of PNG, JPG, JPEG and BMP.'));
        setTimeout(function(){ $('#res-message').html('') }, 5000);
        return;
      }
      if (fileSize > limit) {
        document.getElementById('exampleInputFile').value = null;
        document.getElementById('base64Image').value = null;
        $('#examplePreview').attr('src', '#');
        $('#examplePreview1').hide();
        $('#res-message').html(makeMessage('warning', 'File size exceeds '+limit+' MiB'));
        setTimeout(function(){ $('#res-message').html('') }, 5000);
        return;
      }
      let reader = new FileReader();
      reader.onload = function(event) {
        // console.log(event.target.result);
        // $('#examplePreview').attr('src', event.target.result);
        // $('#examplePreview1').show();
        $('#base64Image').removeClass('is-invalid');
        $('#base64Image-error').remove();
        const data = {img: event.target.result};
        document.getElementById('base64Image').value = file.name;
        window.app_equipment_inspection.btnUploadFile(data);
      }
      reader.readAsDataURL(file);
    };
    function showModalCreate(el) {
      return redirectToUrl(linkEquipmentList);
      // $('#res-message').html('');
      // $('#exampleInputID1').val('');
      // $('#exampleInputCode1').val('');
      // $('#exampleInputName1').val('');
      // $('#modal-card-title').text('Facility management master create');
      // $('#modal-btn-submit').text('新規登録');
      // $('#modal-btn-submit').prop('disabled', false);
      // $('#modal-xl-create').modal('show');
    }
    function redirectToUrl(url) {
      // similar behavior as an HTTP redirect
      // window.location.replace(url);

      // similar behavior as clicking on a link
      return window.location.href = url;
    }
    function getMessage(message) {
      var mes = message;
      if (message === 'Error system') {
        mes = "「システムエラーが発生しました。再度試してください。」"; //'System error, please try again later!';
      }
      if (message === 'System error') {
        mes = "「システムエラーが発生しました。再度試してください。」"; //'System error, please try again later!';
      }
      if (message === 'required') {
        mes = "「この項目は必須です」";//'This field is required.';
      }
      if (message === 'digits') {
        mes = 'Please enter only digits.';
      }
      if (message === 'min1') {
        mes = "「1桁から入力してください」";//'Please enter a value greater than or equal to 1.';
      }
      if (message === 'max99') {
        mes = "99桁以内で入力してください";//'Please enter a value less than or equal to 99.';
      }
      if (message === 'Updated equipment inspection success') {
        mes = "日次生産管理表更新入力成功";//'Please enter a value less than or equal to 99.';
      }
      if (message === 'Updated equipment inspection failed') {
        mes = "日次生産管理表の更新入力に失敗しました";//'Please enter a value less than or equal to 99.';
      }
      return mes;
    }
    function getElStatus(status) {
      var txt = status;
      if (status === 'error') {
        txt = 'warning';
      }
      return txt;
    }
    // Restricts input for the given textbox to the given inputFilter.
    function setInputFilter(textbox, inputFilter, errMsg) {
      [ "input", "keydown", "keyup", "mousedown", "mouseup", "select", "contextmenu", "drop", "focusout" ].forEach(function(event) {
        textbox.addEventListener(event, function(e) {
          if (inputFilter(this.value)) {
            // Accepted value.
            if ([ "keydown", "mousedown", "focusout" ].indexOf(e.type) >= 0) {
              this.classList.remove("input-error");
              this.setCustomValidity("");
            }
            this.oldValue = this.value;
            this.oldSelectionStart = this.selectionStart;
            this.oldSelectionEnd = this.selectionEnd;
          }
          else if (this.hasOwnProperty("oldValue")) {
            // Rejected value: restore the previous one.
            this.classList.add("input-error");
            this.setCustomValidity(errMsg);
            this.reportValidity();
            this.value = this.oldValue;
            this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
          }
          else {
            // Rejected value: nothing to restore.
            this.value = "";
          }
        });
      });
    }
</script>
@endsection
