<form id="frm_main" class="px-4" action="{{ isset($content) ? route('admin.room.update', [$content->id]) : route('admin.room.store') }}" method="POST" enctype="multipart/form-data">
	@csrf
	@if(isset($content))
		@method('PUT')
	@else
		@method('POST')
	@endif
	
	<input type="hidden" id="step" name="step" value="general">
	<input type="hidden" id="save_option" name="save_option" value="">
	<input type="hidden" id="id" name="id" value="{{ $content->id ?? '' }}">

	<div class="kt-portlet">
		<div class="kt-portlet__body kt-portlet__body--fit">
			<div class="kt-portlet__body">
				{{-- @foreach (session('site_langs') as $k=>$lang)
					<input type="hidden" id="lang.{{ $k }}" name="lang[]" value="{{ $lang->lang }}">
				@endforeach --}}
				@include('back.'.config('bookdose.theme_back').'.modules.room.room.box_shared_attributes')
			</div>
		</div>
	</div>

	<div class="kt-portlet">
		<div class="kt-portlet__head bg-primary">
			<div class="kt-portlet__head-label">
				<h3 id="section_title" class="kt-portlet__head-title font-pri-th text-white">
					สิ่งอำนวยความสะดวกภายในห้อง (Facilities)
				</h3>
			</div>
		</div>
		<div class="kt-portlet__body kt-portlet__body--fit">
			<div class="kt-portlet__body">
				@include('back.'.config('bookdose.theme_back').'.modules.room.room.box_facilities')
			</div>
		</div>
	</div>
	
	@isset($content->id)	
	<div id="panel_q_list" class="kt-portlet">
		<div class="kt-portlet__head bg-primary">
			<div class="kt-portlet__head-label">
					<h3 id="section_title" class="kt-portlet__head-title font-pri-th text-white">
						อัลบั้มรูปภาพ (Photo Gallery)
					</h3>
			</div>
			<div class="kt-portlet__head-toolbar">
					<a id="btn_add_new_photo" href="javascript:void(0);" class="btn btn-success btn-bold btn-sm mr-2" data-toggle="modal" data-target="#modal_room_gallery">
						<i class="fa fa-plus fa-fw"></i> Add new photo
					</a>
			</div>
		</div>			
		<div id="panel_gallery_area" class="kt-portlet__body">
		</div>	
	</div>
	@endisset
	
</form>

@include('back.'.config('bookdose.theme_back').'.modules.room.room.modal_room_gallery')


@push('additional_js')
<script type="text/javascript">
function validate() 
{
	$('#frm_main').validate({
		rules: {
	        title: {
	            required: true,
	        },
			description: {
	            required: true,
	        },
			max_seats: {
	            required: true,
				number: true,
				min: 0,
	        },
	    },
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

function initGallery()
{
	$.ajax({
      url: '{{ route("admin.room.ajaxGetGalleryData") }}',
      type: 'POST',
      headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
      data: {
      	'room_id': "{{ $content->id ?? '' }}"
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
		  url: '{{ route("admin.room.ajaxSetCover") }}',

	      type: 'POST',
	      headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
	      data: {
	      	'id': $(e).data('id'),
	      	'room_id': "{{ $content->id ?? '' }}"
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
	      url: '{{ route("admin.room.ajaxDeleteImage") }}',
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

$(document).ready(function() {
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
   	$('#frm_modal_room_gallery')[0].reset();
		$('#modal_room_gallery').modal('show');
	});

   $( "#modal_room_gallery" ).on('hidden.bs.modal', function(e){
		$(this).find('form').trigger('reset');
		$(this).find('.modal-body > .form-group').removeClass('d-none');
   	$(this).find('.modal-body > .modal-loading').html('Uploading...').addClass('d-none');
   	$('#btn_room_gallery_submit').html('<i class="la la-upload"></i> Upload').removeAttr('disabled').removeClass('disabled');
	});

	$('#btn_room_gallery_submit').on('click', function(e) {
		e.preventDefault();
		e.stopImmediatePropagation();
		var t = $('#modal_room_gallery');
     	$.ajax({
         url: '{{ route("admin.room.ajaxUploadImage") }}',
         type: 'POST',
         headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
         data: new FormData($('#frm_modal_room_gallery')[0]),
         dataType: 'json',
         processData: false,
         contentType: false,
         cache: false,
         beforeSend: function() {
         	t.find('.modal-body > .form-group').addClass('d-none');
         	t.find('.modal-body > .modal-loading').html('Uploading...').removeClass('d-none');
         	$('#btn_room_gallery_submit').html('Uploading...').attr('disabled', true).addClass('disabled');
         },
     	})
     	.done(function(resp) {
			if (resp.status == '200') {
				$( "#modal_room_gallery" ).modal('hide');
				initGallery();
				showNotifyOnScreen(resp);
			}
			else {
				showNotifyOnScreen(resp);
				t.find('.modal-body > .form-group').removeClass('d-none');
	      	t.find('.modal-body > .modal-loading').html(resp.msg).removeClass('d-none');
			}
			$('#btn_room_gallery_submit').html('<i class="la la-upload"></i> Upload').removeAttr('disabled').removeClass('disabled');
		})
		.fail(function(resp){
			showNotifyOnScreen(resp);
		  	t.find('.modal-body > .form-group').removeClass('d-none');
      	t.find('.modal-body > .modal-loading').html(resp.msg).removeClass('d-none');
      	$('#btn_room_gallery_submit').html('<i class="la la-upload"></i> Upload').removeAttr('disabled').removeClass('disabled');
		});
	});

	$('#open_time').select2({
		placeholder: 'ไม่ระบุ',
	});

	$('#closed_time').select2({
		placeholder: 'ไม่ระบุ',
	});

	$('#room_type_id').select2({
		placeholder: 'โปรดเลือก',
	});

	var row_facilities = $('#num_fac').val();
	var i=1;  
	$('#add').click(function(){  
		i++;  
		$('#dynamic_row').append('<div class="row" id="row'+i+'"><div id="row'+i+'" class="form-group col-8"><input type="text" name="facilities[]" placeholder="e.g. Projector, 55-inch TV" class="form-control"/></div><div class="form-group col-4" id="row'+i+'"><button type="button" name="remove" id="'+i+'" class="btn btn-danger btn_remove"><i class="far fa-times pr-0"></i></button></div></div>').find(':text:last').focus();  
	});

	$(document).on('click', '.btn_remove', function(){  
		var button_id = $(this).attr("id");
		//alert(button_id);
		$('#row'+button_id+'').remove();  
	});

	for (num_fac = 1; num_fac <= row_facilities; num_fac++) {
		// console.log(num_fac);
		$('#remove_fac_'+num_fac).click(function(){  
			var button_id = $(this).attr("name"); 
			//alert(button_id);
			$('#'+button_id+'').remove();  
		});  
	}
	
	@isset($content)
		initGallery()
	@endisset

	$(':text:first').focus();
});

	
</script>
@endpush