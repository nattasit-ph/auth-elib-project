<!-- Site Settings -->
<li class="kt-menu__section">
	<h4 class="kt-menu__section-text">SETTING</h4>
	<i class="kt-menu__section-icon flaticon-more-v2"></i>
</li>


<li class="kt-menu__item {{ (request()->is($org_slug.'/admin/site/info*')) ? 'kt-menu__item--active' : '' }}" aria-haspopup="true">
	<a href="{{ route('admin.site.editOrgInfo', $org_slug) }}" class="kt-menu__link">
		<span class="kt-menu__link-icon">
			<i class="fas fa-info"></i>
		</span>
		<span class="kt-menu__link-text">Organization Info</span>
	</a>
</li>

@if(isSuperAdmin())
<li class="kt-menu__item {{ (request()->is($org_slug.'/admin/site/google-analytics*')) ? 'kt-menu__item--active' : '' }}" aria-haspopup="true">
	<a href="{{ route('admin.site.GoogleAnalytics', $org_slug) }}" class="kt-menu__link">
		<span class="kt-menu__link-icon">
			<i class="fab fa-google"></i>
		</span>
		<span class="kt-menu__link-text">Google Analytics</span>
	</a>
</li>
<!--
<li class="kt-menu__item {{ (request()->is($org_slug.'/admin/site/privacy-policy*')) ? 'kt-menu__item--active' : '' }}" aria-haspopup="true">
	<a href="{{ route('admin.site.editPrivacyPolicy', $org_slug) }}" class="kt-menu__link">
		<span class="kt-menu__link-icon">
			<i class="far fa-user-secret"></i>
		</span>
		<span class="kt-menu__link-text">{{ __('menu.back.privacy_policy') }}</span>
	</a>
</li>
<li class="kt-menu__item {{ (request()->is($org_slug.'/admin/site/delete-user-policy*')) ? 'kt-menu__item--active' : '' }}" aria-haspopup="true">
	<a href="{{ route('admin.site.editDeleteUserPolicy', $org_slug) }}" class="kt-menu__link">
		<span class="kt-menu__link-icon">
			<i class="far fa-user-times"></i>
		</span>
		<span class="kt-menu__link-text">{{ __('menu.back.delete_user_policy') }}</span>
	</a>
</li> -->


<!-- ### policy ### -->
<li class="kt-menu__item kt-menu__item--submenu {{ (request()->is($org_slug.'/admin/site/addCookie',$org_slug.'/admin/site/addPolicy',$org_slug.'/admin/site/addTerms',$org_slug.'/admin/site/consent',$org_slug.'/admin/site/consentLog',$org_slug.'/admin/site/consentAdd',$org_slug.'/admin/site/consentEdit/*')) ? 'kt-menu__item--open kt-menu__item--here' : '' }}" aria-haspopup="true">
	<a href="javascript:;" class="kt-menu__link kt-menu__toggle">
		<span class="kt-menu__link-icon">
			<i class="fas fa-shield"></i>
		</span>
		<span class="kt-menu__link-text">{{ __('menu.back.menu_policy') }}</span>
		<i class="kt-menu__ver-arrow la la-angle-right"></i>
	</a>
	<div class="kt-menu__submenu "><span class="kt-menu__arrow"></span>
		<ul class="kt-menu__subnav">
			<li class="kt-menu__item {{ (request()->is($org_slug.'/admin/site/addCookie')) ? 'kt-menu__item--active' : '' }}" aria-haspopup="true">
				<a href="{{ route('admin.site.addCookie', $org_slug) }}" class="kt-menu__link ">
					<i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i>
					<span class="kt-menu__link-text sub-menu">{{ __('menu.back.menu_cookie') }}</span>
				</a>
			</li>
			<li class="kt-menu__item {{ (request()->is($org_slug.'/admin/site/addPolicy')) ? 'kt-menu__item--active' : '' }}" aria-haspopup="true">
				<a href="{{ route('admin.site.addPolicy', $org_slug) }}" class="kt-menu__link ">
					<i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i>
					<span class="kt-menu__link-text sub-menu">{{ __('menu.back.menu_privacy') }}</span>
				</a>
			</li>
			<li class="kt-menu__item {{ (request()->is($org_slug.'/admin/site/addTerms')) ? 'kt-menu__item--active' : '' }}" aria-haspopup="true">
				<a href="{{ route('admin.site.addTerms', $org_slug) }}" class="kt-menu__link ">
					<i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i>
					<span class="kt-menu__link-text sub-menu">{{ __('menu.back.menu_terms') }}</span>
				</a>
			</li>
			@if(config('bookdose.regis.consent_enable') === true || in_array(config('bookdose.theme_login'), ['theme_bedo', 'theme_bedo_lib']))

			<li class="kt-menu__item {{ (request()->is($org_slug.'/admin/site/consent',$org_slug.'/admin/site/consentLog',$org_slug.'/admin/site/consentAdd',$org_slug.'/admin/site/consentEdit/*')) ? 'kt-menu__item--active' : '' }}" aria-haspopup="true">
				<a href="{{ route('admin.site.consent', $org_slug) }}" class="kt-menu__link ">
					<i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i>
					<span class="kt-menu__link-text sub-menu">{{ __('menu.back.menu_consent') }}</span>
				</a>
			</li>
			@endif
		</ul>
	</div>
</li>

@if(in_array(config('bookdose.theme_login'), array('theme_okmd', 'theme_etech', 'theme_bedo_lib', 'theme_bedo')))
<li class="kt-menu__item kt-menu__item--submenu {{ (request()->is('admin/interest*')) ? 'kt-menu__item--open kt-menu__item--here' : '' }}" aria-haspopup="true">
	<a href="javascript:;" class="kt-menu__link kt-menu__toggle">
		<span class="kt-menu__link-icon">
			<i class="fas fa-sitemap"></i>
		</span>
		<span class="kt-menu__link-text">{{ __('menu.back.interest') }}</span>
		<i class="kt-menu__ver-arrow la la-angle-right"></i>
	</a>
	<div class="kt-menu__submenu "><span class="kt-menu__arrow"></span>
		<ul class="kt-menu__subnav">
			<li class="kt-menu__item {{ (request()->is('admin/interest/all', 'admin/interest/*/edit')) ? 'kt-menu__item--active' : '' }}" aria-haspopup="true">
				<a href="{{ route('admin.interest.index', $org_slug) }}" class="kt-menu__link ">
					<i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i>
					<span class="kt-menu__link-text sub-menu">{{ __('menu.back.view_all') }}</span>
				</a>
			</li>
			<li class="kt-menu__item {{ (request()->is('admin/interest/create*')) ? 'kt-menu__item--active' : '' }}" aria-haspopup="true">
				<a href="{{ route('admin.interest.create', $org_slug) }}" class="kt-menu__link ">
					<i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i>
					<span class="kt-menu__link-text sub-menu">{{ __('menu.back.add_new') }}</span>
				</a>
			</li>
		</ul>
	</div>
</li>
@endif
@endif




