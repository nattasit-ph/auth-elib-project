<div class="kt-portlet">
	<div class="kt-portlet__head">
		<div class="kt-portlet__head-label">
			<h3 id="section_title" class="kt-portlet__head-title font-pri">
				รายละเอียด
			</h3>
		</div>
	</div>
	<div class="kt-portlet__body kt-portlet__body--fit">
		<div class="kt-portlet__body">
			<div id="panel_content" class="form-group">
				@if (!empty($reference_links->description))
					@foreach ($reference_links->description as $content)
					<div class="bg-light px-4 pt-4 pb-2">
						<textarea class="summernote mb-2" name="description[]">{{ $content ?? '' }}</textarea>
						<div class="text-right my-2 font-sec-th"><a href="javascript:void(0);" class="text-danger btn-delete-block">ลบแถวนี้</a></div>
					</div>
					@endforeach
				@else
					<div class="bg-light px-4 pt-4 pb-2">
						<textarea class="summernote mb-2" name="description[]"></textarea>
						<div class="text-right my-2 font-sec-th"><a href="javascript:void(0);" class="text-danger btn-delete-block">ลบแถวนี้</a></div>
					</div>
				@endif
			</div>
			<div>
				<div class="dropdown mr-1">
					<button type="button" class="btn btn-primary dropdown-toggle font-pri" id="dropdownMenuOffset" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-offset="10,20">
						เพิ่มแถวใหม่
					</button>
					<div class="dropdown-menu" aria-labelledby="dropdownMenuOffset">
						<a id="btn_add_block_text" class="dropdown-item font-sec-th" href="javascript:void(0);">
							<i class="far fa-align-left mr-2"></i>เพิ่มเนื้อหา
						</a>
						<a id="btn_add_block_media" class="dropdown-item font-sec-th" href="javascript:void(0);">
							<i class="far fa-image mr-2"></i>เพิ่มรูปภาพ/วีดิโอ
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

@push('additional_js')
<script type="text/javascript">
$(document).ready(function() {
	$('.summernote').summernote({
   	// airMode: true,
   	height: 100,
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
   	fontSizes: ['8', '9', '10', '11', '12', '14', '16', '18', '24', '36'],
	 	toolbar: [
		    ['style', ['clear', 'bold', 'italic', 'underline']],
		    ['fontsize', ['fontsize']],
		    // ['fontname', ['fontname']],
		    ['color', ['color']],
		    ['para', ['ul', 'ol', 'paragraph']],
		    ['table', ['table']],
		    ['insert', ['link','hr']],
		    ['view', ['fullscreen', 'codeview', 'help']],
		  ]
   });

	$('.note-editable').css('font-size','1rem');


	$('#btn_add_block_text').click(function(e) {
  		e.preventDefault();
  		$('<div class="bg-light px-4 py-2"></div>').appendTo('#panel_content');
		$('<textarea class="summernote" name="description[]"></textarea>')
			.appendTo('.bg-light:last')
			.summernote({
			   	// airMode: true,
			   	height: 100,
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
			   	fontSizes: ['8', '9', '10', '11', '12', '14', '16', '18', '24', '36'],
				 	toolbar: [
					    ['style', ['clear', 'bold', 'italic', 'underline']],
					    ['fontsize', ['fontsize']],
					    ['color', ['color']],
					    ['para', ['ul', 'ol', 'paragraph']],
					    ['table', ['table']],
					    ['insert', ['link','hr']],
					    ['view', ['fullscreen', 'codeview', 'help']],
					  ]
			   });
		$('<div class="text-right my-2 font-sec-th"><a href="javascript:void(0);" class="text-danger btn-delete-block">ลบแถวนี้</a></div>').appendTo('.bg-light:last');

	});

	$('#btn_add_block_media').click(function(e) {
  		e.preventDefault();
  		$('<div class="bg-light px-4 py-2"></div>').appendTo('#panel_content');
		$('<textarea class="summernote" name="description[]"></textarea>')
			.appendTo('.bg-light:last')
			.summernote({
			   	// airMode: true,
			   	height: 100,
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
			   	fontSizes: ['8', '9', '10', '11', '12', '14', '16', '18', '24', '36'],
				 	toolbar: [
					    ['table', ['table']],
					    ['insert', ['picture', 'video','hr']],
					    ['view', ['fullscreen', 'codeview', 'help']],
					  ]
			   });
		$('<div class="text-right my-2 font-sec-th"><a href="javascript:void(0);" class="text-danger btn-delete-block">ลบแถวนี้</a></div>').appendTo('.bg-light:last');

	});

	$('body').delegate('.btn-delete-block', 'click', function() {
		if (confirm('Are you sure you want to delete this block?')) {
			$(this).closest('.bg-light').remove();
		}
	});

});
</script>
@endpush