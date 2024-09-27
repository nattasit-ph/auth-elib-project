<!-- Site Settings -->
<li class="kt-menu__section">
	<h4 class="kt-menu__section-text">Log</h4>
	<i class="kt-menu__section-icon flaticon-more-v2"></i>
</li>


<li class="kt-menu__item {{ (request()->is('admin/visitor-log')) ? 'kt-menu__item--active' : '' }}" aria-haspopup="true">
		<a href="{{ route('admin.visitor-log.index') }}" class="kt-menu__link">
			<span class="kt-menu__link-icon">
				<i class="fas fa-table"></i>
			</span>
			<span class="kt-menu__link-text">Visitor Log</span>
		</a>
	</li>



