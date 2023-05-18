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

<style>
    .light {
        background: #666;
        color: #fff;
    }

    .clouder .tagcloud {
        display: inline-block;
        margin: 2px;
    }
    .tagcloud {
        font-size: 16px;
    }

    .tagcloud--item {
        padding: 2px 2px;
        background-color: transparent;
        border: 1px solid transparent;
        cursor: pointer;
    }

    .tagcloud--item:hover {
        background-color: rgba(0, 0, 0, .1);
        border: 1px solid #333;
        -webkit-border-radius: 2px;
        -moz-border-radius: 2px;
        border-radius: 10px;
        opacity: 1 !important;
        z-index: 100 !important;
    }

    .light .tagcloud--item {
        color: #fff;
    }

    .light .tagcloud--item:hover {
        background-color: rgba(255, 255, 255, .1);
        border: 1px solid #fff;
        }
</style>
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
                    Tags List
                </h4>                
            </div>
            <div class="card-body">
                <div class="table-responsive-lg table-responsive-sm table-responsive-md">
                    <table width="100%">
                        <tr>                            
                            <th width="80%" style="text-align:center">
                                <div id="id_title">Choose Category</div>       
                            </th>
                            <th width="20%" style="text-align:center">
                                List selected
                            </th>
                        </tr>
                        <tr>
                            <td width="80%" align="center">
                                <div id="clouder" style="width: 100%;height: 900px;" />
                            </td>
                            <td width="20%" valign="top">
                                <table width="100%">
                                    <tr>
                                        <td colspan="2" width="100%">
                                            <textarea id="listselect" rows="10" class="form-control"></textarea>
                                        </td>
                                    </tr>
                                    <tr style="height: 10px;"></tr>
                                    <tr>
                                        <td width="50%" align="center">
                                            <button id="btnchoose" class="btn btn-success">Confirm</button>
                                        </td>
                                        <td width="50%" align="center">
                                            <button id="btnback" style="display: none;" class="btn btn-danger">Back</button>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>                    
                </div>
                <form id="slideshow" action="{{route('dashboard.showslide')}}" method="POST">
                    <input type="hidden" name="_token" value="{{csrf_token()}}">
                    <input type="hidden" id="idcate" name="category" value="">
                    <input type="hidden" id="idstate" name="state" value="">                    
                </form>
            </div>
        </div>
    </div>
    </div><!-- row-->
</section>
@stop
<div class="modal fade" id="loading" tabindex="-1" role="dialog" aria-labelledby="deleteLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <table style="opacity: 1">
                <tr>
                    <td>
                        <div class="spinner-border m-5" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </td>
                    <td>
                        <h2>Loading...</h2>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>
{{-- page level scripts --}}
@section('footer_scripts')
<script type="text/javascript" src="{{ asset('js/TagCloud.min.js') }}" ></script> 
<script type="text/javascript" src="{{ asset('vendors/datatables/js/jquery.dataTables.js') }}" ></script>
    <script type="text/javascript" src="{{ asset('vendors/datatables/js/dataTables.bootstrap4.js') }}" ></script>  
<script>
    var curchoose = 0;
    var texts = [];
    var state = '';
    var category = '';
    var liststate = [];
    var listcate = [];
    var tc = TagCloud('#clouder', texts,{
        // radius in px
        radius: 500,
        maxSpeed: 'normal',
        initSpeed: 'normal',
        direction: 135,
        keep: true        
    });
        
    function loadtags(){  
        $.ajax({
            url: "{{route('dashboard.getList')}}",
            type: 'POST',
            data: {
                _token : '{{csrf_token()}}',
            },
            error: function(err) {
                //$('#loading').modal('hide');
            },
            success: function(data) {   
                if(data != 0){                    
                    listcate = data[0];
                    liststate = data[1];
                    tc.update(listcate);
                }
                //$('#loading').modal('hide');                              
            }
        });
    }
   
    $(function() {         
        var rootEl = document.querySelector('#clouder');
        rootEl.addEventListener('click', function clickEventHandler(e) {
            if (e.target.className === 'tagcloud--item') {  
                var tmp = e.target.innerText;
                if(curchoose == 0){
                    if(category == '') category = tmp;
                    else{
                        var ok = 1;
                        var sp = category.split(';');
                        for(var i = 0;i<sp.length;i++){
                            if(sp[i].trim() == tmp.trim()){
                                ok = 0;
                                break;
                            }
                        }
                        if(ok == 1) category += '; ' + tmp;
                    } 
                    $('#listselect').val(category);                    
                }else if(curchoose == 1){
                    if(state == '') state = tmp;
                    else{
                        var ok = 1;
                        var sp = state.split(';'); 
                        for(var i = 0;i<sp.length;i++){
                            if(sp[i].trim() == tmp.trim()){
                                ok = 0;
                                break;
                            }
                        }
                        if(ok == 1) state += '; ' + tmp;
                    } 
                    $('#listselect').val(state);
                }      
            }
        });

        $('#btnback').click(function(){
            category = '';
            if(curchoose == 1){
                $(".tagcloud").css("font-size", "16px");
                $('#id_title').text("Choose Category");
                $('#btnback').hide();
                curchoose = 0;
                tc.update(listcate);
                $('#idshowimg').hide();
                $('#listselect').val("");
            }
        });

        $('#btnchoose').click(function(){
            if($('#listselect').val() != ''){
                if(curchoose == 0){
                    curchoose = 1;
                    category = $('#listselect').val();
                    $('#btnback').show();
                    $('#id_title').text("Choose State");
                    $('#listselect').val("");
                    $(".tagcloud").css("font-size", "30px");
                    tc.update(liststate); 
                }else if(curchoose == 1){
                    state = $('#listselect').val();
                    if(state != '' && category != ''){
                        var strstate = state.split(";").join(",");
                        var strcategory = category.split(";").join(",");
                        $('#idstate').val(strstate);
                        $('#idcate').val(strcategory);
                        $('#slideshow').submit();
                    }else{
                        $('#idshowimg').hide();
                    }
                }
            }           

        });
        loadtags();
    });

</script>
@stop
