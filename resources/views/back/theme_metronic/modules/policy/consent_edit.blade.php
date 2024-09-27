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

</style>
@endpush



@section('content')
<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
	
	<!-- Borrow -->
	<div class="kt-portlet">
		<div class="kt-portlet__head">
			<div class="kt-portlet__head-label">
				<h3 id="section_title" class="kt-portlet__head-title font-pri">
					Edit Consent Management
				</h3>
			</div>
			<div class="kt-portlet__head-toolbar">
				<div class="kt-form__actions">
					 <a href="{{ route('admin.site.consent') }}" class="btn btn-success text-white"> Back</a>
				</div>
				&nbsp;&nbsp;
				<div class="kt-form__actions">
					<button type="button" class="btn btn-brand btn-bold btn-wide kt-font-transform-u" onclick="processSubmit();">
						Update
					</button>

				</div>
			</div>

		</div>
		<div class="kt-portlet__body kt-portlet__body--fit">
			<div class="kt-container">
				{{-- Display Success Message Area --}}
				@include('back.'.config('bookdose.theme_back').'.includes.alert_success')

			  	{{-- Display Error Area --}}
				@include('back.'.config('bookdose.theme_back').'.includes.alert_danger')

				<?php 
					$consent_id = 0;
					$re_consent = "6";
					$detail_th  = "";
					$detail_en  = "";
					foreach ($consent_result as $row) {
						$consent_id = $row->id;
						$re_consent = $row->re_consent;
						$detail_th  = $row->detail_th;
						$detail_en  = $row->detail_en;
					}
				?>

			</div>
			
			<form id="frm_main" class="kt-form" action="{{ route('admin.site.consent.update') }}" method="POST" enctype="multipart/form-data">
				@csrf
				<div class="kt-portlet__body font-pri hr-content">
					<div class="kt-portlet__body">
						<div class="kt-section mb-0">
							<div class="form-group">
								<label>ทบทวนการให้ความยินยอม (Re-Consent)</label>
								<div class="input-group">
									<input type="hidden" name="consent_id" value="<?php echo $consent_id;?>">
									<input type="number" class="form-control re_consent" name="re_consent" value="<?php echo $re_consent;?>">
									<div class="input-group-append">
										<span class="input-group-text">เดือน</span>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label>Content (in English) :</label>
								<div class="form-group">
									<textarea class="summernote summernote-desc detail_en" name="detail_en"><?php echo $detail_en;?></textarea>
								</div>
							</div>
							<div class="form-group">
								<label>เนื้อหา (ภาษาไทย) :</label>
								<div class="form-group">
									<textarea class="summernote summernote-desc detail_th" name="detail_th"><?php echo $detail_th;?></textarea>
								</div>
							</div>
						</div>
					</div>

				</div>
				
			</form>
		</div>
	</div>

	

	

</div>
@endsection

@push('additional_js')
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
<script type="text/javascript">
	function processSubmit() {
		if($('.detail_en').val() == "" || $('.detail_th').val() == "" || $('.re_consent').val() == ""){
			Swal.fire("แจ้งเตือนจากระบบ","กรุณากรอกข้อมูลให้ครบ !","warning");
		}else{
			$('#frm_main').submit();
		}
	}

	$(document).ready(function() {
	  	$('.summernote').summernote({
	        tabsize: 2,
	        height: 400,
	        callbacks:{
			onPaste: function(e) {
	             console.log('paste it')
	             const bufferText = ((e.originalEvent || e).clipboardData || window.clipboardData).getData('Text')
	             e.preventDefault()
	             setTimeout(function () {
	                 document.execCommand('insertText', false, bufferText)
	             }, 10)
	         },
			},
	        toolbar: [
	          // ['style', ['style']],
	          ['font', ['bold', 'underline', 'clear']],
	          ['color', ['color']],
	          ['para', ['ul', 'ol', 'paragraph']],
	          ['table', ['table']],
	          ['insert', ['link', 'picture', 'video']],
	          ['view', ['fullscreen', 'codeview', 'help']]
	        ]
	      });
	});
</script>
@endpush