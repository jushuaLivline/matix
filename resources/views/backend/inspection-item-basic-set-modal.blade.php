<div class="row">
  <div class="col-12">
  	<form id="quickForm" action="{{ route('inspection-item.store') }}" method="post" accept-charset="utf-8" enctype="multipart/form-data">
  	<div class="card card-default">
  		<div class="card-header bg-gray-1 card-header-basic-set">
		    <h3 class="card-title" id="modal-card-title" style="line-height: 1.8;">
		    </h3>
		    <div class="card-tools">
          <button type="button" data-dismiss="modal" aria-label="Close" class="modalCloseBtn js-modal-close">x</button>
		    </div>
		  </div>
      <!-- /.card-header -->
  		<div class="card-body bg-gray-1">
  			<div id="res-message"></div>
  			
  				<input type="hidden" name="_id" id="exampleInputID1">
  				<div class="card">
  					<div class="card-body">
  						<div class="row">
  							<div class="col-md-5">
			          	<div class="form-group">
			                <label class="form-label dotted indented" for="exampleInputBasicName1">点検項目基本セット名 <span class="btn badge bg-orange badge-require">必須</span></label>
			                <input type="text" name="basic_name" class="form-control{{ $errors->has('basic_name') ? ' is-invalid' : '' }}" id="exampleInputBasicName1" value="{{ old('basic_name') }}" required>
			                @if ($errors->has('basic_name'))
			                    <span class="invalid-feedback">
			                        <strong>{{ $errors->first('basic_name') }}</strong>
			                    </span>
			                @endif
			            </div>
			          </div>
  						</div>
  					</div>
  				</div>
  				<div class="card">
  					<div class="card-body">
							<div class="form-group row">
		            <div class="col-sm-10">
		              <table id="table-inspection-item" class="table table-bordered" style="border: none;">
		              	<thead>
		              		<tr class="bg-light">
			              		<th class="col-3" style="text-align: center;">点検項目</th>
			              		<th class="col-3" style="text-align: center;">点検ポイント（基準）</th>
			              		<th class="col-2" style="text-align: center;">周期</th>
			              		<th style="text-align: center;">点検者</th>
			              		<th  class="col-1"></th>
			              	</tr>
		              	</thead>
		              	<tbody>
		              	</tbody>
		              	<tfoot>
		              		<tr>
		              			<th colspan="5" style="border: none;">
		              				<div class="row float-right">
		              					<button onclick="makeDataItem(this)" type="button" class="btn btn-success btn-add-item bg-add">行を追加</button>
		              				</div>
		              			</th>
		              		</tr>
		              	</tfoot>
		              </table>
		            </div>
		          </div>
			      </div>
  				</div>
        
  		</div>
  		<div class="card-footer bg-gray-1">
  			<button type="button" class="btn btn-primary mr-1" id="modal-btn-submit" onclick="submitForm(this)">
      	</button>
      	<button type="button" class="btn btn-success btn-detail mr-1" id="modal-btn-detail" onclick="btnDetail(this)">編集する
      	</button>
      	<button id="btn-bacsic-set-cancel" type="button" class="btn btn-danger" onclick="hideModal(this)">キャンセル</button>
  		</div>
  	</div>
  	</form>
  </div>
</div>
