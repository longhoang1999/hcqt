@extends('admin/layouts/default')

{{-- Page title --}}
@section('title')
    Home
@parent
@stop

{{-- page level styles --}}
@section('header_styles')

@stop

@section('title_page')
    @lang('default.trangchu')
@stop

@section('content')
    <section class="content indexpage pr-3 pl-3">
        <div class="row">
            <h1 class="col-md-12 real-time"></h1>
        </div>
    </section>
@stop

{{-- page level scripts --}}
@section('footer_scripts')



<!--//jquery-ui-->
<script type="text/javascript">
    var h1 = document.querySelector(".real-time");
    let month;
    let day;
    let year;
    let hour;
    let minute;
    let second;

    function convertTime(number) {
        if(number >= 0 && number <= 9){
            number = `0${number}`;
        }
        return number;
    }
    function loadRealTime() {
        let dateObj = new Date();
        month = dateObj.getMonth() + 1;
        day = dateObj.getDate();
        year = dateObj.getFullYear();
        hour = convertTime(dateObj.getHours());
        minute = convertTime(dateObj.getMinutes());
        second = convertTime(dateObj.getSeconds());

        let newdate = `${day}/${month}/${year}  ${hour}:${minute}:${second}`;
        h1.innerText = newdate;
    }
    loadRealTime();

    setInterval(() => {
        let dateObj = new Date();
        month = dateObj.getMonth() + 1;
        day = dateObj.getDate();
        year = dateObj.getFullYear();
        hour = convertTime(dateObj.getHours());
        minute = convertTime(dateObj.getMinutes());
        second = convertTime(dateObj.getSeconds());

        let newdate = `${day}/${month}/${year}  ${hour}:${minute}:${second}`;
        h1.innerText = newdate;
    }, 1000)
</script>
@stop
