@extends('back.'.config('bookdose.theme_back').'.tpl.tpl_admin')

@section('title', 'News')
@section('page_title', 'News')

@section('topbar_button')
<a href="{{ route('admin.news.index') }}" class="btn btn-label-brand btn-bold">
	<i class="fa fa-arrow-left"></i> Back
</a>
@isset ($article)
<a href="{{ route('admin.news.create') }}" class="btn btn-outline-brand btn-bold">
	<i class="fa fa-plus"></i> Add new
</a>
@endisset
@endsection

@push('additional_css')
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<link href="css/pages/wizard/wizard-4.css" rel="stylesheet" type="text/css" />
<style type="text/css">
	.kt-wizard-v4 .kt-wizard-v4__nav .kt-wizard-v4__nav-items .kt-wizard-v4__nav-item {
		flex: 0 0 20%;
	}
	.kt-wizard-v4__nav-label-title,
	.kt-wizard-v4 .kt-wizard-v4__nav .kt-wizard-v4__nav-items .kt-wizard-v4__nav-item .kt-wizard-v4__nav-body .kt-wizard-v4__nav-label .kt-wizard-v4__nav-label-title { 
		font-size: 1rem;
		font-weight: 600; 
	}
	.kt-wizard-v4 .kt-wizard-v4__nav .kt-wizard-v4__nav-items .kt-wizard-v4__nav-item .kt-wizard-v4__nav-body {
		padding: 2rem;
	}
	.kt-wizard-v4__nav-items{
		justify-content: start !important;
	}
	</style>
@endpush

@section('content')
<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">

	<div class="kt-wizard-v4" id="kt_wizard_v4" data-ktwizard-state="step-first">
		<div class="kt-wizard-v4__nav">
			<div class="kt-wizard-v4__nav-items">
				<!-- Article Info -->
				<a id="tab_step_general" class="tab-form-step kt-wizard-v4__nav-item" href="javascript:;" data-step="general" data-ktwizard-type="step" data-ktwizard-state="current">
					<div class="kt-wizard-v4__nav-body">
						<div class="kt-wizard-v4__nav-number">1</div>
						<div class="kt-wizard-v3__nav-label w-75">
							<div class="kt-wizard-v4__nav-label-title d-flex justify-content-between align-items-center">Article Form
								<div>
									@isset($article)
										@if($article->status == 1)
											<span class="kt-badge kt-badge--success kt-badge--dot fs-10"></span>&nbsp;<span class="kt-font-bold kt-font-success fs-10">Active</span>
										@else
											<span class="kt-badge kt-badge--danger kt-badge--dot fs-10"></span>&nbsp;<span class="kt-font-bold kt-font-danger fs-10">Inactive</span>
										@endif
									@endisset
								</div>
							</div>
						</div>
					</div>
				</a>
				<!-- Manage Comment -->
				<a id="tab_step_fields" class="tab-form-step kt-wizard-v4__nav-item" href="{{ !empty($article->id) ? route('admin.news.comment.index',["article_id" => $article->id]) : 'javascript:;' }}" data-step="fields" data-ktwizard-type="step" data-ktwizard-state="pending">
					<div class="kt-wizard-v4__nav-body ">
						<div class="kt-wizard-v4__nav-number">2</div>
						<div class="kt-wizard-v4__nav-label">
							<div class="kt-wizard-v4__nav-label-title">
								Manage Comment
							</div>
						</div>
					</div>
				</a>
			</div>
		</div>
	</div>

	<form id="frm_main" class="kt-form" action="{{ isset($article) ? route('admin.news.update', $article->id) : route('admin.news.store') }}" method="POST" enctype="multipart/form-data">
		@csrf
		@method('POST')
		{{-- {{ isset($article) ? method_field('PUT') : method_field('POST') }} --}}

		<input type="hidden" id="save_option" name="save_option" value="">
		<input type="hidden" id="id" name="id" value="{{ $article->id ?? '' }}">
		<div class="kt-portlet">
			<div class="kt-portlet__head">
				<div class="kt-portlet__head-label">
					<h3 id="section_title" class="kt-portlet__head-title">
						{{ !empty($article) ? 'Edit news' : 'Create new news'}}
					</h3>
				</div>
				<div class="kt-portlet__head-toolbar">
					<div class="kt-form__actions">
						 <button id="btn_save" class="btn btn-brand btn-bold btn-wide kt-font-transform-u" onClick="validate()">
								<?=(empty($article) ? 'Save' : 'Update')?>
						 </button>
						 <a href="{{ route('admin.news.index') }}" class="ml-1 btn btn-secondary btn-bold btn-wide kt-font-transform-u">Cancel</a>
						 <button id="btn_preview" class="ml-4 btn btn-outline-success btn-bold btn-wide kt-font-transform-u" onClick="preview()">
							<i class="fas fa-search"></i> Preview
						</button>
					</div>
				</div>
			</div>
			<div class="kt-portlet__body kt-portlet__body--fit">
				{{-- Display Success Message Area --}}
				@include('back.'.config('bookdose.theme_back').'.includes.alert_success')

			  	{{-- Display Error Area --}}
				@include('back.'.config('bookdose.theme_back').'.includes.alert_danger')

				<div class="kt-portlet__body">

					<div class="form-group">
						<label><sup><i class="la la-asterisk fs-10 text-danger"></i></sup> ชื่อบทความ:</label>
						<input id="title" name="title" type="text" class="form-control required" placeholder="Enter article title" value="{{ $article->title ?? old('title') }}" autocomplete="off">
					</div>

					<div class="form-group">
						<label><sup><i class="la la-asterisk fs-10 text-danger"></i></sup> บทคัดย่อ:</label>
						<textarea name="excerpt" class="form-control" rows="5">{{ $article->excerpt ?? old('excerpt') }}</textarea>
					</div>

					<div class="form-group">
						<label>ผู้เขียน:</label>
						<input id="creator" name="creator" type="text" class="form-control" placeholder="Enter creator name" value="{{ $article->creator ?? old('creator') }}" autocomplete="off">
					</div>

					<div class="form-group">
						<label><sup><i class="la la-asterisk fs-10 text-danger"></i></sup> หมวดหมู่:</label>
						<div>
							@include('back.'.config('bookdose.theme_back').'.modules.news.field_category')
						</div>
					</div>

					<div class="form-group">
						<label><sup><i class="la la-asterisk fs-10 text-danger"></i></sup> เลือกไฟล์ปก:</label>
						<input id="cover_file_path" name="cover_file_path" type="file" class="form-control" accept="image/*">
						<div class="mt-3">
							<span class="form-text text-muted">1. กว้างxยาว ที่แนะนำคือ 1920 × 740px</span>
							<span class="form-text text-muted">2. ขนาดไฟล์รูปไม่ควรเกิน 5MB</span>
						</div>
					</div>
					@if (isset(($article->cover_file_path)) && !empty($article->cover_file_path))
					<div class="form-group">
						<img src="{{ Storage::url($article->cover_file_path) }}" class="img-fluid w-25">
					</div>
					@endif

					<div class="form-row">
						<div class="form-group col-md-4 col-lg-3">
							<label>วันที่เผยแพร่:</label>
							<div class="input-group date">
								<input id="dp_published_date" name="published_date" type="text" class="form-control kt_datepicker required" data-date-format="dd/mm/yyyy"/ autocomplete="off" value="{{ !isset($product_copy) ? date('d/m/Y', strtotime('now')) : (!empty($product_copy->published_at) ? date('d/m/Y', strtotime($product_copy->published_at)) : '') }}">
								<div class="input-group-append">
									<span class="input-group-text">
										<i class="la la-calendar"></i>
									</span>
								</div>
							</div>
						</div>
					</div>

					<div class="form-group">
						<label>URL อ้างอิง:</label>
						<input name="ref_url" type="text" class="form-control" placeholder="https://" value="{{ $article->ref_url ?? old('ref_url') }}" autocomplete="off">
					</div>

					<div class="form-group">
						<div class="kt-checkbox-inline">
							<label class="kt-checkbox kt-checkbox--brand kt-checkbox--{{ ($article->is_recommended ?? '') ? 'solid' : 'bold' }}">
								 <input type="checkbox" name="is_recommended" value="1" {{ ($article->is_recommended ?? '') ? 'checked' : '' }}> แสดงในหมวดหมู่แนะนำ
								 <span></span>
							</label>
						</div>
					</div>

					<div class="form-group">
						<label>สถานะ:</label>
						<div class="kt-radio-inline">
							<label class="kt-radio kt-radio--bold kt-radio--brand kt-radio--check-bold">
								 <input type="radio" name="status" value="1" checked=""> Active
								 <span></span>
							</label>
							<label class="kt-radio kt-radio--bold kt-radio--brand">
								 @if (isset($article))
								 	<input type="radio" name="status" value="0" {{ $article->status == '0' ? 'checked' : '' }}> Inactive
								 @else
								 	<input type="radio" name="status" value="0" checked=""> Inactive
								 @endif
								 <span></span>
							</label>
						 </div>
					</div>
				</div>

			</div>
		</div>

		@include('back.'.config('bookdose.theme_back').'.modules.news.section_content')
		@include('back.'.config('bookdose.theme_back').'.modules.news.modal_article_category')
	</form>
