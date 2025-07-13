<template>
  <div class="container-equipment-inspection">
    <form class="form-horizontal" id="createForm" method="post" accept-charset="utf-8" enctype="multipart/form-data">
      <div class="card-body table-responsive">
        <div class="row">
          <div class="col-12">
            <div class="row">
              <div class="col-3" style="padding-right: 0px;">
                <table id="table-equipment-inspection-11" class="table table-bordered m-0" style="width: 100%;">
                  <thead id="head-equipment-inspection-11">
                    <tr class="text-thin text-center">
                      <th colspan="2" style="width: 6rem; vertical-align: middle;"><div class="text-bold">{{ dailyDate }}</div></th>
                      
                    </tr>
                    <tr class="text-thin text-center">
                      <th style="width: 6rem; vertical-align: middle;" colspan="2"><div class="text-bold">昼勤 Day</div></th>
                      
                    </tr>
                    <tr class="text-center">
                      <th class="th-cavas" colspan="2" style="vertical-align: middle; font-weight: bold; height: 250px;">可動率（％)</th>
                    </tr>
                  </thead>
                </table>
              </div>
              <div class="col-9" style="padding-left: 0px;">
                <table id="table-equipment-inspection-12" class="table table-bordered m-0" style="width: 100%;">
                  <thead id="head-equipment-inspection-12">
                    <tr class="text-thin text-center">
                      
                      <th colspan="2" style="width: 6rem; vertical-align: middle;"><div class="text-bold">日々生産管理表</div></th>
                      
                      <th class="bg-light" style="width: 4rem; vertical-align: middle;">
                        <div style="width: 4rem;">ラインNo.</div>
                      </th>
                      <th style="width: 4rem; vertical-align: middle;" class="bg-td-white text-center" @click="showModalLine($event)">
                        <div style="width: 4rem;" class="form-group m-0">
                          <input id="lineID" type="text" name="line_id" :value="line_id" class="form-control bg-td-white">
                          <p v-if="line_id" style="margin-bottom: 0px;">{{ `${line_name} (${line_code})` }}</p>
                        </div>
                      </th>
                      <th style="width: 4rem; vertical-align: middle;" class="bg-light">
                        <div style="width: 4rem;">品番</div>
                      </th>

                      <th style="width: 4rem; vertical-align: middle;" colspan="2" class="bg-td-white" @click="showModalProduct($event)">
                        <div class="form-group m-0">
                          <input id="productID" type="text" name="product_id" :value="product_id" class="form-control bg-td-white">
                          <p v-if="product_id" style="margin-bottom: 0px;">{{ `[${product_model}] ${product_short_name}` }}</p>
                        </div>
                      </th>
                      
                      <th style="width: 4rem; vertical-align: middle;" class="bg-light">
                        <div style="width: 4rem;">品名</div>
                      </th>
                      <th style="width: 4rem; vertical-align: middle;" colspan="2" class="bg-td-white">
                        <div class="form-group m-0">
                          <p v-if="product_id" style="margin-bottom: 0px;">{{ `${product_name}` }}</p>
                        </div>
                      </th>
                      
                      <th style="width: 4rem; vertical-align: middle;" class="bg-light">
                        <div style="width: 4rem;">職場名</div>
                      </th>
                      <th style="vertical-align: middle;" colspan="2" class="bg-td-white">
                        <div class="form-group m-0">
                          <!-- <input id="addressID" type="text" name="address_id" class="form-control bg-td-white"> -->
                          <!-- <p style="margin-bottom: 0px; display: none;">[300000] 本社工場</p> -->
                          <p v-if="product_id" style="margin-bottom: 0px;">{{ `${department_name}` }}</p>
                        </div>
                      </th>
                      <th v-for="i in (33-15)" style="display: none;"></th>
                    </tr>
                    <tr class="text-thin text-center">
                      
                      <th colspan="2" style="width: 6rem;">
                        <div v-if="false" style="width: 100%; vertical-align: middle;">
                          <button @click="changeModeComment" type="button" class="btn btn-primary bg-search">備考入力モード</button>
                        </div>
                      </th>
                      
                      <th style="width: 4rem; vertical-align: middle;" class="bg-light">
                        <div>C/T</div>
                      </th>
                      
                      <th style="width: 4rem; vertical-align: middle;" class="bg-td-white">
                        <div class="form-group text-center m-0">
                          <input style="color: #333333; border: none !important;" @blur="changeCtInput($event)" id="ctID" type="number" min="0" v-model="dailyDataTable.ct_id" name="ct_id" class="form-control bg-td-white" :disabled="true">
                        </div>
                      </th>
                      
                      <th colspan="9">
                        <div class="form-group row m-0">
                          <div class="col-sm-1 col-form-label pr-0">100%:</div>
                          <div class="col-sm-1 col-form-label pl-0"><span v-if="dailyDataTable.ct_id">{{ Math.round(3600 / parseInt(dailyDataTable.ct_id) * 1 * 100) / 100 }}</span></div>
                          <div class="col-sm-1 col-form-label pr-0">90%:</div>
                          <div class="col-sm-1 col-form-label pl-0"><span v-if="dailyDataTable.ct_id">{{ Math.round(3600 / parseInt(dailyDataTable.ct_id) * 0.9 * 100) / 100 }}</span></div>
                          <div class="col-sm-1 col-form-label pr-0">85%:</div>
                          <div class="col-sm-1 col-form-label pl-0"><span v-if="dailyDataTable.ct_id">{{ Math.round(3600 / parseInt(dailyDataTable.ct_id) * 0.85 * 100) / 100 }}</span></div>
                          <div class="col-sm-1 col-form-label pr-0">80%:</div>
                          <div class="col-sm-1 col-form-label pl-0"><span v-if="dailyDataTable.ct_id">{{ Math.round(3600 / parseInt(dailyDataTable.ct_id) * 0.8 * 100) / 100 }}</span></div>
                          <div class="col-sm-1 col-form-label pr-0">75%:</div>
                          <div class="col-sm-1 col-form-label pl-0"><span v-if="dailyDataTable.ct_id">{{ Math.round(3600 / parseInt(dailyDataTable.ct_id) * 0.75 * 100) / 100 }}</span></div>
                        </div>
                      </th>
                      <th v-for="i in (33-15)" style="display: none;"></th>
                    </tr>
                    <tr id="chartjs-line">
                      <th colspan="14">
                        <div class="chart">
                          <canvas id="lineChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                        </div>
                      </th>
                    </tr>
                  </thead>
                </table>
              </div>
            </div>

            <div class="row">
              <div class="col-3" style="padding-right: 0px;">
                <table id="table-equipment-inspection-21" class="table-s0 table table-bordered">
                  <thead id="head-equipment-inspection-21">
                    <tr class="text-thin text-center">
                      <th colspan="2" id="th-ctinput2" class="dtfc-fixed-left text-right bg-td-white width-2" style="padding-right: 1rem;"><div>C/T</div></th>
                    </tr>
                    <tr class="text-thin text-center">
                      <th colspan="2" id="th-input2" class="bg-td-white width-2"><div></div></th>
                    </tr>
                  </thead>
                  <tbody>

                    <template v-for="(row, index) in dataRow">
                    <template v-if="row.data && row.data.length">
                    <tr>
                      <td :rowspan="(row.data.length)" :id="`rowspan_${row.key}`" class="text-center bg-td-white"><div>{{ row.name}}</div></td>
                      <td class="text-center" :class="row.data[0].edit ? 'bg-td-white' : 'bg-computation'"><div v-if="row.data[0]">{{row.data[0].name}}</div></td>
                    </tr>
                    <tr v-for="j in (row.data.length - 1)" :id="`row_${row.key}${j}`">
                      <td class="text-center" :class="row.data[j].edit ? 'bg-td-white' : 'bg-computation'"><div>{{row.data[j].name}}</div></td>
                    </tr>
                    </template>
                    <tr v-else>
                      <td colspan="2" class="text-center" :class="row.key === 'man_hours_per_machine' ? 'bg-td-yellow' : (row.edit ? 'bg-td-white' : 'bg-computation')"><div>{{ row.name}} <a href="javascript:void(0)" v-if="row.key === 'remarks'" class="float-right btn  btn-sm btn-primary bg-search" @click="btnRemarkList">一覧</a></div></td>
                    </tr>
                    </template>
                  </tbody>
                </table>
              </div>
              <div class="col-9" style="padding-left: 0px;">
                <div class="table-s1">
                  <div class="table-s2">
                    <table id="table-equipment-inspection-22" class="table-s0 table-s3 table table-bordered">
                      <thead id="head-equipment-inspection-22">
                        <tr class="text-thin text-center">
                          <th v-for="i in 31" :class="dailyDataTable.arr_ct_input &&  dailyDataTable.arr_ct_input[i-1] ? checkCtLastConfirmDay(dailyDataTable.arr_ct_input[i-1]) : 'bg-td-white'"><div>{{ (dailyDataTable.arr_ct_input && dailyDataTable.arr_ct_input[i-1]) ? getCtInput(dailyDataTable.arr_ct_input[i-1]) : '' }}</div></th>
                        </tr>
                        
                        <tr class="text-thin text-center">
                          <th v-for="i in 31" :class="(dailyDataTable.arr_ct_input && dailyDataTable.arr_ct_input.length && dailyDataTable.arr_ct_input[i-1] && dailyDataTable.arr_ct_input[i-1].day && (dailyDataTable.arr_ct_input[i-1].day.indexOf('Sat') > -1 || dailyDataTable.arr_ct_input[i-1].day.indexOf('Sun') > -1) ) ? 'bg-td-pink': 'bg-light'"><div>{{ i }}</div></th>
                        </tr>
                      </thead>
                      <tbody>

                        <template v-for="(row, index) in dataRow">
                        <template v-if="row.data && row.data.length">
                        <tr>
                          <td v-for="i in 31" :class="row.data[0].edit ? (mode_comment ? 'bg-td-comment' : 'bg-td-green-important1') : 'bg-computation'"><p style="margin-bottom: 0px;">
                            <div :id="`comment${row.data[0].key}${i-1}`" :class="mode_comment ? 'mode-comment' : 'not-mode-comment'" v-if="(dailyDataTable[row.key] && dailyDataTable[row.key][row.data[0].key] && dailyDataTable[row.key][row.data[0].key][i-1] && dailyDataTable[row.key][row.data[0].key][i-1].comment)" :data-content="dailyDataTable[row.key][row.data[0].key][i-1].comment"><i class="far fa-comment-dots"></i></div>
                            <div v-if="(!mode_comment && row.data[0].edit && dailyDataTable[row.key] && dailyDataTable[row.key][row.data[0].key] && dailyDataTable[row.key][row.data[0].key][i-1] && dailyDataTable[row.key][row.data[0].key][i-1])" class="form-group text-center m-0"><input style="text-align: center; border: none !important; background: transparent; color: #333333;" :value="dailyDataTable[row.key][row.data[0].key][i-1].num_input" v-on:keyup="changeNumInput($event, row, row.data[0], i-1)" type="number" min="0" class="form-control bd-none bg-td-green" :disabled="true"></div>
                            <div style="text-align: center;" v-if="['number_of_prod'].includes(row.data[0].key)">{{ getAchievementPercent(i-1, row.data[0].key) }}</div>

                          </p></td>
                        </tr>
                        <tr v-for="j in (row.data.length - 1)" :id="`row_${row.key}${j}`">
                          <td v-for="i in 31" :class="row.data[j].edit ? (mode_comment ? 'bg-td-comment' : 'bg-td-green-important1') : 'bg-computation'"><p style="margin-bottom: 0px;">
                            <div :id="`comment${row.data[j].key}${i-1}`" :class="mode_comment ? 'mode-comment' : 'not-mode-comment'" v-if="(dailyDataTable[row.key] && dailyDataTable[row.key][row.data[j].key] && dailyDataTable[row.key][row.data[j].key][i-1] && dailyDataTable[row.key][row.data[j].key][i-1].comment)" tabindex="0" role="button" data-toggle="popover" data-trigger="focus" :data-content="dailyDataTable[row.key][row.data[j].key][i-1].comment"><i class="far fa-comment-dots"></i></div>
                            <div v-if="(!mode_comment && row.data[j].edit && dailyDataTable[row.key] && dailyDataTable[row.key][row.data[j].key] && dailyDataTable[row.key][row.data[j].key][i-1] && dailyDataTable[row.key][row.data[j].key][i-1])" class="form-group text-center m-0"><input style="text-align: center; border: none !important; background: transparent; color: #333333;" :value="dailyDataTable[row.key][row.data[j].key][i-1].num_input" v-on:keyup="changeNumInput($event, row, row.data[j], i-1)" type="number" min="0" class="form-control bd-none bg-td-green" :disabled="true"></div>
                            <div style="text-align: center;" v-if="['material_failure', 'judgment_rate', 'rate_of_addition'].includes(row.data[j].key)">{{ getAchievementPercent(i-1, row.data[j].key) }}</div>
                    
                          </p></td>
                        </tr>
                        </template>
                        <tr v-else class="colspan-2">
                          <td v-for="i in 31" :class="row.key === 'man_hours_per_machine' ? 'bg-td-yellow' : (row.edit ? (mode_comment && row.key !== 'remarks' ? 'bg-td-comment' : 'bg-td-green-important1') : 'bg-computation')"><p style="margin-bottom: 0px;">
                            <div :id="`comment${row.key}${i-1}`" :class="mode_comment ? 'mode-comment' : 'not-mode-comment'" v-if="(dailyDataTable[row.key] && dailyDataTable[row.key][i-1] && dailyDataTable[row.key][i-1].comment)" tabindex="0" role="button" data-toggle="popover" data-trigger="focus" :data-content="dailyDataTable[row.key][i-1].comment"><i class="far fa-comment-dots"></i></div>
                            <div v-if="(!mode_comment && row.key !== 'remarks' && row.edit && dailyDataTable[row.key] && dailyDataTable[row.key][i-1])" class="form-group text-center m-0"><input style="text-align: center; border: none !important; background: transparent; color: #333333;" :value="dailyDataTable[row.key][i-1].num_input" v-on:keyup="changeNumInput($event, null, row, i-1)" type="number" min="0" class="form-control bd-none bg-td-green" :disabled="true"></div>
                            <div style="text-align: center;" v-if="row.key === 'sum'">{{ getDeadTimeSum(i-1) }}</div>
                            <div style="text-align: center;" v-if="['operating_time_hours', 'actual_working_hours', 'availability', 'performance'].includes(row.key)">{{ getAchievementDeadTimePercent(i-1, row.key) }}</div>
                          </p></td>
                        </tr>
                        </template>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-3" style="padding-right: 0px;">
                <table v-if="line_id && dailyDataTable.machines && dailyDataTable.machines.length" id="table-equipment-inspection-31" class="table-s0 table table-bordered mt-3" style="width: 100%;" :set="totalUsed = getTotalUsed()">
                  <thead id="head-machine-31">
                    <tr class="text-thin text-center">
                      <th class="bg-light" style="width: 10%; border: none;"></th>
                      <th class="bg-light" style="width: 2%; border: none;"></th>
                    </tr>
                  </thead>
                  <tbody id="body-machine-31">
                    <tr class="text-center">
                      <td style="width: 10%;">予防保全</td>
                      <td class="bg-row-all" style="width: 2%;"></td>
                    </tr>
                    <tr class="text-center" v-if="max_total">
                      <td :rowspan="max_total" style="width: 10%;">刃物</td>
                      <td :class="`bg-row-0`" style="width: 2%;"></td>
                    </tr>
                    <tr v-if="max_total" class="text-center" v-for="i in (max_total - 1)">
                      <td style="display: none;"></td>
                      <td :class="`bg-row-${i}`" style="width: 2%;"></td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <div class="col-9" style="padding-left: 0px;">
                <div class="table-s1">
                  <div class="table-s2">
                    <table v-if="line_id && dailyDataTable.machines && dailyDataTable.machines.length" id="table-equipment-inspection-32" class="table-s0 table-s3 table table-bordered mt-3" style="width: 100%;" :set="totalUsed = getTotalUsed()">
                      <thead id="head-machine-32">
                        <tr class="text-thin text-center">
                          <template v-if="dailyDataTable.machines && dailyDataTable.machines.length">
                          <th class="bg-light" v-for="(machine, index) in dailyDataTable.machines">{{(machine.machine_name) ? `${machine.machine_number}: ${machine.machine_name}` :  `機番 ${machine.machine_number}` }}</th>
                          </template>
                        </tr>
                      </thead>
                      <tbody id="body-machine-32">
                        <tr class="tr-row-all text-center">
                          <template v-if="dailyDataTable.machines && dailyDataTable.machines.length">
                          <td :class="getTotalMachineUsed(machine, 'el')" style="width: 4rem;" v-for="(machine, index) in dailyDataTable.machines">{{ getTotalMachineUsed(machine, 'number') }}</td>
                          </template>
                        </tr>
                        <tr class="tr-row text-center" v-if="max_total">
                          <template v-for="(machine, index) in dailyDataTable.machines">
                            <td v-if="machine.json_data && machine.json_data[0]" @click="btnResetCutlery($event, machine.json_data[0], index, 0)" :class="getTotalMachineUsed(machine.json_data[0], 'c_el')">
                              <div style="width: unset;" v-if="machine.json_data && machine.json_data[0]" data-toggle="tooltip" data-placement="top" :title="`${machine.json_data[0].remarks}`">
                                {{ getTotalMachineUsed(machine.json_data[0], 'c_number') }}
                              </div>
                            </td>
                            <td v-else></td>
                          </template>
                        </tr>
                        <tr v-if="max_total" class="tr-row text-center" v-for="i in (max_total - 1)">
                          <template v-for="(machine, index) in dailyDataTable.machines">
                            <td v-if="machine.json_data && machine.json_data[i]" @click="btnResetCutlery($event, machine.json_data[i], index, i)" :class="getTotalMachineUsed(machine.json_data[i], 'c_el')">
                              <div style="width: unset;" v-if="machine.json_data && machine.json_data[i]" data-toggle="tooltip" data-placement="top" :title="`${machine.json_data[i].remarks}`">
                                {{ getTotalMachineUsed(machine.json_data[i], 'c_number') }}
                              </div>
                            </td>
                            <td v-else></td>
                          </template>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
                
          </div>
        </div>
      </div>
      
      <div v-if="false" class="card-footer" style="display: none;">
        <button type="button" class="btn btn-success mr-1" id="modal-btn-submit" @click="submitDailyForm('all')">登録する</button>
      </div>
    </form>
    <div class="modal fade" id="modal-xl-remark" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
      <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-body" id="modal-body-remark">
            <div class="card card-default">
              <div class="card-header bg-light">

                <div class="card-tools">
                  <button type="button" data-dismiss="modal" aria-label="Close" class="btn btn-danger bg-colse">
                    <i class="fa fa-times"></i>
                  </button>
                </div>
              </div>
              <div class="card-body table-responsive p-0" style="height: 300px;">
                <table class="table table-bordered table-head-fixed">
                  <tbody>
                    <template v-if="dailyDataTable.remarks && dailyDataTable.remarks.length"  v-for="(remark, index) in dailyDataTable.remarks">
                      <tr v-if="remark.comment">
                        <td class="text-center" width="10%">{{ `${(index + 1)}日` }}</td>
                        <td>{{remark.comment}}</td>
                      </tr>
                    </template>
                      
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
        <!-- /.modal-content -->
      </div>
      <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->
  </div>
