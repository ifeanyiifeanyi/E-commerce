<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Services\CategoryService;
use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;

class CategoryController extends Controller
{

    public function __construct(private CategoryService $categoryService)
    {

    }
    public function index()
    {
        $categories = Category::withCount('subcategories')->orderBy('name', 'asc')->simplePaginate(100);
        return view('admin.category.index', compact('categories'));
    }


    public function store(CategoryRequest $request)
    {
        $category = $this->categoryService->create($request->validated());
        return redirect()->route('admin.categories')
            ->with('success', 'Category created successfully.');
    }

    public function update(CategoryRequest $request, Category $category)
    {
        $this->categoryService->update($category, $request->validated());
        return redirect()->route('admin.categories')
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        $this->categoryService->delete($category);
        return redirect()->route('admin.categories')
            ->with('success', 'Category deleted successfully.');
    }
}
