@extends('back.'.config('bookdose.theme_back').'.tpl.tpl_admin')

@section('title', 'Questionnaire')
@section('page_title', 'Questionnaire')
@section('topbar_button')
<a href="{{ route('admin.questionnaire.create', $org_slug) }}" class="btn btn-brand btn-bold">
	<i class="fa fa-plus"></i> Add New
</a>
<div>
	<form id="frm_export" method="get" action="{{ route('admin.questionnaire.exportToExcel', $org_slug) }}">
		<input type="hidden" id="hd_lang" name="hd_lang" value="">
		<input type="hidden" id="hd_status" name="hd_status" value="">
		<input type="hidden" id="hd_category" name="hd_category" value="">
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
					<label class="text-dark font-pri mr-2">สถานะ:</label>
					<select id="ddl_filter_status" class="form-control">
						<option value="">ทั้งหมด</option>
						<option value="1">Active</option>
						<option value="0">Inactive</option>
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
                        <th class="text-center">ID</th>
                        <th class="w-25">Title</th>
                        <th class="text-center">Total Requests</th>
                        @if (config('bookdose.app.belib_url'))
                            <th class="text-center">Belib System</th>
                        @endif
                        @if (config('bookdose.app.km_url'))
                            <th class="text-center">KM System</th>
                        @endif
                        @if (config('bookdose.app.learnext_url'))
                            <th class="text-center">Learnext System</th>
                        @endif
                        <th class="text-center">Status</th>
                        <th class="text-center">Updated</th>
                        <th></th>
                    </tr>
                </thead>
			 </table>
		</div>
	</div>
	<!--begin::Portlet-->
	<input type="hidden" class="form-control" id="page_url_set_status" value="{{ route('admin.questionnaire.setStatus', $org_slug) }}">
	<input type="hidden" class="form-control" id="page_url_delete" value="{{ route('admin.questionnaire.delete', $org_slug) }}">
	<input type="hidden" class="form-control" id="page_url_replicate" value="{{ route('admin.questionnaire.replicate', $org_slug) }}">
    <input type="hidden" class="form-control" id="page_url_set_system" value="{{ route('admin.questionnaire.setSystem', $org_slug) }}">




	<!--end::Portlet-->
</div>
@endsection

@push('additional_js')
<script type="text/javascript" src="https://cdn.datatables.net/1.10.18/js/jquery.dataTables.min.js"></script>
<!-- <script type="text/javascript" src="https://cdn.datatables.net/1.10.18/js/dataTables.bootstrap4.min.js"></script> -->
<script type="text/javascript">
function replicatePost(e)
{
	var source_id = $(e).data('id');
	var destination_lang = $(e).data('to-lang');
	var destination_lang_name = $(e).data('to-lang-name');
	var url = $('#page_url_replicate').val();

	var confirm_line_1 = 'Are you sure you want to replicate this post to '+destination_lang_name+'?';
	var confirm_line_2 = '';
	const swalWithBootstrapButtons = Swal.mixin({
  	customClass: {
	    confirmButton: 'btn btn-brand',
	    cancelButton: 'btn btn-default'
	  },
	  buttonsStyling: false
	})

	swalWithBootstrapButtons.fire({
	  title: confirm_line_1,
	  text: confirm_line_2,
	  type: 'warning',
	  showCancelButton: !0,
	  confirmButtonText: 'Yes, replicate it!',
	  // cancelButtonText: 'Cancel!',
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
				data: { id: source_id, lang: destination_lang },
				dataType: 'json',
			}).done(function(response) {
				if (response.status == '200') {
					// go to edit form of the destination post
					window.location = response.url;
				}
				// showNotifyOnScreen(response);
			});
		}
	})
}

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
            "url": "{{ route('admin.questionnaire.datatable', $org_slug) }}",
            "type": "POST",
            "headers": {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            "data": {
            	filter_status: $('#ddl_filter_status').val(),
            	filter_category: $('#ddl_filter_category').val(),
	     			filter_lang: "{{ $lang ?? config('bookdose.frontend_default_lang') }}"
	     		},
	     		"dataType": 'json',
        },
	     columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false ,className: 'text-center'},
            {data: 'title_action', name: 'title', orderable: true, searchable: true},
            {data: 'submissions_count', name: 'submissions_count', orderable: true, searchable: true, className:'text-center'},

            @if (config('bookdose.app.belib_url'))
                {data: 'belib_html', name: 'belib_html', orderable: false, searchable: false,className: 'text-center'},
            @endif

            @if (config('bookdose.app.km_url'))
            {data: 'km_html', name: 'km_html', orderable: false, searchable: false,className: 'text-center'},
            @endif

            @if (config('bookdose.app.learnext_url'))
            {data: 'learnext_html', name: 'learnext_html', orderable: false, searchable: false,className: 'text-center'},
            @endif

            {data: 'status_html', name: 'status', orderable: true, searchable: false,className: 'text-center'},
            {data: 'updated_date', name: 'forms.updated_at', className: 'text-center'},
            {data: 'actions', name: 'actions', orderable: false, searchable: false },

	     ],
	     order: [[ 3, "desc" ]],
	     initComplete: function(settings, json) {
		    $('#main-table thead').addClass('bg-light');
		    $('div.dataTables_length select').addClass('custom-select custom-select-sm form-control form-control-sm');
		    $('div.dataTables_filter input').addClass('form-control form-control-sm');
		  }
	 });
}

$(document).ready(function() {
	initDataTable();

	$('#ddl_filter_status, #ddl_filter_category').on('change', function(e) {
		initDataTable();
		e.preventDefault();
		e.stopPropagation();
	});

	$('#btn_export_to_excel').on('click', function(e) {
		e.preventDefault();
		$('#hd_lang').val("{{ $lang ?? config('bookdose.frontend_default_lang') }}");
		$('#hd_status').val($('#ddl_filter_status').val());
		$('#hd_category').val($('#ddl_filter_category').val());
		$('#hd_keyword').val($('input[type="search"]').val());
		$('#frm_export').submit();
	});

});
function toggleSystem(e)
{
    var id = $(e).data('id');
    var system = $(e).data('system');
    var url = $('#page_url_set_system').val();
    const swalWithBootstrapButtons = Swal.mixin({
    customClass: {
        confirmButton: 'btn btn-brand',
        cancelButton: 'btn btn-default'
        },
        buttonsStyling: false
    })

    swalWithBootstrapButtons.fire({
        title: 'Are you sure you want to set '+ $(e).data('title') + ' to ' + system + ' system ?',
        // text: confirm_line_2,
        type: 'warning',
        showCancelButton: !0,
        confirmButtonText: 'Yes, '+(status == '1' ? 'inactivate ' : 'activate ')+' it!',
        // cancelButtonText: 'Cancel!',
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
                data: { id: id, system: system },
                dataType: 'json',
            }).done(function(response) {
                if (response.status == '200') {
                    initDataTable();
                }
                showNotifyOnScreen(response);
                    // swalWithBootstrapButtons.fire(
                    //      response.notify_title,
                    //      response.notify_msg,
                    //      response.notify_type,
                //   	)

            });
        }
    })
}
</script>
@endpush
