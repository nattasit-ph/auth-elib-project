@extends('back.'.config('bookdose.theme_back').'.tpl.tpl_admin')

@section('title', __('menu.back.reward_earning'))
@section('page_title', __('menu.back.reward_earning'))
@push('additional_css')
	<link href="css/pages/wizard/wizard-4.css" rel="stylesheet" type="text/css"/>
	<style type="text/css">
		.kt-wizard-v4 .kt-wizard-v4__nav .kt-wizard-v4__nav-items .kt-wizard-v4__nav-item {
		flex: 0 0 50%;
		}
		.kt-wizard-v4__nav-label-title,
		.kt-wizard-v4 .kt-wizard-v4__nav .kt-wizard-v4__nav-items .kt-wizard-v4__nav-item .kt-wizard-v4__nav-body .kt-wizard-v4__nav-label .kt-wizard-v4__nav-label-title {
			font-size: 1rem;
			font-weight: 600;
		}
		.kt-wizard-v4 .kt-wizard-v4__nav .kt-wizard-v4__nav-items .kt-wizard-v4__nav-item .kt-wizard-v4__nav-body {
			padding: 2rem;
		}
		.kt-wizard-v4__nav-items{
			justify-content: start !important;
		}
	</style>
@endpush

@section('content')
<div id="main_content" class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">

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
	<div class="kt-wizard-v4" id="kt_wizard_v4" data-ktwizard-state="step-first">
		<div class="kt-wizard-v4__nav">
			<div class="kt-wizard-v4__nav-items">
				<!-- Podcast Info -->
				<a id="tab_step_general" class="tab-form-step kt-wizard-v4__nav-item" href="{{ route('admin.rewardEarn.index', [$org_slug, 'step-1']) }}" data-step="1" data-ktwizard-type="step" data-ktwizard-state="{{ $step=='1' ? 'current' : 'pending' }}">
					<div class="kt-wizard-v4__nav-body">
						<div class="kt-wizard-v4__nav-number">1</div>
						<div class="kt-wizard-v3__nav-label w-75">
							<div class="kt-wizard-v4__nav-label-title">
								รายงานรายละเอียดการได้รับแต้ม
							</div>
						</div>
					</div>
				</a>
				<!-- Manage Podcast File -->
				<a id="tab_step_fields" class="tab-form-step kt-wizard-v4__nav-item" href="{{ route('admin.rewardEarn.index', [$org_slug, 'step-2']) }}" data-step="2" data-ktwizard-type="step" data-ktwizard-state="{{ $step=='1' ? 'pending' : 'current' }}">
					<div class="kt-wizard-v4__nav-body ">
						<div class="kt-wizard-v4__nav-number">2</div>
						<div class="kt-wizard-v4__nav-label">
							<div class="kt-wizard-v4__nav-label-title">
								รายงานผลรวมแต้ม
							</div>
						</div>
					</div>
				</a>
			</div>
		</div>
	</div>
	@if ($step=='1')
		@include('back.'.config('bookdose.theme_back').'.modules.reward.reward_earning.section_rewardearning_step1')
	@else
		@include('back.'.config('bookdose.theme_back').'.modules.reward.reward_earning.section_rewardearning_step2')
	@endif

</div>
@endsection
