@extends('front.'.config('bookdose.theme_front').'.mails.tpl_email')

{{-- @dd('resources\views\front\theme_bdpath\mails\user\verify.blade.php', $result); --}}
@section('content')
<p>{{__('mail.verify.thank_you').__('common.default_page_title') }}</p>
<p>{{ __('mail.verify.pending_activation') }} :</p>
<div class="text-center mt mb">
    <a href="{{ route('verify', ['org_slug'=>$result->org->slug, 'email'=>$result->email, 'token'=>md5($result->created_at)]) }}" class="button button-primary text-center" target="_blank" rel="noopener">{{ __('mail.verify.btn_verify') }}</a>
</div>
<p>
{{ __('mail.verify.after_activation_prefix') }} ({{ $result->email }}) {{ __('mail.verify.after_activation_postfix') }} <a href="{{ route('login', $result->org->slug) }}">{{ route('login', $result->org->slug) }}</a>
</p>
{{-- @dd($result) --}}
@endsection
