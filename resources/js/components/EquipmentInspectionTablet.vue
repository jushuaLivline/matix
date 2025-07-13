<template>
  <div class="container-equipment-inspection">
    <form class="form-horizontal" id="createForm" method="post" accept-charset="utf-8" enctype="multipart/form-data">
      <div class="card-body table-responsive" style="padding-bottom: 0px;">
        <div class="row">
          <div class="col-12">
            <table class="table table-bordered">
              <tbody>
                <tr>
                  <td class="text-center" style="width:20%; vertical-align: middle;">設備点検票（私のPM責任）</td>
                  <td style="width:30%; vertical-align: middle;">
                    <div class="row">
                      <div class="col-6 col-sm-6">
                        <div class="input-group form-group m-0">
                          <input id="datemaskYear" name="year" v-model="year" type="text" class="form-control bg-td-white" style="color: black;" data-inputmask-alias="datetime" data-inputmask-inputformat="yyyy" data-mask="" placeholder="YYYY" maxlength="4" :disabled="true">
                          <div class="input-group-append"><span class="input-group-text">年</span></div>
                        </div>
                      </div>
                      <div class="col-6 col-sm-6">
                        <div class="input-group form-group m-0">
                          <input id="datemaskMonth" name="month" v-model="month" type="text" class="form-control bg-td-white" style="color: black;" data-inputmask-alias="datetime" data-inputmask-inputformat="mm" data-mask="" placeholder="MM" maxlength="2" :disabled="true">
                          <div class="input-group-append"><span class="input-group-text">月</span></div>
                        </div>
                      </div>
                    </div>
                  </td>
                  <td class="text-center bg-light" style="width: 10%; vertical-align: middle;">実施部署</td>
                  <td style="width: 20%; vertical-align: middle;" class="bg-td-white text-center" @click="showModalDepartment($event)">
                    <div class="form-group m-0">
                      <input id="departmentID" type="text" name="department_id" :value="department_id" class="form-control bg-td-white">
                      <p v-if="department_id" style="margin-bottom: 0px;">{{ `[${department_name}] ${department_code}` }}</p>
                    </div>
                  </td>
                  <td style="width: 20%; vertical-align: middle;" class="text-center bg-light">
                    <p style="margin-bottom: 0px;">項目設定部署</p>
                    <p style="margin-bottom: 0px;">生産統括部　生産技術課</p>
                  </td>
                </tr>
                <tr>
                  <td style="vertical-align: middle;" @click="showModalUpload($event)" id="image-preview" class="bg-td-white text-center" colspan="2" rowspan="2">
                    <div class="form-group m-0">
                      <input id="base64Image" type="text" name="base64_image" class="form-control bg-td-white">
                      <div v-if="base64_image" id="examplePreview1" style="height: 186px;">
                        <img id="examplePreview" style="height: 100%;" class="thumbnail-img" :src="base64_image" alt="">
                      </div>
                    </div>
                  </td>
                  <td style="vertical-align: middle;" class="bg-light text-center">ライン名<br>(ﾗｲﾝｺｰﾄﾞ)</td>
                  <td style="width: 20%; vertical-align: middle;" class="bg-td-white text-center" @click="showModalLine($event)">
                    <div class="form-group m-0">
                      <input id="lineID" type="text" name="line_id" :value="line_id" class="form-control bg-td-white">
                      <p v-if="line_id" style="margin-bottom: 0px;">{{ `${line_name} (${line_code})` }}</p>
                    </div>
                  </td>
                  <td id="creater" class="bg-td-white" style="padding: 0px; vertical-align: middle;">
                    <div class="input-group">
                      <div class="input-group-prepend text-center">
                        <span class="input-group-text bd-radius-0 bg-light">作　成</span>
                      </div>
                      <input type="text" class="form-control bd-radius-0" style="color: black;" :class="created_name ? 'bg-td-white' : 'bg-td-white'" :value="created_name" :disabled="true">
                    </div>
                    <div class="input-group">
                      <div class="input-group-prepend text-center">
                        <span class="input-group-text bd-radius-0 bg-light">確　認</span>
                      </div>
                      <div class="form-control p-0" @click="btnUpdateCreater($event, 'confirmed_name')" data-type="confirmed_name">
                        <input  type="text" class="form-control bd-radius-0" style="color: black;" :class="confirmed_name ? 'bg-td-white' : 'bg-td-white'" :value="confirmed_name" :disabled="true">
                      </div>
                    </div>
                    <div  class="input-group">
                      <div class="input-group-prepend text-center">
                        <span class="input-group-text bd-radius-0 bg-light">承　認</span>
                      </div>
                      <div class="form-control p-0" @click="btnUpdateCreater($event, 'approved_name')" data-type="approved_name">
                        <input type="text" class="form-control bd-radius-0" style="color: black;" :class="approved_name ? 'bg-td-white' : 'bg-td-white'" :value="approved_name" :disabled="true">
                      </div>
                    </div>
                  </td>
                </tr>
                <tr>
                  <td style="vertical-align: middle;" class="bg-light text-center">周　期</td>
                  <td style="vertical-align: middle;">
                    <p style="margin-bottom: 0px;">1/D ･･ 毎日/午前中</p>
                    <p style="margin-bottom: 0px;">1/W ･･ 毎週/月曜日</p>
                    <p style="margin-bottom: 0px;">1/M ･･ 毎月/第1稼働日</p>
                  </td>
                  <td id="completer" class="bg-td-green" style="padding: 0px; vertical-align: middle;">
                    <div class="text-center bg-light" style="border: 1px solid #ced4da;padding: 0.375rem 0.75rem;">
                      <p style="margin-bottom: 0px;">完　了（承認）</p>
                      <p style="margin-bottom: 0px;">課長または係長</p>
                    </div>
                    <div @click="btnUpdateCreater($event, 'completed_name')" data-type="completed_name" class="input-group">
                      <input type="text" class="form-control text-center bd-radius-0" :class="completed_name ? 'bg-td-white' : 'bg-td-white'" :value="completed_name" :disabled="true">
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="card-body table-responsive" style="padding-top: 0px;">
        <div class="row">
          <div class="col-6" style="padding-right: 0px;">
            <table id="table-equipment-inspection-21" class="table-s0 table table-bordered">
              <thead id="head-equipment-inspection-21">
                <tr class="text-thin text-center">
                  <th class="bg-light" style="width: 10%;"><div>No</div></th>
                  <th class="bg-light" style="width: 10%;"><div>工程</div></th>
                  <th class="bg-light"><div>点　検　項　目</div></th>
                  <th class="bg-light"><div>点検ポイント（基準）</div></th>
                  <th class="bg-light" style="width: 10%;"><div>周期</div></th>
                  <th class="bg-light" style="width: 15%;"><div>点検者</div></th>
                </tr>
              </thead>
              <tbody id="body-equipment-inspection-21">
                <template v-if="items && items.length">
                  <tr class="text-center even" v-for="(item, index) in items">
                    <td class="dtfc-fixed-left bg-td-white">
                      <div class="form-group m-0">
                        {{(index+1)}}
                      </div>
                    </td>
                    <td class="dtfc-fixed-left bg-td-white" :style="styles.style2"><div>{{ item.process_id }}</div></td>
                    <td class="dtfc-fixed-left bg-td-white" :style="styles.style3">
                      <div  class="form-group m-0">
                        <span style="border: none;" name="inspection_item[]" :data-index="index" class="form-control1" :id="`inputInspectionItems${index}`">{{item.inspection_item}}</span>
                      </div>
                    </td>
                    <td class="dtfc-fixed-left bg-td-white" :style="styles.style4">
                      <div class="form-group m-0">
                        <span style="border: none;" name="inspection_point[]" :data-index="index" class="form-control1" :id="`inputInspectionPoint${index}`">{{item.inspection_point}}</span>
                      </div>
                    </td>
                    <td class="dtfc-fixed-left bg-td-white" :style="styles.style5">
                      <div  class="form-group m-0">
                        <select style="padding: 0px; height: unset; text-align: center;" name="inspection_period[]" v-on:change="changeBasicItem($event, 'inspection_period', index)" :data-index="index" class="form-control" :id="`inputPeriod${index}`" :disabled="!item.edit">
                          <option style="display: none;" value=""></option>
                          <option :selected="item.inspection_period === '1/D'" value="1/D">1/D</option>
                          <option :selected="item.inspection_period === '1/W'" value="1/W">1/W</option>
                          <option :selected="item.inspection_period === '1/M'" value="1/M">1/M</option>
                        </select>
                      </div>
                    </td>
                    <td class="dtfc-fixed-left bg-td-white" :style="styles.style6">
                      <div class="form-group m-0">
                        <span style="border: none;" v-if="item.typeof_inspector === '作業者'" name="typeof_inspector[]" :data-index="index" class="form-control1" :id="`inputInspector${index}`">作業者</span>
                        <span style="border: none;" v-if="item.typeof_inspector === 'ﾘﾘｰﾌ'" name="typeof_inspector[]" :data-index="index" class="form-control1" :id="`inputInspector${index}`">ﾘﾘｰﾌ</span>
                      </div>
                    </td>
                  </tr>
                  <tr class="text-center">
                    <td colspan="6" class="dtfc-fixed-left bg-light" :style="styles.style1">
                      <div class="form-group form-control1 m-0">
                        点検実施者
                      </div>
                    </td>
                    <td style="display: none;"></td>
                    <td style="display: none;"></td>
                    <td style="display: none;"></td>
                    <td style="display: none;"></td>
                    <td style="display: none;"></td>
                  </tr>
                  <tr class="text-center">
                    <td colspan="6" class="dtfc-fixed-left bg-light" :style="styles.style1">
                      <div  class="form-group form-control1 m-0">
                        監督者確認欄
                      </div>
                    </td>
                    <td style="display: none;"></td>
                    <td style="display: none;"></td>
                    <td style="display: none;"></td>
                    <td style="display: none;"></td>
                    <td style="display: none;"></td>
                  </tr>
                </template>
              </tbody>
            </table>
          </div>
          <div class="col-6" style="padding-left: 0px; width: 300px;">
            <div class="table-s1">
              <div class="table-s2">
                <table id="table-equipment-inspection-22" class="table-s0 table-s3 table table-bordered">
                  <thead id="head-equipment-inspection-22">
                    <tr rowspan="2" class="text-thin text-center">
                      <th class="bg-light" v-for="i in 31" :class="{ 'bg-td-pink': isWeekend(i) }"><div>{{ i }}</div></th>
                    </tr>
                  </thead>
                  <tbody id="body-equipment-inspection-22">
                    <template v-if="items && items.length">
                      <tr class="text-center even" v-for="(item, index) in items">
                        <td @click="(item.inspection_daily && item.inspection_daily[j-1]) ? showModalInspectionResult($event, j-1, index, 'item') : () => { return; }" :class="(item.inspection_daily && item.inspection_daily[j-1]) ? 'bg-td-green-important' : 'bg-light'" v-for="j in 31">
                          <div>{{ (item.inspection_daily && item.inspection_daily[j-1]) ? getResult(item.inspection_daily[j-1]) : '' }}</div>
                        </td>
                      </tr>
                      <tr class="text-center">
                        <td @click="(inspectorDaily[j-1]) ? btnInspectionConfirmation($event, j-1, '', 'inspector') : () => {return;}" :class="(inspectorDaily[j-1]) ? 'bg-td-green-important' : 'bg-light'" v-for="j in 31">
                          <div>{{(inspectorDaily[j-1]) ? getResultConfirmation(inspectorDaily[j-1]) : '' }}</div>
                        </td>
                      </tr>
                      <tr class="text-center">
                        <td @click="(confirmationDaily[j-1]) ? btnInspectionConfirmation($event, j-1, '', 's_confirmation') : () => {return;}" :class="(confirmationDaily[j-1]) ? 'bg-td-green-important' : 'bg-light'" v-for="j in 31">
                          <div>{{(confirmationDaily[j-1]) ? getResultConfirmation(confirmationDaily[j-1]) : '' }}</div>
                        </td>
                      </tr>
                    </template>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="card-body table-responsive" style="padding-top: 0px;">
        <div class="row">
          <div class="col-9">
            <div class="table-responsive" style="padding-top: 0px;">
              <table id="table-equipment-inspection-3" class="table table-s4 table-bordered" style="width: 100%;">
                <thead>
                  <tr class="text-thin text-center">
                    <th class="bg-light" colspan="6">改定履歴</th>
                  </tr>
                  <tr class="text-thin text-center">
                    <th class="col-2 bg-light">年月日</th>
                    <th class="col-1 bg-light">符号</th>
                    <th class="col-4 bg-light">内容</th>
                    <th class="col-2 bg-light">作成</th>
                    <th class="col-2 bg-light">承認</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-if="histories && histories.length" v-for="(history, index) in histories">
                    <td class="col-2">
                      <div class="form-group row m-0">
                        <input type="text" name="h_daily[]" v-on:keyup="changeDataItemHistory($event, 'daily', index)" :data-index="index" :value="history.daily" class="form-control text-center" :class="history.edit ? 'bg-td-white' : 'bg-td-white bd-none'" :id="`inputDaily${index}`" :disabled="!history.edit" placeholder="YYYY/MM/DD">
                      </div>
                    </td>
                    <td class="col-1">
                      <div class="form-group row m-0">
                        <select name="h_sign[]" v-on:change="changeDataItemHistory($event, 'sign', index)" :data-index="index" class="form-control text-center" :class="history.edit ? 'bg-td-white' : 'bg-td-white bd-none'" :id="`inputSign${index}`" :disabled="!history.edit">
                          <option style="display: none;" value=""></option>
                          <option :selected="history.sign === '+'" value="+">+</option>
                          <option :selected="history.sign === '-'" value="-">-</option>
                        </select>
                      </div>
                    </td>
                    <td class="col-2">
                      <div class="form-group row m-0">
                        <input type="text" name="h_content[]" v-on:keyup="changeDataItemHistory($event, 'content', index)" :data-index="index" :value="history.content" class="form-control" :class="history.edit ? 'bg-td-white' : 'bg-td-white bd-none'" :id="`inputContenty${index}`" :disabled="!history.edit">
                      </div>
                    </td>
                    <td class="col-2">
                      <div class="form-group row m-0">
                        <input style="border: none;" type="text" name="h_creater[]" :data-index="index" class="form-control text-center bd-radius-0" :class="history.edit ? 'bg-td-white' : 'bg-td-white bd-none'" :id="`inputCreater${index}`" :value="history.edit ? '' : history.created_name" :disabled="true">
                      </div>
                    </td>
                    <td class="col-2">
                      <div @click="btnUpdateApproved($event, index)" data-type="h_approved_name" class="input-group">
                        <input type="text" class="h_approved_name form-control text-center bd-radius-0" :class="(history.edit && history.approved_name) ? 'bg-td-white' : 'bg-td-white bd-none'" :value="history.approved_name" :disabled="true">
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <div class="card-footer">
        <div class="row">
          <div class="col-12">
            <button style="display: none;" type="button" class="btn btn-success mr-1" id="modal-btn-submit" onclick="submitForm(event, this)" :disabled="! (created_name && confirmed_name && approved_name && completed_name)">更新する</button>
          </div>
        </div>
      </div>
    </form>
  </div>
