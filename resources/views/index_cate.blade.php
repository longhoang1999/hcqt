@extends('layouts/default')

{{-- Page title --}}
@section('title')
Tags List
@parent
@stop

{{-- page level styles --}}
@section('header_styles')
<link rel="stylesheet" type="text/css" href="{{ asset('vendors/datatables/css/dataTables.bootstrap4.css') }}" />
<link href="{{ asset('css/pages/tables.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('vendors/select2/css/select2.min.css') }}" rel="stylesheet"/>
<link href="{{ asset('vendors/select2/css/select2-bootstrap.css') }}" rel="stylesheet"/>
@stop


{{-- Page content --}}
@section('content')
<!-- Main content -->
<section class="content pl-3 pr-3">
    <div class="row">
        <div class="col-12">
        <div class="card ">
            <div class="card-header bg-primary text-white">
                <h4 class="card-title my-2 float-left"> <i class="livicon" data-name="user" data-size="16" data-loop="true" data-c="#fff" data-hc="white"></i>
                    Category List
                </h4>                
            </div>
            <div class="card-body">
                <form action="{{route('dashboard.updatecate')}}" method="POST">
                    <input type="hidden" name="_token" value="{{csrf_token()}}">
                    <div class="table-responsive-lg table-responsive-sm table-responsive-md">
                        <table width="100%">
                            <tr>
                                <th>Category from</th>
                                <th>Category to</th>
                            </tr>
                            <tr>
                                <td width="50%" align="center">
                                    {!! Form::select('catefrom', $cate, null, ['class' => 'form-control', 'id' => 'catefrom']) !!}                                
                                </td>

                                <td width="50%" align="center">
                                    <input type="text" name="cateto" value="" required="" class="form-control">
                                </td>                           
                            </tr>
                            <tr style="height: 10px;"></tr>
                            <tr>
                                <td></td><td><input type="submit" value="submit" class="btn btn-success"></td>
                            </tr>
                        </table>                    
                    </div>
                </form>
                <br/><br/>
                <div id="idshowimg" style="display: block;" class="table-responsive-lg table-responsive-sm table-responsive-md">
                    <table class="table table-bordered width100" id="table">
                        <thead>
                            <tr class="filters">
                                <th width="50%">Category</th>
                                <th width="50%">Label</th>   
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($list as $key => $value)
                            <tr>                                
                                <td>{{$value}}</td>
                                <td>{{$key}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    </div><!-- row-->
</section>
@stop

{{-- page level scripts --}}
@section('footer_scripts')
<script type="text/javascript" src="{{ asset('js/TagCloud.min.js') }}" ></script> 
<script type="text/javascript" src="{{ asset('vendors/datatables/js/jquery.dataTables.js') }}" ></script>
<script type="text/javascript" src="{{ asset('vendors/datatables/js/dataTables.bootstrap4.js') }}" ></script>   
<script language="javascript" type="text/javascript" src="{{ asset('vendors/select2/js/select2.js') }}"></script>
<script type="text/javascript">
    $(function() { 
        var table = $('#table').DataTable();
        $('#catefrom').select2({
            theme: 'bootstrap',
            placeholder: 'select a value',
        });
    });
</script>   
@stop
