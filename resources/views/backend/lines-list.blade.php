@php
$link = route('lines.store');
$linkCreate = route('machines.create');
@endphp

@extends('backend.layouts.app')
@section('title', '設備管理マスタ一覧')
@section('css')
<link rel="stylesheet" href="/plugins/sweetalert2/sweetalert2.css">
{{-- <link rel="stylesheet" href="/pluginssweetalert2-theme-bootstrap-4/bootstrap-4.min.css"> --}}
@endsection
@section('content_admin')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title"><blockquote class="bd-card-title m-0" style="padding: 0px; padding-left: 0.5rem;">設備管理マスタ 一覧</blockquote></h3>

        <div class="card-tools">
          <div class="input-group input-group-md">
            <div class="input-group-append">
              <!-- <button data-href="{{ $link }}" type="button" class="btn btn-success ml-3" onclick="showModalCreate(this)">
                <i class="fa fa-plus-square"></i> 新規登録
              </button> -->
              <a href="{{ $linkCreate }}" type="button" class="btn btn-success ml-3">
                <i class="fa fa-plus-square"></i> 新規登録
              </a>
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
              <th style="width: 33%;">ライン名</th>
              <th style="width: 16%;">最終更新</th>
              <th style="width: 10%;">操作</th>
            </tr>
          </thead>
          <tbody>
            @if (isset($blogs) && count($blogs))
          	@foreach ($blogs as $key => $value)
            <tr>
              <td>{{ $key + 1 }}</td>
              <td>
              	<p style="margin-bottom: 0px;">{{ $value->line_code }}</p>
              </td>
              <td>
                <p style="margin-bottom: 0px;">{{ $value->line_name }}</p>
              </td>
              <td>
              	<p style="margin-bottom: 0px;"><i class="fas fa-user-edit"></i> {{ isset($value->updater->user_name) ? $value->updater->user_name : '' }}</p>
              	<p style="margin-bottom: 0px;"><i class="far fa-clock"></i> {{ $value->updated_at }}</p>
              </td>
              <td>
                <button style="margin-right: 5px;" data-href="{{ $link }}" onclick="return showModalEdit(this);" data-method="PUT" data-id="{{ $value->id }}" data-code="{{ $value->line_code }}" data-name="{{ $value->line_name }}" class="btn btn-primary mr-1" type="button">
                    <i class="fa fa-edit"></i> 編集
                </button>
                <button style="margin-right: 5px;" data-href="{{ $link }}" onclick="return deleteAction(this);" data-method="DELETE" data-id="{{ $value->id }}" data-code="{{ $value->line_code }}" data-name="{{ $value->line_name }}" class="btn btn-danger" type="button">
                    <i class="fa fa-trash"></i> 削除
                </button>
              </td>
            </tr>
            @endforeach
            @endif
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
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-body" id="modal-body-create">
        @include('backend.lines-modal-create')
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
  var linkCreate = '{{$linkCreate}}';
  $(function () {
    initialDataTable();
  });
  function initialDataTable() {
    var link = $('#quickForm').attr('action');
    var _token = $('meta[name="csrf-token"]').attr('content');
    var page = getParam(getCurrentURL(), 'page');
    $.ajax({
      type: 'POST',
      url: link,
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
    $('#res-message').html('');
    $('#exampleInputID1').val('');
    $('#exampleInputCode1').val('');
    $('#exampleInputName1').val('');
    $('#modal-card-title').text(getMessage('Facility management master create'));
    $('#modal-btn-submit').text('新規登録');
    $('#modal-btn-submit').prop('disabled', false);
    clearValidation();
    $('#modal-xl-create').modal('show');
  }
  function showModalEdit(el) {
    $('#res-message').html('');
    $('#exampleInputID1').val($(el).attr('data-id'));
    $('#exampleInputCode1').val($(el).attr('data-code'));
    $('#exampleInputName1').val($(el).attr('data-name'));
    $('#modal-card-title').text(getMessage('Facility management master edit'));
    $('#modal-btn-submit').text('アップデート');
    $('#modal-btn-submit').prop('disabled', false);
    clearValidation();
    $('#modal-xl-create').modal('show');
  }
  function hideModal(el) {
    $('#modal-xl-create').modal('hide');
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

  function submitForm(el) {
    if (validator) {
      validator.destroy();
    }
    $.validator.setDefaults({
      submitHandler: function () {
        var link = $('#quickForm').attr('action');
        var _token = $('meta[name="csrf-token"]').attr('content');
        var id = $('#exampleInputID1').val();
        var code = $('#exampleInputCode1').val();
        var name = $('#exampleInputName1').val();
        var data = {id, code, name, _token};
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
              return;
            } else {
              $('#res-message').html(makeMessage(data.status, data.message));
              initialDataTable();
              return;
            }
          },
          error: function (jqXHR, textStatus, errorThrown) {
            return $('#res-message').html(makeMessage('danger', 'Error system'));
            // window.location.reload(true);
          }
        });
      }
    });
    validator = $('#quickForm').validate({
      rules: {
        line_code: {
          required: true,
          minlength: 3
        },
        line_name: {
          required: true,
          minlength: 3
        },
      },
      messages: {
        line_code: {
          required: getMessage('required'),
          minlength: getMessage('minlength3'),
        },
        line_name: {
          required: getMessage('required'),
          minlength: getMessage('minlength3'),
        },
      },
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
    $('#quickForm').submit();
  }

  function deleteAction(el) {
    Swal.fire({
		  title: '削除しますか？',
      // text: "",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: '削除します',
      confirmButtonColor: '#3085d6',
      cancelButtonText: 'キャンセル',
      cancelButtonColor: '#d33',
		}).then((result) => {
		  // Read more about isConfirmed, isDenied below 
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
          Swal.fire({
            title: "削除されました", 
            // getMessage(data.message), 
            icon: "success",
            confirmButtonText: "戻る"
          });
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
          '<p style="margin-bottom: 0px;">'+data[i].line_code+'</p>'+
        '</td>'+
        '<td>'+
          '<p style="margin-bottom: 0px;">'+data[i].line_name+'</p>'+
        '</td>'+
        '<td>'+
          '<p style="margin-bottom: 0px;"><i class="fas fa-user-edit"></i> '+data[i].updated_name+'</p>'+
          '<p style="margin-bottom: 0px;"><i class="far fa-clock"></i> '+data[i].updated_at+'</p>'+
        '</td>'+
        '<td>'+
          '<button style="margin-right: 5px;" data-href="'+link+'" onclick="return btnEdit(this);" data-method="PUT" data-id="'+data[i].id+'" data-code="'+data[i].line_code+'" data-name="'+data[i].line_name+'" class="btn btn-primary mr-1" type="button">'+
              '<i class="fa fa-edit"></i> 編集'+
          '</button>'+
          '<button style="margin-right: 5px;" data-href="'+link+'" onclick="return deleteAction(this);" data-method="DELETE" data-id="'+data[i].id+'" data-code="'+data[i].line_code+'" data-name="'+data[i].line_name+'" class="btn btn-danger" type="button">'+
             '<i class="fa fa-trash"></i> 削除'+
          '</button>'+
        '</td>'+
      '</tr>';
    }
    return text;
  }

  function btnEdit(el) {
    var code = $(el).attr('data-code');
    var link = linkCreate + '?code=' + code;
    window.location.href = link;
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