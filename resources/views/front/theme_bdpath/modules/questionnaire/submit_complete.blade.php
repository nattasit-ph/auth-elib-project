@extends('front.'.config('bookdose.theme_front').'.tpl_front')
@section('title', 'Questionnaire')

@push('additional_css')
@endpush

@section('content')
<div class="container">
	<div class="row my-5">
		@if ($status == 'success')
			<div class="col p-5 bg-light d-flex align-items-center">
				<i class="fas fa-check-circle fa-2x me-3 text-success"></i> {{ __('questionnaire.submit_success') }}
			</div>
		@else
			<div class="col p-5 bg-light d-flex align-items-center">
				<i class="fas fa-times-circle fa-3x mr-3 text-danger"></i> {{ __('questionnaire.submit_failed') }}
			</div>
		@endif
	</div>
	<div class=" mb-5 text-center">
		<a href="{{ route('home') }}" class="btn btn-primary px-5">{{ __('questionnaire.back_to_main_page') }}</a>
	</div>
</div>
@endsection