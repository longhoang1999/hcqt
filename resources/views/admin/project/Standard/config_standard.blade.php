@extends('admin/layouts/default')
{{-- Page title --}}
@section('title')
    @lang('project/Standard/title.chbtc')
@parent
@stop

{{-- page level styles --}}
@section('header_styles')
<link rel="stylesheet" href="{{ asset('css/project/Standard/standard.css') }}">
<link rel="stylesheet" href="{{ asset('css/project/custom-datatable.css') }}">
<style>
    .copy-standard{
        display: none;
    }
    .w-6rem{
        width: 7rem !important;
    }
    .form-standard{
        margin-left: 13px;
    }
    .form-control{
        appearance: auto !important;
    }
    .row5{
        width: auto !important;
    }

</style>
@stop

@section('title_page')
    @lang('project/Standard/title.chbtc')
@stop

@section('content')
@php
    use Carbon\Carbon;
@endphp
    <section class="content indexpage">
        <!-- Bắt đầu trang -->
        <div class="form-standard">
            <form action="{{ route('admin.thuongtruc.setstandard.updateStandard') }}" method="post">
                @csrf
                <input type="hidden" value="{{ $id }}" name="id">
                <h4>@lang('project/Standard/title.ttbtc')</h4>
                <div class="create-standard">
                    <input type="text" placeholder="@lang('project/Standard/title.tbtc')" class="form-control" name="tbtc" value="{{ $title }}">
                    <select name="ldg" class="form-control"> 
                        <option hidden>@lang('project/Standard/title.ldg')</option>
                        @foreach($ltcs as $key => $ltc)
                            <option value="{{ $key }}" 
                                @if($ldg == $key)
                                    selected = ""
                                @endif
                             >{{ $ltc }}</option>
                        @endforeach
                    </select>
                    <button class="btn"><i class="bi bi-pencil-square" style="font-size: 30px;color: #50cd89;"  data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('project/Standard/title.chinhsua')"></i></button>
                </div>
            </form>
            <!-- search -->
            <h4>@lang('project/Standard/title.sctcc')</h4>
            <div class="create-standard">
                <select id="ldg" class="form-control"> 
                    <option hidden>@lang('project/Standard/title.ldg')</option>
                    @foreach($ltcs as $key => $ltc)
                        <option value="{{ $key }}" 
                            @if($ldg == $key)
                                selected = ""
                            @endif
                         >{{ $ltc }}</option>
                    @endforeach
                </select>

                <select id="tbtc" class="form-control"> 
                    <option value="{{ $id }}" selected>{{ $title }}</option>
                </select>

                <select id="year" class="form-control w-6rem"> 
                    <option value="" hidden>@lang('project/Standard/title.nam')</option>
                    @for($i = Carbon::now()->year; $i >= 2016;  $i--)
                        <option value="{{ $i }}">{{ $i }}</option>
                    @endfor
                </select>
                <button class="btn" id="btn-search"><i class="bi bi-search" style="font-size: 30px;color: #5014d0;" data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('project/Standard/title.timkiem')"></i></button>
            </div>
            <!-- result search -->
            <div class="copy-standard">
                <table class="table table-striped table-bordered">
                  <thead class="thead-light">
                    <tr>
                      <th scope="col">#</th>
                      <th scope="col">
                          @lang('project/Standard/title.ttc')
                      </th>
                      <th scope="col">
                          @lang('project/Standard/title.ngayt')
                      </th>
                      <th scope="col">
                          @lang('project/Standard/title.nguoit')
                      </th>
                      <th scope="col">
                          <input type="checkbox" id="checkall" class="form-control">
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    
                  </tbody>
                </table>
            </div>

            <h3>@lang('project/Standard/title.dstc')</h3>
            <div class="right-block item-group-button mb-2">
                <button type="button" class="btn mr-2" data-toggle="modal" data-target="#modalStt">
                    <i class="bi bi-arrow-down-up" style="font-size: 30px;color: #5014d0;"data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('project/Standard/title.sxstt')"></i>
                </button>
                <a href="{{ route('admin.thuongtruc.setstandard.createSgStandard', $id) }}" class="btn mr-2" type="button">
                    
                    <i class="bi bi-plus-square" style="font-size: 30px;color: #009ef7;" data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('project/Standard/title.tmtc')"></i>
                </a>
                <a href="{{ route('admin.thuongtruc.setstandard.ExportSgStandard', $id) }}" class="btn " type="button">
                    <i class="bi bi-file-earmark-excel " style="font-size: 30px;color: #47be7d;" data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('project/Standard/title.xuat_excel')"></i>
                </a>
            </div>
            <table class="table table-striped table-bordered" id="table" width="100%">
                <thead>
                 <tr>
                    <th >@lang('project/Standard/title.sohieutc')</th>           
                    <th >@lang('project/Standard/title.ngayt')</th>
                    <th >@lang('project/Standard/title.nguoit')</th>
                    <th >@lang('project/Standard/title.ttc')</th>
                    <th style="width: 12%;">@lang('project/Standard/title.hanhd')</th>
                 </tr>
                </thead>
                <tbody>  
                </tbody>                
            </table> 
        </div>
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
                <span class="badge badge-danger">
                    @lang('project/Standard/message.error.hoixoa')
                </span>
                <br>
                <span class="badge badge-primary">
                    @lang('project/Standard/message.error.khoantac')
                </span>
            </div>
            <div class="modal-footer">
                <a href="" class="btn btn-danger" id="delete-standard">
                    @lang('project/Standard/title.xoa')
                </a>
                <button type="button" class="btn btn-primary" data-dismiss="modal">
                    @lang('project/Standard/title.huy')
                </button>
            </div>
        </div>
    </div>
