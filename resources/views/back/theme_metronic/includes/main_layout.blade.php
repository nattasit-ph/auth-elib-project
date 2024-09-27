<div class="kt-grid kt-grid--hor kt-grid--root">
	<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--ver kt-page">
		
		@include('back.'.config('bookdose.theme_back').'.includes.sidebar.main')

		<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-wrapper" id="kt_wrapper">

			@include('back.'.config('bookdose.theme_back').'.includes.topbar')

			<div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">
				@include('back.'.config('bookdose.theme_back').'.includes.subheader')
				@yield('content')
			</div>

			@include('back.'.config('bookdose.theme_back').'.includes.footer')
		</div>
	</div>
</div>