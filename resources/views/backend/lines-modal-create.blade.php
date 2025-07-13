<div class="row">
  <div class="col-12">
  	<div class="card card-default">
  		<div class="card-header">
		    <h3 class="card-title" id="modal-card-title" style="line-height: 1.8;">
		    </h3>
		    <div class="card-tools">
          <button type="button" data-dismiss="modal" aria-label="Close" class="btn btn-danger bg-colse">
          	<i class="fa fa-times"></i>
          </button>
		    </div>
		  </div>
      <!-- /.card-header -->
  		<div class="card-body">
  			<div id="res-message"></div>
  			<form id="quickForm" action="{{ route('lines.store') }}" method="post" accept-charset="utf-8" enctype="multipart/form-data">
  				<input type="hidden" name="_id" id="exampleInputID1">
          <div class="row">
	          <div class="col-md-6">
	          	<div class="form-group">
	                <label for="exampleInputCode1">ラインコード <span class="btn badge bg-orange">必須</span></label>
	                <input type="text" name="line_code" class="form-control{{ $errors->has('line_code') ? ' is-invalid' : '' }}" id="exampleInputCode1" value="{{ old('line_code') }}" required>
	                @if ($errors->has('line_code'))
	                    <span class="invalid-feedback">
	                        <strong>{{ $errors->first('line_code') }}</strong>
	                    </span>
	                @endif
	            </div>
	          </div>
	          <div class="col-md-6">
	          	<div class="form-group">
	                <label for="exampleInputName1">ラインコード <span class="btn badge bg-orange">必須</span></label>
	                <input type="text" name="line_name" class="form-control{{ $errors->has('line_name') ? ' is-invalid' : '' }}" id="exampleInputName1" value="{{ old('line_name') }}" required>
	                @if ($errors->has('line_name'))
	                    <span class="invalid-feedback">
	                        <strong>{{ $errors->first('line_name') }}</strong>
	                    </span>
	                @endif
	            </div>
	          </div>
	        </div>
         </form>
  		</div>
  		<div class="card-footer">
  			<button type="button" class="btn btn-primary mr-1" id="modal-btn-submit" onclick="submitForm(this)">
      	</button>
      	<button type="button" class="btn btn-danger" onclick="hideModal(this)">キャンセル</button>
  		</div>
  	</div>
  </div>
</div>
