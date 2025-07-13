@php
$link = route('inspection-item.store');
$linkList = route('inspection-item.list');
@endphp

@extends('backend.layouts.app')
@section('title', '点検項目基本セット 一覧')
@section('css')
<link rel="stylesheet" href="/plugins/sweetalert2/sweetalert2.css">
@vite('resources/css/order/style.css')
{{-- <link rel="stylesheet" href="/pluginssweetalert2-theme-bootstrap-4/bootstrap-4.min.css"> --}}
@endsection
@section('content_admin')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header card-basic-set">
        <h3 class="card-title"><blockquote class="bd-card-title m-0" style="padding: 0px; padding-left: 0.5rem;">点検項目基本セット 一覧</blockquote></h3>

        <div class="card-tools">
          <div class="input-group input-group-md">
            <div class="input-group-append">
              <button data-href="{{ $link }}" type="button" class="btn btn-success ml-3" onclick="showModalCreate(this)">
                <i class="fa fa-plus-square"></i> 新規登録
              </button>
            </div>
          </div>
        </div>
      </div>
      <!-- /.card-header -->
      <div class="card-body table-responsive p-0">
        <table id="table-data" class="table text-nowrap table-bordered table-hover">
          <thead>
            <tr>
              <th style="width: 5%;">No.</th>
              <th style="width: 33%;">ラインコード</th>
              <th style="width: 16%;">作成日</th>
              <th style="width: 16%;">最終更新日</th>
              <th style="width: 16%;">作成者</th>
              <th style="width: 10%;">操作</th>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
      </div>
      <div id="pagination-link">
      @if (isset($blogs) && $blogs && count($blogs))
        <div class="card-footer clearfix">
          {{ $blogs->links('partials.pagination-bootstrap-4')}}
        </div>
      @endif
      </div>
      <!-- /.card-body -->
    </div>
    <!-- /.card -->
  </div>
</div>
<!-- /.row -->
<div class="modal fade" id="modal-xl-create" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-basic-set modal-dialog-scrollable">
    <div class="modal-content bg-gray-1">
      <div class="modal-body" id="modal-body-create">
        @include('backend.inspection-item-basic-set-modal')
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
@endsection

