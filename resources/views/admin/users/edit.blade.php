@extends('admin/layouts/default')

{{-- Page title --}}
@section('title')
Edit User
@parent
@stop

{{-- page level styles --}}
@section('header_styles')
<!--page level css -->
<link href="{{ asset('vendors/jasny-bootstrap/css/jasny-bootstrap.css') }}" rel="stylesheet">
<link href="{{ asset('vendors/select2/css/select2.min.css') }}" type="text/css" rel="stylesheet">
<link href="{{ asset('vendors/select2/css/select2-bootstrap.css') }}" rel="stylesheet">
<link href="{{ asset('vendors/datetimepicker/css/bootstrap-datetimepicker.min.css') }}" rel="stylesheet">
<link href="{{ asset('vendors/iCheck/css/all.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('css/pages/wizard.css') }}" rel="stylesheet">
@stop
<!--end of page level css-->
<style>
    .form-group{
        margin-top: 10px;
    }
</style>

{{-- Page content --}}
@section('title_page')
    @lang('project/Common/title.cstt')
@stop

@section('content')
<section class="content pr-3 pl-3">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-lg-12 my-3">
            <div class="card ">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title">
                        <i class="livicon" data-name="user-add" data-size="18" data-c="#fff" data-hc="#fff"
                            data-loop="true"></i>
                        @lang('project/Common/title.cstt') : {!! $user->name!!}
                    </h3>
                    <span class="float-right clickable">
                        <i class="fa fa-chevron-up"></i>
                    </span>
                </div>
                <div class="card-body">
                    <!--main content-->
                    {!! Form::model($user, ['url' => URL::to('admin/users/'. $user->id.''), 'method' => 'put', 'class'
                    => 'form-horizontal','id'=>'commentForm', 'enctype'=>'multipart/form-data','files'=> true]) !!}
                    {{ csrf_field() }}
                    <!-- CSRF Token -->


                    <div id="rootwizard">
                        <ul>
                            <li class="nav-item"><a href="#tab1" data-toggle="tab" class="nav-link">
                                @lang('project/Common/title.ttcb')
                            </a>
                            </li>
                            <li class="nav-item"><a href="#tab2" data-toggle="tab" class="nav-link ml-2">
                                @lang('project/Common/title.ttlh')
                            </a></li>
                            <li class="nav-item"><a href="#tab3" data-toggle="tab" class="nav-link ml-2">
                                @lang('project/Common/title.ttns')
                            </a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane " id="tab1">
                                <h2 class="hidden">&nbsp;</h2>
                                <div class="form-group">
                                    <div class="row">
                                        <label for="ma_ns" class="col-sm-2 control-label">
                                            @lang('project/Common/title.ma_nhansu') *
                                         </label>
                                        <div class="col-sm-10">
                                            <input id="ma_ns" name="ma_ns" type="text"
                                                placeholder="@lang('project/Common/title.ma_nhansu')" class="form-control required"
                                                value="{!! old('ma_nhansu', $user->ma_nhansu) !!}" />
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row"> <label for="name" class="col-sm-2 control-label">
                                        @lang('project/Common/title.name')
                                            *</label>
                                        <div class="col-sm-10">
                                            <input id="name" name="name" type="text" placeholder="@lang('project/Common/title.name')"
                                                class="form-control required"
                                                value="{!! old('name', $user->name) !!}" />
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row"> <label for="gender" class="col-sm-2 control-label">
                                        @lang('project/Common/title.gender')
                                            *</label>
                                        <div class="col-sm-10">
                                            <select name="gender" id="gender" class="form-control required">
                                                <option value="" hidden></option>
                                                <option  
                                                @if(old('gender', $user->gender) == 1)
                                                    selected
                                                @endif
                                                 value="1">@lang('project/Common/title.male')</option>
                                                 <option  
                                                @if(old('gender', $user->gender) == 2)
                                                    selected
                                                @endif
                                                 value="3">@lang('project/Common/title.female')</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row"> <label for="dob" class="col-sm-2 control-label">
                                        @lang('project/Common/title.dob')
                                            *</label>
                                        <div class="col-sm-10">
                                            <input id="dob" name="dob" type="date" placeholder="@lang('project/Common/title.dob')"
                                                class="form-control required"
                                                value="{!! old('dob', $user->dob) !!}" />
                                        </div>
                                    </div>
                                </div>


                                <div class="form-group {{ $errors->first('email', 'has-error') }}">
                                    <div class="row">
                                        <label for="email" class="col-sm-2 control-label">Email *</label>
                                        <div class="col-sm-10">
                                            <input id="email" name="email" placeholder="E-mail" type="text"
                                                class="form-control required email"
                                                value="{!! old('email', $user->email) !!}" />
                                            {!! $errors->first('email', '<span class="help-block">:message</span>') !!}
                                        </div>
                                    </div>
                                </div>


                            </div>
                            <div class="tab-pane" id="tab2" disabled="disabled">
                                <h2 class="hidden">&nbsp;</h2>
                                <div class="form-group  {{ $errors->first('dob', 'has-error') }}">
                                    <div class="row">
                                        <label for="dob" class="col-sm-2 control-label">Date of Birth</label>
                                        <div class="col-sm-10">
                                            <input id="dob" name="dob" type="text" class="form-control"
                                                data-date-format="YYYY-MM-DD" value="{!! old('dob', $user->dob) !!}"
                                                placeholder="yyyy-mm-dd" />
                                        </div>
                                        <span class="help-block">{{ $errors->first('dob', ':message') }}</span>
                                    </div>
                                </div>


                                <div class="form-group {{ $errors->first('pic_file', 'has-error') }}">
                                    <div class="row">

                                        <label class="col-sm-2 control-label">Profile picture</label>
                                        <div class="col-sm-10">
                                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                                <div class="fileinput-new thumbnail"
                                                    style="width: 200px; height: 200px;">
                                                    @if($user->pic)
                                                    <img src="{{ $user->pic }}" alt="img" class="img-responsive" />
                                                    @elseif($user->gender === "male")
                                                    <img src="{{ asset('images/authors/avatar3.png') }}" alt="..."
                                                        class="img-responsive" />
                                                    @elseif($user->gender === "female")
                                                    <img src="{{ asset('images/authors/avatar5.png') }}" alt="..."
                                                        class="img-responsive" />
                                                    @else
                                                    <img src="{{ asset('images/authors/no_avatar.jpg') }}" alt="..."
                                                        class="img-responsive" />
                                                    @endif
                                                </div>
                                                <div class="fileinput-preview fileinput-exists thumbnail"
                                                    style="max-width: 200px; max-height: 200px;"></div>
                                                <div>
                                                    <span class="btn btn-primary btn-file">
                                                        <span class="fileinput-new">Select image</span>
                                                        <span class="fileinput-exists">Change</span>
                                                        <input id="pic" name="pic_file" type="file"
                                                            class="form-control" />
                                                    </span>
                                                    <a href="#" class="btn btn-primary fileinput-exists"
                                                        data-dismiss="fileinput"
                                                        style="color: black !important;">Remove</a>
                                                </div>
                                            </div>
                                        </div>
                                        <span class="help-block">{{ $errors->first('pic_file', ':message') }}</span>
                                    </div>
                                </div>


                                <div class="form-group">
                                    <div class="row">
                                        <label for="bio" class="col-sm-2 control-label">Bio <small>(brief intro)
                                                *</small></label>
                                        <div class="col-sm-10">
                                            <textarea name="bio" id="bio" class="form-control resize_vertical"
                                                rows="4">{!! old('bio', $user->bio) !!}</textarea>
                                        </div>
                                        {!! $errors->first('bio', '<span class="help-block">:message</span>') !!}
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="tab3" disabled="disabled">
                                <div class="form-group {{ $errors->first('gender', 'has-error') }}">
                                    <div class="row">
                                        <label for="email" class="col-sm-2 control-label">Gender *</label>
                                        <div class="col-sm-10">
                                            <select class="form-control" title="Select Gender..." name="gender">
                                                <option value="">Select</option>
                                                <option value="male" @if($user->gender === 'male') selected="selected"
                                                    @endif >Male</option>
                                                <option value="female" @if($user->gender === 'female')
                                                    selected="selected" @endif >Female</option>
                                                <option value="other" @if($user->gender === 'other') selected="selected"
                                                    @endif >Other</option>

                                            </select>
                                        </div>
                                        <span class="help-block">{{ $errors->first('gender', ':message') }}</span>
                                    </div>
                                </div>

                                <div class="form-group {{ $errors->first('country', 'has-error') }}">
                                    <div class="row">
                                        <label for="country" class="col-sm-2 control-label">Country</label>
                                        <div class="col-sm-10">
                                            {!! Form::select('country',
                                            $countries,old('country',$user->country),['class' => 'country_field
                                            form-control']) !!}
                                        </div>
                                        <span class="help-block">{{ $errors->first('country', ':message') }}</span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <label for="state" class="col-sm-2 control-label">State</label>
                                        <div class="col-sm-10">
                                            <input id="state" name="user_state" type="text" class="form-control"
                                                value="{!! old('user_state', $user->user_state) !!}" />
                                        </div>
                                        <span class="help-block">{{ $errors->first('state', ':message') }}</span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <label for="city" class="col-sm-2 control-label">City</label>
                                        <div class="col-sm-10">
                                            <input id="city" name="city" type="text" class="form-control"
                                                value="{!! old('city', $user->city) !!}" />
                                        </div>
                                        <span class="help-block">{{ $errors->first('city', ':message') }}</span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <label for="address" class="col-sm-2 control-label">Address</label>
                                        <div class="col-sm-10">
                                            <input id="address" name="address" type="text" class="form-control"
                                                value="{!! old('address', $user->address) !!}" />
                                        </div>
                                        <span class="help-block">{{ $errors->first('address', ':message') }}</span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <label for="postal" class="col-sm-2 control-label">Postal/zip</label>
                                        <div class="col-sm-10">
                                            <input id="postal" name="postal" type="text" class="form-control"
                                                value="{!! old('postal', $user->postal) !!}" />
                                        </div>
                                        <span class="help-block">{{ $errors->first('postal', ':message') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="tab4" disabled="disabled">
                                <p class="text-danger"><strong>Be careful with role selection, if you give admin
                                        access.. they can access admin section</strong></p>

                                <div class="form-group required">
                                    <div class="row">
                                        <label for="role" class="col-sm-2 control-label">Role *</label>
                                        <div class="col-sm-10">
                                            <select class="form-control required" title="Select role..."
                                                name="roles[]" id="roles">
                                                <option value="">Select</option>
                                                @foreach($roles as $role)
                                                <option value="{!! $role->id !!}"
                                                    {{ (array_key_exists($role->id, $userRoles) ? ' selected="selected"' : '') }}
                                                    @if($user->id==1&&$role->id>=2) disabled @endif @if($user->id==2 &&
                                                    $role->id!=2) disabled @endif>{{ $role->name }}</option>
                                                @endforeach
                                            </select>
                                            {!! $errors->first('role', '<span class="help-block">:message</span>') !!}
                                        </div>
                                    </div>
                                    <span class="help-block">{{ $errors->first('role', ':message') }}</span>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <label for="activate" class="col-sm-2 control-label"> Activate User *</label>
                                        <div class="col-sm-10">
                                            <input id="activate" name="activate" type="checkbox"
                                                class="pos-rel p-l-30 custom-checkbox" value="1" @if($status)
                                                checked="checked" @endif>
                                            <span>To activate user account automatically, click the check box</span>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <ul class="pager wizard">
                                <li class="previous"><a href="#">Previous</a></li>
                                <li class="next"><a href="#">Next</a></li>
                                <li class="next finish" style="display:none;"><a href="javascript:;">Finish</a></li>
                            </ul>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
    <!--row end-->
</section>
@stop

{{-- page level scripts --}}
@section('footer_scripts')
<script src="{{ asset('vendors/iCheck/js/icheck.js') }}"></script>
<script src="{{ asset('vendors/moment/js/moment.min.js') }}"></script>
<script src="{{ asset('vendors/jasny-bootstrap/js/jasny-bootstrap.js') }}" type="text/javascript"></script>
<script src="{{ asset('vendors/select2/js/select2.js') }}" type="text/javascript"></script>
<script src="{{ asset('vendors/bootstrapwizard/jquery.bootstrap.wizard.js') }}" type="text/javascript"></script>
<script src="{{ asset('vendors/datetimepicker/js/bootstrap-datetimepicker.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/pages/edituser.js') }}"></script>
<script>
    function formatState (state) {
            if (!state.id) { return state.text; }
            var $state = $(
                '<span><img src="{{asset('img/countries_flags')}}/'+ state.element.value.toLowerCase() + '.png" class="img-flag" width="20px" height="20px" /> ' + state.text + '</span>'
            );
            return $state;

        }
        $(".country_field").select2({
            templateResult: formatState,
            templateSelection: formatState,
            placeholder: "select a country",
            theme:"bootstrap"
        });

</script>
@stop
