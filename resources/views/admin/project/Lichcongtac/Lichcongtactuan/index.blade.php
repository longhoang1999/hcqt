@extends('admin/layouts/default')
{{-- Page title --}}
@section('title')
    @lang('project/Standard/title.qlbtc')
@parent
@stop

{{-- page level styles --}}
@section('header_styles')
<link rel="stylesheet" href="{{ asset('css/project/Standard/standard.css') }}">

@stop

@section('title_page')
    @lang('project/Lichcongtac/title.lcct')
@stop

@section('content')
    <!-- Nội dung section -->
    <div class="content-body">

    </div>

@stop



{{-- page level scripts --}}
@section('footer_scripts')
<script>
    var $url_path = '{!! url('/') !!}';

   
</script>

@stop
