@extends('layouts.vendor')

@section('title', 'Register As Vendor')

@section('vendor-content')
    <div class="form-holder">
        <div class="form-content">
            <div class="form-items">
                <h2 class="text-center text-white card-header">{{ __('Account Pending Approval') }}</h2>

                <div class="text-center card-body">
                    <div class="mb-4">
                        <i class="fas fa-clock fa-4x text-warning"></i>
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
    </div>
@endsection
