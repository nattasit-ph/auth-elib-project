@extends('back.'.config('bookdose.theme_back').'.tpl.tpl_admin')

@section('title', 'News Category')
@section('page_title', 'News Category')

@section('topbar_button')
	<a href="{{ route('admin.news.category.index') }}" class="btn btn-label-brand btn-bold">
		<i class="fa fa-arrow-left"></i> Back
	</a>
	@isset ($category)
	<a href="{{ route('admin.news.category.create') }}" class="btn btn-outline-brand btn-bold">
		<i class="fa fa-plus"></i> Add new
	</a>
	@endisset
@endsection

@push('additional_css')
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
@endpush

@section('content')
<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
	<div class="kt-portlet">
		<div class="kt-portlet__head">
			<div class="kt-portlet__head-label">
				<h3 id="section_title" class="kt-portlet__head-title">
					{{ !empty($category) ? 'Edit category' : 'Create new category'}}
				</h3>
			</div>
			<div class="kt-portlet__head-toolbar">
				<div class="kt-form__actions">
					 <button id="btn_save" class="btn btn-brand btn-bold btn-wide kt-font-transform-u" onClick="validate()">
							<?=(empty($category) ? 'Save' : 'Update')?>
					 </button>
					 <?php if (empty($category)) { ?>
					 	<a id="btn_save_and_add_new" href="javascript:;" class="ml-1 btn btn-outline-brand btn-bold btn-wide kt-font-transform-u" onClick="saveAndContinue()">Save & Add new</a>
					 <?php } ?>
					 <a href="{{ route('admin.news.category.index') }}" class="ml-1 btn btn-secondary btn-bold btn-wide kt-font-transform-u">Cancel</a>
				</div>
			</div>
		</div>
		<div class="kt-portlet__body kt-portlet__body--fit">
			{{-- Display Success Message Area --}}
			@include('back.'.config('bookdose.theme_back').'.includes.alert_success')

		  	{{-- Display Error Area --}}
			@include('back.'.config('bookdose.theme_back').'.includes.alert_danger')

			<form id="frm_main" class="kt-form" action="{{ isset($category) ? route('admin.news.category.update', $category->id) : route('admin.news.category.store') }}" method="POST" enctype="multipart/form-data">
				@csrf
				{{ isset($category) ? method_field('PUT') : method_field('POST') }}

				<input type="hidden" id="save_option" name="save_option" value="">
				<input type="hidden" id="id" name="id" value="{{ $category->id ?? '' }}">
				<div class="kt-portlet__body">
					<div class="kt-section mb-0">

						<div class="form-group">
							<label><sup><i class="la la-asterisk fs-10 text-danger"></i></sup> ชื่อหมวดหมู่บทความ:</label>
							<input id="title" name="title" type="text" class="form-control required" placeholder="Enter article title" value="{{ $category->title ?? old('title') }}" autocomplete="off">
						</div>

						<div class="form-group">
							<label> Pretty URL:</label>
							<input id="slug" name="slug" type="text" class="form-control" placeholder="e.g. health-and-beauty, travel-and-sports" value="{{ $category->slug ?? old('slug') }}" autocomplete="off">
							<div class="my-2">
								<span class="form-text text-muted">อนุญาตให้เฉพาะตัวอักษรภาษาอังกฤษ (a-z), ตัวเลขอารบิค (0-9), เครื่องหมาย - (Hyphen) และเครื่องหมาย _ (Underscore) เท่านั้น </span>
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
									 @if (isset($category))
									 	<input type="radio" name="status" value="0" {{ $category->status == '0' ? 'checked' : '' }}> Inactive
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
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script type="text/javascript">
function validate() 
{
	$('#frm_main').validate();
	if ($('#frm_main').valid()) {
		save();
	}
	else {
		scrollToClass('error');
		return false;
	}
}

$(document).ready(function() {
	
	$(':text:first').focus();
});
</script>
@endpush
