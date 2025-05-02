@extends('layouts.guest')
@section('title', 'Sell on Cinta')
@section('content')
    <div class="auth-container">

        <div class="form-items">
            <h2 class="text-center text-white card-header">{{ __('Account Pending Approval') }}</h2>

            <div class="text-center card-body">
                <div class="mb-4">
                    <img src="{{ asset('clock.gif') }}" alt="Account Pending Approval" class="img-fluid" style="width: 100px; object-fit: cover;">
                </div>
                <h4 class="mb-3 text-light">Your vendor account is pending approval</h4>
                <p class="mb-4" style="line-height: 1.6">
                    Our admin team is reviewing your application. This usually takes 1-2 business days.
                    You'll receive an email notification when your account is approved.
                </p>
                <p class="text-light">
                    If you have any questions, please contact our support team.
                </p>

                <div class="mt-4">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-outline-primary">
                            Log Out
                        </button>
                    </form>
                </div>
            </div>

        </div>


    </div>
@endsection
