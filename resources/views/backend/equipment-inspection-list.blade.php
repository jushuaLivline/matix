@php
$linkList = route('equipment-inspection.list');
$link = route('equipment-inspection.store');
$linkDaily = str_replace('/equipment-inspection', '/daily-production-control-table', $link)
@endphp

@extends('backend.layouts.app')
@section('title', '設備点検票 一覧')
@section('css')
<link rel="stylesheet" href="/plugins/sweetalert2/sweetalert2.css">
{{-- <link rel="stylesheet" href="/pluginssweetalert2-theme-bootstrap-4/bootstrap-4.min.css"> --}}
@endsection
@section('content_admin')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title"><blockquote class="bd-card-title m-0" style="padding: 0px; padding-left: 0.5rem;">設備点検票 一覧</blockquote></h3>

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
              <th style="width: 15%;">ラインコード</th>
              <th style="width: 15%;">ライン名</th>
              <th style="width: 5%;">工程</th>
              <th style="width: 10%;">作成日</th>
              <th style="width: 10%;">最終更新日</th>
              <th style="width: 10%;">作成者</th>
              <th style="width: 5%;">ステータス</th>
              <th style="width: 10%;">操作</th>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
      </div>
      <div id="pagination-link">
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
  var linkList = '{{ $linkList }}';
  var link = '{{ $link }}';
  var linkDaily = '{{ $linkDaily }}';
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
  function redirectToUrl(url) {
    // similar behavior as an HTTP redirect
    // window.location.replace(url);

    // similar behavior as clicking on a link
    return window.location.href = url;
  }
  function showModalCreate(el) {
    return redirectToUrl(link + '/create');
    // $('#res-message').html('');
    // $('#exampleInputID1').val('');
    // $('#exampleInputCode1').val('');
    // $('#exampleInputName1').val('');
    // $('#modal-card-title').text('Facility management master create');
    // $('#modal-btn-submit').text('新規登録');
    // $('#modal-btn-submit').prop('disabled', false);
    // $('#modal-xl-create').modal('show');
  }
  function showModalEdit(el) {
    var id = $(el).attr('data-id');
    return redirectToUrl(link + '/' + id + '/edit');
    // $('#res-message').html('');
    // $('#exampleInputID1').val($(el).attr('data-id'));
    // $('#exampleInputCode1').val($(el).attr('data-code'));
    // $('#exampleInputName1').val($(el).attr('data-name'));
    // $('#modal-card-title').text('Facility management master edit');
    // $('#modal-btn-submit').text('アップデート');
    // $('#modal-btn-submit').prop('disabled', false);
    // $('#modal-xl-create').modal('show');
  }
  function showModalTablet(el) {
    var id = $(el).attr('data-id');
    var method = $(el).attr('data-method');
    if (method === 'DAILY') {
      return redirectToUrl(linkDaily + '/' + id + '/edit');
    }else if (method === 'REFERENCE') {
      return redirectToUrl(linkDaily + '/' + id + '/reference');
    }
    return redirectToUrl(link + '/' + id + '/tablet');
    // $('#res-message').html('');
    // $('#exampleInputID1').val($(el).attr('data-id'));
    // $('#exampleInputCode1').val($(el).attr('data-code'));
    // $('#exampleInputName1').val($(el).attr('data-name'));
    // $('#modal-card-title').text('Facility management master edit');
    // $('#modal-btn-submit').text('アップデート');
    // $('#modal-btn-submit').prop('disabled', false);
    // $('#modal-xl-create').modal('show');
  }
  function hideModal(el) {
    $('#modal-xl-create').modal('hide');
  }

  // function submitForm(el) {
  //   $.validator.setDefaults({
  //     submitHandler: function () {
  //       var _token = $('meta[name="csrf-token"]').attr('content');
  //       var id = $('#exampleInputID1').val();
  //       var code = $('#exampleInputCode1').val();
  //       var name = $('#exampleInputName1').val();
  //       var data = {id, code, name, _token};
  //       $.ajax({
  //         type: 'POST',
  //         url: link,
  //         data,
  //         beforeSend: function () {
  //           $('#modal-btn-submit').prop('disabled', true);
  //         },
  //         success: function(data) {
  //           $('#modal-btn-submit').prop('disabled', false);
  //           if (data.status == 'error') {
  //             $('#res-message').html(makeMessage(data.status, data.message));
  //             return;
  //           } else {
  //             $('#res-message').html(makeMessage(data.status, data.message));
  //             initialDataTable();
  //             return;
  //           }
  //         },
  //         error: function (jqXHR, textStatus, errorThrown) {
  //           return $('#res-message').html(makeMessage('danger', 'Error system'));
  //           // window.location.reload(true);
  //         }
  //       });
  //     }
  //   });
  //   $('#quickForm').validate({
  //     rules: {
  //       line_code: {
  //         required: true,
  //         minlength: 3
  //       },
  //       line_name: {
  //         required: true,
  //         minlength: 3
  //       },
  //     },
  //     messages: {
  //       line_code: {
  //         required: "Please enter a line code",
  //         minlength: "Your line code must be at least 3 characters long"
  //       },
  //       line_name: {
  //         required: "Please provide a line name",
  //         minlength: "Your line name must be at least 3 characters long"
  //       },
  //     },
  //     errorElement: 'span',
  //     errorPlacement: function (error, element) {
  //       error.addClass('invalid-feedback');
  //       element.closest('.form-group').append(error);
  //     },
  //     highlight: function (element, errorClass, validClass) {
  //       $(element).addClass('is-invalid');
  //     },
  //     unhighlight: function (element, errorClass, validClass) {
  //       $(element).removeClass('is-invalid');
  //     }
  //   });
  //   $('#quickForm').submit();
  // }

  function deleteAction(el) {
    Swal.fire({
      title: 'Are you sure?',
      text: "You won't be able to revert this!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes, delete it!'
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
          Swal.fire("Deleted!", data.message , "error");
          return;
        } else {
          // a.prop('disabled', true);
          a.parent().parent().remove();
          Swal.fire("Deleted!", data.message , "success");
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        Swal.fire("Deleted!", getMessage('Error system') , "error");
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
      mes = "「この項目は必須です」";//'This field is required.';
    }
    return mes;
  }

  function makeBodyTable(data) {
    var text = '';
    for (var i = 0; i < data.length; i++) {
      text += '<tr>'+
              '<td>'+(i+1)+
              '</td>'+
              '<td>'+
                '<a onclick="return showModalTablet(this);" data-id="'+data[i].id+'" data-method="TABLET" href="javascript:void(0)" class="nav-link p-0"><i class="fas fa-tablet-alt"></i> '+data[i].line_code+'</a>'+
              '</td>'+
              '<td>'+data[i].line_name+
              '</td>'+
              '<td>'+
                '<p style="margin-bottom: 0px;">'+(data[i].process_id ?? '')+'</p>'+
              '</td>'+
              '<td>'+
                '<p style="margin-bottom: 0px;"><i class="fas fa-clock"></i> '+data[i].created_at+'</p>'+
              '</td>'+
              '<td>'+
                '<p style="margin-bottom: 0px;"><i class="far fa-clock"></i> '+data[i].updated_at+'</p>'+
              '</td>'+
              '<td>'+
                '<p style="margin-bottom: 0px;"><i class="fas fa-user-edit"></i> '+data[i].created_name+'</p>'+
              '</td>'+
              '<td>'+
                '<p style="margin-bottom: 0px;"> '+makeStatus(data[i].status)+'</p>'+
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

  function makeStatus(status) {
    var text = status;
    if (status === '確認') {
      text = '<span class="text-primary">'+status+'</span>';
    }
    if (status === '承認') {
      text = '<span class="text-success">'+status+'</span>';
    }
    return text;
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