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
                          <input id="datemaskYear" name="year" v-model="year" type="text" class="form-control bg-td-green" data-inputmask-alias="datetime" data-inputmask-inputformat="yyyy" data-mask="" placeholder="YYYY" maxlength="4">
                          <div class="input-group-append"><span class="input-group-text">年</span></div>
                        </div>
                      </div>
                      <div class="col-6 col-sm-6">
                        <div class="input-group form-group m-0">
                          <input id="datemaskMonth" name="month" v-model="month" type="text" class="form-control bg-td-green" data-inputmask-alias="datetime" data-inputmask-inputformat="mm" data-mask="" placeholder="MM" maxlength="2">
                          <div class="input-group-append"><span class="input-group-text">月</span></div>
                        </div>
                      </div>
                    </div>
                  </td>
                  <td class="text-center bg-light" style="width: 10%; vertical-align: middle;">実施部署</td>
                  <td style="width: 20%; vertical-align: middle;" class="bg-td-green text-center" @click="showModalDepartment($event)">
                    <div class="form-group m-0">
                      <input id="departmentID" type="text" name="department_id" :value="department_id" class="form-control bg-td-green">
                      <p v-if="department_id" style="margin-bottom: 0px;">{{ `[${department_name}] ${department_code}` }}</p>
                    </div>
                  </td>
                  <td style="width: 20%; vertical-align: middle;" class="text-center bg-light">
                    <p style="margin-bottom: 0px;">項目設定部署</p>
                    <p style="margin-bottom: 0px;">生産統括部　生産技術課</p>
                  </td>
                </tr>
                <tr>
                  <!-- <td style="vertical-align: middle;" @click="showModalUpload($event)" id="image-preview" class="bg-td-green text-center" colspan="2" rowspan="2"> -->
                  <td style="vertical-align: middle;" id="image-preview" class="bg-td-green text-center" colspan="2"
                    rowspan="2">
                    <div class="form-group m-0">
                      <div class="dropzone" id="myDropzone"></div>
                      <!-- <input id="base64Image" type="text" name="base64_image" class="form-control bg-td-green">
                      <div v-if="base64_image" id="examplePreview1" style="height: 186px;">
                        <img id="examplePreview" style="height: 100%;" class="thumbnail-img" :src="base64_image" alt="">
                      </div> -->
                    </div>
                  </td>
                  <td style="vertical-align: middle;" class="bg-light text-center">ライン名<br>(ﾗｲﾝｺｰﾄﾞ)</td>
                  <td style="width: 20%; vertical-align: middle;" class="bg-td-green text-center" @click="showModalLine($event)">
                    <div class="form-group m-0">
                      <input id="lineID" type="text" name="line_id" :value="line_id" class="form-control bg-td-green">
                      <p v-if="line_id" style="margin-bottom: 0px;">{{ `${line_name} (${line_code})` }}</p>
                    </div>
                  </td>
                  <td id="creater" style="padding: 0px; vertical-align: middle;">
                    <div class="input-group">
                      <div class="input-group-prepend text-center">
                        <span class="input-group-text bd-radius-0 bg-light">作　成</span>
                      </div>
                      <input type="text" class="form-control bd-radius-0 bg-td-white" :disabled="true">
                    </div>
                    <div class="input-group">
                      <div class="input-group-prepend text-center">
                        <span class="input-group-text bd-radius-0 bg-light">確　認</span>
                      </div>
                      <input type="text" class="form-control bd-radius-0 bg-td-white" :disabled="true">
                    </div>
                    <div  class="input-group">
                      <div class="input-group-prepend text-center">
                        <span class="input-group-text bd-radius-0 bg-light">承　認</span>
                      </div>
                      <input type="text" class="form-control bd-radius-0 bg-td-white" :disabled="true">
                    </div>
                  </td>
                </tr>
                <tr>
                  <td class="bg-light text-center" style="vertical-align: middle;">周　期</td>
                  <td style="vertical-align: middle;">
                    <p style="margin-bottom: 0px;">1/D ･･ 毎日/午前中</p>
                    <p style="margin-bottom: 0px;">1/W ･･ 毎週/月曜日</p>
                    <p style="margin-bottom: 0px;">1/M ･･ 毎月/第1稼働日</p>
                  </td>
                  <td id="completer" style="padding: 0px; vertical-align: middle;">
                    <div class="text-center bg-light" style="border: 1px solid #ced4da;padding: 0.375rem 0.75rem;">
                      <p style="margin-bottom: 0px;">完　了（承認）</p>
                      <p style="margin-bottom: 0px;">課長または係長</p>
                    </div>
                    <div class="input-group">
                      <input type="text" class="form-control bd-radius-0 bg-td-white" :disabled="true">
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
                  <th class="bg-light" style="width: 10%;"><div></div></th>
                  <th class="bg-light" style="width: 10%;"><div>工程</div></th>
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
                        <button type="button" @click="removeItem($event, index)" :data-index="index" class="btn btn-danger bg-delete"><i class="fa fa-times"></i></button>
                      </div>
                    </td>
                    <td class="dtfc-fixed-left" :style="styles.style2"><div>{{ item.process_id }}</div></td>
                    <td class="dtfc-fixed-left" :style="styles.style3">
                      <div class="form-group m-0">
                        <input type="text" name="inspection_item[]" v-on:keyup="changeBasicItem($event, 'inspection_item', index)" :data-index="index" :value="item.inspection_item" class="form-control" :id="`inputInspectionItems${index}`" :disabled="!item.edit">
                      </div>
                    </td>
                    <td class="dtfc-fixed-left" :style="styles.style4">
                      <div class="form-group m-0">
                        <input type="text" name="inspection_point[]" v-on:keyup="changeBasicItem($event, 'inspection_point', index)"  :data-index="index" :value="item.inspection_point" class="form-control" :id="`inputInspectionPoint${index}`" :disabled="!item.edit">
                      </div>
                    </td>
                    <td class="dtfc-fixed-left" :style="styles.style5">
                      <div class="form-group m-0">
                        <select name="inspection_period[]" v-on:change="changeBasicItem($event, 'inspection_period', index)" :data-index="index" class="form-control" :id="`inputPeriod${index}`" :disabled="!item.edit">
                          <option style="display: none;" value=""></option>
                          <option :selected="item.inspection_period === '1/D'" value="1/D">1/D</option>
                          <option :selected="item.inspection_period === '1/W'" value="1/W">1/W</option>
                          <option :selected="item.inspection_period === '1/M'" value="1/M">1/M</option>
                        </select>
                      </div>
                    </td>
                    <td class="dtfc-fixed-left" :style="styles.style6">
                      <div class="form-group m-0">
                        <select name="typeof_inspector[]" v-on:change="changeBasicItem($event, 'typeof_inspector', index)" :data-index="index" class="form-control" :id="`inputInspector${index}`" :disabled="!item.edit">
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
                        <button type="button" @click="addItem($event)" class="btn btn-success bg-add"><i class="fa fa-plus"></i></button>
                      </div>
                    </td>
                    <td class="dtfc-fixed-left" :style="styles.style2"><div></div></td>
                    <td class="dtfc-fixed-left" :style="styles.style3"><div></div></td>
                    <td class="dtfc-fixed-left" :style="styles.style4"><div></div></td>
                    <td class="dtfc-fixed-left" :style="styles.style5"><div></div></td>
                    <td class="dtfc-fixed-left" :style="styles.style6"><div></div></td>
                  </tr>
                </template>
                  <tr v-else class="text-center odd">
                    <td class="dtfc-fixed-left">
                      <div class="form-group m-0">
                        <button type="button" @click="initialItem($event)" class="btn btn-success bg-add"><i class="fa fa-plus-square"></i></button>
                      </div>
                    </td>
                    <td class="dtfc-fixed-left" :style="styles.style2"><div></div></td>
                    <td class="dtfc-fixed-left" :style="styles.style3"><div></div></td>
                    <td class="dtfc-fixed-left" :style="styles.style4"><div></div></td>
                    <td class="dtfc-fixed-left" :style="styles.style5"><div></div></td>
                    <td class="dtfc-fixed-left" :style="styles.style6"><div></div></td>
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
                      <th class="bg-light" v-for="i in 31"><div>{{ i }}</div></th>
                    </tr>
                  </thead>
                  <tbody id="body-equipment-inspection-22">
                    <template v-if="items && items.length">
                      <tr class="text-center even" v-for="(item, index) in items">
                        <td v-for="j in 31"><div></div></td>
                      </tr>
                      <tr class="text-center odd">
                        <td v-for="j in 31"><div></div></td>
                      </tr>
                    </template>
                      <tr v-else class="text-center odd">
                        <td v-for="j in 31"><div></div></td>
                      </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="card-footer">
        <button type="button" class="btn btn-success mr-1" id="modal-btn-submit" onclick="submitForm(event, this)">登録する</button>
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

