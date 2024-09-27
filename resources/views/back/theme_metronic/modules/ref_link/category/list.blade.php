@extends('back.'.config('bookdose.theme_back').'.tpl.tpl_admin')

@section('title', 'Category')
@section('page_title', 'Category')
@section('topbar_button')
<a href="{{ route('admin.reference-link.category.create') }}" class="btn btn-label-brand btn-bold">
	<i class="fas fa-plus"></i> Add New 
</a>
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
			<table class="table table-hover dt-bootstrap4 no-footer" id="main-table">
			     <thead>
			         <tr>
			             <th>ID</th>
						 <th>รูปภาพ</th>
			             <th class="font-th-pri">ชื่อหมวดหมู่</th>
			             <th class="font-pri">ลำดับที่แสดง</th>
			             <th class="font-th-pri">สถานะ</th>
			             <th></th>
			         </tr>
			     </thead>
			 </table>
		</div>
	</div>
	<!--begin::Portlet-->
	<input type="hidden" class="form-control" id="page_url_set_status" value="{{ route('admin.reference-link.category.setStatus') }}">
	<input type="hidden" class="form-control" id="page_url_delete" value="{{ route('admin.reference-link.category.delete') }}">
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
			"url": '{{ route("admin.reference-link.category.datatable") }}'
	     },
	     columns: [
	         {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
			 {data: 'image', name: 'image', orderable: false, searchable: false },
	         {data: 'title_action', name: 'title', orderable: true, searchable: true},
	         {data: 'weight', name: 'weight', orderable: true, searchable: false , class: "text-center"},
	         {data: 'status_html', name: 'status', orderable: true, searchable: false},
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

$(document).ready(function() {
	initDataTable();
});
</script>
@endpush
