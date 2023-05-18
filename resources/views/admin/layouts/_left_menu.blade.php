    <?php
    $listmenu_1 = Lang::get('menu.1_list');
    $listmenu_2 = Lang::get('menu.2_list');
    $listmenu_3 = Lang::get('menu.3_list');
    $listmenu_4 = Lang::get('menu.4_list');
    $listmenu_5 = Lang::get('menu.5_list');
    $listmenu_6 = Lang::get('menu.6_list');

    $listmenu_4_1 = Lang::get('menu.4_1_list');
    $listmenu_4_2 = Lang::get('menu.4_2_list');
    $listmenu_4_3 = Lang::get('menu.4_3_list');
    $listmenu_4_4 = Lang::get('menu.4_4_list');


    $icon_array = [
        '<i class="bi bi-grid fs-3"></i>',
        '<i class="bi bi-window fs-3"></i>',
        '<i class="bi bi-app-indicator fs-3"></i>',
        '<i class="bi bi-app-indicator fs-3"></i>',
        '<i class="bi bi-person fs-2"></i>',
        '<i class="bi bi-sticky fs-3"></i>',
        '<i class="bi bi-shield-check fs-3"></i>',
        '<i class="bi bi-layers fs-3"></i>',
        '<i class="bi bi-printer fs-3"></i>',
        '<i class="bi bi-cart fs-3"></i>',
        '<i class="bi bi-hr fs-3"></i>',
        '<i class="bi bi-people fs-3"></i>',
        '<i class="bi bi-calendar3-event fs-3"></i>',
        '<i class="bi-chat-left fs-3"></i>',
        '<i class="bi bi-layout-sidebar fs-3"></i>',
        '<i class="bi bi-layers fs-3"></i>',
        '<i class="bi bi-layers fs-3"></i>',
    ];
    
?>

