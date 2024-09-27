@extends('back.'.config('bookdose.theme_back').'.tpl.tpl_admin')

@section('title', 'Reference Link')
@section('page_title', 'Reference Link')
@section('topbar_button')

<a href="{{ route('admin.reference-link.index') }}" class="btn btn-label-brand btn-bold">
	<i class="fa fa-arrow-left"></i> Back
</a>
@endsection

@push('additional_css')
<style type="text/css">

</style>
@endpush

@section('content')
<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
<form id="frm_main" class="kt-form" action="{{ isset($reference_links) ? route('admin.reference-link.update', $reference_links->id) : route('admin.reference-link.store') }}" method="POST" enctype="multipart/form-data">
	<div class="kt-portlet">
		<div class="kt-portlet__head">
			<div class="kt-portlet__head-label">
				<h3 id="section_title" class="kt-portlet__head-title">
					{{ $page_header ?? 'Create a new Reference Link'}}
				</h3>
			</div>
			<div class="kt-portlet__head-toolbar">
				<div class="kt-form__actions">
					 <a id="btn_save" href="javascript:;" class="btn btn-brand btn-bold btn-wide kt-font-transform-u" onClick="save()">
							<?=(request()->is('admin/reference-link/create*') ? 'Save' : 'Update')?>
					 </a>
					 <?php if (request()->is('admin/reference-link/create*')) { ?>
					 	<a id="btn_save_and_add_new" href="javascript:;" class="ml-1 btn btn-outline-brand btn-bold btn-wide kt-font-transform-u" onClick="saveAndContinue()">Save & Add new</a>
					 <?php } ?>
					 <a href="<?=url('/admin/settings/reference-link')?>" class="ml-1 btn btn-secondary btn-bold btn-wide kt-font-transform-u">Cancel</a>
				</div>
			</div>
		</div>
				@csrf
				@method('POST')
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

				<input type="hidden" id="save_option" name="save_option" value="">
				<div class="kt-portlet__body">
					<div class="kt-section mb-0">
						<div class="form-group">
							<label><sup><i class="la la-asterisk fs-10 text-danger"></i></sup> ชื่อหัวข้อ:</label>
							<input id="txt_title" name="title" type="text" class="form-control " placeholder="Enter reference_links name" value="{{ $reference_links->title ?? old('title') }}" autocomplete="off">
						</div>
						<div class="form-group">
							<label><sup><i class="la la-asterisk fs-10 text-danger"></i></sup> ชื่อหน่วยงาน:</label>
							<input  name="agency" type="text" class="form-control " placeholder="Enter agency name" value="{{ $reference_links->agency ?? old('agency') }}" autocomplete="off">
						</div>
						<div class="form-group">
							<label><sup><i class="la la-asterisk fs-10 text-danger"></i></sup> ชื่อเว็บไซต์:</label>
							<input name="url" type="text" class="form-control" placeholder="e.g. https://www.google.com" value="{{ $reference_links->url ?? old('url') }}" autocomplete="off">
							<div class="my-2">
								<span class="form-text text-muted">URL สำหรับแสดงผลที่ ชื่อเว็บไซต์</span>
							</div>
							</div>
						<div class="form-group">
							<label><sup><i class="la la-asterisk fs-10 text-danger"></i></sup> ลิงค์เว็บไซต์:</label>
							<input name="external_url" type="text" class="form-control" placeholder="e.g. https://www.google.com" value="{{ $reference_links->external_url ?? old('external_url') }}" autocomplete="off">
								<div class="my-2">
								<span class="form-text text-muted">URL สำหรับปุ่ม Access ที่ต้องการให้ลิงค์ไปที่เว็บไซต์</span>
							</div>
						</div>
						<div class="form-group row ">
							<div class="col-4">
								<label><sup><i class="la la-asterisk fs-10 text-danger"></i></sup> หมวดหมู่:</label>
								<select id="category" name="category" class="form-control required">
									<option value="">เลือกหมวดหมู่</option>
									@foreach( $category as $item_category)
									<option value="{{$item_category->id}}" {{ ($reference_links->category ?? '') == $item_category->id ? 'selected' : '' }}>{{$item_category->title}}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="form-group row">
							<div class="col-4">
								<label><sup><i class="la la-asterisk fs-10 text-danger"></i></sup> แสดงผลหน้าเว็บไซต์:</label><br>
								<select id="system"  style="width: 100%;" class="form-control required"  name="system" >
								<option value="" >เลือกเว็บไซต์ที่ต้องการให้แสดงผล </option>
									<option value="Home" {{ ($reference_links->system ?? '') == 'Home' ? 'selected' : '' }} >Home</option>
									<option value="eLibrary" {{ ($reference_links->system ?? '') == 'eLibrary' ? 'selected' : '' }}>eLibrary</option>
									<option value="eLearning" {{ ($reference_links->system ?? '') == 'eLearning' ? 'selected' : '' }} >eLearning</option>
								</select>
							</div>

						</div>
						<div class="form-group">
							<label><sup><i class="la la-asterisk fs-10 text-danger"></i></sup>รูปภาพปกสำหรับแสดงหน้าแรก:</label>
							<input id="file_path" name="file_path" type="file" class="form-control ">
							<div class="my-2">
								<span class="form-text text-muted">1. ขนาดที่แนะนำ 200 × 200 px</span>
								<span class="form-text text-muted">2. รองรับเฉพาะไฟล์ .jpg, .jpeg และ .png เท่านั้น</span>
								<span class="form-text text-muted">3. ขนาดไฟล์รูปต้องไม่เกิน 5MB</span>
							</div>
						</div>
						@if (isset($reference_links))
						<div class="form-group">
							<img src="{{ asset('storage/'.$reference_links->file_path) }}" class="img-fluid w-25">
						</div>
						@endif

						<div class="form-group">
							<label><sup><i class="la la-asterisk fs-10 text-danger"></i></sup>รูปภาพปกสำหรับแสดงหน้ารายละเอียด:</label>
							<input id="cover_image_path" name="cover_image_path" type="file" class="form-control ">
							<div class="my-2">
								<span class="form-text text-muted">1. รองรับเฉพาะไฟล์ .jpg, .jpeg และ .png เท่านั้น</span>
								<span class="form-text text-muted">2. ขนาดไฟล์รูปต้องไม่เกิน 5MB</span>
							</div>
						</div>
						@if (isset($reference_links))
						<div class="form-group">
							<img src="{{ asset('storage/'.$reference_links->cover_image_path) }}" class="img-fluid w-25">
						</div>
						@endif
						<div class="form-group">
							<label><sup><i class="la la-asterisk fs-10 text-danger"></i></sup> ลำดับที่แสดงผล:</label>
							<input name="weight" type="number" class="form-control required" placeholder="e.g. 10, 20, 100, 120, etc." value="{{ $reference_links->weight ?? old('weight') }}" autocomplete="off">
							<span class="form-text text-muted">แสดงผลเรียงลำดับจากค่าน้อยไปหามาก</span>
						</div>

						<div class="form-group">
							<label>แสดงหน้าแรก:</label>
							<div class="kt-radio-inline">
								<label class="kt-radio kt-radio--bold kt-radio--brand kt-radio--check-bold">
									 <input type="radio" name="is_home" value="1" checked=""> Active
									 <span></span>
								</label>
								<label class="kt-radio kt-radio--bold kt-radio--brand">
									 @if (isset($reference_links))
									 	<input type="radio" name="is_home" value="0" {{ $reference_links->is_home == '0' ? 'checked' : '' }}> Inactive
									 @else
									 	<input type="radio" name="is_home" value="0" {{ old('is_home') =='0' ? 'checked' : '' }}> Inactive
									 @endif
									 <span></span>
								</label>
							 </div>
						</div>

						<div class="form-group">
							<label>สถานะ:</label>
							<div class="kt-radio-inline">
								<label class="kt-radio kt-radio--bold kt-radio--brand kt-radio--check-bold">
									 <input type="radio" name="status" value="1" checked=""> Active
									 <span></span>
								</label>
								<label class="kt-radio kt-radio--bold kt-radio--brand">
									 @if (isset($reference_links))
									 	<input type="radio" name="status" value="0" {{ $reference_links->status == '0' ? 'checked' : '' }}> Inactive
									 @else
									 	<input type="radio" name="status" value="0" {{ old('status') =='0' ? 'checked' : '' }}> Inactive
									 @endif
									 <span></span>
								</label>
							 </div>
						</div>

					</div>
				</div>
			</div>
		</div>
				@include('back.'.config('bookdose.theme_back').'.modules.ref_link.section_content')
	</form>
	

</div>

@endsection



@push('additional_js')
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

function check_groups()
{
	var data_group = <?php echo json_encode($reference_links->group ?? ''); ?>;
	if(data_group)
	{	
		$('#group').val(data_group);
	}
	
}
$(document).ready(function() {
	check_groups();
	$('#group').select2({
      placeholder: "Choose group ",
  	});

	$(':text:first').focus();
});
</script>
@endpush
