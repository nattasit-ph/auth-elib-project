@extends('auth.'.config('bookdose.theme_login').'.tpl_login')

@section('content')
<div class="container-fluid" style="height: 100vh;">
    <div class="row justify-content-center h-100">
        <div class="col position-relative">
            <div class="row d-flex justify-content-center align-items-center h-100">
				<div class="col-8">
					<div class="d-flex justify-content-left">
                        <!-- image -->
                        <div class="align-left">
                            <img style="max-height: 70px;" src="{{ getOrgLogo($org_slug) }}" onerror="this.src='{{ asset(config('bookdose.app.project').'/images/logo/logo.png') }}'">
                        </div>
					</div>
                    <form method="POST" action="{{ route('register', $org_slug) }}">
                        @csrf

                        <div class="d-flex my-3 text-auth font-weight-bold" style="font-size: 2rem;">
                            {{ __('auth.login.register') }}
                        </div>

                        @if($errors->any() || session()->get('error'))
                            {{-- <div class="text-danger mb-5">{{ ($errors->any() ? $errors->first() : session()->get('error')) }}</div> --}}
                            <div class="alert alert-danger alert-bold alert-dismissible rounded-pill text-center mt-2 p-1" role="alert" dismissable="true">
                                <div class="alert-text">{!! $errors->any() ? implode('', $errors->all('<div>:message</div>')) : session()->get('error') !!}</div>
                                <div class="alert-close">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true"><i class="la la-close"></i></span>
                                    </button>
                                </div>
                            </div>
                        @endif

                        <div class="form-group">
                            <div class="">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror input-regster" name="email" value="{{ old('email') }}" placeholder="{{ __('auth.register.email_address') }}" required autocomplete="email">
                                @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror input-regster" name="password" placeholder="{{ __('auth.register.password') }}" required autocomplete="new-password">
                                <div class="mt-3 text-danger fw-6 lh-1">
                                    <small>
                                        <ul>
                                            <li>{{ __('auth.register.password_format_1', ['number' => '4-13']) }}</li>
                                            <li>{{ __('auth.register.password_format_2') }}</li>
                                        </ul>
                                    </small>
                                </div>
                                @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="">
                                <input id="password-confirm" type="password" class="form-control input-regster" name="password_confirmation" placeholder="{{ __('auth.register.re_enter_password') }}" required autocomplete="new-password">
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="">
                                <input id="f_name" type="text" class="form-control @error('f_name') is-invalid @enderror input-regster" name="f_name" value="{{ old('f_name') }}" placeholder="{{ __('auth.register.first_name') }}" required autocomplete="f_name">
                                @error('f_name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="">
                                <input id="l_name" type="text" class="form-control @error('l_name') is-invalid @enderror input-regster" name="l_name" value="{{ old('l_name') }}" placeholder="{{ __('auth.register.last_name') }}" required autocomplete="l_name">
                                @error('l_name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        {{-- <div class="form-group">
                            <div class="">
                                <input id="department" type="text" class="form-control @error('department') is-invalid @enderror input-regster" name="department" value="{{ old('department') }}" placeholder="Department (Optional)"  autocomplete="department">
                                @error('department')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div> --}}

                        <div class="form-group">
                            <div class="">
                                <input id="contact_number" type="text" class="form-control @error('contact_number') is-invalid @enderror input-regster" name="contact_number" value="{{ old('contact_number') }}" placeholder="{{ __('auth.register.phone') }}" autocomplete="contact_number">
                                @error('contact_number')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        {{-- <div class="form-group">
                        <select class="form-select form-select-lg mb-3 form-control input-regster" name="gender" style="min-height: 50px">
                            <option value="" selected disabled>Gender (Optional)</option>
                            <option value="m">Male</option>
                            <option value="f">Female</option>
                        </select>
                        </div> --}}


                        @if(config('bookdose.app.reCaptcha'))
                        <div class="d-flex justify-content-center"> {!! htmlFormSnippet() !!} </div>
                        @endif

                        <!-- btn register -->
                        <div class="d-flex justify-content-center">
                            <button type="submit" class="btn btn-auth-primary btn-block">
                                {{ __('auth.login.register') }}
                            </button>
                        </div>
                        <div class="d-flex justify-content-center mb-5 mt-2">
                            เป็นสมาชิกแล้ว?<a href="{{ route('login', $org_slug) }}" class="text-link-primary pl-1"> เข้าสู่ระบบ</a>
                        </div>

                    </form>
                </div>
            </div>
        </div>

        <!-- image -->
        <div class="col-lg-6 col-xl-7 d-none d-lg-flex justify-content-center login-blackguard" style="background-image: url({{ getLoginCoverImage($banner->file_path ?? '') }});background-size: cover;">
            {{-- {!! getLogo('align-self-center', 'height:'.config('bookdose.app.logo_login_height')) !!} --}}
        </div>

    </div>
</div>
@endsection