</template>

<script>
import { ref } from "vue";
const _item = {
  process_id: null,
  inspection_item: null,
  inspection_point: null,
  inspection_period: null,
  typeof_inspector: null,
  edit: true,
};

const _styles = {
  style1: '',
  style2: '',
  style3: '',
  style4: '',
  style5: '',
  style6: '',
};

const _histories = {
  daily: '',
  sign: '',
  content: '',
  created_by: '',
  created_name: user_name,
  approved_id: '',
  approved_name: '',
  edit: true,
};

export default {
  data() {
    return {
      basicSetID: '',
    }
  },
  setup() {
    const id = ref('');
    const updating = ref(false);
    const created_name = ref('');
    const confirmed_name = ref('');
    const approved_name = ref('');
    const completed_name = ref('');
    const year = ref('');
    const month = ref('');
    const department_id = ref('');
    const department_name = ref('');
    const department_code = ref('');
    const line_code = ref('');
    const line_name = ref('');
    const line_id = ref('');
    const process_id = ref('');
    const basic_set_id = ref('');
    const base64_image = ref('');
    const items = ref([]);
    const histories = ref([]);
    const confirmationDaily = ref([]);
    const inspectorDaily = ref([]);

    const styles = ref(_styles);

    const showModalUpload = (event) => {
      return;
      $('#modal-xl-upload').modal('show');
    };
    const showModalInspectionResult = (event, j, index, type) => {
      $('#inspectionResult-set').show();
      $('#inspectionResult-set').attr('data-index', index);
      $('#inspectionResult-set').attr('data-j', j);
      $('#inspectionResult-set').attr('data-type', type);
      $('#modal-xl-create').modal('show');
    };
    const showModalLine = (event) => {
      return;
      $('#line-set').show();
      $('#department-set').hide();
      $('#basic-set').hide();
      $('#modal-xl-create').modal('show');
    };

    const initialItem = (event) => {
      return;
      // $('#basic-set').show();
      // $('#department-set').hide();
      // $('#line-set').hide();
      // $('#modal-xl-create').modal('show');
    };

    const btnLineCode = (data, type) => {
      return;
      // const {id, code, name} = data;
      // if (type === 'lines') {
      //   if (id) {
      //     line_id.value = id;
      //     line_code.value = code;
      //     line_name.value = name;
      //   } else {
      //     line_id.value = '';
      //     line_code.value = '';
      //     line_name.value = '';
      //   }
      // } else if (type === 'department') {
      //   if (id) {
      //     department_id.value = id;
      //     department_name.value = code;
      //     department_code.value = name;
      //   } else {
      //     department_id.value = '';
      //     department_name.value = '';
      //     department_code.value = '';
      //   }
      // }
      // $('#modal-xl-create').modal('hide');
    };

    const btnUploadFile = (data) => {
      // const {img} = data;
      // if (img) {
      //   base64_image.value = img;
      // } else {
      //   base64_image.value = '';
      // }
      // $('#modal-xl-upload').modal('hide');
    };

    const btnProcess = (data) => {
      // const {id, processId, dataItems} = data;
      // if (id) {
      //   process_id.value = processId;
      //   basic_set_id.value = id;
      //   for (var i = 0; i < dataItems.length; i++) {
      //     dataItems[i]['process_id'] = processId;
      //     dataItems[i]['edit'] = false;
      //   }
      //   items.value = dataItems;
      // } else {
      //   process_id.value = '';
      //   basic_set_id.value = '';
      //   items.value = [];
      // }
      // $('#modal-xl-create').modal('hide');
    }

    const addItem = (event) => {
      return;
      // const item = Object.create(_item);
      // item.process_id = process_id.value;
      // items.value.push(item);
    };


    const removeItem = (event, index) => {
      return;
      // var dataItems = [];
      // for (var i = 0; i < items.value.length; i++) {
      //   if (i != index) {
      //     dataItems.push(items.value[i]);
      //   }
      // }
      // items.value = dataItems;
    };

    const addItemHistory = (event) => {
      return;
      // const history = Object.create(_histories);
      // histories.value.push(history);
    };

    const removeItemHistory = (event, index) => {
      return;
      // var dataItemHistories = [];
      // for (var i = 0; i < histories.value.length; i++) {
      //   if (i != index) {
      //     dataItemHistories.push(histories.value[i]);
      //   }
      // }
      // histories.value = dataItemHistories;
    };

    const changeBasicItem = (event, inputName, index) => {
      return;
      // var value = event.target.value;
      // var dataItems = items.value;
      // if (!dataItems[index]) {
      //   return;
      // }
      // dataItems[index][inputName] = value;
      // items.value = dataItems;
    };

    const changeDataItemHistory = (event, inputName, index) => {
      return;
      // var value = event.target.value;
      // var dataItemHistories = histories.value;
      // if (!dataItemHistories[index]) {
      //   return;
      // }
      // dataItemHistories[index][inputName] = value;
      // histories.value = dataItemHistories;
    };

    const btnUpdateApproved = (event, index) => {
      return;
      // submitForm
      var dataItemHistories = histories.value;
      if (! dataItemHistories[index]) {
        return;
      }
      if (! dataItemHistories[index].edit && dataItemHistories[index].approved_name) {
        return;
      }
      dataItemHistories[index].approved_name = dataItemHistories[index].approved_name ? '' : user_name;
      histories.value = dataItemHistories;
      return;
      // end
    };

    const btnInspectionConfirmation = (event, j, index, type) => {
      if (type === 's_confirmation') {
        dataItems = confirmationDaily.value;
        if (! dataItems[j]) {
          return;
        }
        dataItems[j].confirmed_name = (dataItems[j].confirmed_name) ? '' : user_name;
        confirmationDaily.value = dataItems;
        setTimeout(() => {
          $('#modal-btn-submit').click();
        }, 2000);
      } else if (type === 'inspector') {
        dataItems = inspectorDaily.value;
        if (! dataItems[j]) {
          return;
        }
        dataItems[j].confirmed_name = (dataItems[j].confirmed_name) ? '' : user_name;
        inspectorDaily.value = dataItems;
        setTimeout(() => {
          $('#modal-btn-submit').click();
        }, 2000);
      }
    }

    const btnInspectionResult = (data) => {
      const {index, j, type, value} = data;
      var dataItems;
      if (type === 'item') {
        dataItems = items.value;
        if (! dataItems[index]) {
          return;
        }
        if (! dataItems[index].inspection_daily[j]) {
          return;
        }
        if (value === 'X' || value === 'O') {
          dataItems[index].inspection_daily[j].admission_decision = value;
          dataItems[index].inspection_daily[j].num_input = '';
        } else {
          dataItems[index].inspection_daily[j].admission_decision = '';
          dataItems[index].inspection_daily[j].num_input = value;
        }
        items.value = dataItems;
      }
      // console.log(data);
      $('#modal-xl-create').modal('hide');
      setTimeout(() => {
        $('#modal-btn-submit').click();
      }, 2000);
    };

    const btnUpdateCreater = (event, type) => {
      return;
      // if (type === 'confirmed_name') {
      //   if (! created_name.value) {
      //     return;
      //   }
      //   if (approved_name.value) {
      //     return;
      //   }
      // }
      // if (type === 'approved_name') {
      //   if (! confirmed_name.value) {
      //     return;
      //   }
      //   if (completed_name.value) {
      //     return;
      //   }
      // }
      // if (type === 'completed_name') {
      //   if (! approved_name.value) {
      //     return;
      //   }
      // }
      // if (updating.value) {
      //   return;
      // }
      // submitForm
      // if (type === 'confirmed_name') {
      //   confirmed_name.value = confirmed_name.value ? '' : user_name;
      // }
      // if (type === 'approved_name') {
      //   approved_name.value = approved_name.value ? '' : user_name;
      // }
      // if (type === 'completed_name') {
      //   completed_name.value = completed_name.value ? '' : user_name;
      // }
      // return;
      // end
      // var _token = $('meta[name="csrf-token"]').attr('content');
      // var link = $('#app-equipment-inspection').attr('data-href');
      // var data = {
      //   id: id.value,
      //   type,
      //   method: 'CONFIRM',
      //   // created_name: created_name.value,
      //   confirmed_name: confirmed_name.value,
      //   approved_name: approved_name.value,
      //   completed_name: completed_name.value,
      //   _token,
      // };
      // $.ajax({
      //   type: 'POST',
      //   url: link,
      //   data,
      //   beforeSend: function () {
      //     updating.value = true;
      //   },
      //   success: function(data) {
      //     if (data.status == 'error') {
      //       $('#res-message-equipment').html(makeMessage(data.status, data.message));
      //       document.getElementById("res-message-equipment").scrollIntoView();
      //       updating.value = false;
      //       setTimeout(function(){ $('#res-message-equipment').html('') }, 5000);
      //       return;
      //     } else {
      //       if (type === 'confirmed_name') {
      //         confirmed_name.value = data.data.confirmed_name;
      //       }
      //       if (type === 'approved_name') {
      //         approved_name.value = data.data.approved_name;
      //       }
      //       if (type === 'completed_name') {
      //         completed_name.value = data.data.completed_name;
      //       }
      //       setTimeout(function() {
      //         updating.value = false;
      //       }, 3000);
      //       // $('#res-message-equipment').html(makeMessage(data.status, data.message));
      //       // document.getElementById("res-message-equipment").scrollIntoView();
      //       // setTimeout(function(){ $('#res-message-equipment').html('') }, 5000);
      //       return;
      //     }
      //   },
      //   error: function (jqXHR, textStatus, errorThrown) {
      //     $('#res-message-equipment').html(makeMessage('danger', 'Error system'));
      //     document.getElementById("res-message-equipment").scrollIntoView();
      //     updating.value = false;
      //     setTimeout(function(){ $('#res-message-equipment').html('') }, 5000);
      //     return;
      //     // window.location.reload(true);
      //   }
      // });
    };

    const submitForm = (event) => {
      var _token = $('meta[name="csrf-token"]').attr('content');
      var link = $('#app-equipment-inspection').attr('data-href');
      var data = {
        id: id.value,
        year: year.value, month: month.value, department_id: department_id.value,
        department_name: department_name.value, department_code: department_code.value,
        line_id: line_id.value, line_code: line_code.value, line_name: line_name.value,
        process_id: process_id.value, basic_set_id: basic_set_id.value,
        // base64_image: base64_image.value,
        dataItems: items.value,
        // histories: histories.value,
        confirmation_daily: confirmationDaily.value,
        inspector_daily: inspectorDaily.value,
        // confirmed_name: confirmed_name.value,
        // approved_name: approved_name.value,
        // completed_name: completed_name.value,
        method: 'TABLET',
        _token,
      };
      $.ajax({
        type: 'POST',
        url: link,
        data,
        dataType: "json",
        beforeSend: function () {
          $('#modal-btn-submit').prop('disabled', true);
        },
        success: function(data) {
          $('#modal-btn-submit').prop('disabled', false);
          if (data.status == 'error') {
            $('#res-message-equipment').html(makeMessage(data.status, data.message));
            document.getElementById("res-message-equipment").scrollIntoView();
            setTimeout(function(){ $('#res-message-equipment').html('') }, 5000);
            return;
          } else {
            $('#res-message-equipment').html(makeMessage(data.status, data.message));
            document.getElementById("res-message-equipment").scrollIntoView();
            setTimeout(function(){ $('#res-message-equipment').html('') }, 5000);
            var dataItems = items.value;
            if (dataItems && dataItems.length) {
              for (var i = 0; i < dataItems.length; i++) {
                dataItems[i]['edit'] = false;
              }
              items.value = dataItems;
            }
            var dataItemHistories = histories.value;
            if (dataItemHistories && dataItemHistories.length) {
              for (var i = 0; i < dataItemHistories.length; i++) {
                dataItemHistories[i]['edit'] = false;
              }
              histories.value = dataItemHistories;
            }
            return;
          }
        },
        error: function (jqXHR, textStatus, errorThrown) {
          $('#res-message-equipment').html(makeMessage('danger', 'Error system'));
          document.getElementById("res-message-equipment").scrollIntoView();
          setTimeout(function(){ $('#res-message-equipment').html('') }, 5000);
          return;
          // window.location.reload(true);
        }
      });
    };

    const initialDataTable = (initial) => {
      let el2;

      // for (var i = 2; i < 7; i++) {
      //   el2 = $('#head-equipment-inspection').find(`th:nth-child(${i})`);
      //   if (el2) {
      //     styles.value[`style${i}`] = `left: ${el2.css('left')}; position: sticky;`;
      //   }
      // }
      id.value = initial.id ?? '';
      created_name.value = initial.created_name ?? '';
      confirmed_name.value = initial.confirmed_name ?? '';
      approved_name.value = initial.approved_name ?? '';
      completed_name.value = initial.completed_name ?? '';
      base64_image.value = initial.base64_image ?? '';
      year.value = initial.json_data.year ?? '';
      month.value = initial.json_data.month ?? '';
      department_id.value = initial.json_data.department_id ?? '';
      department_name.value = initial.json_data.department_name ?? '';
      department_code.value = initial.json_data.department_code ?? '';
      line_id.value = initial.json_data.line_id ?? '';
      line_code.value = initial.json_data.line_code ?? '';
      line_name.value = initial.json_data.line_name ?? '';
      process_id.value = initial.json_data.process_id ?? '';
      basic_set_id.value = initial.json_data.basic_set_id ?? '';
      var dataItems = [];
      if (initial.json_data && initial.json_data.dataItems && initial.json_data.dataItems.length) {
        for (var i = 0; i < initial.json_data.dataItems.length; i++) {
          initial.json_data.dataItems[i]['edit'] = false;
          dataItems.push(Object.create(initial.json_data.dataItems[i]));
        }
        items.value = dataItems;
      }
      var dataItemHistories = [];
      if (initial.json_data && initial.json_data.histories && initial.json_data.histories.length) {
        for (var i = 0; i < initial.json_data.histories.length; i++) {
          initial.json_data.histories[i]['edit'] = false;
          dataItemHistories.push(Object.create(initial.json_data.histories[i]));
        }
        histories.value = dataItemHistories;
      }

      var dataConfirmationDaily = [];
      if (initial.json_data && initial.json_data.s_confirmation_daily && initial.json_data.s_confirmation_daily.length) {
        for (var i = 0; i < initial.json_data.s_confirmation_daily.length; i++) {
          dataConfirmationDaily.push(Object.create(initial.json_data.s_confirmation_daily[i]));
        }
        confirmationDaily.value = dataConfirmationDaily;
      }
      var dataInspectorDaily = [];
      if (initial.json_data && initial.json_data.inspector_daily && initial.json_data.inspector_daily.length) {
        for (var i = 0; i < initial.json_data.inspector_daily.length; i++) {
          dataInspectorDaily.push(Object.create(initial.json_data.inspector_daily[i]));
        }
        inspectorDaily.value = dataInspectorDaily;
      }
      // console.log(initial);
    };

    return {
      id, year, month, department_id, department_name, department_code,
      line_id, line_code, line_name, process_id,
      basic_set_id, base64_image, items,
      created_name, confirmed_name, approved_name, completed_name,
      showModalUpload, showModalInspectionResult, showModalLine, initialItem,
      btnLineCode, btnUploadFile, btnProcess, addItem, removeItem,
      changeBasicItem, submitForm, initialDataTable, btnUpdateCreater, updating,
      styles, histories, addItemHistory, removeItemHistory, changeDataItemHistory,
      btnUpdateApproved, btnInspectionResult, confirmationDaily, inspectorDaily,
      btnInspectionConfirmation,
    };
  },
  methods: {
    getResult: (dailyData) => {
        if (dailyData.admission_decision) {
          return dailyData.admission_decision;
        } else if (dailyData.num_input) {
          return dailyData.num_input;
        }
        return '';
    },
    getResultConfirmation: (dailyData) => {
        if (dailyData.confirmed_name) {
          return dailyData.confirmed_name;
        }
        return '';
    },
    isWeekend(day) {
      const currentYear = this.year;
      const currentMonth = this.month;
      // Calculate the date for the day of the month
      const currentDate = new Date(currentYear, currentMonth - 1, day);

      // Check if the day is a Saturday (6) or Sunday (0)
      return currentDate.getDay() === 6 || currentDate.getDay() === 0;
    },
  },
  mounted() {
    console.log('Component mounted.');
  },
  created() {
    console.log('Component created');
  }
}
</script>