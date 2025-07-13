@php
$link = route('machines.store');
$linkLine = route('lines.store');
@endphp

@extends('backend.layouts.app')
@section('title', '設備管理マスタ 登録・編集')
@section('css')
<link rel="stylesheet" href="/plugins/sweetalert2/sweetalert2.css">
@vite('resources/css/order/style.css')
<style>
  .form-control[readonly] {
    border: 1px solid #ced4da !important;
    background-color: white !important;
  }
  #modal-body-create {
    max-height: 85vh;
    overflow-y: auto;
    overflow-x: hidden;
  }
  .clear-button {
    padding: 10px;
    border-radius: 5px;
    background-color: #0068ae;
    border: #0077c7 solid 1px;
    color: white;
  }
  .fa-search {
    margin-top: 6px;
  }
  .fa-search:before {
    content: '';
    background-image: url('/images/icons/magnifying_glass.svg');
    width: 24px;
    height: 24px;
    display: block;
    background-size: cover;
  }
  .clear-button {
    font-size: 13px;
  }
  .bg-search {
    padding: 0px 10px !important;
  }
</style>
{{-- <link rel="stylesheet" href="/pluginssweetalert2-theme-bootstrap-4/bootstrap-4.min.css"> --}}
@endsection
@section('content_admin')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title"><blockquote class="bd-card-title m-0" style="padding: 0px; padding-left: 0.5rem;">設備管理マスタ 登録・編集</blockquote></h3>
      </div>
      <!-- /.card-header -->
      <form class="form-horizontal" id="quickForm" action="{{$link}}" method="post" accept-charset="utf-8" enctype="multipart/form-data">
        <div class="card-body bg-light">
          <div id="res-message"></div>
        	<div class="card">
  			      <div class="card-body">
  							<div class="form-group row">
  		            <label for="inputLineId3" class="col-sm-2 col-md-1 col-form-label">ライン <span class="btn badge bg-orange">必須</span></label>
  		            <div class="col-sm-7">
  		            	<div class="row">
  		            		<div class="col-sm-3 form-group">
  				              <input type="text" name="line_code" readonly="true" class="form-control" id="inputLineCode3" onkeypress="enterSearchLine(event)">
                        <input type="hidden" class="form-control" id="inputLineId3">
  				            </div>
  				            <div class="col-sm-3 form-group">
  				              <input type="text" class="form-control" id="inputLine3" style="color:black" disabled>
  				            </div>
  				            <div class="col-sm-1">
  				              <button id="btn-search-line" onclick="btnSearchLine(this)" type="button" class="btn btn-primary bg-search"><i class="fa fa-search"></i></button>
                        <button style="display: none;" id="btn-search-line-init" onclick="searchLine(this)" type="button" class="btn btn-primary bg-search"><i class="fa fa-search"></i></button>
  				            </div>
  		            	</div>
  		            </div>
  		          </div>
  			      </div>
  				</div>
  				<!-- /.card -->
  				<div class="card">
  		    		<div class="card-body">
  							<div class="form-group row">
  		            <label class="col-sm-2 col-md-1 col-form-label">設備 <span class="btn badge bg-orange">必須</span></label>
  		            <div class="col-sm-7">
  		              <table id="table-machines" class="table table-bordered" style="border: none;">
  		              	<thead>
  		              		<tr class="bg-light">
  			              		<th class="col-6" style="text-align: center;">機番</th>
  			              		<th class="col-5" style="text-align: center;">予防保全回数</th>
  			              		<th class="col-1"></th>
  			              	</tr>
  		              	</thead>
  		              	<tbody>
  		              	</tbody>
  		              	<tfoot>
  		              		<tr>
  		              			<th colspan="3" style="border: none;">
  		              				<div class="row float-right">
                              {{-- <div style="padding: .375rem .75rem; margin-right: 15px; display: none;" id="res-message-limit" class="text-thin text-danger">Limit 10 machine</div> --}}
  		              					<button onclick="makeDataItemMachine(this)" type="button" class="btn btn-success bg-add">行を追加</button>
  		              				</div>
  		              			</th>
  		              		</tr>
  		              	</tfoot>
  		              </table>
  		            </div>
  		          </div>
  			      </div>
  		    </div>
  		    <!-- /.card -->
  		    <div class="card">
  		    	<div id="machines-store">
  		    	</div>
  		    </div>
  		    <!-- /.card -->
  			</div>
      </form>
			<div class="card-footer">
        <button id="modal-btn-submit" onclick="submitForm(this)" type="button" class="btn btn-success">登録する</button>
      </div>
      <!-- /.card-body -->
    </div>
    <!-- /.card -->
  </div>
