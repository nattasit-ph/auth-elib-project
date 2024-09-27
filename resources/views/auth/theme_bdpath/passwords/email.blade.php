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
                        <div class="img-logo">
                            <img class="w-100" src="{{ getOrgLogo($org_slug) }}" onerror="this.src='{{ asset(config('bookdose.app.project').'/images/logo/logo.png') }}'">
                        </div>
                    </div>
                    <div class="d-flex justify-content-center mt-2">
                        <a href="{{ route('login', $org_slug) }}" class="text-decoration-none text-link-secondary fs-6 pl-1"> <i class="fas fa-chevron-circle-left fa-xs"></i> Back to login</a>
                    </div>


                    {{-- <p class="text-center mb-5">{{ __('Reset Password') }}</p> --}}
                    <form method="POST" action="{{ route('password.email', $org_slug) }}">
                        @csrf

                        <div class="text-center mb-4">
                            <h1 class="mb-0 text-auth">{{ __('auth.reset_password.title') }}</h1>
                            @if (config('bookdose.app.forgot.send_mail'))
                            <small class="text-auth">{{ __('auth.reset_password.description') }}</small>
                            @endif
                        </div>
                        <div class="form-group">
                            <div class="w-100">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required placeholder="{{ __('E-Mail Address') }}" autocomplete="email" autofocus>
                                @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row mb-4">
                            <div class="d-flex justify-content-center w-100">
                                <button type="submit" class="btn btn-block btn-auth-primary lh-2">
                                    {{ __('auth.reset_password.btn_submit') }} <i class="far fa-envelope fa-xs"></i>
                                </button>
                            </div>
                        </div>
                        @if($errors->any())
                            {{-- <div class="text-danger mb-5">
                                {!! implode('', $errors->all('<div>:message</div>')) !!}
                            </div> --}}
                            <div class="alert alert-danger alert-bold alert-dismissible rounded-pill text-center mt-2 p-1" role="alert" dismissable="true">
                                <div class="alert-text">{!! implode('', $errors->all('<div><i class="fas fa-times-circle"></i> :message</div>')) !!}</div>
                                <div class="alert-close">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true"><i class="la la-close"></i></span>
                                    </button>
                                </div>
                            </div>
                        @endif
                        @if (session('status'))
                        {{-- <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div> --}}
                        <div class="alert alert-success alert-bold alert-dismissible rounded-pill text-center mt-2 p-1" role="alert" dismissable="true">
                            <div class="alert-text"><i class="fas fa-check"></i> {!! session('status') !!}</div>
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
