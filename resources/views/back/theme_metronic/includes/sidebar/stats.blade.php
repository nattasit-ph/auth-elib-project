
<!-- Stat Repors -->
<li class="kt-menu__section">
	<h4 class="kt-menu__section-text">STATISTICS</h4>
	<i class="kt-menu__section-icon flaticon-more-v2"></i>
</li>

<li class="kt-menu__item kt-menu__item--submenu {{ (request()->is('admin/report/user*')) ? 'kt-menu__item--open kt-menu__item--here' : '' }}" aria-haspopup="true">
	<a href="javascript:;" class="kt-menu__link kt-menu__toggle">
		<span class="kt-menu__link-icon">
			<i class="fas fa-chart-bar"></i>
		</span>
		<span class="kt-menu__link-text">{{ __('menu.back.user_statistics') }}</span>
		<i class="kt-menu__ver-arrow la la-angle-right"></i>
	</a>
	<div class="kt-menu__submenu "><span class="kt-menu__arrow"></span>
		<ul class="kt-menu__subnav">
			<li class="kt-menu__item {{ (request()->is('admin/report/user/overall')) ? 'kt-menu__item--active' : '' }}" aria-haspopup="true">
				<a href="{{ route('admin.report.user.overall') }}" class="kt-menu__link ">
					<i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i>
					<span class="kt-menu__link-text sub-menu">{{ __('menu.back.user_information') }}</span>
				</a>
			</li>
		</ul>
	</div>
</li>


