@if ($message = Session::get('success'))
<div class="alert alert-dark" role="alert">
  {!! $message !!}
</div>
@endif

@if ($message = Session::get('error'))
<div class="alert alert-dark" role="alert">
  {!! $message !!}
</div>
@endif

@if ($message = Session::get('warning'))
<div class="alert alert-dark" role="alert">
  {!! $message !!}
</div>
@endif

@if ($message = Session::get('info'))
<div class="alert alert-dark" role="alert">
  {!! $message !!}
</div>
@endif

@if (session('status'))
<div class="alert alert-dark" role="alert">
  {{ session('status') }}
</div>
@endif

@if ($errors->any())
<div class="alert alert-dark" role="alert">
    @foreach ($errors->all() as $error)
        {!! $error !!}<BR />
    @endforeach
</div>
@endif

{{Session::forget('success')}}
{{Session::forget('error')}}
{{Session::forget('warning')}}
{{Session::forget('info')}}
{{Session::forget('status')}}
