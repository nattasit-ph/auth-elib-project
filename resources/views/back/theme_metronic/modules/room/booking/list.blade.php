@extends('back.'.config('bookdose.theme_back').'.tpl.tpl_admin')

@section('title', 'Room Reservation')
@section('page_title', 'Room Reservation')
@section('topbar_button')

<div class="dropdown">
	<a class="btn btn-brand btn-bold dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
		Reserve Room
	</a>
	<div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
		@forelse ($room_list as $item)
			<a class="dropdown-item" href="{{ route('admin.room.booking.create',['room_id' => $item->id]) }}">{{$item->title}}</a>
		@empty
			<a class="dropdown-item" href="javascript:void(0);">Room not found.</a>
		@endforelse
	</div>
</div>

<div>
	<form id="frm_export" method="get" action="{{ route('admin.room.booking.exportToExcel') }}">
		<input type="hidden" id="hd_status" name="hd_status" value="">
		<input type="hidden" id="hd_keyword" name="hd_keyword" value="">
		<input type="hidden" id="hd_room_id" name="hd_room_id" value="">
		<input type="hidden" id="hd_reserve_start" name="hd_reserve_start" value="">
		<input type="hidden" id="hd_reserve_end" name="hd_reserve_end" value="">
		<a id="btn_export_to_excel" href="javascript:void(0);" class="btn btn-success">
			<i class="fa fa-file-excel"></i> Export to excel
		</a>
	</form>
</div>
@endsection

@push('additional_css')
@endpush

@section('content')
<div id="main_content" class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
	
	{{-- Display Success Message Area --}}
	@if(session()->get('success'))
	    <div class="alert alert-solid-success alert-bold alert-dismissible fade show" role="alert" dismissable="true">
	      <div class="alert-text">{{ session()->get('success') }}</div>
	      <div class="alert-close">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close">
					<span aria-hidden="true"><i class="la la-close"></i></span>
				</button>
			</div>
	    </div>
  	@endif

  	{{-- Display Error Area --}}
	@if ($errors->any())
	    <div class="alert alert-solid-danger alert-bold" role="alert">
	        <ul>
	            @foreach ($errors->all() as $error)
	                <li>{{ $error }}</li>
	            @endforeach
	        </ul>
	    </div>
	@endif

	<!-- Filter -->
	<div class="kt-portlet kt-portlet--mobile">
		<div class="kt-portlet__body">
			<div class="row d-flex justify-content-start">
				<div class="form-inline-block col-4">
					<label class="text-dark font-pri mr-2">รายชื่อห้อง:</label>
					<select id="ddl_filter_title" class="form-control">
						<option value="">ทั้งหมด</option>
						@forelse ($room_list as $item)
						<option value="{{ $item->id }}">{{ $item->title }}</option>
						@empty
							
						@endforelse
					</select>
				</div>

				<div class="form-inline-block col">
					<label class="text-dark font-pri mr-2">วันที่เริ่มต้น:</label>
					<div class="input-group date">
						<input id="dp_reserve_start" name="reserve_start" type="text" class="form-control kt_datepicker required" data-date-format="dd/mm/yyyy" autocomplete="off" value="{{ date('d/m/Y', strtotime(now()))}}">
						<div class="input-group-append">
							<span class="input-group-text">
								<i class="la la-calendar"></i>
							</span>
						</div>
					</div>
				</div>

				<div class="form-inline-block col">
					<label class="text-dark font-pri mr-2">วันที่สิ้นสุด:</label>
					<div class="input-group date">
						<input id="dp_reserve_end" name="reserve_end" type="text" class="form-control kt_datepicker required" data-date-format="dd/mm/yyyy" autocomplete="off" value="">
						<div class="input-group-append">
							<span class="input-group-text">
								<i class="la la-calendar"></i>
							</span>
						</div>
					</div>
				</div>


			</div>
		</div>
	</div>
	
	<div class="kt-portlet kt-portlet--mobile">
		<div class="kt-portlet__body">
			<table class="table table-hover dt-bootstrap4 no-footer" id="main-table">
			     <thead>
			         <tr>
			             <th>ID</th>
						 <th>ชื่อห้อง</th>
			             <th class="w-10">วันที่จอง</th>
			             <th>เวลาเริ่ม</th>
			             <th>เวลาสิ้นสุด</th>
			             <th>หัวข้อ</th>
			             <th>ชื่อผู้จอง</th>
						 <th>สถานะ</th>
			             <th></th>
			         </tr>
			     </thead>
			 </table>
		</div>
	</div>
	<!--begin::Portlet-->
	<input type="hidden" class="form-control" id="page_url_set_status" value="{{ route('admin.room.booking.setStatus') }}">
	<input type="hidden" class="form-control" id="page_url_delete" value="{{ route('admin.room.booking.delete') }}">
	<!--end::Portlet-->
</div>
@endsection

@push('additional_js')
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
	         {data: 'title_action', name: 'rooms.title', orderable: true, searchable: true},
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
	
	$('#btn_export_to_excel').on('click', function(e) {
		e.preventDefault();
		$('#hd_lang').val("{{ $lang ?? config('bookdose.frontend_default_lang') }}");
		$('#hd_room_id').val($('#ddl_filter_title').val());
		$('#hd_reserve_start').val($('#dp_reserve_start').val());
		$('#hd_reserve_end').val($('#dp_reserve_end').val());
		$('#hd_keyword').val($('input[type="search"]').val());
		$('#frm_export').submit();
	});

});
</script>
@endpush
