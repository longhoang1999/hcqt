@extends('admin/layouts/default')
{{-- Page title --}}
@section('title')
    @lang('project/Standard/title.tmdtds')
@parent
@stop

{{-- page level styles --}}
@section('header_styles')
<link rel="stylesheet" href="{{ asset('css/project/Standard/standard.css') }}">

@stop

@section('title_page')
    @lang('project/Standard/title.tmdtds')
@stop

@section('content')
    <section class="content indexpage pr-3 pl-3">
        <!-- Bắt đầu trang -->
<!-- page trang ở đây -->
<section class="content-body">
    <div class="form-standard">
        <h4>@lang('project/Standard/title.tcds')</h4>
        <div class="create-standard">
            <input type="text" placeholder="@lang('project/Standard/title.ndtcds')">
            <select name="" id="">
                <option hidden>@lang('project/Standard/title.lds')</option>
            </select>
        </div>
    </div>
    <h2 class="mt-3">
        @lang('project/Standard/title.tmdt')
    </h2>
    <div class="form-standard">
        <form action="" class="container-fuild pl-4">
            @csrf
            <div class="form-group row">
                <div class="col-md-2">
                    <span>Số lượng</span>
                </div>
                <div class="col-md-3">
                    <input type="number" name="" placeholder="Số lượng" class="form-control h-2rem">
                </div>
            </div>
            <div class="form-group row">
                <div class="col-md-2">
                    <span>Đối tượng 1</span>
                </div>
                <div class="col-md-6">
                    <input type="text" name="" placeholder="Nội dung đối tượng đối sách" class="form-control h-2rem">
                </div>
            </div>
            <div class="form-group row">
                <div class="col-md-2">
                    <span>Đối tượng 2</span>
                </div>
                <div class="col-md-6">
                    <input type="text" name="" placeholder="Nội dung đối tượng đối sách" class="form-control h-2rem">
                </div>
            </div>
            <div class="form-group row">
                <div class="col-md-2">
                    <span>Đối tượng 3</span>
                </div>
                <div class="col-md-6">
                    <input type="text" name="" placeholder="Nội dung đối tượng đối sách" class="form-control h-2rem">
                </div>
            </div>
            <button class="btn btn-success btn-benchmark mr-2 h-2rem" type="button">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-down mr-2" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M3.5 10a.5.5 0 0 1-.5-.5v-8a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 .5.5v8a.5.5 0 0 1-.5.5h-2a.5.5 0 0 0 0 1h2A1.5 1.5 0 0 0 14 9.5v-8A1.5 1.5 0 0 0 12.5 0h-9A1.5 1.5 0 0 0 2 1.5v8A1.5 1.5 0 0 0 3.5 11h2a.5.5 0 0 0 0-1h-2z"/>
                    <path fill-rule="evenodd" d="M7.646 15.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 14.293V5.5a.5.5 0 0 0-1 0v8.793l-2.146-2.147a.5.5 0 0 0-.708.708l3 3z"/>
                </svg>
                <span>@lang('project/Standard/title.save')</span>
            </button>
        </form>
    </div>
</section>
<!-- /Kết thúc page trang -->
    
    <!-- Kết thúc trang -->
    </section>
@stop



{{-- page level scripts --}}
@section('footer_scripts')

@stop
