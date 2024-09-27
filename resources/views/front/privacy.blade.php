@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-center">
        <h2>{{ __('Privacy Policy') }}</h2>
    </div>
    <p class="lead">{!! nl2br($site_info->meta_value) ?? '' !!}</p>
</div>
@endsection
