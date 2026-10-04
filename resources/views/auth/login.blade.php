@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="card card-narrow">
    <h1>Log in</h1>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
            @error('email') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>

        <div class="form-group">
            <label class="checkbox">
                <input type="checkbox" name="remember"> Remember me
            </label>
        </div>

        <button type="submit" class="btn btn-block">Log in</button>
    </form>

    <p class="text-center muted mt">
        No account? <a href="{{ route('register') }}">Register</a>
    </p>
</div>
@endsection