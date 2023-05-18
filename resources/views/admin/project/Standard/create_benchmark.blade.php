@extends('admin/layouts/default')
{{-- Page title --}}
@section('title')
    @lang('project/Standard/title.tltchi')
@parent
@stop

{{-- page level styles --}}
@section('header_styles')

<link rel="stylesheet" href="{{ asset('css/project/Standard/standard.css') }}">

<style type="text/css">
    /*input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }*/
    .w-20{
        width: 20%;
    }
    .render_UI_tab2 .item-group{
        align-items: stretch !important;
    }
</style>

@stop

@section('title_page')
    @lang('project/Standard/title.tltchi')
@stop

@section('content')
    <section class="content indexpage pr-3 pl-3">
        <!-- Bắt đầu trang -->
<section class="content-body">
    <div class="form-standard">
        <h4>@lang('project/Standard/title.tbtc')</h4>
        <div class="create-standard">
            <input type="text" placeholder="@lang('project/Standard/title.tbtc')" class="form-control" disabled value="{{ $btc_name }}">
            <select class="form-control" disabled>
                <option hidden>@lang('project/Standard/title.ldg')</option>
                <option value="csdt" 
                    @if($btc_ldg == "csdt")
                        selected = ""
                    @endif
                 >CSDT</option>
                <option value="ctdt" 
                    @if($btc_ldg == "ctdt")
                        selected = ""
                    @endif
                 >CTDT</option>
            </select>
        </div>
        <h4>@lang('project/Standard/title.tchuan_tchi')</h4>
        <form action="{{ route('admin.thuongtruc.setstandard.upCriteria') }}" method="post" class="create-standard">
            @csrf
            <select class="form-control" disabled>
                <option hidden>@lang('project/Standard/title.tieuchuan')</option>
                <option value="{{ $tc_name }}" selected>
                    {{ $tc_name }}
                </option>
            </select>

            <input type="hidden" name="id_tchi" value="{{ $id_tchi }}">
            <input class="form-control" type="text" name="nameTchi" value="{{ $tchi_name }}">
            <button class="btn ">
                <i class="bi bi-save" style="font-size: 35px;color: #50cd89;;" data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('project/Standard/title.upTChi')"></i>             
            </button>
        </form>

        <div class="content-page">
            <div class="wrapper-tabs">
                <ul class="tabs">
                    <li class="tab-link current" data-tab="tab-1">
                        @lang('project/Standard/title.mocchuan')
                    </li>
                    <li class="tab-link" data-tab="tab-2">
                        @lang('project/Standard/title.chibao')
                    </li>
                    <!-- <li class="tab-link" data-tab="tab-3">
                        @lang('project/Standard/title.gyhd')
                    </li> -->
                </ul>
                <div id="tab-1" class="tab-content current">
                    <form action="{{ route('admin.thuongtruc.setstandard.createBenchmark') }}" method="POST" id="form-slmc">
                        @csrf
                        <input type="hidden" value="{{ $id_tchi }}" name="id_tchi">
                        <div class="item-group first-item">
                            <span class="mr-4">@lang('project/Standard/title.slmc')</span>
                            <input type="number" class="form-control slmc" value="0">
                            <div class="item-group-button ml-3">
                                <button id="btn-up-slmc" class="btn btn-benchmark mr-2" type="button">
                                    <i class="bi bi-caret-up-fill" style="font-size: 30px;color: #50cd89;"></i>
                                </button>
                                <button id="btn-down-slmc" class="btn btn-benchmark" type="button">
                                    <i class="bi bi-caret-down-fill" style="font-size: 30px;color: #50cd89;"></i>
                                </button>
                            </div>
                        </div>
                        <div class="render_UI">
                 
                        </div>
                        <hr>

                        <div class="item-group-button">
                            <button id="btn-cancel-slmc" class="btn btn-benchmark mr-2" type="button" data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('project/Standard/title.huy')">
                                <i class="bi bi-x-circle" style="font-size: 30px;color: red;"></i>
                            </button>
                            <button id="btn-save-slmc" class="btn btn-benchmark mr-2" type="button" data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('project/Standard/title.luu')">
                                <i class="bi bi-save" style="font-size: 30px;color: #50cd89;"></i>
                            </button>
                        </div>
                    </form>

                    <h4 class="mt-5">@lang('project/Standard/title.qlmocchuan')</h4>
                    <table class="table table-striped table-bordered" id="table" width="100%">
                        <thead>
                         <tr>
                            <th >@lang('project/Standard/title.ndmc')</th>
                            <th >@lang('project/Standard/title.giatri')</th>
                            <th >@lang('project/Standard/title.trongso')</th>
                            <th >@lang('project/Standard/title.ngayt')</th>
                            <th >@lang('project/Standard/title.nguoit')</th>
                            <th >@lang('project/Standard/title.hanhd')</th>
                         </tr>
                        </thead>
                        <tbody>  
                        </tbody>                
                    </table>
                </div>
                <div id="tab-2" class="tab-content">
                    <form action="{{ route('admin.thuongtruc.setstandard.postChibao') }}" method="post" id="form-slmctt">
                        @csrf
                        <input type="hidden" value="{{ $id_tchi }}" name="id_tchi">
                        <div class="item-group first-item">
                            <span class="mr-4">@lang('project/Standard/title.slcb')</span>
                            <input type="number" class="form-control slmctt" value="0">
                            <div class="item-group-button ml-3">
                                <button id="btn-up-slmctt" class="btn btn-benchmark mr-2" type="button">
                                    <i class="bi bi-caret-up-fill" style="font-size: 30px;color: #50cd89;"></i>
                                </button>
                                <button id="btn-down-slmctt" class="btn btn-benchmark" type="button">
                                    <i class="bi bi-caret-down-fill" style="font-size: 30px;color: #50cd89;"></i>
                                </button>
                            </div>
                        </div>

                        <div class="render_UI_tab2">
                 
                        </div>
                        <hr>

                        <div class="item-group-button">
                            <button id="btn-cancel-slmctt" class="btn btn-benchmark mr-2" type="button">
                                <i class="bi bi-x-circle" style="font-size: 30px;color: red;"></i>
                            </button>
                            <button id="btn-save-slmctt" class="btn btn-benchmark mr-2" type="button">
                                <i class="bi bi-save" style="font-size: 30px;color: #50cd89;"></i>
                            </button>
                        </div>
                    </form>

                    <h4 class="mt-5">@lang('project/Standard/title.qlcbao')</h4>
                    <table class="table table-striped table-bordered table_wrapper" id="table2" width="100%">
                        <thead>
                         <tr>
                            <th >@lang('project/Standard/title.ndcb')</th>
                            <th >@lang('project/Standard/title.tukhoa')</th>
                            <th >@lang('project/Standard/title.ktmc')</th>
                            <th >@lang('project/Standard/title.kttp')</th>
                            <th >@lang('project/Standard/title.gioihan')</th>
                            <th >@lang('project/Standard/title.hanhd')</th>
                         </tr>
                        </thead>
                        <tbody>  
                        </tbody>                
                    </table>

                </div>
            </div>
        </div>
    </div>
