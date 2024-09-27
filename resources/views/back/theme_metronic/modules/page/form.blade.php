@extends('back.'.config('bookdose.theme_back').'.tpl.tpl_admin')

@section('title', 'Pages')
@section('page_title', 'Pages')

@section('topbar_button')
<a href="{{ route('admin.pages.index') }}" class="btn btn-label-brand btn-bold">
	<i class="fa fa-arrow-left"></i> Back
</a>
@isset ($article)
<a href="{{ route('admin.pages.create') }}" class="btn btn-outline-brand btn-bold">
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
							<div class="kt-wizard-v4__nav-label-title d-flex justify-content-between align-items-center">Pages Form
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

			</div>
		</div>
	</div>

	<form id="frm_main" class="kt-form" action="{{ isset($article) ? route('admin.pages.update', $article->id) : route('admin.pages.store') }}" method="POST" enctype="multipart/form-data">
		@csrf
		@method('POST')
		{{-- {{ isset($article) ? method_field('PUT') : method_field('POST') }} --}}

		<input type="hidden" id="save_option" name="save_option" value="">
		<input type="hidden" id="id" name="id" value="{{ $article->id ?? '' }}">
		<div class="kt-portlet">
			<div class="kt-portlet__head">
				<div class="kt-portlet__head-label">
					<h3 id="section_title" class="kt-portlet__head-title">
						{{ !empty($article) ? 'Edit Pages' : 'Create new pages'}}
					</h3>
				</div>
				<div class="kt-portlet__head-toolbar">
					<div class="kt-form__actions">
						 <button id="btn_save" class="btn btn-brand btn-bold btn-wide kt-font-transform-u" onClick="validate()">
								<?=(empty($article) ? 'Save' : 'Update')?>
						 </button>
						 <a href="{{ route('admin.pages.index') }}" class="ml-1 btn btn-secondary btn-bold btn-wide kt-font-transform-u">Cancel</a>
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
						<label><sup><i class="la la-asterisk fs-10 text-danger"></i></sup> ชื่อเว็บเพจภาษาไทย (TH) :</label>
						<input id="title_th" name="title_th" type="text" class="form-control required" placeholder="Enter pages title (TH)" value="{{ $article->title_th ?? old('title_th') }}" autocomplete="off">
					</div>

					<div class="form-group">
						<label><sup><i class="la la-asterisk fs-10 text-danger"></i></sup> ชื่อเว็บเพจภาษาอังกฤษ (EN):</label>
						<input id="title_en" name="title_en" type="text" class="form-control required" placeholder="Enter pages title (EN)" value="{{ $article->title_en ?? old('title_en') }}" autocomplete="off">
					</div>

					<div class="form-group">
						<label><sup><i class="la la-asterisk fs-10 text-danger"></i></sup> ระบบ:</label>
						<select id="system" name="system" class="form-control required">
							@isset($article->system)
								@switch($article->system)
									@case('belib')
									<option value="{{$article->system}}">Belib System</option>
										@break
									@case('km')
									<option value="{{$article->system}}">KM System</option>
										@break
									@default
								@endswitch
							@endisset

							@if(!empty(config('bookdose.app.belib_url')))
							<option value="belib">Belib System</option>
							@endif
							@if(!empty(config('bookdose.app.km_url')))
							<option value="km">KM System</option>
							@endif
						</select>
					</div>

					<div class="form-group">
						<label>Pretty URL:</label>
						<input name="slug" type="text" class="form-control" placeholder="e.g. contact, about-us, room_service, etc." value="{{ $article->slug ?? old('slug') }}" autocomplete="off">
						<div class="my-2">
							<span class="form-text text-muted">
								อนุญาตให้เฉพาะตัวอักษรภาษาอังกฤษ (a-z), ตัวเลขอารบิค (0-9), เครื่องหมาย - (Hyphen) และเครื่องหมาย _ (Underscore) เท่านั้น<br>
							</span>
						</div>
					</div>

					<div class="form-group">
						<label></sup> เลือกไฟล์ปก:</label>
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

					<div class="form-group">
						<label>URL อ้างอิง:</label>
						<input name="ref_url" type="text" class="form-control" placeholder="https://" value="{{ $article->ref_url ?? old('ref_url') }}" autocomplete="off">
					</div>

                    <input type="hidden" class="form-control" id="file_url_delete" value="{{ route('admin.pages.file.delete') }}">
					<div class="form-group">
						<label>ไฟล์แนบ:</label>
						@if(!empty($article->attachments))
							@foreach($article->attachments as $item_file)
							<div id="select-file-{{$item_file->id}}" class="my-3">
								<a href="{{'/storage/'.$item_file->file_path}}" download ><i class="fas fa-file-download mr-2"></i> {{$item_file->title}}</a>
								<label class="text-danger cursor-pointer mx-3" data-id="{{ $item_file->id }}"data-title="{{ $item_file->title }}" onclick="deleteFile(this)"><i class="fas fa-trash mr-2"></i> ลบไฟล์นี้</ส>
							</div>
							@endforeach
						@endif
						<input type="file" name="attach_file[]" class="form-control" multiple="true">
						<div class="my-2">
							<span class="form-text text-muted">1. รองรับไฟล์นามสกุล .png, .gif, .jpg, .jpeg .pdf, .txt, .docx, .xlsx, .pptx, .mp4, mp3 เท่านั้น</span>
							<span class="form-text text-muted">2. รองรับขนาดไฟล์สูงสุดไม่เกิน 200MB</span>
							<span class="form-text text-muted">3. ชื่อไฟล์ที่แสดงบนเว็บไซต์จะเป็นชื่อตามไฟล์แนบที่เลือก กรุณาตรวจสอบชื่อไฟล์ก่อนอัพโหลด</span>
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
		$('#frm_main').attr('action', "{{ isset($article) ? route('admin.pages.update', [$article->id]) : route('admin.pages.store') }}");
		$('#frm_main').removeAttr('target');
		save();
	}
	else {
		scrollToClass('error');
		return false;
	}
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

	var system = $('#system').val();
	console.log(system);
	if ($('#frm_main').valid()) {
		//check system url
		switch(system) {
		case "belib":
		$('#frm_main').attr('action', "{{ config('bookdose.app.belib_url').'/admin/pages/preview/pages'}}");
			break;
		case "km":
		$('#frm_main').attr('action', "{{ config('bookdose.app.km_url').'/admin/article/preview/article?system=center'}}");
			break;
		default:
		}

		$('#frm_main').attr('target', '_blank');
		$('#frm_main').submit();
	}
	else {
		$('.error:first').focus();
		return false;
	}
}

