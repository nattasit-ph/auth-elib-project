<form id="frm_main" class="px-4" method="POST">
	@csrf
	@method('POST')
	<input type="hidden" id="step" name="step" value="general">
	<input type="hidden" id="save_option" name="save_option" value="">
	<input type="hidden" id="id" name="id" value="{{ $content->id ?? '' }}">
	<input type="hidden" name="lang" value="th">

	<div class="kt-portlet__body">
		<div class="kt-section mb-0">
			<input type="hidden" id="form_id" value="{{ $content->id ?? '' }}">

			<div class="kt-portlet kt-portlet--mobile">
				<div class="kt-portlet__body">
					<div class="row d-flex justify-content-start">
						<div class="form-inline-block col-3 d-none">
							<label class="text-dark font-pri mr-2">สถานะ:</label>
							<select id="ddl_filter_status" class="form-control">
								<option value="">ทั้งหมด</option>
								<option value="0">รอการอนุมัติ</option>
								<option value="1">กำลังดำเนินการ</option>
								<option value="2">ดำเนินการเรียบร้อย</option>
							</select>
						</div>

						<div class="form-inline-block col">
							<label class="text-dark font-pri mr-2">วันที่ส่งแบบฟอร์มเริ่มต้น:</label>
							<div class="input-group date">
								<input id="dp_submitted_start" name="submitted_start" type="text" class="form-control kt_datepicker required" data-date-format="dd/mm/yyyy" autocomplete="off" value="">
								<div class="input-group-append">
									<span class="input-group-text">
										<i class="la la-calendar"></i>
									</span>
								</div>
							</div>
						</div>

						<div class="form-inline-block col">
							<label class="text-dark font-pri mr-2">วันที่ส่งแบบฟอร์มสิ้นสุด:</label>
							<div class="input-group date">
								<input id="dp_submitted_end" name="submitted_end" type="text" class="form-control kt_datepicker required" data-date-format="dd/mm/yyyy" autocomplete="off" value="">
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
					             <th class="text-center">ID</th>
					             <th class="font-pri">วันที่ส่งแบบฟอร์ม</th>
					             <th class="font-pri w-40">ชื่อ-สกุลผู้ส่ง</th>
					             {{-- <th class="text-center font-pri">สถานะแบบฟอร์ม</th> --}}
					             <th></th>
					         </tr>
					     </thead>
					 </table>
				</div>
			</div>
			<!--begin::Portlet-->
			<input type="hidden" class="form-control" id="page_url_set_status" value="{{ route('admin.questionnaire.submission.setStatus', $org_slug) }}">
			<input type="hidden" class="form-control" id="page_url_delete" value="{{ route('admin.questionnaire.submission.delete', $org_slug) }}">
			<!--end::Portlet-->


		</div>
	</div>
</form>



@push('additional_js')
<script type="text/javascript" src="https://cdn.datatables.net/1.10.18/js/jquery.dataTables.min.js"></script>
<script type="text/javascript">
function initDataTable()
{
	if ($.fn.DataTable.isDataTable("#main-table")) {
        $("#main-table").DataTable().clear();
        $("#main-table").dataTable().fnDestroy();
 	}
	$('#main-table').DataTable({
	     processing: true,
	     serverSide: true,
	     pageLength: 25,
	     ajax: {
            "url": "{{ route('admin.questionnaire.submission.datatable', $org_slug) }}",
            "type": "POST",
            "headers": {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            "data": {
            	form_id: $('#form_id').val(),
            	filter_status: $('#ddl_filter_status').val(),
            	filter_submitted_start: $('#dp_submitted_start').val(),
            	filter_submitted_end: $('#dp_submitted_end').val(),
	     		},
	     		"dataType": 'json',
        },
	     columns: [
	         {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false ,className: 'text-center'},
	         {data: 'created_date', name: 'form_submissions.created_at'},
	         {data: 'title_action', name: 'creator.name', orderable: false, searchable: true},
	         // {data: 'status_html', name: 'form_submissions.status', orderable: true, searchable: false,className: 'text-center'},
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

$(document).ready(function() {

	$('#ddl_filter_status, #dp_submitted_start, #dp_submitted_end').on('change', function(e) {
		initDataTable();
		e.preventDefault();
		e.stopPropagation();
	});

	$('#dp_submitted_start, #dp_submitted_end').datepicker({
	   rtl: KTUtil.isRTL(),
	   todayBtn: "linked",
	   clearBtn: true,
	   todayHighlight: true,
	   orientation: "bottom left",
	   autoclose: true,
	});

	$('#btn_export_to_excel').on('click', function(e) {
		e.preventDefault();
		$('#hd_lang').val("th");
		$('#hd_status').val($('#ddl_filter_status').val());
		$('#hd_submitted_start').val($('#dp_submitted_start').val());
		$('#hd_submitted_end').val($('#dp_submitted_end').val());
		$('#hd_keyword').val($('input[type="search"]').val());
		$('#frm_export').submit();
	});

	// $('#ddl_filter_status').val(0);
	initDataTable();

});
</script>
@endpush
