
<style type="text/css">
	.noti-circle{
		color: #fff !important;
		background: #FF0000 !important;
		padding-top: 3px;
		padding-bottom: 3px;
		padding-left: 6px;
		padding-right: 6px;
		border-radius: 50%;
		font-size: 10px;
		text-align: center;
	}
</style>
@if (session('has_reward'))
<!-- Reward & Activity -->
<li class="kt-menu__section mt-0">
	<h4 class="kt-menu__section-text">REWARD</h4>
	<i class="kt-menu__section-icon flaticon-more-v2"></i>
</li>

<li class="kt-menu__item kt-menu__item--submenu {{ (request()->is($org_slug.'/admin/reward*', $org_slug.'/admin/redemption*')) ? 'kt-menu__item--open kt-menu__item--here' : '' }}" aria-haspopup="true">
	<a href="javascript:;" class="kt-menu__link kt-menu__toggle">
		<span class="kt-menu__link-icon">
			<i class="fas fa-trophy"></i>
		</span>
		<span class="kt-menu__link-text">{{ __('menu.back.reward') }}</span>
		<i class="kt-menu__ver-arrow la la-angle-right"></i>
	</a>
	<div class="kt-menu__submenu "><span class="kt-menu__arrow"></span>
		<ul class="kt-menu__subnav">
			<li class="kt-menu__item {{ (request()->is($org_slug.'/admin/reward', $org_slug.'/admin/reward/*')) ? 'kt-menu__item--active' : '' }}" aria-haspopup="true">
				<a href="{{ route('admin.reward.index', $org_slug) }}" class="kt-menu__link ">
					<i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i>
					<span class="kt-menu__link-text sub-menu">{{ __('menu.back.reward_item') }}</span>
				</a>
			</li>
			<li class="kt-menu__item {{ (request()->is($org_slug.'/admin/reward-category', $org_slug.'/admin/reward-category/*')) ? 'kt-menu__item--active' : '' }}" aria-haspopup="true">
				<a href="{{ route('admin.reward-category.index', $org_slug) }}" class="kt-menu__link ">
					<i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i>
					<span class="kt-menu__link-text sub-menu">{{ __('menu.back.reward_category') }}</span>
				</a>
			</li>
			<li class="kt-menu__item {{ (request()->is($org_slug.'/admin/redemption*')) ? 'kt-menu__item--active' : '' }}" aria-haspopup="true">
				<a href="{{ route('admin.redemption.index', $org_slug) }}" class="kt-menu__link ">
					<i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i>
					<span class="kt-menu__link-text sub-menu">
						{{ __('menu.back.reward_redemption') }}
						&nbsp;
						@if(countRedemption() > 0)
						<span class="noti-circle">{{countRedemption()}}</span>
						@endif
					</span>
				</a>
			</li>
			<li class="kt-menu__item {{ (request()->is($org_slug.'/admin/reward-earn*')) ? 'kt-menu__item--active' : '' }}" aria-haspopup="true">
				<a href="{{ route('admin.rewardEarn.index', [$org_slug, 'step-1']) }}" class="kt-menu__link ">
					<i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i>
					<span class="kt-menu__link-text sub-menu">{{ __('menu.back.reward_earning') }}</span>
				</a>
			</li>
			<li class="kt-menu__item {{ (request()->is($org_slug.'/admin/reward/report/reward-popular')) ? 'kt-menu__item--active' : '' }}" aria-haspopup="true">
				<a href="{{ route('admin.reward.report.rewardPopular', $org_slug) }}" class="kt-menu__link ">
					<i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i>
					<span class="kt-menu__link-text sub-menu">{{ __('menu.back.reward_popular') }}</span>
				</a>
			</li>
		</ul>
	</div>
</li>
@endif
