@extends('back.'.config('bookdose.theme_back').'.tpl.tpl_admin')

@section('title', __('menu.back.interest'))
@section('page_title', __('menu.back.interest'))
@section('topbar_button')
<a href="{{ route('admin.interest.index') }}" class="btn btn-label-brand btn-bold">
	<i class="fa fa-arrow-left"></i> Back
</a>
@isset ($interest)
<a href="{{ route('admin.interest.create') }}" class="btn btn-outline-brand btn-bold">
	<i class="fa fa-plus"></i> Add new
</a>
@endisset
@endsection

@push('additional_css')
<style type="text/css">

</style>
@endpush

@section('content')
<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
	<div class="kt-portlet">
		<div class="kt-portlet__head">
			<div class="kt-portlet__head-label">
				<h3 id="section_title" class="kt-portlet__head-title">
					{{ !empty($interest) ? 'Edit site interest' : 'Create new interest'}}
				</h3>
			</div>
			<div class="kt-portlet__head-toolbar">
				<div class="kt-form__actions">
					<button id="btn_save" class="btn btn-brand btn-bold btn-wide kt-font-transform-u" onClick="validate()">
						<?= (empty($interest) ? 'Save' : 'Update') ?>
					</button>
					<?php if (empty($interest)) { ?>
						<a id="btn_save_and_add_new" href="javascript:;" class="ml-1 btn btn-outline-brand btn-bold btn-wide kt-font-transform-u" onClick="saveAndContinue()">Save & Add new</a>
					<?php } ?>
					<a href="{{ route('admin.interest.index') }}" class="ml-1 btn btn-secondary btn-bold btn-wide kt-font-transform-u">Cancel</a>
				</div>
			</div>
		</div>
		<div class="kt-portlet__body kt-portlet__body--fit">
			{{-- Display Success Message Area --}}
			@include('back.'.config('bookdose.theme_back').'.includes.alert_success')

			{{-- Display Error Area --}}
			@include('back.'.config('bookdose.theme_back').'.includes.alert_danger')

			<form id="frm_main" class="kt-form" action="{{ isset($interest) ? route('admin.interest.update', $interest->id) : route('admin.interest.store') }}" method="POST" enctype="multipart/form-data">
				@csrf
				{{ isset($interest) ? method_field('PUT') : method_field('POST') }}

				<input type="hidden" id="save_option" name="save_option" value="">
				<input type="hidden" id="id" name="id" value="{{ $interest->id ?? '' }}">
				<div class="kt-portlet__body">
					<div class="kt-section mb-0">

						<div class="form-group">
							<label><sup><i class="la la-asterisk fs-10 text-danger"></i></sup> ชื่อ:</label>
							<input id="txt_interest_name" name="title" type="text" class="form-control required" placeholder="Enter interest name" value="{{ $interest->title ?? old('title') }}" autocomplete="off">
						</div>

						<div class="form-group">
							<label><sup><i class="la la-asterisk fs-10 text-danger"></i></sup> คำอธิบาย:</label>
							<input name="description" type="text" class="form-control" placeholder="Enter description required" value="{{ $interest->description ?? old('description') }}">
						</div>

						<div class="form-group">
							<label><sup><i class="la la-asterisk fs-10 text-danger"></i></sup>เลือกไฟล์รูปภาพ:</label>
							<input id="file_path" name="file_path" type="file" class="form-control required" accept="image/*">
							<div class="my-3">
								<span class="form-text text-muted">2. ขนาดไฟล์รูปไม่ควรเกิน 5MB</span>
							</div>
						</div>
						@if (isset(($interest->file_path)) && !empty($interest->file_path))
						<div class="form-group">
							<img src="{{ Storage::url($interest->file_path) }}" class="img-fluid w-25">
						</div>
						@endif

						<div class="form-group">
							<label>ลำดับ:</label>
							<input id="txt_weight" name="weight" type="number" class="form-control" placeholder="Enter weight. e.g. 10, 20, -10" value="{{ $interest->weight ?? old('weight') }}">
						</div>

						@if(config('bookdose.app.belib_url') != '')
						@include('back.'.config('bookdose.theme_back').'.modules.interest.form.form_belib')
						@endif

						@if(config('bookdose.app.learnext_url') != '')
						@include('back.'.config('bookdose.theme_back').'.modules.interest.form.form_elearn')
						@endif

						<div class="form-group">
							<label>สถานะ:</label>
							<div class="kt-radio-inline">
								<label class="kt-radio kt-radio--bold kt-radio--brand kt-radio--check-bold">
									<input type="radio" name="status" value="1" checked=""> Active
									<span></span>
								</label>
								<label class="kt-radio kt-radio--bold kt-radio--brand">
									@if (isset($interest))
									<input type="radio" name="status" value="0" {{ $interest->status == '0' ? 'checked' : '' }}> Inactive
									@else
									<input type="radio" name="status" value="0" {{ old('status') =='0' ? 'checked' : '' }}> Inactive
									@endif
									<span></span>
								</label>
							</div>
						</div>

					</div>
				</div>
			</form>
		</div>
	</div>

</div>
@endsection

@push('additional_js')
<script type="text/javascript">
	function validate() {
		$('#frm_main').validate();
		if ($('#frm_main').valid()) {
			save();
		} else {
			scrollToClass('error');
			return false;
		}
	}

	$(document).ready(function() {
		$('[data-toggle=popover]').popover({
			'html': true,
			'placement': 'top',
			'trigger': 'focus'
		});
	});
</script>
@endpush