@extends('backend.layouts.app')
@section('content')
@php
    $block1 = 'block';
    $block2 = 'block';
    $block3 = 'none';
    $block4 = 'none';

    if($errors->has('name') OR $errors->has('phone') OR $errors->has('birthday')) {
	    $block1 = 'none';
	    $block3 = 'block';
	}
    if($errors->has('current_password') OR $errors->has('new_password') OR $errors->has('new_password_confirmation')) {
	    $block2 = 'none';
        $block4 = 'block';
	}
@endphp
<!-- SELECT2 EXAMPLE -->
<div class="card card-default">
  <div class="card-header">
    <h3 class="card-title">Thông tin hồ sơ</h3>

    <div class="card-tools">
      <button type="button" class="btn btn-tool" data-card-widget="collapse">
        <i class="fas fa-minus"></i>
      </button>
    </div>
  </div>
  <!-- /.card-header -->
  <div class="card-body">
    <div class="row">
      <div class="col-md-6">
          <div id="edit-1" style="display: {{ $block1 }};">
              <div class="box-content">
                  <div class="box-body box-profile">
                  		<div class="text-center">
                      	<img class="profile-user-img img-fluid img-circle" src="{{ $user->getAvatarUrl() }}" alt="" style="width: 100px; height: 100px">
                  		</div>
                      <h3 class="profile-username text-center">{{ $user->name}}</h3>
                      <ul class="list-group list-group-unbordered mb-3">
                          <li class="list-group-item">
                              <b>Address:</b> <a class="float-right">{{ $user->address}}</a>
                          </li>
                          <li class="list-group-item">
                              <b>Email:</b> <a class="float-right">{{ $user->email}}</a>
                          </li>
                          <li class="list-group-item">
                              <b>Phone:</b> <a class="float-right">{{ $user->phone}}</a>
                          </li>
                          <li class="list-group-item">
                              <b>Birthday:</b> <a class="float-right">
                                  @if($user->birthday != null)
                                  {{date('m/d/Y', strtotime($user->birthday))}}
                                  @else
                                  {{ $user->birthday}}
                                  @endif
                              </a>
                          </li>
                          <li class="list-group-item">
                              <b>Gender:</b> <a class="float-right">
                              	@php
                              		$arrGender = [1 => 'Male', 2 => 'Female'];
                              		$strGender = isset($arrGender[$user->gender]) ? $arrGender[$user->gender] : '';
                              	@endphp
                              	{{$strGender}}
                              </a>
                          </li>
                      </ul>
                      <a id="edit-2" href="javascript:void(0)" class="btn btn-primary" onclick="editInfo(); return false"><i class="fa fa-edit"></i> Edit info</a>
                  </div>
              </div>
          </div>
          <div id="edit-3" style="display: {{ $block3 }};">
            <form action=" {{ route('users.profile')}}"  enctype="multipart/form-data" method="post" accept-charset="utf-8">
                {{ csrf_field() }}
                {{ method_field('PUT') }}
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" id="name" name="name"  value="{{$user->name}}">
                    @if ($errors->has('name'))
                    <span class="invalid-feedback">
                      	{{ $errors->first('name') }}
                    </span>
                    @endif
                </div>
                <div class="form-group">
                    <label for="address">Address</label>
                    <input type="text" class="form-control" id="address" name="address"  value="{{$user->address}}">
                </div>
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="text" class="form-control{{ $errors->has('phone') ? ' is-invalid' : '' }}" id="phone" name="phone"  value="{{$user->phone}}">
                    @if ($errors->has('phone'))
                    <span class="invalid-feedback">
                        {{ $errors->first('phone') }}
                    </span>
                    @endif
                </div>
                <div class="form-group">
                    <label for="birthday">Birthday</label>
                    <input  type="date" class="form-control{{ $errors->has('birthday') ? ' is-invalid' : '' }}" id="birthday" name="birthday"  value="{{ $user->birthday}}">
                    @if ($errors->has('birthday'))
                    <span class="invalid-feedback">
                        {{ $errors->first('birthday') }}
                    </span>
                    @endif
                </div>
                <div class="form-group">
                    <label for="gender">Gender</label><br>
                    @php
                    	$checked1 = ($user->gender == 1) ? ' checked' : '';
                    	$checked2 = ($user->gender == 2) ? ' checked' : '';
                    @endphp
                    <div class="custom-control custom-radio d-inline mr-3">
                      <input class="custom-control-input" type="radio" id="gender1" name="gender" value="1"{{$checked1}}>
                      <label for="gender1" class="custom-control-label">Nam</label>
                    </div>
                    <div class="custom-control custom-radio d-inline">
                      <input class="custom-control-input custom-control-input-danger" type="radio" id="gender2" name="gender" value="2"{{$checked2}}>
                      <label for="gender2" class="custom-control-label">Nữ</label>
                    </div>
                </div>
                <div class="form-group">
                    <input type="file" name="avatar" class="form-control{{ $errors->has('avatar') ? ' is-invalid' : '' }}">
                    @if ($errors->has('avatar'))
                    <span class="invalid-feedback">
                        {{ $errors->first('avatar') }}
                    </span>
                    @endif
                </div>
                <button class="btn btn-success">Submit</button>
                <a href="javascript:void(0)" title="Cancel" class="btn btn-danger" onclick="exit();" style="color: #ffff;">Cancel</a>
            </form>
        </div>
      </div>
      <!-- /.col -->
      <div class="col-md-6">
          <div style="display: {{$block2}}" id="pass">
              <a class="btn btn-warning" href="javascript:void(0)" onclick="viewForm(); return false"><i class="fa fa-edit"></i> Edit password</a>
          </div>

          <div class="form_pass" id="form_pass" style="display: {{$block4}};">
              <form method="post" action="{{route('users.password') }}">
                  {{ csrf_field() }}
                  {{ method_field('PUT') }}
                  <div class="form-group">
                      <label for="password_old">Password current</label>
                      <input type="password" class="form-control{{ $errors->has('current_password') ? ' is-invalid' : '' }}" id="password_old" placeholder="Enter password current" name="current_password" autocomplete="off">
                      @if ($errors->has('current_password'))
                      <span class="invalid-feedback">
                          {{ $errors->first('current_password') }}
                      </span>
                      @endif
                  </div>
                  <div class="form-group">
                      <label>Password new</label>
                      <input type="password" class="form-control{{ $errors->has('new_password') ? ' is-invalid' : '' }}" placeholder="Enter password new" name="new_password" autocomplete="off"/>
                      @if ($errors->has('new_password'))
                      <span class="invalid-feedback">
                          {{ $errors->first('new_password') }}
                      </span>
                      @endif
                  </div>
                  <div class="form-group">
                  	<label>Password confirm</label>
                      <input type="password" class="form-control{{ $errors->has('new_password_confirmation') ? ' is-invalid' : '' }}" placeholder="Enter password confirm" name="new_password_confirmation" autocomplete="off"/>
                      @if ($errors->has('new_password_confirmation'))
                      <span class="invalid-feedback">
                          {{ $errors->first('new_password_confirmation') }}
                      </span>
                      @endif
                  </div>
                  <button  class="btn btn-success">Submit</button>
                  <a href="javascript:void(0)" title="" onclick="exit1();" class="btn btn-danger">cancel</a>
              </form>
          </div>
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
  </div>
  <!-- /.card-body -->
</div>
<!-- /.card -->
@endsection

@section('js')
<script>
    function editInfo() {
        $("#edit-1").css("display", "none");
        $("#edit-3").css("display", "block");
    }
    function viewForm() {
        $("#pass").css("display", "none");
        $("#form_pass").css("display", "block");
    }
    function exit(){
        $("#edit-1").css("display", "block");
        $("#edit-3").css("display", "none");
    }
    function exit1(){
        $("#pass").css("display", "block");
        $("#form_pass").css("display", "none");
    }
</script>
@endsection