</div>
@endsection



@push('additional_js')
<script type="text/javascript">
function validate() 
{
	$('#frm_main').validate();
	if ($('#frm_main').valid()) {
		$('#frm_main').attr('action', "{{ isset($article) ? route('admin.news.update', [$article->id]) : route('admin.news.store') }}");
		$('#frm_main').removeAttr('target');
		save();
	}
	else {
		scrollToClass('error');
		return false;
	}
}

function addNewCategory()
{
	$('#modal_category').modal('show');
	$('#modal_category').find(':text:first').focus();
}

$(document).ready(function() {
	$.fn.datepicker.defaults.format = "dd/mm/yyyy";
	$('#dp_published_date').datepicker({
	    autoclose: true,
	    todayHighlight: true,
	    todayBtn: 'linked',
	});
	$('.input-group-append').click(function() {
		$(this).siblings(':text').trigger('focus');
	});

	$(':text:first').focus();
});

function preview()
{
	$('#frm_main').validate({
		errorPlacement: function(error, element) {
       	if (element.attr('type') == 'file') {
       		error.insertBefore(element.siblings('.help-text'));
       	}
       	else if (element.hasClass('kt_datepicker')) {
       		error.insertAfter(element.closest('.input-group.date'));
       	}
       	else {
          	error.insertAfter(element);
       	}
      }
	});
	if ($('#frm_main').valid()) {
		$('#frm_main').attr('action', "{{ config('bookdose.app.belib_url').'/admin/article/preview/article?system=center'}}");
		$('#frm_main').attr('target', '_blank');
		$('#frm_main').submit();
	}
	else {
		$('.error:first').focus();
		return false;
	}
}
</script>
@endpush
