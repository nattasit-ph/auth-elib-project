@extends('front.'.config('bookdose.theme_front').'.tpl_front')

@section('title', 'Reward Store'.config('bookdose.app.meta_title'))

@push('additional_css')
<link rel="stylesheet" href="{{ asset('auth/'.config('bookdose.theme_front').'/css/reward.css') }}">
<link rel="stylesheet" href="{{ asset(config('bookdose.app.folder').'/'.config('bookdose.app.custom_css')) }}">
@endpush

@section('content')
@include('front.'.config('bookdose.theme_front').'.modules.reward.section_main_head')
@include('front.'.config('bookdose.theme_front').'.modules.reward.section_main_category')
@endsection

@push('additional_js')
<!-- aos -->
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

<script>
    AOS.init();

    $(function() {
        $('#point').prop('Counter', 0).animate({
            Counter: parseInt('{{Auth::user()->points}}')
        }, {
            duration: 1500,
            easing: 'swing',
            step: function(now) {
                $(this).text(Math.ceil(now));
            }
        });
    });
</script>
@endpush