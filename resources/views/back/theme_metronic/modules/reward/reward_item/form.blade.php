@extends('back.'.config('bookdose.theme_back').'.tpl.tpl_admin')

@section('title', __('menu.back.reward_item'))
@section('page_title', __('menu.back.reward_item'))
@section('topbar_button')
<a href="{{ route('admin.reward.index', $org_slug) }}" class="btn btn-label-brand btn-bold">
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
					{{ $page_header ?? 'Create a new reward'}}
				</h3>
			</div>
			<div class="kt-portlet__head-toolbar">
				<div class="kt-form__actions">
					 <a id="btn_save" href="javascript:;" class="btn btn-brand btn-bold btn-wide kt-font-transform-u" onClick="save()">
							<?=(request()->is('admin/reward/create*') ? 'Save' : 'Update')?>
					 </a>
					 <a href="{{ route('admin.reward.index', $org_slug) }}" class="ml-1 btn btn-secondary btn-bold btn-wide kt-font-transform-u">Cancel</a>
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

			<form id="frm_main" class="kt-form" action="{{ ($reward_item ?? '') ? route('admin.reward.update', [$org_slug, $reward_item->id]) : route('admin.reward.store', $org_slug) }}" method="POST">
				@csrf
				@method('POST')

				<input type="hidden" id="save_option" name="save_option" value="">
				<div class="kt-portlet__body">
					<div class="kt-section mb-0">
						<div class="form-group">
							<label><sup><i class="la la-asterisk fs-10 text-danger"></i></sup> ชื่อของรางวัล:</label>
							<input id="title" name="title" type="text" class="form-control required" placeholder="Enter reward name" value="{{ $reward_item->title ?? old('title') }}" autocomplete="off">
						</div>

						<div class="form-group">
							<label><sup><i class="la la-asterisk fs-10 text-danger"></i></sup> รายละเอียด:</label>
							<textarea class="form-control" id="description" name="description" rows="5">{{ $reward_item->description ?? old('description') }}</textarea>
						</div>

						<div class="form-group row">
							<div class="col-4">
								<label><sup><i class="la la-asterisk fs-10 text-danger"></i></sup> หมวดหมู่ของรางวัล:</label>
								<select id="reward_category_id" name="reward_category_id" class="form-control required">
									<option value="">เลือกหมวดหมู่</option>
									@forelse ($reward_categories as $category)
										<option value="{{ $category->id }}" {{ ($reward_item->reward_category_id ?? '') == $category->id ? 'selected' : '' }}>{{ $category->title }}</option>
									@empty
									@endforelse
								</select>
							</div>
						</div>

						<div class="form-group row">
							<div class="col-4">
								<label><sup><i class="la la-asterisk fs-10 text-danger"></i></sup> คะแนนที่ใช้แลก (แต้ม):</label>
								<input type="number" class="form-control required" id="point" name="point" value="{{ $reward_item->point ?? old('point') }}" placeholder="e.g. 20">
							</div>
							<div class="col-4">
								<label>จำกัดการแลก (ชิ้น/1 ผู้ใช้งาน):</label>
								<input type="number" class="form-control" id="max_per_user" name="max_per_user" value="{{ $reward_item->max_per_user ?? old('max_per_user') }}" placeholder="e.g. 2">
								<span class="form-text text-muted">ไม่ต้องระบุหากไม่จำกัดจำนวนชิ้นในการแลก</span>
							</div>
							<div class="col-4">
								<label><sup><i class="la la-asterisk fs-10 text-danger"></i></sup> สต๊อกจำนวนชิ้นที่มีให้แลก (ชิ้น):</label>
								<input type="number" class="form-control required" id="stock_avail" name="stock_avail" value="{{ $reward_item->stock_avail ?? old('stock_avail') }}" placeholder="e.g. 10">
							</div>
						</div>


						<div class="form-group row">
							<div class="col-4">
								<label>วันเริ่มต้นเปิดรับแลก</label>
								<div class="input-group date">
									<input type="hidden" id="started_at" name="started_at" value="{{ !empty($reward_item->started_at) ? date('Y-m-d', strtotime($reward_item->started_at)) : '' }}"/>
									<input id="dp_start_date" name="dp_start_date" type="text" class="form-control kt_datepicker" data-date-format="dd/mm/yyyy" autocomplete="off">
									<div class="input-group-append">
										<span class="input-group-text">
											<i class="la la-calendar"></i>
										</span>
									</div>
								</div>
								<span class="form-text text-muted">ระบุเป็นวันที่ปัจจุบันหรือไม่ต้องระบุหากต้องการให้สามารถเปิดรับแลกได้เลยทันที</span>
							</div>
							<div class="col-4">
								<label>วันสิ้นสุด</label>
								<div class="input-group date">
									<input type="hidden" id="expired_at" name="expired_at" value="{{ !empty($reward_item->expired_at) ? date('Y-m-d', strtotime($reward_item->expired_at)) : '' }}"/>
									<input id="dp_expiration_date" name="dp_expiration_date" type="text" class="form-control kt_datepicker" data-date-format="dd/mm/yyyy" autocomplete="off">
									<div class="input-group-append">
										<span class="input-group-text">
											<i class="la la-calendar"></i>
										</span>
									</div>
								</div>
								<span class="form-text text-muted">1. จะสามารถแลกของรางวัลได้จนถึงเวลา 23:59 ของวันที่ระบุ หรือจนกว่าของจะหมด</span>
								<span class="form-text text-muted">2. ไม่ต้องระบุหากต้องการให้แลกจนกว่าของจะหมด</span>
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
									 @if (isset($reward_item->status))
									 	<input type="radio" name="status" value="0" {{ $reward_item->status == '0' ? 'checked' : '' }}> Inactive
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


	@isset($reward_item->id)
	<div id="panel_q_list" class="kt-portlet">
			<div class="kt-portlet__head">
				 <div class="kt-portlet__head-label">
						<h3 id="section_title" class="kt-portlet__head-title">
							Photo Gallery
						</h3>
				 </div>
				 <div class="kt-portlet__head-toolbar">
						<a id="btn_add_new_photo" href="javascript:void(0);" class="btn btn-brand btn-bold btn-sm mr-2" data-toggle="modal" data-target="#modal_reward_gallery">
							<i class="fa fa-plus fa-fw"></i> Add new photo
						</a>
				 </div>
			</div>
			<div id="panel_gallery_area" class="kt-portlet__body">
			</div>
	</div>
	@endisset

