<div class="kt-portlet">
	<div class="kt-portlet__head">
		<div class="kt-portlet__head-label">
			<h3 id="section_title" class="kt-portlet__head-title font-pri">
				เนื้อหา
			</h3>
		</div>
	</div>
	<div class="kt-portlet__body kt-portlet__body--fit">
		<div class="kt-portlet__body">
			<div id="panel_content" class="form-group">
				@if (!empty($article->data_blocks))
					@php $num=0; @endphp
					@foreach ($article->data_blocks as $content)
					@php $num++; @endphp
					<div class="bg-light px-4 pt-4 pb-2">
						<textarea id="summernote_{{$num}}" class="summernote mb-2" name="description[]">{{ $content ?? '' }}</textarea>
						<div class="text-right my-2 font-sec-th"><a href="javascript:void(0);" class="text-danger btn-delete-block">ลบแถวนี้</a></div>
					</div>
					@endforeach
				@else
					<div class="bg-light px-4 pt-4 pb-2">
						<textarea id="summernote_1" class="summernote mb-2" name="description[]"></textarea>
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
	var summernote_count = $("textarea.summernote").length;
	console.log(summernote_count);

	$('.summernote').summernote({
		// airMode: true,
		height: 100,
		callbacks: {
			onImageUpload: function (image) {
				// console.log(image[0]);
				var textarea_summernote_id = $(this).attr('id');
				uploadImage(image[0], textarea_summernote_id);
			},
			onMediaDelete: function (target) {
				// alert(target[0].src) 
				deleteFile(target[0].src);focus
			}
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
		],
		imageAttributes: {
			icon: '<i class="note-icon-pencil"/>',
			figureClass: 'figureClass',
			figcaptionClass: 'captionClass',
			captionText: 'Caption Goes Here.',
			manageAspectRatio: true // true = Lock the Image Width/Height, Default to true
		},
		lang: 'en-US',
		popover: {
			image: [
				['image', ['resizeFull', 'resizeHalf', 'resizeQuarter', 'resizeNone']],
				['float', ['floatLeft', 'floatRight', 'floatNone']],
				['remove', ['removeMedia']],
				['custom', ['imageAttributes']],
			],
		},
   	});

	$('.note-editable').css('font-size','1rem');


	$('#btn_add_block_text').click(function(e) {
		summernote_count = summernote_count + 1;
		// console.log(summernote_count);
  		e.preventDefault();
  		$('<div class="bg-light px-4 py-2"></div>').appendTo('#panel_content');
		$('<textarea id="summernote_'+summernote_count+'" class="summernote" name="description[]"></textarea>')
			.appendTo('.bg-light:last')
			.summernote({
			   	// airMode: true,
			   	height: 100,
				callbacks: {
					onImageUpload: function (image) {
						// console.log(image[0]);
						var textarea_summernote_id = $(this).attr('id');
						uploadImage(image[0], textarea_summernote_id);
					},
					onMediaDelete: function (target) {
						// alert(target[0].src) 
						deleteFile(target[0].src);focus
					}
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
				],
				imageAttributes: {
					icon: '<i class="note-icon-pencil"/>',
					figureClass: 'figureClass',
					figcaptionClass: 'captionClass',
					captionText: 'Caption Goes Here.',
					manageAspectRatio: true // true = Lock the Image Width/Height, Default to true
				},
				lang: 'en-US',
				popover: {
					image: [
						['image', ['resizeFull', 'resizeHalf', 'resizeQuarter', 'resizeNone']],
						['float', ['floatLeft', 'floatRight', 'floatNone']],
						['remove', ['removeMedia']],
						['custom', ['imageAttributes']],
					],
				},
			});
		$('<div class="text-right my-2 font-sec-th"><a href="javascript:void(0);" class="text-danger btn-delete-block">ลบแถวนี้</a></div>').appendTo('.bg-light:last');

	});

	$('#btn_add_block_media').click(function(e) {
		summernote_count = summernote_count + 1;
		// console.log(summernote_count);
  		e.preventDefault();
  		$('<div class="bg-light px-4 py-2"></div>').appendTo('#panel_content');
		$('<textarea id="summernote_'+summernote_count+'" class="summernote" name="description[]"></textarea>')
			.appendTo('.bg-light:last')
			.summernote({
			   	// airMode: true,
			   	height: 100,
				callbacks: {
					onImageUpload: function (image) {
						// console.log(image[0]);
						var textarea_summernote_id = $(this).attr('id');
						uploadImage(image[0], textarea_summernote_id);
					},
					onMediaDelete: function (target) {
						// alert(target[0].src) 
						deleteFile(target[0].src);focus
					}
				},
			   	fontSizes: ['8', '9', '10', '11', '12', '14', '16', '18', '24', '36'],
				toolbar: [
				    ['table', ['table']],
					['para', ['ul', 'ol', 'paragraph']],
				    ['insert', ['picture', 'video','hr']],
				    ['view', ['fullscreen', 'codeview', 'help']],
				],
				imageAttributes: {
					icon: '<i class="note-icon-pencil"/>',
					figureClass: 'figureClass',
					figcaptionClass: 'captionClass',
					captionText: 'Caption Goes Here.',
					manageAspectRatio: true // true = Lock the Image Width/Height, Default to true
				},
				lang: 'en-US',
				popover: {
					image: [
						['image', ['resizeFull', 'resizeHalf', 'resizeQuarter', 'resizeNone']],
						['float', ['floatLeft', 'floatRight', 'floatNone']],
						['remove', ['removeMedia']],
						['custom', ['imageAttributes']],
					],
				},
			   });
		$('<div class="text-right my-2 font-sec-th"><a href="javascript:void(0);" class="text-danger btn-delete-block">ลบแถวนี้</a></div>').appendTo('.bg-light:last');

	});

	$('body').delegate('.btn-delete-block', 'click', function() {
		if (confirm('Are you sure you want to delete this block?')) {
			$(this).closest('.bg-light').remove();
			summernote_count = $("textarea.summernote").length;
		}
	});

	function uploadImage(image, textarea_summernote_id="") {
		var data = new FormData();
		data.append("image", image);
		$.ajax({
			headers: {
	        	'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	   	 	},
			url: "{{ route('admin.upload.summernoteUploadImage') }}",
			contentType: false,
			processData: false,
			data: data,
			type: "post",
			success: function(url) {
				// console.log(url);
				var image = $('<img>').attr('src', url);
				
				$('#'+textarea_summernote_id).summernote("insertNode", image[0]);
			},
			error: function(data) {
				console.log(data);
			}
		});
	}
	
	function deleteFile(src) {
		$.ajax({
			headers: {
	        	'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	   	 	},
			data: {src : src},
			type: "POST",
			url: "{{ route('admin.upload.summernoteRemoveImage') }}",
			cache: false,
			success: function(resp) {
				console.log(resp);
			}
		});
	}
});
</script>
@endpush