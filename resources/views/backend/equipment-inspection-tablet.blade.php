@php
$linkList = route('equipment-inspection.list');
$link = route('equipment-inspection.store');
$linkBasicList = route('inspection-item.list');
@endphp

@extends('backend.layouts.app')
@section('title', '【タブレット】 設備点検票 入力')
@section('css')
<link rel="stylesheet" href="/plugins/select2/css/select2.min.css">
<link rel="stylesheet" href="/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
<link rel="stylesheet" href="/plugins/sweetalert2/sweetalert2.css">
<link rel="stylesheet" href="/plugins/datatables-fixedcolumns/css/fixedColumns.bootstrap4.css">
@vite('resources/css/order/style.css')
{{-- <link rel="stylesheet" href="/pluginssweetalert2-theme-bootstrap-4/bootstrap-4.min.css"> --}}
@endsection
@section('content_admin')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header" style="display: none;">
        <h3 class="card-title"><blockquote class="bd-card-title m-0" style="padding: 0px; padding-left: 0.5rem;">設備点検票 登録</blockquote></h3>

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
      <div data-href="{{ $link }}" id="app-equipment-inspection"></div>
      <!-- /.card-body -->
    </div>
    <!-- /.card -->
  </div>
</div>
<!-- /.row -->
<div class="modal fade" id="modal-xl-create" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-md w_percent_30 modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-body" id="modal-body-create">
        <div class="card card-default">
          <div class="card-header bg-gray-1">
            <div class="card-tools">
              <button type="button" data-dismiss="modal" aria-label="Close" class="modalCloseBtn js-modal-close">x</button>
            </div>
          </div>
          <div class="card-body">
            <div id="res-message-process"></div>
            <form id="quickForm" method="post" accept-charset="utf-8" enctype="multipart/form-data">
              <div class="row" id="inspectionResult-set" style="display: none;">
                <div class="col-sm-12">
                  <div class="form-group mb-3">
                    <label class="form-label dotted indented" for="exampleInspectionResult1">合否判定</label>
                    <div class="col-12 p-0">
                      <button  id="btn-inspectionResult-yes" style="width: 40px;" onclick="btnInspectionResult1(this)" type="button" class="col-sm-3 btn btn-success bg-yes mr-3">O</button>
                      <button id="btn-inspectionResult-no" style="width: 40px;" onclick="btnInspectionResult1(this)" type="button" class="col-sm-3 btn btn-danger bg-no">X</button>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="form-label dotted indented" for="exampleInspectionResult2">数値入力</label>
                    <div class="col-12">
                      <div class="row">
                        <div class="col-sm-3 form-group p-0">
                          <input type="text" name="inspectionResult2" class="form-control" id="exampleInspectionResult2">
                        </div>
                        <div class="col-sm-3">
                          <button id="btn-inspectionResult2" onclick="btnInspectionResult1(this)" type="button" class="btn btn-primary bg-search">入力</button>
                        </div>
                      </div>
                    </div>
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
@vite('resources/js/tablet/equipment-inspection-tablet.js')
<script>
  var validator;
  var validatorCreate;
  var dataItems = [];
  const user_name = '{{ $user_name }}';
  window.user_name = user_name;
  const linkList = '{{ $linkList }}';
  const link = '{{ $link }}';
  const equipment_inspection_id = '{{ $id }}';
  const linkBasicList = '{{ $linkBasicList }}';
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
  const makeMessage = (status, message) => {
    var text = '<div class="alert alert-'+getElStatus(status)+' alert-dismissible">'+
                  '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'+
                  // '<h5><i class="icon fas fa-check"></i> Alert!</h5>'+
                  getMessage(message)+
                '</div>';
    return text;
  }
  window.makeMessage = makeMessage;

  $(function () {
    //Initialize Select2 Elements
    $('.select2').select2();
    $.validator.addMethod("validDateYear", function(value, element) {
        return this.optional(element) || moment(value,"YYYY").isValid();
    }, getMessage("Please enter a valid date in the format YYYY"));
    $.validator.addMethod("validDateMonth", function(value, element) {
        return this.optional(element) || moment(value,"MM").isValid();
    }, getMessage("Please enter a valid date in the format MM"));
    // setInputFilter(document.getElementById("datemaskYear"), function(value) {
    //   return /^\d*$/.test(value);
    // }, '');
    // setInputFilter(document.getElementById("datemaskMonth"), function(value) {
    //   return /^\d*$/.test(value);
    // }, '');
    initialDataTableFixed();
    initialDataTable();
  });
  function initialDataTableFixed() {
    setTimeout(() => {
      var _th = $('#head-equipment-inspection-21').find('tr>th');
      if (_th) {
        var _h = _th.height();
        // console.log(_h);
        var _th2 = $('#head-equipment-inspection-22').find('tr:first');
        _th2.children('th').each(function () {
          $(this).css('height', `${_h}px`);
        });
      }
      // var table = $('#table-equipment-inspection-2').DataTable({
      //   // scrollY:        '500px',
      //   scrollX:        true,
      //   scrollCollapse: true,
      //   paging:         false,
      //   ordering: false,
      //   info:     false,
      //   searching: false,
      //   fixedColumns:   {
      //       left: 6,
      //       // right: 1
      //   }
      // });
    }, 1500);
  }
  function initialDataTable() {
    var _token = $('meta[name="csrf-token"]').attr('content');
    $.ajax({
      type: 'POST',
      url: linkList,
      data : {method: 'SEARCH', id: equipment_inspection_id, _token },
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

  function btnInspectionResult1(el) {
    var elId = $(el).attr('id');
    var index = $('#inspectionResult-set').attr('data-index');
    var type = $('#inspectionResult-set').attr('data-type');
    var j = $('#inspectionResult-set').attr('data-j');
    if (elId === 'btn-inspectionResult-yes') {
        value = 'O';
    }
    if (elId === 'btn-inspectionResult-no') {
        value = 'X';
    }
    if (elId === 'btn-inspectionResult2') {
      value = $('#exampleInspectionResult2').val();
      if (!value) {
        return;
      }
    }
    var data = {index, j, type, value};
    window.app_equipment_inspection.btnInspectionResult(data);
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
    // _rules['base64_image'] = {
    //   required: true,
    // };
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

  function btnUpdateCreater(event, el) {
    var _type = $(el).attr('data-type');
  }


    function showModalCreate(el) {
      return redirectToUrl(link + '/list');
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
      if (message === 'Updated equipment inspection success') {
        mes = "機器検査の成功を更新しました"; //'System error, please try again later!';
      }
      if (message === 'Updated equipment inspection failed') {
        mes = "更新された機器検査に失敗しました"; //'System error, please try again later!';
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