</div>
@include('back.'.config('bookdose.theme_back').'.modules.reward.reward_item.modal_reward_gallery')
@endsection

@push('additional_js')
<script type="text/javascript" src="https://cdn.datatables.net/1.10.18/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-notify/0.2.0/js/bootstrap-notify.min.js"></script>
<script type="text/javascript">
function save() {
	$('#started_at').val('');
	$('#expired_at').val('');
	if ($('#dp_expiration_date').val() !== '') {
		$('#expired_at').val($('#dp_expiration_date').data('datepicker').getFormattedDate('yyyy-mm-dd'));
	}
	if ($('#dp_start_date').val() !== '') {
		$('#started_at').val($('#dp_start_date').data('datepicker').getFormattedDate('yyyy-mm-dd'));
	}
	$('#save_option').val('1');
	$('#frm_main').submit();
}

function initGallery()
{
	$.ajax({
      url: '{{ route("admin.reward.ajaxGetGalleryData", $org_slug) }}',
      type: 'POST',
      headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
      data: {
      	'reward_item_id': "{{ $reward_item->id ?? '' }}"
      },
      dataType: 'json',
      beforeSend: function() {
      	$('#panel_gallery_area').html('Loading...');
      },
  	})
  	.done(function(resp) {
		if (resp.status == '200') {
			$('#panel_gallery_area').html(resp.html);
		}
	})
}

