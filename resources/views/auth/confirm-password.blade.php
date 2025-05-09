@extends('layouts.guest')

@section('title', 'Confirm Password')
@section('content')
    <div class="auth-container">

        <div class="mb-4 text-sm text-muted">
            This is a secure area of the application. Please confirm your password before continuing.
        </div>

        <form method="POST" action="{{ route('password.confirm') }}">
            @csrf

            <!-- Password -->
            <div class="form-group">
                <label for="password">Password</label>
                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                    name="password" required autocomplete="current-password">
                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="flex justify-end mt-4">
                <x-primary-button>
                    Confirm
                </x-primary-button>
            </div>
        </form>
    </div>
@endsection
