<link rel="stylesheet" href="{{ asset('css/custom_horizontal.css') }}">
<?php
    $listmenu_2 = Lang::get('menu.2_list');
    $listmenu_3 = Lang::get('menu.3_list');
    $listmenu_4 = Lang::get('menu.4_list');
    $listmenu_5 = Lang::get('menu.5_list');
    $listmenu_6 = Lang::get('menu.6_list');
    $listmenu_7 = Lang::get('menu.7_list');
    $listmenu_8 = Lang::get('menu.8_list');
    $listmenu_9 = Lang::get('menu.9_list');
    $listmenu_2_1 = Lang::get('menu.2_1_list');
    $listmenu_2_2 = Lang::get('menu.2_2_list');
    $listmenu_2_3 = Lang::get('menu.2_3_list');
    // link
    $linkMenuTwoParent = Lang::get('menu.2_list_parent');
    $linkMenuTwoChild = Lang::get('menu.2_list_child');

    $linkMenuThreeParent = Lang::get('menu.3_list_parent');
    $linkMenuFourParent = Lang::get('menu.4_list_parent');
    $linkMenuSevenParent = Lang::get('menu.7_list_parent');

    $listmenu_7_1 = Lang::get('menu.7_1_list');
    $linkMenuSevenChild = Lang::get('menu.7_list_child');
?>

<ul id="navigation" class="slimmenu">    
    <li class="main-menu option-one"><a href="javascript:void(0)">@lang('menu.2')</a>
        <ul>
            @foreach($listmenu_2 as $key => $menu)
                <li class="none-icon-default">
                    <a href="{{ $linkMenuTwoParent[$key] }}" class="none-icon-down">
                        <span class="title">{{$menu}}</span>
                        <span class="fa arrow"></span>
                    </a>
                    <ul class="sub-menu">
                        @if($key  == '1')
                            @foreach($listmenu_2_1 as $key_child => $menu_child)
                                <li>
                                    <a href="{{ $linkMenuTwoChild[$key_child] }}">
                                        <i class="fa fa-angle-double-right"></i>
                                        {{ $menu_child }}
                                    </a>
                                </li>
                            @endforeach
                        @elseif($key  == '2')
                            @foreach($listmenu_2_2 as $key_child => $menu_child)
                                @php
                                    $key_child = $key_child + count($listmenu_2_1);
                                @endphp
                                <li>
                                    <a href="{{ $linkMenuTwoChild[$key_child] }}">
                                        <i class="fa fa-angle-double-right"></i>
                                        {{ $menu_child }}
                                    </a>
                                </li>
                            @endforeach
                        @elseif($key  == '3')
                            @foreach($listmenu_2_3 as $key_child => $menu_child)
                                @php
                                    $key_child = $key_child + count($listmenu_2_1) + count($listmenu_2_2);
                                @endphp
                                <li>
                                    <a href="{{ $linkMenuTwoChild[$key_child] }}">
                                        <i class="fa fa-angle-double-right"></i>
                                        {{ $menu_child }}
                                    </a>
                                </li>
                            @endforeach
                        @endif
                    </ul>
                </li>
            @endforeach            
        </ul>
    </li>

    <li style="width: 170px !important;" class="main-menu option-one">
        <a href="javascript:void(0)">@lang('menu.3')</a>
        <ul>
            @foreach($listmenu_3 as $key => $menu)
                <li>
                   <a href="{{ $linkMenuThreeParent[$key] }}" >{{$menu}}</a>
                </li>
            @endforeach            
        </ul>
    </li>

    <li style="width: 170px !important;" class="main-menu option-one">
        <a href="javascript:void(0)">@lang('menu.4')</a>
        <ul>
            @foreach($listmenu_4 as $key => $menu)
                <li>
                   <a href="{{ $linkMenuFourParent[$key] }}" >{{$menu}}</a>
                </li>
            @endforeach            
        </ul>
    </li>
    
    <li class="main-menu option-one">
        <a href="javascript:void(0)">@lang('menu.5')</a>
        <ul>
            @foreach($listmenu_5 as $key => $menu)
                <li>
                   <a href="" >{{$menu}}</a>
                </li>
            @endforeach            
        </ul>
    </li>

    <li  class="main-menu option-one">
        <a href="javascript:void(0)">@lang('menu.6')</a>
        <ul>
            @foreach($listmenu_6 as $key => $menu)
                <li>
                   <a href="" >{{$menu}}</a>
                </li>
            @endforeach            
        </ul>
    </li>

    <li class="main-menu option-one"><a href="javascript:void(0)">@lang('menu.7')</a>
        <ul>
            @foreach($listmenu_7 as $key => $menu)
                @if($key == 2)
                    <li class="none-icon-default">
                        <a href="#" class="none-icon-down">
                            <span class="title">{{$menu}}</span>
                            <span class="fa arrow"></span>
                        </a>
                        <ul class="sub-menu">
                            @foreach($listmenu_7_1 as $key_child => $menu_child)
                                <li>
                                    <a href="{{ $linkMenuSevenChild[$key_child] }}">
                                        <i class="fa fa-angle-double-right"></i>
                                        {{ $menu_child }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                @else
                    <li>
                       <a href="{{ $linkMenuSevenParent[$key] }}" >{{$menu}}</a>
                    </li>
                @endif
            @endforeach            
        </ul>
    </li>
    <li class="main-menu option-one"><a href="javascript:void(0)">@lang('menu.8')</a>
        <ul>
            @foreach($listmenu_8 as $key => $menu)
                <li>
                   <a href="" >{{$menu}}</a>
                </li>
            @endforeach            
        </ul>
    </li>
    <li class="main-menu option-one"><a href="javascript:void(0)">@lang('menu.9')</a>
        <ul>
            @foreach($listmenu_9 as $key => $menu)
                <li>
                   <a href="" >{{$menu}}</a>
                </li>
            @endforeach            
        </ul>
    </li>
</ul>
