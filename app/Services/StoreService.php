<?php

namespace App\Services;

use App\Models\VendorStore;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class StoreService
{
    /**
     * Update or create a vendor store
     *
     * @param array $data Validated store data
     * @param int $userId User ID of the vendor
     * @param UploadedFile|null $logo Store logo file
     * @param UploadedFile|null $banner Store banner file
     * @return array Returns store object and isNewStore flag
     */
    public function updateStore(array $data, int $userId, ?UploadedFile $logo = null, ?UploadedFile $banner = null)
    {
        $store = VendorStore::where('user_id', $userId)->first();
        $isNewStore = false;

        if (!$store) {
            $store = new VendorStore();
            $store->user_id = $userId;
            $store->join_date = now();
            $isNewStore = true;
        }

        // Reset status to pending if was previously rejected
        if ($store->status === 'rejected') {
            $store->status = 'pending';
            $store->rejection_reason = null;
        }

        // Update store details
        $store->store_name = $data['store_name'];
        $store->store_slug = Str::slug($data['store_name']);
        $store->store_phone = $data['store_phone'];
        $store->store_email = $data['store_email'];
        $store->store_address = $data['store_address'];
        $store->store_city = $data['store_city'];
        $store->store_state = $data['store_state'];
        $store->store_postal_code = $data['store_postal_code'];
        $store->store_country = $data['store_country'];
        $store->store_description = $data['store_description'];
        $store->store_url = $data['store_url'] ?? null;
        $store->social_facebook = $data['social_facebook'] ?? null;
        $store->social_twitter = $data['social_twitter'] ?? null;
        $store->social_instagram = $data['social_instagram'] ?? null;
        $store->social_youtube = $data['social_youtube'] ?? null;
        $store->tax_number = $data['tax_number'] ?? null;
        $store->bank_name = $data['bank_name'] ?? null;
        $store->bank_account_number = $data['bank_account_number'] ?? null;
        $store->bank_routing_number = $data['bank_routing_number'] ?? null;
        $store->bank_account_name = $data['bank_account_name'] ?? null;
        $store->meta_title = $data['meta_title'] ?? null;
        $store->meta_description = $data['meta_description'] ?? null;
        $store->meta_keywords = $data['meta_keywords'] ?? null;

        // Handle logo upload
        if ($logo) {
            $this->handleLogoUpload($store, $logo);
        }

        // Handle banner upload
        if ($banner) {
            $this->handleBannerUpload($store, $banner);
        }

        $store->save();

        return [
            'store' => $store,
            'isNewStore' => $isNewStore
        ];
    }

    /**
     * Handle store logo upload
     *
     * @param VendorStore $store
     * @param UploadedFile $logoFile
     * @return void
     */
    protected function handleLogoUpload(VendorStore $store, UploadedFile $logoFile)
    {
        // Remove old logo if exists
        if ($store->store_logo) {
            Storage::disk('public')->delete($store->store_logo);
        }

        $logoPath = $logoFile->store('uploads/vendor/logo', 'public');
        $store->store_logo = $logoPath;
    }

    /**
     * Handle store banner upload
     *
     * @param VendorStore $store
     * @param UploadedFile $bannerFile
     * @return void
     */
    protected function handleBannerUpload(VendorStore $store, UploadedFile $bannerFile)
    {
        // Remove old banner if exists
        if ($store->store_banner) {
            Storage::disk('public')->delete($store->store_banner);
        }

        $bannerPath = $bannerFile->store('uploads/vendor/banner', 'public');
        $store->store_banner = $bannerPath;
    }

    /**
     * Delete store logo
     *
     * @param int $userId
     * @return array
     */
    public function deleteLogo(int $userId)
    {
        $store = VendorStore::where('user_id', $userId)->first();

        if ($store && $store->store_logo) {
            Storage::disk('public')->delete($store->store_logo);
            $store->store_logo = null;
            $store->save();

            return [
                'success' => true,
                'message' => 'Store logo deleted successfully'
            ];
        }

        return [
            'success' => false,
            'message' => 'Store logo not found'
        ];
    }

    /**
     * Delete store banner
     *
     * @param int $userId
     * @return array
     */
    public function deleteBanner(int $userId)
    {
        $store = VendorStore::where('user_id', $userId)->first();

        if ($store && $store->store_banner) {
            Storage::disk('public')->delete($store->store_banner);
            $store->store_banner = null;
            $store->save();

            return [
                'success' => true,
                'message' => 'Store banner deleted successfully'
            ];
        }

        return [
            'success' => false,
            'message' => 'Store banner not found'
        ];
    }
}
