<style type="text/css">
	.rounded-25px{
		border-radius: 25px !important;
	}
</style>

<div id="kt_header" class="kt-header kt-grid__item  kt-header--fixed ">
	<!-- begin:: Header Topbar system name -->
	<div class="kt-header__topbar">
		<!-- system name -->
		<div class="kt-header__topbar-item kt-header__topbar-item--user">
			<div class="kt-header__topbar-user">
				<span class="kt-header__topbar-welcome  font-pri px-3 py-2 bg-primary rounded-25px text-white">Admin Center • ระบบจัดการกลาง</span>
			</div>
		</div>
	</div>
	<!-- end:: Header Menu system name-->

	<!-- begin:: Header Menu -->
	<button class="kt-header-menu-wrapper-close" id="kt_header_menu_mobile_close_btn"><i class="la la-close"></i></button>
	<div class="kt-header-menu-wrapper" id="kt_header_menu_wrapper">
		<div id="kt_header_menu" class="kt-header-menu kt-header-menu-mobile  kt-header-menu--layout-default ">

		</div>
	</div>
	<!-- end:: Header Menu -->

	<!-- begin:: Header Topbar -->
	<div class="kt-header__topbar">
		<!-- last login-->
		<div class="kt-header__topbar-item kt-header__topbar-item--user">
			<div class="kt-header__topbar-user">
				<span class="kt-header__topbar-welcome font-pri">เข้าสู่ระบบล่าสุด เมื่อ </span>
				<span class="kt-header__topbar-welcome font-pri">{{ \Carbon\Carbon::parse(Auth::user()->last_login_at)->isoFormat('D MMMM, YYYY H:m:s') }} </span>
			</div>
		</div>

		<!--begin: Quick panel toggler -->
		<div class="kt-header__topbar-item kt-header__topbar-item--quick-panel" {{--data-toggle="kt-tooltip" title="Notifications" data-placement="left"--}} onclick="noti_list()">
			<span class="kt-header__topbar-icon" id="kt_quick_panel_toggler_btn">
				<i class="fas fa-bell kt-font-danger"></i>
				<span class="badge badge-danger bagge-top" id="countnoti"></span>
			</span>
		</div>
		<!--end: Quick panel toggler -->

		<!--begin: User Bar -->
		<div class="kt-header__topbar-item kt-header__topbar-item--user">
			<div class="kt-header__topbar-wrapper" data-toggle="dropdown" data-offset="0px,0px">
				<div class="kt-header__topbar-user">
					<span class="kt-header__topbar-welcome kt-hidden-mobile">Hi,</span>
					<span class="kt-header__topbar-username kt-hidden-mobile">{{ Auth::user()->name }}</span>
					<img src="media/avatar/default-avatar.png" alt="avatar" class="rounded-circle">

					<!--use below badge element instead the user avatar to display username's first letter(remove kt-hidden class to display it) -->
					<!-- <span class="kt-badge kt-badge--username kt-badge--unified-success kt-badge--lg kt-badge--rounded kt-badge--bold">S</span> -->
				</div>
			</div>
			<div class="dropdown-menu dropdown-menu-fit dropdown-menu-right dropdown-menu-anim dropdown-menu-top-unround dropdown-menu-xl">

				<!--begin: Head -->
				<div class="kt-user-card kt-user-card--skin-dark kt-notification-item-padding-x" style="background-image: url('media/misc/bg-1.jpg')">
					<div class="kt-user-card__avatar">
						<img src="media/avatar/default-avatar.png" alt="avatar" class="rounded-circle">
					</div>
					<div class="kt-user-card__name">
						{{ Auth::user()->name }}
					</div>
				</div>

				<!--end: Head -->

				<!--begin: Navigation -->
				<div class="kt-notification">
					@if (!empty(config('bookdose.app.belib_url')))
						<a href="{{ config('bookdose.app.belib_url').(!is_blank($org_slug ?? '')?'/'.$org_slug:'') }}/admin" class="kt-notification__item">
							<div class="kt-notification__item-icon">
								<i class="fad fa-books"></i>
							</div>
							<div class="kt-notification__item-details">
								<div class="kt-notification__item-title kt-font-bold">
									Manage E-Library
								</div>
							</div>
						</a>
					@endif

					@if (!empty(config('bookdose.app.learnext_url')))
						<a href="{{ config('bookdose.app.learnext_url') }}/admin" class="kt-notification__item">
							<div class="kt-notification__item-icon">
								<i class="fad fa-graduation-cap"></i>
							</div>
							<div class="kt-notification__item-details">
								<div class="kt-notification__item-title kt-font-bold">
									Manage E-Learning
								</div>
							</div>
						</a>
					@endif

					@if (!empty(config('bookdose.app.km_url')))
						<a href="{{ config('bookdose.app.km_url') }}/admin" class="kt-notification__item">
							<div class="kt-notification__item-icon">
								<i class="fad fa-seedling"></i>
							</div>
							<div class="kt-notification__item-details">
								<div class="kt-notification__item-title kt-font-bold">
									Manage KM
								</div>
							</div>
						</a>
					@endif

					@if (!empty(config('bookdose.app.cms_url')))
						<a href="{{ config('bookdose.app.cms_url') }}/admin" class="kt-notification__item">
							<div class="kt-notification__item-icon">
								<i class="fad fa-video"></i>
							</div>
							<div class="kt-notification__item-details">
								<div class="kt-notification__item-title kt-font-bold">
									Manage CMS
								</div>
							</div>
						</a>
					@endif

					<div class="kt-notification__custom kt-space-between">
						<form id="frm_logout" method="post" action="{{ route('logout', Auth::user()->org->slug) }}">
							@csrf
						</form>
						<a href="javascript:;" onclick="logout()" class="btn btn-label btn-label-brand btn-sm btn-bold">Sign Out</a>
					</div>
				</div>

				<!--end: Navigation -->
			</div>
		</div>

		<!--end: User Bar -->
	</div>
	<!-- end:: Header Topbar -->

</div>
