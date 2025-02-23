<?php

namespace App\Services;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use App\Models\Brand;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class BrandService
{
    public function createBrand(array $data): Brand
    {
        $data['slug'] = Str::slug($data['name']);

        if (isset($data['logo'])) {
            $data['logo'] = $this->handleLogoUpload($data['logo']);
        }

        return Brand::create($data);
    }

    public function updateBrand(Brand $brand, array $data): Brand
    {
        $data['slug'] = Str::slug($data['name']);

        if (isset($data['logo'])) {
            $this->deleteLogo($brand->logo);
            $data['logo'] = $this->handleLogoUpload($data['logo']);
        }

        $brand->update($data);
        return $brand;
    }

    public function deleteBrand(Brand $brand): bool
    {
        $this->deleteLogo($brand->logo);
        return $brand->delete();
    }

    public function toggleStatus(Brand $brand): Brand
    {
        $brand->update(['status' => !$brand->status]);
        return $brand;
    }

    protected function handleLogoUpload(UploadedFile $file): string
    {
        // Create a unique filename
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();

        // Define the storage path (relative to storage/app/public)
        $path = 'brands/' . $filename;

        // Ensure the brands directory exists in storage/app/public
        Storage::disk('public')->makeDirectory('brands');

        // Create new ImageManager instance
        $manager = new ImageManager(new Driver());

        // Read and resize the image
        $image = $manager->read($file)
            ->cover(300, 200);

        // Save the image to storage/app/public/brands
        $image->save(storage_path('app/public/' . $path));

        return $path;
    }
    protected function deleteLogo(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
