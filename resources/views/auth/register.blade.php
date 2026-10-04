@extends('layouts.app')

@section('title', 'Register')

@section('content')
<div class="card card-narrow">
    <h1>Create an account</h1>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus>
            @error('name') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required>
            @error('email') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="role">I want to</label>
            <select id="role" name="role" required>
                <option value="buyer" @selected(old('role', 'buyer') === 'buyer')>Buy items (Buyer)</option>
                <option value="seller" @selected(old('role') === 'seller')>Sell items (Seller)</option>
            </select>
            @error('role') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
            @error('password') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="password_confirmation">Confirm password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required>
        </div>

        <button type="submit" class="btn btn-block">Register</button>
    </form>

    <p class="text-center muted mt">
        Already registered? <a href="{{ route('login') }}">Log in</a>
    </p>
</div>
@endsection