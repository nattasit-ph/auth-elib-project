<!DOCTYPE html>
<html class="no-js" lang="en">

<head>
	<title>@yield('title')</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
	<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>

	<!-- Primary Meta Tags -->
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="csrf-token" content="{{ csrf_token() }}">

	<!-- css -->
	<link type="text/css" href="{{ asset('auth/'.config('bookdose.theme_front').'/css/color/main.css') }}" rel="stylesheet">
	<link type="text/css" href="{{ asset('auth/'.config('bookdose.theme_front').'/css/lang/lang_'.app()->getLocale().'.css') }}" rel="stylesheet">
	<link rel="stylesheet" href="{{ asset('auth/'.config('bookdose.theme_front').'/css/main.css') }}">
	<link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.15.0/css/all.css">
	<link rel="stylesheet" href="{{ asset('auth/'.config('bookdose.theme_front').'/css/home.css') }}">
	<link rel="stylesheet" href="{{ asset('auth/'.config('bookdose.theme_front').'/css/footer.css') }}">

	<!-- fe fe ICON libarary -->
	<link rel="stylesheet" href="{{ asset('/auth/'.config('bookdose.theme_front').'/fonts/feather/feather.css') }}">

	<link rel="shortcut icon" href="{{ asset(config('bookdose.app.project').'/images/favicons/favicon.ico') }}" type="image/x-icon">

	<!-- jquery -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js" integrity="sha512-bLT0Qm9VnAYZDflyKcBaQ2gg0hSYNQrJ8RilYldYQ1FxQYoCLtUjuuRuZo+fjqhx/qtq/1itJ0C2ejDxltZVFg==" crossorigin="anonymous"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.9.0/jquery.validate.min.js"></script>
	@stack('additional_css')
</head>

<body>
	@include('front.'.config('bookdose.theme_front').'.includes.top_nav')
	@include('front.'.config('bookdose.theme_front').'.includes.section_breadcrumbs')
	<main>
		@yield('content')
	</main>
	@include('front.'.config('bookdose.theme_front').'.includes.footer')
	@stack('additional_js')
</body>

</html>
