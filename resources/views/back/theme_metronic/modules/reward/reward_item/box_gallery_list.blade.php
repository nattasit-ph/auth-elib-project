@forelse ($gallery as $photo)
	<div class="row py-2">
		<div class="col-2 align-self-start">
			<img src="{{ Storage::url($photo->file_path) }}" class="img-fluid">
		</div>
		<div class="col-10 align-self-center">
			@if ($photo->is_cover == 0)
				<a href="javascript:;" data-id="{{ $photo->id }}" onclick="setCover(this)">Set as cover</a>
				<span class="mx-1">|</span>
			@endif
			<a href="javascript:;" data-id="{{ $photo->id }}" class="text-danger" onclick="deletePhoto(this)">Delete</a>
		</div>
	</div>
@empty
	No photo for this reward.
@endforelse