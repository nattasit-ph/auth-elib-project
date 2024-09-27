@extends('back.'.config('bookdose.theme_back').'.tpl.tpl_admin')

@section('title', 'Polls')
@section('page_title', 'Polls')
@section('topbar_button')

<a href="{{ route('admin.poll.create') }}" class="btn btn-brand btn-bold">
	<i class="fa fa-plus"></i> Add New 
</a>
<div>
	<form id="frm_export" method="get" action="{{ route('admin.poll.exportToExcel') }}">
		<input type="hidden" id="hd_status" name="hd_status" value="">
		<input type="hidden" id="hd_poll_start" name="hd_poll_start" value="">
		<input type="hidden" id="hd_poll_end" name="hd_poll_end" value="">
		<input type="hidden" id="hd_keyword" name="hd_keyword" value="">
		<a id="btn_export_to_excel" href="javascript:void(0);" class="btn btn-success">
			<i class="fa fa-file-excel"></i> Export to excel
		</a>
	</form>
</div>
{{-- @can ('km.poll.manage')
@endcan --}}
@endsection

@push('additional_css')
<style type="text/css">

</style>
{{-- <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.0.3/css/buttons.dataTables.min.css"> --}}
@endpush

@section('content')
<div id="main_content" class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
	
	{{-- Display Success Message Area --}}
	@include('back.'.config('bookdose.theme_back').'.includes.alert_success')

  	{{-- Display Error Area --}}
  	@include('back.'.config('bookdose.theme_back').'.includes.alert_danger')

	<!-- Filter -->
	<div class="kt-portlet kt-portlet--mobile">
		<div class="kt-portlet__body">
			<div class="row d-flex justify-content-start">
				<div class="form-inline-block col-3">
					<label class="text-dark font-pri-th mr-2">สถานะ:</label>
					<select id="ddl_filter_status" class="form-control">
						<option value="">ทั้งหมด</option>
						<option value="1">Active</option>
						<option value="0">Inactive</option>
					</select>
				</div>

				<div class="form-inline-block col-3">
					<label class="text-dark font-pri-th mr-2">วันที่เริ่มต้น:</label>
					<div class="input-group date">
						<input id="dp_poll_start" name="poll_start" type="text" class="form-control kt_datepicker required" data-date-format="dd/mm/yyyy" autocomplete="off" value="">
						<div class="input-group-append">
							<span class="input-group-text">
								<i class="la la-calendar"></i>
							</span>
						</div>
					</div>
				</div>

				<div class="form-inline-block col-3">
					<label class="text-dark font-pri-th mr-2">วันที่สิ้นสุด:</label>
					<div class="input-group date">
						<input id="dp_poll_end" name="poll_end" type="text" class="form-control kt_datepicker required" data-date-format="dd/mm/yyyy" autocomplete="off" value="">
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
			             <th class="font-pri-th">คำถาม</th>
			             <th class="font-pri-th">วันที่เริ่มต้น</th>
			             <th class="font-pri-th">วันที่สิ้นสุด</th>
			             <th class="font-pri-th">จำนวนตัวเลือก</th>
			             <th class="font-pri-th">จำนวนโหวต</th>
			             <th class="font-pri-th">แก้ไขล่าสุด</th>
			             <th class="font-pri-th">สถานะ</th>
			             <th></th>
			         </tr>
			     </thead>
			 </table>
		</div>
	</div>
	<!--begin::Portlet-->
	<input type="hidden" class="form-control" id="page_url_set_status" value="{{ route('admin.poll.setStatus') }}">
	<input type="hidden" class="form-control" id="page_url_delete" value="{{ route('admin.poll.delete') }}">
	
	<!--end::Portlet-->
</div>
@endsection

@push('additional_js')
<script type="text/javascript" src="https://cdn.datatables.net/1.10.18/js/jquery.dataTables.min.js"></script>
<!-- <script type="text/javascript" src="https://cdn.datatables.net/1.10.18/js/dataTables.bootstrap4.min.js"></script> -->
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
            "url": "{{ route('admin.poll.datatable') }}",
            "type": "POST",
            "headers": {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            "data": {
            	filter_status: $('#ddl_filter_status').val(),
            	filter_poll_start: $('#dp_poll_start').val(),
            	filter_poll_end: $('#dp_poll_end').val(),
	     		},
	     		"dataType": 'json',
        },
	     columns: [
	         {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
	         {data: 'title_action', name: 'question', orderable: true, searchable: true },
	         {data: 'poll_start_date', name: 'poll_start', orderable: true, searchable: true },
	         {data: 'poll_end_date', name: 'poll_end', orderable: true, searchable: true },
	         {data: 'total_options', name: 'total_options', orderable: true, searchable: true, className: 'text-center' },
	         {data: 'total_votes', name: 'total_votes', orderable: true, searchable: true, className: 'text-center' },
	         {data: 'updated_date', name: 'updated_at', orderable: true, searchable: true },
	         {data: 'status_html', name: 'status', orderable: true, searchable: false},
	         {data: 'actions', name: 'actions', orderable: false, searchable: false},
	     ],
	     order: [[ 6, "desc" ]],
	     initComplete: function(settings, json) {
		    $('#main-table thead').addClass('bg-light');
		    $('div.dataTables_length select').addClass('custom-select custom-select-sm form-control form-control-sm');
		    $('div.dataTables_filter input').addClass('form-control form-control-sm');
		  }
	 });
}

$(document).ready(function() {
	initDataTable();

	$('#ddl_filter_status, #dp_poll_start, #dp_poll_end').on('change', function(e) {
		initDataTable();
		e.preventDefault();
		e.stopPropagation();
	});

	$('#dp_poll_start, #dp_poll_end').datepicker({
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
		$('#hd_poll_start').val($('#dp_poll_start').val());
		$('#hd_poll_end').val($('#dp_poll_end').val());
		$('#hd_keyword').val($('input[type="search"]').val());
		$('#frm_export').submit();
	});

});
</script>
@endpush
