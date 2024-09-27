@extends('back.'.config('bookdose.theme_back').'.tpl.tpl_admin')

@section('title', __('menu.back.reward_popular'))
@section('page_title', __('menu.back.reward_popular'))
@section('topbar_button')
<div>
    <form id="frm_export" method="get" action="{{ route('admin.reward.report.rewardPopular.exportToExcel', $org_slug) }}">
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

	@if(session()->get('error'))
	    <div class="alert alert-solid-danger alert-bold alert-dismissible fade show" role="alert" dismissable="true">
	      <div class="alert-text">{{ session()->get('error') }}</div>
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

				<div class="form-inline-block col-md-3 my-2 my-md-0">
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

				<div id="panel_custom_period" class="form-row col-md-5 d-none">
					<div class="form-inline-block col-6  my-2 my-md-0">
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
					<div class="form-inline-block col-6  my-2 my-md-0">
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

			</div>
		</div>
	</div>

	<div class="kt-portlet kt-portlet--mobile">
		<div class="kt-portlet__body">
			<table class="table table-hover dt-bootstrap4 no-footer" id="main-table">
			     <thead>
			         <tr>
			             <th>#</th>
			             <th class="font-th-pri">ชื่อของรางวัล</th>
			             <th class="font-pri">จำนวนของรางวัลที่แลก</th>
			             <th></th>
			         </tr>
			     </thead>
			 </table>
		</div>
	</div>
	<!--begin::Portlet-->
	<input type="hidden" class="form-control" id="page_url_set_status" value="{{ route('admin.reward-category.setStatus', $org_slug) }}">
	<input type="hidden" class="form-control" id="page_url_delete" value="{{ route('admin.reward-category.delete', $org_slug) }}">
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
        ordering: false,
        ajax: {
            type: "post",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ route("admin.reward.report.rewardPopular.datatable", $org_slug) }}",
            data: {
                period: $('#ddl_filter_period').val(),
                period_start: $('#dp_period_start').val(),
                period_end: $('#dp_period_end').val(),
                product_main_slug: $('#ddl_filter_product_main').val(),
            },
            dataType: 'json',
        },
        columns: [{
                data: 'id_row',
                searchable: false,
            },
            {
                data: 'reward_name',

            },

            {
                data: 'redempt_qty',
                class: 'text-center',
            },

        ],
        order: [],

	     pageLength: 25,
	     initComplete: function(settings, json) {
		    $('#main-table thead').addClass('bg-light');
		    $('div.dataTables_length select').addClass('custom-select custom-select-sm form-control form-control-sm');
		    $('div.dataTables_filter input').addClass('form-control form-control-sm');
		  }
	 });
}


    $(document).ready(function() {
		$('#ddl_filter_period').val('last7Days');
		initDataTable();

		$('#dp_period_start, #dp_period_end, #ddl_filter_product_main').on('change', function(e) {
			initDataTable();
			e.preventDefault();
			e.stopPropagation();
		});

		$('#ddl_filter_period').on('change', function(e) {
			if ($(this).val() == 'customPeriod') {
				$('#panel_custom_period').removeClass('d-none');
				$('#dp_period_start').focus();
			} else {
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
			$('#hd_custom_period_start').val($('#dp_period_start').val());
			$('#hd_custom_period_end').val($('#dp_period_end').val());
			$('#hd_keyword').val($('input[type="search"]').val());
			$('#frm_export').submit();
		});

	});

</script>
@endpush
