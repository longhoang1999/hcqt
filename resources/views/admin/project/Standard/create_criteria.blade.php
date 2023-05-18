@extends('admin/layouts/default')
{{-- Page title --}}
@section('title')
    @lang('project/Standard/title.tmtchi')
@parent
@stop

{{-- page level styles --}}
@section('header_styles')

<link rel="stylesheet" href="{{ asset('css/project/Standard/standard.css') }}">

@stop

@section('title_page')
    @lang('project/Standard/title.tmtchi')
@stop

@section('content')
    <section class="content indexpage pr-3 pl-3">
        <!-- Bắt đầu trang -->
            <section class="content-body">
                <div class="form-standard">
                    <h4>@lang('project/Standard/title.btc')</h4>
                    <div class="create-standard">
                        <input disabled value="{{ $tieu_de }}" type="text" placeholder="@lang('project/Standard/title.tbtc')" class="form-control">
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
                    <form method="post" action="{{ route('admin.thuongtruc.setstandard.updateNameTC') }}" class="standard mb-4">
                        @csrf
                        <input type="hidden" name="id_tchuan" value="{{ $id_tc }}">
                        <h4 class="mr-2">@lang('project/Standard/title.tieuchuan') {{ $stt }}</h4>
                        <input type="text" placeholder="@lang('project/Standard/title.tieuchuan') {{ $stt }}" required class="form-control h-2rem" value="{{ $mo_ta }}" name="nameTC">
                        <button class="btn ">
                            <i class="bi bi-save" style="font-size: 35px;color: #50cd89;;" data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('project/Standard/title.upTchuan')"></i>
                            
                        </button>
                    </form>

                    <h3>@lang('project/Standard/title.tmtchi')</h3>
                    <form action="{{ route('admin.thuongtruc.setstandard.createCriteria',$id_tc) }}" method="post" class="container-fuild" id="form-create-criteria">
                        @csrf
                        <div class="form-group row pl-4">
                            <div class="col-md-2">
                                @lang('project/Standard/title.sltchi') 
                            </div>
                            <div class="col-md-2">
                                <input type="number" class="form-control h-2rem" placeholder="@lang('project/Standard/title.sltchi')" id="sltchi">
                            </div>
                        </div>
                        <div class="block-content">
                            
                        </div>
                        <button type="button" class="ml-4 pl-3 pr-3 btn btn-submit"><i class="bi bi-save" style="font-size: 35px;color: #009ef7;"data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('project/Standard/title.luu')"></i></button>
                    </form>
                    
                </div>
            </section>


        <!-- Kết thúc trang -->
    </section>
@stop



{{-- page level scripts --}}
@section('footer_scripts')
<script>
    var tieuchi = "@lang('project/Standard/title.tieuchi')";
    var sotchi  = "{{ $stt }}";
    var tcdk    = "@lang('project/Standard/title.tcdk')";
    $("#sltchi").change(function() {
        if($(this).val() <= 0){
            alert("@lang('project/Standard/title.nddd')");
        }else{
            $(".block-content").empty();
            for(let i = 1; i <= $(this).val(); i++){
                let tcrow = `
                    <div class="form-group row pl-4">
                        <div class="col-md-2">
                            ${tieuchi} ${sotchi}.${i}
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control" placeholder="${tieuchi} ${sotchi}.${i}" name="tieuchi[]">
                        </div>
                        <div class="col-md-2 display-flex">
                            <input type="hidden" value="off" name="tieuchidk[]" class="tieuchidk-hd">
                            <input type="checkbox" class="tieuchidk" id="tieuchi${sotchi}_${i}">
                            <label for="tieuchi${sotchi}_${i}">${tcdk}</label>
                        </div>
                    </div>
                `;
                $(".block-content").append(tcrow);
            }
            
        }
        
    })

    $(".btn-submit").click(function() {
        let listTc = document.querySelectorAll("input[name='tieuchi[]']");
        let check = false;
        for(let i = 0;i < listTc.length; i++){
            if(listTc[i].value != ""){
                check = true;
                break;
            }
        }

        if(listTc.length == 0 || !check){
            alert("@lang('project/Standard/title.vldddtt') ");
        }else{
            $("#form-create-criteria").submit();
        }
    })

    $(".block-content").on("click", ".tieuchidk", function() {
        if($(this).is(":checked")){
            $(this).parent().find(".tieuchidk-hd").val("on");
        }else{
            $(this).parent().find(".tieuchidk-hd").val("off");  
        }
    })
</script>



@stop
