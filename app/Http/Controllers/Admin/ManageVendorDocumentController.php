<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\VendorDocument;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class ManageVendorDocumentController extends Controller
{
    public function index(User $user)
    {
        if ($user->role !== 'vendor') {
            return back()->with('error', 'User is not a vendor.');
        }
        $user->load('documents');
        return view('admin.vendors.documents.index', compact('user'));
    }
    public function create(User $user)
    {
        if ($user->role !== 'vendor') {
            return back()->with('error', 'User is not a vendor.');
        }

        // List of Nigerian business document types
        $documentTypes = [
            'CAC Certificate' => 'Corporate Affairs Commission (CAC) Registration Certificate',
            'TIN Certificate' => 'Tax Identification Number (TIN) Certificate',
            'BVN' => 'Bank Verification Number (BVN)',
            'NIN' => 'National Identification Number (NIN)',
            'Business Permit' => 'Local Government Business Permit',
            'VAT Certificate' => 'Value Added Tax (VAT) Certificate',
            'NAFDAC Registration' => 'NAFDAC Registration (for food/drug/cosmetics vendors)',
            'SON Certificate' => 'Standards Organization of Nigeria (SON) Certificate',
            'SCUML Registration' => 'Special Control Unit Against Money Laundering (SCUML) Registration',
            'ID Card' => 'Government-issued ID Card (Driver\'s License, Voter\'s Card, etc.)',
            'Utility Bill' => 'Utility Bill for Business Address Verification',
            'Bank Statement' => 'Recent Bank Statement',
            'Other' => 'Other Supporting Document'
        ];

        return view('admin.vendors.documents.create', compact('user', 'documentTypes'));
    }

    public function store(Request $request, User $user)
    {
        if ($user->role !== 'vendor') {
            return back()->with('error', 'User is not a vendor.');
        }

        $request->validate([
            'document_type' => 'required|string',
            'document_number' => 'nullable|string|max:100',
            'document_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'expiry_date' => 'nullable|date',
        ]);

        $filePath = $request->file('document_file')->store('vendor-documents/' . $user->id, 'public');

        VendorDocument::create([
            'user_id' => $user->id,
            'document_type' => $request->document_type,
            'document_number' => $request->document_number,
            'file_path' => $filePath,
            'expiry_date' => $request->expiry_date,
            'status' => 'approved',
        ]);

        return redirect()->route('admin.vendors.documents', $user)
            ->with('success', 'Document uploaded successfully and pending review.');
    }

    /**
     * Show the document details.
     */
    public function show(User $user, VendorDocument $document)
    {
        if ($user->id !== $document->user_id) {
            abort(403, 'Unauthorized action.');
        }

        return view('admin.vendors.documents.show', compact('user', 'document'));
    }

    /**
     * Approve a document.
     */
    public function approve(User $user, VendorDocument $document)
    {
        if ($user->id !== $document->user_id) {
            abort(403, 'Unauthorized action.');
        }

        $document->update([
            'status' => 'approved',
            'rejection_reason' => null
        ]);

        return back()->with('success', 'Document has been approved successfully.');
    }

    /**
     * Reject a document.
     */
    public function reject(Request $request, User $user, VendorDocument $document)
    {
        if ($user->id !== $document->user_id) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $document->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason
        ]);

        return back()->with('success', 'Document has been rejected with reason provided.');
    }

    public function destroy(User $user, VendorDocument $document)
    {
        if ($user->id !== $document->user_id) {
            abort(403, 'Unauthorized action.');
        }

        // Delete the file from storage
        Storage::disk('public')->delete($document->file_path);

        // Delete the record
        $document->delete();

        return redirect()->route('admin.vendors.documents', $user)
            ->with('success', 'Document has been deleted successfully.');
    }
}