</section>

<!-- modal -->
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
                <span class="badge badge-danger">
                    @lang('project/Standard/message.error.hoixoaTc')
                </span>
                <br>
                <span class="badge badge-primary">
                    @lang('project/Standard/message.error.khoantac')
                </span>
            </div>
            <div class="modal-footer">
                <a href="#" class="btn btn-danger" id="btn-delete-mc">
                    @lang('project/Standard/title.xoa')
                </a>
                <button type="button" class="btn btn-primary" data-dismiss="modal">
                    @lang('project/Standard/title.huy')
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalUpdate" tabindex="-1" role="dialog" aria-labelledby="modalUpdateLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalUpdateLabel">
                    @lang('project/Standard/title.csmc')
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" action="{{ route('admin.thuongtruc.setstandard.updateMocchuan') }}">
                <input type="hidden" value="id_mch" id="id_mch" name="id_mch">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="inputNdmc">
                            @lang('project/Standard/title.ndmc')
                        </label>
                        <input type="text" class="form-control h-2rem" id="inputNdmc" placeholder="@lang('project/Standard/title.ndmc')" required name="inputNdmc">
                    </div>
                    <div class="form-group">
                        <label for="inputGiatri">
                            @lang('project/Standard/title.giatri')
                        </label>
                        <input type="number" class="form-control h-2rem" id="inputGiatri" placeholder=" @lang('project/Standard/title.giatri')"  name="inputGiatri">
                    </div>
                    <div class="form-group">
                        <label for="inputTrongso">
                            @lang('project/Standard/title.trongso')
                        </label>
                        <input type="number" class="form-control h-2rem" id="inputTrongso" placeholder=" @lang('project/Standard/title.trongso')"  name="inputTrongso">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">
                        @lang('project/Standard/title.tdmc')
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDeleteCb" tabindex="-1" role="dialog" aria-labelledby="modalDeleteCbLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDeleteCbLabel">
                    @lang('project/Standard/title.thongbao')
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <span class="badge badge-danger">
                    @lang('project/Standard/message.error.hoixoaTc')
                </span>
                <br>
                <span class="badge badge-primary">
                    @lang('project/Standard/message.error.khoantac')
                </span>
            </div>
            <div class="modal-footer">
                <a href="#" class="btn btn-danger" id="btn-delete-chibao">
                    @lang('project/Standard/title.xoa')
                </a>
                <button type="button" class="btn btn-primary" data-dismiss="modal">
                    @lang('project/Standard/title.huy')
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalUpdateCb" tabindex="-1" role="dialog" aria-labelledby="modalUpdateCbLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalUpdateCbLabel">
                    @lang('project/Standard/title.cscb')
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" action="{{ route('admin.thuongtruc.setstandard.updateChibao') }}">
                <input type="hidden" id="id_chibao" name="id_chibao">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="up_inputNdcb">
                            @lang('project/Standard/title.ndcb')
                        </label>
                        <textarea class="form-control" id="up_inputNdcb" placeholder="@lang('project/Standard/title.ndcb')" name="ndcb" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="up_inputTk">
                            @lang('project/Standard/title.tukhoa')
                        </label>
                        <input type="text" class="form-control h-2rem" id="up_inputTk" placeholder="@lang('project/Standard/title.tukhoa')" required name="tukhoa">
                    </div>
                    <div class="container-fuild mb-4">
                        <div class="row">
                            <div class="col-md-6">
                                <input type="checkbox" id="up_inputKtmc">
                                <label for="up_inputKtmc">
                                    @lang('project/Standard/title.ktmc')
                                </label>
                                <input type="hidden" value="N"  name="value_inputKtmc" id="value_inputKtmc">
                            </div>
                            <div class="col-md-6">
                                <input type="checkbox" id="up_inputKttp">
                                <label for="up_inputKttp">
                                    @lang('project/Standard/title.kttp')
                                </label>
                                <input type="hidden" value="N"  name="value_inputKttp" id="value_inputKttp">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="up_inputGioihan">
                            @lang('project/Standard/title.gioihan')
                        </label>
                        <input type="number" id="up_inputGioihan" class="form-control h-2rem" placeholder="@lang('project/Standard/title.gioihan')" name="gioihan">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">
                        @lang('project/Standard/title.tdcb')
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>




