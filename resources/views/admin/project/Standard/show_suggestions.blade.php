@extends('admin/layouts/default')
{{-- Page title --}}
@section('title')
    @lang('project/Standard/title.qlgyhd')
@parent
@stop

{{-- page level styles --}}
@section('header_styles')
<link href="{{ asset('vendors/select2/css/select2.min.css') }}" rel="stylesheet" />
<link href="{{ asset('css/pages/editor.css') }}" rel="stylesheet" type="text/css"/>
<link rel="stylesheet" href="{{ asset('css/project/Standard/standard.css') }}">
<link rel="stylesheet" href="{{ asset('css/project/Standard/custom-minimun.css') }}">
<style type="text/css">
    .mt-3{
        margin-top: 13px;
    }
    .form-control{
        
    }
    .mr-2{
        margin-right: 10px;
    }
    .select2-selection{
        padding: 0 !important;
    }
    .select2-selection__rendered{
        height: 100% !important;
        display: flex !important;
        align-items: center !important;
    }
</style>
@stop

@section('title_page')
    @lang('project/Standard/title.qlgyhd')
@stop

@section('content')
    <section class="content indexpage pr-3 pl-3">
        <!-- Bắt đầu trang -->
<!-- page trang ở đây -->
<section class="content-body">
    <div class="form-standard">
        <h4>@lang('project/Standard/title.timkiem')</h4>
        <div class="container-fuild pl-5 pr-5">
            <div class="row">
                <div class="col-md-10">
                    <input type="text" placeholder="@lang('project/Standard/title.ndgyhd')" class="form-control " id="mctt_name">
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn-benchmark mr-2 btn" data-toggle="modal" data-target="#modaleCreate" data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('project/Standard/title.tmgyhd')">
                        <i class="bi bi-plus-square" style="font-size: 30px;color: #009ef7;"></i>
                    </button>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-4">
                    <select class="form-control" id="ldg">
                        <option value="" hidden>-- @lang('project/Standard/title.ldg')</option>
                        @foreach($loai_tieuchuan as $key => $ltc)
                            <option value="{{ $key }}">{{ $ltc }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <select class="form-control " id="btc">
                        
                    </select>
                </div>
                <div class="col-sm-1">
                    <button class="btn" id="btn-search" data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('project/Standard/title.timkiem')">
                        <i class="bi bi-search"style="font-size: 30px;color: #009ef7;"></i>
                    </button>
                </div>
                <div class="col-md-1">
                    <a href="{{ route('admin.thuongtruc.setstandard.exportGyhd') }}" class="btn btn-benchmark mr-2" type="button" data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('project/Standard/title.xuat_excel')">
                        <i class="bi bi-file-earmark-excel " style="font-size: 35px;color: #47be7d;"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="form-standard">
        <h4>@lang('project/Standard/title.dsgyhd')</h4>
        <div class="item-group-button right-block mb-2">
            
        </div>

        <div class="container-fuild">
            <table class="table table-striped table-bordered" id="table">
              <thead class="thead-light">
                <tr>
                    <th scope="col" title="@lang('project/Standard/title.ndgyhd')">
                        @lang('project/Standard/title.ndgyhd')
                    </th>
                    <th scope="col" title="@lang('project/Standard/title.btcad')">
                        @lang('project/Standard/title.btcad')
                    </th>
                    <th scope="col" title="@lang('project/Standard/title.tchiad')">
                        @lang('project/Standard/title.tchiad') 
                    </th>
                    <th scope="col" title="@lang('project/Standard/title.ldg')">
                        @lang('project/Standard/title.ldg_short')
                    </th>
                    <th scope="col" title="@lang('project/Standard/title.hanhd')">
                        @lang('project/Standard/title.hanhd')
                    </th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
        </div>
    </div>
</section>
<!-- Modal -->
<div class="modal fade" id="modalDelete" tabindex="-1" role="dialog" aria-labelledby="modalDeleteLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDeleteLabel">
                    @lang('project/Standard/title.thongbao')
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <span class="badge ">
                    @lang('project/Standard/message.error.hoixoaTc')
                </span>
                <br>
                <span class="badge ">
                    @lang('project/Standard/message.error.khoantac')
                </span>
            </div>
            <div class="modal-footer">
                <a href="#" class="btn btn-danger" id="btn-delete-mctt">
                    @lang('project/Standard/title.xoa')
                </a>
                <button type="button" class="btn btn-primary" data-dismiss="modal">
                    @lang('project/Standard/title.huy')
                </button>
            </div>
        </div>
    </div>
</div>

<!-- modal create mctt -->
<div class="modal fade" id="modaleCreate" tabindex="-1" role="dialog" aria-labelledby="modaleCreateLabal" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modaleCreateLabal">
                    @lang('project/Standard/title.tmgyhd')
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.thuongtruc.setstandard.createGyhd') }}" method="post">
                @csrf
                <div class="modal-body">
                    <div class="container-fuild">
                        <div class="row">
                            <div class="col-md-6 pl-4 pr-4 pb-4">
                                <h4>
                                    @lang('project/Standard/title.tmgyhd')
                                </h4>
                                <div class="form-group">
                                    <label for="selectldg">
                                        @lang('project/Standard/title.ldg')
                                    </label>
                                    <select class="form-control " id="selectldg" name="ldg">
                                        <option value="">-- @lang('project/Standard/title.ldg')</option>
                                        @foreach($loai_tieuchuan as $key => $ltc)
                                            <option value="{{ $key }}">{{ $ltc }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="selectbtc">
                                        @lang('project/Standard/title.btc')
                                    </label>
                                    <select class="form-control " id="selectbtc" name="btc">
                                        <option value="">-- @lang('project/Standard/title.btc')</option>
                                    </select>
                                </div>
                                <!-- <div class="form-group">
                                    <label for="inputtieude">
                                        @lang('project/Standard/title.tieude')
                                    </label>
                                    <input type="text" placeholder="@lang('project/Standard/title.tieude')" class="form-control " name="tieude" required id="inputtieude">
                                </div> -->
                                <div class="form-group">
                                    <label for="textcontent">
                                        @lang('project/Standard/title.ndmctt')
                                    </label>
                                    <textarea id="textcontent" name="ndmctt"></textarea>
                                </div>
                            </div>
                            <div class="col-md-6 pl-4 pr-4 pb-4">
                                <h4>
                                    @lang('project/Standard/title.tchiad')
                                </h4>

                                <div class="block-render">
                                    
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-info">
                        @lang('project/Standard/title.tgyhd') 
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- modal update mctt  -->
<div class="modal fade" id="modalUpdate" tabindex="-1" role="dialog" aria-labelledby="modalUpdateLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalUpdateLabel">
                    @lang('project/Standard/title.cnndhd')
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.thuongtruc.setstandard.updateGyhd') }}" method="post">
                @csrf
                <input type="hidden" value="" id="idmctt" name="idmctt">
                <div class="modal-body">
                    <div class="container-fuild">
                        <div class="row">
                            <div class="col-md-6 pl-4 pr-4 pb-4">
                                <h4>
                                    @lang('project/Standard/title.gyhd')
                                </h4>
                                <div class="form-group">
                                    <label for="up_selectldg">
                                        @lang('project/Standard/title.ldg')
                                    </label>
                                    <select class="form-control " id="up_selectldg" name="ldg">
                                        <option value="">-- @lang('project/Standard/title.ldg')</option>
                                        @foreach($loai_tieuchuan as $key => $ltc)
                                            <option value="{{ $key }}">{{ $ltc }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="up_selectbtc">
                                        @lang('project/Standard/title.btc')
                                    </label>
                                    <select class="form-control " id="up_selectbtc" name="btc">
                                        <option value="">-- @lang('project/Standard/title.btc')</option>
                                    </select>
                                </div>
                                <!-- <div class="form-group">
                                    <label for="up_inputtieude">
                                        @lang('project/Standard/title.tieude')
                                    </label>
                                    <input type="text" placeholder="@lang('project/Standard/title.tieude')" class="form-control " name="tieude" required id="up_inputtieude">
                                </div> -->
                                <div class="form-group">
                                    <label for="up_textcontent">
                                        @lang('project/Standard/title.ndgyhd')
                                    </label>
                                    <textarea id="up_textcontent" name="ndmctt"></textarea>
                                </div>
                            </div>
                            <div class="col-md-6 pl-4 pr-4 pb-4">
                                <h4>
                                    @lang('project/Standard/title.tchiad')
                                </h4>

                                <div class="block-render-update">
                                    
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-info">
                        @lang('project/Standard/title.cngyhd') 
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- /Kết thúc page trang -->

    <!-- Kết thúc trang -->
    </section>

@stop



{{-- page level scripts --}}
@section('footer_scripts')
    <script src="{{asset('vendors/ckeditor/js/ckeditor.js')}}" type="text/javascript"></script>
    <script src="{{asset('vendors/tinymce/js/tinymce.min.js')}}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/editor.js') }}" type="text/javascript"></script>
    <script type="text/javascript">
        CKEDITOR.replace( 'textcontent', {
            toolbar: [
                [  
                    'Bold', 'Italic', 'BulletedList', 'NumberedList', 'Image','Table',
                    'Cut', 'Copy', 'Paste', 'Undo', 'Redo' , 'FontSize'
                ],
            ]
        });

        CKEDITOR.replace( 'up_textcontent', {
            toolbar: [
                [  
                    'Bold', 'Italic', 'BulletedList', 'NumberedList', 'Image','Table',
                    'Cut', 'Copy', 'Paste', 'Undo', 'Redo' , 'FontSize'
                ],
            ]
        });

        

        $("#btc").select2({
            allowClear: false,
            placeholder: "@lang('project/Standard/title.btc')",
        });

        $("#ldg").change(function() {
            let routeApi = "{{ route('admin.thuongtruc.setstandard.findStandard') }}" + "?ldg="  +  $(this).val();
            fetch(routeApi, {
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
            })
                .then((response) => response.json())
                .then((data) => {
                    $("#btc").empty();
                    $("#btc").append(`<option value="" hidden>-- @lang('project/Standard/title.btc')</option>`);
                    data.forEach(item => {
                        let UI = ` 
                            <option value="${item.id}">${item.tieu_de}</option>
                         `;
                        $("#btc").append(UI);
                    });
                })
        })


        $("#selectldg").change(function() {
            let routeApi = "{{ route('admin.thuongtruc.setstandard.findStandard') }}" + "?ldg="  +  $(this).val();
            fetch(routeApi, {
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
            })
                .then((response) => response.json())
                .then((data) => {
                    $("#selectbtc").empty();
                    $("#selectbtc").append(`<option value="" hidden>-- @lang('project/Standard/title.btc')</option>`);
                    data.forEach(item => {
                        let UI = ` 
                            <option value="${item.id}">${item.tieu_de}</option>
                         `;
                        $("#selectbtc").append(UI);
                    });
                    $(".block-render").empty();
                })
        })


        $(function(){
        table = $('#table').DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                searching: false,
                ajax:  {
                    url: "{!! route('admin.thuongtruc.setstandard.dataSugges') !!}",
                    type: 'POST',
                    data: {
                        'mctt_name' : function() { return $("#mctt_name").val() },
                        'ldg'       : function() { return $("#ldg").val() },
                        'btc_id'    : function() { return $("#btc").val() },
                        _token: '{{csrf_token()}}'
                    },
                },
                columns: [
                    { data: 'mo_ta_gyhd', name: 'mo_ta_gyhd' },
                    { data: 'tieu_de_btc', name: 'tieu_de_btc' },
                    { data: 'tchi_ad', name: 'tchi_ad', className: 'dt-tchi' },
                    { data: 'ldg', name: 'ldg' },
                    { data: 'actions', name: 'actions' ,className: 'action'},
                ],    
            });
        });

        $('#modalDelete').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget) 
            var id = button.data('id') 
            var route = "{{ route('admin.thuongtruc.setstandard.deleteSugge') }}" + "?id_delete=" + id;
            var modal = $(this);
            modal.find('#btn-delete-mctt').attr('href' , route);
        })

        $("#btn-search").click(function() {
            table.ajax.reload();
        })

        document.addEventListener("keydown", function(e){
            if(e.key == "Enter"){
                $("#btn-search").trigger("click")
            }
        })
    </script>


    <script type="text/javascript">
        $('.collapse').on('show.bs.collapse', function () {
            let icon = $(this).parent().find(".btn-standard").find(".icon");
            icon.css("transform", "translateY(-50%) rotate(180deg)");
        })

        $('.collapse').on('hidden.bs.collapse', function () {
            let icon = $(this).parent().find(".btn-standard").find(".icon");
            icon.css("transform", "translateY(-50%) rotate(0deg)");
        })

        // load tiêu chí
        $("#selectbtc").change(function() {
            let routeApi = "{{ route('admin.thuongtruc.setstandard.fStandardCriteria') }}" + "?btc="  +  $(this).val() + "&standard=true";
            fetch(routeApi, {
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
            })
                .then((response) => response.json())
                .then((data) => {
                    let dataStandard =  data[0];
                    let dataCriteria =  data[1];

                    $(".block-render").empty();
                    dataStandard.forEach((item, index) => {
                        let UI = ` 
                            <div class="collapse-item mb-2">
                                <button data-id="${item.id_tc}" class="btn-standard text-left" type="button" data-toggle="collapse" data-target="#blockCriteria_${item.id_tc}" aria-expanded="false" aria-controls="blockCriteria_${item.id_tc}">
                                    <span>TC ${item.stt}: ${item.mo_ta_tc}</span>
                                    <div class="icon">
                                        <ion-icon name="caret-up-outline"></ion-icon>
                                    </div>
                                </button>
                        `;
                        let wrap_item = "";
                        dataCriteria.forEach((itemCriteria, indexCriteria) => {
                            if(itemCriteria.data_tchi == "yes" && itemCriteria.id_tc  == item.id_tc){
                                wrap_item += ` 
                                    <label class="item-block-render" for="item_${itemCriteria.id_tchi}" title="${itemCriteria.mo_ta_tchi}">
                                        <input type="checkbox" id="item_${itemCriteria.id_tchi}" name="id_tchi[]" value="${itemCriteria.id_tchi}">
                                        <span>
                                            ${item.stt}.${itemCriteria.stt}: ${itemCriteria.mo_ta_tchi}
                                        </span>
                                    </label>
                                 `
                            }else if(itemCriteria.data_tchi == "none" && itemCriteria.id_tc  == item.id_tc){
                                wrap_item += ` 
                                    <label class="item-block-render" title="@lang('project/Standard/title.kcdltc')">
                                        <span>
                                            @lang('project/Standard/title.kcdltc') 
                                        </span>
                                    </label>
                                 `
                            }

                        })
                        let wrap_ = ` 
                            <div class="collapse" id="blockCriteria_${item.id_tc}">
                                ${wrap_item}
                            </div>
                         `;

                        UI = UI + wrap_ + "</div>";
                        $(".block-render").append(UI);
                    })
                    
                })
        })

    </script>

    <script type="text/javascript">
        // load thông tin khi cập nhật
        $('#modalUpdate').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget)
            var idmctt = button.data('id') 
            $("#idmctt").val(idmctt);

            let routeApi = "{{ route('admin.thuongtruc.setstandard.loadDataSugg') }}" + "?mctt="  +  idmctt;
            fetch(routeApi, {
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
            })
                .then((response) => response.json())
                .then((data) => {
                    let findMctt = data[0];
                    let btc = data[1];

                    $("#up_selectbtc").empty();
                    btc.forEach(item => {
                        let UI = ` 
                            <option value="${item.id}">${item.tieu_de}</option>
                         `;
                        $("#up_selectbtc").append(UI);
                    });
                    return findMctt;
                })
                .then((findMctt) => {
                    $("#up_selectbtc").val(findMctt.bo_tieuchuan_id);
                    $("#up_selectldg").val(findMctt.loai_tieuchuan);
                    // $("#up_inputtieude").val(findMctt.tieu_de_mctt);
                    CKEDITOR.instances['up_textcontent'].setData(findMctt.tieu_de_mctt);

                    let routeRe = "{{ route('admin.thuongtruc.setstandard.fStandardCriteria2') }}" + "?btc="  +  findMctt.bo_tieuchuan_id + "&standard=true";
                    return fetch(routeRe, {
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                    })
                })
                .then((response) => response.json())
                .then((data) => {
                    let dataStandard =  data[0];
                    let dataCriteria =  data[1];

                    $(".block-render-update").empty();
                    dataStandard.forEach((item, index) => {
                        let UI = ` 
                            <div class="collapse-item mb-2">
                                <button data-id="${item.id_tc}" class="btn-standard text-left" type="button" data-toggle="collapse" data-target="#up_blockCriteria_${item.id_tc}" aria-expanded="false" aria-controls="up_blockCriteria_${item.id_tc}">
                                    <span>TC ${item.stt}: ${item.mo_ta_tc}</span>
                                    <div class="icon">
                                        <ion-icon name="caret-up-outline"></ion-icon>
                                    </div>
                                </button>
                        `;
                        let wrap_item = "";
                        dataCriteria.forEach((itemCriteria, indexCriteria) => {
                            if(itemCriteria.data_tchi == "yes" && itemCriteria.id_tc  == item.id_tc){
                                wrap_item += ` 
                                    <label class="item-block-render" for="up_item_${itemCriteria.id_tchi}" title="${itemCriteria.mo_ta_tchi}">
                                        <input class="mr-2" type="checkbox" id="up_item_${itemCriteria.id_tchi}" name="up_id_tchi[]" value="${itemCriteria.id_tchi}">
                                        <span>
                                            ${item.stt}.${itemCriteria.stt}: ${itemCriteria.mo_ta_tchi}
                                        </span>
                                    </label>
                                 `
                            }else if(itemCriteria.data_tchi == "none" && itemCriteria.id_tc  == item.id_tc){
                                wrap_item += ` 
                                    <label class="item-block-render" title="@lang('project/Standard/title.kcdltc')">
                                        <span>
                                            @lang('project/Standard/title.kcdltc') 
                                        </span>
                                    </label>
                                 `
                            }
                        })
                        let wrap_ = ` 
                            <div class="collapse" id="up_blockCriteria_${item.id_tc}">
                                ${wrap_item}
                            </div>
                         `;

                        UI = UI + wrap_ + "</div>";
                        $(".block-render-update").append(UI);
                    })

                    let routeRole_ = "{{ route('admin.thuongtruc.setstandard.loadTchiMctt2') }}" + "?mctt="  +  idmctt;
                    return fetch(routeRole_, {
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                    });
                })
                .then((response) => response.json())
                .then((dataCriteria) => {
                    let input = $("input[name='up_id_tchi[]']");
                    for(let index = 0; index < input.length; index ++){
                        if(dataCriteria.includes(input[index].value)){
                            let idInput = input[index].getAttribute("id");
                            $(`#${idInput}`).prop('checked',true);
                        }
                    }
                })

        })


        $("#up_selectldg").change(function() {
            let routeApi = "{{ route('admin.thuongtruc.setstandard.findStandard') }}" + "?ldg="  +  $(this).val();
            fetch(routeApi, {
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
            })
                .then((response) => response.json())
                .then((data) => {
                    $("#up_selectbtc").empty();
                    $("#up_selectbtc").append(`<option value="" hidden>-- @lang('project/Standard/title.btc')</option>`);
                    data.forEach(item => {
                        let UI = ` 
                            <option value="${item.id}">${item.tieu_de}</option>
                         `;
                        $("#up_selectbtc").append(UI);
                    });
                })
        })

        // check nếu chọn đúng thì load lại select
        $("#up_selectbtc").change(function() {
            let routeApi = "{{ route('admin.thuongtruc.setstandard.fStandardCriteria') }}" + "?btc="  +  $(this).val() + "&standard=true" + "&id_mctt=" + $("#idmctt").val();
            fetch(routeApi, {
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
            })
                .then((response) => response.json())
                .then((data) => {
                    let dataStandard =  data[0];
                    let dataCriteria =  data[1];
                    let exitsCriteria = data[2];

                    $(".block-render-update").empty();
                    dataStandard.forEach((item, index) => {
                        let UI = ` 
                            <div class="collapse-item mb-2">
                                <button data-id="${item.id_tc}" class="btn-standard text-left" type="button" data-toggle="collapse" data-target="#up_blockCriteria_${item.id_tc}" aria-expanded="false" aria-controls="up_blockCriteria_${item.id_tc}">
                                    <span>TC ${item.stt}: ${item.mo_ta_tc}</span>
                                    <div class="icon">
                                        <ion-icon name="caret-up-outline"></ion-icon>
                                    </div>
                                </button>
                        `;
                        let wrap_item = "";
                        dataCriteria.forEach((itemCriteria, indexCriteria) => {
                            if(itemCriteria.data_tchi == "yes" && itemCriteria.id_tc  == item.id_tc){
                                wrap_item += ` 
                                    <label class="item-block-render" for="up_item_${itemCriteria.id_tchi}" title="${itemCriteria.mo_ta_tchi}">
                                        <input type="checkbox" id="up_item_${itemCriteria.id_tchi}" name="up_id_tchi[]" value="${itemCriteria.id_tchi}">
                                        <span>
                                            ${item.stt}.${itemCriteria.stt}: ${itemCriteria.mo_ta_tchi}
                                        </span>
                                    </label>
                                 `
                            }else if(itemCriteria.data_tchi == "none" && itemCriteria.id_tc  == item.id_tc){
                                wrap_item += ` 
                                    <label class="item-block-render" title="@lang('project/Standard/title.kcdltc')">
                                        <span>
                                            @lang('project/Standard/title.kcdltc') 
                                        </span>
                                    </label>
                                 `
                            }

                        })
                        let wrap_ = ` 
                            <div class="collapse" id="up_blockCriteria_${item.id_tc}">
                                ${wrap_item}
                            </div>
                         `;

                        UI = UI + wrap_ + "</div>";
                        $(".block-render-update").append(UI);
                    })
                    return exitsCriteria;
                })
                .then((exitsCriteria) => {
                    if(exitsCriteria.length != 0){
                        var myArrr = exitsCriteria.reduce((arr, value) => {
                            arr.push(value.tieuchi_id.toString());
                            return arr;
                        }, []);
                       

                        let input = $("input[name='up_id_tchi[]']");
                        for(let index = 0; index < input.length; index ++){
                            if(myArrr.includes(input[index].value)){
                                let idInput = input[index].getAttribute("id");
                                $(`#${idInput}`).prop('checked',true);
                            }
                        }
                    }
                })
        })
    </script>

@stop
