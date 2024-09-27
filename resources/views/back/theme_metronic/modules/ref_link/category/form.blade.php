@extends('back.'.config('bookdose.theme_back').'.tpl.tpl_admin')

@section('title', 'Category')
@section('page_title', 'Category')
@section('topbar_button')
<a href="{{ route('admin.reference-link.category.index') }}" class="btn btn-label-brand btn-bold">
	<i class="fa fa-arrow-left"></i> Back
</a>
@endsection

@push('additional_css')
<style type="text/css">
.kt-wizard-v3 .kt-wizard-v3__wrapper .kt-form {
	padding-top: 0 !important;
}
.popover {
  max-width: 600px;
  width: auto;
}
.form-check-label { cursor: pointer; }
.col-w-status { width: 110px; }
</style>
@endpush

@section('content')
<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
	<div class="kt-portlet">
		<div class="kt-portlet__head">
			<div class="kt-portlet__head-label">
				<h3 id="section_title" class="kt-portlet__head-title">
					{{ $page_header ?? 'Create a new reference Link category'}}
				</h3>
			</div>
			<div class="kt-portlet__head-toolbar">
				<div class="kt-form__actions">
					 <a id="btn_save" href="javascript:;" class="btn btn-brand btn-bold btn-wide kt-font-transform-u" onClick="save()">
						<?=(request()->is('admin/reference-link/category/create*') ? 'Save' : 'Update')?>
					 </a>
					 <?php if (request()->is('admin/reference-link/category/create*')) { ?>
					 	<a id="btn_save_and_add_new" href="javascript:;" class="ml-1 btn btn-outline-brand btn-bold btn-wide kt-font-transform-u" onClick="saveAndContinue()">Save & Add new</a>
					 <?php } ?>
					 <a href="{{ route('admin.reference-link.category.index') }}" class="ml-1 btn btn-secondary btn-bold btn-wide kt-font-transform-u">Cancel</a>
				</div>
			</div>
		</div>
		<div class="kt-portlet__body kt-portlet__body--fit">
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

			<form id="frm_main" class="kt-form" action="{{ ($reference_Link_category ?? '') ? route('admin.reference-link.category.update', $reference_Link_category->id) : route('admin.reference-link.category.store') }}" enctype="multipart/form-data" method="POST">
				@csrf
				@method('POST')

				<input type="hidden" id="save_option" name="save_option" value="">
				<div class="kt-portlet__body">
					<div class="kt-section mb-0">
						<div class="form-group">
							<label><sup><i class="la la-asterisk fs-10 text-danger"></i></sup> ชื่อหมวดหมู่:</label>
							<input id="title" name="title" type="text" class="form-control required" placeholder="Enter category name" value="{{ $reference_Link_category->title ?? old('title') }}" autocomplete="off">
						</div>
						<div class="form-group">
							<label><sup><i class="la la-asterisk fs-10 text-danger"></i></sup>Pretty URL:</label>
							<input id="slug" name="slug" type="text" class="form-control" placeholder="Enter pretty URL" value="{{ $reference_Link_category->slug ?? old('slug') }}" autocomplete="off">
							<span class="form-text text-muted">เป็นตัวอักษรภาษาอังกฤษ (a-z) หรือตัวเลข (0-9) หรือเครื่องหมาย - (hyphen) เท่านั้น ใช้แสดงบน URL เพื่อทำการค้นหาข้อมูลทั้งหมดของหมวดนี้ เช่น lifestyle, health-and-beauty เป็นต้น</span>
						</div>

						<div class="form-group">
							<label><sup><i class="la la-asterisk fs-10 text-danger"></i></sup>รูปภาพปก:</label>
							<input id="cover_image_path" name="cover_image_path" type="file" class="form-control ">
							<div class="my-2">
								<span class="form-text text-muted">1. ขนาดที่แนะนำ 200 × 200 px</span>
								<span class="form-text text-muted">2. รองรับเฉพาะไฟล์ .jpg, .jpeg และ .png เท่านั้น</span>
								<span class="form-text text-muted">3. ขนาดไฟล์รูปต้องไม่เกิน 5MB</span>
							</div>
						</div>
						@if (isset($reference_Link_category))
						<div class="form-group">
							<img src="{{ asset('storage/'.$reference_Link_category->cover_image_path) }}" class="img-fluid w-25">
						</div>
						@endif

						<div class="form-group">
							<label><sup><i class="la la-asterisk fs-10 text-danger"></i></sup> ลำดับที่แสดงผล:</label>
							<input name="weight" type="number" class="form-control required" placeholder="e.g. 10, 20, 100, 120, etc." value="{{ $reference_Link_category->weight ?? old('weight') }}" autocomplete="off">
							<span class="form-text text-muted">แสดงผลเรียงลำดับจากค่าน้อยไปหามาก</span>
						</div>

						<div class="form-group d-none">
							<label>สถานะ:</label>
							<div class="kt-radio-inline">
								<label class="kt-radio kt-radio--bold kt-radio--brand kt-radio--check-bold">
									 <input type="radio" name="status" value="1" checked=""> Active
									 <span></span>
								</label>
								<label class="kt-radio kt-radio--bold kt-radio--brand">
									 @if (isset($reward_category->status))
									 	<input type="radio" name="status" value="0" {{ $reward_category->status == '0' ? 'checked' : '' }}> Inactive
									 @else
									 	<input type="radio" name="status" value="0" {{ old('status') =='0' ? 'checked' : '' }}> Inactive
									 @endif
									 <span></span>
								</label>
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
<script type="text/javascript" src="https://cdn.datatables.net/1.10.18/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-notify/0.2.0/js/bootstrap-notify.min.js"></script>
<script type="text/javascript">
function save() {
	$('#save_option').val('1');
	$('#frm_main').submit();
}

function saveAndContinue() {
	$('#save_option').val('2');
	$('#frm_main').submit();
}

$(function() {
	$('[data-toggle=popover]').popover({
		'html': true,
		'placement': 'top',
		'trigger': 'focus'
	});
	$(':text:first').focus();
});
</script>
@endpush
