@if (session('has_module'))
	<!-- Library -->
	<li class="kt-menu__section ">
		<h4 class="kt-menu__section-text">MODULE</h4>
		<i class="kt-menu__section-icon flaticon-more-v2"></i>
	</li>

	@if (!empty(session('modules')))
	@foreach (session('modules') as $item)
		@switch($item->slug)
			@case('room')
			<li class="kt-menu__item kt-menu__item--submenu {{ (request()->is($org_slug.'/admin/'.$item->slug.'*')) ? 'kt-menu__item--open kt-menu__item--here' : '' }}" aria-haspopup="true">
				<a href="javascript:;" class="kt-menu__link kt-menu__toggle">
					<span class="kt-menu__link-icon">
						<i class="{{ $item->backend_fa_icon }}"></i>
					</span>
					<span class="kt-menu__link-text">{{ $item->{'name_'.app()->getLocale()} }}</span>
					<i class="kt-menu__ver-arrow la la-angle-right"></i>
				</a>
				<div class="kt-menu__submenu "><span class="kt-menu__arrow"></span>
					<ul class="kt-menu__subnav">

						<li class="kt-menu__item {{ (request()->is($org_slug.'/admin/'.$item->slug.'/all', 'admin/'.$item->slug.'/create*', 'admin/'.$item->slug.'/*/edit')) ? 'kt-menu__item--active' : '' }}" aria-haspopup="true">
							<a href="{{ route('admin.'.$item->slug.'.all', $org_slug) }}" class="kt-menu__link ">
								<i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i>
								<span class="kt-menu__link-text sub-menu">ห้องทั้งหมด</span>
							</a>
						</li>

						<li class="kt-menu__item {{ (request()->is($org_slug.'/admin/room/booking*')) ? 'kt-menu__item--active' : '' }}" aria-haspopup="true">
							<a href="{{ route('admin.room.booking.all', $org_slug) }}" class="kt-menu__link ">
								<i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i>
								<span class="kt-menu__link-text sub-menu">รายการจองห้อง</span>
							</a>
						</li>

						<li class="kt-menu__item {{ (request()->is($org_slug.'/admin/room-type/*')) ? 'kt-menu__item--active' : '' }}" aria-haspopup="true">
							<a href="{{ route('admin.roomType.index', $org_slug) }}" class="kt-menu__link ">
								<i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i>
								<span class="kt-menu__link-text sub-menu">ประเภทห้อง</span>
							</a>
						</li>

						<li class="kt-menu__item {{ (request()->is($org_slug.'/admin/room/setting*')) ? 'kt-menu__item--active' : '' }}" aria-haspopup="true">
							<a href="{{ route('admin.room.setting.all', $org_slug) }}" class="kt-menu__link ">
								<i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i>
								<span class="kt-menu__link-text sub-menu">ตั้งค่าการจองห้อง</span>
							</a>
						</li>

					</ul>
				</div>
			</li>
			@break
			@case('reward') @break
			@default
			<li class="kt-menu__item kt-menu__item--submenu {{ (request()->is($org_slug.'/admin/'.$item->slug.'*')) ? 'kt-menu__item--open kt-menu__item--here' : '' }}" aria-haspopup="true">
				<a href="javascript:;" class="kt-menu__link kt-menu__toggle">
					<span class="kt-menu__link-icon">
						<i class="{{ $item->backend_fa_icon }}"></i>
					</span>
					<span class="kt-menu__link-text">{{ $item->{'name_'.app()->getLocale()} }}</span>
					<i class="kt-menu__ver-arrow la la-angle-right"></i>
				</a>
				<div class="kt-menu__submenu "><span class="kt-menu__arrow"></span>
					<ul class="kt-menu__subnav">
						@if ($item->has_categories == 1)
							<li class="kt-menu__item {{ (request()->is($org_slug.'/admin/'.$item->slug.'/category*')) ? 'kt-menu__item--active' : '' }}" aria-haspopup="true">
								<a href="{{ route('admin.'.$item->slug.'.category.index', $org_slug) }}" class="kt-menu__link ">
									<i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i>
									<span class="kt-menu__link-text sub-menu">{{ __('menu.back.category') }}</span>
								</a>
							</li>
						@endif

						<li class="kt-menu__item {{ (request()->is($org_slug.'/admin/'.$item->slug, 'admin/'.$item->slug.'/*/edit', 'admin/'.$item->slug.'/edit*')) ? 'kt-menu__item--active' : '' }}" aria-haspopup="true">
							<a href="{{ route('admin.'.$item->slug.'.index', $org_slug) }}" class="kt-menu__link ">
								<i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i>
								<span class="kt-menu__link-text sub-menu">{{ __('menu.back.view_all') }}</span>
							</a>
						</li>

						<li class="kt-menu__item {{ (request()->is($org_slug.'/admin/'.$item->slug.'/create*')) ? 'kt-menu__item--active' : '' }}" aria-haspopup="true">
							<a href="{{ route('admin.'.$item->slug.'.create', $org_slug) }}" class="kt-menu__link ">
								<i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i>
								<span class="kt-menu__link-text sub-menu">{{ __('menu.back.add_new') }}</span>
							</a>
						</li>
					</ul>
				</div>
			</li>
		@endswitch
	@endforeach
	@endif
@endif
