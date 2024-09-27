@extends('auth.'.config('bookdose.theme_login').'.tpl_login')

@section('content')
<div class="container-fluid" style="height: 100vh;">
    <div class="row justify-content-center h-100">

        <div class="col position-relative">
            <div class="row d-flex justify-content-center align-items-center h-100">

                <div class="col-9 col-sm-8 col-md-7 col-lg-8 col-xl-8" style="z-index: 1;">

                    <!-- image -->
                    <div class="mt-5 d-flex justify-content-center">
                        {{-- <div class="img-logo">{!! getLogo('', 'height:'.config('bookdose.app.logo_login_height')) !!}</div> --}}
                        <div class="">
                            <img class="w-100" src="{{ getOrgLogo($org_slug) }}" onerror="this.src='{{ asset(config('bookdose.app.project').'/images/logo/logo.png') }}'">
                        </div>
                    </div>
                    <div class="d-flex justify-content-center mt-2">
                        <a href="{{ route('login', $org_slug) }}" class="text-decoration-none text-link-secondary fs-6 pl-1"> <i class="fas fa-chevron-circle-left fa-xs"></i> Back to login</a>
                    </div>


                    {{-- <p class="text-center mb-5">{{ __('Reset Password') }}</p> --}}
                    <form method="POST" action="{{ route('password.update', $org_slug) }}">
                        @csrf

                        <div class="text-center mb-4">
                            <h1 class="mb-0 text-auth">{{ __('auth.reset_password.title') }}</h1>
                            {{--  <small class="text-auth">{{ __('auth.reset_password.description') }}</small>  --}}
                        </div>
                        <input type="hidden" name="token" value="{{ $token }}">
                        <div class="form-group row">
                            <div class="col-md-12">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required placeholder="{{ __('auth.reset_password.email_address') }}" autocomplete="email" autofocus>

                                {{-- @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror --}}
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-12">
                                <div class="d-flex">
                                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required placeholder="{{ __('auth.reset_password.password') }}" autocomplete="new-password">
                                    <i class="far fa-eye toggle-password" toggle="#password" style="cursor: pointer; margin-top: 15px; margin-left: -30px; z-index: 100; @error('password') color: var(--bs-danger) @enderror"></i>
                                </div>
                                <div class="mt-3 text-danger fw-6 lh-1">
                                    <small>
                                        <ul>
                                            <li>{{ __('auth.reset_password.password_format_1', ['number' => '4-13']) }}</li>
                                            <li>{{ __('auth.reset_password.password_format_2') }}</li>
                                        </ul>
                                    </small>
                                </div>
                                {{-- @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror --}}
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-12">
                                <div class="d-flex">
                                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required placeholder="{{ __('auth.reset_password.re_enter_password') }}" autocomplete="new-password">
                                    <i class="far fa-eye toggle-password" toggle="#password-confirm" style="cursor: pointer; margin-top: 15px; margin-left: -30px; z-index: 100; @error('password') color: var(--bs-danger) @enderror"></i>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-auth-primary lh-2">
                                    {{ __('Reset Password') }}
                                </button>
                            </div>
                        </div>
						@if($errors->any())
                            <div class="alert alert-danger alert-bold alert-dismissible rounded-pill text-center mt-2 p-1" role="alert" dismissable="true">
                                <div class="alert-text">{!! implode('', $errors->all('<div><i class="fas fa-times-circle"></i> :message</div>')) !!}</div>
                                <div class="alert-close">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true"><i class="la la-close"></i></span>
                                    </button>
                                </div>
                            </div>
						@endif
                    </form>


                </div>

            </div>
        </div>


        <!-- image -->
        <div class="col-lg-6 col-xl-7 d-none d-lg-flex justify-content-center login-blackguard" style="background-image: url({{ getLoginCoverImage($banner->file_path ?? '') }});background-size: cover;">

        </div>

    </div>
</div>
@endsection

@push('additional_js')
<script type="text/javascript">
    $(function() {
        //toggle show password
        $(".toggle-password").click(function() {
            $(this).toggleClass("fa-eye fa-eye-slash");
            var input = $($(this).attr("toggle"));
            console.log($(this).attr("toggle"))
            if (input.attr("type") == "password") {
                input.attr("type", "text");
            } else {
                input.attr("type", "password");
            }
        });
    });
</script>
@endpush
