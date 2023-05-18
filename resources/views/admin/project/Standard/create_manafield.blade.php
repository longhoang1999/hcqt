@extends('admin/layouts/default')
{{-- Page title --}}
@section('title')
    @lang('project/Standard/title.tmlv')
@parent
@stop

{{-- page level styles --}}
@section('header_styles')
<link rel="stylesheet" href="{{ asset('css/project/Standard/standard.css') }}">
<style>
    
</style>
@stop

@section('title_page')
    @lang('project/Standard/title.tmlv')
@stop
@section('content')
    <section class="content indexpage pr-3 pl-3">
        <!-- Bắt đầu trang -->

<!-- page trang ở đây -->
<section class="content-body">
    <div class="form-standard">
        <form action="{{ route('admin.thuongtruc.manacategory.createManafields') }}" method="post" class="container-fuild" id="form-create">
            @csrf
            <div class="form-group row pl-4">
                <div class="col-md-2">
                    @lang('project/Standard/title.sllvuc') 
                </div>
                <div class="col-md-2">
                    <input type="number" class="form-control " placeholder="@lang('project/Standard/title.sllvuc')" id="input-linhvuc" value="0">
                </div>
            </div>
            <div class="block_render container-fuild">
                
            </div>
            <div class="item-group-button right-block w-50percent">
                <button class="btn btn-benchmark mr-2" type="button" id="btn-cancel">
                    <i class="bi bi-x-circle" style="font-size: 30px;color: red;"></i>
                </button>
                <button class="btn btn-benchmark mr-2" type="button" id="btn-save" data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('project/Standard/title.luu')">
                    <i class="bi bi-save" style="font-size: 30px;color: #50cd89;"></i>
                </button>
            </div>
        </form>
    </div>
</section>
<!-- /Kết thúc page trang -->

    <!-- Kết thúc trang -->
    </section>
@stop



{{-- page level scripts --}}
@section('footer_scripts')
<script type="text/javascript">
    var linhvuc = "@lang('project/Standard/title.linhvuc')";
    var dvpt = "@lang('project/Standard/title.dvpt')";
    $("#input-linhvuc").change(function() {
        if($(this).val() <= 0){
            alert("@lang('project/Standard/title.nddd')");
        }else{
            $(".block_render").empty();
            for(let i = 1; i <= $(this).val(); i++){
                let tcrow = `
                    <div class="form-group row pl-4">
                        <div class="col-md-1">
                            ${linhvuc} ${i}
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control " placeholder="${linhvuc} ${i}" name="linhvuc[]">
                        </div>
                        <div class="col-md-1">
                            ${dvpt}
                        </div>
                        <div class="col-md-4">
                            <select class="form-control " name="donvi[]">
                                @foreach($donvi as $dv)
                                    <option value="{{ $dv->id }}">{{ $dv->ten_donvi }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                `;
                $(".block_render").append(tcrow);
            }
            
        }
    })

    $("#btn-save").click(function() {
        $("#form-create").submit();
    })

    $("#btn-cancel").click(function() {
        $("#input-linhvuc").val("0");
        $(".block_render").empty();
    })
</script>

@stop
