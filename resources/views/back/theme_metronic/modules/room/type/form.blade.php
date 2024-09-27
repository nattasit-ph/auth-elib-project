@extends('back.'.config('bookdose.theme_back').'.tpl.tpl_admin')

@section('title', 'Room Type')
@section('page_title', 'Room Type')
@section('topbar_button')
<a href="{{ route('admin.roomType.index') }}" class="btn btn-label-brand btn-bold">
	<i class="fa fa-arrow-left"></i> Back
</a>
@endsection

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
					{{ empty($room_type) ? 'Create RoomType' : 'Update RoomType: '.$room_type->title}}
				</h3>
			</div>
			<div class="kt-portlet__head-toolbar">
				<div class="kt-form__actions">
					<button id="btn_save" class="btn btn-brand btn-bold btn-wide kt-font-transform-u" onClick="validate()">
						{{ empty($room_type) ? 'Save' : 'Update'}}
					</button>
					<a href="{{ route('admin.roomType.index') }}" class="ml-1 btn btn-secondary btn-bold btn-wide kt-font-transform-u">Cancel</a>
				</div>
			</div>
		</div>
		
		<div class="kt-portlet__body kt-portlet__body--fit">
			<div class="kt-grid kt-wizard-v3 kt-wizard-v3--white" id="kt_wizard_v3" data-ktwizard-state="first">
				<div class="kt-grid__item kt-grid__item--fluid kt-wizard-v3__wrapper">
					<div class="w-100">

                        <form id="frm_main" class="kt-form" action="{{ isset($room_type) ? route('admin.roomType.update', $room_type->id) : route('admin.roomType.store') }}" method="POST">
                        @csrf

						@if(isset($room_type))
							@method('PUT')
						@else
							@method('POST')
						@endif
						 
							@include('back.'.config('bookdose.theme_back').'.modules.room.type.section_form')
                        </form>

					</div>
				</div>
			</div>
		</div>
		
	</div>
</div>    
@endsection
@push('additional_js')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script type="text/javascript">
$(document).ready(function() {

function reservation() {
	document.getElementById("frm_main").submit();
}

function updateReservation() {
	document.getElementById("frm_main").submit();
}
});

function validate() 
{
	$('#frm_main').validate({
		rules: {
			title: {
	            required: true,
	        },
	    },
		messages: {
			title: {
				required: "Title is required",
			},
		},
		errorPlacement: function(error, element) {
       	if (element.hasClass('select2-selection__rendered')) {
       		error.insertAfter(element.siblings('.select2'));
       		element.siblings('.select2').addClass('error');
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
		document.getElementById("frm_main").submit();
	}
	else {
		$('.error:first').focus();
		return false;
	}
}		
</script>
@endpush