export default {
  data() {
    return {
      basicSetID: '',
      dropzone: null,
      _token: '',
      link: '',
      uploadedImageUrl: '',
    }
  },
  setup(props) {
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

    const styles = ref(_styles);

    const showModalUpload = (event) => {
      $('#modal-xl-upload').modal('show');
    };
    const showModalDepartment = (event) => {
      $('#department-set').show();
      $('#line-set').hide();
      $('#basic-set').hide();
      $('#modal-xl-create').modal('show');
    };
    const showModalLine = (event) => {
      $('#line-set').show();
      $('#department-set').hide();
      $('#basic-set').hide();
      $('#modal-xl-create').modal('show');
    };

    const initialItem = (event) => {
      $('#basic-set').show();
      $('#department-set').hide();
      $('#line-set').hide();
      $('#modal-xl-create').modal('show');
    };

    const btnLineCode = (data, type) => {
      const {id, code, name} = data;
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

    const btnUploadFile = (data) => {
      const {img} = data;
      if (img) {
        base64_image.value = img;
      } else {
        base64_image.value = '';
      }
      $('#modal-xl-upload').modal('hide');
    };

    const btnProcess = (data) => {
      let el2;
      // for (var i = 2; i < 7; i++) {
      //   el2 = $('#head-equipment-inspection').find(`th:nth-child(${i})`);
      //   if (el2) {
      //     styles.value[`style${i}`] = `left: ${el2.css('left')}; position: sticky;`;
      //   }
      // }
      const {id, processId, dataItems} = data;
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

    const changeBasicItem = (event, inputName, index) => {
      var value = event.target.value;
      var dataItems = items.value;
      if (!dataItems[index]) {
        return;
      }
      dataItems[index][inputName] = value;
      items.value = dataItems;
    };

    const submitForm = (event) => {
      var _token = $('meta[name="csrf-token"]').attr('content');
      var id = $('#exampleInputID1').val();
      var code = $('#exampleInputCode1').val();
      var name = $('#exampleInputName1').val();
      var link = $('#app-equipment-inspection').attr('data-href');
      var data = {
        year: year.value, 
        month: month.value, 
        department_id: 
        department_id.value,
        department_name: department_name.value, 
        department_code: department_code.value,
        line_id: line_id.value, 
        line_code: line_code.value, 
        line_name: line_name.value,
        process_id: process_id.value, 
        basic_set_id: basic_set_id.value,
        // base64_image: base64_image.value,
        dataItems: items.value,
        _token,
        currentUrl: ref(props.currentUrl).value,
      };

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
            $('#res-message-equipment').html(makeMessage(data.status, data.message));
            document.getElementById("res-message-equipment").scrollIntoView();
            setTimeout(function(){ $('#res-message-equipment').html('') }, 5000);
            return;
          } else {
            $('#res-message-equipment').html(makeMessage(data.status, data.message));
            document.getElementById("res-message-equipment").scrollIntoView();
            setTimeout(function(){ $('#res-message-equipment').html('') }, 5000);
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

    return {
      year, month, department_id, department_name, department_code,
      line_id, line_code, line_name, process_id,
      basic_set_id, 
      // base64_image, 
      items,
      showModalUpload, showModalDepartment, showModalLine, initialItem,
      btnLineCode, btnUploadFile, btnProcess, addItem, removeItem,
      changeBasicItem, submitForm, styles,
    };
  },
  props: ['userId', 'currentUrl'],
  mounted() {
    console.log('Component mounted.');

    this._token = $('meta[name="csrf-token"]').attr('content');
    this._link = this.currentUrl;

    this.initializeDropzone();
  },
  methods: {
    initializeDropzone() {
      const _token = $('meta[name="csrf-token"]').attr('content');;
      const currentUrl = this.currentUrl;
      const userId = this.userId;

      const vm = this;

      console.log(currentUrl, userId)

      // Dropzone.autoDiscover = false;

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
          },
          success: function (file, response) {
            if (response.success) {
              const thumbnailElement = file.previewElement.querySelector(".dz-thumbnail");
              if (thumbnailElement) {
                thumbnailElement.remove();
              }
            }
          },
          removedfile: function(file) {
            $.ajax({
                url: "/temporary-upload/0",
                type: 'DELETE',
                success: function() {
                    //
                },
                data: {
                    file_name: file.name,
                    user_id: userId,
                    '_token': _token,
                },
            })
            file.previewElement.remove();
          },
        });
      }
    }
  },
  created() {
    console.log('Component created');
    const dropzoneScript = document.createElement("script");
    dropzoneScript.src = "https://unpkg.com/dropzone@5/dist/min/dropzone.min.js";

    const dropzoneStyle = document.createElement("style");
    dropzoneStyle.href = "https://unpkg.com/dropzone@5/dist/min/dropzone.min.css";
    dropzoneStyle.type = "text/css";
    dropzoneStyle.rel = "stylesheet";
  }
}
</script>

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