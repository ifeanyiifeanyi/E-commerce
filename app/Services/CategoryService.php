<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CategoryService
{ public function create(array $data): Category
    {
        $data['slug'] = Str::slug($data['name']);
        $data['is_active'] = isset($data['is_active']) ? 1 : 0;

        if (isset($data['image'])) {
            $data['image'] = $data['image']->store('categories', 'public');
        }

        return Category::create($data);
    }

    public function update(Category $category, array $data): Category
    {
        $data['slug'] = Str::slug($data['name']);

        if (isset($data['image'])) {
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            $data['image'] = $data['image']->store('categories', 'public');
        }

        $category->update($data);
        return $category;
    }

    public function delete(Category $category): void
    {
        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }
        $category->delete();
    }
}
