@extends('back.'.config('bookdose.theme_back').'.tpl.tpl_admin')

@section('title', 'Event')
@section('page_title', 'Event')
@section('topbar_button')
<a href="{{ route('admin.event.index') }}" class="btn btn-label-brand btn-bold">
	<i class="fa fa-arrow-left"></i> Back
</a>
@isset ($content)
<a href="{{ route('admin.event.create') }}" class="btn btn-outline-brand btn-bold">
	<i class="fa fa-plus"></i> Add New
</a>
@endisset
@endsection

@push('additional_css')
<link href="css/pages/wizard/wizard-2.css" rel="stylesheet" type="text/css" />
<link href="css/pages/wizard/wizard-3.css" rel="stylesheet" type="text/css" />
<style type="text/css">
.kt-wizard-v3 .kt-wizard-v3__nav .kt-wizard-v3__nav-items .kt-wizard-v3__nav-item {
	flex: 0 0 50%;
}
.kt-wizard-v3 .kt-wizard-v3__nav .kt-wizard-v3__nav-items .kt-wizard-v3__nav-item .kt-wizard-v3__nav-body.disabled {
	color: #ddd !important;
}
.select2-selection.select2-selection--multiple {
	padding: 0.7em;
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
					{{ $page_header ?? 'Create new '}}
				</h3>
			</div>
			@if ($step == 'general')
			<div class="kt-portlet__head-toolbar">
				<div class="kt-form__actions">
					 <button id="btn_save" class="btn btn-brand btn-bold btn-wide kt-font-transform-u" onClick="validate()">
							<?=(request()->is('admin/event/create*') ? 'Save' : 'Update')?>
					 </button>

					 <a href="{{ route('admin.event.index') }}" class="ml-1 btn btn-secondary btn-bold btn-wide kt-font-transform-u">Cancel</a>
					 
				</div>
			</div>
			@elseif ($step == 'invitation')
			<div class="kt-portlet__head-toolbar">
				<div class="kt-form__actions">
					<form id="frm_export" method="get" action="{{ route('admin.event.join.exportToExcel') }}">
						<input type="hidden" id="hd_event_id" name="hd_event_id" value="{{ $event->id ?? '' }}">
						<input type="hidden" id="hd_keyword" name="hd_keyword" value="">
						 <a id="btn_export_to_excel" class="btn btn-success btn-wide text-white">
								<i class="fa fa-file-excel"></i> Export to excel
						 </a>
					</form>
				</div>
			</div>
			@endif
		</div>


		<div class="kt-portlet__body kt-portlet__body--fit">
			<div class="kt-grid kt-wizard-v3 kt-wizard-v3--white" id="kt_wizard_v3" data-ktwizard-state="first">
				@include('back.'.config('bookdose.theme_back').'.modules.event.section_steps')

				<div class="kt-grid__item kt-grid__item--fluid kt-wizard-v3__wrapper">
					<div class="w-100">
						@include('back.'.config('bookdose.theme_back').'.modules.event.include_form_'.($step ?? 'general').($action ?? ''))
					</div>
				</div>
			</div>
		</div>

	</div>

</div>
@endsection


@push('additional_js')
<script type="text/javascript">
$(function() {

	$('#btn_export_to_excel').on('click', function(e) {
		e.preventDefault();
		$('#hd_keyword').val($('input[type="search"]').val());
		$('#frm_export').submit();
	});

})
</script>
@endpush