<div class="kt-grid__item">
	<!--begin: Form Wizard Nav -->
	<div class="kt-wizard-v3__nav">
		<div class="kt-wizard-v3__nav-items">

			<a id="tab_step_general" class="tab-form-step kt-wizard-v3__nav-item" href="{{ route('admin.event.form.step', [$event->id ?? '', 'general']) }}" data-step="general" data-ktwizard-type="step" data-ktwizard-state="{{ $step=='general' ? 'current' : 'pending' }}">
				<div class="kt-wizard-v3__nav-body">
					<div class="kt-wizard-v3__nav-label">
						<span>1</span> General Info
					</div>
					<div class="kt-wizard-v3__nav-bar"></div>
				</div>
			</a>

			<a id="tab_step_invitation" class="tab-form-step kt-wizard-v3__nav-item" href="{{ !empty($event) ? route('admin.event.form.step', [$event->id ?? '', 'invitation']) : 'javascript:;' }}" data-step="invitation" data-ktwizard-type="step" data-ktwizard-state="{{ $step=='invitation' ? 'current' : 'pending' }}">
				<div class="kt-wizard-v3__nav-body {{ empty($event) ? 'disabled' : '' }}">
					<div class="kt-wizard-v3__nav-label">
						<span>2</span> Invitation & Participation
					</div>
					<div class="kt-wizard-v3__nav-bar"></div>
				</div>
			</a>
		</div>
	</div>
	<!--end: Form Wizard Nav -->
</div>