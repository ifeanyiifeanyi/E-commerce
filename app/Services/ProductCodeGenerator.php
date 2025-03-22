<?php

namespace App\Services;

use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Str;

class ProductCodeGenerator
{
    /**
     * Generate a unique product code
     * Format: [VendorPrefix]-[CategoryPrefix]-[Random]-[Increment]
     * For admin uploads: [AD]-[CategoryPrefix]-[Random]-[Increment]
     *
     * @param int|null $vendorId The vendor ID (null for admin uploads)
     * @param int $categoryId The category ID
     * @param bool $isAdmin Whether the product is being uploaded by an admin
     * @return string
     */
    public static function generate(?int $vendorId, int $categoryId, bool $isAdmin = false): string
    {
        // Get vendor prefix (2 characters)
        // If it's admin upload and no vendor specified, use 'AD' as prefix
        $vendorPrefix = $isAdmin && !$vendorId ? 'AD' : self::getVendorPrefix($vendorId);

        // Get category prefix (2 characters)
        $categoryPrefix = self::getCategoryPrefix($categoryId);

        // Generate random part (3 characters)
        $randomPart = strtoupper(Str::random(3));

        // Get the count of products in this category
        // For admin uploads without vendor, count all admin products in this category
        $query = Product::where('category_id', $categoryId);

        if ($vendorId) {
            $query->where('vendor_id', $vendorId);
        } elseif ($isAdmin) {
            $query->whereNull('vendor_id')->orWhere('vendor_id', 0);
        }

        $count = $query->count();

        // Format the incremental part (4 digits)
        $incrementalPart = str_pad($count + 1, 4, '0', STR_PAD_LEFT);

        // Combine all parts
        $productCode = "{$vendorPrefix}-{$categoryPrefix}-{$randomPart}-{$incrementalPart}";

        // Check if this product code already exists
        if (Product::where('product_code', $productCode)->exists()) {
            // Recursively try again with a new random part
            return self::generate($vendorId, $categoryId, $isAdmin);
        }

        return $productCode;
    }

    /**
     * Get the vendor prefix based on vendor name
     *
     * @param int|null $vendorId
     * @return string
     */
    protected static function getVendorPrefix(?int $vendorId): string
    {
        // If no vendor ID (admin product), use admin prefix
        if (!$vendorId) {
            return 'AD';
        }

        $vendor = User::find($vendorId);
        if (!$vendor) {
            return 'XX'; // Default if vendor not found
        }

        // Get vendor store name if available, otherwise use vendor name
        $vendorStore = \App\Models\VendorStore::where('user_id', $vendorId)->first();
        $name = $vendorStore ? $vendorStore->store_name : $vendor->name;

        // Extract first letters of each word, up to 2 characters
        $words = explode(' ', $name);
        $prefix = '';

        foreach ($words as $word) {
            if (strlen($prefix) < 2) {
                $prefix .= strtoupper(substr($word, 0, 1));
            } else {
                break;
            }
        }

        // If we don't have 2 characters yet, pad with the next letter from the first word
        if (strlen($prefix) < 2 && isset($words[0]) && strlen($words[0]) > 1) {
            $prefix .= strtoupper(substr($words[0], 1, 1));
        }

        // If still less than 2 characters, pad with X
        while (strlen($prefix) < 2) {
            $prefix .= 'X';
        }

        return $prefix;
    }

    /**
     * Get the category prefix based on category name
     *
     * @param int $categoryId
     * @return string
     */
    protected static function getCategoryPrefix(int $categoryId): string
    {
        $category = \App\Models\Category::find($categoryId);
        if (!$category) {
            return 'XX'; // Default if category not found
        }

        // Extract first letters of each word, up to 2 characters
        $words = explode(' ', $category->name);
        $prefix = '';

        foreach ($words as $word) {
            if (strlen($prefix) < 2) {
                $prefix .= strtoupper(substr($word, 0, 1));
            } else {
                break;
            }
        }

        // If we don't have 2 characters yet, pad with the next letter from the first word
        if (strlen($prefix) < 2 && isset($words[0]) && strlen($words[0]) > 1) {
            $prefix .= strtoupper(substr($words[0], 1, 1));
        }

        // If still less than 2 characters, pad with X
        while (strlen($prefix) < 2) {
            $prefix .= 'X';
        }

        return $prefix;
    }
}