</template>

<script>
import { ref } from "vue";

var oldCtInput;

// https://stackoverflow.com/questions/36685745/acceptable-range-highlighting-of-background-in-chart-js-2-0
// The original draw function for the line chart. This will be applied after we have drawn our highlight range (as a rectangle behind the line chart).
var originalLineDraw = Chart.controllers.line.prototype.draw;
// Extend the line chart, in order to override the draw function.
Chart.helpers.extend(Chart.controllers.line.prototype, {
  draw : function() {
    var chart = this.chart;
    // Get the object that determines the region to highlight.
    var yHighlightRange = chart.config.data.yHighlightRange;

    // If the object exists.
    if (yHighlightRange !== undefined) {
      var ctx = chart.chart.ctx;

      var yRangeBegin = yHighlightRange.begin;
      var yRangeEnd = yHighlightRange.end;

      var xaxis = chart.scales['x-axis-0'];
      var yaxis = chart.scales['y-axis-0'];

      var yRangeBeginPixel = yaxis.getPixelForValue(yRangeBegin);
      var yRangeEndPixel = yaxis.getPixelForValue(yRangeEnd);

      ctx.save();

      // The fill style of the rectangle we are about to fill.
      // ctx.fillStyle = 'rgba(0, 255, 0, 0.3)';
      ctx.fillStyle = '#D8D8D8';
      // Fill the rectangle that represents the highlight region. The parameters are the closest-to-starting-point pixel's x-coordinate,
      // the closest-to-starting-point pixel's y-coordinate, the width of the rectangle in pixels, and the height of the rectangle in pixels, respectively.
      ctx.fillRect(xaxis.left, Math.min(yRangeBeginPixel, yRangeEndPixel), xaxis.right - xaxis.left, Math.max(yRangeBeginPixel, yRangeEndPixel) - Math.min(yRangeBeginPixel, yRangeEndPixel));

      ctx.restore();
    }

    // Apply the original draw function for the line chart.
    originalLineDraw.apply(this, arguments);
  }
});
var lineChartOptions = {
  maintainAspectRatio : false,
  responsive : true,
  legend: {
    display: false
  },
  tooltips: {
    enabled: false
  },
  scales: {
    xAxes: [{
      gridLines : {
        display : true,
        offsetGridLines : true,
      }
    }],
    yAxes: [{
      ticks: {
          beginAtZero: true,
          min: 0,
          max: 100,
          stepSize: 5,
      },
      gridLines : {
        display : true,
        offsetGridLines : true,
      },
    }]
  },
  spanGaps: true, // this is the property I found
  options: {
    legend: {
      display: false,
      position: 'top',
      labels: {
        usePointStyle: true, // show legend as point instead of box
        fontSize: 10 // legend point size is based on fontsize
      }
    },
  },
};

