@extends('admin/layouts/default')
{{-- Page title --}}
@section('title')
    @lang('project/Standard/title.qlctdt')
@parent
@stop

{{-- page level styles --}}
@section('header_styles')
<link rel="stylesheet" href="{{ asset('css/project/Standard/standard.css') }}">
<style>
    .select2-container .select2-selection--single{
        height: 44px;
    }
</style>
@stop

@section('title_page')
    @lang('project/Standard/title.qlctdt')
@stop

@section('content')
    <section class="content indexpage pr-3 pl-3">
        <!-- Bắt đầu trang -->

<!-- page trang ở đây -->
<section class="content-body">
    <form action="{{ route('admin.thuongtruc.manacategory.createCTDT') }}" method="post">
        @csrf
        <div class="container-fuild mt-3">
            <div class="row">
                <div class="col-md-2">
                    <span>@lang('project/Standard/title.mactdt')</span>
                </div>
                <div class="col-md-5">
                    <span>@lang('project/Standard/title.tctdttv')</span>
                </div>
                <div class="col-md-5">
                    <span>@lang('project/Standard/title.tctdtta')</span>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-2">
                    <input type="text" class="form-control " placeholder="@lang('project/Standard/title.mactdt')" name="mactdt">
                </div>
                <div class="col-md-5">
                    <input type="text" class="form-control " placeholder="@lang('project/Standard/title.tctdttv')" name="tctdttv">
                </div>
                <div class="col-md-3">
                    <input type="text" class="form-control " placeholder="@lang('project/Standard/title.tctdtta')" name="tctdtta">
                </div>
            </div>
        </div>
        <div class="container-fuild mt-3">
            <div class="row">
                <div class="col-md-2">
                    <span>@lang('project/Standard/title.hdt')</span>
                </div>
                <div class="col-md-5">
                    <span>@lang('project/Standard/title.dvql')</span>
                </div>
                <div class="col-md-5">
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-2">
                    <select class="form-control " name="hdt">
                        <option hidden value=""></option>
                        @foreach($hdt as $value)
                            <option value="{{ $value->id }}">
                                {{ $value->ten_hdt }}
                            </option>
                        @endforeach
                    </select>   
                </div>
                <div class="col-md-5">
                    <select class="form-control " name="dvql">
                        <option hidden value=""></option>
                        @foreach($loai_dv as $ldv)
                            <optgroup label="{{ $ldv->loai_donvi }}">
                                @foreach($donvi as $value)
                                    @if($value->loai_dv_id == $ldv->id)
                                        <option value="{{ $value->id }}">
                                            {{ $value->ten_donvi }}
                                        </option>
                                    @endif
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-5 item-group-button">
                    <button class="btn btn-benchmark mr-2 " type="button" id="btn-refress" data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('project/Standard/title.lammoi')">
                        <i class="bi bi-arrow-clockwise" style="font-size: 30px;color: red;"></i>
                    </button>
                    <button class="btn btn-benchmark mr-2 " type="submit" data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('project/Standard/title.luu')">
                        <i class="bi bi-save" style="font-size: 30px;color: #50cd89;"></i>
                    </button>
                    <a href="{{ route('admin.thuongtruc.manacategory.exportCTDT') }}" class="btn btn-benchmark mr-2" type="button" data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('project/Standard/title.xuat_excel')">
                        <i class="bi bi-file-earmark-excel " style="font-size: 35px;color: #50cd89;"></i>
                    </a>
                </div>
            </div>
        </div>
    </form>

    <h2 class="mt-3">
        @lang('project/Standard/title.dsctdt')
    </h2>
    <div class="form-standard">
        
        <table class="table table-striped table-bordered" id="table" width="100%">
            <thead>
             <tr>
                <th >@lang('project/Standard/title.mactdt')</th>
                <th >@lang('project/Standard/title.tctdttv')</th>
                <th >@lang('project/Standard/title.tctdtta')</th>
                <th >@lang('project/Standard/title.hdt')</th>
                <th >@lang('project/Standard/title.donvi')</th>
                <th >@lang('project/Standard/title.ngayt')</th>
                <th >@lang('project/Standard/title.nguoit')</th>
                <th >@lang('project/Standard/title.hanhd')</th>
             </tr>
            </thead>
            <tbody>  
            </tbody>                
        </table>
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
                <a href="#" class="btn btn-danger" id="btn-delete-csdt">
                    @lang('project/Standard/title.xoa')
                </a>
                <button type="button" class="btn btn-primary" data-dismiss="modal">
                    @lang('project/Standard/title.huy')
                </button>
            </div>
        </div>
    </div>
</div>


