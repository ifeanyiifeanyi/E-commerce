<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Mail\VendorApproved;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
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

    public function createVendor()
    {
        return view('admin.vendors.create');
    }


    public function storeVendor(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'password' => 'required|string|min:8|confirmed',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Process photo if uploaded
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('vendor-photos', 'public');
        }

        // Create new vendor
        $vendor = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'photo' => $photoPath,
            'role' => 'vendor',
            'status' => 'active',
            'password' => bcrypt($request->password),
        ]);

        // Send verification email if requested
        if ($request->has('send_verification_email') && $request->send_verification_email) {
            // You'll need to implement this - could use Laravel's built-in verification
            $vendor->sendEmailVerificationNotification();
        }

        return redirect()->route('admin.vendors')->with('success', 'Vendor created successfully.');
    }

    public function editVendor(User $user)
    {
        if ($user->role !== 'vendor') {
            return back()->with('error', 'User is not a vendor.');
        }
        return view('admin.vendors.edit', compact('user'));
    }

    public function showVendor(User $user)
    {
        if ($user->role !== 'vendor') {
            return back()->with('error', 'User is not a vendor.');
        }

        // You could load additional related data here if needed
        // For example: $user->load('orders', 'products');

        return view('admin.vendors.show', compact('user'));
    }

    public function updateVendor(Request $request, User $user)
    {
        if ($user->role !== 'vendor') {
            return back()->with('error', 'User is not a vendor.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = [
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'status' => 'active',
        ];

        // Process photo if uploaded
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
            }

            $data['photo'] = $request->file('photo')->store('vendor-photos', 'public');
        }

        // Update password if provided
        if ($request->filled('password')) {
            $request->validate([
                'password' => 'required|string|min:8|confirmed',
            ]);

            $data['password'] = bcrypt($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.vendors')->with('success', 'Vendor updated successfully.');
    }

    public function deleteVendor(User $user)
    {
        $user->delete();
        return redirect()->back()->with('success', '');
    }
}
