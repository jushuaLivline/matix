@extends('layouts.app')

@push('styles')
    @vite('resources/css/index.css')
    @vite('resources/css/materials/supply_fraction_instruction.css')
    @vite('resources/css/modals/index.css')
    @vite('resources/css/search-modal.css')
    <style>
        .input-error {
            border: 2px solid red;
        }
    </style>
@endpush

@section('title', '作業実績入力')

@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="pageHeaderBox rounded">
                作業実績入力
            </div>

            <div class="section">
                <h1 class="form-label bar indented">作業実績入力</h1>
                <div class="box mb-3">
                    <div class="mb-2">
                        <div class="mb-2 d-flex">
                            <div class="mr-3">
                                <label class="form-label dotted indented">作業日</label>
                                <span class="btn-orange badge">必須</span>
                                <div class="d-flex">
                                    <input type="text" name="work_day" style="text-align: center; width: 120px;" 
                                            id="work_day"
                                            data-format="YYYYMMDD"
                                            minlength="8"
                                            maxlength="8"
                                            pattern="\d*" 
                                            value="{{ isset($latestItem['work_day']) ? $latestItem['work_day'] : now()->format('Ymd') }}"
                                            oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                    <button type="button" class="btnSubmitCustom buttonPickerJS ml-1" 
                                            data-target="work_day"
                                            data-format="YYYYMMDD">
                                        <img src="{{ asset('images/icons/iconsvg_calendar_w.svg') }}" alt="iconsvg_calendar_w.svg">
                                    </button>
                                </div>
                            </div>
    
                            <div class="ml-4">
                                <label class="form-label dotted indented">作業者 </label>
                                <div class="d-flex">
                                    <input type="text" id="employee_code" name="employee_code"
                                        value="{{ isset($latestItem['employee_code']) ? $latestItem['employee_code'] : '' }}" class="mr-half">
                                    <input type="text"
                                        id="employee_name"
                                        name="employee_name"
                                        value="{{ isset($latestItem['employee_name']) ? $latestItem['employee_name'] : '' }}"
                                        class="middle-name mr-half"
                                        style="width: 170px"
                                        readonly>
                                    <button type="button" class="btnSubmitCustom js-modal-open"
                                            data-target="searchEmployeeModal">
                                        <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                alt="magnifying_glass.svg">
                                    </button>
                                </div>
                            </div>
                        </div>
                        <br/>
                        <div class="mb-2 d-flex">
                            <div class="mr-3">
                                <label class="form-label dotted indented">勤務区分</label>
                                <div>
                                    <label class='radioBasic mr-2'>
                                        <input type="radio" name="category" class="" value="1" {{ (request()->input('category', '') == '') ? 'checked' : '' }}> 
                                        <span>
                                            通常
                                        </span>
                                    </label>
                                    <label class='radioBasic mr-2'>
                                        <input type="radio" name="category" class="" value="2" {{ (request()->input('category', '') == '2') ? 'checked' : '' }}> 
                                        <span>
                                            半休
                                        </span>
                                    </label>
                                    <label class='radioBasic mr-2'>
                                        <input type="radio" name="category" class="" value="3" {{ (request()->input('category', '') == '1') ? 'checked' : '' }}> 
                                        <span>
                                            休出
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <br/>
                        <div class="d-flex">
                            <div class="mr-3">
                                <button class="btn btn-gray" id="prevDayButton"> < 前日 </button>
                                <button class="btn btn-gray" id="nextDayButton"> 翌日 > </button>
                            </div>
                        </div>
                        <div class="mt-4">
                            <table class="table table-bordered table-striped text-center">
                                <thead>
                                <tr>
                                    <th>分類</th>
                                    <th>コード</th>
                                    <th>機番</th>
                                    <th>作業内容</th>
                                    <th>
                                        作業時間 
                                        <span class="btn-orange badge">必須</span>
                                    </th>
                                    <th>備考</th>
                                    <th style="width:200px;">操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach ($items as $item)
                                        <tr data-instruction-id="{{ $item['id'] }}">
                                            <td>
                                                <select name="select" style="width: 100%" disabled>
                                                    <option value="machine" @if($item['classification'] == "machine") selected @endif>プロジェクト（機番）</option>
                                                    <option value="common" @if($item['classification'] == "common") selected @endif>プロジェクト（共通）</option>
                                                    <option value="line" @if($item['classification'] == "line") selected @endif>ライン</option>
                                                    <option value="other" @if($item['classification'] == "other") selected @endif>その他</option>
                                                </select>                                                                                       
                                            </td>
                                            <td>
                                                <div class="center" id="classification_input_1">
                                                    <input type="text" id="project_code_1" name="project_code_1"
                                                            value="{{ $item['project_code'] }}" 
                                                            class="mr-half" disabled>
                                                    <input type="text"
                                                            id="project_name_1"
                                                            name="project_name_1"
                                                            value="{{ $item['project_name'] }}"
                                                            class="middle-name mr-half"
                                                            style="width: 170px"
                                                            readonly>
                                                    <button type="button" class="btnSubmitCustom js-modal-open" id="buttonModal"
                                                            data-target="searchProjectModal">
                                                        <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                                alt="magnifying_glass.svg">
                                                    </button>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="center" id="machine_input_1">
                                                    <input type="text" id="machine_code_1" name="machine_code_1"
                                                            value="{{ $item['machine_code'] }}" 
                                                            class="mr-half" disabled>
                                                    <input type="text"
                                                            id="machine_name_1"
                                                            name="machine_name_1"
                                                            value="{{ $item['machine_name'] }}"
                                                            class="middle-name mr-half"
                                                            style="width: 170px"
                                                            readonly>
                                                    <button type="button" class="btnSubmitCustom js-modal-open"
                                                            data-target="searchMachineModal">
                                                        <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                                alt="magnifying_glass.svg">
                                                    </button>
                                                </div>
                                            </td>
                                            <td>
                                                <select name="select" style="width: 100%" disabled>
                                                    @foreach ($options as $option)
                                                        <option value="{{ $option->code }}" @if($option->code == $item['work_detail']) selected @endif>
                                                            {{ $option->abbreviation }}
                                                        </option>
                                                    @endforeach
                                                </select>                                                
                                            </td>
                                            <td>
                                                <input type="text" value="{{ $item['work_hour'] }}" name="work_hour_1" disabled>
                                            </td>
                                            <td>
                                                <input type="text" value="{{ $item['remark_work'] }}" name="remark_work_1" disabled>
                                            </td>
                                            <td>
                                                <div class="center" id="EditDelete">
                                                    <button onclick="enableInputs(this)" class="btn btn-block btn-blue mr-1" id="edit">編集</button>
                                                    <button onclick="confirmDelete(this)" class="btn btn-block btn-orange" style="margin-left: 2px" id="delete">削除</button>
                                                </div>
                                                
                                                <div class="center" id="UdpateUndo" style="display: none;">
                                                    <button onclick="updateData(this)" class="btn btn-block btn-green mr-1" id="update">更新</button>
                                                    <button onclick="cancelEdit(this)" class="btn btn-block btn-gray" style="margin-left: 1px" id="undo">取消</button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td>
                                            <select name="select" style="width: 100%" name="classification" id="classification">
                                                <option value="machine">プロジェクト（機番）</option>
                                                <option value="common">プロジェクト（共通）</option>
                                                <option value="line">ライン</option>
                                                <option value="other">その他</option>
                                            </select>                                         
                                        </td>
                                        <td>
                                            <div class="center" id="classification_input">
                                                <input type="text" id="project_code" name="project_code"
                                                        value="" 
                                                        class="mr-half">
                                                <input type="text"
                                                        id="project_name"
                                                        name="project_name"
                                                        value=""
                                                        class="middle-name mr-half"
                                                        style="width: 170px"
                                                        readonly>
                                                <button type="button" class="btnSubmitCustom js-modal-open" id="buttonModal"
                                                        data-target="searchProjectModal">
                                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                            alt="magnifying_glass.svg">
                                                </button>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="center" id="machine_input">
                                                <input type="text" id="machine_code" name="machine_code"
                                                        value="" 
                                                        class="mr-half">
                                                <input type="text"
                                                        id="machine_name"
                                                        name="machine_name"
                                                        value=""
                                                        class="middle-name mr-half"
                                                        style="width: 170px"
                                                        readonly>
                                                <button type="button" class="btnSubmitCustom js-modal-open"
                                                        data-target="searchMachineModal">
                                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                            alt="magnifying_glass.svg">
                                                </button>
                                            </div>
                                        </td>
                                        <td>
                                            <select name="work_detail" style="width: 100%" id="work_detail">
                                            </select>  
                                        </td>
                                        <td>
                                            <input type="text" value="" name="work_hour">
                                        </td>
                                        <td>
                                            <input type="text" value="" name="remark_work">
                                        </td>
                                        <td>
                                            <div class="center">
                                                <button onclick="storeData(this)" class="btn btn-block btn-green mr-1">追加</button>
                                                <button onclick="clearData(this)" class="btn btn-block btn-gray" style="margin-left: 1px">クリア</button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <sub>作業時間合計：0.00（H）残業時間合計：0.00（H）</sub>
                        </div>
                        <br/>
                        <div class="d-flex">
                            <div class="mr-3">
                                <label class="form-label dotted indented">備考</label>
                                <div class="d-flex">
                                    <input type="text" name="remark" value="{{ isset($latestItem['remark']) ? $latestItem['remark'] : '' }}" class="mr-half" style="width: 400px;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="space-between">
                <div>
                    <p class="text-red" id="warningInputs" style="display:none;">登録に必要ないくつかの情報が入力されていません！</p>
                    <p id="successInputs" style="display:none; color:#0d9c38;">「データは正常に登録されました」</p>
                </div>
                <div>
                    <a href="#" class="btn btn-blue" style="width: 250px"> クリア </a>
                    <button  onclick="bulkSavingData(this)"  class="btn btn-green" style="width: 15rem"> 登録 </button>
                </div>
            </div>
        </div>
    </div>
    @include('partials.modals.masters._search', [
        'modalId' => 'searchEmployeeModal',
        'searchLabel' => '作業者',
        'resultValueElementId' => 'employee_code',
        'resultNameElementId' => 'employee_name',
        'model' => 'Employee',
    ])
    @include('partials.modals.masters._search', [
        'modalId' => 'searchMachineModal',
        'searchLabel' => '機番',
        'resultValueElementId' => 'machine_code',
        'resultNameElementId' => 'machine_name',
        'model' => 'MachineNumber',
    ])
    @include('partials.modals.masters._search', [
        'modalId' => 'searchProjectModal',
        'searchLabel' => 'コード',
        'resultValueElementId' => 'project_code',
        'resultNameElementId' => 'project_name',
        'model' => 'Project',
    ])
    @include('partials.modals.masters._search', [
        'modalId' => 'searchLineModal',
        'searchLabel' => 'ライン',
        'resultValueElementId' => 'line_code',
        'resultNameElementId' => 'line_name',
        'model' => 'Line',
    ])
