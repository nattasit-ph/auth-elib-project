@extends('auth.'.config('bookdose.theme_login').'.tpl_login')

@section('content')

<div class="container-fluid" style="height: 100vh;">
    <div class="row justify-content-center h-100">
        <div class="col position-relative">
            <div class="row d-flex justify-content-center align-items-center h-100">

                <div class="col-9 col-sm-8 col-md-7 col-lg-8 col-xl-7" style="z-index: 1;">
					<div class="d-flex justify-content-{{ (is_blank($org_slug) ? 'left' : 'center') }}">
                        <!-- image -->
                        <div class="align-left">
                            <img @if (is_blank($org_slug)) style="max-height: 70px; max-width: 100%; width: auto; height: auto;" @endif src="{{ getOrgLogo($org_slug) }}" onerror="this.src='{{ asset(config('bookdose.app.project').'/images/logo/logo.png') }}'">
                        </div>
					</div>

					<!-- Form -->
					<form id="frm_login" class="mb-6" method="POST" action="{{ route('login', $org_slug) }}">
						@csrf

                        @if (is_blank($org_slug))
                            <h1 class="mb-0 text-auth">{{ __('auth.login.welcome_back') }}</h1>
                            <small class="text-auth">เริ่มต้นใช้งาน กรุณากรอกอีเมล์เพื่อตรวจสอบความเป็นสมาชิก</small>
                            <div class="form-group">
                                <label class="sr-only" for="email">
                                    Your {{ config('bookdose.login_auth_field_placeholder') }}
                                </label>
                                <div>
                                    <input id="{{ config('bookdose.login_auth_using_field') }}" type="text" class="form-control @error(config('bookdose.login_auth_using_field')) is-invalid @enderror @if($errors->any() || session()->get('error')) is-invalid @endif @if(session()->get('success')) is-valid @endif" name="{{ config('bookdose.login_auth_using_field') }}" value="{{ old(config('bookdose.login_auth_using_field')) }}" placeholder="{{ config('bookdose.login_auth_field_placeholder') }}" required autocomplete="{{ config('bookdose.login_auth_using_field') }}" autofocus>

                                    {{-- ------------------------------- [ Response ] --------------------------------------- --}}
                                    @if($errors->any() || session()->get('error'))
                                        <div class="alert alert-danger alert-bold alert-dismissible rounded-pill text-center mt-2 p-1" role="alert" dismissable="true">
                                            <div class="alert-text">{!! $errors->any() ? implode('', $errors->all('<div>:message</div>')) : session()->get('error') !!}</div>
                                            <div class="alert-close">
                                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                    <span aria-hidden="true"><i class="la la-close"></i></span>
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                    @error(config('bookdose.login_auth_using_field'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                    @if(session()->get('success'))
                                        <div class="alert alert-success alert-bold alert-dismissible rounded-pill text-center mt-2 p-1" role="alert" dismissable="true">
                                        <div class="alert-text">{!! session()->get('success') !!}</div>
                                        <div class="alert-close">
                                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                    <span aria-hidden="true"><i class="la la-close"></i></span>
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                    {{-- ------------------------------- [ Response ] --------------------------------------- --}}
                                </div>
                            </div>
                            @if (session()->get('orgs'))
                                <div class="form-group">
                                    <label>เข้าสู่ระบบห้องสมุดที่คุณต้องการ</label>
                                    <ul class="list-group list-group-flush ps-3 border border-gray rounded-3">
                                        @foreach (session()->get('orgs') as $org)
                                        <a href="javascript:go_to_login('{{ $org->slug }}');" class="list-group-item list-group-item-action border-dashed ps-2 pe-2">{{ $org->{ 'name_' . app()->getLocale() } }}</a>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        @else
                            @if($errors->any())
                                <div class="alert alert-danger alert-bold alert-dismissible rounded-pill text-center mt-2 p-1" role="alert" dismissable="true">
                                    <div class="alert-text">{!! implode('', $errors->all('<div>:message</div>')) !!}</div>
                                    <div class="alert-close">
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true"><i class="la la-close"></i></span>
                                        </button>
                                    </div>
                                </div>
                            @endif
						    @if (config('bookdose.login_adldap') === true)
							<div class="form-group">
								<label class="sr-only" for="username">
									{{ config('bookdose.ldap.login_username_placeholder') }}
								</label>
								<div>
									<input id="username" type="text" class="form-control @error('username') is-invalid @enderror" name="username" value="{{ old('username') }}" placeholder="{{ config('bookdose.ldap.login_username_placeholder') }}" required autocomplete="username" autofocus>

									@error('username')
									<span class="invalid-feedback" role="alert">
										<strong>{{ $message }}</strong>
									</span>
									@enderror
								</div>
							</div>
						    @else
                            <h1 class="mb-0 text-auth text-center">{{ __('auth.login.label') }}</h1>
                            @if ($user_org)
                            <div class="d-flex justify-content-center">
                                <p class="text-muted">
                                    {{ $user_org->{'name_' . app()->getLocale()} }}
                                </p>
                            </div>
                            @endif

							<div class="form-group">
								<label class="sr-only" for="email">
									Your {{ config('bookdose.login_auth_field_placeholder') }}
								</label>
								<div>
									<input id="{{ config('bookdose.login_auth_using_field') }}" type="text" class="form-control @error(config('bookdose.login_auth_using_field')) is-invalid @enderror" name="{{ config('bookdose.login_auth_using_field') }}" value="{{ old(config('bookdose.login_auth_using_field')) }}" placeholder="{{ config('bookdose.login_auth_field_placeholder') }}" required autocomplete="{{ config('bookdose.login_auth_using_field') }}" autofocus>

									@error(config('bookdose.login_auth_using_field'))
									<span class="invalid-feedback" role="alert">
										<strong>{{ $message }}</strong>
									</span>
									@enderror
								</div>
							</div>
						    @endif {{-- ELSE-IF (config('bookdose.login_adldap') === true) --}}

                            <!-- Password -->
                            <div class="form-group">
                                <div class="d-flex">
                                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="Password">
                                    <i class="far fa-eye" id="togglePassword" style="cursor: pointer; margin-top: 15px; margin-left: -30px; z-index: 100; @error('password') color: var(--bs-danger) @enderror" onclick="togglePassword(this)"></i>
                                </div>
                                @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        @endif

						<!-- Button -->
						<button id="btn_login" type="submit" class="btn btn-block btn-auth-primary text-white lh-2">
							{{ __('auth.login.btn_submit') }}
						</button>
					</form>
                    @if ($user_org)
                    <div class="d-flex justify-content-center">
                        <p class="text-muted">
                            <a href="{{ route('login') }}" class="text-link-primary">กลับไปยังหน้าตรวจสอบ email</a>
                        </p>
                    </div>
                    @endif


					<!-- Link -->
					@if (!is_blank($org_slug) && Route::has('password.request'))
                        @if (!config('bookdose.app.forgot.password') && !is_number_no_zero($setting_forgot->data_value['forgot_password']??'0'))
                        <span class="fs-6 text-danger" style="font-size: 0.8em !important">(หากคุณต้องการแจ้ง reset password ให้ติดต่อ Admin ขององค์กรเท่านั้น)</span>
                        @endif

						<div class="d-flex justify-content-between">
							<div class="form-check">
								<input class="form-check-input" type="checkbox" value="" id="remember">
								<label class="form-check-label" for="remember">
									{{ __('auth.login.remember_me') }}
								</label>
							</div>
                            @if (config('bookdose.app.forgot.password') || is_number_no_zero($setting_forgot->data_value['forgot_password']??'0'))
							<a class="text-decoration-none text-link-primary" href="{{ route('password.request', $org_slug) }}">
                                {{ __('auth.login.forgot_password') }}?
							</a>
                            @endif
						</div>
					@elseif (!is_blank($org_slug) && !empty(config('bookdose.app.url_forgot_password')))
						<div class="pt-4 d-flex justify-content-between">
							<a href="{{ config('bookdose.app.url_forgot_password') }}" class="font-size-sm text-link-primary">
								{{ __('auth.login.forgot_password') }}?
							</a>
						</div>
					@endif

					<!-- register -->
					@if ((!is_blank($org_slug) && Route::has('register') && is_number_no_zero($setting_regis->data_value['regis_online']??'0')) || (is_blank($org_slug) && session()->get('register')))
					<hr class="my-3">
					<div class="d-flex justify-content-center">
						<p class="text-muted">
							ยังไม่มีบัญชีใช่ไหม? <a href="{{ route('register', (!is_blank($org_slug) ? $org_slug : config('bookdose.default.org_slug'))) }}" class="text-link-primary">สมัครสมาชิกเลย</a>
						</p>
					</div>
					@endif



                </div>

            </div>
        </div>

        <!-- image -->
        <div class="col-lg-6 col-xl-7 d-none d-lg-flex justify-content-center login-blackguard" style="background-image: url({{ getLoginCoverImage($banner->file_path ?? '') }}); background-size: cover;">
            {{-- {!! getLogo('align-self-center', 'height:'.config('bookdose.app.logo_login_height')) !!} --}}
        </div>

    </div>
</div>
@endsection

@push('additional_js')
<script type="text/javascript">
$('#btn_login').click(function() {
	@if (config('bookdose.login_adldap') === true)
		$.ajax({
			dataType: "json",
			url: "{{ route('ajaxSetLdapConfig') }}",
			data: { username: $('#username').val() },
			success: function(data) {
				 $('#frm_login').submit();
			}
		});
	@else
		$('#frm_login').submit();
	@endif
});
function go_to_login(slug) {
    $('#frm_login').attr('action', '{{ route('site-login', ':slug') }}'.replace(':slug', slug)).submit();
}
function togglePassword(e)
{
    if ($('#password').attr('type') == 'text') {
        $('#password').attr('type', 'password');
        $(e).removeClass('fa-eye-slash').addClass('fa-eye');
    }
    else {
        $('#password').attr('type', 'text');
        $(e).addClass('fa-eye-slash').removeClass('fa-eye');
    }
}
@if(session()->get('register') && session()->get('success'))
    Swal.fire({
        type: 'success',
        title: '<strong class="text-success">Successful</strong>',
        html: '{!! session()->get('success') !!}',
        allowOutsideClick: false,
        customClass: {
            confirmButton: 'btn btn-primary text-white',
        },
        confirmButtonText: 'เข้าใจแล้ว',
    })
@endif
</script>
@endpush
