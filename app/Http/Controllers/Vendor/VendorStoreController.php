<?php

namespace App\Http\Controllers\Vendor;

use App\Models\VendorStore;
use Illuminate\Http\Request;
use App\Services\StoreService;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUpdateRequest;
use WisdomDiala\Countrypkg\Models\Country;

class VendorStoreController extends Controller
{
    public function __construct(private StoreService $storeService) {}


    public function index()
    {
        $store = VendorStore::where('user_id', request()->user()->id)->firstOrNew();
        $documents = request()->user()->documents;
        $countries = Country::all();
        return view('vendor.store.index', compact('store', 'documents', 'countries'));
    }

    /**
     * Update the store details
     *
     * @param StoreUpdateRequest $request
     * @param StoreService $storeService
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(StoreUpdateRequest $request)
    {
        $user = request()->user();
        $logo = $request->hasFile('store_logo') ? $request->file('store_logo') : null;
        $banner = $request->hasFile('store_banner') ? $request->file('store_banner') : null;

        $result = $this->storeService->updateStore(
            $request->validated(),
            $user->id,
            $logo,
            $banner
        );

        $message = $result['isNewStore'] ?
            'Store details submitted successfully. Waiting for admin approval.' :
            'Store details updated successfully.';

        return redirect()->route('vendor.stores.show')
            ->with('success', $message);
    }

    public function show()
    {
        $documents = request()->user()->documents;
        $store = VendorStore::where('user_id', request()->user()->id)->first();
        return view('vendor.store.show', compact('store', 'documents'));
    }
}
