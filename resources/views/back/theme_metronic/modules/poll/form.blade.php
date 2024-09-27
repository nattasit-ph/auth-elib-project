@extends('back.'.config('bookdose.theme_back').'.tpl.tpl_admin')

@section('title', 'Poll')
@section('page_title', 'Poll')
@section('topbar_button')
<a href="{{ route('admin.poll.index') }}" class="btn btn-label-brand btn-bold">
	<i class="fa fa-arrow-left"></i> Back
</a>
@isset ($poll)
<a href="{{ route('admin.poll.create') }}" class="btn btn-outline-brand btn-bold">
	<i class="fa fa-plus"></i> Add New
</a>
@endisset
@endsection

@push('additional_css')
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-notify/0.2.0/css/bootstrap-notify.min.css">
<style type="text/css">

</style>
@endpush

@section('content')
<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
	<form id="frm_main" class="kt-form" action="{{ isset($poll) ? route('admin.poll.update') : route('admin.poll.store') }}" method="POST" enctype="multipart/form-data">
		@csrf
		@method('POST')
		<input type="hidden" id="save_option" name="save_option" value="">
		<input type="hidden" id="id" name="id" value="{{ $poll->id ?? '' }}">

		<div class="kt-portlet">
			<div class="kt-portlet__head">
				<div class="kt-portlet__head-label">
					<h3 id="section_title" class="kt-portlet__head-title">
						{{ $page_header ?? 'Create a new poll'}}
					</h3>
				</div>
				<div class="kt-portlet__head-toolbar">
					<div class="kt-form__actions">
						 <button id="btn_save" class="btn btn-brand btn-bold btn-wide kt-font-transform-u" onClick="validate()">
								<?=(request()->is('admin/poll/create*') ? 'Save' : 'Update')?>
						 </button>
						 <a href="{{ route('admin.poll.index') }}" class="ml-1 btn btn-secondary btn-bold btn-wide kt-font-transform-u">Cancel</a>
					</div>
				</div>
			</div>
			<div class="kt-portlet__body kt-portlet__body--fit">
				{{-- Display Success Message Area --}}
				@include('back.'.config('bookdose.theme_back').'.includes.alert_success')

			  	{{-- Display Error Area --}}
			  	@include('back.'.config('bookdose.theme_back').'.includes.alert_danger')


					<div class="kt-portlet__body">
						<div class="kt-section mb-0">
							<!-- poll category start -->
		
							@if($module->has_categories == 1)
							<div class="form-group">
								<label><sup><i class="la la-asterisk fs-10 text-danger"></i></sup> หมวดหมู่:</label>
								<div>
								@include('back.'.config('bookdose.theme_back').'.modules.poll.field_category')
								</div>
							</div>
							@endif
			
							<!-- poll category end -->
							<div class="form-group">
								<label><sup><i class="la la-asterisk fs-10 text-danger"></i></sup> คำถาม:</label>
								<input id="txt_poll_question" name="question" type="text" class="form-control required" placeholder="Enter question" value="{{ $poll->question ?? old('question') }}">
							</div>

							<div class="form-row">
								<div class="form-group col-6" style="margin-bottom:0 !important;">
									<label>วันที่เริ่มต้น:</label>
									<div class="input-group date">
										<input id="dp_poll_start" name="poll_start" type="text" class="form-control kt_datepicker" data-date-format="dd/mm/yyyy" autocomplete="off" value="{{ !empty($poll->poll_start) ? date('d/m/Y', strtotime($poll->poll_start)) : '' }}">
										<div class="input-group-append">
											<span class="input-group-text">
												<i class="la la-calendar"></i>
											</span>
										</div>
									</div>
									<div class="my-3"><span class="form-text text-muted">ไม่จำเป็นต้องระบุ หากต้องการเปิดให้โหวตได้ตั้งแต่วันนี้เป็นต้นไป</span></div>
								</div>
								<div class="form-group col-6" style="margin-bottom:0 !important;">
									<label>วันที่สิ้นสุด:</label>
									<div class="input-group date">
										<input id="dp_poll_end" name="poll_end" type="text" class="form-control kt_datepicker" data-date-format="dd/mm/yyyy" autocomplete="off" value="{{ !empty($poll->poll_end) ? date('d/m/Y', strtotime($poll->poll_end)) : '' }}">
										<div class="input-group-append">
											<span class="input-group-text">
												<i class="la la-calendar"></i>
											</span>
										</div>
									</div>
									<div class="my-3"><span class="form-text text-muted">หากต้องการเปิดให้โหวตได้ตลอด ไม่มีวันสิ้นสุด ให้ทิ้งไว้เป็นค่าว่าง</span></div>
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
										 @if (isset($poll))
										 	<input type="radio" name="status" value="0" {{ $poll->status == '0' ? 'checked' : '' }}> Inactive
										 @else
										 	<input type="radio" name="status" value="0" {{ old('status') =='0' ? 'checked' : '' }}> Inactive
										 @endif
										 <span></span>
									</label>
								 </div>
							</div>

						</div>
					</div>
				
			</div>
		</div>

		@isset($poll)
			@include('back.'.config('bookdose.theme_back').'.modules.poll.box_poll_options')
		@endisset
		@include('back.'.config('bookdose.theme_back').'.modules.poll.modal_poll_category')
	</form>
</div>
@endsection



@push('additional_js')
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-notify/0.2.0/js/bootstrap-notify.min.js"></script>
<script type="text/javascript">
function validate() 
{
	$('#frm_main').validate({
		errorPlacement: function(error, element) {
       	if (element.hasClass('kt_datepicker')) {
       		error.insertAfter(element.closest('.input-group.date'));
       	}
       	else {
          	error.insertAfter(element);
       	}
      }
	});
	if ($('#frm_main').valid()) {
		save();
	}
	else {
		$('.error:first').focus();
		return false;
	}
}

function addNewCategory()
{
	$('#modal_category').modal('show');
	$('#modal_category').find(':text:first').focus();
}

$(document).ready(function() {
	$('[data-toggle=popover]').popover({
		'html': true,
		'placement': 'top',
		'trigger': 'focus'
	});

	$('#dp_poll_start, #dp_poll_end').datepicker({
	   rtl: KTUtil.isRTL(),
	   // todayBtn: "linked",
	   clearBtn: true,
	   todayHighlight: true,
	   // startDate: new Date(),
	   orientation: "bottom left",
	   autoclose: true,
	});

	$(':text:first').focus();
});

function addNewCategory()
{
	$('#modal_category').modal('show');
	$('#modal_category').find(':text:first').focus();
}
</script>
@endpush
