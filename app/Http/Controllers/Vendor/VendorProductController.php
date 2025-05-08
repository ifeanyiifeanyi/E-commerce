<?php

namespace App\Http\Controllers\Vendor;

use App\Models\Brand;
use App\Models\Product;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use App\Models\MeasurementUnit;
use App\Services\ProductService;
use App\Models\ProductMultiImage;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\ProductCodeGenerator;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;

class VendorProductController extends Controller
{
    public function __construct(
        private ProductService $productService,
        private ProductCodeGenerator $productCodeGenerator
    ) {}

    public function index()
    {
        // Get all products for the current vendor
        $products = Product::with(['brand', 'category', 'subcategory'])
            ->where('vendor_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('vendor.products.index', compact('products'));
    }

    public function create()
    {
        $brands = Brand::active()->get();
        $categories = Category::all();
        $measurementUnits = MeasurementUnit::where('is_active', true)->get();
        $currencySymbol = session('currency_symbol', '₦');
        $currency = session('currency', 'NGN');


        return view('vendor.products.create', compact(
            'brands',
            'categories',
            'measurementUnits',
            'currencySymbol',
            'currency'
        ));
    }

    public function show(Product $product)
    {
        // Verify the product belongs to the current vendor
        $this->authorizeVendorProduct($product);

        $product->load(['brand', 'category', 'subcategory', 'productMultiImages']);
        return view('vendor.products.show', compact('product'));
    }

    public function getUnitDetails(Request $request)
    {
        $unitId = $request->unit_id;
        $unit = MeasurementUnit::with('baseUnit')->findOrFail($unitId);

        return response()->json($unit);
    }

    // Add this method if it doesn't exist to handle subcategory fetching
    public function getSubcategories(Request $request)
    {
        $subcategories = Subcategory::where('category_id', $request->category_id)->get();
        return response()->json($subcategories);
    }

    public function store(ProductStoreRequest $request)
    {
        $validated = $request->validated();

        // Add the current vendor's ID to the data
        $validated['vendor_id'] = Auth::id();

        // Generate unique product code
        $validated['product_code'] = $this->productCodeGenerator->generate(
            Auth::id(), // Current vendor ID
            $validated['category_id'],
            false // Not admin
        );

        // Create files array manually
        $files = [
            'product_thumbnail' => $request->file('product_thumbnail'),
            'multi_images' => $request->file('multi_images')
        ];

        // Create product using the service
        $product = $this->productService->createProduct(
            $validated,
            $files
        );

        return redirect()
            ->route('vendor.products')
            ->with('success', 'Product created successfully');
    }

    public function edit(Product $product)
    {
        // Verify the product belongs to the current vendor
        $this->authorizeVendorProduct($product);

        $brands = Brand::active()->get();
        $categories = Category::all();
        $subcategories = Subcategory::all();
        $currencySymbol = session('currency_symbol', '₦');
        $currency = session('currency', 'NGN');

        return view('vendor.products.edit', compact(
            'product',
            'brands',
            'categories',
            'subcategories',
            'currencySymbol',
            'currency'
        ));
    }

    public function update(ProductUpdateRequest $request, Product $product)
    {
        // Verify the product belongs to the current vendor
        $this->authorizeVendorProduct($product);

        $validated = $request->validated();

        // Ensure vendor_id doesn't change
        $validated['vendor_id'] = Auth::id();

        // Update product using the service
        $this->productService->updateProduct(
            $product,
            $validated,
            $request->allFiles()
        );

        return redirect()
            ->route('vendor.products')
            ->with('success', 'Product updated successfully');
    }

    public function deleteMultiImage(int $imageId)
    {
        $image = ProductMultiImage::findOrFail($imageId);

        // Verify the image belongs to a product owned by the current vendor
        $product = Product::findOrFail($image->product_id);
        $this->authorizeVendorProduct($product);

        $this->productService->deleteMultiImage($image);

        return redirect()->back()->with('success', 'Image deleted successfully');
    }

    public function destroy(Product $product)
    {
        // Verify the product belongs to the current vendor
        $this->authorizeVendorProduct($product);

        $this->productService->deleteProduct($product);

        return redirect()
            ->route('vendor.products')
            ->with('success', 'Product deleted successfully');
    }

    /**
     * Toggle product status (active/inactive)
     */
    public function toggleStatus(Request $request, Product $product)
    {
        // Verify the product belongs to the current vendor
        $this->authorizeVendorProduct($product);

        // Validate request
        $request->validate([
            'status' => 'required|boolean'
        ]);

        // Update product status
        $product->update([
            'status' => $request->status
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Product status updated successfully',
            'status' => $product->status
        ]);
    }

    /**
     * Authorize vendor access to a product
     */
    private function authorizeVendorProduct(Product $product)
    {
        if ($product->vendor_id !== Auth::id()) {
            abort(403, 'Unauthorized action. This product does not belong to you.');
        }
    }
}
