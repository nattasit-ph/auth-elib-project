@extends('back.'.config('bookdose.theme_back').'.tpl.tpl_admin')

@section('title', 'Event')
@section('page_title', 'Event')
@section('topbar_button')
<a href="{{ route('admin.event.create') }}" class="btn btn-brand btn-bold">
	<i class="fa fa-plus"></i> Add New 
</a>

<div>
	<form id="frm_export" method="get" action="{{ route('admin.event.exportToExcel') }}">
		<input type="hidden" id="hd_status" name="hd_status" value="">
		<input type="hidden" id="hd_event_start" name="hd_event_start" value="">
		<input type="hidden" id="hd_event_end" name="hd_event_end" value="">
		<input type="hidden" id="hd_keyword" name="hd_keyword" value="">
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
				<div class="form-inline-block col">
					<label class="text-dark font-pri mr-2">สถานะ:</label>
					<select id="ddl_filter_status" class="form-control">
						<option value="">ทั้งหมด</option>
						<option value="1">Active</option>
						<option value="0">Inactive</option>
					</select>
				</div>

				<div class="form-inline-block col">
					<label class="text-dark font-pri mr-2">วันที่เริ่มต้น:</label>
					<div class="input-group date">
						<input id="dp_event_start" name="event_start" type="text" class="form-control kt_datepicker required" data-date-format="dd/mm/yyyy" autocomplete="off" value="">
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
						<input id="dp_event_end" name="event_end" type="text" class="form-control kt_datepicker required" data-date-format="dd/mm/yyyy" autocomplete="off" value="">
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
			             <th>ชื่อกิจกรรม</th>
			             <th>วันที่เริ่มต้น</th>
			             <th>วันที่สิ้นสุด</th>
			             <th>จำนวนผู้เข้าร่วม</th>
			             <th>สถานะ</th>
			             <th></th>
			         </tr>
			     </thead>
			 </table>
		</div>
	</div>
	<!--begin::Portlet-->
	<input type="hidden" class="form-control" id="page_url_set_status" value="{{ route('admin.event.setStatus') }}">
	<input type="hidden" class="form-control" id="page_url_delete" value="{{ route('admin.event.delete') }}">
	
		

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
            "url": "{{ route('admin.event.datatable') }}",
            "type": "POST",
            "headers": {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            "data": {
            	filter_status: $('#ddl_filter_status').val(),
            	filter_country: $('#ddl_filter_country').val(),
            	filter_event_type: $('#ddl_filter_event_type').val(),
            	filter_event_start: $('#dp_event_start').val(),
            	filter_event_end: $('#dp_event_end').val(),
	     		},
	     		"dataType": 'json',
        },
	     columns: [
	         {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
	         {data: 'title_action', name: 'title', orderable: true, searchable: true},
	         {data: 'txt_event_start', name: 'event_start', orderable: true, searchable: false},
	         {data: 'txt_event_end', name: 'event_end', orderable: true, searchable: false},
	         {data: 'event_joins_count', name: 'event_joins_count', orderable: false, searchable: false, className: 'text-center' },
	         {data: 'status_html', name: 'status', orderable: true, searchable: false},
	         {data: 'actions', name: 'actions', orderable: false, searchable: false},
	     ],
	     order: [[ 2, "asc" ]],
	     initComplete: function(settings, json) {
		    $('#main-table thead').addClass('bg-light');
		    $('div.dataTables_length select').addClass('custom-select custom-select-sm form-control form-control-sm');
		    $('div.dataTables_filter input').addClass('form-control form-control-sm');
		  }
	 });
}

$(document).ready(function() {
	initDataTable();

	$('#ddl_filter_status, #dp_event_start, #dp_event_end').on('change', function(e) {
		initDataTable();
		e.preventDefault();
		e.stopPropagation();
	});

	$('#dp_event_start, #dp_event_end').datepicker({
	   rtl: KTUtil.isRTL(),
	   todayBtn: "linked",
	   clearBtn: true,
	   todayHighlight: true,
	   orientation: "bottom left",
	   autoclose: true,
	});

	$('#btn_export_to_excel').on('click', function(e) {
		e.preventDefault();
		$('#hd_status').val($('#ddl_filter_status').val());
		$('#hd_event_start').val($('#dp_event_start').val());
		$('#hd_event_end').val($('#dp_event_end').val());
		$('#hd_keyword').val($('input[type="search"]').val());
		$('#frm_export').submit();
	});
	
});
</script>
@endpush