</div>
<!-- /.row -->
<div class="modal fade" id="modal-xl-create" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl w_percent_30 modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-body" id="modal-body-create">
        <button type="button" data-dismiss="modal" aria-label="Close" class="modalCloseBtn js-modal-close">x</button>
        <div class="modalInner">
            <form action="#" accept-charset="utf-8">
                <div class="section">
                    <div class="boxModal mb-1">
                        <div class="mr-0">
                            <label class="form-label dotted indented label_for">ライン選択</label>
                            <div class="flex searchModal">
                                <input type="hidden" id="model" value="Line">
                                <input type="hidden" id="searchLabel" value="ライン一覧">
                                <input type="text" class="w-100 mr-half" placeholder="検索キーワードを入力" name="keyword" onkeyup="ajaxBtnSearchLine('#modal-xl-create')">
                                <ul class="searchResult" id="search-result" data-result-value-element="line_code" data-result-name-element="line_name">
                                </ul>
                                <div class="clear text-center">
                                    <button type="button" onclick="btnReset('#modal-xl-create')" id="clear" class="clear-button" data-result-value-element="line_code" data-result-name-element="line_name">
                                        選択した値をクリアする
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<div class="modal fade" id="modal-machines" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl w_percent_30 modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-body" id="modal-body-machines">
        <button type="button" data-dismiss="modal" aria-label="Close" class="modalCloseBtn js-modal-close">x</button>
        <div class="modalInner">
            <form action="#" accept-charset="utf-8">
                <div class="section">
                    <div class="boxModal mb-1">
                        <div class="mr-0">
                            <label class="form-label dotted indented label_for">機番マスタ選択</label>
                            <div class="flex searchModal">
                                <input type="hidden" id="_index" value="">
                                <input type="hidden" id="_data_origin" value="">
                                <input type="hidden" id="model1" value="MachineNumber">
                                <input type="hidden" id="searchLabel1" value="機番マスタ一覧">
                                <input type="text" class="w-100 mr-half" placeholder="検索キーワードを入力" name="keyword" onkeyup="ajaxBtnSearchMachine('#modal-machines')">
                                <ul class="searchResult" data-result-value-element="machine_code" data-result-name-element="machine_name">
                                </ul>
                                <div class="clear text-center">
                                    <button type="button" onclick="btnReset1('#modal-machines')" id="clear1" class="clear-button" data-result-value-element="machine_code" data-result-name-element="machine_name">
                                        選択した値をクリアする
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
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
<script src="/plugins/sweetalert2/sweetalert2.min.js"></script>
<script>
  var linkLine = '{{$linkLine}}';
  var lineId;
  var validator;
  var validatorLine;
  var validatorMachine;
  var itemCutlery = {
    cutlery: null,
    remarks: null,
    number_of_uses: null,
  };
  var itemMachine = {
    machine_number: null,
    machine_name: null,
    number_of_maintenance: null,
    json_data: null,
  };
  var _code = '';
  var _edit = false;
  var dataItems = [];
  $(function () {
    _code = getParam(getCurrentURL(), 'code');
    if (_code) {
      _edit = true;
      $('#inputLineCode3').val(_code).attr('data-origin', _code).css('background-color', 'transparent');
      $('#btn-search-line-init').click();
    }
    // initialDataTable();
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

  function searchItemMachineNumber(el) {

    $('#modal-machines').find('input[id=_data_origin]').val('').attr('');
    var ind = $(el).attr('data-index');
    var data_origin = $('#inputMachineId'+ind).attr('data-origin');

    $('#modal-machines').find('input[id=_index]').val(ind).attr(ind);

    if (data_origin) {
      $('#modal-machines').find('input[id=_data_origin]').val(data_origin).attr(data_origin);
    }

    $('#modal-machines').modal('show');
    ajaxBtnSearchMachine('#modal-machines');
  }

  function btnReset1(el) {
    var ind = $('#modal-machines').find('input[id=_index]').val();
    var machine_code = $('#inputMachineId'+ind).attr('data-origin');
    $('#inputMachineId'+ind).val('');
    $('#inputMachine'+ind).val('');
    $('#inputMachineNumber'+ind).val('');
    if (machine_code) {
      $('#inputMachineId'+ind).val(machine_code);
      $('#btn-search-machine'+ind).click();
    }
    $(el).modal('hide');
  }

  function selectMachine(el) {
    var ind = $('#modal-machines').find('input[id=_index]').val();
    var data_origin = $('#modal-machines').find('input[id=_data_origin]').val();
    var data_origin = '';
    var machine_code = $(el).attr('data-value');

    // if (machine_code) {
      // machine_code = machine_code;
    // }

    $('#inputMachineId'+ind).val(machine_code);
    $('#inputMachineId'+ind).attr('data-origin', '');
    $('#inputMachineId'+ind).trigger('change');

    if (machine_code) {
      $('#btn-search-machine'+ind).click();
    }
    $('#modal-machines').modal('hide');
    if (data_origin) {
      setTimeout(() => {
        $('#inputMachineId'+ind).attr('data-origin', data_origin);
      }, 250);
    }
  }

  function searchItemMachine(el) {

    console.log('test active')

    console.log(el)
    
    if (validatorLine) {
      validatorLine.destroy();
    }
    if (validatorMachine) {
      validatorMachine.destroy();
    }
    if (validator) {
      validator.destroy();
    }

    var index = $(el).attr('data-index');
    var elName = `#inputMachineId${index}`;

    console.log(elName)

    var inputName = `machine_number${index}`;

    $(`#inputMachine${index}`).val('');
    $(`#inputMachineNumber${index}`).val('');
    $('#res-message').html('');

    var searchRules = {};
    var searchMessages = {};

    searchRules[inputName] = {
      required: true,
    };

    searchMessages[inputName] = {
      required: 'Please enter a machine number',
    };

    validatorMachine = $('#quickForm').validate({
      rules: searchRules,
      messages: searchMessages,
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

    if (! validatorMachine.form()) {
      return;
    }
    // $('#formLineCode').submit();
    var _token = $('meta[name="csrf-token"]').attr('content');
    var link = $('#quickForm').attr('action');
    var id = $(elName).val();

    console.log(id)

    $.ajax({
      type: 'POST',
      url: link,
      data : {method: 'SEARCH', id, _token },
      beforeSend: function () {
      },
      success: function(data) {
        
        console.log('results')
        
        if (data.status == 'error') {
          $('#res-message').html(makeMessage(data.status, data.message));
          setTimeout(function(){ $('#res-message').html('') }, 5000);

          return;

        } else {

          dataItems[index].machine_name = data.data.machine_name;
          dataItems[index].number_of_maintenance = data.data.number_of_maintenance;

          $(`#inputMachine${index}`).val(data.data.machine_name);
          $(`#inputMachineNumber${index}`).val(data.data.number_of_maintenance);

          if (data.data.json_data && data.data.json_data.length) {
            dataItems[index].json_data = data.data.json_data;

            var text = makeTrItemMachine();
            $('#table-machines').find('tbody').html(text);

          } else {
            dataItems[index].json_data = data.data.json_data;
          }

          var textCard = makeCardItemMachine();
          $('#machines-store').html(textCard);
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        // window.location.reload(true);
      }
    });
  }

  function btnReset(el) {
    var line_code = $('#inputLineCode3').attr('data-origin');
    $('#inputLineCode3').val(line_code);
    $('#inputLine3').val('');
    if (line_code) {
      $('#btn-search-line-init').click();
    }
    $(el).modal('hide');
  }

  function selectLine(el) {
    var line_code = $(el).attr('data-value');

    

    $('#inputLineCode3').val(line_code);
    $('#modal-xl-create').modal('hide');
    $('#btn-search-line-init').click();
  }

  function btnSearchLine(el) {
    $('#modal-xl-create').modal('show');
    ajaxBtnSearchLine('#modal-xl-create');
  }

  function ajaxBtnSearchLine(el) {
    var model = $(el).find('#model').val()
    var searchLabel = $(el).find('#searchLabel').val();
    var searchQuery = $(el).find('input[name="keyword"]').val();
    $.ajax({
        url: '/search',
        method: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            model: model,
            query: searchQuery,
        },
        success: function(response) {
            $(el).find('.searchResult').empty();

            // Iterate over the response and append list items
            $.each(response, function(index, value) {
                var listItem = $('<li>').attr('data-value', value.code).text(value.name).attr('onclick', 'selectLine(this)');
                $(el).find('.searchResult').append(listItem);
            });

            // Add the disabled list item
            var disabledListItem = $('<li>').addClass('disabled').text(searchLabel);
            $(el).find('.searchResult').prepend(disabledListItem);
        },
        error: function(xhr, status, error) {
          // Handle errors
          console.log(error);
        }
    });
  }

  function ajaxBtnSearchMachine(el) {
    var model = $(el).find('#model1').val()
    var searchLabel = $(el).find('#searchLabel1').val();
    var searchQuery = $(el).find('input[name="keyword"]').val();
    $.ajax({
        url: linkLine,
        method: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            model: model,
            method: 'SEARCH-MACHINE-NUMBER',
            query: searchQuery,
        },
        success: function(response) {

            $(el).find('.searchResult').empty();

            // Iterate over the response and append list items
            $.each(response, function(index, value) {
                var listItem = $('<li>').attr('data-value', value.code).text(value.name).attr('onclick', 'selectMachine(this)');
                $(el).find('.searchResult').append(listItem);
            });

            // Add the disabled list item
            var disabledListItem = $('<li>').addClass('disabled').text(searchLabel);
            $(el).find('.searchResult').prepend(disabledListItem);
        },
        error: function(xhr, status, error) {
          // Handle errors
          console.log(error);
        }
    });
  }
  function searchLine(el) {
    if (validatorLine) {
      validatorLine.destroy();
    }
    if (validatorMachine) {
      validatorMachine.destroy();
    }
    if (validator) {
      validator.destroy();
    }
    $('#inputLineId3').val('');
    $('#inputLine3').val('');
    $('#res-message').html('');

    validatorLine = $('#quickForm').validate({
      rules: {
        line_code: { required: true },
      },
      messages: {
        line_code: {
          required: getMessage('required'),
        }
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
    if (! validatorLine.form()) {
      return;
    }
    // $('#formLineCode').submit();
    var _token = $('meta[name="csrf-token"]').attr('content');
    var id = $('#inputLineCode3').val();
    $.ajax({
      type: 'POST',
      url: linkLine,
      data : {method: 'SEARCH', id, _token },
      beforeSend: function () {
      },
      success: function(data) {
        // reset div on parent line search
        var table = document.getElementById("table-machines");
        for (var i = table.rows.length - 2; i > 0; i--) {
            table.deleteRow(i);
        }

        var machinesStoreDiv = document.querySelector('.card #machines-store');
        while (machinesStoreDiv.firstChild) {
          machinesStoreDiv.removeChild(machinesStoreDiv.firstChild);
        }
        dataItems = []

        if (data.status == 'error') {
          $('#res-message').html(makeMessage(data.status, data.message));
          setTimeout(function(){ $('#res-message').html('') }, 5000);

          return;
        } else {
          lineId = data.data.id;
          $('#inputLineId3').val(data.data.id);
          $('#inputLine3').val(data.data.line_name);
          if (data.data.json_data && data.data.json_data.length) {
            dataItems = [];
            for (var i = 0; i < data.data.json_data.length; i++) {
              dataItems.push(Object.create(data.data.json_data[i]));
            }
            var text = makeTrItemMachine();
            $('#table-machines').find('tbody').html(text);
            var textCard = makeCardItemMachine();
            $('#machines-store').html(textCard);
          }
          // var links = data.data.links;
          // if (links) {
          //   links = '<div class="card-footer clearfix">'+
          //     links+
          //   '</div>';
          // }
          // var tbody = makeBodyTable(data.data.items);
          // $('#pagination-link').html(links);
          // $('#table-data').find('tbody').html(tbody);
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        // window.location.reload(true);
      }
    });
  }

  // function showModalCreate(el) {
  //   $('#res-message').html('');
  //   $('#exampleInputID1').val('');
  //   $('#exampleInputCode1').val('');
  //   $('#exampleInputName1').val('');
  //   $('#modal-card-title').text('Facility management master create');
  //   $('#modal-btn-submit').text('新規登録');
  //   $('#modal-btn-submit').prop('disabled', false);
  //   $('#modal-xl-create').modal('show');
  // }
  // function showModalEdit(el) {
  //   $('#res-message').html('');
  //   $('#exampleInputID1').val($(el).attr('data-id'));
  //   $('#exampleInputCode1').val($(el).attr('data-code'));
  //   $('#exampleInputName1').val($(el).attr('data-name'));
  //   $('#modal-card-title').text('Facility management master edit');
  //   $('#modal-btn-submit').text('アップデート');
  //   $('#modal-btn-submit').prop('disabled', false);
  //   $('#modal-xl-create').modal('show');
  // }
  // function hideModal(el) {
  //   $('#modal-xl-create').modal('hide');
  // }

  function getRules() {
    var rules = {};
    rules['line_code'] = {
      required: true,
    };
    var messages = {};
    messages['line_code'] = {
      required: getMessage('required'),
    };
    rules['machine_number[]'] = {
      required: true,
      digits: true,
    };
    messages['machine_number[]'] = {
      required: getMessage('required'),
      digits: getMessage('digits'),
    };
    rules['number_of_maintenance[]'] = {
      required: true,
      digits: true,
    };
    messages['number_of_maintenance[]'] = {
      required: getMessage('required'),
      digits: getMessage('digits'),
    };
    rules['cutlery[]'] = {
      required: true,
    };
    messages['cutlery[]'] = {
      required: getMessage('required'),
    };
    // rules['remarks[]'] = {
    //   required: true,
    // };
    // messages['remarks[]'] = {
    //   required: getMessage('required'),
    // };
    rules['number_of_uses[]'] = {
      required: true,
      digits: true,
    };
    messages['number_of_uses[]'] = {
      required: getMessage('required'),
      digits: getMessage('digits'),
    };
    // $('[id^=inputMachineNumberOfUse]').each(function(e) {
    //   var inputName = $(this).attr('name');
    //   console.log(374, inputName);
    //   rules[inputName] = {
    //     required: true,
    //   };
    //   messages[inputName] = {
    //     required: "Please enter a number of uses",
    //   };
    // });
    return {rules, messages};
  }

  function submitForm(el) {
    if (validatorLine) {
      validatorLine.destroy();
    }
    if (validatorMachine) {
      validatorMachine.destroy();
    }
    if (validator) {
      validator.destroy();
    }
    // $.validator.setDefaults({
    //   submitHandler: function () {
    //   }
    // });
    var {rules, messages} = getRules();
    // console.log('rules', rules);
    // console.log('messages', messages);
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
    var code = $('#inputLineCode3').val();
    var data = {id: lineId, code, dataItems, _token};
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
          // initialDataTable();
          return;
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        $('#res-message').html(makeMessage('danger', 'Error system'));
        document.getElementById("res-message").scrollIntoView();
        setTimeout(function(){ $('#res-message').html('') }, 5000);
        return;
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
    if (message === 'System error') {
      mes = "「システムエラーが発生しました。再度試してください。」"; //'System error, please try again later!';
    }
    if (message === 'Updated machine success') {
      mes = "データは正常に登録されました";
    }
    if (message === 'Updated machine failed') {
      mes = "更新されたマシンが失敗しました"; //'System error, please try again later!';
    }
    if (message === 'Please input line code correct and choose search button') {
      mes = "見つからない行"; //'System error, please try again later!';
    }
    if (message === 'Not exists line code') {
      mes = "行が存在しません"; //'System error, please try again later!';
    }
    if (message === 'Not found machine') {
      mes = "マシンが見つかりません"; //'System error, please try again later!';
    }
    if (message === 'Not found line code') {
      mes = "見つからない行"; //'System error, please try again later!';
    }
    if (message === 'required') {
        mes = "「この項目は必須です」";//'This field is required.';
      }
      if (message === 'digits') {
        // mes = 'Please enter only digits.';
        mes = '数字のみ入力して下さい。';
      }
    return mes;
  }

  function enterSearchLine(event) {
    if (event.key === "Enter") {
      // Cancel the default action, if needed
      event.preventDefault();
      // Trigger the button element with a click
      document.getElementById("btn-search-line").click();
    }
  }

  function enterSearchItemMachine(el, event) {
    var index = $(el).attr('data-index');
    var btnName = `btn-search-machine${index}`;
    if (event.key === "Enter") {
      // Cancel the default action, if needed
      event.preventDefault();
      // Trigger the button element with a click
      document.getElementById(btnName).click();
    }
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
          '<button style="margin-right: 5px;" data-href="'+link+'" onclick="return showModalEdit(this);" data-method="PUT" data-id="'+data[i].id+'" data-code="'+data[i].line_code+'" data-name="'+data[i].line_name+'" class="btn btn-primary mr-1" type="button">'+
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

  function makeDataItemMachine(el) {
    $('#res-message-limit').hide();

    // if (dataItems.length == 10) {
    //   $('#res-message-limit').show();
    //   setTimeout(() => { $('#res-message-limit').hide();}, 1000);

    //   return;
    // }

    dataItems.push(Object.create(itemMachine));
    
    var text = makeTrItemMachine();

    $('#table-machines').find('tbody').html(text);
    var textCard = makeCardItemMachine();
    $('#machines-store').html(textCard);
  }

  function makeTrItemMachine() {
    var text = '';
    var data_origin = '';

    for (var i = 0; i < dataItems.length; i++) {
      data_origin = '';

      if (_edit) {
        data_origin = ' data-origin="'+(dataItems[i].machine_number ?? '')+'" ';
      }

      text += '<tr>'+
                '<td class="col-6">'+
                  '<div class="form-group row">'+
                    '<div class="col-sm-5 form-group">'+
                      '<input readonly="true"'+data_origin+'type="number" min="1" max="99999" name="machine_number[]" onkeypress="enterSearchItemMachine(this, event)" onchange="changeDataItemMachine(this)" onkeyup="changeDataItemMachine(this)" data-index="'+i+'" value="'+(dataItems[i].machine_number ?? '')+'" class="form-control text-right" id="inputMachineId'+i+'">'+
                    '</div>'+
                    '<div class="col-sm-5 form-group">'+
                      '<input type="text" name="machine_name[]" onkeyup="changeDataItemMachine(this)" data-index="'+i+'" value="'+(dataItems[i].machine_name ?? '')+'" class="form-control" id="inputMachine'+i+'" disabled style="color:black;">'+
                    '</div>'+
                    '<div class="col-sm-1">'+
                      '<button style="display: none;" id="btn-search-machine'+i+'" type="button" onclick="searchItemMachine(this)" data-index="'+i+'" class="btn btn-primary bg-search"><i class="fa fa-search"></i></button>'+
                      '<button id="btn-search-machine-number'+i+'" type="button" onclick="searchItemMachineNumber(this)" data-index="'+i+'" class="btn btn-primary bg-search"><i class="fa fa-search"></i></button>'+
                    '</div>'+
                  '</div>'+
                '</td>'+
                '<td class="col-5">'+
                  '<div class="form-group row">'+
                    '<div class="col-sm-12">'+
                      '<input type="number" min="1" max="1000000" name="number_of_maintenance[]" onchange="changeDataItemMachine(this)" onkeyup="changeDataItemMachine(this)" data-index="'+i+'" value="'+(dataItems[i].number_of_maintenance ?? '')+'" class="form-control text-right" id="inputMachineNumber'+i+'">'+
                    '</div>'+
                  '</div>'+
                '</td>'+
                '<td class="col-1">'+
                  '<div class="form-group row">'+
                    '<div class="col-sm-1">'+
                      '<button type="button" onclick="removeItemMachine(this)" data-index="'+i+'" class="btn btn-danger bg-delete"><i class="fa fa-times"></i></button>'+
                    '</div>'+
                  '</div>'+
                '</td>'+
              '</tr>';
    }

    if (_edit) {
      _edit = false;
    }

    return text;
  }

  function makeCardItemMachine() {
    var text = '';
    var textCutlery = '';

    for (var i = 0; i < dataItems.length; i++) {
      textCutlery = '';

      if (dataItems[i].json_data && dataItems[i].json_data.length) {
        textCutlery = makeTrItemCutlery(dataItems[i].json_data, i);
      }

      text += '<div class="card-body table-bordered">'+
        '<div class="form-group row">'+
          '<label id="machineName'+i+'" class="col-sm-2 col-md-1 col-form-label">機番'+(dataItems[i].machine_number ?? '')+' <span class="btn badge bg-orange">必須</span></label>'+
          '<div class="col-sm-7">'+
            '<table id="table-machine-cutlery'+i+'" class="table table-bordered" style="border: none;">'+
              '<thead>'+
                '<tr class="bg-light">'+
                  '<th class="col-2" style="text-align: center;">刃物</th>'+
                  '<th class="col-7" style="text-align: center;">備考</th>'+
                  '<th class="col-2" style="text-align: center;">使用回数</th>'+
                  '<th class="col-1"key: "value", ></th>'+
                '</tr>'+
              '</thead>'+
              '<tbody>'+
              textCutlery+
              '</tbody>'+
              '<tfoot>'+
                '<tr>'+
                  '<th colspan="4" style="border: none;">'+
                    '<div class="row float-right">'+
                      '<button type="button" onclick="makeDataItemCutlery(this)" data-index="'+i+'" class="btn btn-success bg-add">行を追加</button>'+
                    '</div>'+
                  '</th>'+
                '</tr>'+
              '</tfoot>'+
            '</table>'+
          '</div>'+
        '</div>'+
      '</div>';
      textCutlery = '';
    }

    return text;
  }

  function removeItemMachine(el) {
    var index = $(el).attr('data-index');
    var datas = [];
    for (var i = 0; i < dataItems.length; i++) {
      if (i != index) {
        datas.push(dataItems[i]);
      }
    }
    dataItems = datas;
    var text = makeTrItemMachine();
    $('#table-machines').find('tbody').html(text);
    var textCard = makeCardItemMachine();
    $('#machines-store').html(textCard);
  }

  function makeDataItemCutlery(el) {
    var index = $(el).attr('data-index');
    var dataItemCutlery = dataItems[index].json_data ? dataItems[index].json_data : [];
    dataItemCutlery.push(Object.create(itemCutlery));
    dataItems[index].json_data = dataItemCutlery.slice();
    var text = makeTrItemCutlery(dataItems[index].json_data, index);
    $('#table-machine-cutlery'+`${index}`).find('tbody').html(text);
  }

  function makeTrItemCutlery(dataItemCutlery, index) {
    var text = '';
    for (var i = 0; i < dataItemCutlery.length; i++) {
      text += '<tr>'+
        '<td class="col-2">'+
          '<div class="form-group row">'+
            '<div class="col-sm-12">'+
              '<input type="text" name="cutlery[]" onkeyup="changeDataItemCutlery(this)" data-index="'+index+'" data-ind="'+i+'" value="'+(dataItemCutlery[i].cutlery ?? '')+'" class="form-control" id="inputMachineCutlery'+`${index}${i}`+'">'+
            '</div>'+
          '</div>'+
        '</td>'+
        '<td class="col-7">'+
          '<div class="form-group row">'+
            '<div class="col-sm-12">'+
              '<input type="text" name="remarks[]" onkeyup="changeDataItemCutlery(this)" data-index="'+index+'" data-ind="'+i+'" value="'+(dataItemCutlery[i].remarks ?? '')+'" class="form-control" maxlength="100" id="inputMachineRemarks'+`${index}${i}`+'">'+
            '</div>'+
          '</div>'+
        '</td>'+
        '<td class="col-2">'+
          '<div class="form-group row">'+
            '<div class="col-sm-12">'+
              '<input type="number" name="number_of_uses[]" onchange="changeDataItemCutlery(this)" onkeyup="changeDataItemCutlery(this)" data-index="'+index+'" data-ind="'+i+'" value="'+(dataItemCutlery[i].number_of_uses ?? '')+'" class="form-control text-right" id="inputMachineNumberOfUse'+`${index}${i}`+'">'+
            '</div>'+
          '</div>'+
        '</td>'+
        '<td class="col-1">'+
          '<div class="form-group row">'+
            '<div class="col-sm-1">'+
              '<button type="button" onclick="removeItemCutlery(this)" data-index="'+index+'" data-ind="'+i+'" class="btn btn-danger bg-delete"><i class="fa fa-times"></i></button>'+
            '</div>'+
          '</div>'+
        '</td>'+
      '</tr>';
    }
    return text;
  }

  function changeDataItemMachine(el) {
    var index = $(el).attr('data-index');
    var value = $(el).val();
    var itemName = $(el).attr('id');

    itemName = itemName.replace(index, '');

    if (!dataItems[index]) {
      return;
    }

    if (itemName === 'inputMachineId') {

      dataItems[index].machine_number = value;

      $('#machineName' + `${index}`).html('機番 '+value+'<span class="btn badge bg-orange">必須</span>')

    } else if (itemName === 'inputMachine') {

      dataItems[index].machine_name = value;

    } else if (itemName === 'inputMachineNumber') {

      dataItems[index].number_of_maintenance = value;

    }
  }

  function changeDataItemCutlery(el) {
    var index = $(el).attr('data-index');
    var ind = $(el).attr('data-ind');
    var value = $(el).val();
    var itemName = $(el).attr('id');
    itemName = itemName.replace(index, '');
    itemName = itemName.replace(ind, '');
    if (!dataItems[index]) {
      return;
    }
    var dataItemCutlery = dataItems[index].json_data;
    if (!dataItemCutlery[ind]) {
      return;
    }
    if (itemName === 'inputMachineCutlery') {
      dataItemCutlery[ind].cutlery = value;
    } else if (itemName === 'inputMachineRemarks') {
      dataItemCutlery[ind].remarks = value;
    } else if (itemName === 'inputMachineNumberOfUse') {
      dataItemCutlery[ind].number_of_uses = value;
    }
    dataItems[index].json_data = dataItemCutlery;
  }

  function removeItemCutlery(el) {
    var index = $(el).attr('data-index');
    var dataItemCutlery = dataItems[index].json_data;
    dataItems[index].json_data = [];
    var ind = $(el).attr('data-ind');
    var datas = [];
    for (var i = 0; i < dataItemCutlery.length; i++) {
      if (i != ind) {
        datas.push(dataItemCutlery[i]);
      }
    }
    dataItemCutlery = datas;
    dataItems[index].json_data = dataItemCutlery;
    var text = makeTrItemCutlery(dataItemCutlery, index);
    $('#table-machine-cutlery' + index).find('tbody').html(text);
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