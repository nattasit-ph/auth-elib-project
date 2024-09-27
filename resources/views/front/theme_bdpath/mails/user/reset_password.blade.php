@extends('front.'.config('bookdose.theme_front').'.mails.tpl_email')


@section('content')
<p>{{ __('mail.reset_password.desc') }}</p>
<div class="text-center mt mb">
    <a href="{{ $url ?? '' }}" class="button button-primary text-center" target="_blank" rel="noopener">{{ __('mail.reset_password.btn_reset') }}</a>
</div>
<p>{{ __('mail.reset_password.expire', ['count' => config('auth.passwords.users.expire')]) }}</p>
<p>{{ __('mail.reset_password.not_request') }}</p>
@endsection