<!-- Kết thúc trang -->
    </section>
@stop



{{-- page level scripts --}}
@section('footer_scripts')
<script type="text/javascript">
    $(function(){
        table = $('#table').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            ajax: "{!! route('admin.thuongtruc.setstandard.dataMocchuan') !!}"  + "?id_tchi=" 
                    + '{{ $id_tchi }}',
            columns: [
                { data: 'mo_ta', name: 'mo_ta' },
                { data: 'gia_tri', name: 'gia_tri' },
                { data: 'trong_so', name: 'trong_so' },
                { data: 'createAt', name: 'createAt' },
                { data: 'createHuman', name: 'createHuman' },
                { data: 'actions', name: 'actions' },
            ],            
        });
    });  

    $('#modalDelete').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget) 
        var id = button.data('id') 
        var route = "{{ route('admin.thuongtruc.setstandard.deleteMocchuan') }}" + "?id_delete=" + id;
        var modal = $(this);
        modal.find('#btn-delete-mc').attr('href' , route);
    })

    $('#modalUpdate').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget) 
        var id = button.data('id') 
        var route = "{{ route('admin.thuongtruc.setstandard.getDataMocchuan') }}" + 
                    "?id_mc=" + id;
        fetch(route, {
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
        })
            .then((response) => response.json())
            .then((data) => {
                $("#id_mch").val(data.id);
                $("#inputTrongso").val(data.trong_so);
                $("#inputGiatri").val(data.gia_tri);
                $("#inputNdmc").val(data.mo_ta)
            })
    })

    $(document).ready(function(){
        $('ul.tabs li').click(function(){
            var tab_id = $(this).attr('data-tab');

            $('ul.tabs li').removeClass('current');
            $('.tab-content').removeClass('current');

            $(this).addClass('current');
            $("#"+tab_id).addClass('current');
        })
    })
</script>

