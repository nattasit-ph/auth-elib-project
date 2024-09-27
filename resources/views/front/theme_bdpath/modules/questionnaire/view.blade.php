@extends('front.'.config('bookdose.theme_front').'.tpl_front')

@section('title', 'Questionnaire')

@push('additional_css')
<style type="text/css">
.form-check {
	font-size: 1.3rem;
	line-height: 1.7rem;
}
.form-group > label {
	color: #333; 
	font-size: 1.3rem; 
}
.form-check-input:disabled~.form-check-label, .form-check-input[disabled]~.form-check-label {
	color: #333;
}
.desc p { margin-bottom: 0; }
</style>
@endpush

@section('content')
<div class="container">
	<h1 class="pt-4">{{ $form->title }}</h1>
	<div class="desc lead p-4 bg-light">{!! $form->description !!}</div>

	<div class="d-flex justify-content-between text-primary mt-3 pb-2 border-bottom">
		<div>ผู้ตอบแบบสอบถาม: {{ $sender->name }}</div>
		<div>วันที่ตอบแบบฟอร์ม: {{ \Carbon\Carbon::parse($form->created_at)->format('d M Y') }}</div>
	</div>

	<div>
		@forelse ($form->fields as $k=>$field)
			<div class="form-group pb-2">
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
					@case ('dropdown')
						<p>{{ (isset($form_data[$field->id]) && !empty($form_data[$field->id]) ? $form_data[$field->id] : '-') }}</p>
						@break

					@case ('textarea')
						<p>{!! (isset($form_data[$field->id]) && !empty($form_data[$field->id]) ? nl2br($form_data[$field->id]) : '-') !!}</p>
						@break

					@case ('radio')
						<?php $arr_options = $field->options; ?>
							@foreach ($arr_options as $k=>$v)
							<div class="form-check">
								<input class="form-check-input {{ $field->is_required ? 'required' : '' }}" type="radio" name="ff_{{ $field->id }}" id="ff_{{ $field->id.'_'.$k }}" value="{{ $v }}" disabled="" {{ (isset($form_data[$field->id]) && $form_data[$field->id]==$v ? 'checked' : '') }}>
								<label class="form-check-label font-sec-th {{ (isset($form_data[$field->id]) && $form_data[$field->id]==$v ? 'font-weight-bold' : '') }}" for="ff_{{ $field->id.'_'.$k }}">
									{{ $v }}
								</label>
							</div>
							@endforeach
						@break

					@case ('checkbox')
						<?php $arr_options = $field->options; ?>
							@foreach ($arr_options as $k=>$v)
							<div class="form-check">
								<input class="form-check-input {{ $field->is_required ? 'required' : '' }}" type="checkbox" name="ff_{{ $field->id }}[]" id="ff_{{ $field->id.'_'.$k }}" value="{{ $v }}" disabled="" {{ (isset($form_data[$field->id]) && in_array($v, $form_data[$field->id]) ? 'checked' : '') }}>
								<label class="form-check-label font-sec-th {{ (isset($form_data[$field->id]) && in_array($v, $form_data[$field->id]) ? 'font-weight-bold' : '') }}" for="ff_{{ $field->id.'_'.$k }}">
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
	</div>
</div>
@endsection

@push('additional_js')
<script type="text/javascript">
$(function() {

})
</script>
@endpush