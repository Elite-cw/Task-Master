@extends('layouts.app')
@section('content')
<section class="auth-shell">
    <div class="auth-intro"><div><p class="eyebrow">Task Master</p><h1>Make space for work that matters.</h1><p>One calm workspace for your projects, priorities, and next steps.</p></div><div class="feature-list"><span>Organize projects with clarity</span><span>Keep every task moving forward</span><span>Private by design</span></div></div>
    <div class="auth-form"><h2>Welcome back</h2><p class="meta">Sign in to continue to your workspace.</p>
    <form method="POST" action="{{ route('login') }}">@csrf
    <label>Email address</label><input type="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" required autofocus>
    <label>Password</label><div class="password-field"><input id="password" type="password" name="password" placeholder="Enter your password" required><button class="password-toggle" type="button" data-toggle="password" aria-label="Show password" title="Show password"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg></button></div>
    <label style="font-weight:normal"><input style="width:auto" type="checkbox" name="remember"> Keep me signed in</label>
    <button class="btn">Sign in</button>
    </form><span class="auth-link">New to Task Master? <a href="{{ route('register') }}">Create an account</a></span></div>
</section>
<script>const eye='<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>', eyeOff='<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m3 3 18 18"/><path d="M10.6 5.2A10.8 10.8 0 0 1 12 5c6.5 0 10 7 10 7a18.5 18.5 0 0 1-3 4.1M6.2 6.2C3.6 8 2 12 2 12s3.5 7 10 7a9.8 9.8 0 0 0 3.6-.7"/><path d="M9.9 9.9a3 3 0 0 0 4.2 4.2"/></svg>';document.querySelectorAll('[data-toggle]').forEach(button => button.addEventListener('click', () => { const field=document.getElementById(button.dataset.toggle), visible=field.type==='password'; field.type=visible?'text':'password'; button.classList.toggle('is-visible',visible); button.innerHTML=visible?eyeOff:eye; const label=visible?'Hide password':'Show password'; button.setAttribute('aria-label',label); button.title=label; }));</script>
@endsection
