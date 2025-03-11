@extends('layouts.guest')

@section('title', 'Register')
@section('auth-content')
    <div class="form-holder">
        <div class="form-content">
            <div class="form-items">
                <h3>Get more things done with Loggin platform.</h3>
                <p>Access to the most powerfull tool in the entire design and web industry.</p>
                <div class="page-links">
                    <a href="{{ route('login') }}">Login</a><a href="{{ route('register') }}" class="active">Register</a>
                </div>

                
                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    <div class="mb-3 form-group">
                        <input class="form-control" type="text" name="name" placeholder="Full Name" required
                            value="{{ old('name') }}">
                        @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <input class="form-control" type="text" name="name" placeholder="Username" required
                        value="{{ old('username') }}">
                    <input class="form-control" type="email" name="email" placeholder="E-mail Address" required
                        value="{{ old('email') }}">
                    <input class="form-control" type="password" name="password" placeholder="Password" required>
                    <input class="form-control" type="password" name="password_confirmation" placeholder="Confirm Password"
                        required>

                    <div class="form-button">
                        <button id="submit" type="submit" class="ibtn">Register</button>
                    </div>
                </form>


                <div class="other-links">
                    <span>Or register with</span><a href="#">Facebook</a><a href="#">Google</a><a
                        href="#">Linkedin</a>
                </div>
            </div>
        </div>
    </div>

@endsection
