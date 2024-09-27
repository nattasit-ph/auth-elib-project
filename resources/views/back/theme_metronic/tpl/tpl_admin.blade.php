<!DOCTYPE html>
<html lang="en">
	<!-- begin::Head -->
	<head>
		<base href="{{ asset('/back').'/'.config('bookdose.theme_back').'/' }}">
		<title>@yield('title') | {{ config('app.name') }}</title>
		@include('back.'.config('bookdose.theme_back').'.includes.meta')
		@stack('additional_css')
	</head>

	<!-- end::Head -->

	<!-- begin::Body -->
	<body class="kt-quick-panel--right kt-demo-panel--right kt-offcanvas-panel--right kt-header--fixed kt-header-mobile--fixed kt-subheader--enabled kt-subheader--fixed kt-subheader--solid kt-aside--enabled kt-aside--fixed kt-page--loading">

		@include('back.'.config('bookdose.theme_back').'.includes.header_mobile')
		<input type="text" id="hd_txt_copy" name="hd_txt_copy" value="" style="z-index: -999">

		@include('back.'.config('bookdose.theme_back').'.includes.main_layout')

		<!-- begin::Scrolltop -->
		<div id="kt_scrolltop" class="kt-scrolltop">
			<i class="fa fa-arrow-up"></i>
		</div>
		<!-- end::Scrolltop -->

		@include('back.'.config('bookdose.theme_back').'.includes.panel_noti')

		@include('back.'.config('bookdose.theme_back').'.includes.scripts_base')
		@include('back.'.config('bookdose.theme_back').'.includes.scripts')
		@stack('additional_js')
	</body>

	<!-- end::Body -->
</html>