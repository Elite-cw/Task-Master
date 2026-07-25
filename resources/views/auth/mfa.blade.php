@extends('layouts.app')
@section('content')
<div class="card" style="max-width:460px;margin:52px auto"><p class="eyebrow" style="color:#6967cc">Security check</p><h1>Enter your code</h1><p class="meta">We sent a six-digit code to your verified email. It expires in 10 minutes.</p><form method="POST" action="{{ route('mfa.verify') }}">@csrf<label>Security code</label><input name="code" inputmode="numeric" autocomplete="one-time-code" pattern="[0-9]{6}" maxlength="6" placeholder="123456" required autofocus><div class="actions"><button class="btn">Verify and continue</button></div></form><form method="POST" action="{{ route('mfa.resend') }}">@csrf<div class="actions"><button class="btn small secondary">Resend code</button></div></form></div>
@endsection