</div>

<!-- modal delete Tiêu chí -->
<div class="modal fade" id="deleteTieuchi" tabindex="-1" role="dialog" aria-labelledby="deleteTieuchiLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteTieuchiLabel">
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
                <a href="#" class="btn btn-danger" id="btn-delete-criteria">
                    @lang('project/Standard/title.xoa')
                </a>
                <button type="button" class="btn btn-primary" data-dismiss="modal">
                    @lang('project/Standard/title.huy')
                </button>
            </div>
        </div>
    </div>
</div>

<!-- modal edit Stt -->
<div class="modal fade" id="modalStt" tabindex="-1" role="dialog" aria-labelledby="modalSttLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalSttLabel">
                    @lang('project/Standard/title.sxstt')
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.thuongtruc.setstandard.searchVSUpdateSttSt2') }}" method="post">
                @csrf
                <div class="modal-body">
                    <table class="table tbw-100 table-striped table-bordered" >
                      <thead class="thead-light">
                        <tr>
                          <th scope="col">
                            @lang('project/Standard/title.ttc_tc')
                          </th>
                          <th scope="col">
                            @lang('project/Standard/title.sohieutc')
                          </th>
                        </tr>
                      </thead>
                      <tbody>
                        
                      </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-change-stt">
                        @lang('project/Standard/title.thaydoi')
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
<script>
    var $url_path = '{!! url('/') !!}';

    $(function(){
        table = $('#table').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            ajax: "{!! route('admin.thuongtruc.setstandard.showStandard', $id) !!}",
            columns: [
                { data: 'sohieutc', name: 'sohieutc', className: 'dt-sohieutc dt-control' },               
                { data: 'createAt', name: 'createAt', className: 'dt-createAt' },
                { data: 'createHuman', name: 'createHuman', className: 'dt-createHuman' },
                { data: 'nameTC', name: 'nameTC' , className: ''},
                { 
                    data: 'actions', 
                    name: 'actions', 
                    className: 'action', 
                },
            ],            
        });


        $('#table tbody').on('click', 'td.dt-control', function () {
            var tr = $(this).closest('tr');
            var row = table.row(tr);
            if (row.child.isShown()) {
                // This row is already open - close it
                row.child.hide();
                tr.removeClass('shown');
            } else {
                // Open this row
                let id_tc = row.data().id;
                let routeApi = "{{ route('admin.thuongtruc.setstandard.showCriteria') }}" + "?idtc="  +  id_tc;
                fetch(routeApi, {
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                })
                    .then((response) => response.json())
                    .then((data) => {
                        let trrow = "";
                        data.forEach(item => {
                            trrow = trrow + `
                                <tr class="diplay_color">
                                    <td class = "row4">${item.sohieu}</td>     
                                    <td class = "row2">${item.createAt}</td>
                                    <td class = "row3">${item.createHuman}</td>
                                    <td class = "row1">${item.mo_ta}</td>
                                    <td class = "row5">
                                            <div class="d-flex align-items-center">
                                                <a href="{{ route('admin.thuongtruc.setstandard.criteria') }}?id_tchi=${item.id_tchi}" class="btn"data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('project/Standard/title.thietlap')">
                                                    <i class="bi bi-gear-fill" style="font-size: 25px;color: #009ef7;"></i>
                                                </a>
                                                <br/>
                                                <a href="#" class="btn" data-toggle="modal" data-target="#deleteTieuchi" data-id="${item.id_tchi}"
                                                >
                                                    <i class="bi bi-trash" style="font-size: 25px;color: red;"></i>
                                                </a>
                                            </div>
                                    </td>
                                </tr>
                            `;
                        });
                        return trrow;
                    })
                    .then((trrow) => {
                        if(trrow == ""){
                            trrow = `
                                <tr>
                                    <td class="font-weight-bold">
                                        @lang('project/Standard/title.kcdltc')
                                    </td>
                                </tr>
                            `;
                        }
                        let UI =  `
                            <table class="table table-hover table-more">
                                <tbody>
                                    ${trrow}
                                </tbody>
                            </table>
                        `;
                        row.child(UI).show();
                    });
                tr.addClass('shown');
            }
        });
    });  

    // Xóa tiêu chuẩn
    $('#modalDelete').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget)
        var id = button.data('id')
        var routeDelete = $url_path + "/admin/thuong-truc/setstandard/delete-single-standard/" + id;
        var modal = $(this)
        modal.find('#delete-standard').attr('href' ,routeDelete)
    })

    // Xóa tiêu chí
    $('#deleteTieuchi').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget)
        var id = button.data('id') 
        var routeDelete = "{{ route('admin.thuongtruc.setstandard.deleteSgCriteria') }}" + "?idtchi="  +  id;
        var modal = $(this)
        modal.find('#btn-delete-criteria').attr("href", routeDelete);
    })


    // tìm kiếm and sao chép
    let data = {
        'ldg' : $("#ldg").val(),
        'id_'  : '{{ $id }}'
    }
    let routeApi = "{{ route('admin.thuongtruc.setstandard.searchStandardName') }}";
    fetch(routeApi, {
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        method: 'POST',
        body: JSON.stringify(data)
    })
        .then((response) => response.json())
        .then((data) => {
            // $("#tbtc").empty();
            data.forEach(item => {
                let opt = `<option value="${item.id}" >${item.tieu_de}</option>`;
                $("#tbtc").append(opt);
            })
        })

    $("#ldg").change(function() {
        let data = {
            'type' : $(this).val(),
        }
        let routeApi = "{{ route('admin.thuongtruc.setstandard.searchStandardName') }}";
        fetch(routeApi, {
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            method: 'POST',
            body: JSON.stringify(data)
        })
            .then((response) => response.json())
            .then((data) => {
                $("#tbtc").empty();
                data.forEach(item => {
                    let opt = `<option value="${item.id}" >${item.tieu_de}</option>`;
                    $("#tbtc").append(opt);
                })
            })
    })

    document.addEventListener('keydown', function(){
        $("#btn-search").click();
    })

    $("#btn-search").click(function() {
        let data = {
            'type_search' : $("#ldg").val(),
            'id_btc'         : $("#tbtc").val(),
            'year'          : $("#year").val()
        }
        let routeApi = "{{ route('admin.thuongtruc.setstandard.searchStandardName') }}";
        fetch(routeApi, {
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            method: 'POST',
            body: JSON.stringify(data)
        })
            .then((response) => response.json())
            .then((data) => {
                $(".copy-standard").css("display", "block");
                $(".copy-standard tbody").empty();
                data.forEach((item, index) => {
                    let ui = `<tr>
                        <th scope="row">${index + 1}</th>
                        <td>${item.mo_ta}</td>
                        <td class="text-center">${item.created_at}</td>
                        <td class="text-center">${item.name}</td>
                        <td class="text-center">
                            <input type="checkbox" value="${item.id}" name="check-item[]" class="form-control">
                        </td>
                    </tr>`;
                    $(".copy-standard tbody").append(ui);
                }) 
            })
            .then(() => {
                let ui_last = `<tr>
                    <th scope="row"></th>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="text-center">
                        <button id="copy-tc" class="btn btn-primary">
                            @lang('project/Standard/title.saochep')
                        </button>
                    </td>
                </tr>`;
                $(".copy-standard tbody").append(ui_last);
            })
    })