<!-- modal update -->
<div class="modal fade" id="modalUpdate" tabindex="-1" role="dialog" aria-labelledby="modalUpdateLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalUpdateLabel">
                    @lang('project/Standard/title.cnctdt')
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.thuongtruc.manacategory.updateCTDT') }}" method="post" id="update-ctdt">
                    <input type="hidden" name="id_update" id="id_update">
                    @csrf
                    <input type="hidden" id="id_unit" name="id_unit">
                    <div class="container-fuild">
                        <div class="row">
                            <div class="form-group col-md-2">
                                <label for="upMaCTDT">
                                    <span>@lang('project/Standard/title.mactdt')</span>
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control " id="upMaCTDT" placeholder="@lang('project/Standard/title.mactdt')" name="up_mactdt">
                            </div>
                            <div class="form-group col-md-5">
                                <label for="upTenCTDT">
                                    <span>@lang('project/Standard/title.tctdttv')</span>
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control " id="upTenCTDT" placeholder="@lang('project/Standard/title.tctdttv')" name="up_tenctdt">
                            </div>
                            <div class="form-group col-md-5">
                                <label for="upTenCTDT_en">
                                    <span>@lang('project/Standard/title.tctdtta')</span>
                                    <!-- <span class="text-danger">*</span> -->
                                </label>
                                <input type="text" class="form-control " id="upTenCTDT_en" placeholder="@lang('project/Standard/title.tctdtta')" name="up_tenctdten">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-2">
                                <label for="upHdtCTDT">
                                    <span>@lang('project/Standard/title.hdt')</span>
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="form-control " id="upHdtCTDT" name="up_hdt">
                                    @foreach($hdt as $value)
                                        <option value="{{ $value->id }}">
                                            {{ $value->ten_hdt }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-5">
                                <label for="upDvCTDT">
                                    <span>@lang('project/Standard/title.dvql')</span>
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="form-control " id="upDvCTDT" name="up_dvql">
                                    @foreach($loai_dv as $ldv)
                                        <optgroup label="{{ $ldv->loai_donvi }}">
                                            @foreach($donvi as $value)
                                                @if($value->loai_dv_id == $ldv->id)
                                                    <option value="{{ $value->id }}">
                                                        {{ $value->ten_donvi }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-5">
                                <label for="upSdtCTDT">
                                    <span>@lang('project/Standard/title.sdt')</span>
                                    <!-- <span class="text-danger">*</span> -->
                                </label>
                                <input type="text" class="form-control " id="upSdtCTDT" placeholder="@lang('project/Standard/title.sdt')" name="up_sdt">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="upDtkhoa1">
                                    <span>@lang('project/Standard/title.tgkhoa1')</span>
                                </label>
                                <input type="number" class="form-control " id="upDtkhoa1" placeholder="@lang('project/Standard/title.tgkhoa1')" name="up_dtKhoa1">
                            </div>
                            <div class="form-group col-md-2">
                                <label for="upCbkhoa1">
                                    <span>@lang('project/Standard/title.cbkhoa1')</span>
                                </label>
                                <input type="number" class="form-control " id="upCbkhoa1" placeholder="@lang('project/Standard/title.cbkhoa1')" name="up_cbKhoa1">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="upTentd">
                                    <span>@lang('project/Standard/title.tenTd')</span>
                                </label>
                                <input type="text" class="form-control " id="upTentd" placeholder="@lang('project/Standard/title.tenTd')" name="up_tenTd">
                            </div>
                        </div>
                    </div> 
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="btn-update-ctdt">
                    @lang('project/Standard/title.thaydoi')
                </button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    @lang('project/Standard/title.huy')
                </button>
            </div>
        </div>
    </div>
</div>
<!-- /Kết thúc page trang -->
    
    <!-- Kết thúc trang -->
    </section>
@stop



{{-- page level scripts --}}
@section('footer_scripts')
<script>
    $(function(){
        table = $('#table').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            ajax: "{!! route('admin.thuongtruc.manacategory.datactdt') !!}",
            columns: [
                { data: 'ma_ctdt', name: 'ma_ctdt' },
                { data: 'tennganh', name: 'tennganh' },
                { data: 'tennganh_en', name: 'tennganh_en' },
                { data: 'hedaotao', name: 'hedaotao' },
                { data: 'donvi', name: 'donvi' },
                { data: 'createAt', name: 'createAt' },
                { data: 'createHuman', name: 'createHuman' },
                { data: 'actions', name: 'actions' ,className: 'action'},
            ],            
        });
    });

    $("#btn-refress").click(function() {
        $("input[name='mactdt']").val("");
        $("input[name='tctdttv']").val("");
        $("input[name='tctdtta']").val("");
        $("select[name='hdt']").val("0");
        $("select[name='dvql']").val("0");
    });

    $('#modalDelete').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget) 
        var id = button.data('id') 
        var route = "{{ route('admin.thuongtruc.manacategory.deleteCTDT') }}" + "?id_delete=" + id;
        var modal = $(this);
        modal.find('#btn-delete-csdt').attr('href' , route);
    })

    $('#modalUpdate').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget) 
        var id = button.data('id') 
        var route = "{{ route('admin.thuongtruc.manacategory.datactdt') }}" + "?id_search=" + id;
        fetch(route, {
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
        })
            .then((response) => response.json())
            .then((data) => {
                $("input[name='up_mactdt']").val(data.ma_ctdt);
                $("input[name='up_tenctdt']").val(data.tennganh);
                $("input[name='up_tenctdten']").val(data.tennganh_en);
                $("select[name='up_hdt']").val(data.hedaotao_id);
                $("select[name='up_dvql']").val(data.donvi_id);
                $("input[name='up_sdt']").val(data.sdt_lienhe);
                $("input[name='id_update']").val(data.id);
                $("#upTentd").val(data.tentd);
                $("#upCbkhoa1").val(data.ncbkhoa1);
                $("#upDtkhoa1").val(data.dtkhoa1); 
            })
    })
    
    $("#btn-update-ctdt").click(function() {
        $("#update-ctdt").submit();
    })
    $('select[name="hdt"]').select2({
        placeholder: "@lang('project/Standard/title.hdt')",
        allowClear: false
    });
    $('select[name="dvql"]').select2({
        placeholder: "@lang('project/Standard/title.dvql')",
        allowClear: false
    });
    $('select[name="up_dvql"]').select2({
        placeholder: "@lang('project/Standard/title.up_dvql')",
        allowClear: false
    });
</script>
@stop
