<form id="frm_main" class="kt-form py-0 px-4" action="{{ isset($event) ? route('admin.event.update') : route('admin.event.store') }}" method="POST" enctype="multipart/form-data">
	@csrf
	@method('POST')

	<input type="hidden" id="save_option" name="save_option" value="">
	<input type="hidden" id="id" name="id" value="{{ $event->id ?? '' }}">

	<div class="kt-portlet__body">
		<div class="kt-section mb-0">
			<div class="form-group">
				<label><sup><i class="la la-asterisk fs-10 text-danger"></i></sup>ชื่อกิจกรรม:</label>
				<input name="title" type="text" class="form-control required" placeholder="Enter title" value="{{ $event->title ?? old('title') }}" autocomplete="off">
			</div>

			<div class="row">
				<div class="form-group col-6">
					<label><sup><i class="la la-asterisk fs-10 text-danger"></i></sup>วันที่เริ่มต้น:</label>
					<div class="input-group date">
						<input id="dp_event_start" name="event_start" type="text" class="form-control kt_datepicker required" data-date-format="dd/mm/yyyy"/ autocomplete="off" value="{{ !empty($event->event_start) ? date('d/m/Y', strtotime($event->event_start)) : '' }}">
						<div class="input-group-append">
							<span class="input-group-text">
								<i class="la la-calendar"></i>
							</span>
						</div>
					</div>
				</div>

				<div class="form-group col-6">
					<label><sup><i class="la la-asterisk fs-10 text-danger"></i></sup>วันที่สิ้นสุด:</label>
					<div class="input-group date">
						<input id="dp_event_end" name="event_end" type="text" class="form-control kt_datepicker required" data-date-format="dd/mm/yyyy"/ autocomplete="off" value="{{ !empty($event->event_end) ? date('d/m/Y', strtotime($event->event_end)) : '' }}">
						<div class="input-group-append">
							<span class="input-group-text">
								<i class="la la-calendar"></i>
							</span>
						</div>
					</div>
				</div>

			</div>

			<div class="row">
				<div class="form-group col-6">
					<label>ผู้จัดงาน:</label>
					<input name="organizer" type="text" class="form-control" placeholder="Enter organizer" value="{{ $event->organizer ?? old('organizer') }}" autocomplete="off">
				</div>
				<div class="form-group col-6">
					<label>สถานที่จัดงาน:</label>
					<input name="venue" type="text" class="form-control" placeholder="Enter venue" value="{{ $event->venue ?? old('venue') }}" autocomplete="off">
				</div>
			</div>

			<div class="row">
				<div class="form-group col-6" style="margin-bottom:0 !important;">
					<label>Email:</label>
					<input name="email" type="text" class="form-control" placeholder="contact@email.com" value="{{ $event->email ?? old('email') }}" autocomplete="off">
				</div>
				<div class="form-group col-6" style="margin-bottom:0 !important;">
					<label>เว็บไซต์:</label>
					<input name="website" type="text" class="form-control" placeholder="https://" value="{{ $event->website ?? old('website') }}" autocomplete="off">
				</div>
			</div>

			<div class="row d-none">
				<div class="form-group col-6">
					<label>Facebook:</label>
					<input name="facebook" type="text" class="form-control" placeholder="Enter facebook URL" value="{{ $event->facebook ?? old('facebook') }}" autocomplete="off">
				</div>
				<div class="form-group col-6">
					<label>Youtube:</label>
					<input name="youtube" type="text" class="form-control" placeholder="Enter youtube URL" value="{{ $event->youtube ?? old('youtube') }}" autocomplete="off">
				</div>
			</div>

			<div class="form-group d-none">
				<label><sup><i class="la la-asterisk fs-10 text-danger"></i></sup>Pretty URL:</label>
				<input name="slug" type="text" class="form-control" placeholder="Enter pretty URL" value="{{ $event->slug ?? old('slug') }}" autocomplete="off">
				<div class="my-3">
					<span class="form-text text-muted">* อนุญาตให้เฉพาะตัวอักษรภาษาอังกฤษ (a-z), ตัวเลขอารบิค (0-9), เครื่องหมาย - (Hyphen) และเครื่องหมาย _ (Underscore) เท่านั้น </span>
				</div>
			</div>

			<div class="form-group my-5">
				<label><sup><i class="la la-asterisk fs-10 text-danger"></i></sup>รายละเอียดกิจกรรม:</label>
				<textarea name="description" class="summernote">{{ $event->description ?? old('description') }}</textarea>
			</div>

			<div class="form-group">
				<label>เลือกไฟล์ปก:</label>
				<input id="cover_image_path" name="cover_image_path" type="file" class="form-control" accept="image/*">
				<div class="my-3">
					<span class="form-text text-muted">1. กว้างxยาว ที่แนะนำคือ 830 × 530px</span>
					<span class="form-text text-muted">2. ขนาดไฟล์รูปไม่ควรเกิน 5MB</span>
				</div>
			</div>
			@if (isset($event) && !empty($event->cover_image_path))
			<div class="form-group">
				<img src="{{ Storage::url($event->cover_image_path) }}" class="img-fluid w-25">
			</div>
			@endif

			<div class="form-group">
				<label>สถานะ:</label>
				<div class="kt-radio-inline">
					<label class="kt-radio kt-radio--bold kt-radio--brand kt-radio--check-bold">
						<input type="radio" name="status" value="1" checked=""> Active
						<span></span>
					</label>
					<label class="kt-radio kt-radio--bold kt-radio--brand">
						@if (isset($event))
						<input type="radio" name="status" value="0" {{ $event->status == '0' ? 'checked' : '' }}> Inactive
						@else
						<input type="radio" name="status" value="0" checked=""> Inactive
						@endif
						<span></span>
					</label>
				</div>
			</div>

		</div>
	</div>
</form>



@push('additional_js')
<script type="text/javascript">
function validate() 
{
	$('#frm_main').validate({
		errorPlacement: function(error, element) {
       	if (element.hasClass('select2-hidden-accessible')) {
       		error.insertAfter(element.siblings('.select2'));
       		element.siblings('.select2').addClass('error');
       	}
       	else if (element.hasClass('kt_datepicker')) {
       		error.insertAfter(element.closest('.input-group.date'));
       	}
       	else {
          	error.insertAfter(element);
       	}
      }
	});
	if ($('#frm_main').valid()) {
		save();
	}
	else {
		$('.error:first').focus();
		return false;
	}
}

$(function() { 
	$('.summernote').summernote({
   	height: 300,
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
   });
   
	$('[data-toggle=popover]').popover({
		'html': true,
		'placement': 'top',
		'trigger': 'focus'
	});

	if ($('#ddl_country').length > 0)
   	$('#ddl_country').select2({});

	$('#dp_event_start').datepicker({
	   rtl: KTUtil.isRTL(),
	   // todayBtn: "linked",
	   clearBtn: true,
	   todayHighlight: true,
	   startDate: new Date(),
	   orientation: "bottom left",
	   autoclose:true,
	   // templates: arrows
	});
	
	$('#dp_event_end').datepicker({
	   rtl: KTUtil.isRTL(),
	   // todayBtn: "linked",
	   clearBtn: true,
	   todayHighlight: true,
	   startDate: new Date(),
	   orientation: "bottom left",
	   autoclose:true,
	   // templates: arrows
	});
	
	$(':text:first').focus();
});
</script>
@endpush