@extends('back.'.config('bookdose.theme_back').'.tpl.tpl_admin')

@section('title', __('menu.back.delete_user_policy'))
@section('page_title', __('menu.back.delete_user_policy'))

@push('additional_css')
<style type="text/css">

</style>
@endpush

@section('content')
<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
	<div class="kt-portlet">
		<div class="kt-portlet__head">
			<div class="kt-portlet__head-label">
				<h3 id="section_title" class="kt-portlet__head-title">
					{{ $page_header ?? 'Edit delete user policy'}}
				</h3>
			</div>
			<div class="kt-portlet__head-toolbar">
				<div class="kt-form__actions">
					 <button id="btn_save" class="btn btn-brand btn-bold btn-wide kt-font-transform-u" onClick="validate()">Update</button>
				</div>
			</div>
		</div>
		<div class="kt-portlet__body kt-portlet__body--fit">
			{{-- Display Success Message Area --}}
			@include('back.'.config('bookdose.theme_back').'.includes.alert_success')

		  	{{-- Display Error Area --}}
			@include('back.'.config('bookdose.theme_back').'.includes.alert_danger')

			<form id="frm_main" class="kt-form" action="{{ route('admin.site.updateDeleteUserPolicy') }}" method="POST" enctype="multipart/form-data">
				@csrf
				@method('PUT')

				<div class="kt-portlet__body">
					<div class="kt-section mb-0">
						@foreach ($site_info as $item)
						<div class="form-group">
							<label>{{ $item->meta_label }}:</label>

							@switch ($item->meta_input_type)
								@case ('text')
									<input id="{{ $item->meta_key.'_'.$item->meta_lang }}" name="{{ $item->meta_key.'_'.$item->meta_lang }}" type="text" class="form-control" placeholder="Enter {{ $item->meta_label }}" value="{{ $item->meta_value }}" autocomplete="off">
									@break

								@case ('textarea')
									<textarea id="{{ $item->meta_key.'_'.$item->meta_lang }}" name="{{ $item->meta_key.'_'.$item->meta_lang }}" class="form-control" rows="10" autocomplete="off">{{ $item->meta_value }}</textarea>
									@break

								@default
									<input id="{{ $item->meta_key.'_'.$item->meta_lang }}" name="{{ $item->meta_key.'_'.$item->meta_lang }}" type="text" class="form-control" placeholder="Enter {{ $item->meta_label }}" value="{{ $item->meta_value }}" autocomplete="off">
									@break
							@endswitch

							<span class="form-text text-muted">{{ $item->meta_help }}</span>
						</div>
						<input type="hidden" name="meta_key[]" value="{{ $item->meta_key }}">
						<input type="hidden" name="meta_lang[]" value="{{ $item->meta_lang }}">
						@endforeach
					</div>

				</div>
			</form>
		</div>
	</div>

</div>
@endsection

@push('additional_js')
<script type="text/javascript">
function validate() 
{
	$('#frm_main').validate();
	if ($('#frm_main').valid()) {
		save();
	}
	else {
		scrollToClass('error');
		return false;
	}
}

$(document).ready(function() {
	$('[data-toggle=popover]').popover({
		'html': true,
		'placement': 'top',
		'trigger': 'focus'
	});

	$(':text:first').focus();
});
</script>
@endpush