<!-- JS số lượng mốc chuẩn -->
<script type="text/javascript">
    var ndmc = "@lang('project/Standard/title.ndmc')";
    var giatri = "@lang('project/Standard/title.giatri')";
    var trongso = "@lang('project/Standard/title.trongso')";
    var slmc = 0;
    $(".slmc").change(function() {
        if($(this).val() <= 0){
            alert("@lang('project/Standard/title.nddd')");
        }else{
            slmc = parseInt($(this).val());
            $(".render_UI").empty();
            for(let i = 1; i <= slmc; i++){
                formatUI(i);
            }
        }
        
    })

    function formatUI(i){
        let tcrow = `
            <div class="mt-4">
                <div class="item-group">
                    <input type="text" class="form-control item-group-content group-content-1" placeholder="${ndmc} ${i}" id="content-slmc-${i}" name="noidung[]" required>
                    <input type="number" class="form-control item-group-value group-value-1" placeholder="${giatri}" name="giatri[]" min="1" max="100" step="0.01" required>
                    <input type="number" class="form-control item-group-number group-number-1" placeholder="${trongso}" name="trongso[]" min="1" max="100" step="0.01" required>
                    <button class="item-group-trash" data-id="${i}" type="button">
                        <ion-icon name="trash-outline" style="font-size:22px"></ion-icon>
                    </button>
                </div>
                <hr>
            </div>
        `;
        $(".render_UI").append(tcrow);
    }

    $(".render_UI").on("click", ".item-group-trash", function() {
        let indexItemDelete = $(this).data("id");
        $(this).parent().parent().remove();

        for(let i = indexItemDelete; i < slmc; i++){
            $(`#content-slmc-${i + 1}`).attr("placeholder", `${ndmc} ${i}`);
            $(`#content-slmc-${i + 1}`).parent()
                                        .find(".item-group-trash")
                                        .attr("data-id", `${i}`);
            $(`#content-slmc-${i + 1}`).attr("id", `content-slmc-${i}`);
        }
        slmc = slmc - 1;
        $(".slmc").val(slmc);
    })

    $("#btn-up-slmc").click(function() {
        slmc = slmc + 1;
        $(".slmc").val(slmc);
        formatUI(slmc);
    })

    $("#btn-down-slmc").click(function() {
        if(slmc > 0){
            $(`#content-slmc-${slmc}`).parent().parent().remove();
            slmc = slmc - 1;
            $(".slmc").val(slmc);
        }
    })

    $("#btn-cancel-slmc").click(function() {
        slmc = 0;
        $(".slmc").val("0");
        $(".render_UI").empty();
    })

    $("#btn-save-slmc").click(function() {
        let check = document.querySelectorAll(".group-content-1").length == 0;
        if(checkSubmit() && !check){
            $("#form-slmc").submit();
        }else{
            alert("@lang('project/Standard/title.ttbths')")
        }
    })

    function checkSubmit() {
        var listContent = document.querySelectorAll(".group-content-1");
        var listValue = document.querySelectorAll(".group-value-1");
        var listNumber = document.querySelectorAll(".group-number-1");
        let check = true;

        for(let i = 0; i < listContent.length;i++){
            if(listContent[i].value == "")
                check = false;
        }

        // for(let i = 0; i < listValue.length;i++){
        //     if(listValue[i].value == "" || listValue[i].value < 0 || listValue[i].value > 100)
        //         check = false;

        // }
        // for(let i = 0; i < listNumber.length;i++){
        //     if(listNumber[i].value == "" || listNumber[i].value < 0 || listNumber[i].value > 100)
        //         check = false;
        // }
        return check;
    }
    
</script>