function deleteFile(e)
{
	var id = $(e).data('id');
	var url = $('#file_url_delete').val();

	var confirm_line_1 = 'Are you sure you want to delete \n'+$(e).data('title')+'?';
	var confirm_line_2 = '';

	const swalWithBootstrapButtons = Swal.mixin({
		customClass: {
			confirmButton: 'btn btn-brand',
			cancelButton: 'btn btn-default'
		},
		buttonsStyling: false
	})

	swalWithBootstrapButtons.fire({
		title: confirm_line_1,
		text: confirm_line_2,
		type: 'warning',
		showCancelButton: !0,
		confirmButtonText: 'Yes, delete it!',
		// cancelButtonText: 'Cancel!',
	}).then((result) => {
		if (result.value) {
			$.ajaxSetup({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});
			$.ajax({
				url: url,
				method: 'POST',
				data: { id: id, name: "{{ $name ?? '' }}", lang: "{{ $lang ?? app()->getLocale() }}" },
				dataType: 'json',
			}).done(function(response) {
				if (response.status == '200') {
				}
				showNotifyOnScreen(response);
				// swalWithBootstrapButtons.fire(
				// 	response.notify_title,
				// 	response.notify_msg,
				// 	response.notify_type,
				// )
				$('#select-file-'+id).remove();
			});
		}
	})
}
</script>
@endpush
