@extends('layouts.guest')

@section('title', 'Verify Email Address')
@section('content')
    <div class="auth-container">

        <div class="form-items">
            <div class="mb-4 text-sm text-muted">
                Thanks for signing up! Before getting started, could you verify your email address by clicking on the link
                we just emailed to you? If you didn't receive the email, we will gladly send you another.
            </div>

            @if (session('status') == 'verification-link-sent')
                <div class="mb-4 text-success">
                    A new verification link has been sent to the email address you provided during registration.
                </div>
            @endif

            <div class="flex justify-between items-center mt-4">
                <form method="POST" action="{{ route('verification.send') }}" style="display: inline-block">
                    @csrf

                    <div>
                        <x-primary-button>
                         <div class="fas fa-envelope"></div>   Resend Verification Email
                        </x-primary-button>
                    </div>
                </form>

                <form method="POST" action="{{ route('logout') }}" style="display: inline-block">
                    @csrf

                    <button type="submit" class="btn btn-warning">
                       <div class="fas fa-sign-out-alt"></div> Log Out
                    </button>
                </form>
            </div>
        </div>

    </div>


@endsection
