@extends('back.'.config('bookdose.theme_back').'.tpl.tpl_admin')

@section('title', 'News Category')
@section('page_title', 'News Category')
@section('topbar_button')
<a href="{{ route('admin.news.category.create') }}" class="btn btn-brand btn-bold">
	<i class="fa fa-plus"></i> Add new
</a>
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
			<table class="table table-hover dt-bootstrap4 no-footer" id="main-table">
			     <thead>
			         <tr>
			             <th>ID</th>
			             <th></th>
			             <th>ชื่อหมวดหมู่</th>
			             <th>URL</th>
			             <th>สถานะ</th>
			             <th></th>
			         </tr>
			     </thead>
			 </table>
		</div>
	</div>
	<!--begin::Portlet-->
	<input type="hidden" class="form-control" id="page_url_set_status" value="{{ route('admin.news.category.setStatus') }}">
	<input type="hidden" class="form-control" id="page_url_delete" value="{{ route('admin.news.category.delete') }}">
	<!--end::Portlet-->
</div>
@endsection

@push('additional_js')
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
            "url": "{{ route('admin.news.category.datatable') }}",
            "type": "POST",
            "headers": {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        },
	     columns: [
	         {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
	         {data: 'id', name: 'id', visible:false, orderable: false, searchable: false  },
	         {data: 'title_action', name: 'title', orderable: true, searchable: true },
	         {data: 'slug', name: 'slug', orderable: true, searchable: true },
	         {data: 'status_html', name: 'status', orderable: true, searchable: false },
	         {data: 'actions', name: 'actions', orderable: false, searchable: false },
	     ],
     		order: [[ 2, "desc" ]],
	     	initComplete: function(settings, json) {
		    $('#main-table thead').addClass('bg-light');
		    $('div.dataTables_length select').addClass('custom-select custom-select-sm form-control form-control-sm');
		    $('div.dataTables_filter input').addClass('form-control form-control-sm');
		  }
	 });
}

$(document).ready(function() {
	initDataTable();
});
</script>
@endpush
