@if ($errors->any())
    <div class="alert alert-solid-danger alert-bold" role="alert">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
@if(session()->get('error'))
    <div class="alert alert-solid-danger alert-bold alert-dismissible fade show" role="alert" dismissable="true">
      <div class="alert-text">{{ session()->get('error') }}</div>
      <div class="alert-close">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true"><i class="la la-close"></i></span>
			</button>
		</div>
    </div>
@endif