@extends('layouts.guest')

@section('title', 'Login')
@section('auth-content')
    <div class="form-holder">
        <div class="form-content">
            <div class="form-items">
                <h3>Get more things done with Loggin platform.</h3>
                @if(session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                <div class="page-links">
                    <a href="{{ route('login') }}" class="active">Login</a><a href="{{ route('register') }}">Register</a>
                </div>

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-3 form-group">
                        <input class="form-control @error('email') is-invalid border-danger @enderror" type="email" name="email"
                            value="{{ old('email') }}" autocomplete="username" autofocus placeholder="E-mail Address">

                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-4 form-group">
                        <input class="form-control @error('password') is-invalid borde-danger @enderror" type="password" name="password"
                            autocomplete="current-password" placeholder="Password" >
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <input type="checkbox" id="chk1" name="remember"><label for="chk1">Remmeber me</label>
                    <div class="form-button">
                        <button id="submit" type="submit" class="ibtn">Login</button>

                        
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}">Forget password?</a>
                        @endif
                    </div>
                </form>
                <div class="other-links">
                    <span>Or login with</span><a href="#">Facebook</a><a href="#">Google</a><a
                        href="#">Linkedin</a>
                </div>
            </div>
        </div>
    </div>
@endsection