const DATA_COUNT = 32;
const labels = [];
for (let i = 0; i < DATA_COUNT; ++i) {
  labels.push(i.toString());
}

var lineChartData = {
  labels: labels,
  datasets: [
    {
      label               : '可動率（％)',
      backgroundColor     : 'rgba(60,141,188,0.9)',
      borderColor         : 'rgba(60,141,188,0.8)',
      // pointRadius         : false,
      // pointColor          : '#3b8bba',
      // pointStrokeColor    : 'rgba(60,141,188,1)',
      // pointHighlightFill  : '#fff',
      // pointHighlightStroke: 'rgba(60,141,188,1)',
      data                : [],
      borderWidth: 2,
      lineTension: 0,
      /* point options */
      pointBorderColor: "blue", // blue point border
      pointBackgroundColor: "blue", // wite point fill
      pointBorderWidth: 1, // point border width
    },
  ],
  yHighlightRange : {
      begin: 82.5,
      end: 87.5,
    }
};
var lineChart;

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

const _dataKey = [
  {
    key: 'achievement', name: '実績', edit: false,
    data: [
      {key: 'number_of_prod', name: '生産数', edit: false},
      {key: 'finished_prod', name: '完成品', edit: true},
      {key: 'no_materials', name: '材不', edit: true},
      {key: 'material_failure', name: '材不率', edit: false},
      {key: 'judgment_prod', name: '判定品', edit: true},
      {key: 'add_or_remove', name: '加不', edit: true},
      {key: 'judgment_rate', name: '判定品率', edit: false},
      {key: 'rate_of_addition', name: '加不率', edit: false},
    ],
  },
  {
    key: 'deadtime', name: '不動時間', edit: false,
    data: [
      {key: 'ht15_activities', name: 'HT・15活動', edit: true},
      {key: 'tool_exchange', name: '刃具交換', edit: true},
      {key: 'step_change', name: '段替', edit: true},
      {key: 'short_stop', name: 'チョコ停', edit: true},
      {key: 'mechanical_failure', name: '機械故障', edit: true},
      {key: 'quality_trouble', name: '品質ﾄﾗﾌﾞﾙ', edit: true},
      {key: 'dimension_adjustment', name: '寸法調整', edit: true},
      {key: 'cleaning_endofwork', name: '掃除(作業終了時)', edit: true},
      {key: 'other_sudden', name: 'その他(突発)', edit: true},
    ],
  },
  { key: 'sum', name: '', edit: false},
  { key: 'actual_time_hours', name: '実績時間', edit: true},
  { key: 'total_time_hours', name: '総時間', edit: true},
  { key: 'operating_time_hours', name: '稼働時間', edit: false},
  { key: 'actual_working_hours', name: '実働時間', edit: false},
  { key: 'availability', name: '可動率', edit: false},
  { key: 'performance', name: 'ﾊﾟﾌｫｰﾏﾝｽ', edit: false},
  { key: 'man_hours_per_machine', name: '台当り工数', edit: false},
  { key: 'supervisor_confirmation', name: '監督者確認欄', edit: true},
  { key: 'remarks', name: '備考', edit: true},
];

