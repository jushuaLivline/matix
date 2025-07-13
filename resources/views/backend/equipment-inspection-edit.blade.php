@php
    $linkList = route('equipment-inspection.list');
    $link = route('equipment-inspection.store');
    $linkBasicList = route('inspection-item.list');
@endphp

@extends('backend.layouts.app')
@include('partials._dropzone')
@section('title', '設備点検票 確認・編集・承認')
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
                <div class="card-header">
                    <h3 class="card-title">
                        <blockquote class="bd-card-title m-0" style="padding: 0px; padding-left: 0.5rem;">設備点検票 登録
                        </blockquote>
                    </h3>
                    <div class="card-tools">
                        <div class="input-group input-group-md">
                            <div class="input-group-append">
                                <button data-href="{{ $link }}" type="button" class="btn btn-primary ml-3"
                                    onclick="showModalCreate(this)">
                                    <i class="fa fa-list"></i> 新規登録
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.card-header -->
                <div id="res-message-equipment"></div>
                <div data-href="{{ $link }}" id="app-equipment-inspection" data-file-id="{{ $file_id }}" data-current-url="{{ url()->current()  }}" data-user-id="{{ Auth::user()->id }}"></div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
    </div>
    <!-- /.row -->
    <div class="modal fade" id="modal-xl-create" data-backdrop="static" data-keyboard="false"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl w_percent_30 modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-body" id="modal-body-create">
                    <div class="card card-default">
                        <div class="card-header bg-gray-1">
                            <div class="card-tools">
                                <button type="button" data-dismiss="modal" aria-label="Close"
                                    class="modalCloseBtn js-modal-close">x</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="res-message-process"></div>
                            <form id="quickForm" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                                <div class="row" id="basic-set" style="display: none;">
                                    <div class="col-sm-12">
                                        <div class="form-group mb-3">
                                            <label class="form-label dotted indented" for="exampleProcess1">工程</label>
                                            <input name="process_id" type="number" min="1" max="99"
                                                class="form-control text-right col-sm-3" id="exampleProcess1">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label dotted indented"
                                                for="exampleBasicSet1">点検項目基本セット選択</label>
                                            <select name="_basic_set_id" id="exampleBasicSet1" class="form-control select2"
                                                style="width: 100%;">
                                                @if (isset($inspectionItem))
                                                    <option value="" style="background-color: gray;">点検項目基本セット
                                                    </option>
                                                    @foreach ($inspectionItem as $key => $value)
                                                        <option value="{{ $value->id }}">
                                                            {{ $value->inspection_item_set }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                        <div class="form-group mb-0" style="margin-top: 1rem;">
                                            <button onclick="btnProcess(this)" type="button"
                                                class="col-sm-2 btn btn-primary bg-search">選択</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" id="department-set" style="display: none;">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label class="form-label dotted indented" for="exampleDepartment1">部署選択</label>
                                            <select id="exampleDepartment1" class="form-control select2"
                                                style="width: 100%;">
                                                @if (isset($departments))
                                                    <option style="background-color: gray;" value="">部署</option>
                                                    @foreach ($departments as $key => $value)
                                                        <option data-code="{{ $value->code }}"
                                                            data-name="{{ $value->name }}" value="{{ $value->id }}">
                                                            {{ '[' . $value->code . '] ' . $value->name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                        <div class="form-group mb-0" style="margin-top: 1rem;">
                                            <button onclick="btnDepartment(this)" type="button"
                                                class="col-sm-2 btn btn-primary bg-search">選択</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" id="line-set" style="display: none;">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label class="form-label dotted indented"
                                                for="exampleLineCode1">ライン名（ラインコード）</label>
                                            <select id="exampleLineCode1" class="form-control select2"
                                                style="width: 100%;">
                                                @if (isset($lines))
                                                    <option style="background-color: gray;" value="">ライン名</option>
                                                    @foreach ($lines as $key => $value)
                                                        <option data-code="{{ $value->line_code }}"
                                                            data-name="{{ $value->line_name }}"
                                                            value="{{ $value->id }}">
                                                            {{ $value->line_name . ' (' . $value->line_code . ')' }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                        <div class="form-group mb-0" style="margin-top: 1rem;">
                                            <button onclick="btnLineCode(this)" type="button"
                                                class="col-sm-2 btn btn-primary bg-search">選択</button>
                                        </div>
                                    </div>
                                </div>

                                {{-- confirmed by --}}
                                <div class="row" id="confirmed-by" style="display: none;">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label class="form-label dotted indented"
                                                for="confirmedBy">ライン名（ラインコード）</label>
                                            <select id="confirmedBy" class="form-control select2" style="width: 100%;">
                                                @if (isset($employee))
                                                    <option style="background-color: gray;" value="">ライン名</option>
                                                    @foreach ($employee as $key => $value)
                                                        <option data-code="{{ $value->employee_code }}"
                                                            data-name="{{ $value->employee_name }}"
                                                            value="{{ $value->id }}">
                                                            {{ $value->employee_name . ' (' . $value->employee_code . ')' }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                        <div class="form-group mb-0" style="margin-top: 1rem;">
                                            <button onclick="btnConfirmedByCode(this)" type="button"
                                                class="col-sm-2 btn btn-primary bg-search">選択</button>
                                        </div>
                                    </div>
                                </div>

                                {{-- approved by --}}
                                <div class="row" id="approved-by" style="display: none;">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label class="form-label dotted indented"
                                                for="approvedBy">ライン名（ラインコード）</label>
                                            <select id="approvedBy" class="form-control select2" style="width: 100%;">
                                                @if (isset($employee))
                                                    <option style="background-color: gray;" value="">ライン名</option>
                                                    @foreach ($employee as $key => $value)
                                                        <option data-code="{{ $value->employee_code }}"
                                                            data-name="{{ $value->employee_name }}"
                                                            value="{{ $value->id }}">
                                                            {{ $value->employee_name . ' (' . $value->employee_code . ')' }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                        <div class="form-group mb-0" style="margin-top: 1rem;">
                                            <button onclick="btnApprovedByCode(this)" type="button"
                                                class="col-sm-2 btn btn-primary bg-search">選択</button>
                                        </div>
                                    </div>
                                </div>

                                {{-- completed by --}}
                                <div class="row" id="completed-by" style="display: none;">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label class="form-label dotted indented"
                                                for="completedBy">ライン名（ラインコード）</label>
                                            <select id="completedBy" class="form-control select2" style="width: 100%;">
                                                @if (isset($employee))
                                                    <option style="background-color: gray;" value="">ライン名</option>
                                                    @foreach ($employee as $key => $value)
                                                        <option data-code="{{ $value->employee_code }}"
                                                            data-name="{{ $value->employee_name }}"
                                                            value="{{ $value->id }}">
                                                            {{ $value->employee_name . ' (' . $value->employee_code . ')' }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                        <div class="form-group mb-0" style="margin-top: 1rem;">
                                            <button onclick="btnCompletedByCode(this)" type="button"
                                                class="col-sm-2 btn btn-primary bg-search">選択</button>
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
    <div class="modal fade" id="modal-xl-upload" data-backdrop="static" data-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl w_percent_30 modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-body" id="modal-body-upload">
                    <div class="card card-default">
                        <div class="card-header bg-gray-1">
                            <h3 class="card-title" style="line-height: 1.8;">
                                ラインレイアウト画像
                            </h3>
                            <div class="card-tools">
                                <button type="button" data-dismiss="modal" aria-label="Close"
                                    class="modalCloseBtn js-modal-close">x</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="res-message"></div>
                            <div class="col-12">
                                <div class="form-group row">
                                    <label for="exampleInputFile"
                                        class="col-sm-3 col-form-label btn btn-default bg-tr-gray">参照...</label>
                                    <div class="col-sm-9">
                                        <input onchange="uploadFile(this)" ref="file"
                                            style="visibility:hidden; padding: 0px; height: 0px;" accept="image/*"
                                            class="form-control" name="logo" type="file" id="exampleInputFile">
                                        <input id="exampleInputFileName" style="background-color: transparent;"
                                            type="text" class="form-control" disabled>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button onclick="preview(this)" type="button"
                                class="col-sm-3 btn btn-primary bg-search">選択</button>
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
    @vite('resources/js/tablet/equipment-inspection-edit.js')
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
            process_id: {
                required: true,
                digits: true,
                min: 1,
                max: 99
            },
            _basic_set_id: {
                required: true
            },
        };
        const makeMessage = (status, message) => {
            var text = '<div class="alert alert-' + getElStatus(status) + ' alert-dismissible">' +
                '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' +
                // '<h5><i class="icon fas fa-check"></i> Alert!</h5>'+
                getMessage(message) +
                '</div>';
            return text;
        }
        window.makeMessage = makeMessage;

        $(function() {
            //Initialize Select2 Elements
            $('.select2').select2();
            $.validator.addMethod("validDateYear", function(value, element) {
                return this.optional(element) || moment(value, "YYYY").isValid();
            }, getMessage("Please enter a valid date in the format YYYY"));
            $.validator.addMethod("validDateMonth", function(value, element) {
                return this.optional(element) || moment(value, "MM").isValid();
            }, getMessage("Please enter a valid date in the format MM"));
            setInputFilter(document.getElementById("datemaskYear"), function(value) {
                return /^\d*$/.test(value);
            }, '');
            setInputFilter(document.getElementById("datemaskMonth"), function(value) {
                return /^\d*$/.test(value);
            }, '');
            initialDataTableFixed();
            initialDataTable();
        });

        function initialDataTableFixed() {
            var _th = $('#head-equipment-inspection-21').find('tr>th');
            if (_th) {
                var _h = _th.height();
                // console.log(_h);
                var _th2 = $('#head-equipment-inspection-22').find('tr:first');
                _th2.children('th').each(function() {
                    $(this).css('height', `${_h}px`);
                });
            }
            // var table = $('#table-equipment-inspection-2').DataTable( {
            //     // scrollY:        '500px',
            //     scrollX:        true,
            //     scrollCollapse: true,
            //     paging:         false,
            //     ordering: false,
            //     info:     false,
            //     searching: false,
            //     fixedColumns:   {
            //         left: 6,
            //         // right: 1
            //     }
            // } );
        }

        function initialDataTable() {
            var _token = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                type: 'POST',
                url: linkList,
                data: {
                    method: 'SEARCH',
                    id: equipment_inspection_id,
                    _token
                },
                beforeSend: function() {},
                success: function(data) {
                    if (data.status == 'error') {
                        $('#res-message-equipment').html(makeMessage(data.status, data.message));
                        setTimeout(function() {
                            $('#res-message-equipment').html('')
                        }, 5000);
                        return;
                    } else {
                        window.app_equipment_inspection.initialDataTable(data.data);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    // window.location.reload(true);
                }
            });
        }

        function btnLineCode(el) {
            console.log('sdfsd')
            var input = $('#exampleLineCode1').find(":selected");
            var id = input.val();
            var code = input.attr('data-code');
            var name = input.attr('data-name');
            var data = {
                id,
                code,
                name
            };
            $('#lineID').removeClass('is-invalid');
            $('#lineID-error').remove();
            window.app_equipment_inspection.btnLineCode(data, 'lines');
        }

        function btnDepartment(el) {
            var input = $('#exampleDepartment1').find(":selected");
            var id = input.val();
            var code = input.attr('data-code');
            var name = input.attr('data-name');
            var data = {
                id,
                code,
                name
            };
            $('#departmentID').removeClass('is-invalid');
            $('#departmentID-error').remove();
            window.app_equipment_inspection.btnLineCode(data, 'department');
        }

        function btnConfirmedByCode(el) {
            var input = $('#confirmedBy').find(":selected");
            var id = input.val();
            var code = input.attr('data-code');
            var name = input.attr('data-name');
            var data = {
                id,
                code,
                name
            };

            $('#confBy').removeClass('is-invalid');
            window.app_equipment_inspection.btnEmployeeCode(data, 'confirmed');
        }

        function btnApprovedByCode(el) {
            var input = $('#approvedBy').find(":selected");
            var id = input.val();
            var code = input.attr('data-code');
            var name = input.attr('data-name');
            var data = {
                id,
                code,
                name
            };

            $('#appBy').removeClass('is-invalid');
            window.app_equipment_inspection.btnEmployeeCode(data, 'approved');
        }

        function btnCompletedByCode(el) {
            var input = $('#completedBy').find(":selected");
            var id = input.val();
            var code = input.attr('data-code');
            var name = input.attr('data-name');
            var data = {
                id,
                code,
                name
            };

            $('#comBy').removeClass('is-invalid');
            window.app_equipment_inspection.btnEmployeeCode(data, 'completed');
        }

        // function btnCompletedBy(el) {
        //   var input = $('#exampleDepartment1').find(":selected");
        //   var id = input.val();
        //   var code = input.attr('data-code');
        //   var name = input.attr('data-name');
        //   var data = {id, code, name};
        //   $('#departmentID').removeClass('is-invalid');
        //   $('#departmentID-error').remove();
        //   window.app_equipment_inspection.btnLineCode(data, 'department');
        // }



        function btnProcess(el) {
            if (validator) {
                validator.destroy();
            }
            $('#res-message-process').html('');
            validator = $('#quickForm').validate({
                rules,
                messages,
                errorElement: 'span',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.form-group').append(error);
                },
                highlight: function(element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                }
            });
            if (!validator.form()) {
                return;
            }
            var input = $('#exampleBasicSet1').find(":selected");
            var id = input.val();
            var processId = $('#exampleProcess1').val();
            var datas = {
                id,
                processId,
                dataItems: null
            };
            var _token = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                type: 'POST',
                url: linkBasicList,
                data: {
                    method: 'SEARCH',
                    id,
                    _token
                },
                beforeSend: function() {},
                success: function(data) {
                    if (data.status == 'error') {
                        $('#res-message-process').html(makeMessage(data.status, data.message));
                        setTimeout(function() {
                            $('#res-message').html('')
                        }, 5000);
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
                error: function(jqXHR, textStatus, errorThrown) {
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
            _rules['inspection_item[]'] = {
                required: true,
            };
            _messages['inspection_item[]'] = {
                required: getMessage('required'),
            };
            _rules['inspection_point[]'] = {
                required: true,
            };
            _messages['inspection_point[]'] = {
                required: getMessage('required'),
            };
            _rules['inspection_period[]'] = {
                required: true,
            };
            _messages['inspection_period[]'] = {
                required: getMessage('required'),
            };
            _rules['typeof_inspector[]'] = {
                required: true,
            };
            _messages['typeof_inspector[]'] = {
                required: getMessage('required'),
            };
            return {
                _rules,
                _messages
            };
        }

        function submitForm(event, el) {
            if (validatorCreate) {
                validatorCreate.destroy();
            }
            var {
                _rules,
                _messages
            } = getRules();
            validatorCreate = $('#createForm').validate({
                rules: _rules,
                messages: _messages,
                errorElement: 'span',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.form-group').append(error);
                },
                highlight: function(element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                }
            });
            if (!validatorCreate.form()) {
                return;
            }
            return window.app_equipment_inspection.submitForm(event);
        }

        function btnUpdateCreater(event, el) {
            var _type = $(el).attr('data-type');
        }

        const uploadFile = (el) => {
            $('#exampleInputFileName').val('');
            const fileInput = document.getElementById('exampleInputFile');
            const file = fileInput.files[0];
            if (!(typeof(file) !== "undefined" && file !== null)) {
                $('#exampleInputFileName').val('');
                return;
            }
            const filename = file.name;
            $('#exampleInputFileName').val(filename);
        };

        const preview = (el) => {
            const fileInput = document.getElementById('exampleInputFile');
            const file = fileInput.files[0];
            if (!(typeof(file) !== "undefined" && file !== null)) {
                document.getElementById('exampleInputFile').value = null;
                return;
            }
            const filename = file.name;
            const fileSize = file.size / 1024 / 1024; // in MiB
            const limit = 5; // MiB
            const imageType = /image.*/;
            if (!file.type.match(imageType)) {
                document.getElementById('exampleInputFile').value = null;
                document.getElementById('base64Image').value = null;
                $('#examplePreview').attr('src', '#');
                $('#examplePreview1').hide();
                $('#res-message').html(makeMessage('warning',
                    'Image only allows file types of PNG, JPG, JPEG and BMP.'));
                setTimeout(function() {
                    $('#res-message').html('')
                }, 5000);
                return;
            }
            var extension = filename.substring(filename.lastIndexOf('.') + 1).toLowerCase();
            if (!(extension == "gif" || extension == "png" || extension == "bmp" ||
                    extension == "jpeg" || extension == "jpg")) {
                document.getElementById('exampleInputFile').value = null;
                document.getElementById('base64Image').value = null;
                $('#examplePreview').attr('src', '#');
                $('#examplePreview1').hide();
                $('#res-message').html(makeMessage('warning',
                    'Image only allows file types of PNG, JPG, JPEG and BMP.'));
                setTimeout(function() {
                    $('#res-message').html('')
                }, 5000);
                return;
            }
            if (fileSize > limit) {
                document.getElementById('exampleInputFile').value = null;
                document.getElementById('base64Image').value = null;
                $('#examplePreview').attr('src', '#');
                $('#examplePreview1').hide();
                $('#res-message').html(makeMessage('warning', 'File size exceeds ' + limit + ' MiB'));
                setTimeout(function() {
                    $('#res-message').html('')
                }, 5000);
                return;
            }
            let reader = new FileReader();
            reader.onload = function(event) {
                // console.log(event.target.result);
                // $('#examplePreview').attr('src', event.target.result);
                // $('#examplePreview1').show();
                $('#base64Image').removeClass('is-invalid');
                $('#base64Image-error').remove();
                const data = {
                    img: event.target.result
                };
                document.getElementById('base64Image').value = file.name;
                window.app_equipment_inspection.btnUploadFile(data);
            }
            reader.readAsDataURL(file);
        };

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
                mes = "「データは正常に登録されました」"; //'System error, please try again later!';
            }
            if (message === 'Updated equipment inspection failed') {
                mes = "更新された機器検査に失敗しました"; //'System error, please try again later!';
            }
            if (message === 'required') {
                mes = "「この項目は必須です」"; //'This field is required.';
            }
            if (message === 'digits') {
                mes = 'Please enter only digits.';
            }
            if (message === 'min1') {
                mes = "「1桁から入力してください」"; //'Please enter a value greater than or equal to 1.';
            }
            if (message === 'max99') {
                mes = "99桁以内で入力してください"; //'Please enter a value less than or equal to 99.';
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
            ["input", "keydown", "keyup", "mousedown", "mouseup", "select", "contextmenu", "drop", "focusout"].forEach(
                function(event) {
                    textbox.addEventListener(event, function(e) {
                        if (inputFilter(this.value)) {
                            // Accepted value.
                            if (["keydown", "mousedown", "focusout"].indexOf(e.type) >= 0) {
                                this.classList.remove("input-error");
                                this.setCustomValidity("");
                            }
                            this.oldValue = this.value;
                            this.oldSelectionStart = this.selectionStart;
                            this.oldSelectionEnd = this.selectionEnd;
                        } else if (this.hasOwnProperty("oldValue")) {
                            // Rejected value: restore the previous one.
                            this.classList.add("input-error");
                            this.setCustomValidity(errMsg);
                            this.reportValidity();
                            this.value = this.oldValue;
                            this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
                        } else {
                            // Rejected value: nothing to restore.
                            this.value = "";
                        }
                    });
                });
        }
    </script>
@endsection