@section('js')
<script src="/plugins/sweetalert2/sweetalert2.min.js"></script>
<script>
  var validator;
  var item = {
    inspection_item: null,
    inspection_point: null,
    inspection_period: null,
    typeof_inspector: null,
  };
  var linkList = '{{$linkList}}';
  var dataItems = [];
  $(function () {
    initialDataTable();
  });
  function initialDataTable() {
    var _token = $('meta[name="csrf-token"]').attr('content');
    var page = getParam(getCurrentURL(), 'page');
    $.ajax({
      type: 'POST',
      url: linkList,
      data : {method: 'LIST', page, _token },
      beforeSend: function () {
      },
      success: function(data) {
        if (data.status == 'error') {
          return;
        } else {
          var links = data.data.links;
          if (links) {
            links = '<div class="card-footer clearfix">'+
              links+
            '</div>';
          }
          var tbody = makeBodyTable(data.data.items);
          $('#pagination-link').html(links);
          $('#table-data').find('tbody').html(tbody);
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        // window.location.reload(true);
      }
    });
  }
  function showModalCreate(el) {
    dataItems = [];
    $('#res-message').html('');
    $('#exampleInputID1').val('');
    $('#exampleInputBasicName1').val('');
    $('#table-inspection-item').find('tbody').html('');
    clearValidation();
    $('#modal-card-title').text('点検項目基本セット登録');
    $('#modal-btn-submit').text('新規登録');
    $('#modal-btn-submit').prop('disabled', false);
    makeDetail('CREATE');
    $('#modal-xl-create').modal('show');
  }
  function showModalEdit(el) {
    $('#res-message').html('');
    $('#exampleInputBasicName1').val('');
    $('#table-inspection-item').find('tbody').html('');
    clearValidation();
    dataItems = [];
    var link = $('#quickForm').attr('action');
    var _token = $('meta[name="csrf-token"]').attr('content');
    var id = $(el).attr('data-id');
    var _type = $(el).attr('data-method');
    $('#modal-card-title').text('点検項目基本セット編集');
    $('#exampleInputID1').val(id);
    var method = 'SEARCH';
    var data = {id, method, _token};
    $.ajax({
      type: 'POST',
      url: link,
      data,
      beforeSend: function () {
        $('#modal-btn-submit').prop('disabled', true);
      },
      success: function(data) {
        $('#modal-btn-submit').prop('disabled', false);
        if (data.status == 'error') {
          $('#res-message').html(makeMessage(data.status, data.message));
          setTimeout(function(){ $('#res-message').html('') }, 5000);
          makeDetail(_type);
          return;
        } else {
          $('#exampleInputBasicName1').val(data.data.inspection_item_set);
          if (data.data.json_data && data.data.json_data.length) {
            for (var i = 0; i < data.data.json_data.length; i++) {
              dataItems.push(Object.create(data.data.json_data[i]));
            }
            var text = makeTrItem();
            $('#table-inspection-item').find('tbody').html(text);
            makeDetail(_type);
          }
          return;
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        return $('#res-message').html(makeMessage('danger', 'Error system'));
        // window.location.reload(true);
      }
    });
    $('#modal-btn-submit').text('アップデート');
    // $('#modal-btn-submit').prop('disabled', false);
    $('#modal-xl-create').modal('show');
  }
  function btnDetail(el) {
    return makeDetail('EDIT');
  }
  function makeDetail(type) {
    $('#quickForm').removeClass('detail');
    $('#modal-btn-submit').show();
    $('#modal-btn-detail').hide();
    $('#quickForm').find('input').prop('disabled', false);
    $('#quickForm').find('input').prop('disabled', false);
    $('#quickForm').find('select').prop('disabled', false);
    $('#quickForm').find('select').prop('disabled', false);
    if (type === 'DETAIL') {
      $('#modal-card-title').text('点検項目基本セット詳細');
      $('#quickForm').addClass('detail');
      $('#modal-btn-submit').hide();
      $('#modal-btn-detail').show();
      $('#quickForm').find('input').prop('disabled', true);
      $('#quickForm').find('input').prop('disabled', true);
      $('#quickForm').find('select').prop('disabled', true);
      $('#quickForm').find('select').prop('disabled', true);
    }
  }
  function hideModal(el) {
    $('#modal-xl-create').modal('hide');
  }

  function getRules() {
    var rules = {};
    rules['basic_name'] = {
      required: true,
      minlength: 3
    };
    var messages = {};
    messages['basic_name'] = {
      required: getMessage('required'),
      minlength: getMessage('minlength3'),
    };
    rules['inspection_item[]'] ={
      required: true,
    };
    messages['inspection_item[]'] = {
      required: getMessage('required'),
    };
    rules['inspection_point[]'] ={
      required: true,
    };
    messages['inspection_point[]'] = {
      required: getMessage('required'),
    };
    rules['inspection_period[]'] ={
      required: true,
    };
    messages['inspection_period[]'] = {
      required: getMessage('required'),
    };
    rules['typeof_inspector[]'] ={
      required: true,
    };
    messages['typeof_inspector[]'] = {
      required: getMessage('required'),
    };
    // $('[id^=inputInspector]').each(function(e) {
    //   var inputName = $(this).attr('name');
    //   rules[inputName] ={
    //     required: true,
    //   };
    //   messages[inputName] = {
    //     required: "Please select typeof inspector",
    //   };
    // });
    return {rules, messages};
  }

  function submitForm(el) {
    // $.validator.setDefaults({
    //   submitHandler: function () {
    //   }
    // });
    if (validator) {
      validator.destroy();
    }
    var {rules, messages} = getRules();
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
    var link = $('#quickForm').attr('action');
    var _token = $('meta[name="csrf-token"]').attr('content');
    var id = $('#exampleInputID1').val();
    var name = $('#exampleInputBasicName1').val();
    var data = {id, name, dataItems, _token};
    $.ajax({
      type: 'POST',
      url: link,
      data,
      beforeSend: function () {
        $('#modal-btn-submit').prop('disabled', true);
      },
      success: function(data) {
        $('#modal-btn-submit').prop('disabled', false);
        if (data.status == 'error') {
          $('#res-message').html(makeMessage(data.status, data.message));
          document.getElementById("res-message").scrollIntoView();
          setTimeout(function(){ $('#res-message').html('') }, 5000);
          return;
        } else {
          $('#res-message').html(makeMessage(data.status, data.message));
          document.getElementById("res-message").scrollIntoView();
          setTimeout(function(){ $('#res-message').html('') }, 5000);
          initialDataTable();
          return;
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        $('#res-message').html(makeMessage('danger', 'Error system'));
        document.getElementById("res-message").scrollIntoView();
        setTimeout(function(){ $('#res-message').html('') }, 5000);
        return
        // window.location.reload(true);
      }
    });
    // $('#quickForm').submit();
  }

  function clearValidation() {
    if (validator) {
      validator.resetForm();
      validator.reset();
      $('.form-group .form-control').each(function () { $(this).removeClass('is-invalid'); });
      $('.form-group').each(function () { $(this).removeClass('has-success'); });
      $('.form-group').each(function () { $(this).removeClass('has-error'); });
      $('.form-group').each(function () { $(this).removeClass('has-feedback'); });
      $('.help-block').each(function () { $(this).remove(); });
      $('.form-control-feedback').each(function () { $(this).remove(); });
      $('.invalid-feedback').each(function () { $(this).remove(); });
      $('.error').each(function () { $(this).remove(); });
    }
  }

  function deleteAction(el) {
    Swal.fire({
      title: '本気ですか？',
      text: "これを元に戻すことはできません。",
      icon: 'warning',
      showCancelButton: true,
      cancelButtonText: 'キャンセル',
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'はい、削除してください!'
    }).then((result) => {
      /* Read more about isConfirmed, isDenied below */
      if (result.isConfirmed) {
        deleteConfirmed(el);
      }
    })
  };
  function deleteConfirmed(el) {
    var _token = $('meta[name="csrf-token"]').attr('content');
    var a = $(el);
    var linkDel = a.data('href');
    var id = a.data('id');
    var method = a.data('method');
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': _token
        }
    });
    $.ajax({
      type: 'POST',
      url: linkDel,
      data: {
        _token,
        method,
        id,
      },
      beforeSend: function () {
      },
      success: function(data) {
        if (data.status == 'error') {
          Swal.fire("削除されました！", getMessage(data.message) , "error");
          return;
        } else {
          // a.prop('disabled', true);
          a.parent().parent().remove();
          Swal.fire("削除されました！", getMessage(data.message) , "success");
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        Swal.fire("削除されました！", getMessage('Error system') , "error");
        // window.location.reload(true);
      }
    });
  };

  function makeMessage(status, message) {
    var text = '<div class="alert alert-'+getElStatus(status)+' alert-dismissible">'+
                  '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'+
                  // '<h5><i class="icon fas fa-check"></i> Alert!</h5>'+
                  getMessage(message)+
                '</div>';
    return text;
  }
  function getElStatus(status) {
    var txt = status;
    if (status === 'error') {
      txt = 'warning';
    }
    return txt;
  }
  function getMessage(message) {
    var mes = message;
    if (message === 'Error system') {
      mes = "「システムエラーが発生しました。再度試してください。」"; //'System error, please try again later!';
    }
    if (message === 'System error') {
      mes = "「システムエラーが発生しました。再度試してください。」"; //'System error, please try again later!';
    }
    if (message === 'Updated inspection item set success') {
      mes = "更新された検査項目セットの成功"; //'System error, please try again later!';
    }
    if (message === 'Updated inspection item set failed') {
      mes = "更新された検査項目セットに失敗しました"; //'System error, please try again later!';
    }
    if (message === 'Created inspection item set success') {
      mes = "検査項目セットの作成に成功しました"; //'System error, please try again later!';
    }
    if (message === 'Created inspection item set failed') {
      mes = "作成した検査項目セットに失敗しました"; //'System error, please try again later!';
    }
    if (message === 'Inspection item set deleted success!') {
      mes = "検査項目セットの削除に成功しました!"; //'System error, please try again later!';
    }
    if (message === 'Inspection item set deleted failed!') {
      mes = "検査項目セットの削除に失敗しました!"; //'System error, please try again later!';
    }
    if (message === 'required') {
      mes = "「この項目は必須です」";//'This field is required.';
    }
    if (message === 'minlength3') {
      mes = "「3桁から入力してください」"; //'Your enter a value must be at least 3 characters long';
    }
    return mes;
  }

  function makeBodyTable(data) {
    var link = $('#quickForm').attr('action');
    var text = '';
    for (var i = 0; i < data.length; i++) {
      text += '<tr>'+
        '<td>'+(i+1)+'</td>'+
        '<td>'+
          '<a onclick="return showModalEdit(this);" data-id="'+data[i].id+'" data-method="DETAIL" href="javascript:void(0)" class="nav-link p-0">'+
            '<i class="fas fa-eye"></i>'+
            ' '+data[i].inspection_item_set+
          '</a>'+
        '</td>'+
        '<td>'+
          '<p style="margin-bottom: 0px;"><i class="fas fa-clock"></i> '+data[i].created_at+'</p>'+
        '</td>'+
        '<td>'+
          '<p style="margin-bottom: 0px;"><i class="far fa-clock"></i> '+data[i].updated_at+'</p>'+
        '</td>'+
        '<td>'+
          '<p style="margin-bottom: 0px;"><i class="fas fa-user-edit"></i> '+data[i].updated_name+'</p>'+
        '</td>'+
        '<td>'+
          '<button style="margin-right: 5px;" data-href="'+link+'" onclick="return showModalEdit(this);" data-method="PUT" data-id="'+data[i].id+'" class="btn btn-primary mr-1" type="button">'+
              '<i class="fa fa-edit"></i> 編集'+
          '</button>'+
          '<button style="margin-right: 5px;" data-href="'+link+'" onclick="return deleteAction(this);" data-method="DELETE" data-id="'+data[i].id+'" class="btn btn-danger" type="button">'+
              '<i class="fa fa-trash"></i> 削除'+
          '</button>'+
        '</td>'+
      '</tr>';
    }
    return text;
  }

  function makeDataItem(el) {
    dataItems.push(Object.create(item));
    var text = makeTrItem();
    $('#table-inspection-item').find('tbody').html(text);
  }
  function makeTrItem() {
    var text = '';
    for (var i = 0; i < dataItems.length; i++) {
      text += '<tr>'+
        '<td class="col-3">'+
          '<div class="form-group row">'+
            '<div class="col-sm-12">'+
              '<input type="text" name="inspection_item[]" onkeyup="changeDataItem(this)" data-index="'+i+'" value="'+(dataItems[i].inspection_item ?? '')+'" class="form-control" id="inputInspectionItems'+i+'">'+
            '</div>'+
          '</div>'+
        '</td>'+
        '<td class="col-3">'+
          '<div class="form-group row">'+
            '<div class="col-sm-12">'+
              '<input type="text" name="inspection_point[]" onkeyup="changeDataItem(this)" data-index="'+i+'" value="'+(dataItems[i].inspection_point ?? '')+'" class="form-control" id="inputInspectionPoint'+i+'">'+
            '</div>'+
          '</div>'+
        '</td>'+
        '<td class="col-2">'+
          '<div class="form-group row">'+
            '<div class="col-sm-12">'+
              '<select name="inspection_period[]" onchange="changeDataItem(this)" data-index="'+i+'" class="form-control" id="inputPeriod'+i+'">'+
                  '<option style="display: none;" value=""></option>'+
                  '<option '+(dataItems[i].inspection_period === '1/D' ? 'selected' : '')+' value="1/D">1/D</option>'+
                  '<option '+(dataItems[i].inspection_period === '1/W' ? 'selected' : '')+' value="1/W">1/W</option>'+
                  '<option '+(dataItems[i].inspection_period === '1/M' ? 'selected' : '')+' value="1/M">1/M</option>'+
              '</select>'+
            '</div>'+
          '</div>'+
        '</td>'+
        '<td class="col-3">'+
          '<div class="form-group row">'+
            '<div class="col-sm-12">'+
              '<select name="typeof_inspector[]" onchange="changeDataItem(this)" data-index="'+i+'" class="form-control" id="inputInspector'+i+'">'+
                  '<option style="display: none;" value=""></option>'+
                  '<option '+(dataItems[i].typeof_inspector === '作業者' ? 'selected' : '')+' value="作業者">作業者</option>'+
                  '<option '+(dataItems[i].typeof_inspector === 'ﾘﾘｰﾌ' ? 'selected' : '')+' value="ﾘﾘｰﾌ">ﾘﾘｰﾌ</option>'+
              '</select>'+
            '</div>'+
          '</div>'+
        '</td>'+
        '<td class="col-1">'+
          '<div class="form-group row">'+
            '<div class="col-sm-1">'+
              '<button type="button" onclick="removeItem(this)" data-index="'+i+'" class="btn btn-danger btn-remove-item bg-delete"><i class="fa fa-times"></i></button>'+
            '</div>'+
          '</div>'+
        '</td>'+
      '</tr>';
    }
    return text;
  }

  function removeItem(el) {
    var index = $(el).attr('data-index');
    var datas = [];
    for (var i = 0; i < dataItems.length; i++) {
      if (i != index) {
        datas.push(dataItems[i]);
      }
    }
    dataItems = datas;
    var text = makeTrItem();
    $('#table-inspection-item').find('tbody').html(text);
  }

  function changeDataItem(el) {
    var index = $(el).attr('data-index');
    var value = $(el).val();
    var itemName = $(el).attr('id');
    itemName = itemName.replace(index, '');
    if (!dataItems[index]) {
      return;
    }
    if (itemName === 'inputInspectionItems') {
      dataItems[index].inspection_item = value;
    } else if (itemName === 'inputInspectionPoint') {
      dataItems[index].inspection_point = value;
    } else if (itemName === 'inputPeriod') {
      dataItems[index].inspection_period = value;
    } else if (itemName === 'inputInspector') {
      dataItems[index].typeof_inspector = value;
    }
  }

  function getCurrentURL() {
    return window.location.href
  }

  function getParam(url_string, param) {
    var url = new URL(url_string);
    var c = url.searchParams.get(param);
    return c;
  }
</script>
@endsection