const _dataValue = {
  ct_id: null, achievement: null,
  deadtime: null, actual_time_hours: null,
  man_hours_per_machine: null,
  remarks: null, supervisor_confirmation: null,
  total_time_hours: null, machines: null,
  arr_ct_input: null
}

export default {
  data() {
    return {
      basicSetID: '',
    }
  },
  setup() {
    const dataRow = ref(_dataKey);
    // console.log(dataRow.value);
    const max_total = ref(0);
    const total_used = ref(0);
    const mode_comment = ref(false);
    const dailyDate = ref(daily_date);
    const year = ref('');
    const month = ref('');
    const department_id = ref('');
    const department_name = ref('');
    const department_code = ref('');
    const product_id = ref('');
    const product_name = ref('');
    const product_code = ref('');
    const product_model = ref('');
    const product_short_name = ref('');
    const address_id = ref('');
    const address_name = ref('');
    const line_code = ref('');
    const line_name = ref('');
    const line_id = ref('');
    const process_id = ref('');
    const basic_set_id = ref('');
    const items = ref([]);
    // 
    const dailyDataTable = ref(_dataValue);
    // const machines = ref([]);
    // const ct_id = ref('');
    // const achievement = ref([]);
    // const deadtime = ref([]);
    // const actual_time_hours = ref([]);
    // const man_hours_per_machine = ref([]);
    // const remarks = ref([]);
    // const supervisor_confirmation = ref([]);
    // const total_time_hours = ref([]);
    // const arr_ct_input = ref([]);

    const styles = ref(_styles);

    const showModalUpload = (event) => {
      $('#modal-xl-upload').modal('show');
    };
    const showModalDepartment = (event) => {
      $('#department-set').show();
      $('#product-set').hide();
      $('#line-set').hide();
      $('#basic-set').hide();
      $('#comment-set').hide();
      $('#modal-xl-create').modal('show');
    };
    const showModalProduct = (event) => {
      return;
      $('#product-set').show();
      $('#department-set').hide();
      $('#line-set').hide();
      $('#basic-set').hide();
      $('#comment-set').hide();
      $('#modal-xl-create').modal('show');
    };
    const showModalLine = (event) => {
      return;
      $('#line-set').show();
      $('#product-set').hide();
      $('#department-set').hide();
      $('#basic-set').hide();
      $('#comment-set').hide();
      $('#modal-xl-create').modal('show');
    };
    const showModalComment = (event, rowP, row, index) => {
      var comment = '';
      var _dailyDataTable = dailyDataTable.value;
      if (row.key === 'remarks') {
        if (!(_dailyDataTable[row.key] && _dailyDataTable[row.key][index])) {
          return;
        } else {
          comment = _dailyDataTable[row.key][index].comment ? _dailyDataTable[row.key][index].comment : '';

        }
        if (!comment) {
          return;
        }
        // 
        // console.log(comment);
        $(`#comment${row.key}${index}`).popover({
          container: 'body',
          offset: 0,
          trigger: 'focus',
          content: comment,
        }).popover('toggle');
        // 
        return;
      }
      if (rowP && rowP.key) {
        if (!(_dailyDataTable[rowP.key] && _dailyDataTable[rowP.key][row.key]
          && _dailyDataTable[rowP.key][row.key][index]
        )) {
          return;
        } else {
          comment = _dailyDataTable[rowP.key][row.key][index].comment ? _dailyDataTable[rowP.key][row.key][index].comment : '';
          if (!comment) {
            return;
          }
          // 
          // console.log(comment);
          $(`#comment${row.key}${index}`).popover({
            container: 'body',
            offset: 0,
            trigger: 'focus',
            content: comment,
          }).popover('toggle');
          // 
          return;
        }
      } else {
        if (!(_dailyDataTable[row.key] && _dailyDataTable[row.key][index])) {
          return;
        } else {
          comment = _dailyDataTable[row.key][index].comment ? _dailyDataTable[row.key][index].comment : '';
          if (!comment) {
            return;
          }
          // 
          // console.log(comment);
          $(`#comment${row.key}${index}`).popover({
            container: 'body',
            offset: 0,
            trigger: 'focus',
            content: comment,
          }).popover('toggle');
          // 
          return;
        }
      }
      return;
    };

    const changeCtInput = (event) => {
      return;
      var inputVal = event.target.value;
      if (inputVal != oldCtInput) {
        submitDailyForm();
        oldCtInput = inputVal;
      }
    };
    const changeNumInput = (event, rowP, row, index) => {
      return;
      var inputVal = event.target.value;
      var oldId;
      if (rowP && rowP.key && dailyDataTable.value[rowP.key]
          && row.key && dailyDataTable.value[rowP.key][row.key]
          && dailyDataTable.value[rowP.key][row.key][index]
        ) {
          oldId = dailyDataTable.value[rowP.key][row.key][index].num_input;
          dailyDataTable.value[rowP.key][row.key][index].num_input = inputVal;
        } else if(
          row && row.key && dailyDataTable.value[row.key] && dailyDataTable.value[row.key][index]
        ) {
          oldId = dailyDataTable.value[row.key][index].num_input;
          dailyDataTable.value[row.key][index].num_input = inputVal;
        }
        if (oldId != inputVal) {
          setTimeout(() => {
              submitDailyForm();
          }, 3000);
        }
    };

    const checkCtLastConfirmDay = (ctInput) => {
      const currentDate = new Date();
      const currentDayOfMonth = currentDate.getDate();

      var confirmedNameIndex = null;
      var _dailyDataTable = dailyDataTable.value;
      var supervisor_confirmation = _dailyDataTable.supervisor_confirmation ? _dailyDataTable.supervisor_confirmation : null;
      if (supervisor_confirmation && supervisor_confirmation.length) {
        for (var i = 0; i < supervisor_confirmation.length; i++) {
          if (supervisor_confirmation[i].confirmed_name) {
            confirmedNameIndex = supervisor_confirmation[i].index;
          }
        }
      }
      // if (parseInt(ctInput.index) > confirmedNameIndex) {
      //   return 'bg-td-white';
      // }

      if (currentDayOfMonth > ctInput.index) {
        return 'bg-gray';
      }

      return 'bg-light';
    };

    const btnResetCutlery = (event, _item, index, i) => {
      var c_el = getTotalMachineUsed(_item, 'c_el');
      $('#basic-set').show();
      $('#basic-set').find('input[name=name_cutlery]').val('');
      $('#basic-set').find('input[name=remarks_cutlery]').val('');
      $('#basic-set').find('input[name=used_cutlery]').val('');
      $('#basic-set').find('input[name=number_of_uses_cutlery]').val('');
      $('#basic-set').attr('data-index', '');
      $('#basic-set').attr('data-i', '');
      $('#product-set').hide();
      $('#department-set').hide();
      $('#line-set').hide();
      $('#comment-set').hide();
      if (c_el !== 'bg-td-danger') {
        return;
      }
      $('#basic-set').find('input[name=name_cutlery]').val(_item.cutlery);
      $('#basic-set').find('input[name=remarks_cutlery]').val(_item.remarks);
      $('#basic-set').find('input[name=used_cutlery]').val(_item.total_used);
      $('#basic-set').find('input[name=number_of_uses_cutlery]').val(_item.number_of_uses);
      $('#basic-set').attr('data-index', index);
      $('#basic-set').attr('data-i', i);
      $('#modal-xl-create').modal('show');
    };

    const btnLineCode = (data, type) => {
      const {id, code, name} = data;
      var oldId;
      if (type === 'lines') {
        oldId = line_id.value;
        if (id) {
          line_id.value = id;
          line_code.value = code;
          line_name.value = name;
        } else {
          line_id.value = '';
          line_code.value = '';
          line_name.value = '';
        }
        if (oldId != id) {
          submitDailyForm();
        }
      } else if (type === 'product') {
        oldId = product_id.value;
        if (id) {
          product_id.value = id;
          product_name.value = name;
          product_code.value = code;
          product_model.value = data.model;
          product_short_name.value = data.short_name;
          department_name.value = data.department_name;
        } else {
          product_id.value = '';
          product_name.value = '';
          product_code.value = '';
          product_model.value = '';
          product_short_name.value = '';
          department_name.value = '';
        }
        if (oldId != id) {
          submitDailyForm();
        }
      } else if (type === 'comment') {
        if (data.p_code && dailyDataTable.value[data.p_code]
          && data.code && dailyDataTable.value[data.p_code][data.code]
          && dailyDataTable.value[data.p_code][data.code][data.index]
        ) {
          oldId = dailyDataTable.value[data.p_code][data.code][data.index].comment;
          if (data.comment) {
            dailyDataTable.value[data.p_code][data.code][data.index].comment = data.comment;
          } else {
            dailyDataTable.value[data.p_code][data.code][data.index].comment = '';
          }
          if (oldId !== data.comment) {
            submitDailyForm();
          }
        } else if(
          data.code && dailyDataTable.value[data.code] && dailyDataTable.value[data.code][data.index]
        ) {
          oldId = dailyDataTable.value[data.code][data.index].comment;
          if (data.comment) {
            dailyDataTable.value[data.code][data.index].comment = data.comment;
          } else {
            dailyDataTable.value[data.code][data.index].comment = '';
          }
          if (oldId !== data.comment) {
            submitDailyForm();
          }
        }
      } else if (type === 'resetCutlery') {
        // console.log(data);
        var {index, i, name_cutlery, remarks_cutlery, used_cutlery, number_of_uses_cutlery} = data;
        if (dailyDataTable.value.machines && dailyDataTable.value.machines[index] && dailyDataTable.value.machines[index].json_data && dailyDataTable.value.machines[index].json_data[i]) {
          dailyDataTable.value.machines[index].json_data[i].cutlery = name_cutlery;
          dailyDataTable.value.machines[index].json_data[i].number_of_uses = parseInt(number_of_uses_cutlery);
          dailyDataTable.value.machines[index].json_data[i].total_used = parseInt(used_cutlery);
          dailyDataTable.value.machines[index].json_data[i].remarks = remarks_cutlery;
          setTimeout(() => { submitDailyForm(); }, 1000);
        }
      }
      $('#modal-xl-create').modal('hide');
    };

    const btnProcess = (data) => {
      let el2;
      for (var i = 2; i < 7; i++) {
        el2 = $('#head-equipment-inspection').find(`th:nth-child(${i})`);
        if (el2) {
          styles.value[`style${i}`] = `left: ${el2.css('left')}; position: sticky;`;
        }
      }
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

    const submitDailyForm = (_type) => {
      var _token = $('meta[name="csrf-token"]').attr('content');
      var link = $('#app-daily-production').attr('data-href');
      var data = {
        daily_production_control_id,
        year: _year,
        month: _month,
        ct_input: dailyDataTable.value.ct_id,
        line_id: line_id.value, line_code: line_code.value, line_name: line_name.value,
        product_id: product_id.value, product_name: product_name.value,
        product_code: product_code.value, product_model: product_model.value, department_name: department_name.value,
        product_short_name: product_short_name.value,
        address_id: address_id.value, address_name: address_name.value,
        dailyDataTable: dailyDataTable.value,
        _type,
        _token,
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
            if (data.data && data.data.refesh_line_machine) {
              if (data.data.id) {
                daily_production_control_id = data.data.id;
              }
              _initialDataTable();
            }
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
    const initialDataTable = (initial) => {
      // ct_id, achievement, deadtime, actual_time_hours, man_hours_per_machine,
      // remarks, supervisor_confirmation, total_time_hours, machines, arr_ct_input,
      year.value = initial.json_data.year ?? '';
      month.value = initial.json_data.month ?? '';
      line_id.value = initial.json_data.line_id ?? '';
      line_code.value = initial.json_data.line_code ?? '';
      line_name.value = initial.json_data.line_name ?? '';
      product_id.value = initial.json_data.product_id ?? '';
      product_name.value = initial.json_data.product_name ?? '';
      product_code.value = initial.json_data.product_code ?? '';
      product_model.value = initial.json_data.product_model ?? '';
      product_short_name.value = initial.json_data.product_short_name ?? '';
      department_name.value = initial.json_data.product_department_name ?? '';
      var _machines = [];
      if (initial.json_data && initial.json_data.machines && initial.json_data.machines.length) {
        for (var i = 0; i < initial.json_data.machines.length; i++) {
          initial.json_data.machines[i]['edit'] = false;
          _machines.push(Object.create(initial.json_data.machines[i]));
        }
        dailyDataTable.value.machines = _machines;
        max_total.value = getTotalCutlery(_machines);
      } else {
        dailyDataTable.value.machines = null;
        max_total.value = 0;
      }
      if (initial.json_data && initial.json_data.ct_id && initial.json_data.ct_id) {
        dailyDataTable.value.ct_id = initial.json_data.ct_id;
        oldCtInput = initial.json_data.ct_id;
      } else {
        oldCtInput = '';
      }
      if (initial.json_data && initial.json_data.achievement && initial.json_data.achievement) {
        dailyDataTable.value.achievement = initial.json_data.achievement;
      }
      if (initial.json_data && initial.json_data.deadtime && initial.json_data.deadtime) {
        dailyDataTable.value.deadtime = initial.json_data.deadtime;
      }
      if (initial.json_data && initial.json_data.actual_time_hours && initial.json_data.actual_time_hours) {
        dailyDataTable.value.actual_time_hours = initial.json_data.actual_time_hours;
      }
      if (initial.json_data && initial.json_data.man_hours_per_machine && initial.json_data.man_hours_per_machine) {
        dailyDataTable.value.man_hours_per_machine = initial.json_data.man_hours_per_machine;
      }
      if (initial.json_data && initial.json_data.remarks && initial.json_data.remarks) {
        dailyDataTable.value.remarks = initial.json_data.remarks;
      }
      if (initial.json_data && initial.json_data.supervisor_confirmation && initial.json_data.supervisor_confirmation) {
        dailyDataTable.value.supervisor_confirmation = initial.json_data.supervisor_confirmation;
      }
      if (initial.json_data && initial.json_data.total_time_hours && initial.json_data.total_time_hours) {
        dailyDataTable.value.total_time_hours = initial.json_data.total_time_hours;
      }
      if (initial.json_data && initial.json_data.arr_ct_input && initial.json_data.arr_ct_input) {
        dailyDataTable.value.arr_ct_input = initial.json_data.arr_ct_input;
      }
    };

    const getTotalCutlery = (machines) => {
      var total = 0;
      for (var i = 0; i < machines.length; i++) {
        var cutlerys = machines[i].json_data;
        if (cutlerys && cutlerys.length) {
          total = (cutlerys.length > total) ? cutlerys.length : total;
        }
      }
      // console.log(total);
      return total;
    };

    const changeModeComment = () => {
      mode_comment.value = !mode_comment.value;
    };
    const checkOldDay = (day) => {
      if (!day) {
        return false;
      }
      var new_day = '';
      const arrDay = day.split('-');
      if (arrDay.length >=3) {
        new_day = `${arrDay[0]}-${arrDay[1]}-${arrDay[2]}`;
      }
      if (!new_day) {
        return false;
      }
      var d1 = new Date();
      var d2 = new Date(new_day);
      var check = d1.getTime() > d2.getTime();
      return check;
    };

    const getCtInput = (item) => {
      return item.ct_input;
      return item.num_input ? item.num_input : dailyDataTable.value.ct_id;
      if (checkOldDay(item.day)) {
        return item.num_input;
      } else {
        return item.num_input ? item.num_input : dailyDataTable.value.ct_id;
      }
    };

    const getDeadTimeSum = (index) => {
      // =BW19+BW20+BW21+BW22+BW23+BW24+BW25+BW26
      var total = 0;
      var _deadTime = dailyDataTable.value.deadtime;
      var data;
      if (_deadTime) {
        for (var k in _deadTime) {
            if (_deadTime.hasOwnProperty(k)) {
              if (k !== 'other_sudden') {
                total += (_deadTime[k][index] && _deadTime[k][index].num_input) ? parseInt(_deadTime[k][index].num_input) : 0;
              }
            }
        }
      }
      return total ? total : '';
    };

    const getAchievementPercent = (index, _type) => {
      var total = 0;
      var arr = {};
      var _achievement = dailyDataTable.value.achievement;
      if (_achievement) {
        for (var k in _achievement) {
            if (_achievement.hasOwnProperty(k)) {
               arr[k] = (_achievement[k][index] && _achievement[k][index].num_input) ? parseInt(_achievement[k][index].num_input) : 0;
            }
        }
      }
      if (_type === 'material_failure') {
        // =BQ13/BQ11
        if (!arr.number_of_prod) {
          return '';
        }
        if (arr.number_of_prod <= 0) {
          return '';
        }
        if (arr.no_materials <= 0) {
          return '';
        }
        // total = arr.finished_prod / arr.number_of_prod;
        total = arr.no_materials / arr.number_of_prod;
        total = parseFloat(total * 100).toFixed(2);
        return total ? `${total}%` : '';
      }else if (_type === 'judgment_rate') {
        if (!arr.number_of_prod) {
          return '';
        }
        if (arr.number_of_prod <= 0) {
          return '';
        }
        total = arr.judgment_prod / arr.number_of_prod;
        total = parseFloat(total * 100).toFixed(2);
        return total ? `${total}%` : '';
      }else if (_type === 'rate_of_addition') {
        if (!arr.number_of_prod) {
          return '';
        }
        if (arr.number_of_prod <= 0) {
          return '';
        }
        total = arr.add_or_remove / arr.number_of_prod;
        total = parseFloat(total * 100).toFixed(2);
        return total ? `${total}%` : '';
      } else if (_type === 'number_of_prod') {
        // =BQ12+BQ13+BQ15
        total = arr.finished_prod + arr.no_materials + arr.judgment_prod;
        if (dailyDataTable.value.achievement && dailyDataTable.value.achievement.number_of_prod[index]) {
          dailyDataTable.value.achievement.number_of_prod[index].num_input = total;
        }
        total = Math.round(total * 100) / 100;
        return total ? `${total}` : '';
      }
      return total ? total : '';
    };

    const getAchievementDeadTimePercent = (index, _type) => {
      var total = 0;
      var arr = {};
      var _achievement = dailyDataTable.value.achievement;
      var _deadTime = dailyDataTable.value.deadtime;
      var _actual_time_hours = dailyDataTable.value.actual_time_hours;
      var _total_time_hours = dailyDataTable.value.total_time_hours;
      var _ctInput =  (dailyDataTable.value.arr_ct_input && dailyDataTable.value.arr_ct_input[index] && dailyDataTable.value.arr_ct_input[index].ct_input) ? parseInt(dailyDataTable.value.arr_ct_input[index].ct_input) : 0;
      if (_achievement) {
        for (var k in _achievement) {
            if (_achievement.hasOwnProperty(k)) {
               arr[k] = (_achievement[k][index] && _achievement[k][index].num_input) ? parseInt(_achievement[k][index].num_input) : 0;
            }
        }
      }
      if (_deadTime) {
        for (var k in _deadTime) {
            if (_deadTime.hasOwnProperty(k)) {
               arr[k] = (_deadTime[k][index] && _deadTime[k][index].num_input) ? parseInt(_deadTime[k][index].num_input) : 0;
            }
        }
      }
      if (_actual_time_hours && _actual_time_hours.length) {
        arr['actual_time_hours'] = (_actual_time_hours[index] && _actual_time_hours[index].num_input) ? parseFloat(_actual_time_hours[index].num_input) : 0;
      }
      if (_total_time_hours && _total_time_hours.length) {
        arr['total_time_hours'] = (_total_time_hours[index] && _total_time_hours[index].num_input) ? parseFloat(_total_time_hours[index].num_input) : 0;
      }
      if (_type === 'operating_time_hours') {
        // =ROUNDUP((BQ29*60-BQ19-BQ22-BQ25)/60,2)
        // =ROUNDUP((BW29*60-BW19-BW21-BW26-BW27)/60,2)
        total = getOperatingTimeHours(arr);
        total = total ? roundUp(Number(total), 2) : '';
        return total ? `${total}` : '';
      }else if (_type === 'actual_working_hours') {
        // =ROUNDUP((BQ29*60-BQ19-BQ20-BQ21-BQ22-BQ23-BQ24-BQ25)/60,2)
        total = getActualWorkingHours(arr);
        total = total ? roundUp(Number(total), 2) : '';
        return total ? `${total}` : '';
      }else if (_type === 'availability') {
        // =BQ11/BQ31/(3600/BQ9)
        // var _ctInput = dailyDataTable.value.ct_id ? parseInt(dailyDataTable.value.ct_id) : 0;
        var _bq31 = getOperatingTimeHours(arr);
        _bq31 = _bq31 ? roundUp(Number(_bq31), 2) : 0;
        if (_bq31 == 0) {
          return '';
        }
        if (_ctInput <= 0) {
          return '';
        }
        var _bq9 = Math.round(3600/_ctInput);
        if (_bq9 <= 0) {
          return '';
        }
        total = (arr.number_of_prod / _bq31) / _bq9;
        total = total ? Number(parseFloat(total * 100).toFixed(2)) : '';
        if (lineChart && total && total <= 100) {
          lineChart.data.datasets[0].data[index + 1] = (total) ? roundToQuarterPercentage(total) : 0;
          // lineChart.data.datasets[0].data = [0, 35, 48, 40, 19, 85, 27, 90];
          // animate update of 'March' from 90 to 50.
          lineChart.update();
        }
        return total ? `${total}%` : '';
      }else if (_type === 'performance') {
        // =BQ11/BQ32/(3600/BQ9)
        // var _ctInput = dailyDataTable.value.ct_id ? parseInt(dailyDataTable.value.ct_id) : 0;
        var _bq32 = getActualWorkingHours(arr);
        _bq32 = _bq32 ? roundUp(Number(_bq32), 2) : 0;
        if (_bq32 == 0) {
          return '';
        }
        if (_ctInput <= 0) {
          return '';
        }
        var _bq9 = Math.round(3600/_ctInput);
        if (_bq9 <= 0) {
          return '';
        }
        total = (arr.number_of_prod / _bq32) / _bq9;
        total = total ? Number(parseFloat(total * 100).toFixed(2)) : '';
        return total ? `${total}%` : '';
      }
      return total ? total : '';
    };

    const roundUp = (value, index) => {
      if (value <= 0) {
        return value;
      }
      var _ind = 1;
      for (var i = 0; i < index; i++) {
        _ind = _ind * 10;
      }
      var value1 = Number(parseFloat(value).toFixed(index));
      if (value > value1) {
        return value1 + 1/_ind;
      } else {
        return value1;
      }
    };

    const getOperatingTimeHours = (arr) => {
      // =ROUNDUP((BQ29*60-BQ19-BQ22-BQ25)/60,2)
      // return (arr.actual_time_hours  * 60 - arr.ht15_activities - arr.short_stop - arr.dimension_adjustment) / 60;
      // =ROUNDUP((BW29*60-BW19-BW21-BW26-BW27)/60,2)
      var _data = (Number(arr.actual_time_hours)  * 60 - Number(arr.ht15_activities) - Number(arr.step_change) - Number(arr.cleaning_endofwork) - Number(arr.other_sudden));
      return _data / 60;
    };
    const getActualWorkingHours = (arr) => {
      // =ROUNDUP((BT29*60-BT19-BT20-BT21-BT22-BT23-BT24-BT25)/60,2)
      // return (arr.actual_time_hours  * 60 - arr.ht15_activities - arr.tool_exchange - arr.step_change - arr.short_stop - arr.mechanical_failure - arr.quality_trouble - arr.dimension_adjustment) / 60;
      // =ROUNDUP((BW29*60-BW19-BW20-BW21-BW22-BW23-BW24-BW25-BW26-BW27)/60,2)
      var _data = (Number(arr.actual_time_hours)  * 60 - Number(arr.ht15_activities) - Number(arr.tool_exchange) - Number(arr.step_change) - Number(arr.short_stop) - Number(arr.mechanical_failure) - Number(arr.quality_trouble) - Number(arr.dimension_adjustment) - Number(arr.cleaning_endofwork) - Number(arr.other_sudden));
      return _data / 60;
    };

    const getTotalUsed = () => {
      var total = 0;
      var _dailyDataTable = dailyDataTable.value;
      var _achievement = _dailyDataTable.achievement ? _dailyDataTable.achievement : null;
      var number_of_prod = _achievement.number_of_prod ? _achievement.number_of_prod : null;
      if (number_of_prod && number_of_prod.length) {
        for (var i = 0; i < number_of_prod.length; i++) {
          total += number_of_prod[i].num_input ? parseInt(number_of_prod[i].num_input) : 0;
        }
      }
      total_used.value = total;
      return total;
    };

    const getTotalMachineUsed = (_machine, _type) => {
      if (_type === 'el') {
        if (_machine && _machine.total_used && parseInt(_machine.total_used)/parseInt(_machine.number_of_maintenance) >= 0.99) {
          return 'bg-td-danger';
        } else if(_machine &&  _machine.total_used && parseInt(_machine.total_used)/parseInt(_machine.number_of_maintenance) >= 0.9) {
          return 'bg-td-warning';
        }
        return '';
      } else if (_type === 'number') {
        return _machine && _machine.total_used ? `${_machine.total_used}/${_machine.number_of_maintenance}` :  `0/${_machine.number_of_maintenance}`;
      }else if (_type === 'c_el') {
        if (_machine && _machine.total_used && parseInt(_machine.total_used)/parseInt(_machine.number_of_uses) >= 0.99) {
          return 'bg-td-danger';
        } else if(_machine && _machine.total_used && parseInt(_machine.total_used)/parseInt(_machine.number_of_uses) >= 0.9) {
          return 'bg-td-warning';
        }
        return '';
      } else if (_type === 'c_number') {
        return _machine && _machine && _machine.total_used ? `${_machine.cutlery} (${_machine.total_used}/${_machine.number_of_uses})` :  `${_machine.cutlery} (0/${_machine.number_of_uses})`;
      }
      return '';
    };

    const btnRemarkList = () => {
      $('#modal-xl-remark').modal('show');
    };

    const roundToQuarterPercentage = (inputPercent) => {
      return Math.round(inputPercent / 5) * 5;
    };

    return {
      year, month, department_id, department_name, department_code,
      line_id, line_code, line_name, process_id,
      basic_set_id, items,
      showModalUpload, showModalDepartment, showModalProduct, showModalLine, btnResetCutlery,
      btnLineCode, btnProcess, addItem, removeItem,
      changeBasicItem, submitDailyForm, styles, mode_comment, dailyDate,
      initialDataTable, max_total, dataRow, changeModeComment, showModalComment,
      product_id, product_name, product_code, product_model, product_short_name,
      // ct_id, achievement, deadtime, actual_time_hours, man_hours_per_machine,
      // remarks, supervisor_confirmation, total_time_hours, machines, arr_ct_input,
      dailyDataTable, checkOldDay, getCtInput, getDeadTimeSum, changeNumInput,
      getAchievementPercent, changeCtInput, total_used, getTotalUsed,
      btnRemarkList, getAchievementDeadTimePercent, checkCtLastConfirmDay,
      getTotalMachineUsed,
    };
  },
  mounted() {
    lineChartData.datasets[0].fill = false;
    lineChartOptions.datasetFill = false;
    var lineChartCanvas = $('#lineChart').get(0).getContext('2d');
    lineChart = new Chart(lineChartCanvas, {
      type: 'line',
      data: lineChartData,
      options: lineChartOptions,
    });
    setTimeout(() => {
      $(function () {
        $('[data-toggle="tooltip"]').tooltip();
        $('[data-toggle="popover"]').popover();
      });
    }, 1000);
    
    console.log('Component mounted.');
  },
  created() {
    console.log('Component created');
  }
}
</script>
<style scoped>
.not-mode-comment{
  width: 0px !important;
  height: 0px;
  font-size: 13px;
}
</style>