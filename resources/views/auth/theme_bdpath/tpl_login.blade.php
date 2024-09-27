<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">


@if (!is_blank(config('bookdose.app.ios_id') ?? '') || !is_blank(config('bookdose.app.android_id') ?? ''))
<!-- webapp metatags -->
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="black" />
<meta name="apple-itunes-app" content="app-id={{ config('bookdose.app.ios_id') ?? '' }}">
<meta name="google-play-app" content="app-id={{ config('bookdose.app.android_id') ?? '' }}">
@endif

    <title>{{ config('app.name') }}</title>
    <base href="{{ url('auth').'/'.config('bookdose.theme_login') }}/">

    <!-- Libs CSS -->
    <link rel="stylesheet" href="assets/libs/@fancyapps/fancybox/dist/jquery.fancybox.min.css">
    <link rel="stylesheet" href="assets/libs/aos/dist/aos.css">
    <link rel="stylesheet" href="assets/libs/flag-icon-css/css/flag-icon.min.css">
    <link rel="stylesheet" href="assets/libs/flickity/dist/flickity.min.css">
    <link rel="stylesheet" href="assets/libs/flickity-fade/flickity-fade.css">
    <link rel="stylesheet" href="assets/libs/highlightjs/styles/vs2015.css">
    <link rel="stylesheet" href="assets/libs/jarallax/dist/jarallax.css">
    <link rel="stylesheet" href="assets/fonts/feather/feather.css">
    <link rel="stylesheet" href="assets/libs/sweetalert2/dist/sweetalert2.css" type="text/css" >

    <!--
      Theme Sans Serif CSS
      Remove the "disabled" attribute if you want to enable Sans Serif for headings.
    -->
    <link rel="stylesheet" href="assets/css/theme-sans-serif.min.css" id="themeSansSerif" >
    <!-- Theme CSS -->
    <link rel="stylesheet" href="assets/css/theme.min.css">
    <link rel="stylesheet" href="assets/css/green.css">
    <!-- Bootstrap -->
    <link rel="stylesheet" href="{{ asset('auth/'.config('bookdose.theme_front').'/bootstrap-5.0.2/css/bootstrap.css') }}">

    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.15.0/css/all.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="{{ asset(config('bookdose.app.folder').'/'.config('bookdose.app.custom_css')) }}">

    <!-- css -->
    <link type="text/css" href="{{ asset('auth/'.config('bookdose.theme_front').'/css/color/'.config('bookdose.theme_front_color').'.css') }}?{{ time() }}" rel="stylesheet">
    <link type="text/css" href="{{ asset('auth/'.config('bookdose.theme_front').'/css/lang/lang_'.app()->getLocale().'.css') }}?{{ time() }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('auth/'.config('bookdose.theme_front').'/css/auth.css') }}?{{ time() }}">

    <!-- favicon -->
    <link rel="shortcut icon" href="{{ asset(config('bookdose.app.project').'/images/favicons/favicon.ico') }}" type="image/x-icon">
  </head>
  <body>

    <!-- BODY -->
    <main class="">
        	@yield('content')

    </main>

    <!-- JAVASCRIPT -->
    <!-- Polyfills -->
    <script src="https://polyfill.io/v3/polyfill.min.js?features=Array.prototype.find,Array.prototype.includes,Array.from,Object.entries,Promise,Object.assign"></script>

    <!-- Libs JS -->
    <script src="assets/libs/jquery/dist/jquery.min.js"></script>
    <script src="assets/libs/@fancyapps/fancybox/dist/jquery.fancybox.min.js"></script>
    <script src="assets/libs/@popperjs/core/dist/umd/popper.min.js"></script>
    <script src="assets/libs/aos/dist/aos.js"></script>
    <script src="assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/libs/countup.js/dist/countUp.min.js"></script>
    <script src="assets/libs/flickity/dist/flickity.pkgd.min.js"></script>
    <script src="assets/libs/flickity-fade/flickity-fade.js"></script>
    <script src="assets/libs/highlightjs/highlight.pack.min.js"></script>
    <script src="assets/libs/imagesloaded/imagesloaded.pkgd.min.js"></script>
    <script src="assets/libs/isotope-layout/dist/isotope.pkgd.min.js"></script>
    <script src="assets/libs/jarallax/dist/jarallax.min.js"></script>
    <script src="assets/libs/jarallax/dist/jarallax-video.min.js"></script>
    <script src="assets/libs/jarallax/dist/jarallax-element.min.js"></script>
    <script src="assets/libs/smooth-scroll/dist/smooth-scroll.min.js"></script>
    <script src="assets/libs/typed.js/lib/typed.min.js"></script>
    <script src="assets/libs/sweetalert2/dist/sweetalert2.min.js" type="text/javascript"></script>

    <!-- Map -->
    <script src="https://api.mapbox.com/mapbox-gl-js/v0.53.0/mapbox-gl.js"></script>

    <!-- Theme JS -->
    <script src="assets/js/theme.min.js"></script>
    @stack('additional_js')
  </body>
</html>
