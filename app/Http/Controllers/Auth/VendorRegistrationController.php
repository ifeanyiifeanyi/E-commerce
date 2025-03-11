<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;
use App\Services\VendorRegistrationService;
use App\Http\Requests\VendorRegisterRequest;

class VendorRegistrationController extends Controller
{
    public function create()
    {
        return view('auth.vendor-register');
    }

    public function login()
    {

        return view('auth.vendor-login');
    }

    public function store(VendorRegisterRequest $request)
{
    $data = $request->validated();
    $user = (new VendorRegistrationService)->register($data);
    event(new Registered($user));
    Auth::login($user);
    return redirect()->route('verification.notice');
}
}
