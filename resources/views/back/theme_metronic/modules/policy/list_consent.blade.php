@extends('back.'.config('bookdose.theme_back').'.tpl.tpl_admin')

@section('title', 'Manage consent')
@section('page_title', 'การจัดการคำยินยอม')


@push('additional_css')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
<link href="css/pages/wizard/wizard-4.css" rel="stylesheet" type="text/css" />
<style type="text/css">
.kt-wizard-v3 .kt-wizard-v3__nav .kt-wizard-v3__nav-items .kt-wizard-v3__nav-item {
	flex: 0 0 33%;
}
.f-content{
	font-size: 16px;
	color: #48465b;
	font-weight: 500;
}
.hr-content{
	border-bottom: 1px solid #ebedf2 !important;
}
.kt-container{
	width: 100% !important;
	padding: 10px 10px !important;
}

.mb-2{
	margin-bottom:10px !important;
}
input:read-only {
  background-color: #f7f8fa !important;
}

.pb-add{
	padding-bottom: 20px;
}
.mt-icon-down-5{
	margin-top: -5px;
}
.pb-search{
	padding-bottom: 10px;
}
.w-100{
	width: 100%;
}
table, th, td {
  border: 1px solid #DDDDDD;
}

.paginate_button{
	width: unset !important;
}
.overflow-tabel{
    width: 100%;
    overflow-x: scroll;
    overflow-y: hidden;
    padding-bottom: 20px;
}

</style>
@endpush

<?php
$policy_result = DB::table('policy_and_terms')
         ->select('*')
         ->where('type', '=', 3)
         ->get();


?>

@section('content')
<div class="kt-container kt-container--fluid  kt-grid__item kt-grid__item--fluid">
	
	<!-- Borrow -->
	<div class="kt-portlet">
		<div class="kt-portlet__head">
			<div class="kt-portlet__head-label">
				<h3 id="section_title" class="kt-portlet__head-title font-pri">
					Manage consent
				</h3>
			</div>
			<div class="kt-portlet__head-toolbar">
				<div class="kt-form__actions">
					 <a href="{{ route('admin.site.consent.add') }}" class="btn btn-success text-white"><i class="fa fa-plus"></i> Add new</a>
				</div>
				&nbsp;
				<div class="kt-form__actions">
					 <a href="{{ route('admin.site.consent.log') }}" class="btn btn-info text-white"><i class="fa fa-database"></i> Log Consent User</a>
				</div>
			</div>

		</div>
		<div class="kt-portlet__body kt-portlet__body--fit">
			<div class="kt-portlet__body font-pri hr-content">
				<div class="row">
					<div class="col-sm-12 col-md-12 pb-search">
						<div class="input-group">
							<input type="text" id="search_table" class="form-control" placeholder="กรอกข้อูลที่ต้องการค้นหา">
							<div class="input-group-append">
								<span class="input-group-text">ค้นหา</span>
							</div>
						</div>
					</div>
					<div class="col-md-12 col-lg-12 overflow-tabel">
						<table id="consent_datatable" class="table nowrap table-bordered table-striped">
							<thead>
								<tr>
									<th>Version</th>
									<th>Detail TH</th>
									<th>Detail EN</th>
									<th>Re-Consent</th>
									<th>Date</th>
									<th>Edit</th>
								</tr>
							</thead>
							<tbody>

							</tbody>
						</table>
					</div>
				</div>	
			</div>
		</div>
	</div>

	

	

</div>
@endsection

@push('additional_js')
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
<script type="text/javascript">
	var consent_table;
	var consentConfig = {
		consent_url:"<?php echo route('admin.site.consent.getConsentControl');?>",

		searchTable:"#search_table",
		consent_datatable:"#consent_datatable",
	};

	function startDatatable(){
		consent_table = $(consentConfig.consent_datatable).DataTable({
			"sPaginationType": "full_numbers",
			"dom": ' tpi', //lrtip
			"order": [[ 0, "desc" ]],
			"pageLength": 25,
			"columns": [
					{"width": "5%" },
					{"width": "35%" },
					{"width": "35%" },
					{"width": "10%" },
					{"width": "10%" },
					{"width": "5%" },
			 ],
			"ajax": {
			"url": consentConfig.consent_url,
			"data": function ( d ) {
			}
		  }
		});
	}

	startDatatable();

	// // search ข้อมูล
	$(consentConfig.searchTable).on('keyup change', function () {
		consent_table.search(this.value).draw();
	});

</script>
@endpush