@extends('back.'.config('bookdose.theme_back').'.tpl.tpl_admin')

@section('title', 'Pages')
@section('page_title', 'Pages')
@section('topbar_button')
<a href="{{ route('admin.pages.create') }}" class="btn btn-brand btn-bold">
	<i class="fa fa-plus"></i> Add new
</a>

<div>
	<form id="frm_export" method="get" action="{{ route('admin.pages.exportToExcel', 'article') }}">
		<input type="hidden" id="hd_system" name="hd_system" value="">
		<input type="hidden" id="hd_period" name="hd_period" value="">
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
	@include('back.'.config('bookdose.theme_back').'.includes.alert_success')

  	{{-- Display Error Area --}}
	@include('back.'.config('bookdose.theme_back').'.includes.alert_danger')


	<div class="kt-portlet kt-portlet--mobile">
		<div class="kt-portlet__body">
			<div class="row d-flex justify-content-start">

				<div class="form-inline-block col-3">
					<label class="text-dark font-pri">ระบบ:</label>
					<select id="ddl_filter_system" class="form-control ml-2">
						<option value="">ทั้งหมด</option>

						@if(!empty(config('bookdose.app.belib_url')))
						<option value="belib">Belib System</option>
						@endif
						@if(!empty(config('bookdose.app.km_url')))
						<option value="km">KM System</option>
						@endif
					</select>
				</div>

				<div class="form-inline-block col-3">
					<label class="text-dark font-pri">ช่วงเวลาที่เผยแพร่:</label>
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

				<div id="panel_custom_period" class="form-row col-5 d-none">
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


                <input type="text" id="hd_txt_copy" name="hd_txt_copy" value="" style="z-index: -999">
			</div>
		</div>
	</div>
	<div class="kt-portlet kt-portlet--mobile">
		<div class="kt-portlet__body">
			<table class="table table-hover dt-bootstrap4 no-footer" id="main-table">
			     <thead>
			         <tr>
			             <th>ID</th>
			             <th></th>

			             <th>ชื่อเว็บเพจ (TH)</th>
						 <th>ชื่อเว็บเพจ (EN)</th>
						 <th>Pretty URL</th>
			             <th>สถานะ</th>
			             <th></th>
			         </tr>
			     </thead>
			 </table>
		</div>
	</div>
	<!--begin::Portlet-->
	<input type="hidden" class="form-control" id="page_url_set_status" value="{{ route('admin.pages.setStatus') }}">
	<input type="hidden" class="form-control" id="page_url_delete" value="{{ route('admin.pages.delete') }}">
	<!--end::Portlet-->
</div>
@endsection

@push('additional_js')
<script type="text/javascript">
function copyUrl(t) {
	$('#hd_txt_copy').val( $(t).data('url') );

  /* Get the text field */
  var copyText = document.getElementById("hd_txt_copy");

  /* Select the text field */
  copyText.select();
  copyText.setSelectionRange(0, 99999); /*For mobile devices*/

  /* Copy the text inside the text field */
  document.execCommand("copy");

  /* Alert the copied text */
  // alert("URL Copied: " + copyText.value);
  	var resp = {};
   resp.notify_title = 'URL Copied'
   resp.notify_msg = 'You can Ctrl + V or paste anywhere.';
   resp.notify_icon = 'icon la la-copy';
   resp.notify_type = 'info';
 	showNotifyOnScreen(resp);
}
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
            url: "{{ route('admin.pages.datatable') }}",
            type: "POST",
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			data: {
				category: $('#ddl_filter_system').val(),
				period: $('#ddl_filter_period').val(),
				period_start: $('#dp_period_start').val(),
				period_end: $('#dp_period_end').val(),
			},
        },

	     columns: [
	         {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
	         {data: 'id', name: 'id', visible:false, orderable: false, searchable: false  },
	         {data: 'title_action_th', name: 'title_th', orderable: true, searchable: true },
			 {data: 'title_action_en', name: 'title_en', orderable: true, searchable: true },
			 {data: 'slug', name: 'slug', orderable: true, searchable: true },
	         {data: 'status_html', name: 'status', orderable: true, searchable: false },
	         {data: 'actions', name: 'actions', orderable: false, searchable: false },
	     ],
     		order: [[ 1, "desc" ]],
	     	initComplete: function(settings, json) {
		    $('#main-table thead').addClass('bg-light');
		    $('div.dataTables_length select').addClass('custom-select custom-select-sm form-control form-control-sm');
		    $('div.dataTables_filter input').addClass('form-control form-control-sm');
		  }
	 });
}

$('#ddl_filter_system').on('change', function(e) {
	initDataTable();
});

$(document).ready(function() {
	$('#ddl_filter_period').val('');
	initDataTable();

	$('#dp_period_start, #dp_period_end').on('change', function(e) {
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
		$('#hd_system').val($('#ddl_filter_system').val());
		$('#hd_period').val($('#ddl_filter_period').val());
		$('#hd_custom_period_start').val($('#dp_period_start').val());
		$('#hd_custom_period_end').val($('#dp_period_end').val());
		$('#hd_keyword').val($('input[type="search"]').val());
		$('#frm_export').submit();
	});

});
</script>
@endpush