<!-- JS chỉ báo -->
<script type="text/javascript">
    var content = "@lang('project/Standard/title.content')";
    var slmctt = 0;
    $(".slmctt").change(function() {
        if($(this).val() <= 0){
            alert("@lang('project/Standard/title.nddd')");
        }else{
            slmctt = parseInt($(this).val());
            $(".render_UI_tab2").empty();
            for(let i = 1; i <= slmctt; i++){
                formatUITab2(i);
            }
        }
        
    })

    function formatUITab2(i){
        let tcrow = `
            <div class="mt-4">
                <div class="item-group">
                    <textarea class="form-control h-2rem item-group-content group-content-2" placeholder="${content} ${i}" id="content-slmctt-${i}" name="noidung[]"></textarea>
                    <input type="text" class="form-control ml-4 mr-4 w-20 group-tukhoa-2" placeholder="@lang('project/Standard/title.tukhoa')" name="tukhoa[]">
                    <div align="center">
                        <input type="checkbox" id="checkBox_ktmc_${i}" class="checkBox_ktmc">
                        <input type="hidden" class="value_ktmc" value="N" name="ktmc[]">
                        <label for="checkBox_ktmc_${i}">
                            @lang('project/Standard/title.ktmc')
                        </label>
                    </div>
                    <div align="center">
                        <input type="checkbox" id="checkBox_kttp_${i}" class="checkBox_kttp">
                        <input type="hidden" class="value_kttp" value="N" name="kttp[]">
                        <label for="checkBox_kttp_${i}">
                            @lang('project/Standard/title.kttp')
                        </label>
                    </div>
                    <input type="number" class="form-control ml-4 mr-4 w-20 group-gioihan-2" placeholder="@lang('project/Standard/title.gioihan')" name="gioihan[]">

                    <button class="item-group-trash" data-id="${i}" type="button">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-trash-fill" viewBox="0 0 16 16">
                            <path d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1H2.5zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5zM8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5zm3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0z"/>
                        </svg>
                    </button>
                </div>
                <hr>
            </div>
        `;
        $(".render_UI_tab2").append(tcrow);
    }

    $(".render_UI_tab2").on("click", ".item-group-trash", function() {
        let indexItemDelete = $(this).data("id");
        $(this).parent().parent().remove();

        for(let i = indexItemDelete; i < slmctt; i++){
            $(`#content-slmctt-${i + 1}`).attr("placeholder", `${content} ${i}`);
            $(`#content-slmctt-${i + 1}`).parent()
                                        .find(".item-group-trash")
                                        .attr("data-id", `${i}`);
            $(`#content-slmctt-${i + 1}`).attr("id", `content-slmctt-${i}`);
        }
        slmctt = slmctt - 1;
        $(".slmctt").val(slmctt);
    })

    $("#btn-up-slmctt").click(function() {
        slmctt = slmctt + 1;
        $(".slmctt").val(slmctt);
        formatUITab2(slmctt);
    })

    $("#btn-down-slmctt").click(function() {
        if(slmctt > 0){
            $(`#content-slmctt-${slmctt}`).parent().parent().remove();
            slmctt = slmctt - 1;
            $(".slmctt").val(slmctt);
        }
    })

    $("#btn-cancel-slmctt").click(function() {
        slmctt = 0;
        $(".slmctt").val("0");
        $(".render_UI_tab2").empty();
    })

    $("#btn-save-slmctt").click(function() {
        if(checkSubmit2())
            $("#form-slmctt").submit();
        else
            alert("@lang('project/Standard/title.tttcb')");
    })

    function checkSubmit2() {
        var listContent = document.querySelectorAll(".group-content-2");
        var listTuKhoa = document.querySelectorAll(".group-tukhoa-2");
        var listGioiHan = document.querySelectorAll(".group-gioihan-2");
        let check = true;

        for(let i = 0; i < listContent.length;i++){
            if(listContent[i].value == "")
                check = false;
        }
        for(let i = 0; i < listTuKhoa.length;i++){
            if(listTuKhoa[i].value == "")
                check = false;

        }
        for(let i = 0; i < listGioiHan.length;i++){
            if(listGioiHan[i].value == "")
                check = false;
        }
        return check;
    }
</script>


