@extends('layout')

@section('content')
<div class="panel-head">
    <p class="eyebrow">Welcome Back</p>
    <h1 class="heading-title">Log in to continue</h1>
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

<form method="POST" action="{{ route('login') }}" class="auth-form">
    @csrf

    <label class="input-wrap">
        <span>Email</span>
        <input type="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" autocomplete="email" required>
    </label>

    <label class="input-wrap">
        <span>Password</span>
        <input type="password" name="password" placeholder="Enter your password" autocomplete="current-password" required>
    </label>

    <button type="submit" class="btn-style full-width" data-loading-text="Signing in...">Login</button>
</form>

<p class="helper-text">
    New here?
    <a href="{{ route('register') }}">Create an account</a>
</p>
@endsection
