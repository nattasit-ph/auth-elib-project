@extends('back.'.config('bookdose.theme_back').'.tpl.tpl_admin')

@section('title', 'Organization Info')
@section('page_title', 'Organization Info')

@push('additional_css')
<style type="text/css">

</style>
@endpush

@section('content')
<?php
	$data_contact = $org_info['data_contact'];
?>
<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
	<div class="kt-portlet">
		<div class="kt-portlet__head">
			<div class="kt-portlet__head-label">
				<h3 id="section_title" class="kt-portlet__head-title">
					{{ $page_header ?? 'Edit organization info'}}
				</h3>
			</div>
			<div class="kt-portlet__head-toolbar">
				<div class="kt-form__actions">
					 <button id="btn_save" class="btn btn-brand btn-bold btn-wide kt-font-transform-u" onClick="validate()">Update</button>
				</div>
			</div>
		</div>
		<div class="kt-portlet__body kt-portlet__body--fit">
			{{-- Display Success Message Area --}}
			@include('back.'.config('bookdose.theme_back').'.includes.alert_success')

		  	{{-- Display Error Area --}}
			@include('back.'.config('bookdose.theme_back').'.includes.alert_danger')

			<form id="frm_main" class="kt-form" action="{{ route('admin.site.updateOrgInfo', $org_slug) }}" method="POST" enctype="multipart/form-data">
				@csrf
				@method('PUT')

				<div class="kt-portlet__body">

					<div class="row">
						<div class="form-group col-6">
							<label><span class="text-danger">*</span> ชื่อองค์กร (ไทย):</label>
							<input type="text" name="name_th" class="form-control required" autocomplete="off" value="{{ $org_info['name_th'] ?? old('name_th') }}">
						</div>
						<div class="form-group col-6">
							<label><span class="text-danger">*</span> Organization Name (English):</label>
							<input type="text" name="name_en" class="form-control required" autocomplete="off" value="{{ $org_info['name_en'] ?? old('name_en') }}">
						</div>
					</div>

					<div class="row">
						<div class="form-group col-6">
							<label>ที่อยู่องค์กร (ไทย):</label>
							<textarea rows="3" name="address_th" class="form-control" autocomplete="off">{{ $org_info['data_contact']['address_th'] ?? old('address_th') }}</textarea>
						</div>
						<div class="form-group col-6">
							<label>Address (English):</label>
							<textarea rows="3" name="address_en" class="form-control" autocomplete="off">{{ $org_info['data_contact']['address_en'] ?? old('address_en') }}</textarea>
						</div>
					</div>
					{{-- @if(in_array(config('bookdose.theme_login'), ['theme_ais']))
					<div class="row">
						<div class="form-group col-6">
							<label>วันทำงาน:</label>
							<div class="row">
								<div class="col-6">
									<select name="days_s" class="form-control">
									@for ($i=0; $i < count($days); $i++)
									<option value="{{$i}}" {{$i == $day_start ? 'selected' : ''}}>{{$days[$i]}}</option>
									@endfor
									</select>
								</div>
								<div class="col-6">
									<select name="days_e" class="form-control">
									@for ($i=0; $i < count($days); $i++)
									<option value="{{$i}}" {{$i == $day_end ? 'selected' : ''}}>{{$days[$i]}}</option>
									@endfor
									</select>
								</div>
							</div>
						</div>
						<div class="form-group col-6">
							<label>เวลาทำงาน:</label>
							<input type="text" name="working_time" class="form-control" autocomplete="off" value="{{ $org_info['working_time'] ?? old('working_time') }}">
						</div>
					</div>
					@endif --}}
					<div class="row">
						<div class="form-group col-6">
							<label>โทรศัพท์:</label>
							<input type="text" name="phone" class="form-control" autocomplete="off" value="{{ $org_info['data_contact']['phone'] ?? old('phone') }}">
						</div>
						<div class="form-group col-6">
							<label>โทรสาร:</label>
							<input type="text" name="fax" class="form-control" autocomplete="off" value="{{ $org_info['data_contact']['fax'] ?? old('fax') }}">
						</div>
					</div>

					<div class="row">
						<div class="form-group col-6">
							<label><span class="text-danger">*</span> อีเมลติดต่อห้องสมุด:</label>
							<input type="email" name="contact_email" class="form-control required" placeholder="contact@example.com" autocomplete="off" value="{{ $org_info['data_contact']['contact_email'] ?? old('contact_email') }}">
						</div>
						<div class="form-group col-6">
							<label>Line:</label>
							<input type="text" name="line" class="form-control" autocomplete="off" value="{{ $org_info['data_contact']['line'] ?? old('line') }}">
						</div>
					</div>

					<div class="row">
						<div class="form-group col-6">
							<label>Facebook:</label>
							<input type="text" name="facebook" class="form-control" autocomplete="off" value="{{ $org_info['data_contact']['facebook'] ?? old('facebook') }}">
						</div>
						<div class="form-group col-6">
							<label>Twitter:</label>
							<input type="text" name="twitter" class="form-control" autocomplete="off" value="{{ $org_info['data_contact']['twitter'] ?? old('twitter') }}">
						</div>
					</div>

					<div class="row">
						<div class="form-group col-6">
							<label>Youtube:</label>
							<input type="text" name="youtube" class="form-control" autocomplete="off" value="{{ $org_info['data_contact']['youtube'] ?? old('youtube') }}">
						</div>
						<div class="form-group col-6">
							<label>Instagram:</label>
							<input type="text" name="instagram" class="form-control" autocomplete="off" value="{{ $org_info['data_contact']['instagram'] ?? old('instagram') }}">
						</div>
					</div>

					<div class="row">
						<div class="form-group col-6">
							<label>Google Map:</label>
							<input type="text" name="google_map" class="form-control" autocomplete="off" value="{{ $org_info['data_contact']['google_map'] ?? old('google_map') }}">
						</div>
						<div class="form-group col-6">
							<label>Website:</label>
							<input type="text" name="website" class="form-control" autocomplete="off" value="{{ $org_info['data_contact']['website'] ?? old('website') }}">
						</div>
					</div>

					<div class="form-group">
						<label>เลือกไฟล์โลโก้องค์กร:</label>
						<input id="logo_path" name="logo_path" type="file" class="form-control" accept="image/*">
						<div class="my-3">
							<span class="form-text text-muted">1. กว้างxยาว ที่แนะนำคือ 500 × 200</span>
							<span class="form-text text-muted">2. ขนาดไฟล์รูปไม่ควรเกิน 2MB</span>
						</div>
					</div>
					@if (isset(($org_info->logo_path)) && !empty($org_info->logo_path))
					<div class="form-group">
						<img src="{{ (Storage::disk('s3')->exists($org_info->logo_path) ? Storage::disk('s3')->url($org_info->logo_path) : Storage::url($org_info->logo_path)) }}" class="img-fluid w-25">
					</div>
					@endif

				</div>
			</form>
		</div>
	</div>

</div>
@endsection

@push('additional_js')
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
	$('[data-toggle=popover]').popover({
		'html': true,
		'placement': 'top',
		'trigger': 'focus'
	});

	$(':text:first').focus();
});
</script>
@endpush
