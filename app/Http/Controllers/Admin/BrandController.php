<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\Brand;
use Illuminate\Http\Request;
use App\Services\BrandService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\StoreBrandRequest;
use App\Http\Requests\UpdateBrandRequest;

class BrandController extends Controller
{

    public function __construct(private BrandService $brandService)
    {
        //
    }
    public function index()
    {
        $brands = Brand::orderBy('name', 'asc')->simplePaginate(100);

        return view('admin.brand.index', compact('brands'));
    }

    public function create()
    {
        return view('admin.brand.create');
    }



    public function store(StoreBrandRequest $request): RedirectResponse
    {
        $this->brandService->createBrand($request->validated());
        return redirect()->route('admin.brands')
            ->with('success', 'Brand created successfully.');
    }

    public function edit(Brand $brand)
    {
        return view('admin.brand.edit', compact('brand'));
    }

    public function update(UpdateBrandRequest $request, Brand $brand): RedirectResponse
    {
        $this->brandService->updateBrand($brand, $request->validated());
        return redirect()->route('admin.brands')
            ->with('success', 'Brand updated successfully.');
    }

    public function toggleStatus(Brand $brand)
    {
        try {
            DB::beginTransaction();

            $brand = $this->brandService->toggleStatus($brand);

            DB::commit();

            return redirect()->route('admin.brands.index')
                ->with('success', 'Brand status updated successfully.');
        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->route('admin.brands')
                ->with('error', 'Failed to update brand status');
        }
    }


    public function destroy(Brand $brand): RedirectResponse
    {
        try {
            DB::beginTransaction();

            // Check if brand has associated products
            // if ($brand->products()->exists()) {
            //     return back()->with('error', 'Cannot delete brand. It has associated products.');
            // }

            $this->brandService->deleteBrand($brand);

            DB::commit();

            return redirect()->route('admin.brands')
                ->with('success', 'Brand deleted successfully.');
        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->route('admin.brands')
                ->with('error', 'Failed to delete brand.');
        }
    }
}
