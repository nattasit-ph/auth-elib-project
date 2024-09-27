@extends('back.'.config('bookdose.theme_back').'.tpl.tpl_admin')

@section('title', 'Privacy Policy')
@section('page_title', 'Privacy Policy')


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
</style>
@endpush

@section('content')
<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">

	<!-- Borrow -->
	<div class="kt-portlet">
		<div class="kt-portlet__head">
			<div class="kt-portlet__head-label">
				<h3 id="section_title" class="kt-portlet__head-title font-pri">
					เนื้อหา (Content)
				</h3>
			</div>
		</div>
		<div class="kt-portlet__body kt-portlet__body--fit">
			<div class="kt-container">
				{{-- Display Success Message Area --}}
				@include('back.'.config('bookdose.theme_back').'.includes.alert_success')

			  	{{-- Display Error Area --}}
				@include('back.'.config('bookdose.theme_back').'.includes.alert_danger')
			</div>

			<form id="frm_main" class="kt-form" action="{{ route('admin.site.savePolicyAndTerms', $org_slug) }}" method="POST" enctype="multipart/form-data">
				@csrf
				<input type="hidden" name="check_type" value="1">

				<div class="kt-portlet__body font-pri hr-content">
					<div class="row p-3 d-flex align-items-center">
						<div class="col-md-12">
							<p class="f-content">Content (in English)</p>
							<textarea class="summernote" name="detail_en">{{ $policy->detail_en ?? ''; }}</textarea>
						</div>
					</div>
				</div>
				<div class="kt-portlet__body font-pri hr-content">
					<div class="row p-3 d-flex align-items-center">
						<div class="col-md-12">
							<p class="f-content">เนื้อหา (ภาษาไทย)</p>
							<textarea class="summernote" name="detail_th">{{ $policy->detail_th ?? ''; }}</textarea>
						</div>
					</div>
				</div>
				<div class="kt-portlet__body font-pri">
					<div class="row p-3 d-flex align-items-center">
						<div class="col-md-12 text-right">
							<button type="submit" class="btn btn-brand btn-bold ml-auto">Update</button>
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
