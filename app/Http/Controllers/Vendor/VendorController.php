<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    public function dashboard(){
        return view('vendor.dashboard');
    }


    public function logout(){
        auth()->guard('web')->logout();
        return redirect()->route('login');
    }
}
