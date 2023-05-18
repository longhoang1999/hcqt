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
        font-size: 30px;
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
                                <div id="id_title">Choose State</div>       
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
                <div id="idshowimg" style="display: none;" class="table-responsive-lg table-responsive-sm table-responsive-md">
                    <table class="table table-bordered width100" id="table">
                        <thead>
                            <tr class="filters">
                                <th>Title</th>                                                  
                                <th>Name</th>
                                <th>Author</th>                                
                                <th>Lat</th>
                                <th>Lon</th>                                
                                <th>Credit</th>
                                <th>Image</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
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
    var texts = ["Italia", "Spagna", "Grecia", "Croazia", "Portogallo", "Francia", "Egitto", "Malta"];
    var state = '';
    var city = '';
    var label = '';
    var category = '';
    var tc = TagCloud('#clouder', texts,{
        // radius in px
        radius: 500,
        maxSpeed: 'normal',
        initSpeed: 'normal',
        direction: 135,
        keep: true        
    });
        
    function loadtags(opt){  
        if(curchoose == 1 && opt == 1){
            $('#loading').modal('toggle');
        }      
        $.ajax({
            url: "{{route('dashboard.getTags')}}",
            type: 'POST',
            data: {
                state : state,
                city : city,
                category : category,
                _token : '{{csrf_token()}}',
            },
            error: function(err) {
                $('#loading').modal('hide');
            },
            success: function(data) {   
                if(data != 0){                    
                    if(curchoose == 0 && state == ''){                        
                        $(".tagcloud").css("font-size", "30px");
                        $('#id_title').text("Choose State");
                        $('#btnback').hide();
                        tc.update(data);                        
                    }else{
                        if(curchoose == 0){
                            if(opt == 1){
                                curchoose = 1;
                                $('#id_title').text("Choose City");
                                $('#listselect').val("");
                                $('#btnback').show();
                                $(".tagcloud").css("font-size", "20px");
                                city = '';
                                category = '';
                                label = '';
                            }else{
                                $('#id_title').text("Choose State");
                                $('#btnback').hide();
                                $(".tagcloud").css("font-size", "30px");
                            }                            
                            tc.update(data);                        
                        }else{
                            if(curchoose == 1){
                                if(opt == 1){
                                    curchoose = 2;
                                    $('#btnback').show();
                                    $('#id_title').text("Choose Category");
                                    $('#listselect').val("");
                                    $(".tagcloud").css("font-size", "12px");
                                    category = '';
                                    label = '';
                                }else{
                                    $('#id_title').text("Choose City");    
                                    $(".tagcloud").css("font-size", "20px");
                                }                                
                                tc.update(data);                                
                            }else if(curchoose == 2){
                                if(opt == 1){
                                    curchoose = 3;
                                    $('#btnback').show();
                                    $('#id_title').text("Choose Label");
                                    $('#listselect').val("");
                                    $(".tagcloud").css("font-size", "12px");
                                    label = '';
                                }else{
                                    $('#id_title').text("Choose Category");    
                                    $(".tagcloud").css("font-size", "20px");
                                }                                
                                tc.update(data);                                
                            }
                        }
                    }
                }
                $('#loading').modal('hide');                              
            }
        });
    }
    
    $(function() { 
        var table = $('#table').DataTable({
            processing: true,
            //serverSide: true,
            ajax: "{!! route('dashboard.showimg') !!}",                                
            order: [],
            columns: [
                { data: 'title', name: 'title' },
                { data: 'name', name: 'name' },
                { data: 'author', name: 'author' },
                { data: 'lat', name: 'lat'},
                { data: 'lon', name:'lon'},
                { data: 'credit', name: 'credit'},
                { data: 'image', name: 'image', orderable: false, searchable: false }
            ]
        });

        var rootEl = document.querySelector('#clouder');
        rootEl.addEventListener('click', function clickEventHandler(e) {
            if (e.target.className === 'tagcloud--item') {  
                var tmp = e.target.innerText;
                if(curchoose == 0){
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
                }else if(curchoose == 1){
                    if(city == '') city = tmp;
                    else{
                        var ok = 1;
                        var sp = city.split(';');
                        for(var i = 0;i<sp.length;i++){
                            if(sp[i].trim() == tmp.trim()){
                                ok = 0;
                                break;
                            }
                        }
                        if(ok == 1) city += '; ' + tmp;
                    } 
                    $('#listselect').val(city);
                }else if(curchoose == 2){
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
                }else {
                    if(label == '') label = tmp;
                    else{
                        var ok = 1;
                        var sp = label.split(';');
                        for(var i = 0;i<sp.length;i++){
                            if(sp[i].trim() == tmp.trim()){
                                ok = 0;
                                break;
                            }
                        }
                        if(ok == 1) label += '; ' + tmp;
                    }
                    $('#listselect').val(label);
                }                              
            }
        });

        $('#btnback').click(function(){
            label = '';
            category = '';
            if(curchoose == 1){
                curchoose = 0;                
                state = '';                
            }else if(curchoose == 2){
                curchoose = 1;
                city = '';                
            }else if(curchoose == 3){
                curchoose = 2;                
            }
            $('#listselect').val("");
            $('#idshowimg').hide();
            loadtags(0);
        });

        $('#btnchoose').click(function(){
            if($('#listselect').val() != ''){
                if(state != '' && city != '' && category != '' && label != ''){
                    $('#idshowimg').show();
                    if(curchoose == 0){
                        state = $('#listselect').val();
                    }else if(curchoose == 1){
                        city = $('#listselect').val();
                    }else if(curchoose == 2){
                        category = $('#listselect').val();
                    }else{
                        label = $('#listselect').val();
                    }
                    var strcity = city.split(";").join("_______");
                    var strlabel = label.split(";").join("_______");
                    var strcategory = category.split(";").join("_______");
                    var params = '?city=' + strcity.split(" ").join("_____");
                    params += '&category=' + strcategory.split(" ").join("_____");
                    params += '&label=' + strlabel.split(" ").join("_____");
                    var url = "{!! route('dashboard.showimg') !!}" + params;
                    table.ajax.url(url).load();               
                }else{
                    $('#idshowimg').hide();
                    loadtags(1);
                }                
            }else{
                $('#idshowimg').hide();
                alert('You have to choose at lease one tag!');
            }
        });
        loadtags(1);
    });

</script>
@stop
