@forelse ($gallery as $photo)
	<div class="row py-2">
		<div class="col-2 align-self-start">
			<img src="{{ Storage::url($photo->file_path) }}" class="img-fluid">
		</div>
		<div class="col-10 align-self-center">
			@if ($photo->is_cover == 0)
				<a href="javascript:;" data-id="{{ $photo->id }}" class="font-sec-th" onclick="setCover(this)">ตั้งค่าเป็นภาพปก</a>
				<span class="mx-1">|</span>
			@endif
			<a href="javascript:;" data-id="{{ $photo->id }}" class="font-sec-th text-danger" onclick="deletePhoto(this)"><i class="fas fa-trash mr-2"></i>ลบรูปนี้</a>
		</div>
	</div>
@empty
	No photos for this room.
@endforelse