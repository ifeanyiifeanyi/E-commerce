<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\SubcategoryService;
use App\Http\Requests\SubcategoryRequest;

class SubcategoryController extends Controller
{


    public function __construct(private SubcategoryService $subcategoryService)
    {
    }
    public function index()
    {
        $categories = Category::all();
        $subcategories = Subcategory::with('category')->simplePaginate(100);
        return view('admin.category.subcategory.index', compact('subcategories', 'categories'));
    }

    public function store(SubcategoryRequest $request)
    {
        $subcategory = $this->subcategoryService->create($request->validated());
        return redirect()->route('admin.subcategories')
            ->with('success', 'Subcategory created successfully.');
    }

    public function edit(Subcategory $subcategory)
    {
        return view('admin.subcategories.edit', compact('subcategory'));
    }

    public function update(SubcategoryRequest $request, Subcategory $subcategory)
    {
        $this->subcategoryService->update($subcategory, $request->validated());
        return redirect()->route('admin.subcategories')
            ->with('success', 'Subcategory updated successfully.');
    }

    public function destroy(Subcategory $subcategory)
    {
        $this->subcategoryService->delete($subcategory);
        return redirect()->route('admin.subcategories')
            ->with('success', 'Subcategory deleted successfully.');
    }
}
