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
                          <input id="datemaskYear" name="year" v-model="year" type="text" class="form-control bg-td-green"
                            data-inputmask-alias="datetime" data-inputmask-inputformat="yyyy" data-mask=""
                            placeholder="YYYY" maxlength="4">
                          <div class="input-group-append"><span class="input-group-text">年</span></div>
                        </div>
                      </div>
                      <div class="col-6 col-sm-6">
                        <div class="input-group form-group m-0">
                          <input id="datemaskMonth" name="month" v-model="month" type="text"
                            class="form-control bg-td-green" data-inputmask-alias="datetime"
                            data-inputmask-inputformat="mm" data-mask="" placeholder="MM" maxlength="2">
                          <div class="input-group-append"><span class="input-group-text">月</span></div>
                        </div>
                      </div>
                    </div>
                  </td>
                  <td class="text-center bg-light" style="width: 10%; vertical-align: middle;">実施部署</td>
                  <td style="width: 20%; vertical-align: middle;" class="bg-td-green text-center"
                    @click="showModalDepartment($event)">
                    <div class="form-group m-0">
                      <input id="departmentID" type="text" name="department_id" :value="department_id"
                        class="form-control bg-td-green">
                      <p v-if="department_id" style="margin-bottom: 0px;">{{ `[${department_name}] ${department_code}` }}
                      </p>
                    </div>
                  </td>
                  <td style="width: 20%; vertical-align: middle;" class="text-center bg-light">
                    <p style="margin-bottom: 0px;">項目設定部署</p>
                    <p style="margin-bottom: 0px;">生産統括部　生産技術課</p>
                  </td>
                </tr>
                <tr>
                  <!-- <td style="vertical-align: middle;" @click="showModalUpload($event)" id="image-preview" -->
                  <td style="vertical-align: middle;" id="image-preview" class="bg-td-green text-center" colspan="2"
                    rowspan="2">
                    <div class="form-group m-0">
                      <div class="dropzone"></div>
                      <!-- <input id="base64Image" type="text" name="base64_image" class="form-control bg-td-green">
                      <div v-if="base64_image" id="examplePreview1" style="height: 186px;">
                        <img id="examplePreview" style="height: 100%;" class="thumbnail-img" :src="base64_image" alt="">
                      </div> -->
                    </div>
                  </td>
                  <td style="vertical-align: middle;" class="bg-light text-center">ライン名<br>(ﾗｲﾝｺｰﾄﾞ)</td>
                  <td style="width: 20%; vertical-align: middle;" class="bg-td-green text-center"
                    @click="showModalLine($event)">
                    <div class="form-group m-0">
                      <input id="lineID" type="text" name="line_id" :value="line_id" class="form-control bg-td-green">
                      <p v-if="line_id" style="margin-bottom: 0px;">{{ `${line_name} (${line_code})` }}</p>
                    </div>
                  </td>
                  <td id="creater" class="bg-td-green" style="padding: 0px; vertical-align: middle;">
                    <div class="input-group">
                      <div class="input-group-prepend text-center">
                        <span class="input-group-text bd-radius-0 bg-light">作　成</span>
                      </div>
                      <input type="text" class="form-control bd-radius-0" style="color: black;"
                        :class="created_name ? 'bg-td-white' : 'bg-td-green'" :value="created_name" :disabled="true">
                    </div>
                    <div class="input-group">
                      <div class="input-group-prepend text-center">
                        <span class="input-group-text bd-radius-0 bg-light">確　認</span>
                      </div>
                      <!-- <div class="form-control p-0" @click="showModalConfirmedBy($event)" data-type="confirmed_name"> -->
                      <div class="form-control p-0" @click="btnUpdateCreater($event, 'confirmed_name')"
                        data-type="confirmed_name">
                        <input id="confBy" type="text" class="form-control bd-radius-0" style="color: black;"
                          :class="confirmed_name ? 'bg-td-white' : 'bg-td-green'" :value="confirmed_name"
                          :disabled="true">

                        <!-- <input id="confBy" type="text" name="confirmedBy_id" style="color: black;" :value="confirmedBy_id" class="form-control bg-td-green" :disabled="true">
                        <p v-if="confirmedBy_id" style="margin-bottom: 0px; display: none;">{{ `${confirmedBy_name} (${confirmedBy_code})` }}</p> -->
                      </div>
                    </div>
                    <div class="input-group">
                      <div class="input-group-prepend text-center">
                        <span class="input-group-text bd-radius-0 bg-light">承　認</span>
                      </div>
                      <!-- <div class="form-control p-0" @click="showModalApprovedBy($event)" data-type="approved_name"> -->
                      <div class="form-control p-0" @click="btnUpdateCreater($event, 'approved_name')"
                        data-type="approved_name">
                        <input id="appBy" type="text" class="form-control bd-radius-0" style="color: black;"
                          :class="approved_name ? 'bg-td-white' : 'bg-td-green'" :value="approved_name" :disabled="true">

                        <!-- <input id="appBy" type="text" name="approvedBy_id" style="color: black;" :value="approvedBy_id" class="form-control bg-td-green" :disabled="true">
                        <p v-if="approvedBy_id" style="margin-bottom: 0px; display: none;">{{ `${approvedBy_name} (${approvedBy_code})` }}</p> -->
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
                    <div @click="btnUpdateCreater($event, 'completed_name')" data-type="completed_name"
                      class="input-group">
                      <input id="comBy" type="text" class="form-control text-center bd-radius-0" style="color: black;"
                        :class="completed_name ? 'bg-td-white' : 'bg-td-green'" :value="completed_name" :disabled="true">
                      <!-- <div @click="showModalCompletedBy($event)" data-type="completed_name" class="input-group"> -->
                      <!-- <input id="comBy" type="text" class="form-control text-center bd-radius-0" :value="completedBy_id" :class="completed_name ? 'bg-td-white' : 'bg-td-green'" :disabled="true"> -->
                      <!-- <input id="comBy" type="text" name="completedBy_id" style="color: black;" :value="completedBy_id" class="form-control text-center bg-td-green" :disabled="true">
                      <p v-if="completedBy_id" style="margin-bottom: 0px; display: none;">{{ `${completedBy_name} (${completedBy_code})` }}</p> -->
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
                  <th class="bg-light" style="width: 10%;">
                    <div></div>
                  </th>
                  <th class="bg-light" style="width: 10%;">
                    <div>工程</div>
                  </th>
                  <th class="bg-light">点　検　項　目</th>
                  <th class="bg-light">点検ポイント（基準）</th>
                  <th class="bg-light" style="width: 10%;">周期</th>
                  <th class="bg-light" style="width: 15%;">点検者</th>
                </tr>
              </thead>
              <tbody id="body-equipment-inspection-21">
                <template v-if="items && items.length">
                  <tr class="text-center even" v-for="(item, index) in items">
                    <td class="dtfc-fixed-left">
                      <div class="form-group m-0">
                        <button type="button" @click="removeItem($event, index)" :data-index="index"
                          class="btn btn-danger bg-delete"><i class="fa fa-times"></i></button>
                      </div>
                    </td>
                    <td class="dtfc-fixed-left" :style="styles.style2">
                      <div>{{ item.process_id }}</div>
                    </td>
                    <td class="dtfc-fixed-left" :style="styles.style3">
                      <div class="form-group m-0">
                        <input type="text" name="inspection_item[]"
                          v-on:keyup="changeBasicItem($event, 'inspection_item', index)" :data-index="index"
                          :value="item.inspection_item" class="form-control" :id="`inputInspectionItems${index}`"
                          :disabled="!item.edit">
                      </div>
                    </td>
                    <td class="dtfc-fixed-left" :style="styles.style4">
                      <div class="form-group m-0">
                        <input type="text" name="inspection_point[]"
                          v-on:keyup="changeBasicItem($event, 'inspection_point', index)" :data-index="index"
                          :value="item.inspection_point" class="form-control" :id="`inputInspectionPoint${index}`"
                          :disabled="!item.edit">
                      </div>
                    </td>
                    <td class="dtfc-fixed-left" :style="styles.style5">
                      <div class="form-group m-0">
                        <select name="inspection_period[]"
                          v-on:change="changeBasicItem($event, 'inspection_period', index)" :data-index="index"
                          class="form-control" :id="`inputPeriod${index}`" :disabled="!item.edit">
                          <option style="display: none;" value=""></option>
                          <option :selected="item.inspection_period === '1/D'" value="1/D">1/D</option>
                          <option :selected="item.inspection_period === '1/W'" value="1/W">1/W</option>
                          <option :selected="item.inspection_period === '1/M'" value="1/M">1/M</option>
                        </select>
                      </div>
                    </td>
                    <td class="dtfc-fixed-left" :style="styles.style6">
                      <div class="form-group m-0">
                        <select name="typeof_inspector[]" v-on:change="changeBasicItem($event, 'typeof_inspector', index)"
                          :data-index="index" class="form-control" :id="`inputInspector${index}`" :disabled="!item.edit">
                          <option style="display: none;" value=""></option>
                          <option :selected="item.typeof_inspector === '作業者'" value="作業者">作業者</option>
                          <option :selected="item.typeof_inspector === 'ﾘﾘｰﾌ'" value="ﾘﾘｰﾌ">ﾘﾘｰﾌ</option>
                        </select>
                      </div>
                    </td>
                  </tr>
                  <tr class="text-center odd">
                    <td class="dtfc-fixed-left">
                      <div class="form-group m-0">
                        <button type="button" @click="addItem($event)" class="btn btn-success bg-add"><i
                            class="fa fa-plus"></i></button>
                      </div>
                    </td>
                    <td class="dtfc-fixed-left" :style="styles.style2">
                      <div></div>
                    </td>
                    <td class="dtfc-fixed-left" :style="styles.style3">
                      <div></div>
                    </td>
                    <td class="dtfc-fixed-left" :style="styles.style4">
                      <div></div>
                    </td>
                    <td class="dtfc-fixed-left" :style="styles.style5">
                      <div></div>
                    </td>
                    <td class="dtfc-fixed-left" :style="styles.style6">
                      <div></div>
                    </td>
                  </tr>
                </template>
                <tr v-else class="text-center odd">
                  <td class="dtfc-fixed-left">
                    <div class="form-group m-0">
                      <button type="button" @click="initialItem($event)" class="btn btn-success bg-add"><i
                          class="fa fa-plus-square"></i></button>
                    </div>
                  </td>
                  <td class="dtfc-fixed-left" :style="styles.style2">
                    <div></div>
                  </td>
                  <td class="dtfc-fixed-left" :style="styles.style3">
                    <div></div>
                  </td>
                  <td class="dtfc-fixed-left" :style="styles.style4">
                    <div></div>
                  </td>
                  <td class="dtfc-fixed-left" :style="styles.style5">
                    <div></div>
                  </td>
                  <td class="dtfc-fixed-left" :style="styles.style6">
                    <div></div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="col-6" style="padding-left: 0px; width: 300px;">
            <div class="table-s1">
              <div class="table-s2">
                <table id="table-equipment-inspection-22" class="table-s0 table-s3 table table-bordered">
                  <thead id="head-equipment-inspection-22">
                    <tr class="text-thin text-center">
                      <th class="bg-light" v-for="i in 31">
                        <div>{{ i }}</div>
                      </th>
                    </tr>
                  </thead>
                  <tbody id="body-equipment-inspection-22">
                    <template v-if="items && items.length">
                      <tr class="text-center even" v-for="(item, index) in items">
                        <td v-for="j in 31">
                          <div></div>
                        </td>
                      </tr>
                      <tr class="text-center odd">
                        <td v-for="j in 31">
                          <div></div>
                        </td>
                      </tr>
                    </template>
                    <tr v-else class="text-center odd">
                      <td v-for="j in 31">
                        <div></div>
                      </td>
                    </tr>
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
            <div class="table-responsive1" style="padding-top: 0px;">
              <table id="table-equipment-inspection-3" class="table-s4 table table-bordered">
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
                    <th class="col-1 bg-light"></th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-if="histories && histories.length" v-for="(history, index) in histories">
                    <td class="col-2">
                      <div class="form-group row m-0">
                        <input type="text" name="h_daily[]" v-on:keyup="changeDataItemHistory($event, 'daily', index)"
                          :data-index="index" :value="history.daily" class="form-control"
                          :class="history.edit ? 'bg-td-white' : 'bg-td-white bd-none'" :id="`inputDaily${index}`"
                          :disabled="!history.edit" placeholder="YYYY/MM/DD" required title="入力してください">
                      </div>
                    </td>
                    <td class="col-1">
                      <div class="form-group row m-0">
                        <select name="h_sign[]" v-on:change="changeDataItemHistory($event, 'sign', index)"
                          :data-index="index" class="form-control text-center"
                          :class="history.edit ? 'bg-td-white' : 'bg-td-white bd-none'" :id="`inputSign${index}`"
                          :disabled="!history.edit" required title="入力してください">
                          <option style="display: none;" value=""></option>
                          <option :selected="history.sign === '+'" value="+">+</option>
                          <option :selected="history.sign === '-'" value="-">-</option>
                        </select>
                      </div>
                    </td>
                    <td class="col-2">
                      <div class="form-group row m-0">
                        <input type="text" name="h_content[]" v-on:keyup="changeDataItemHistory($event, 'content', index)"
                          :data-index="index" :value="history.content" class="form-control"
                          :class="history.edit ? 'bg-td-white' : 'bg-td-white bd-none'" :id="`inputContenty${index}`"
                          :disabled="!history.edit" required title="入力してください">
                      </div>
                    </td>
                    <td class="col-2">
                      <div class="form-group row m-0">
                        <input style="border: none;" type="text" name="h_creater[]" :data-index="index"
                          class="form-control text-center bd-radius-0"
                          :class="history.edit ? 'bg-td-white' : 'bg-td-white bd-none'" :id="`inputCreater${index}`"
                          :value="history.edit ? history.created_name : history.created_name" :disabled="true">
                      </div>
                    </td>
                    <td class="col-2">
                      <div @click="btnUpdateApproved($event, index)" data-type="h_approved_name" class="input-group">
                        <input type="text" class="h_approved_name form-control text-center bd-radius-0"
                          :class="(history.edit && history.approved_name) ? 'bg-td-white' : 'bg-td-white bd-none'"
                          :value="history.approved_name" :disabled="true">
                      </div>
                    </td>
                    <td class="col-1">
                      <div class="form-group m-0">
                        <button type="button" @click="removeItemHistory($event, index)" :data-index="index"
                          class="btn btn-danger bg-delete"><i class="fa fa-times"></i></button>
                      </div>
                    </td>
                  </tr>
                </tbody>
                <tfoot>
                  <tr>
                    <th colspan="6" style="border: none;">
                      <div class="row float-right" style="margin: 0.5rem 0.5rem 0.5rem 0px;">
                        <button @click="addItemHistory(this)" type="button"
                          class="btn btn-success btn-add-h-item bg-add">行を追加</button>
                      </div>
                    </th>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>
        </div>
      </div>
      <div class="card-footer">
        <div class="row">
          <div class="col-12">
            <button type="button" class="btn btn-success mr-1" id="modal-btn-submit" :link="link"
              onclick="submitForm(event, this, link)">更新する</button>
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
      dropzone: null,
      _token: '',
      link: '',
    }
  },
  setup(props) {
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

    const confirmedBy_code = ref('');
    const confirmedBy_name = ref('');
    const confirmedBy_id = ref('');

    const approvedBy_code = ref('');
    const approvedBy_name = ref('');
    const approvedBy_id = ref('');

    const completedBy_code = ref('');
    const completedBy_name = ref('');
    const completedBy_id = ref('');

    const process_id = ref('');
    const basic_set_id = ref('');
    const base64_image = ref('');
    const items = ref([]);
    const histories = ref([]);

    const styles = ref(_styles);

    const showModalUpload = (event) => {
      $('#modal-xl-upload').modal('show');
    };

    const initialItem = (event) => {
      $('#basic-set').show();
      $('#department-set').hide();
      $('#line-set').hide();
      $('#confimed-by').hide();
      $('#completed-by').hide();
      $('#approved-by').hide();
      $('#modal-xl-create').modal('show');
    };

    const showModalDepartment = (event) => {
      $('#department-set').show();
      $('#line-set').hide();
      $('#basic-set').hide();
      $('#completed-by').hide();
      $('#approved-by').hide();
      $('#modal-xl-create').modal('show');
    };
    const showModalLine = (event) => {
      $('#line-set').show();
      $('#department-set').hide();
      $('#basic-set').hide();
      $('#completed-by').hide();
      $('#approved-by').hide();
      $('#modal-xl-create').modal('show');
    };


    const showModalConfirmedBy = (event) => {
      $('#basic-set').hide();
      $('#department-set').hide();
      $('#line-set').hide();
      $('#approved-by').hide();
      $('#completed-by').hide();

      $('#confirmed-by').show();
      $('#modal-xl-create').modal('show');
    };

    const showModalApprovedBy = (event) => {
      $('#basic-set').hide();
      $('#department-set').hide();
      $('#line-set').hide();
      $('#completed-by').hide();
      $('#confirmed-by').hide();

      $('#approved-by').show();
      $('#modal-xl-create').modal('show');
    };

    const showModalCompletedBy = (event) => {
      $('#basic-set').hide();
      $('#department-set').hide();
      $('#line-set').hide();
      $('#confirmed-by').hide();
      $('#approved-by').hide();

      $('#completed-by').show();
      $('#modal-xl-create').modal('show');
    };

    const btnLineCode = (data, type) => {
      const { id, code, name } = data;
      if (type === 'lines') {
        if (id) {
          line_id.value = id;
          line_code.value = code;
          line_name.value = name;
        } else {
          line_id.value = '';
          line_code.value = '';
          line_name.value = '';
        }
      } else if (type === 'department') {
        if (id) {
          department_id.value = id;
          department_name.value = code;
          department_code.value = name;
        } else {
          department_id.value = '';
          department_name.value = '';
          department_code.value = '';
        }
      }
      $('#modal-xl-create').modal('hide');
    };

    const btnEmployeeCode = (data, type) => {
      const { id, code, name } = data;
      switch (type) {
        case 'confirmed':
          console.log('code', code)
          console.log('name', name)
          if (id) {
            confirmedBy_code.value = id;
            confirmedBy_name.value = code;
            confirmedBy_id.value = name;
          } else {
            confirmedBy_code.value = '';
            confirmedBy_name.value = '';
            confirmedBy_id.value = '';
          }
          break;
        case 'approved':
          if (id) {
            approvedBy_id.value = name;
            approvedBy_code.value = id;
            approvedBy_name.value = code;
          } else {
            approvedBy_id.value = '';
            approvedBy_code.value = '';
            approvedBy_name.value = '';
          }
          break;
        case 'completed':
          console.log(id, code, name)
          if (id) {
            completedBy_code.value = id;
            completedBy_id.value = name;
            completedBy_name.value = code;
          } else {
            completedBy_code.value = '';
            completedBy_id.value = '';
            completedBy_name.value = '';
          }
          break;

      }
      $('#modal-xl-create').modal('hide');
    };

    const btnUploadFile = (data) => {
      const { img } = data;
      if (img) {
        base64_image.value = img;
      } else {
        base64_image.value = '';
      }
      $('#modal-xl-upload').modal('hide');
    };

    const btnProcess = (data) => {
      const { id, processId, dataItems } = data;
      if (id) {
        process_id.value = processId;
        basic_set_id.value = id;
        for (var i = 0; i < dataItems.length; i++) {
          dataItems[i]['process_id'] = processId;
          dataItems[i]['edit'] = false;
        }
        items.value = dataItems;
      } else {
        process_id.value = '';
        basic_set_id.value = '';
        items.value = [];
      }
      $('#modal-xl-create').modal('hide');
    }

    const addItem = (event) => {
      const item = Object.create(_item);
      item.process_id = process_id.value;
      items.value.push(item);
    };


    const removeItem = (event, index) => {
      var dataItems = [];
      for (var i = 0; i < items.value.length; i++) {
        if (i != index) {
          dataItems.push(items.value[i]);
        }
      }
      items.value = dataItems;
    };

    const addItemHistory = (event) => {
      const history = Object.create(_histories);
      histories.value.push(history);
    };

    const removeItemHistory = (event, index) => {
      var dataItemHistories = [];
      for (var i = 0; i < histories.value.length; i++) {
        if (i != index) {
          dataItemHistories.push(histories.value[i]);
        }
      }
      histories.value = dataItemHistories;
    };

    const changeBasicItem = (event, inputName, index) => {
      var value = event.target.value;
      var dataItems = items.value;
      if (!dataItems[index]) {
        return;
      }
      dataItems[index][inputName] = value;
      items.value = dataItems;
    };

    const changeDataItemHistory = (event, inputName, index) => {
      var value = event.target.value;
      var dataItemHistories = histories.value;
      if (!dataItemHistories[index]) {
        return;
      }
      dataItemHistories[index][inputName] = value;
      histories.value = dataItemHistories;
    };

    const btnUpdateApproved = (event, index) => {
      // submitForm
      var dataItemHistories = histories.value;
      if (!dataItemHistories[index]) {
        return;
      }
      if (!dataItemHistories[index].edit && dataItemHistories[index].approved_name) {
        return;
      }
      dataItemHistories[index].approved_name = dataItemHistories[index].approved_name ? '' : user_name;
      histories.value = dataItemHistories;
      return;
      // end
    };

    const btnUpdateCreater = (event, type) => {
      if (type === 'confirmed_name') {
        if (!created_name.value) {
          return;
        }
        if (approved_name.value) {
          return;
        }
      }
      if (type === 'approved_name') {
        if (!confirmed_name.value) {
          return;
        }
        if (completed_name.value) {
          return;
        }
      }
      if (type === 'completed_name') {
        if (!approved_name.value) {
          return;
        }
      }
      if (updating.value) {
        return;
      }
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
      var _token = $('meta[name="csrf-token"]').attr('content');
      var link = $('#app-equipment-inspection').attr('data-href');
      var data = {
        id: id.value,
        type,
        method: 'CONFIRM',
        // created_name: created_name.value,
        confirmed_name: confirmed_name.value,
        approved_name: approved_name.value,
        completed_name: completed_name.value,
        _token,
      };
      $.ajax({
        type: 'POST',
        url: link,
        data,
        beforeSend: function () {
          updating.value = true;
        },
        success: function (data) {
          if (data.status == 'error') {
            $('#res-message-equipment').html(makeMessage(data.status, data.message));
            document.getElementById("res-message-equipment").scrollIntoView();
            updating.value = false;
            setTimeout(function () { $('#res-message-equipment').html('') }, 5000);
            return;
          } else {
            if (type === 'confirmed_name') {
              confirmed_name.value = data.data.confirmed_name;
            }
            if (type === 'approved_name') {
              approved_name.value = data.data.approved_name;
            }
            if (type === 'completed_name') {
              completed_name.value = data.data.completed_name;
            }
            setTimeout(function () {
              updating.value = false;
            }, 3000);
            $('#res-message-equipment').html(makeMessage(data.status, data.message));
            document.getElementById("res-message-equipment").scrollIntoView();
            setTimeout(function () { $('#res-message-equipment').html('') }, 5000);
            return;
          }
        },
        error: function (jqXHR, textStatus, errorThrown) {
          $('#res-message-equipment').html(makeMessage('danger', 'Error system'));
          document.getElementById("res-message-equipment").scrollIntoView();
          updating.value = false;
          setTimeout(function () { $('#res-message-equipment').html('') }, 5000);
          return;
          // window.location.reload(true);
        }
      });
    };

    const submitForm = (event) => {
      var _token = $('meta[name="csrf-token"]').attr('content');
      var link = $('#app-equipment-inspection').attr('data-href');
      var data = {
        id: id.value, year: year.value, month: month.value, department_id: department_id.value,
        department_name: department_name.value, department_code: department_code.value,
        line_id: line_id.value, line_code: line_code.value, line_name: line_name.value,
        process_id: process_id.value, basic_set_id: basic_set_id.value,
        base64_image: base64_image.value,
        dataItems: items.value,
        histories: histories.value,
        confirmed_name: confirmed_name.value,
        approved_name: approved_name.value,
        completed_name: completed_name.value,
        method: 'PUT',
        _token,
        currentUrl: ref(props.currentUrl).value,
        fileID: ref(props.fileID).value,
      };

      $.ajax({
        type: 'POST',
        url: link,
        data,
        beforeSend: function () {
          $('#modal-btn-submit').prop('disabled', true);
        },
        success: function (data) {
          $('#modal-btn-submit').prop('disabled', false);
          if (data.status == 'error') {
            $('#res-message-equipment').html(makeMessage(data.status, data.message));
            document.getElementById("res-message-equipment").scrollIntoView();
            setTimeout(function () { $('#res-message-equipment').html('') }, 5000);

            return;
          } else {

            $('#res-message-equipment').html(makeMessage(data.status, data.message));
            document.getElementById("res-message-equipment").scrollIntoView();
            setTimeout(function () { $('#res-message-equipment').html('') }, 5000);
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
          setTimeout(function () { $('#res-message-equipment').html('') }, 5000);
          return;
          // window.location.reload(true);
        }
      });
    };

    const initialDataTable = (initial) => {
      let el2;
      for (var i = 2; i < 7; i++) {
        el2 = $('#head-equipment-inspection').find(`th:nth-child(${i})`);
        if (el2) {
          styles.value[`style${i}`] = `left: ${el2.css('left')}; position: sticky;`;
        }
      }
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
    };

    return {
      id,
      year,
      month,
      department_id,
      department_name,
      department_code,
      line_id,
      line_code,
      line_name,
      confirmedBy_code,
      confirmedBy_name,
      confirmedBy_id,
      approvedBy_code,
      approvedBy_name,
      approvedBy_id,
      completedBy_code,
      completedBy_name,
      completedBy_id,
      process_id,
      basic_set_id,
      base64_image,
      items,
      created_name,
      confirmed_name,
      approved_name,
      completed_name,
      showModalUpload,
      showModalDepartment,
      showModalLine,
      showModalConfirmedBy,
      showModalApprovedBy,
      showModalCompletedBy,
      initialItem,
      btnLineCode,
      btnEmployeeCode,
      btnUploadFile,
      btnProcess,
      addItem,
      removeItem,
      changeBasicItem,
      submitForm,
      initialDataTable,
      btnUpdateCreater,
      updating,
      styles,
      histories,
      addItemHistory,
      removeItemHistory,
      changeDataItemHistory,
      btnUpdateApproved,
    };
  },
  props: ['userId', 'currentUrl', 'fileID'],
  mounted() {
    console.log('Component mounted.');

    const dropzoneScript = document.createElement("script");
    dropzoneScript.src = "https://unpkg.com/dropzone@5/dist/min/dropzone.min.js";

    const dropzoneStyle = document.createElement("style");
    dropzoneStyle.href = "https://unpkg.com/dropzone@5/dist/min/dropzone.min.css";
    dropzoneStyle.type = "text/css";
    dropzoneStyle.rel = "stylesheet";

    this._token = $('meta[name="csrf-token"]').attr('content');
    this._link = this.currentUrl;

    // console.log(this.userId, this.currentUrl, this.fileID)

    this.initializeDropzone();
  },
  methods: {
    initializeDropzone() {
      const _token = $('meta[name="csrf-token"]').attr('content');;
      const currentUrl = this.currentUrl;
      const userId = this.userId;
      const fileID = this.fileID;

      let dropzoneInstance;

      Dropzone.autoDiscover = false;

      if (this.dropzone === null) {
        this.dropzone = new Dropzone("div.dropzone", {
          url: "/temporary-upload",
          addRemoveLinks: true,
          uploadMultiple: false,
          createImageThumbnails: true, 
          thumbnailWidth: null,
          thumbnailHeight: null,
          dictRemoveFile: "<span style='color:white; background-color:red; padding:3px 5px; border-radius: 50%; cursor:pointer'>X</span>",
          maxFilesize: 20,
          maxFile: 1,
          acceptedFiles: "image/gif,image/jpeg,image/jpg,image/x-png,image/png,",
          params: {
            '_token': _token,
            'form': currentUrl,
            'user_id': userId,
            'file_id': fileID,
          },
          success: function (file, response) {
            if (response.success) {
              const thumbnailElement = file.previewElement.querySelector(".dz-thumbnail");
              if (thumbnailElement) {
                thumbnailElement.remove();
              }
            }
          },
          init: function () {
            dropzoneInstance = this;
            $.ajax({
              url: '/temporary-upload',
              type: 'get',
              data: {
                '_token': _token,
                'form': currentUrl,
                'file_id' : fileID,
                'user_id': userId,
              },
              dataType: 'json',
              success: function (response) {
                if (response && response.name && response.size) {
                  let imgPath = 'data:image/jpeg;base64,' + response.file;

                  var mockFile = {
                    name: response.name,
                    size: response.size,
                    accepted: true,
                  };
                  dropzoneInstance.files.push(mockFile);
                  dropzoneInstance.displayExistingFile(mockFile, imgPath);
                } else {
                  console.error('Invalid response data:', response);
                }
              },
              error: function (jqXHR, textStatus, errorThrown) {
                console.error('AJAX request failed:', textStatus, errorThrown);
              }
            });
          },
        });
      }
    }
  },
  created() {
    console.log('Component created');
  }
}
</script>
<link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
<style>

.dropzone {
  border: 2px solid #dfdfdf;
  min-height: 180px;
  background: #fff;
  padding: 0;
  margin: 0;
}
.dropzone .dz-preview.dz-image-preview {
  background: #dfdfdf;
  min-width: 100%;
  min-height: 100%;
  padding: 0;
  margin: 0;
}
.dropzone .dz-preview .dz-image {
  border-radius: 0px;
  height: 180px;
  max-height: 180px;
  min-height: 180px;
  width: inherit;
}
.dz-image img{width: 100%;height: 100%;}

.dropzone .dz-message {
  text-align: center;
  margin: 5em 0;
}
</style>