<!-- JS gợi ý hướng dẫn -->
<script type="text/javascript">
    var gyhd = 0;
    $(".gyhd").change(function() {
        if($(this).val() <= 0){
            alert("@lang('project/Standard/title.nddd')");
        }else{
            gyhd = parseInt($(this).val());
            $(".render_UI_tab3").empty();
            for(let i = 1; i <= gyhd; i++){
                formatUITab3(i);
            }
        }
        
    })

    function formatUITab3(i){
        let tcrow = `
            <div class="mt-4">
                <div class="item-group">
                    <input type="text" class="form-control item-group-content" placeholder="${content} ${i}"  id="content-gyhd-${i}" name="noidung[]">
                    <button class="item-group-trash" data-id="${i}" type="button">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-trash-fill" viewBox="0 0 16 16">
                            <path d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1H2.5zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5zM8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5zm3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0z"/>
                        </svg>
                    </button>
                </div>
                <hr>
            </div>
        `;
        $(".render_UI_tab3").append(tcrow);
    }

    $(".render_UI_tab3").on("click", ".item-group-trash", function() {
        let indexItemDelete = $(this).data("id");
        $(this).parent().parent().remove();

        for(let i = indexItemDelete; i < gyhd; i++){
            $(`#content-gyhd-${i + 1}`).attr("placeholder", `${content} ${i}`);
            $(`#content-gyhd-${i + 1}`).parent()
                                        .find(".item-group-trash")
                                        .attr("data-id", `${i}`);
            $(`#content-gyhd-${i + 1}`).attr("id", `content-gyhd-${i}`);
        }
        gyhd = gyhd - 1;
        $(".gyhd").val(gyhd);
    })

    $("#btn-up-gyhd").click(function() {
        gyhd = gyhd + 1;
        $(".gyhd").val(gyhd);
        formatUITab3(gyhd);
    })

    $("#btn-down-gyhd").click(function() {
        if(gyhd > 0){
            $(`#content-gyhd-${gyhd}`).parent().parent().remove();
            gyhd = gyhd - 1;
            $(".gyhd").val(gyhd);
        }
    })

    $("#btn-cancel-gyhd").click(function() {
        gyhd = 0;
        $(".gyhd").val("0");
        $(".render_UI_tab3").empty();
    })

    $("#btn-save-gyhd").click(function() {
        $("#form-gyhd").submit();
    })
    
</script>

<script type="text/javascript">
    $(function(){
        table2 = $('#table2').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            ajax: "{!! route('admin.thuongtruc.setstandard.dataChibao') !!}"  + "?id_tchi=" 
                    + '{{ $id_tchi }}',
            columns: [
                { data: 'mo_ta', name: 'mo_ta' },
                { data: 'tu_khoa', name: 'tu_khoa' },
                { data: 'ktmc', name: 'ktmc' },
                { data: 'kttp', name: 'kttp' },
                { data: 'gioihantu', name: 'gioihantu' },
                { data: 'actions', name: 'actions' },
            ],            
        });
    });  


    $('#modalDeleteCb').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget) 
        var id = button.data('id') 
        var route = "{{ route('admin.thuongtruc.setstandard.deleteChibao') }}" + "?id_delete=" + id;
        var modal = $(this);
        modal.find('#btn-delete-chibao').attr('href' , route);
    })

    

    $(".render_UI_tab2").on('click', ".checkBox_ktmc", function() {
        let saveVa = $(this).parent().find(".value_ktmc")
        if(saveVa.val() == 'N') {
            saveVa.val("Y");
        }else{
            saveVa.val("N");
        }
    })

    $(".render_UI_tab2").on('click', ".checkBox_kttp", function() {
        let saveVa = $(this).parent().find(".value_kttp")
        if(saveVa.val() == 'N') {
            saveVa.val("Y");
        }else{
            saveVa.val("N");
        }
    })


    $("#up_inputKtmc").on('click', function() {
        let saveVa = $(this).parent().find("#value_inputKtmc")
        if(saveVa.val() == 'N') {
            saveVa.val("Y");
        }else{
            saveVa.val("N");
        }
    })

    $("#up_inputKttp").on('click', function() {
        let saveVa = $(this).parent().find("#value_inputKttp")
        if(saveVa.val() == 'N') {
            saveVa.val("Y");
        }else{
            saveVa.val("N");
        }
    })

    
    $('#modalUpdateCb').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget)
        var id = button.data('id') 
        $("#id_chibao").val(id)
        var route = "{{ route('admin.thuongtruc.setstandard.dataChibao') }}" + 
                    "?id_cb=" + id;
        fetch(route, {
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
        })
            .then((response) => response.json())
            .then((data) => {
                $("#up_inputNdcb").val(data.mo_ta)
                $("#up_inputTk").val(data.tu_khoa)
                console.log(data.kiemtra_mc)
                if(data.kiemtra_mc == "Y"){
                    $("#up_inputKtmc").prop("checked", true);
                    $("#value_inputKtmc").val("Y")
                }else if(data.kiemtra_mc == "N"){
                    $("#up_inputKtmc").prop("checked", false);
                    $("#value_inputKtmc").val("N")
                }
                
                if(data.kiemtra_tp == "Y"){
                    $("#up_inputKttp").prop("checked", true);
                    $("#value_inputKttp").val("Y")
                }else if(data.kiemtra_tp == "N"){
                    $("#up_inputKttp").prop("checked", false);
                    $("#value_inputKttp").val("N")
                }
                
                $("#up_inputGioihan").val(data.gioihantu);
            })
    })
</script>


@stop
