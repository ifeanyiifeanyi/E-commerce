<?php

namespace App\Http\Controllers\Vendor;

use Illuminate\Http\Request;
use App\Models\VendorDocument;
use App\Http\Controllers\Controller;
use App\Services\VendorDocumentService;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreDocumentRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class VendorDocumentController extends Controller
{
    use AuthorizesRequests;
    public function __construct(protected VendorDocumentService $documentService) {}


    /**
     * Display a listing of the vendor's documents.
     */
    public function index()
    {
        $documents = request()->user()->documents;
        return view('vendor.documents.index', compact('documents'));
    }

    /**
     * Show the form for creating a new document.
     */
    public function create()
    {
        $documentTypes = $this->documentService->getDocumentTypes();
        return view('vendor.documents.create', compact('documentTypes'));
    }

     /**
     * Store a newly created document in storage.
     */
    public function store(StoreDocumentRequest $request)
    {
        $result = $this->documentService->storeDocument($request);

        if ($result['status'] === 'success') {
            return redirect()->route('vendor.documents')
                ->with('success', 'Document uploaded successfully and is pending approval.');
        }

        return back()->with('error', $result['message']);
    }

    /**
     * Display the specified document.
     */
    public function show(VendorDocument $document)
    {
        $this->authorize('view', $document);
        return view('vendor.documents.show', compact('document'));
    }

    /**
     * Remove the specified document from storage.
     */
    public function destroy(VendorDocument $document)
    {
        $this->authorize('delete', $document);

        if (Storage::exists($document->file_path)) {
            Storage::delete($document->file_path);
        }

        $document->delete();

        return redirect()->route('vendor.documents')
            ->with('success', 'Document deleted successfully.');
    }

}
