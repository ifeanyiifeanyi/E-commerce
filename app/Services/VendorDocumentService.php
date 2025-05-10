<?php

namespace App\Services;

use App\Models\VendorDocument;

class VendorDocumentService
{
    public function getDocumentTypes()
    {
        return [
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
    }

    public function storeDocument($request)
    {
        try {
            $user = request()->user();

            if (!$user->isVendor()) {
                return [
                    'status' => 'error',
                    'message' => 'Only vendors can upload documents.'
                ];
            }

            // Check if user already has this document type
            $existingDocument = VendorDocument::where('user_id', $user->id)
                ->where('document_type', $request->document_type)
                ->first();

            if ($existingDocument) {
                return [
                    'status' => 'error',
                    'message' => 'You have already uploaded this document type. Please delete the existing one first.'
                ];
            }

            // Store the file in the public folder
            $path = $request->file('document_file')->store('public/vendor-documents/' . $user->id);

            // Remove 'public/' from the path when saving to database
            $pathForDatabase = str_replace('public/', '', $path);

            VendorDocument::create([
                'user_id' => $user->id,
                'document_type' => $request->document_type,
                'document_number' => $request->document_number,
                'file_path' => $pathForDatabase,
                'expiry_date' => $request->expiry_date,
                'status' => 'pending'
            ]);

            
            return [
                'status' => 'success',
                'message' => 'Document uploaded successfully.'
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'An error occurred: ' . $e->getMessage()
            ];
        }
    }
}
