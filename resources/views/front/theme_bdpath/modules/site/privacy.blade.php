@extends('front.'.config('bookdose.theme_front').'.tpl_front')

@section('title', $site_info->meta_label ?? 'Privacy Policy')

@section('content')
<div class="container">
    <div class="d-flex justify-content-center pt-4">
        <h1 class="display-4">{{ $site_info->meta_label }}</h1>
    </div>
    <div class="p-3 py-md-5">
    	<p class="lead">{!! nl2br($site_info->meta_value) ?? '' !!}</p>
    </div>
</div>
@endsection