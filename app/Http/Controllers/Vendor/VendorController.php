<?php

namespace App\Http\Controllers\Vendor;

use view;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class VendorController extends Controller
{
    public function dashboard(){
        return view('vendor.dashboard');
    }


    public function logout(Request $request){
        auth()->guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
