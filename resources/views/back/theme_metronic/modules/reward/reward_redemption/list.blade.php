@extends('back.'.config('bookdose.theme_back').'.tpl.tpl_admin')

@section('title', __('menu.back.reward_redemption'))
@section('page_title', __('menu.back.reward_redemption'))
@section('topbar_button')
<div>
    <form id="frm_export" method="get" action="{{ route('admin.redemption.exportToExcel', $org_slug) }}">
		<input type="hidden" id="hd_is_delivered" name="hd_is_delivered" value="">
		<input type="hidden" id="hd_is_refunded" name="hd_is_refunded" value="">
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
				<div class="form-inline-block col-3">
					<label class="text-dark font-th-pri mr-2">สถานะการรับของรางวัล:</label>
					<select id="ddl_filter_delivery_status" class="form-control">
						<option value="">All</option>
						<option value="1">รับของแล้ว</option>
						<option value="0">ยังไม่รับของ</option>
					</select>
				</div>
				<div class="form-inline-block col-3">
					<label class="text-dark font-th-pri mr-2">สถานะการคืนแต้ม:</label>
					<select id="ddl_filter_refund_status" class="form-control">
						<option value="">All</option>
						<option value="1">มีการคืนแต้ม</option>
						<option value="0">ไม่มีการคืนแต้ม</option>
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
			             <th class="font-th-pri">วันที่แลก</th>
			             <th class="font-th-pri">ผู้แลกของรางวัล</th>
			             <th class="font-th-pri">รายชื่อของรางวัล</th>
			             <th class="font-th-pri">จำนวน</th>
			             <th class="font-th-pri">แต้มที่ใช้แลก</th>
			             <th class="font-th-pri">สถานะการรับของรางวัล</th>
			             <th class="font-th-pri">วัน/เวลาคืนแต้ม</th>
			             <th></th>
			         </tr>
			     </thead>
			 </table>
		</div>
	</div>
	<!--begin::Portlet-->
	<input type="hidden" class="form-control" id="page_url_set_status" value="{{ route('admin.redemption.ajaxSetDeliveryStatus', $org_slug) }}">
	<input type="hidden" class="form-control" id="page_url_refund" value="{{ route('admin.redemption.ajaxRefund', $org_slug) }}">
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
	     ajax: {
	     		type: "post",
	     		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
	     		url: '{{ route("admin.redemption.datatable", $org_slug) }}',
	     		data: {
	     			delivery_status: $('#ddl_filter_delivery_status').val(),
	     			refund_status: $('#ddl_filter_refund_status').val(),
	     		},
	     		dataType: 'json',
	     },
	     columns: [
	         {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
	         {data: 'redeemed_date', name: 'redeemed_at', orderable: true, searchable: true},
	         {data: 'user_fullname', name: 'user.name', orderable: true, searchable: true},
	         {data: 'reward_item_title', name: 'rewardItem.title', orderable: true, searchable: true},
	         {data: 'unit', name: 'unit', orderable: true, searchable: false, className:'text-center'},
	         {data: 'total_point', name: 'total_point', orderable: true, searchable: false, className:'text-center'},
	         {data: 'delivery_status_html', name: 'is_delivered', orderable: false, searchable: false},
	         {data: 'refund_status_html', name: 'is_refunded', orderable: false, searchable: false, className:'text-center'},
	         {data: 'actions', name: 'actions', orderable: false, searchable: false},
	     ],
	     order: [ [ 1, "desc" ] ],
	     pageLength: 25,
	     initComplete: function(settings, json) {
		    $('#main-table thead').addClass('bg-light');
		    $('div.dataTables_length select').addClass('custom-select custom-select-sm form-control form-control-sm');
		    $('div.dataTables_filter input').addClass('form-control form-control-sm');
		  }
	 });
}

function toggleDeliveryStatus(e)
{
	var id = $(e).data('id');
	var status = $(e).data('status');
	var url = $('#page_url_set_status').val();

	const swalWithBootstrapButtons = Swal.mixin({
  	customClass: {
	    confirmButton: 'btn btn-brand',
	    cancelButton: 'btn btn-default'
	  },
	  buttonsStyling: false
	})

	swalWithBootstrapButtons.fire({
	  title: 'Are you sure you want to '+ (status == '1' ? 'undeliver ' : 'deliver ') + $(e).data('reward-title') +' to '+ $(e).data('user-fullname') + '?',
	  type: 'warning',
	  showCancelButton: !0,
	  confirmButtonText: 'Yes, set as '+(status == '1' ? 'undelivered ' : 'delivered ')+'!',
	}).then((result) => {
		if (result.value) {
			$.ajaxSetup({
			    headers: {
			        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			    }
			});
			$.ajax({
				url: url,
				method: 'POST',
				data: { id: id, status: status },
				dataType: 'json',
			}).done(function(response) {
				if (response.status == '200') {
					initDataTable();
				}
				showNotifyOnScreen(response);
			});
		}
	})
}

function refund(e)
{
	var id = $(e).data('id');
	var status = $(e).data('status');
	var url = $('#page_url_refund').val();

	const swalWithBootstrapButtons = Swal.mixin({
  	customClass: {
	    confirmButton: 'btn btn-brand',
	    cancelButton: 'btn btn-default'
	  },
	  buttonsStyling: false
	})

	swalWithBootstrapButtons.fire({
	  title: 'Are you sure you want to refund '+ $(e).data('point') +' points back to '+ $(e).data('user-fullname') + '?',
	  type: 'warning',
	  showCancelButton: !0,
	  confirmButtonText: 'Confirm Refund',
	}).then((result) => {
		if (result.value) {
			$.ajaxSetup({
			    headers: {
			        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			    }
			});
			$.ajax({
				url: url,
				method: 'POST',
				data: { id: id },
				dataType: 'json',
			}).done(function(response) {
				if (response.status == '200') {
					initDataTable();
				}
				showNotifyOnScreen(response);
			});
		}
	})
}

$(document).ready(function() {
	initDataTable();

	$('#ddl_filter_delivery_status, #ddl_filter_refund_status').on('change', function(e) {
		initDataTable();
		e.preventDefault();
		e.stopPropagation();
	});

		$('#btn_export_to_excel').on('click', function(e) {
			e.preventDefault();
			$('#hd_is_delivered').val($('#ddl_filter_delivery_status').val());
			$('#hd_is_refunded').val($('#ddl_filter_refund_status').val());
			$('#hd_keyword').val($('input[type="search"]').val());
			$('#frm_export').submit();
		});

});
</script>
@endpush
