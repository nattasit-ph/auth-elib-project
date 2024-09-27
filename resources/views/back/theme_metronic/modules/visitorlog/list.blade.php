@extends('back.'.config('bookdose.theme_back').'.tpl.tpl_admin')

@section('title', 'Visitor Log')
@section('page_title', 'Visitor Log')
@section('topbar_button')
<div>
	<form id="frm_export" method="get" action="{{ route('admin.visitor-log.exportToExcel') }}">
		<input type="hidden" id="hd_period" name="hd_period" value="">
		<input type="hidden" id="hd_device" name="hd_device" value="">
		<input type="hidden" id="hd_system" name="hd_system" value="">
		<input type="hidden" id="hd_custom_period_start" name="hd_custom_period_start" value="">
		<input type="hidden" id="hd_custom_period_end" name="hd_custom_period_end" value="">
		<input type="hidden" id="hd_keyword" name="hd_keyword" value="">
		<a id="btn_export_to_excel" href="javascript:void(0);" class="btn btn-success">
			<i class="fa fa-file-excel"></i> Export to excel
		</a>
	</form>
</div>
@endsection

@push('additional_css')
<style type="text/css">
</style>
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

	<div class="kt-portlet kt-portlet--mobile">
		<div class="kt-portlet__body">
			<div class="row d-flex justify-content-start">
				<div class="form-inline-block col-3">
					<label class="text-dark font-pri">ช่วงเวลาที่ต้องการดู:</label>
					<select id="ddl_filter_period" class="form-control ml-2">
						<option value="">ทั้งหมด</option>
						<option value="today">Today</option>
						<option value="yesterday">Yesterday</option>
						<option value="last7Days">Last 7 days</option>
						<option value="thisMonth">This month</option>
						<option value="lastMonth">Last month</option>
						<option value="customPeriod">Choose period</option>
					</select>
				</div>

				<div id="panel_custom_period" class="form-row col-4 d-none">
					<div class="form-inline-block col-6">
						<label class="text-dark font-pri mr-2">เริ่มต้น:</label>
						<div class="input-group date">
							<input id="dp_period_start" name="period_start" type="text" class="form-control kt_datepicker required" data-date-format="dd/mm/yyyy" autocomplete="off" value="">
							<div class="input-group-append">
								<span class="input-group-text">
									<i class="la la-calendar"></i>
								</span>
							</div>
						</div>
					</div>
					<div class="form-inline-block col-6">
						<label class="text-dark font-pri mr-2">สิ้นสุด:</label>
						<div class="input-group date">
							<input id="dp_period_end" name="period_end" type="text" class="form-control kt_datepicker required" data-date-format="dd/mm/yyyy" autocomplete="off" value="">
							<div class="input-group-append">
								<span class="input-group-text">
									<i class="la la-calendar"></i>
								</span>
							</div>
						</div>
					</div>
				</div>
				<div class="form-inline-block col-2">
					<label class="text-dark font-pri">System:</label>
					<select id="ddl_filter_system" class="form-control ml-2">
						<option value="">ทั้งหมด</option>
						<option value="belib">E-library</option>
						<option value="learnext">E-learning</option>
						<option value="knowledge">Public space</option>
					</select>
				</div>
				<div class="form-inline-block col-2">
					<label class="text-dark font-pri">Device:</label>
					<select id="ddl_filter_device" class="form-control ml-2">
						<option value="">ทั้งหมด</option>
						@foreach ($device as $item)
						<option value="{{ $item->device }}">{{{ $item->device }}}</option>
						@endforeach
					</select>
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
			             <th>URL</th>
			             <th>IP Address</th>
			             <th>Created Date</th>
			             <th>Browser</th>
						 <th>Device</th>
			         </tr>
			     </thead>
			 </table>
		</div>
	</div>
	<!--begin::Portlet-->
	
		

	<!--end::Portlet-->
</div>
@endsection

@push('additional_js')
<script type="text/javascript" src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
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
	     ajax: {
	     		type: "post",
	     		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
	     		url: "{{ route('admin.visitor-log.datatable') }}",
	     		data: {
	     			period: $('#ddl_filter_period').val(),
					device: $('#ddl_filter_device').val(),
	     			period_start: $('#dp_period_start').val(),
	     			period_end: $('#dp_period_end').val(),
					system:	$('#ddl_filter_system').val(),
	     		},
	     		dataType: 'json',
	     },
	     columns: [
	         {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
	         {data: 'browser_detail', name: 'browser_detail', orderable: true, searchable: true,  },
	         {data: 'ip', name: 'ip', orderable: true, searchable: true },
			 {data: 'created_date', name: 'created_at', orderable: true, searchable: false },
			 {data: 'browser', name: 'browser', orderable: false, searchable: false },
			 {data: 'device', name: 'device', orderable: true, searchable: true },
	     ],
	     order: [[ 3, "desc" ]],
	     pageLength: 25,
	     initComplete: function(settings, json) {
		    $('#main-table thead').addClass('bg-light');
		    $('div.dataTables_length select').addClass('custom-select custom-select-sm form-control form-control-sm');
		    $('div.dataTables_filter input').addClass('form-control form-control-sm');
		  }
	 });
}
$('#ddl_filter_device').on('change', function(e) {
	initDataTable();
});

$(document).ready(function() {
	$('#ddl_filter_period').val('last7Days');
	initDataTable();

	$('#dp_period_start, #dp_period_end, #ddl_filter_system, #ddl_filter_device').on('change', function(e) {
		initDataTable();
		e.preventDefault();
		e.stopPropagation();
	});

	$('#ddl_filter_period').on('change', function(e) {
		if ($(this).val() == 'customPeriod') {
			$('#panel_custom_period').removeClass('d-none');
			$('#dp_period_start').focus();
		}
		else {
			$('#panel_custom_period').addClass('d-none');
			initDataTable();
			e.preventDefault();
			e.stopPropagation();
		}
	});

	$('#dp_period_start, #dp_period_end').datepicker({
	   rtl: KTUtil.isRTL(),
	   todayBtn: "linked",
	   clearBtn: true,
	   todayHighlight: true,
	   orientation: "bottom left",
	   autoclose: true,
	});
	
	$('#btn_export_to_excel').on('click', function(e) {
		e.preventDefault();
		$('#hd_period').val($('#ddl_filter_period').val());
		$('#hd_device').val($('#ddl_filter_device').val());
		$('#hd_system').val($('#ddl_filter_system').val());
		$('#hd_custom_period_start').val($('#dp_period_start').val());
		$('#hd_custom_period_end').val($('#dp_period_end').val());
		$('#hd_keyword').val($('input[type="search"]').val());
		$('#frm_export').submit();
	});
	
});
</script>
@endpush