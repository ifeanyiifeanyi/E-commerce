<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Str;
use Intervention\Image\Image;
use App\Models\ProductMultiImage;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Imagick\Driver;

class ProductService
{
    /**
     * Create a new product
     */
    public function createProduct(array $data, array $files)
    {
        // Handle thumbnail upload
        $thumbnailName = $this->handleThumbnailUpload($files['product_thumbnail']);

        // Create product data
        $productData = [
            'brand_id' => $data['brand_id'],
            'category_id' => $data['category_id'],
            'subcategory_id' => $data['subcategory_id'],
            'product_name' => $data['product_name'],
            'product_slug' => Str::slug($data['product_name']),
            'product_code' => $data['product_code'],
            'product_qty' => $data['product_qty'],
            'product_tags' => $data['product_tags'] ?? null,
            'product_size' => $data['product_size'] ?? null,
            'product_color' => $data['product_color'] ?? null,
            'selling_price' => $data['selling_price'],
            'discount_price' => $data['discount_price'] ?? null,
            'short_description' => $data['short_description'],
            'long_description' => $data['long_description'],
            'product_thumbnail' => $thumbnailName,
            'vendor_id' => $data['vendor_id'] ?? null,
            'hot_deals' => isset($data['hot_deals']) ? 1 : 0,
            'featured' => isset($data['featured']) ? 1 : 0,
            'special_offer' => isset($data['special_offer']) ? 1 : 0,
            'special_deals' => isset($data['special_deals']) ? 1 : 0,
            'status' => isset($data['status']) ? 1 : 0,
        ];

        // Create product
        $product = Product::create($productData);

        // Handle multiple images
        if (isset($files['multi_images'])) {
            $this->handleMultiImageUpload($files['multi_images'], $product);
        }

        return $product;
    }

    /**
     * Update an existing product
     */
    public function updateProduct(Product $product, array $data, array $files)
    {
        // Handle thumbnail update if present
        if (isset($files['product_thumbnail'])) {
            // Delete old thumbnail
            if ($product->product_thumbnail) {
                $oldPath = public_path('uploads/products/thumbnails/' . $product->product_thumbnail);
                if (File::exists($oldPath)) {
                    File::delete($oldPath);
                }
            }

            // Upload new thumbnail
            $thumbnailName = $this->handleThumbnailUpload($files['product_thumbnail']);
        } else {
            $thumbnailName = $product->product_thumbnail;
        }

        // Update product data
        $productData = [
            'brand_id' => $data['brand_id'],
            'category_id' => $data['category_id'],
            'subcategory_id' => $data['subcategory_id'],
            'product_name' => $data['product_name'],
            'product_slug' => Str::slug($data['product_name']),
            'product_code' => $data['product_code'],
            'product_qty' => $data['product_qty'],
            'product_tags' => $data['product_tags'] ?? null,
            'product_size' => $data['product_size'] ?? null,
            'product_color' => $data['product_color'] ?? null,
            'selling_price' => $data['selling_price'],
            'discount_price' => $data['discount_price'] ?? null,
            'short_description' => $data['short_description'],
            'long_description' => $data['long_description'],
            'product_thumbnail' => $thumbnailName,
            'vendor_id' => $data['vendor_id'] ?? null,
            'hot_deals' => isset($data['hot_deals']) ? 1 : 0,
            'featured' => isset($data['featured']) ? 1 : 0,
            'special_offer' => isset($data['special_offer']) ? 1 : 0,
            'special_deals' => isset($data['special_deals']) ? 1 : 0,
            'status' => isset($data['status']) ? 1 : 0,
        ];

        // Update product
        $product->update($productData);

        // Handle multiple images if present
        if (isset($files['multi_images'])) {
            $this->handleMultiImageUpload($files['multi_images'], $product);
        }

        return $product;
    }


    private function handleThumbnailUpload($file)
    {
        if (!$file) {
            return null;
        }

        $fileName = hexdec(uniqid()) . '.' . $file->getClientOriginalExtension();
        $path = 'uploads/products/thumbnails/';

        // Create directory if it doesn't exist
        $directory = public_path($path);
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        // Create image manager with GD driver (or use Imagick)
        $manager = new ImageManager(new Driver());

        // Read the image
        $image = $manager->read($file);

        // Add watermark
        $app_name = config('app.name', 'Your Application');

        // Create a position at the center of the image
        $width = $image->width();
        $height = $image->height();

        // Add watermark text
        $image->text(
            $app_name,
            $width / 2,
            $height / 2,
            function ($font) {
                // In Intervention Image v3, font handling is different
                $font->filename(public_path('fonts/new.ttf'));
                $font->size(40);
                $font->color('#ffffff80'); // White with 50% transparency
                $font->align('center');
                $font->valign('middle');
                $font->angle(45);
            }
        );

        // Save the image
        $image->save(public_path($path . $fileName));

        return $fileName;
    }

    /**
     * Handle multiple image upload with watermark using Intervention Image v3
     */
    private function handleMultiImageUpload($files, Product $product)
    {
        if (!$files) {
            return;
        }

        $path = 'uploads/products/multi-images/';

        // Create directory if it doesn't exist
        $directory = public_path($path);
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        // Create image manager with GD driver (or use Imagick)
        $manager = new ImageManager(new Driver());

        foreach ($files as $file) {
            $fileName = hexdec(uniqid()) . '.' . $file->getClientOriginalExtension();

            // Read the image
            $image = $manager->read($file);

            // Add watermark
            $app_name = config('app.name', 'Your Application');

            // Create a position at the center of the image
            $width = $image->width();
            $height = $image->height();

            // Add watermark text
            $image->text(
                $app_name,
                $width / 2,
                $height / 2,
                function ($font) {
                    // In Intervention Image v3, font handling is different
                    $font->filename(public_path('fonts/new.ttf'));
                    $font->size(40);
                    $font->color('#ffffff80'); // White with 50% transparency
                    $font->align('center');
                    $font->valign('middle');
                    $font->angle(45);
                }
            );

            // Save the image
            $image->save(public_path($path . $fileName));

            // Store in database
            ProductMultiImage::create([
                'product_id' => $product->id,
                'photo_name' => $fileName,
            ]);
        }
    }


    /**
     * Delete product images
     */
    public function deleteProductImages(Product $product)
    {
        // Delete thumbnail
        if ($product->product_thumbnail) {
            $thumbnailPath = public_path('uploads/products/thumbnails/' . $product->product_thumbnail);
            if (File::exists($thumbnailPath)) {
                File::delete($thumbnailPath);
            }
        }

        // Delete multi images
        foreach ($product->productMultiImages as $image) {
            $imagePath = public_path('uploads/products/multi-images/' . $image->photo_name);
            if (File::exists($imagePath)) {
                File::delete($imagePath);
            }
            $image->delete();
        }
    }

    /**
     * Delete a specific product multi image
     */
    public function deleteMultiImage(ProductMultiImage $image)
    {
        $imagePath = public_path('uploads/products/multi-images/' . $image->photo_name);
        if (File::exists($imagePath)) {
            File::delete($imagePath);
        }
        $image->delete();
    }
}
