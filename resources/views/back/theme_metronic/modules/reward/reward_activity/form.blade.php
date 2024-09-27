@extends('back.'.config('bookdose.theme_back').'.tpl.tpl_admin')

@section('title', __('menu.back.activity_point'))
@section('page_title', __('menu.back.activity_point'))
@section('topbar_button')
@endsection

@push('additional_css')
<style type="text/css">
.kt-wizard-v3 .kt-wizard-v3__wrapper .kt-form {
	padding-top: 0 !important;
}
.popover {
  max-width: 600px;
  width: auto;
}
.form-check-label { cursor: pointer; }
.col-w-status { width: 110px; }
</style>
@endpush

@section('content')
<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
	<div class="kt-portlet">
		<div class="kt-portlet__head">
			<div class="kt-portlet__head-label">
				<h3 id="section_title" class="kt-portlet__head-title">
					{{ $page_header ?? 'Update activities'}}
				</h3>
			</div>
			<div class="kt-portlet__head-toolbar">
				<div class="kt-form__actions">
					 <a id="btn_save" href="javascript:;" class="btn btn-brand btn-bold btn-wide kt-font-transform-u" onClick="save()">
						<?=(request()->is('admin/coin-activity/create*') ? 'Save' : 'Update')?>
					 </a>
				</div>
			</div>
		</div>
		<div class="kt-portlet__body kt-portlet__body--fit">
			{{-- Display Success Message Area --}}
			@if(session()->get('success'))
			    <div class="alert alert-solid-success alert-bold alert-dismissible fade show" role="alert" dismissable="true">
			      <div class="alert-text">{{ session()->get('success') }}</div>
			      <div class="alert-close">
						<button type="button" class="close" data-dismiss="alert" aria-label="Close">
							<span aria-hidden="true"><i class="la la-close"></i></span>
						</button>
					</div>
			    </div>
		  	@endif

		  	{{-- Display Error Area --}}
			@if ($errors->any())
			    <div class="alert alert-solid-danger alert-bold" role="alert">
			        <ul>
			            @foreach ($errors->all() as $error)
			                <li>{{ $error }}</li>
			            @endforeach
			        </ul>
			    </div>
			@endif

			<form id="frm_main" class="kt-form" action="{{ route('admin.coin-activity.update') }}" method="POST">
				@csrf
				@method('POST')

				<!-- group resource -->
				<h2 class="px-4 py-3 bg-label-info font-pri">{{ __('menu.back.reward_activity_group_resource',[],'th')}}:</h2>
				<div class="kt-portlet__body">
					<div class="kt-section mb-0">
				
						@foreach ($activities as $activity)
						@if($activity->module == 'belib_resource')
						<div class="form-group row d-flex align-items-center">
							<div class="col-9">
								<label>{{ $activity->title }}</label>
							</div>
							<div class="col-3">
								<input type="hidden" id="collect_id" name="collect_id[]" value="{{ $activity->id }}" readonly>
								<input id="collect_point" name="collect_point[]" type="number" class="form-control" placeholder="e.g. 10" value="{{ $activity->point ?? '' }}" autocomplete="off">
								<span class="form-text text-muted">หากไม่ต้องการให้พอยท์โปรดระบุ 0</span>
							</div>
						</div>
						@endif
						@endforeach

					</div>
				</div>

				<!-- group article -->
				<h2 class="px-4 py-3 bg-label-info font-pri">{{ __('menu.back.reward_activity_group_article',[],'th')}}:</h2>
				<div class="kt-portlet__body">
					<div class="kt-section mb-0">

						@foreach ($activities as $activity)
						@if($activity->module == 'belib_article')
						<div class="form-group row d-flex align-items-center">
							<div class="col-9">
								<label>{{ $activity->title }}</label>
							</div>
							<div class="col-3">
								<input type="hidden" id="collect_id" name="collect_id[]" value="{{ $activity->id }}" readonly>
								<input id="collect_point" name="collect_point[]" type="number" class="form-control" placeholder="e.g. 10" value="{{ $activity->point ?? '' }}" autocomplete="off">
								<span class="form-text text-muted">หากไม่ต้องการให้พอยท์โปรดระบุ 0</span>
							</div>
						</div>
						@endif
						@endforeach

					</div>
				</div>

			</form>
		</div>
	</div>

</div>
@endsection

@push('additional_js')
<script type="text/javascript" src="https://cdn.datatables.net/1.10.18/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-notify/0.2.0/js/bootstrap-notify.min.js"></script>
<script type="text/javascript">
function save() {
	$('#save_option').val('1');
	$('#frm_main').submit();
}

function saveAndContinue() {
	$('#save_option').val('2');
	$('#frm_main').submit();
}

$(function() {
	$('[data-toggle=popover]').popover({
		'html': true,
		'placement': 'top',
		'trigger': 'focus'
	});
	$(':text:first').focus();
});
</script>
@endpush