function setCover(e)
{
	if (confirm('Are you sure you want to set this photo as a cover?')) {
		$.ajax({
	      url: '{{ route("admin.reward.ajaxSetCover", $org_slug) }}',
	      type: 'POST',
	      headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
	      data: {
	      	'id': $(e).data('id'),
	      	'reward_item_id': "{{ $reward_item->id ?? '' }}"
	      },
	      dataType: 'json',
	      beforeSend: function() {

	      },
	  	})
	  	.done(function(resp) {
			if (resp.status == '200') {
				initGallery();
			}
		})
	}
}

function deletePhoto(e)
{
	if (confirm('Are you sure you want to delete this photo?')) {
		$.ajax({
	      url: '{{ route("admin.reward.ajaxDeleteImage", $org_slug) }}',
	      type: 'POST',
	      headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
	      data: {
	      	'id': $(e).data('id'),
	      },
	      dataType: 'json',
	      beforeSend: function() {

	      },
	  	})
	  	.done(function(resp) {
			if (resp.status == '200') {
				initGallery();
			}
		})
	}
}


$(function() {
	$('[data-toggle=popover]').popover({
		'html': true,
		'placement': 'top',
		'trigger': 'focus'
	});

	// Init modals
	/* ---------------------------------------------------------------------
	 * Modal import questions
	/* ---------------------------------------------------------------------*/
	$('#btn_add_new_photo').on('click', function(e) {
   	$('#frm_modal_reward_gallery')[0].reset();
		$('#modal_reward_gallery').modal('show');
	});

   $( "#modal_reward_gallery" ).on('hidden.bs.modal', function(e){
		$(this).find('form').trigger('reset');
		$(this).find('.modal-body > .form-group').removeClass('d-none');
   	$(this).find('.modal-body > .modal-loading').html('Uploading...').addClass('d-none');
   	$('#btn_reward_gallery_submit').html('<i class="la la-upload"></i> Upload').removeAttr('disabled').removeClass('disabled');
	});

	$('#btn_reward_gallery_submit').on('click', function(e) {
		e.preventDefault();
		e.stopImmediatePropagation();
		var t = $('#modal_reward_gallery');
     	$.ajax({
         url: '{{ route("admin.reward.ajaxUploadImage", $org_slug) }}',
         type: 'POST',
         headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
         data: new FormData($('#frm_modal_reward_gallery')[0]),
         dataType: 'json',
         processData: false,
         contentType: false,
         cache: false,
         beforeSend: function() {
         	t.find('.modal-body > .form-group').addClass('d-none');
         	t.find('.modal-body > .modal-loading').html('Uploading...').removeClass('d-none');
         	$('#btn_reward_gallery_submit').html('Uploading...').attr('disabled', true).addClass('disabled');
         },
     	})
     	.done(function(resp) {
			if (resp.status == '200') {
				$( "#modal_reward_gallery" ).modal('hide');
				initGallery();
				showNotifyOnScreen(resp);
			}
			else {
				showNotifyOnScreen(resp);
				t.find('.modal-body > .form-group').removeClass('d-none');
	      	t.find('.modal-body > .modal-loading').html(resp.msg).removeClass('d-none');
			}
			$('#btn_reward_gallery_submit').html('<i class="la la-upload"></i> Upload').removeAttr('disabled').removeClass('disabled');
		})
		.fail(function(resp){
			showNotifyOnScreen(resp);
		  	t.find('.modal-body > .form-group').removeClass('d-none');
      	t.find('.modal-body > .modal-loading').html(resp.msg).removeClass('d-none');
      	$('#btn_reward_gallery_submit').html('<i class="la la-upload"></i> Upload').removeAttr('disabled').removeClass('disabled');
		});
	});


	// Init
	$('#dp_start_date, #dp_expiration_date').datepicker({
	   autoclose: true,
	   todayBtn: "linked",
	   clearBtn: true,
	   todayHighlight: true,
	   startDate: new Date()
	});

	@isset($reward_item)
		$("#dp_start_date").datepicker("setDate" , "{{ date('d/m/Y', strtotime($reward_item->started_at ?? '')) }}");
		$("#dp_expiration_date").datepicker("setDate" , "{{ date('d/m/Y', strtotime($reward_item->expired_at ?? '')) }}");
	@endisset

	initGallery();
	$(':text:first').focus();
});
</script>
@endpush