<div class="aside-menu flex-column-fluid" style="background-color: #ffff">
                        <!--begin::Aside Menu-->
                        <div class="hover-scroll-overlay-y my-2 py-5 py-lg-8" id="kt_aside_menu_wrapper" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-height="auto" data-kt-scroll-dependencies="#kt_aside_logo, #kt_aside_footer" data-kt-scroll-wrappers="#kt_aside_menu" data-kt-scroll-offset="0">
                            <!--begin::Menu-->
                            <div class="menu menu-column menu-title-gray-800 menu-state-title-primary menu-state-icon-primary menu-state-bullet-primary menu-arrow-gray-500" id="#kt_aside_menu" data-kt-menu="true">
							
                                <!-- Lịch công tác -->
                                <div class="menu-item">
                                    <div class="menu-content pt-8 pb-2">
                                        <span class="menu-section text-muted text-uppercase ls-1">
                                            @lang('menu.1')
                                        </span>
                                    </div>
                                </div>
                                <!-- Lịch công tác tuần -->
                                <div class="menu-item">
                                    <a class="menu-link 
                                        {!! (Request::is('admin/trao-doi-thong-tin/messageboard/index') 
                                        ? 'active' : '' ) !!}
                                     " href="#">
                                        <span class="menu-icon">
                                            {!! $icon_array[array_rand($icon_array, 1) ] !!}
                                        </span>
                                        <span class="menu-title">{{$listmenu_1[1]}}</span>
                                    </a>
                                </div>
                                <!-- / Lịch công tác tuần -->
                                <!-- Lịch lãnh đạo -->
                                <div class="menu-item">
                                    <a class="menu-link 
                                        {!! (Request::is('admin/trao-doi-thong-tin/messageboard/index') 
                                        ? 'active' : '' ) !!}
                                     " href="#">
                                        <span class="menu-icon">
                                            {!! $icon_array[array_rand($icon_array, 1) ] !!}
                                        </span>
                                        <span class="menu-title">{{$listmenu_1[2]}}</span>
                                    </a>
                                </div>
                                <!-- / Lịch lãnh đạo -->
                                <!-- Lịch nhà trường -->
                                <div class="menu-item">
                                    <a class="menu-link 
                                        {!! (Request::is('admin/trao-doi-thong-tin/messageboard/index') 
                                        ? 'active' : '' ) !!}
                                     " href="#">
                                        <span class="menu-icon">
                                            {!! $icon_array[array_rand($icon_array, 1) ] !!}
                                        </span>
                                        <span class="menu-title">{{$listmenu_1[3]}}</span>
                                    </a>
                                </div>
                                <!-- / Lịch nhà trường -->
                                <!-- Lịch khoa / phòng -->
                                <div class="menu-item">
                                    <a class="menu-link 
                                        {!! (Request::is('admin/trao-doi-thong-tin/messageboard/index') 
                                        ? 'active' : '' ) !!}
                                     " href="#">
                                        <span class="menu-icon">
                                            {!! $icon_array[array_rand($icon_array, 1) ] !!}
                                        </span>
                                        <span class="menu-title">{{$listmenu_1[4]}}</span>
                                    </a>
                                </div>
                                <!-- / Lịch khoa / phòng -->
                                <!-- Lịch cá nhân -->
                                <div class="menu-item">
                                    <a class="menu-link 
                                        {!! (Request::is('admin/trao-doi-thong-tin/messageboard/index') 
                                        ? 'active' : '' ) !!}
                                     " href="#">
                                        <span class="menu-icon">
                                            {!! $icon_array[array_rand($icon_array, 1) ] !!}
                                        </span>
                                        <span class="menu-title">{{$listmenu_1[5]}}</span>
                                    </a>
                                </div>
                                <!-- / Lịch cá nhân -->
                                <!-- /Lịch công tác -->
                                
                                <!-- Trao đổi thông tin -->
                                <div class="menu-item">
                                    <div class="menu-content pt-8 pb-2">
                                        <span class="menu-section text-muted text-uppercase ls-1">
                                            @lang('menu.2')
                                        </span>
                                    </div>
                                </div>
                                <!-- Thông báo -->
                                <div class="menu-item">
                                    <a class="menu-link 
                                        {!! (Request::is('admin/trao-doi-thong-tin/messageboard/index') 
                                        ? 'active' : '' ) !!}
                                     " href="#">
                                        <span class="menu-icon">
                                            {!! $icon_array[array_rand($icon_array, 1) ] !!}
                                        </span>
                                        <span class="menu-title">{{$listmenu_2[1]}}</span>
                                    </a>
                                </div>
                                <!-- / Thông báo -->
                                <!-- Chat -->
                                <div class="menu-item">
                                    <a class="menu-link 
                                        {!! (Request::is('admin/trao-doi-thong-tin/messageboard/index') 
                                        ? 'active' : '' ) !!}
                                     " href="#">
                                        <span class="menu-icon">
                                            {!! $icon_array[array_rand($icon_array, 1) ] !!}
                                        </span>
                                        <span class="menu-title">{{$listmenu_2[2]}}</span>
                                    </a>
                                </div>
                                <!-- / Chat -->
                                <!-- Thư nội bộ -->
                                <div class="menu-item">
                                    <a class="menu-link 
                                        {!! (Request::is('admin/trao-doi-thong-tin/messageboard/index') 
                                        ? 'active' : '' ) !!}
                                     " href="#">
                                        <span class="menu-icon">
                                            {!! $icon_array[array_rand($icon_array, 1) ] !!}
                                        </span>
                                        <span class="menu-title">{{$listmenu_2[3]}}</span>
                                    </a>
                                </div>
                                <!-- / Thư nội bộ -->
                                <!-- /Trao đổi thông tin -->

                                <!-- Công việc -->
                                <div class="menu-item">
                                    <div class="menu-content pt-8 pb-2">
                                        <span class="menu-section text-muted text-uppercase ls-1">
                                            @lang('menu.3')
                                        </span>
                                    </div>
                                </div>
                                <!-- Công việc theo dự án -->
                                <div class="menu-item">
                                    <a class="menu-link 
                                        {!! (Request::is('admin/trao-doi-thong-tin/messageboard/index') 
                                        ? 'active' : '' ) !!}
                                     " href="#">
                                        <span class="menu-icon">
                                            {!! $icon_array[array_rand($icon_array, 1) ] !!}
                                        </span>
                                        <span class="menu-title">{{$listmenu_3[1]}}</span>
                                    </a>
                                </div>
                                <!-- / Công việc theo dự án -->
                                <!-- Công việc nội bộ đơn vị -->
                                <div class="menu-item">
                                    <a class="menu-link 
                                        {!! (Request::is('admin/trao-doi-thong-tin/messageboard/index') 
                                        ? 'active' : '' ) !!}
                                     " href="#">
                                        <span class="menu-icon">
                                            {!! $icon_array[array_rand($icon_array, 1) ] !!}
                                        </span>
                                        <span class="menu-title">{{$listmenu_3[2]}}</span>
                                    </a>
                                </div>
                                <!-- / Công việc nội bộ đơn vị -->
                                <!-- /Công việc -->

                                <!-- Văn bản -->
                                <div class="menu-item ">
                                    <div class="menu-content pt-8 pb-2">
                                        <span class="menu-section text-muted text-uppercase ls-1">
                                            @lang('menu.4')
                                        </span>
                                    </div>
                                </div>
                                <!-- show trong menu-item,  active trong menu-link-->
                                <!-- Văn bản đến -->
                                <div data-kt-menu-trigger="click" class="menu-item  menu-accordion 
                                    {!! (Request::is('admin/thuong-truc/setstandard') 
                                    || Request::is('admin/thuong-truc/setstandard/*')
                                    ? 'show' : '' ) !!}
                                 ">
                                    <span class="menu-link ">
                                        <span class="menu-icon">
                                            {!! $icon_array[array_rand($icon_array, 1) ] !!}
                                        </span>
                                        <span class="menu-title">{{ $listmenu_4[1] }}</span>
                                        <span class="menu-arrow"></span>
                                    </span>
                                    <div class="menu-sub menu-sub-accordion menu-active-bg">
                                        <div class="menu-item">
                                            <a class="menu-link 
                                            {!! (Request::is('admin/thuong-truc/setstandard/index') 
                                            || Request::is('admin/thuong-truc/setstandard/index/*') 
                                            ? 'active' : '' ) !!}
                                             " href="#">
                                                <span class="menu-bullet">
                                                    <span class="bullet bullet-dot"></span>
                                                </span>
                                                <span class="menu-title">{{ $listmenu_4_1[1] }}</span>
                                            </a>
                                        </div>
                                        <div class="menu-item">
                                            <a class="menu-link 
                                            {!! (Request::is('admin/thuong-truc/setstandard/show-suggestions') 
                                            ? 'active' : '' ) !!}
                                             " href="#">
                                                <span class="menu-bullet">
                                                    <span class="bullet bullet-dot"></span>
                                                </span>
                                                <span class="menu-title">{{ $listmenu_4_1[2] }}</span>
                                            </a>
                                        </div>
                                        <div class="menu-item">
                                            <a class="menu-link 
                                            {!! (Request::is('admin/thuong-truc/setstandard/show-minimum') 
                                            ? 'active' : '' ) !!}
                                             " href="#">
                                                <span class="menu-bullet">
                                                    <span class="bullet bullet-dot"></span>
                                                </span>
                                                <span class="menu-title">{{ $listmenu_4_1[3] }}</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <!-- /Văn bản đến -->

                                <!-- Dự thảo văn bản -->
                                <div data-kt-menu-trigger="click" class="menu-item  menu-accordion 
                                    {!! (Request::is('admin/thuong-truc/setstandard') 
                                    || Request::is('admin/thuong-truc/setstandard/*')
                                    ? 'show' : '' ) !!}
                                 ">
                                    <span class="menu-link ">
                                        <span class="menu-icon">
                                            {!! $icon_array[array_rand($icon_array, 1) ] !!}
                                        </span>
                                        <span class="menu-title">{{ $listmenu_4[2] }}</span>
                                        <span class="menu-arrow"></span>
                                    </span>
                                    <div class="menu-sub menu-sub-accordion menu-active-bg">
                                        <div class="menu-item">
                                            <a class="menu-link 
                                            {!! (Request::is('admin/thuong-truc/setstandard/index') 
                                            || Request::is('admin/thuong-truc/setstandard/index/*') 
                                            ? 'active' : '' ) !!}
                                             " href="#">
                                                <span class="menu-bullet">
                                                    <span class="bullet bullet-dot"></span>
                                                </span>
                                                <span class="menu-title">{{ $listmenu_4_2[1] }}</span>
                                            </a>
                                        </div>
                                        <div class="menu-item">
                                            <a class="menu-link 
                                            {!! (Request::is('admin/thuong-truc/setstandard/show-suggestions') 
                                            ? 'active' : '' ) !!}
                                             " href="#">
                                                <span class="menu-bullet">
                                                    <span class="bullet bullet-dot"></span>
                                                </span>
                                                <span class="menu-title">{{ $listmenu_4_2[2] }}</span>
                                            </a>
                                        </div>
                                        <div class="menu-item">
                                            <a class="menu-link 
                                            {!! (Request::is('admin/thuong-truc/setstandard/show-minimum') 
                                            ? 'active' : '' ) !!}
                                             " href="#">
                                                <span class="menu-bullet">
                                                    <span class="bullet bullet-dot"></span>
                                                </span>
                                                <span class="menu-title">{{ $listmenu_4_2[3] }}</span>
                                            </a>
                                        </div>
                                        <div class="menu-item">
                                            <a class="menu-link 
                                            {!! (Request::is('admin/thuong-truc/setstandard/show-minimum') 
                                            ? 'active' : '' ) !!}
                                             " href="#">
                                                <span class="menu-bullet">
                                                    <span class="bullet bullet-dot"></span>
                                                </span>
                                                <span class="menu-title">{{ $listmenu_4_2[4] }}</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <!-- /Dự thảo văn bản -->

                                <!-- Phát hành văn bản -->
                                <div data-kt-menu-trigger="click" class="menu-item  menu-accordion 
                                    {!! (Request::is('admin/thuong-truc/setstandard') 
                                    || Request::is('admin/thuong-truc/setstandard/*')
                                    ? 'show' : '' ) !!}
                                 ">
                                    <span class="menu-link ">
                                        <span class="menu-icon">
                                            {!! $icon_array[array_rand($icon_array, 1) ] !!}
                                        </span>
                                        <span class="menu-title">{{ $listmenu_4[3] }}</span>
                                        <span class="menu-arrow"></span>
                                    </span>
                                    <div class="menu-sub menu-sub-accordion menu-active-bg">
                                        <div class="menu-item">
                                            <a class="menu-link 
                                            {!! (Request::is('admin/thuong-truc/setstandard/index') 
                                            || Request::is('admin/thuong-truc/setstandard/index/*') 
                                            ? 'active' : '' ) !!}
                                             " href="#">
                                                <span class="menu-bullet">
                                                    <span class="bullet bullet-dot"></span>
                                                </span>
                                                <span class="menu-title">{{ $listmenu_4_3[1] }}</span>
                                            </a>
                                        </div>
                                        <div class="menu-item">
                                            <a class="menu-link 
                                            {!! (Request::is('admin/thuong-truc/setstandard/show-suggestions') 
                                            ? 'active' : '' ) !!}
                                             " href="#">
                                                <span class="menu-bullet">
                                                    <span class="bullet bullet-dot"></span>
                                                </span>
                                                <span class="menu-title">{{ $listmenu_4_3[2] }}</span>
                                            </a>
                                        </div>
                                        <div class="menu-item">
                                            <a class="menu-link 
                                            {!! (Request::is('admin/thuong-truc/setstandard/show-minimum') 
                                            ? 'active' : '' ) !!}
                                             " href="#">
                                                <span class="menu-bullet">
                                                    <span class="bullet bullet-dot"></span>
                                                </span>
                                                <span class="menu-title">{{ $listmenu_4_3[3] }}</span>
                                            </a>
                                        </div>
                                        <div class="menu-item">
                                            <a class="menu-link 
                                            {!! (Request::is('admin/thuong-truc/setstandard/show-minimum') 
                                            ? 'active' : '' ) !!}
                                             " href="#">
                                                <span class="menu-bullet">
                                                    <span class="bullet bullet-dot"></span>
                                                </span>
                                                <span class="menu-title">{{ $listmenu_4_3[4] }}</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <!-- /Phát hành văn bản -->

                                <!-- Báo cáo thống kê -->
                                <div data-kt-menu-trigger="click" class="menu-item  menu-accordion 
                                    {!! (Request::is('admin/thuong-truc/setstandard') 
                                    || Request::is('admin/thuong-truc/setstandard/*')
                                    ? 'show' : '' ) !!}
                                 ">
                                    <span class="menu-link ">
                                        <span class="menu-icon">
                                            {!! $icon_array[array_rand($icon_array, 1) ] !!}
                                        </span>
                                        <span class="menu-title">{{ $listmenu_4[4] }}</span>
                                        <span class="menu-arrow"></span>
                                    </span>
                                    <div class="menu-sub menu-sub-accordion menu-active-bg">
                                        <div class="menu-item">
                                            <a class="menu-link 
                                            {!! (Request::is('admin/thuong-truc/setstandard/index') 
                                            || Request::is('admin/thuong-truc/setstandard/index/*') 
                                            ? 'active' : '' ) !!}
                                             " href="#">
                                                <span class="menu-bullet">
                                                    <span class="bullet bullet-dot"></span>
                                                </span>
                                                <span class="menu-title">{{ $listmenu_4_4[1] }}</span>
                                            </a>
                                        </div>
                                        <div class="menu-item">
                                            <a class="menu-link 
                                            {!! (Request::is('admin/thuong-truc/setstandard/show-suggestions') 
                                            ? 'active' : '' ) !!}
                                             " href="#">
                                                <span class="menu-bullet">
                                                    <span class="bullet bullet-dot"></span>
                                                </span>
                                                <span class="menu-title">{{ $listmenu_4_4[2] }}</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <!-- /Báo cáo thống kê -->
                                <!-- /Văn bản -->
                                
                                <!-- Tiện ích -->
                                <div class="menu-item">
                                    <div class="menu-content pt-8 pb-2">
                                        <span class="menu-section text-muted text-uppercase ls-1">
                                            @lang('menu.5')
                                        </span>
                                    </div>
                                </div>
                                <!-- Người dùng -->
                                <div class="menu-item">
                                    <a class="menu-link 
                                        {!! (Request::is('admin/trao-doi-thong-tin/messageboard/index') 
                                        ? 'active' : '' ) !!}
                                     " href="#">
                                        <span class="menu-icon">
                                            {!! $icon_array[array_rand($icon_array, 1) ] !!}
                                        </span>
                                        <span class="menu-title">{{$listmenu_5[1]}}</span>
                                    </a>
                                </div>
                                <!-- / Người dùng -->
                                <!-- Nhóm người dùng -->
                                <div class="menu-item">
                                    <a class="menu-link 
                                        {!! (Request::is('admin/trao-doi-thong-tin/messageboard/index') 
                                        ? 'active' : '' ) !!}
                                     " href="#">
                                        <span class="menu-icon">
                                            {!! $icon_array[array_rand($icon_array, 1) ] !!}
                                        </span>
                                        <span class="menu-title">{{$listmenu_5[2]}}</span>
                                    </a>
                                </div>
                                <!-- / Nhóm người dùng -->
                                <!-- Đơn vị trong trường -->
                                <div class="menu-item">
                                    <a class="menu-link 
                                        {!! (Request::is('admin/trao-doi-thong-tin/messageboard/index') 
                                        ? 'active' : '' ) !!}
                                     " href="#">
                                        <span class="menu-icon">
                                            {!! $icon_array[array_rand($icon_array, 1) ] !!}
                                        </span>
                                        <span class="menu-title">{{$listmenu_5[3]}}</span>
                                    </a>
                                </div>
                                <!-- / Đơn vị trong trường -->
                                <!-- Đơn vị ngoài trường -->
                                <div class="menu-item">
                                    <a class="menu-link 
                                        {!! (Request::is('admin/trao-doi-thong-tin/messageboard/index') 
                                        ? 'active' : '' ) !!}
                                     " href="#">
                                        <span class="menu-icon">
                                            {!! $icon_array[array_rand($icon_array, 1) ] !!}
                                        </span>
                                        <span class="menu-title">{{$listmenu_5[4]}}</span>
                                    </a>
                                </div>
                                <!-- / Đơn vị ngoài trường -->
                                <!-- /Tiện ích -->
                                
                                <!-- Quản trị hệ thống -->
                                <div class="menu-item">
                                    <div class="menu-content pt-8 pb-2">
                                        <span class="menu-section text-muted text-uppercase ls-1">
                                            @lang('menu.6')
                                        </span>
                                    </div>
                                </div>
                                <!-- Quyền -->
                                <div class="menu-item">
                                    <a class="menu-link 
                                        {!! (Request::is('admin/trao-doi-thong-tin/messageboard/index') 
                                        ? 'active' : '' ) !!}
                                     " href="#">
                                        <span class="menu-icon">
                                            {!! $icon_array[array_rand($icon_array, 1) ] !!}
                                        </span>
                                        <span class="menu-title">{{$listmenu_6[1]}}</span>
                                    </a>
                                </div>
                                <!-- / Quyền -->
                                <!-- Nhóm quyền -->
                                <div class="menu-item">
                                    <a class="menu-link 
                                        {!! (Request::is('admin/trao-doi-thong-tin/messageboard/index') 
                                        ? 'active' : '' ) !!}
                                     " href="#">
                                        <span class="menu-icon">
                                            {!! $icon_array[array_rand($icon_array, 1) ] !!}
                                        </span>
                                        <span class="menu-title">{{$listmenu_6[2]}}</span>
                                    </a>
                                </div>
                                <!-- / Nhóm quyền -->
                                <!-- Phòng họp -->
                                <div class="menu-item">
                                    <a class="menu-link 
                                        {!! (Request::is('admin/trao-doi-thong-tin/messageboard/index') 
                                        ? 'active' : '' ) !!}
                                     " href="#">
                                        <span class="menu-icon">
                                            {!! $icon_array[array_rand($icon_array, 1) ] !!}
                                        </span>
                                        <span class="menu-title">{{$listmenu_6[3]}}</span>
                                    </a>
                                </div>
                                <!-- / Phòng họp -->
                                <!-- Log hệ thống -->
                                <div class="menu-item">
                                    <a class="menu-link 
                                        {!! (Request::is('admin/trao-doi-thong-tin/messageboard/index') 
                                        ? 'active' : '' ) !!}
                                     " href="#">
                                        <span class="menu-icon">
                                            {!! $icon_array[array_rand($icon_array, 1) ] !!}
                                        </span>
                                        <span class="menu-title">{{$listmenu_6[4]}}</span>
                                    </a>
                                </div>
                                <!-- / Log hệ thống -->
                                <!-- /Quản trị hệ thống -->



                                <div class="menu-item">
                                    <div class="menu-content">
                                        <div class="separator mx-1 my-4"></div>
                                    </div>
                                </div>
                                
                            </div>
                            <!--end::Menu-->
                        </div>
                    </div>