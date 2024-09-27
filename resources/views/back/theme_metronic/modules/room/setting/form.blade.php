@extends('back.'.config('bookdose.theme_back').'.tpl.tpl_admin')

@section('title', 'Room Setting')
@section('page_title', 'Room Setting')
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
					{{ $page_header ?? 'Update room setting'}}
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

			<form id="frm_main" class="kt-form" action="{{ route('admin.room.setting.update') }}" method="POST">
				@csrf
				@method('POST')

				<!-- group resource -->
				<h2 class="px-4 py-3 bg-label-info font-pri">กลุ่มผู้ใช้งาน:</h2>
				<div class="kt-portlet__body">
					<div class="kt-section mb-0">
                        <div class="form-group row d-flex align-items-center">
                            <div class="col-3"></div>
                            <div class="col-3"><label>จำนวนวันที่สามารถจองล่วงหน้า (วัน)</label></div>
                            <div class="col-3"><label>จำนวนการจอง (ครั้ง/วัน)</label></div>
                            <div class="col-3"><label>จำนวนเวลาในการจอง (ชั่วโมง/ครั้ง)</label></div>
                        </div>
						@foreach ($user_groups as $user_group)
						<div class="form-group row d-flex align-items-center">
							<div class="col-3">
								<label>{{ $user_group->name }}</label>
							</div>
                            <div class="col-3">
								<input id="collect_point" name="{{$user_group->id.'_in_advance_day'}}" type="number" class="form-control" placeholder="e.g. 10" value="{{ $user_group->data_rooms['in_advance_day'] ?? '' }}" autocomplete="off">
								<span class="form-text text-muted">หากไม่ระบุจะใช้ค่าเป็น {{config('bookdose.room.in_advance_day')}} วัน</span>
							</div>
                            <div class="col-3">
								<input id="collect_point" name="{{$user_group->id.'_per_day'}}" type="number" class="form-control" placeholder="e.g. 10" value="{{ $user_group->data_rooms['per_day'] ?? '' }}" autocomplete="off">
								<span class="form-text text-muted">หากไม่ระบุจะใช้ค่าเป็น {{config('bookdose.room.per_day')}} ครั้ง/วัน</span>
							</div>
							<div class="col-3">
								<input id="collect_point" name="{{$user_group->id.'_max_hour'}}" type="number" class="form-control" placeholder="e.g. 10" value="{{ $user_group->data_rooms['max_hour'] ?? '' }}" autocomplete="off">
								<span class="form-text text-muted">หากไม่ระบุจะใช้ค่าเป็น {{config('bookdose.room.max_hour')}} ชั่วโมง/ครั้ง</span>
							</div>
						</div>
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
