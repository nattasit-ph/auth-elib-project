@extends('back.'.config('bookdose.theme_back').'.tpl.tpl_admin')

@section('title', 'Questionnaire')
@section('page_title', 'Questionnaire')
@section('topbar_button')
<a href="{{ route('admin.questionnaire.index', $org_slug) }}" class="btn btn-label-brand btn-bold">
	<i class="fa fa-arrow-left"></i> Back
</a>
@isset ($content)
<a href="{{ route('admin.questionnaire.create', $org_slug) }}" class="btn btn-outline-brand btn-bold">
	<i class="fa fa-plus"></i> Add New
</a>
@endisset
@endsection

@push('additional_css')
<link href="css/pages/wizard/wizard-2.css" rel="stylesheet" type="text/css" />
<link href="css/pages/wizard/wizard-3.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-notify/0.2.0/css/bootstrap-notify.min.css">
<style type="text/css">
.kt-wizard-v3 .kt-wizard-v3__nav .kt-wizard-v3__nav-items .kt-wizard-v3__nav-item {
	flex: 0 0 33%;
}
</style>
@endpush

@section('content')
<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
	{{-- Display Success Message Area --}}
	@include('back.'.config('bookdose.theme_back').'.includes.alert_success')

  	{{-- Display Error Area --}}
  	@include('back.'.config('bookdose.theme_back').'.includes.alert_danger')

	<div class="kt-portlet">
		<div class="kt-portlet__head">
			<div class="kt-portlet__head-label">
				<h3 id="section_title" class="kt-portlet__head-title">
					{{ !empty($content) ? 'Edit '.$content->title : 'Create new form'}}
				</h3>
			</div>

			@if (in_array($step, ['general', 'fields']))
				<div class="kt-portlet__head-toolbar">
					<div class="kt-form__actions">
						 <button id="btn_save" class="btn btn-brand btn-bold btn-wide kt-font-transform-u" onClick="validate()">
								<?=(request()->is($org_slug.'/admin/questionnaire/create*') ? 'Save' : 'Update')?>
						 </button>

						 <a href="{{ route('admin.questionnaire.index', $org_slug) }}" class="ml-1 btn btn-secondary btn-bold btn-wide kt-font-transform-u">Cancel</a>

						 @isset ($content)
					 	<button id="btn_preview" class="ml-4 btn btn-outline-success btn-bold btn-wide kt-font-transform-u" onClick="preview()">
					 		<i class="fas fa-search"></i> Preview
					 	</button>
					 	@endisset
					</div>
				</div>
			@else ($step == 'submissions')
				<div class="kt-portlet__head-toolbar">
					<div class="kt-form__actions">
						<form id="frm_export" method="get" action="{{ route('admin.questionnaire.submission.exportToExcel', $org_slug) }}">
							<input type="hidden" id="hd_lang" name="hd_lang" value="">
							<input type="hidden" id="hd_form_id" name="hd_form_id" value="{{ $content->id }}">
							<input type="hidden" id="hd_status" name="hd_status" value="">
							<input type="hidden" id="hd_submitted_start" name="hd_submitted_start" value="">
							<input type="hidden" id="hd_submitted_end" name="hd_submitted_end" value="">
							<input type="hidden" id="hd_keyword" name="hd_keyword" value="">
							<a id="btn_export_to_excel" href="javascript:void(0);" class="btn btn-success">
								<i class="fa fa-file-excel"></i> Export to excel
							</a>
						</form>
					</div>
				</div>
			@endif
		</div>

		<div class="kt-portlet__body kt-portlet__body--fit">
			<div class="kt-grid kt-wizard-v3 kt-wizard-v3--white" id="kt_wizard_v3" data-ktwizard-state="first">
				@include('back.'.config('bookdose.theme_back').'.modules.questionnaire.section_steps')

				<div class="kt-grid__item kt-grid__item--fluid kt-wizard-v3__wrapper">
					<div class="w-100">
						@include('back.'.config('bookdose.theme_back').'.modules.questionnaire.include_step_'.($step ?? 'general'))
					</div>
				</div>
			</div>

		</div>

	</div>
</div>
@endsection



@push('additional_js')
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-notify/0.2.0/js/bootstrap-notify.min.js"></script>
<script type="text/javascript">
function validate()
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
		$('#frm_main').attr('action', "{{ isset($content) ? route('admin.questionnaire.update', $org_slug) : route('admin.questionnaire.store', $org_slug) }}");
		$('#frm_main').removeAttr('target');
		save(3);
	}
	else {
		$('.error:first').focus();
		return false;
	}
}

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
		$('#frm_main').attr('action', "{{ route('admin.questionnaire.preview', $org_slug) }}");
		$('#frm_main').attr('target', '_blank');
		$('#frm_main').submit();
	}
	else {
		$('.error:first').focus();
		return false;
	}
}

$(document).ready(function() {
	$('.summernote').summernote({
   	height: 150,
   	callbacks:{
	onPaste: function(e) {
         console.log('paste it')
         const bufferText = ((e.originalEvent || e).clipboardData || window.clipboardData).getData('Text')
         e.preventDefault()
         setTimeout(function () {
             document.execCommand('insertText', false, bufferText)
         }, 10)
     },
	},
   	toolbar: [
		    // [groupName, [list of button]]
		    ['style', ['clear', 'bold', 'italic', 'underline']],
		    ['font', ['strikethrough', 'superscript', 'subscript']],
		    ['fontsize', ['fontsize']],
		    ['color', ['color']],
		    ['para', ['ul', 'ol', 'paragraph']],
		    ['table', ['table']],
  			 ['insert', ['link', 'picture', 'video']],
		  ]
   });
	$(':text:first').focus();
});
</script>
@endpush
