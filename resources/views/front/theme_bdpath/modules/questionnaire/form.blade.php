@extends('front.'.config('bookdose.theme_front').'.tpl_front')

@section('title', 'Questionnaire')

@push('additional_css')
<link rel="stylesheet" href="{{ asset('auth/'.config('bookdose.theme_front').'/assets/css/reward.css') }}">
<link rel="stylesheet" href="{{ asset(config('bookdose.app.folder').'/'.config('bookdose.app.custom_css')) }}">
<style type="text/css">
.form-check label { cursor: pointer; }
label.error { color: red; }
.form-check {
	font-size: 1.3rem;
	line-height: 1.7rem;
}
.form-group > label {
	color: #333; 
	font-size: 1.3rem; 
}
.desc p { margin-bottom: 0; }
</style>
@endpush

@section('content')
<div class="container">
	<h1 class="pt-4">{{ $content->title }}</h1>
	<div class="desc lead p-4 bg-light">{!! $content->description !!}</div>

	<form id="frm_main" action="{{ route('questionnaire.submit', $content->slug) }}" method="post">
	@csrf
	@forelse ($content->fields as $k=>$field)
		<div class="form-group pb-4">
			@if (!empty($field->section_label))
				<h2 class="pt-4 pb-0 text-primary">{{ $field->section_label }}</h2>
			@else
				@if ($field->is_required == true)
					<label><sup><i class="fas fa-asterisk text-danger fs-very-small"></i></sup> {{ $field->label }}</label>
				@else
					<label>{{ $field->label }}</label>
				@endif
			@endif

			@switch ($field->input_type)
				@case ('text')
					<input name="ff_{{ $field->id }}" type="text" class="form-control {{ $field->is_required ? 'required' : '' }}" placeholder="" value="{{ old('ff_'.$field->id) }}" autocomplete="off">
					@break

				@case ('textarea')
					<textarea name="ff_{{ $field->id }}" class="form-control {{ $field->is_required ? 'required' : '' }}">{{ old('ff_'.$field->id) }}</textarea>
					@break

				@case ('dropdown')
					<select name="ff_{{ $field->id }}" class="form-select {{ $field->is_required ? 'required' : '' }}">
						<?php $arr_options = $field->options; ?>
						<option value="">โปรดระบุ</option>
						@foreach ($arr_options as $v)
							<option>{{ $v }} </option>
						@endforeach
					</select>
					@break

				@case ('radio')
					<?php $arr_options = $field->options; ?>
						@foreach ($arr_options as $k=>$v)
						<div class="form-check">
							<input class="form-check-input {{ $field->is_required ? 'required' : '' }}" type="radio" name="ff_{{ $field->id }}" id="ff_{{ $field->id.'_'.$k }}" value="{{ $v }}">
							<label class="form-check-label font-sec-th" for="ff_{{ $field->id.'_'.$k }}">
								{{ $v }}
							</label>
						</div>
						@endforeach
					@break

				@case ('checkbox')
					<?php $arr_options = $field->options; ?>
						@foreach ($arr_options as $k=>$v)
						<div class="form-check">
							<input class="form-check-input {{ $field->is_required ? 'required' : '' }}" type="checkbox" name="ff_{{ $field->id }}[]" id="ff_{{ $field->id.'_'.$k }}" value="{{ $v }}">
							<label class="form-check-label font-sec-th" for="ff_{{ $field->id.'_'.$k }}">
								{{ $v }}
							</label>
						</div>
						@endforeach
					@break
					
			@endswitch

			@if (!empty($field->help_text))
				<small class="form-text text-muted">{{ $field->help_text }}</small>
			@endif
		</div>
	@empty
	@endforelse

	<div class="form-group text-center mb-5">
		<a id="btn_submit" href="javascript:void(0);" class="btn btn-primary btn-sm-block px-5">SUBMIT</a>
	</div>
	</form>
</div>
@endsection

@push('additional_js')
<script type="text/javascript">
function validate() 
{
	$('#frm_main').validate({
		errorPlacement: function(error, el) {
      	$(el).closest('.form-group').append(error);
      }
	});
	if ($('#frm_main').valid()) {
		$('#btn_submit').text('SUBMITTING...').addClass('disabled').prop('disabled', true);
		$('#frm_main').submit();
	}
	else {
		$('.error:first').focus();
		$('#btn_submit').text('SUBMIT').removeClass('disabled').removeAttr('disabled');
		return false;
	}
}

$(function() {
	@if (!request()->is('*/preview'))
		$('#btn_submit').click(function() {
			validate();
		});
	@endif
})
</script>
@endpush