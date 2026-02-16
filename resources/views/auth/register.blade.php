@extends('layout')

@section('content')
<div class="panel-head">
    <p class="eyebrow">Create Account</p>
    <h1 class="heading-title">Join the quiz app</h1>
    <p class="heading-sub">Set up your account and start playing right away.</p>
</div>

@if($errors->any())
    <div class="flash error">
        <ul class="error-list">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('register') }}" class="auth-form">
    @csrf

    <label class="input-wrap">
        <span>Username</span>
        <input name="username" value="{{ old('username') }}" placeholder="Your name" autocomplete="username" required>
    </label>

    <label class="input-wrap">
        <span>Email</span>
        <input type="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" autocomplete="email" required>
    </label>

    <label class="input-wrap">
        <span>Password</span>
        <input type="password" name="password" placeholder="Use at least 8 chars with letters and numbers" autocomplete="new-password" required>
    </label>

    <label class="input-wrap">
        <span>Confirm Password</span>
        <input type="password" name="password_confirmation" placeholder="Retype your password" autocomplete="new-password" required>
    </label>

    <button type="submit" class="btn-style full-width" data-loading-text="Creating account...">Register</button>
</form>

<p class="helper-text">
    Already have an account?
    <a href="{{ route('login') }}">Login</a>
</p>
@endsection
