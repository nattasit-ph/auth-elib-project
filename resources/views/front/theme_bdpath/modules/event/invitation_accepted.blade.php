@extends('front.'.config('bookdose.theme_front').'.tpl_front')

@section('title', 'Reward Store'.config('bookdose.app.meta_title'))

@push('additional_css')
<link rel="stylesheet" href="{{ asset(config('bookdose.app.folder').'/'.config('bookdose.app.custom_css')) }}">
@endpush

@section('content')
<section class="container-lg py-5 py-md-5 text-center">
	<h1 class="text-primary">Invitation Accepted.</h1>
	<p class="lead">Now you're already joined the event, {{ $event->title }}</p>
	<a href="{{ config('bookdose.app.km_url') }}/event" class="btn btn-primary px-5"><i class="far fa-calendar-alt me-2"></i> View all events</a>
</section>
@endsection

@push('additional_js')
<script type="text/javascript">
$(function() {
	$('#bc_system').text('Event Invitation')
})
</script>
@endpush