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
	.btn-sm-icon{
		padding: 6px 2px 7px 8px !important;
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
				<h3 id="section_title" class="kt-portlet__head-title">
					Log Consent User
				</h3>
			</div>
			<div class="kt-portlet__head-toolbar">
				<div class="kt-form__actions">
					 <a href="{{ route('admin.site.consent') }}" class="btn btn-success text-white"> Back</a>
				</div>
			</div>
		</div>
		<div class="kt-portlet__body kt-portlet__body--fit">
			<div class="kt-portlet__body">
				<div class="kt-section mb-0">
					<div class="row">
						<div class="col-md-4 text-center">
							<canvas id="myChart" class="pb-4" style="width:100%; height: 100%;"></canvas>
							<p><i class="fa fa-check text-success"></i> ให้การยินยอม {{$percen_agree}}%</p>
							<p><i class="fa fa-ban text-danger"></i> ไม่ให้/ถอนความยินยอม {{$percen_not_agree}}%</p>
						</div>
						<div class="col-md-8">
							<div class="row">
								<div class="col-md-6 pb-search">
									<div class="input-group">
										<input type="text" id="search_table" class="form-control" placeholder="กรอกข้อูลที่ต้องการค้นหา">
										<div class="input-group-append">
											<span class="input-group-text">ค้นหา</span>
										</div>
									</div>
								</div>
								<div class="col-md-3 pb-search">
									<div class="input-group">
										<input type="text" class="form-control datepicker start_date" placeholder="ว/ด/ป" onchange="filteringOptions();">
										<div class="input-group-append">
											<span class="input-group-text">ตั้งแต่</span>
										</div>
									</div>
								</div>
								<div class="col-md-3 pb-search">
									<div class="input-group">
										<input type="text" class="form-control datepicker end_date" placeholder="ว/ด/ป" onchange="filteringOptions();">
										<div class="input-group-append">
											<span class="input-group-text">จนถึง</span>
										</div>
									</div>
								</div>
								<div class="col-md-12 overflow-tabel">
									<table id="consent_user_datatable" class="table nowrap table-bordered table-striped">
										<thead>
											<tr>
												<th>#</th>
												<th>Name(user_id)</th>
												<th>Device</th>
												<th>Version Consent</th>
												<th>Status</th>
												<th>Date Time</th>
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
		</div>
	</div>

</div>
@endsection

@push('additional_js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
<script type="text/javascript">
	// ## datepicker ##
    $('.datepicker').datepicker({
		format: 'dd/mm/yyyy' 
	});

	var filtering = new Object();
	filtering.start_date = "";
	filtering.end_date = "";

	var consent_user_table;
	var consentConfig = {
		consent_user_url:"<?php echo route('admin.site.consent.getConsentUser');?>",
		consent_set_status_url:"<?php echo route('admin.site.consent.updateStatusConsentUser');?>",

		searchTable:"#search_table",
		consent_user_datatable:"#consent_user_datatable",
	};


	function startDatatable(){
		consent_user_table = $(consentConfig.consent_user_datatable).DataTable({
			"sPaginationType": "full_numbers",
			"dom": ' tpi', //lrtip
			// "order": [[ 0, "desc" ]],
			"pageLength": 25,
			"columns": [
					{"width": "5%" ,className: "text-center"},
					{"width": "20%" },
					{"width": "30%" },
					{"width": "15%" },
					{"width": "20%" ,className: "text-left"},
					{"width": "10%" },
			 ],
			"ajax": {
			"url": consentConfig.consent_user_url,
			"type": 'POST',
			"data": function ( d ) {
				d._token = "{{ csrf_token() }}";
				d.start_date = filtering.start_date;
				d.end_date = filtering.end_date;
			}
		  }
		});
	}

	startDatatable();

	// search ข้อมูล
	$(consentConfig.searchTable).on('keyup change', function () {
		consent_user_table.search(this.value).draw();
	});

	// search filtering วันที่
	function filteringOptions(){

		var start_date = $(".start_date").val();
		var end_date = $(".end_date").val();

		filtering.start_date = start_date;
		filtering.end_date   = end_date;

		consent_user_table.ajax.reload();
	}

	function setConsentStatus(id){
		var status = 0;
		var status_val = $(".status_"+id).attr("status_val");

		if(status_val == 0){
			status = 1;
		}else{
			status = 0;
		}

		Swal.fire({   
			title: "แน่ใจหรือ ?",   
			text: "ท่านแน่ใจหรือว่าต้องการแก้ไขความยินยอม!",   
			type: "warning",   
			showCancelButton: true, 
			cancelButtonText: "ยกเลิก",
			confirmButtonColor: "#DD6B55",   
			confirmButtonText: "ไช่, ต้องการ !",   
		}).then((result) => {
			if (result.value) {
				$.ajax({
					type: 'POST',
					data:{
						"id":id,
						"status":status,
						"_token":"{{ csrf_token() }}",
					},
					url: consentConfig.consent_set_status_url,
					success: function(json){
						location.reload();
					}
				});
			}
		});
	}

	// ############  Chart  ############
	$( document ).ready(function() {
		var count_agree = "<?php echo $count_agree;?>"; //ยินยอม
		var count_not_agree = "<?php echo $count_not_agree;?>"; //ไม่ยินยอม
		if(count_agree <= 0){ 
			count_agree = 0;
		}
		if(count_not_agree <= 0){ 
			count_not_agree = 0;
		}

		var xValues = ["ให้การยินยอม", " ไม่ให้/ถอนความยินยอม"];
		var yValues = [count_agree, count_not_agree];
		var barColors = ["#008000","#FF4500"];

		new Chart("myChart", {
			type: "pie",
			data: {
				labels: xValues,
				datasets: [{
					backgroundColor: barColors,
					data: yValues
				}]
			},
			options: {
				title: {
					display: true,
					text: "สถิติการขอความยินยอมการใช้ข้อมูล (ทั้งหมด)"
				}
			}
		});
	});

</script>
@endpush