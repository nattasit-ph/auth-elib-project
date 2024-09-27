<div id="modal_reward_gallery" class="modal fade bd-example-modal" tabindex="-1" role="dialog" aria-hidden="true">
  	<div class="modal-dialog modal-lg">
  		<form id="frm_modal_reward_gallery" name="frm_modal_reward_gallery" method="POST" enctype="multipart/form-data">
		@csrf
		@method('POST')
  		<input type="hidden" id="modal_reward_item_id" name="modal_reward_item_id" value="{{ $reward_item->id ?? '' }}">
  		
    	<div class="modal-content">
    		<div class="modal-header">
				<h5 class="modal-title"><i class="la la-edit"></i> <span id="modal_file_title">Upload photo</span></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
	      </div>
			<div class="modal-body">
				<div class="modal-loading mb-4 d-none">Loading...</div>
				<div class="form-group">
					<label><sup><i class="la la-asterisk fs-10 text-danger"></i></sup> Please choose an image file:</label><br>
					<input type="file" name="modal_reward_gallery_file" class="form-control required"/>
					<div class="my-3">
						<span class="form-text text-muted">1. รองรับไฟล์รูปภาพ นามสกุล .jpg, .jpeg หรือ .png เท่านั้น</span>
						<span class="form-text text-muted">2. ขนาดไฟล์รูปไม่ควรเกิน 4MB</span>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<div class="d-flex justify-content-between">
					<button id="btn_reward_gallery_submit" type="button" class="btn btn-primary text-uppercase mr-2"><i class="la la-upload"></i> Upload</button>
					<button type="button" class="btn btn-secondary text-uppercase" data-dismiss="modal" aria-label="Close">Close</button>
				</div>
			</div>
    	</div>
    	</form>
  	</div>
</div>