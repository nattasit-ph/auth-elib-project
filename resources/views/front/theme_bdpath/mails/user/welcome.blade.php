<p>Dear, {{ $result->name }}</p>
<p>Thank you for signing up to {{ config('bookdose.app.name') }}</p>
<p>
	{{-- @if (config('bookdose.regis.verify_by_admin')) --}}
	@if (($result->regis_verify_by_admin??'0') == '1')
		Your account is now pending for approval. Please wait for activation process. We will let you know when activation is complete. After activation is completed,
	@endif
	You can use your registered email ({{ $result->email }}) and password to access your account at <a href="{{ route('login', $result->org->slug) }}">{{ route('login', $result->org->slug) }}</a>
</p>
