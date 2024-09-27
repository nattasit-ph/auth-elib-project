<?php
$filename = 'version.txt';
$vs       = '';
if (file_exists($filename)) {
	$handle = fopen($filename, "r");
	$vs     = fread($handle, filesize($filename));
	fclose($handle);
	$txt_version = "version " . $vs;
}
?>

<footer class="py-4 py-md-5">
	<div class="container-lg">
		<div class="row">
			<div class="col-12 col-md-2 text-center text-md-start mb-5 mb-md-0">
				<a class="navbar-brand" href="{{ url('home') }}">
					<img src="{{ asset('/auth/'.config('bookdose.theme_front').'/img/goodkit/footer/logo.svg') }}" class="img-fluid">
				</a>
			</div>

			@isset($footer)
				<div class="col-6 col-md-3">
					<!-- Heading -->
					<h3 class="text-white">
						{{ $footer->{'name_'.app()->getLocale()} }}
					</h3>
					<div class="text-white">
						{{ $footer->{'address_'.app()->getLocale()} }}
					</div>
					<div class="text-white">
						{{__('common.phone')}} : {{$footer->phone ?? ''}}
					</div>
				</div>

				<div class="col-6 col-md-3">
					<!-- Heading -->
					<h3 class="text-white">
						Resources / ช่วยเหลือ
					</h3>

					<!-- List -->
					<ul class="list-unstyled">
						<li>
						<a class="text-white" href="{{ config('bookdose.sso.auth_url') }}/reward">{{ __('menu.back.reward') }}</a>
					</li>
					<!-- user manual -->
					{{-- <li style="margin-left: -4px;">
						<select onchange="window.open(this.options[this.selectedIndex].value,'_blank')" name="website"
						class="user-manual text-white cursor-pointer" style="border: 0; background-color: #00A99D;">
							<option value="" selected="selected" disabled>{{__('menu.front.user_manual') }}</option>
							@auth
								@if (Auth::user()->hasAnyRole(['Super Admin Belib', 'Admin Belib']))
									<option value="{{ asset('client/gpo/download/GPO_Maread_คู่มือผู้ดูแลระบบ(System Admin).pdf') }}">{{__('home.minimal.library_system')}}</option>
								@else
									<option value="{{ asset('client/gpo/download/GPO_MaRead_คู่มือผู้ใช้งาน(User Admin).pdf') }}">{{__('home.minimal.library_system')}}</option>
								@endif
							@else
								<option value="{{ asset('client/gpo/download/GPO_MaRead_คู่มือผู้ใช้งาน(User Admin).pdf') }}">{{__('home.minimal.library_system')}}</option>
							@endauth
								<option value="{{ asset('client/gpo/download/FORGOT_GPO_Maled_MaRead.pdf') }}">{{__('home.minimal.change_password')}}</option>
						</select>
					</li> --}}
					<li>
						<a target="_blank" href="{{ url('belib/privacy-and-policy') }}" class="text-white">{{__('common.common_privacy')}}</a>
					</li>
					<li>
						<a target="_blank" href="{{ url('belib/terms-and-conditions') }}" class="text-white">{{__('common.common_terms')}}</a>
					</li>
					</ul>
				</div>

				<div class="col-12 col-md-4 text-center text-md-start">
					<div class="d-block d-lg-none mb-2 mt-4 mt-md-0">
                        @if(count($footer->questionBelib) > 0)
						<a class="text-white px-4 py-2 rounded bg-secondary" href="{{ route('questionnaire.form', $footer->questionBelib[0]->slug)}}">{{ __('common.questionnaire') }} <span><img src="{{ asset('/front/'.config('bookdose.theme_front').'/img/goodkit/footer/feedback.svg') }}" class="img-fluid"></span></a>
                        @endif
                    </div>
					<h3 class="text-center text-white mb-4">
						{{__('common.download_app')}}
					</h3>
					<div class="row d-flex justify-content-center mb-4">
						<div class="col-6 col-md-12 col-lg-6 text-end text-md-center text-lg-end mb-0 mb-md-2 mb-lg-0">
							<a href="{{ config('bookdose.app.ios_path') ?? 'javascript:;' }}" target="_blank"><img src="{{ asset('/auth/'.config('bookdose.theme_front').'/img/goodkit/footer/app-store.svg') }}" class="img-fluid"></a>
						</div>
						<div class="col-6 col-md-12 col-lg-6 text-start text-md-center text-lg-start mb-0 mb-md-2 mb-lg-0">
							<a href="{{ config('bookdose.app.android_path') ?? 'javascript:;' }}" target="_blank"><img src="{{ asset('/auth/'.config('bookdose.theme_front').'/img/goodkit/footer/google-play.svg') }}" class="img-fluid"></a>
						</div>
					</div>

					@isset($footer->data_contact)
					<div class="social text-center">
						<a class="ico-facebook me-2" href="{{ $footer->data_contact['facebook'] ? $footer->data_contact['facebook'] : 'javascript:;'}}" title="Facebook" target="_blank"><i class="fab fa-facebook-f"></i></a>
						{{-- <a class="ico-twitter" href="{{ $footer->data_contact['twitter'] ? $footer->data_contact['twitter'] : 'javascript:;'}}" title="Twitter" target="_blank"><i class="fab fa-twitter"></i></a> --}}
						{{-- <a class="ico-youtube" href="{{ $footer->data_contact['youtube'] ? $footer->data_contact['youtube'] : 'javascript:;'}}" title="Youtube" target="_blank"><i class="fab fa-youtube"></i></a> --}}
						{{-- <a class="ico-line" href="{{ $footer->data_contact['line'] ? $footer->data_contact['line'] : 'javascript:;'}}" title="Line" target="_blank"><i class="fab fa-line"></i></a> --}}
						{{-- <a class="ico-instagram" href="{{ $footer->data_contact['instagram'] ? $footer->data_contact['instagram'] : 'javascript:;'}}" title="Instagram" target="_blank"><i class="fab fa-instagram"></i></a> --}}
						<a class="ico-mail" href="{{ $footer->contact_email ? "mailto:".$footer->contact_email : 'javascript:;'}}" title="E-mail" target="_blank"><i class="fa fa-envelope"></i></a>
					</div>
					@endisset

				</div>
			@endisset
		</div>
	</div>
</footer>
<div class="bg-tertiary" style="height:27px;">
	<div class="text-center text-white" style="font-size:0.9em; opacity: 0.5;">
		<?=(!empty($vs) ? $txt_version . shell_exec("git log -1 --pretty=format:'%h'") : '')?>
    </div>
</div>