@endsection
@push('scripts')
<script>
    var optionsData = @json($options);
    var workStoreRoute = @json(route('facility.work.input.store'));
    var workStoreBulkRoute = @json(route('facility.work.bulk.store'));
    var csrfToken = '{{ csrf_token() }}';

    function clearData(button) {
        var tr = button.closest('tr');
        var inputs = tr.getElementsByTagName('input');

        for (var i = 0; i < inputs.length; i++) {
            inputs[i].value = '';
        }
    }
    function storeData(button) {
        var workDay = document.getElementById('work_day').value;
        var employeeCode = document.getElementById('employee_code').value;
        var employeeName = document.getElementById('employee_name').value;
        var category = document.querySelector('input[name="category"]:checked').value;
        var classification = document.getElementById('classification').value;
        var projectCode = document.getElementById('project_code').value;
        var projectName = document.getElementById('project_name').value;
        var machineCode = document.getElementById('machine_code').value;
        var machineName = document.getElementById('machine_name').value;
        var workDetail = document.getElementById('work_detail').value;
        var workHour = document.getElementsByName('work_hour')[0].value;
        var remarkWork = document.getElementsByName('remark_work')[0].value;
        var remark = document.getElementsByName('remark')[0].value;

        var data = {
            work_day: workDay,
            employee_code: employeeCode,
            employee_name: employeeName,
            category: category,
            classification: classification,
            project_code: projectCode,
            project_name: projectName,
            machine_code: machineCode,
            machine_name: machineName,
            work_detail: workDetail,
            work_hour: workHour,
            remark_work: remarkWork,
            remark: remark
        };

        fetch(workStoreRoute, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            console.log(data);
            if (data.success) {
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }

    function bulkSavingData(button) {
        var sessionData = {!! json_encode(session('workItems', [])) !!};

        fetch(workStoreBulkRoute, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ workItems: sessionData })
        })
        .then(response => response.json())
        .then(data => {
            console.log(data);

            if (data.success) {
                alert('Data saved successfully!');

                var userConfirmed = confirm('Do you want to reload the page?');

                if (userConfirmed) {
                    location.reload();
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
</script>

@vite(['resources/js/facility/work.js'])
@endpush


