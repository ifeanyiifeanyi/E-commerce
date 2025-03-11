<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Mail\VendorApproved;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;

class VendorManagementController extends Controller
{
    public function index()
    {
        $vendors = User::where('role', 'vendor')->latest()->paginate(10);

        return view('admin.vendors.index', compact('vendors'));
    }

    public function approveVendor(User $user)
    {
        if ($user->role !== 'vendor') {
            return back()->with('error', 'User is not a vendor.');
        }

        $user->update(['status' => 'active']);

        // Send vendor approval email
        Mail::to($user->email)->send(new VendorApproved($user));

        return back()->with('success', 'Vendor has been approved successfully.');
    }


    /**
     * Deactivate a vendor.
     */
    public function deactivateVendor(User $user)
    {
        if ($user->role !== 'vendor') {
            return back()->with('error', 'User is not a vendor.');
        }

        $user->update(['status' => 'inactive']);

        return back()->with('success', 'Vendor has been deactivated successfully.');
    }
}
