<?php

namespace App\Http\Controllers\Admin;

use App\Models\Brand;
use App\Models\Product;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use App\Models\MeasurementUnit;
use App\Services\ProductService;
use App\Models\ProductMultiImage;
use App\Http\Controllers\Controller;
use App\Services\ProductCodeGenerator;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;

class ProductController extends Controller
{

    public function __construct(private ProductService $productService, private ProductCodeGenerator $productCodeGenerator) {}

    public function index()
    {
        $products = Product::with(['brand', 'category', 'subcategory'])
            ->latest()
            ->paginate(10);

        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $brands = Brand::active()->get();
        $categories = Category::all();
        $measurementUnits = MeasurementUnit::where('is_active', true)->get();

        return view('admin.products.create', compact('brands', 'categories', 'measurementUnits'));
    }

    public function show(Product $product)
    {
        $product->load(['brand', 'category', 'subcategory', 'productMultiImages']);
        return view('admin.products.show', compact('product'));
    }


    public function getSubcategories(Request $request)
    {
        $subcategories = Category::find($request->category_id)->subcategories;

        return response()->json($subcategories);
    }

    public function getCategories()
    {
        $categories = Category::all();
        return response()->json($categories);
    }

    public function store(ProductStoreRequest $request)
    {

        $validated = $request->validated();

        // Generate unique product code if not provided

        $validated['product_code'] = ProductCodeGenerator::generate(
            $request->vendor_id ?? null,
            $validated['category_id'],
            true // Set isAdmin to true since this is the admin controller
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
            ->route('admin.products')
            ->with('success', 'Product created successfully');
    }



    public function edit(Product $product)
    {
        $brands = Brand::active()->get();
        $categories = Category::all();
        $subcategories = Subcategory::all();

        return view('admin.products.edit', compact('product', 'brands', 'categories', 'subcategories'));
    }

    public function update(ProductUpdateRequest $request, Product $product)
    {
        $validated = $request->validated();

        $validated['product_code'] = ProductCodeGenerator::generate(
            $request->vendor_id ?? null,
            $validated['category_id'],
            true // Set isAdmin to true since this is the admin controller
        );

        // Update product using the service
        $this->productService->updateProduct(
            $product,
            $validated,
            $request->allFiles()
        );

        return redirect()
            ->route('admin.products')
            ->with('success', 'Product updated successfully');
    }



    public function deleteMultiImage(int $imageId)
    {
        $image = ProductMultiImage::findOrFail($imageId);
        $this->productService->deleteMultiImage($image);

        return redirect()->back()->with('success', 'Image deleted successfully');
    }


    public function destroy(Product $product)
    {
        $this->productService->deleteProduct($product);

        return redirect()
            ->route('admin.products')
            ->with('success', 'Product deleted successfully');
    }



    /**
     * Toggle product status (active/inactive)
     */
    public function toggleStatus(Request $request, Product $product)
    {
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
}
