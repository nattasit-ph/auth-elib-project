<div id="modal_noti" class="modal fade bd-example-modal" tabindex="-1" role="dialog" aria-hidden="true">
  	<div class="modal-dialog modal-lg">
  		<form id="frm_modal_noti" name="frm_modal_noti" method="POST" enctype="multipart/form-data">
		@csrf
		@method('POST')
		<input type="hidden" id="modal_noti_item_type" name="modal_noti_item_type" value="">
		<input type="hidden" id="modal_noti_item_id" name="modal_noti_item_id" value="">

    	<div class="modal-content">
    		<div class="modal-header bg-secondary">
				<h5 class="modal-title"><i class="la la-bell"></i> Send Notification</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
	      </div>
			<div class="modal-body">
				<div class="form-group">
					<label><sup><i class="la la-asterisk fs-10 text-danger"></i></sup> ข้อความ:</label><br>
					<input type="text" id="modal_noti_message" name="modal_noti_message" class="form-control required" autocomplete="off" />
					<div class="my-2">
						<span class="form-text text-muted">ข้อความควรกระชับ ความยาวไม่ควรเกิน 50 ตัวอักษร</span>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<div class="w-100 d-flex justify-content-end">
					<div>
						<button id="btn_modal_noti_submit" type="button" class="btn btn-primary text-uppercase mr-2" onclick="submitNotiForm()"><i class="la la-sms"></i> Send</button>
						<button type="button" class="btn btn-secondary text-uppercase" data-dismiss="modal" aria-label="Close">Close</button>
					</div>
				</div>
			</div>
    	</div>
    	</form>
  	</div>
</div>

@push('additional_js')
<script type="text/javascript">
function submitNotiForm() {
	var t = $('#modal_noti');
	$.ajaxSetup({
	    headers: {
	        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	    }
	});
  	$.ajax({
      url: "{{ route('admin.noti.send') }}",
      type: 'POST',
      data: {
      	message: $('#modal_noti_message').val(),
      	item_type: $('#modal_noti_item_type').val(),
      	item_id: $('#modal_noti_item_id').val(),
      },
      dataType: 'json',
      cache: false,
      beforeSend: function() {
      	t.find('.modal-body > .form-group').addClass('d-none');
      	t.find('.modal-body > .modal-loading').html('Saving...').removeClass('d-none');
      	$('#btn_modal_noti_submit').html('Sending...').attr('disabled', true).addClass('disabled');
      },
  	})
  	.done(function(resp) {
		if (resp.status == '200') {
			$("#modal_noti").modal('hide');
			showNotifyOnScreen(resp);
		}
		else {
			t.find('.modal-body > .form-group').removeClass('d-none');
      	t.find('.modal-body > .modal-loading').html(resp.msg).removeClass('d-none');
		}
		$('#btn_modal_noti_submit').html('<i class="la la-download"></i> Save &amp; Close').removeAttr('disabled').removeClass('disabled');
	})
	.fail(function(resp){
	  	t.find('.modal-body > .form-group').removeClass('d-none');
   	t.find('.modal-body > .modal-loading').html(resp.msg).removeClass('d-none');
   	$('#btn_modal_noti_submit').html('<i class="la la-download"></i> Save &amp; Close').removeAttr('disabled').removeClass('disabled');
	});
}

$(function() {
	// Init modal when hidden
	$( "#modal_noti" ).on('hidden.bs.modal', function(e){
		$(this).find('.modal-body > .form-group').removeClass('d-none');
		$(this).find('form').trigger('reset');
   	$('#btn_modal_noti_submit').html('<i class="la la-download"></i> Send notification').removeAttr('disabled').removeClass('disabled');
	});
	$( "#modal_noti" ).on('shown.bs.modal', function(e){
		$(this).find(':text:first').focus();
	});
})
</script>
@endpush