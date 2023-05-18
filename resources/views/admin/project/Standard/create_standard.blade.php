@extends('admin/layouts/default')
{{-- Page title --}}
@section('title')
    @lang('project/Standard/title.tmtc')
@parent
@stop

{{-- page level styles --}}
@section('header_styles')
<link rel="stylesheet" href="{{ asset('css/project/Standard/standard.css') }}">

@stop

@section('title_page')
    @lang('project/Standard/title.tmtc')
@stop

@section('content')
    <section class="content indexpage pr-3 pl-3">
        <!-- Bắt đầu trang -->
        
                <section class="content-body">
                    <div class="form-standard">
                        <h4>@lang('project/Standard/title.btc')</h4>
                        <div class="create-standard">
                            <input disabled type="text" placeholder="@lang('project/Standard/title.tbtc')" value="{{ $tieu_de }}" class="form-control">
                            <select class="form-control" disabled>
                                <option hidden>@lang('project/Standard/title.ldg')</option>
                                <option value="csgd" 
                                    @if($loai_tieuchuan == "csgd")
                                        selected = ""
                                    @endif
                                 >@lang('project/Standard/title.csgd')</option>
                                <option value="ctdt" 
                                    @if($loai_tieuchuan == "ctdt")
                                        selected = ""
                                    @endif
                                 >@lang('project/Standard/title.ctdt')</option>
                            </select>
                        </div>
                        <h3>@lang('project/Standard/title.tmtc')</h3>
                        <form action="{{ route('admin.thuongtruc.setstandard.createLiStandard', $id) }}" method="post" class="container-fuild" id="form-create-standard">
                            @csrf
                            <div class="form-group row pl-4">
                                <div class="col-md-2">
                                    @lang('project/Standard/title.sltchuan')
                                </div>
                                <div class="col-md-2">
                                    <input type="number" class="form-control h-2rem" placeholder="@lang('project/Standard/title.sltchuan')" id="sltchuan">
                                </div>
                            </div>
                            <div class="block-content">
                                
                            </div>
                            <button type="button" class="ml-4 pl-3 pr-3 btn btn-submit"><i class="bi bi-save" style="font-size: 35px;color: #50cd89;"data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('project/Standard/title.luu')"></i></button>

                            <a href="{{ route('admin.thuongtruc.setstandard.configStandard', $id) }}" class="ml-4 pl-3 pr-3 btn"><i class="bi bi-backspace-fill" style="font-size: 35px;color: red;"data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('project/Standard/title.quaylai')"></i></a>
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
    var tieuchuan = "@lang('project/Standard/title.tieuchuan')";
    $("#sltchuan").change(function() {
        if($(this).val() <= 0){
            alert("@lang('project/Standard/title.nddd')");
        }else{
            $(".block-content").empty();
            for(let i = 1; i <= $(this).val(); i++){
                let tcrow = `
                    <div class="form-group row pl-4">
                        <div class="col-md-2">
                            ${tieuchuan} ${i}
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control" placeholder="${tieuchuan} ${i}" name="tieuchuan[]">
                        </div>
                    </div>
                `;
                $(".block-content").append(tcrow);
            }
            
        }
        
    })


    $(".btn-submit").click(function() {
        let check = false;
        for(let i = 0; i < $("input[name='tieuchuan[]']").length; i++){
            if($("input[name='tieuchuan[]']")[i].value != ""){
                check = true;
            }
        }
        if(check){
            $("#form-create-standard").submit();
        }else{
            alert("Vui lòng không để trống thông tin")
        }
        
    })
</script>

@stop
