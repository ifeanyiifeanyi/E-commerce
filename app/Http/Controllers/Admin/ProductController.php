<?php

namespace App\Http\Controllers\Admin;

use App\Models\Brand;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Services\ProductService;
use App\Models\ProductMultiImage;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductStoreRequest;

class ProductController extends Controller
{
    public function __construct(private ProductService $productService) {}

    public function index()
    {
        $products = Product::with(['brand', 'category', 'subcategory'])
            ->latest()
            ->paginate(10);

        return view('admin.products.index', compact('products'));
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $brands = Brand::active()->get();
        $categories = Category::all();

        return view('admin.products.create', compact('brands', 'categories'));
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

        return view('admin.products.edit', compact('product', 'brands', 'categories'));
    }

    public function update(ProductStoreRequest $request, Product $product)
    {
        $validated = $request->validated();

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
}