</script>

<script type="text/javascript">
    $('#checkall').click(function () {   
        $("input[name='check-item[]']").prop('checked', this.checked);    
    });
    $(".copy-standard tbody").on("click", "input[name='check-item[]']", function() {
        // if(!$(this).is(':checked')){
        //     $('#checkall').prop('checked', false);
        // }
        var sList = true;
        $("input[name='check-item[]").each(function () {
            if(!this.checked){
                sList = false;
            }
        });
        $('#checkall').prop('checked', sList);
    })

    $(".copy-standard tbody").on("click", "#copy-tc", function() {
        var list = $("input[name='check-item[]']:checked").map(function () {
            return this.value;
        }).get();
        let data = {
            'id_copy' : list,
            'id_'  : '{{ $id }}'
        }
        let routeApi = "{{ route('admin.thuongtruc.setstandard.copyStandar') }}";
        fetch(routeApi, {
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            method: 'POST',
            body: JSON.stringify(data)
        })
            .then((response) => response.json())
            .then((data) => {
                if(data.message == "ok"){
                    $(".copy-standard").css("display", "none");
                    table.ajax.reload();
                }
            })
    })


    // open modal change stt
    table2 = $('#modalStt .table').DataTable({
        responsive: true,
        processing: true,
        serverSide: true,
        ajax: {
            'type': 'GET',
            'url': "{{ route('admin.thuongtruc.setstandard.searchVSUpdateSttSt') }}" + "?idbtc_search=" + "{{ $id }}",
        },
        columns: [
            { data: 'mo_ta', name: 'mo_ta' , className: 'control'},
            { data: 'stt_custom', name: 'stt_custom', className: 'stt_custom' },
        ],            
    });
    $('#modalStt').on('show.bs.modal', function (event) {
        table2.ajax.reload();
    })



    $('#modalStt .table tbody').on('click', 'td.control', function () {
        var tr = $(this).closest('tr');
        var row = table2.row(tr);
        if (row.child.isShown()) {
            row.child.hide();
            tr.removeClass('shown');
        } else {
            let id_tc = row.data().id;
            let routeApi = "{{ route('admin.thuongtruc.setstandard.showCriteria') }}" + "?idtc="  +  id_tc;
            fetch(routeApi, {
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
            })
                .then((response) => response.json())
                .then((data) => {
                    let trrow = "";
                    data.forEach(item => {
                        trrow = trrow + `
                            <tr>
                                <td class = "row1">${item.mo_ta}</td>
                                <td class = "row2" data-id="${item.id_tchi}">
                                    <input type='number' class='form-control input-stt' value='${item.stt}''>
                                </td>
                            </tr>
                        `;
                    });
                    return trrow;
                })
                .then((trrow) => {
                    if(trrow == ""){
                        trrow = `
                            <tr>
                                <td class="font-weight-bold">
                                    @lang('project/Standard/title.kcdltc')
                                </td>
                            </tr>
                        `;
                    }
                    let UI =  `
                        <table class="table table-hover table-more">
                            <tbody>
                                ${trrow}
                            </tbody>
                        </table>
                    `;
                    row.child(UI).show();
                });
            tr.addClass('shown');
        }
    });


    $('#modalStt .table tbody').on("change", ".input-stt", function() {
        let id_tieuchi = $(this).parent().data("id");
        let dataObj = {
            'id_tieuchi' : id_tieuchi,
            'stt'  : $(this).val()
        }
        let routeApi = "{{ route('admin.thuongtruc.setstandard.updateSttCriteria') }}";
        fetch(routeApi, {
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            method: 'POST',
            body: JSON.stringify(dataObj)
        })
            .then((response) => response.json())
            .then((data) => {
                if(data.message == "ok"){
                    table2.ajax.reload();
                    table.ajax.reload();
                }
            })
    })
</script>
@stop
