<?php

namespace App\Services;

use App\Models\Subcategory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class SubcategoryService
{
    public function create(array $data): Subcategory
    {
        $data['slug'] = Str::slug($data['name']);

        if (isset($data['image'])) {
            $data['image'] = $data['image']->store('subcategories', 'public');
        }

        return Subcategory::create($data);
    }

    public function update(Subcategory $subcategory, array $data): Subcategory
    {
        $data['slug'] = Str::slug($data['name']);

        if (isset($data['image'])) {
            if ($subcategory->image) {
                Storage::disk('public')->delete($subcategory->image);
            }
            $data['image'] = $data['image']->store('subcategories', 'public');
        }

        $subcategory->update($data);
        return $subcategory;
    }

    public function delete(Subcategory $subcategory): void
    {
        if ($subcategory->image) {
            Storage::disk('public')->delete($subcategory->image);
        }
        $subcategory->delete();
    }
}
