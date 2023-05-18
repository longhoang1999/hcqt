@extends('admin/layouts/default')
{{-- Page title --}}
@section('title')
    @lang('project/Standard/title.qltcds')
@parent
@stop

{{-- page level styles --}}
@section('header_styles')
<link rel="stylesheet" href="{{ asset('css/project/Standard/standard.css') }}">

@stop

@section('title_page')
   @lang('project/Standard/title.qltcds')
@stop

@section('content')
    <section class="content indexpage pr-3 pl-3">
        <!-- Bắt đầu trang -->
<!-- page trang ở đây -->
<section class="content-body">
    <div class="form-standard">
        <h4>@lang('project/Standard/title.tcds')</h4>
        <div class="create-standard">
            <input type="text" placeholder="@lang('project/Standard/title.tcds')">
            <select name="" id="">
                <option hidden>@lang('project/Standard/title.lds')</option>
            </select>
            <button>@lang('project/Standard/title.luu')</button>
        </div>
    </div>
    <h2 class="mt-3">
        @lang('project/Standard/title.dstcds')
    </h2>
    <div class="form-standard">
        <div class="item-group-button right-block mb-2">
            <button class="btn btn-success btn-benchmark mr-2" type="button">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark-excel mr-2" viewBox="0 0 16 16">
                    <path d="M5.884 6.68a.5.5 0 1 0-.768.64L7.349 10l-2.233 2.68a.5.5 0 0 0 .768.64L8 10.781l2.116 2.54a.5.5 0 0 0 .768-.641L8.651 10l2.233-2.68a.5.5 0 0 0-.768-.64L8 9.219l-2.116-2.54z"/>
                    <path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5v2z"/>
                </svg>
                <span>@lang('project/Standard/title.xuat_excel')</span>
            </button>
        </div>
        
        <table class="table table-striped table-bordered" id="table" width="100%">
            <thead>
             <tr>
                <th >@lang('project/Standard/title.ttcdt')</th>
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
            ajax: "{!! route('admin.thuongtruc.setstandard.data') !!}",
            columns: [
                { data: 'name', name: 'name' },
                { data: 'created_at', name: 'created_at' },
                { data: 'human', name: 'human' },
                { data: 'actions', name: 'actions' },
            ],            
        });
    });  
</script>

@stop
