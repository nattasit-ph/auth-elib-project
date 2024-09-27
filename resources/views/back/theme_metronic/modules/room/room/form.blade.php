@extends('back.'.config('bookdose.theme_back').'.tpl.tpl_admin')

@section('title', 'Room')
@section('page_title', 'Room')
@section('topbar_button')
<a href="{{ route('admin.room.all')}}" class="btn btn-label-brand btn-bold">
	<i class="fa fa-arrow-left"></i> Back
</a>
@isset ($content)
<a href="{{ route('admin.room.create') }}" class="btn btn-outline-brand btn-bold">
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
	flex: 0 0 50%;
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
					{{ !empty($content) ? 'Edit '.$content->title : 'Create new room'}}
				</h3>
			</div>
			@if (in_array($step, ['general']))
			<div class="kt-portlet__head-toolbar">
				<div class="kt-form__actions">
					 <button id="btn_save" class="btn btn-brand btn-bold btn-wide kt-font-transform-u" onClick="validate()">
							<?=(request()->is('admin/room/create*') ? 'Save' : 'Update')?>
					 </button>

					 <a href="{{ route('admin.room.all') }}" class="ml-1 btn btn-secondary btn-bold btn-wide kt-font-transform-u">Cancel</a>
				</div>
			</div>
			@endif
		</div>
		
		<div class="kt-portlet__body kt-portlet__body--fit">
			<div class="kt-grid kt-wizard-v3 kt-wizard-v3--white" id="kt_wizard_v3" data-ktwizard-state="first">
				@include('back.'.config('bookdose.theme_back').'.modules.room.room.section_steps')

				<div class="kt-grid__item kt-grid__item--fluid kt-wizard-v3__wrapper">
					<div class="w-100">
						@include('back.'.config('bookdose.theme_back').'.modules.room.room.include_step_'.($step ?? 'general'))
					</div>
				</div>
			</div>
			
		</div>
		
	</div>
</div>
@endsection



@push('additional_js')
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-notify/0.2.0/js/bootstrap-notify.min.js"></script>
<!-- Scropt for include_step_reservation.blade -->
<script type="text/javascript" src="https://cdn.datatables.net/1.10.18/js/jquery.dataTables.min.js"></script>
<script type="text/javascript">
function initDataTable() {
	if ($.fn.DataTable.isDataTable("#main-table")) {
        $("#main-table").DataTable().clear();
        $("#main-table").dataTable().fnDestroy();
 	}
	$('#main-table').DataTable({
	     processing: true,
	     serverSide: true,
	     pageLength: 25,
	     ajax: {
            "url": "{{ route('admin.room.booking.datatable') }}",
            "type": "POST",
            "headers": {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            "data": {
            	filter_title: $('#ddl_filter_title').val(),
				filter_reserve_start: $('#dp_reserve_start').val(),
				filter_reserve_end: $('#dp_reserve_end').val(),
	     		},
	     		"dataType": 'json',
        },
	     columns: [
	         {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
			 {data: 'date_html', name: 'room_bookings.start_datetime', orderable: true, searchable: true},
	         {data: 'time_start_html', name: 'room_bookings.start_datetime', orderable: true, searchable: true},
	         {data: 'time_end_html', name: 'room_bookings.end_datetime', orderable: true, searchable: true},
	         {data: 'title_html', name: 'room_bookings.title', orderable: true, searchable: true},
			 {data: 'user_name_html', name: 'users.name', orderable: true, searchable: true},
			 {data: 'status_html', name: 'room_bookings.status', orderable: true, searchable: true},
	         {data: 'actions', name: 'actions', orderable: false, searchable: false},
	     ],
		 order: [[ 2, "asc" ], [ 3, "asc" ]],
	     initComplete: function(settings, json) {
		    $('#main-table thead').addClass('bg-light');
		    $('div.dataTables_length select').addClass('custom-select custom-select-sm form-control form-control-sm');
		    $('div.dataTables_filter input').addClass('form-control form-control-sm');
		  }
	 });
}

$(document).ready(function() {
	initDataTable();

	$('#ddl_filter_title, #dp_reserve_start, #dp_reserve_end').on('change', function(e) {
		initDataTable();
		e.preventDefault();
		e.stopPropagation();
	});

	$('#dp_reserve_start, #dp_reserve_end').datepicker({
	   rtl: KTUtil.isRTL(),
	   todayBtn: "linked",
	   clearBtn: true,
	   todayHighlight: true,
	   orientation: "bottom left",
	   autoclose: true,
	});


});
</script>
<!-- Scropt for include_step_reservation.blade -->
@endpush
