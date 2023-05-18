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


<div id="sidebar" class="sidebar responsive ace-save-state">
	<ul class="nav nav-list">
		<li class="active li-dashboard li-item">
			<a href="{{ route('admin.dashboard') }}">
				<i class="menu-icon fa fa-tachometer"></i>
				<span class="menu-text"> Dashboard </span>
			</a>

			<b class="arrow"></b>
		</li>

		<li class="li-thuongtruc li-item">
			<a href="#" class="dropdown-toggle">
				<i class="menu-icon fa fa-desktop"></i>
				<span class="menu-text">
					@lang('menu.2')
				</span>

				<b class="arrow fa fa-angle-down"></b>
			</a>

			<b class="arrow"></b>

			<ul class="submenu">
				@foreach($listmenu_2 as $key => $menu)
					<li class="">
						<a href="{{ $linkMenuTwoParent[$key] }}" class="dropdown-toggle">
							<i class="menu-icon fa fa-caret-right"></i>
							{{$menu}}
							<b class="arrow fa fa-angle-down"></b>
						</a>

						<b class="arrow"></b>

						<ul class="submenu">
							@if($key  == '1')
								@foreach($listmenu_2_1 as $key_child => $menu_child)
									<li class="">
										<a href="{{ $linkMenuTwoChild[$key_child] }}">
											<i class="menu-icon fa fa-caret-right"></i>
											{{ $menu_child }}
										</a>

										<b class="arrow"></b>
									</li>
								@endforeach
							@elseif($key  == '2')
								@foreach($listmenu_2_2 as $key_child => $menu_child)
	                                @php
	                                    $key_child = $key_child + count($listmenu_2_1);
	                                @endphp
                                	<li class="">
										<a href="{{ $linkMenuTwoChild[$key_child] }}">
											<i class="menu-icon fa fa-caret-right"></i>
											{{ $menu_child }}
										</a>

										<b class="arrow"></b>
									</li>
								@endforeach
                        	@elseif($key  == '3')
                            	@foreach($listmenu_2_3 as $key_child => $menu_child)
                                	@php
	                                    $key_child = $key_child + count($listmenu_2_1) + count($listmenu_2_2);
	                                @endphp
                                	<li class="">
										<a href="{{ $linkMenuTwoChild[$key_child] }}">
											<i class="menu-icon fa fa-caret-right"></i>
											{{ $menu_child }}
										</a>

										<b class="arrow"></b>
									</li>
								@endforeach
                       		@endif
						</ul>
					</li>
				@endforeach   
			</ul>
		</li>
		<li class="li-tdtt li-item">
			<a href="#" class="dropdown-toggle">
				<i class="menu-icon fa fa-info-circle"></i>
				<span class="menu-text">
					@lang('menu.3')
				</span>

				<b class="arrow fa fa-angle-down"></b>
			</a>

			<b class="arrow"></b>

			<ul class="submenu">
				@foreach($listmenu_3 as $key => $menu)
					<li class="">
						<a href="{{ $linkMenuThreeParent[$key] }}">
							<i class="menu-icon fa fa-caret-right"></i>
							{{$menu}}
						</a>

						<b class="arrow"></b>
					</li>
				@endforeach    
			</ul>
		</li>
		<li class="li-dbcl li-item">
			<a href="#" class="dropdown-toggle">
				<i class="menu-icon fa fa-book"></i>
				<span class="menu-text">
					@lang('menu.4')
				</span>

				<b class="arrow fa fa-angle-down"></b>
			</a>

			<b class="arrow"></b>

			<ul class="submenu">
				@foreach($listmenu_4 as $key => $menu)
					<li class="">
						<a href="{{ $linkMenuFourParent[$key] }}">
							<i class="menu-icon fa fa-caret-right"></i>
							{{$menu}}
						</a>

						<b class="arrow"></b>
					</li>
				@endforeach    
			</ul>
		</li>
		<li class="li-sochuan li-item">
			<a href="#" class="dropdown-toggle">
				<i class="menu-icon fa fa-arrows-h"></i>
				<span class="menu-text">
					@lang('menu.5')
				</span>

				<b class="arrow fa fa-angle-down"></b>
			</a>

			<b class="arrow"></b>

			<ul class="submenu">
				@foreach($listmenu_5 as $key => $menu)
					<li class="">
						<a href="">
							<i class="menu-icon fa fa-caret-right"></i>
							{{$menu}}
						</a>

						<b class="arrow"></b>
					</li>
				@endforeach    
			</ul>
		</li>
		<li class="li-doisanh li-item">
			<a href="#" class="dropdown-toggle">
				<i class="menu-icon fa fa-compress"></i>
				<span class="menu-text">
					@lang('menu.6')
				</span>

				<b class="arrow fa fa-angle-down"></b>
			</a>

			<b class="arrow"></b>

			<ul class="submenu">
				@foreach($listmenu_6 as $key => $menu)
					<li class="">
						<a href="">
							<i class="menu-icon fa fa-caret-right"></i>
							{{$menu}}
						</a>

						<b class="arrow"></b>
					</li>
				@endforeach    
			</ul>
		</li>
		<li class="li-tdg li-item">
			<a href="#" class="dropdown-toggle">
				<i class="menu-icon fa fa-sticky-note"></i>
				<span class="menu-text">
					@lang('menu.7')
				</span>

				<b class="arrow fa fa-angle-down"></b>
			</a>

			<b class="arrow"></b>

			<ul class="submenu">
				@foreach($listmenu_7 as $key => $menu)
					@if($key == 2)
						<li class="">
							<a href="" class="dropdown-toggle">
								<i class="menu-icon fa fa-caret-right"></i>
								{{$menu}}
								<b class="arrow fa fa-angle-down"></b>
							</a>

							<b class="arrow"></b>

							<ul class="submenu">
								@foreach($listmenu_7_1 as $key_child => $menu_child)
									<li class="">
										<a href="{{ $linkMenuSevenChild[$key_child] }}">
											<i class="menu-icon fa fa-caret-right"></i>
											{{ $menu_child }}
										</a>

										<b class="arrow"></b>
									</li>
								@endforeach
							</ul>
						</li>
					@else
						<li class="">
							<a href="{{ $linkMenuSevenParent[$key] }}">
								<i class="menu-icon fa fa-caret-right"></i>
								{{$menu}}
							</a>

							<b class="arrow"></b>
						</li>
					@endif
				@endforeach   
			</ul>
		</li>
		<li class="li-dgn li-item">
			<a href="#" class="dropdown-toggle">
				<i class="menu-icon fa fa-inbox"></i>
				<span class="menu-text">
					@lang('menu.8')
				</span>

				<b class="arrow fa fa-angle-down"></b>
			</a>

			<b class="arrow"></b>

			<ul class="submenu">
				@foreach($listmenu_8 as $key => $menu)
					<li class="">
						<a href="">
							<i class="menu-icon fa fa-caret-right"></i>
							{{$menu}}
						</a>

						<b class="arrow"></b>
					</li>
				@endforeach    
			</ul>
		</li>
		<li class="li-tonghop li-item">
			<a href="#" class="dropdown-toggle">
				<i class="menu-icon fa fa-line-chart"></i>
				<span class="menu-text">
					@lang('menu.9')
				</span>

				<b class="arrow fa fa-angle-down"></b>
			</a>

			<b class="arrow"></b>

			<ul class="submenu">
				@foreach($listmenu_9 as $key => $menu)
					<li class="">
						<a href="">
							<i class="menu-icon fa fa-caret-right"></i>
							{{$menu}}
						</a>

						<b class="arrow"></b>
					</li>
				@endforeach    
			</ul>
		</li>
	</ul><!-- /.nav-list -->

	<div class="sidebar-toggle sidebar-collapse" id="sidebar-collapse">
		<i id="sidebar-toggle-icon" class="ace-icon fa fa-angle-double-left ace-save-state" data-icon1="ace-icon fa fa-angle-double-left" data-icon2="ace-icon fa fa-angle-double-right"></i>
	</div>
</div>