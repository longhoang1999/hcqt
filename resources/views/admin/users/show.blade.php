@extends('admin/layouts/default')

{{-- Page title --}}
@section('title')
    View User Details
    @parent
@stop

{{-- page level styles --}}
@section('header_styles')
    <meta name="csrf_token" content="{{ csrf_token() }}">
    <link href="{{ asset('vendors/jasny-bootstrap/css/jasny-bootstrap.css') }}" rel="stylesheet"/>
    <link href="{{ asset('vendors/x-editable/css/bootstrap-editable.css') }}" rel="stylesheet"/>

    <link href="{{ asset('css/pages/user_profile.css') }}" rel="stylesheet"/>
@stop
@section('title_page')
    @lang('project/Common/title.tttk')
@stop

{{-- Page content --}}
@section('content')
    <!--section ends-->
    <section class="content user_profile  pr-3 pl-3">
        <div class="row">
            <div class="col-lg-12">
                <ul class="nav  nav-tabs first_svg">
                    <li class="nav-item">
                        <a href="#tab1" data-toggle="tab" class="nav-link active">
                            <i class="livicon" data-name="user" data-size="16" data-c="#777"  data-hc="#000" data-loop="true"></i>
                            @lang('project/Common/title.tttk')
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#tab2" data-toggle="tab" class="nav-link">
                            <i class="livicon" data-name="key" data-size="16" data-loop="true" data-c="#000" data-hc="#000"></i>
                            @lang('project/Common/title.tdmk')
                        </a>
                    </li>
                    <!-- <li class="nav-item">
                        <a href="{{ URL::to('admin/user_profile') }}" class=" nav-link" >
                            <i class="livicon" data-name="gift" data-size="16" data-loop="true" data-c="#000" data-hc="#000"></i>
                            Advanced User Profile</a>
                    </li> -->

                </ul>
                <div  class="tab-content mar-top" id="clothing-nav-content">
                    <div id="tab1" class="tab-pane fade show active">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row">
                                        <div class="col-md-4">
                                            <div class="img-file">
                                                @if($user->pic != null && $user->pic != "")
                                                    <img src="{{ asset($user->pic) }}" alt="img"
                                                         class="img-fluid"/>
                                                @elseif($user->gender === "male")
                                                    <img src="{{ asset('images/authors/avatar3.png') }}" alt="..."
                                                         class="img-fluid"/>
                                                @elseif($user->gender === "female")
                                                    <img src="{{ asset('images/authors/avatar5.png') }}" alt="..."
                                                         class="img-fluid"/>
                                                @else
                                                    <img src="{{ asset('images/authors/no_avatar.jpg') }}" alt="..."
                                                         class="img-fluid"/>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                                <div class="table-responsive-lg table-responsive-sm table-responsive-md table-responsive">
                                                    <table class="table table-bordered table-striped" id="users">

                                                        <tr>
                                                            <td>@lang('project/Common/title.ma_nhansu')</td>
                                                            <td>
                                                                <p class="user_name_max">{{ $user->ma_nhansu }}</p>
                                                            </td>

                                                        </tr>
                                                        <tr>
                                                            <td>@lang('project/Common/title.name')</td>
                                                            <td>
                                                                <p class="user_name_max">{{ $user->name }}</p>
                                                            </td>

                                                        </tr>
                                                        <tr>
                                                            <td>@lang('project/Common/title.email')</td>
                                                            <td>
                                                                {{ $user->email }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                @lang('project/Common/title.gender')
                                                            </td>
                                                            <td>
                                                                @if($user->gender == 1)
                                                                    @lang('project/Common/title.male')
                                                                @elseif($user->gender == 2)
                                                                    @lang('project/Common/title.female')
                                                                @endif

                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>@lang('project/Common/title.dob')</td>

                                                            @if($user->ns=='0000-00-00')
                                                                <td>
                                                                </td>
                                                            @else
                                                                <td>
                                                                    {{ date("d/m/Y", strtotime($user->ns)) }}
                                                                </td>
                                                            @endif
                                                        </tr>
                                                        <tr>
                                                            <td>@lang('project/Common/title.phone')</td>
                                                            <td>
                                                                {{ $user->phone }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>@lang('project/Common/title.donvi')</td>
                                                            <td>
                                                                @php
                                                                    $dv = DB::table('donvi')->select('id', 'ten_donvi')->where('id', $user->donvi_id)->first();
                                                                @endphp
                                                                {{ $dv->ten_donvi }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>@lang('project/Common/title.tdnvtcn')</td>
                                                            <td>
                                                                {{ $user->tdnvtcn }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>@lang('project/Common/title.ntn')</td>
                                                            <td>
                                                                {{ $user->ntn }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>@lang('project/Common/title.noitn')</td>
                                                            <td>
                                                                {{ $user->noitn }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>@lang('project/Common/title.gvsp')</td>
                                                            <td>
                                                                {{ $user->gvsp }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>@lang('project/Common/title.th')</td>
                                                            <td>
                                                                {{ $user->th }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>@lang('project/Common/title.nn')</td>
                                                            <td>
                                                                {{ $user->nn }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>@lang('project/Common/title.hhdp')</td>
                                                            <td>
                                                                {{ $user->hhdp }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>@lang('project/Common/title.ndp')</td>
                                                            <td>
                                                                {{ $user->ndp }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>@lang('project/Common/title.cdnnktd')</td>
                                                            <td>
                                                                {{ $user->cdnnktd }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>@lang('project/Common/title.mscnktd')</td>
                                                            <td>
                                                                {{ $user->mscnktd }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>@lang('project/Common/title.ntd')</td>
                                                            <td>
                                                                {{ $user->ntd }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>@lang('project/Common/title.qdbn')</td>
                                                            <td>
                                                                {{ $user->qdbn }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>@lang('project/Common/title.cdkn')</td>
                                                            <td>
                                                                {{ $user->cdkn }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td></td>
                                                            <td>
                                                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalUpdate">
                                                                    @lang('project/Common/title.chinhsua')
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </div>
                    <div id="tab2" class="tab-pane fade">
                        <div class="row">
                            <div class="col-md-12 pd-top ml-auto">
                                <form action="{{ route('admin.changePass') }}" method="post" class="form-horizontal">
                                    <div class="form-body">
                                        <div class="form-group">
                                            <div class="row">
                                            {{ csrf_field() }}
                                            <label for="inputpassword" class="col-md-3 control-label">
                                                @lang('project/Common/title.oldPass')
                                                <span class='require'>*</span>
                                            </label>
                                            <div class="col-md-9">
                                                <div class="input-group">
                                                    <input type="password" id="password" placeholder="Password" name="password"
                                                           class="form-control" required />
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="row">
                                            <label for="inputnumber" class="col-md-3 control-label">
                                                @lang('project/Common/title.newPass')
                                                <span class='require'>*</span>
                                            </label>
                                            <div class="col-md-9">
                                                <div class="input-group">
                                                    <input type="password" id="password-new" placeholder="New Password" name="new_password"
                                                           class="form-control" required />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    </div>
                                    <div class="form-actions">
                                        <div class="col-md-offset-3 col-md-9 ml-auto">
                                            <button type="submit" class="btn btn-primary" id="change-password">
                                                @lang('project/Common/title.changePass')
                                            </button>
                                            &nbsp;
                                            <input type="reset" class="btn btn-secondary" value="Reset"></div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- chỉnh sửa user -->
    <div class="modal fade" id="modalUpdate" tabindex="-1" role="dialog" aria-labelledby="modalUpdateLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalUpdateLabel">@lang('project/Common/title.chinhsua')</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form action="{{ route('admin.updateUser') }}" method="post">
                @csrf
              <div class="modal-body">
                    <table class="table table-bordered table-striped" id="users">
                        <tr>
                            <td>@lang('project/Common/title.ma_nhansu')</td>
                            <td>
                                <input class="form-control" type="text" name="ma_nhansu" value="{{ $user->ma_nhansu }}">
                            </td>
                        </tr>
                        <tr>
                            <td>@lang('project/Common/title.name')</td>
                            <td>
                                <input class="form-control" type="text" name="ten_nhansu" value="{{ $user->name }}">
                            </td>

                        </tr>
                        <tr>
                            <td>@lang('project/Common/title.email')</td>
                            <td>
                                <input type="email" name="email_ns" class="form-control" value="{{ $user->email }}" disabled>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                @lang('project/Common/title.gender')
                            </td>
                            <td>
                                <select class="form-control" name="gender_ns">
                                    <option value="1"   
                                        @if($user->gender == 1)
                                            selected
                                        @endif
                                     >
                                        @lang('project/Common/title.male')
                                    </option>
                                    <option value="2"   
                                        @if($user->gender == 2)
                                            selected
                                        @endif
                                     >
                                        @lang('project/Common/title.female')
                                    </option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>@lang('project/Common/title.dob')</td>
                            <td>
                                <input type="date" value="{{ $user->ns }}" class="form-control" name="ns_ns">
                            </td>
                        </tr>
                        <tr>
                            <td>@lang('project/Common/title.phone')</td>
                            <td>
                                <input type="text" class="form-control" value="{{ $user->phone }}" name="phone_ns">
                            </td>
                        </tr>
                        <tr>
                            <td>@lang('project/Common/title.donvi')</td>
                            <td>
                                @php
                                    $dv = DB::table('donvi')->select('id', 'ten_donvi')->where('id', $user->donvi_id)->first();
                                    $dvAll = DB::table('donvi')->get();
                                @endphp
                                <select name="dv_ns">
                                    @foreach($dvAll as $dvs)
                                        <option  
                                            @if($dv->id == $dvs->id)
                                                selected
                                            @endif
                                         value="{{ $dvs->id }}">{{ $dvs->ten_donvi }}</option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>@lang('project/Common/title.tdnvtcn')</td>
                            <td>
                                <input class="form-control" type="text" name="tdnvtcn" value="{{ $user->tdnvtcn }}">
                            </td>
                        </tr>
                        <tr>
                            <td>@lang('project/Common/title.ntn')</td>
                            <td>
                                <input type="text" name="ntn" class="form-control" value="{{ $user->ntn }}">
                            </td>
                        </tr>
                        <tr>
                            <td>@lang('project/Common/title.noitn')</td>
                            <td>
                                <input type="text" class="form-control" value="{{ $user->noitn }}" name="noitn">
                            </td>
                        </tr>
                        <tr>
                            <td>@lang('project/Common/title.gvsp')</td>
                            <td>
                                <input type="text" class="form-control" value="{{ $user->gvsp }}" name="gvsp">
                            </td>
                        </tr>
                        <tr>
                            <td>@lang('project/Common/title.th')</td>
                            <td>
                                <input type="text" class="form-control" value="{{ $user->th }}" name="th_ns">
                            </td>
                        </tr>
                        <tr>
                            <td>@lang('project/Common/title.nn')</td>
                            <td>
                                <input type="text" class="form-control" value="{{ $user->nn }}" name="nn_ns">
                            </td>
                        </tr>
                        <tr>
                            <td>@lang('project/Common/title.hhdp')</td>
                            <td>
                                <input type="text" class="form-control" value="{{ $user->hhdp }}" name="hhdp">
                            </td>
                        </tr>
                        <tr>
                            <td>@lang('project/Common/title.ndp')</td>
                            <td>
                                <input type="text" class="form-control" value="{{ $user->ndp }}" name="ndp">
                            </td>
                        </tr>
                        <tr>
                            <td>@lang('project/Common/title.cdnnktd')</td>
                            <td>
                                <input type="text" class="form-control" value="{{ $user->cdnnktd }}" name="cdnnktd">
                            </td>
                        </tr>
                        <tr>
                            <td>@lang('project/Common/title.mscnktd')</td>
                            <td>
                                <input type="text" class="form-control" value="{{ $user->mscnktd }}" name="mscnktd">
                            </td>
                        </tr>
                        <tr>
                            <td>@lang('project/Common/title.ntd')</td>
                            <td>
                                <input type="text" class="form-control" value="{{ $user->ntd }}" name="ntd">
                            </td>
                        </tr>
                        <tr>
                            <td>@lang('project/Common/title.qdbn')</td>
                            <td>
                                <input type="text" class="form-control" value="{{ $user->qdbn }}" name="qdbn">
                            </td>
                        </tr>
                        <tr>
                            <td>@lang('project/Common/title.cdkn')</td>
                            <td>
                                <input type="text" class="form-control" value="{{ $user->cdkn }}" name="cdkn">
                            </td>
                        </tr>
                    </table>
              </div>
              <div class="modal-footer">
                <button type="submit" class="btn btn-primary">@lang('project/Common/title.luu')</button>
              </div>
          </form>
        </div>
      </div>
    </div>
@stop

{{-- page level scripts --}}
@section('footer_scripts')
    <!-- Bootstrap WYSIHTML5 -->
    <script  src="{{ asset('vendors/jasny-bootstrap/js/jasny-bootstrap.js') }}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $('#change-password').click(function (e) {
                e.preventDefault();
                var check = true;
                if ($('#password').val() ===""){
                    alert("@lang('project/Common/title.enterPass')");
                    check = false;
                }
                else if($('#password-new').val() === ""){
                    alert("@lang('project/Common/title.enterNewPass')");
                    check = false;
                }

                if(check == true){
                    $(".form-horizontal").submit();
                    // var sendData =  '_token=' + $("input[name='_token']").val() +'&password=' + $('#password').val() +'&id=' + {{ $user->id }};
                    // var path = "passwordreset";
                    // $.ajax({
                    //     url: path,
                    //     type: "post",
                    //     data: sendData,
                    //     headers: {
                    //         'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
                    //     },
                    //     success: function (data) {
                    //         $('#password, #password-confirm').val('');
                    //         alert('password reset successful');
                    //     },
                    //     error: function (xhr, ajaxOptions, thrownError) {
                    //         alert('error in password reset');
                    //     }
                    // });
                }
            });
        });



        $('a[data-toggle="tab"]').on('show.bs.tab', function (e) {
            e.target // newly activated tab
            e.relatedTarget // previous active tab
            $("#clothing-nav-content .tab-pane").removeClass("show active");
        });

    </script>

@stop
