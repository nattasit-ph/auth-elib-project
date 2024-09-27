@if(session()->get('success'))
	<div class="alert alert-success bg-label-success fade show" role="alert">
		<div class="alert-icon"><i class="flaticon2-check-mark"></i></div>
		<div class="alert-text th-pri fs-16">{{ session()->get('success') }}</div>
		<div class="alert-close">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true"><i class="la la-close"></i></span>
			</button>
		</div>
	</div>
@endif