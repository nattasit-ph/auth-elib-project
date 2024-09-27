<div class="kt-grid__item">
	<!--begin: Form Wizard Nav -->
	<div class="kt-wizard-v3__nav">
		<div class="kt-wizard-v3__nav-items">

			<a id="tab_step_general" class="tab-form-step kt-wizard-v3__nav-item" href="{{ route('admin.questionnaire.edit', [$org_slug, $content->id ?? '', 'step' => 'general']) }}" data-step="general" data-ktwizard-type="step" data-ktwizard-state="{{ $step=='general' ? 'current' : 'pending' }}">
				<div class="kt-wizard-v3__nav-body">
					<div class="kt-wizard-v3__nav-label d-flex justify-content-between align-items-end">
						<div><span>1</span> General Info</div>
						@if (isset($content) && $content->status == 1)
							<div><span class="kt-badge kt-badge--success kt-badge--dot fs-10"></span>&nbsp;<span class="kt-font-bold kt-font-success fs-10">Active</span></div>
						@elseif (isset($content) && $content->status == 0)
							<div><span class="kt-badge kt-badge--danger kt-badge--dot fs-10"></span>&nbsp;<span class="kt-font-bold kt-font-danger fs-10">Inactive</span></div>
						@endif
					</div>
					<div class="kt-wizard-v3__nav-bar"></div>
				</div>
			</a>

			@isset($content)
			<a id="tab_step_fields" class="tab-form-step kt-wizard-v3__nav-item" href="{{ route('admin.questionnaire.edit', [$org_slug, $content->id, 'step' => 'fields']) }}" data-step="fields" data-ktwizard-type="step" data-ktwizard-state="{{ $step=='fields' ? 'current' : 'pending' }}">
				<div class="kt-wizard-v3__nav-body {{ empty($content) ? 'disabled' : '' }}">
					<div class="kt-wizard-v3__nav-label">
						<span>2</span> Manage Fields
					</div>
					<div class="kt-wizard-v3__nav-bar"></div>
				</div>
			</a>

			<a id="tab_step_submissions" class="tab-form-step kt-wizard-v3__nav-item" href="{{ route('admin.questionnaire.edit', [$org_slug, $content->id, 'step' => 'submissions']) }}" data-step="submissions" data-ktwizard-type="step" data-ktwizard-state="{{ $step=='submissions' ? 'current' : 'pending' }}">
				<div class="kt-wizard-v3__nav-body {{ empty($content) ? 'disabled' : '' }}">
					<div class="kt-wizard-v3__nav-label">
						<span>3</span> Submissions
					</div>
					<div class="kt-wizard-v3__nav-bar"></div>
				</div>
			</a>
			@endisset

		</div>
	</div>
	<!--end: Form Wizard Nav -->
</div>
