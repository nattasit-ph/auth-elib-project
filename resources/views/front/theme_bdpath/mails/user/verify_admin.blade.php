<p>Dear, {{ $result->name }}</p>
<p>Thank you for signing up to {{ config('bookdose.app.name') }}</p>
<p>Your account is now pending for activation. Please wait for an administrator to activate your account.</p>
<p>
	After activation is completed, you can use your registered email ({{ $result->email }}) and password to access your account at <a href="{{ route('login', $result->org->slug) }}">{{ route('login', $result->org->slug) }}</a>
</p>
