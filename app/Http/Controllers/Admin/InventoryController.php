<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Product;
use App\Models\InventoryLog;
use Illuminate\Http\Request;
use App\Models\InventoryAlert;
use App\Http\Controllers\Controller;
use App\Http\Requests\ResolveAlertRequest;
use App\Http\Requests\AdjustInventoryRequest;
use App\Http\Requests\ReserveInventoryRequest;

class InventoryController extends Controller
{
    // public function index()
    // {
    //     $products = Product::with(['category', 'brand'])
    //         ->withCount(['inventoryLogs', 'inventoryAlerts' => function($query) {
    //             $query->where('is_resolved', false);
    //         }])
    //         ->paginate(15);

    //     return view('admin.inventory.index', compact('products'));
    // }

    public function index(Request $request)
    {
        $query = Product::with(['category', 'brand', 'measurementUnit'])
            ->withCount(['inventoryLogs', 'inventoryAlerts' => function($query) {
                $query->where('is_resolved', false);
            }]);

        // Filter by stock status
        if ($request->has('stock_status') && $request->stock_status != 'all') {
            $query->where('stock_status', $request->stock_status);
        }

        // Filter by low stock
        if ($request->has('low_stock') && $request->low_stock == 'true') {
            $query->whereRaw('product_qty <= low_stock_threshold');
        }

        // Search by product name or code
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('product_name', 'like', "%{$search}%")
                  ->orWhere('product_code', 'like', "%{$search}%");
            });
        }

        // Sort by inventory level
        if ($request->has('sort') && $request->sort == 'stock_low_to_high') {
            $query->orderBy('product_qty', 'asc');
        } elseif ($request->has('sort') && $request->sort == 'stock_high_to_low') {
            $query->orderBy('product_qty', 'desc');
        } else {
            $query->orderBy('product_name', 'asc');
        }

        $products = $query->paginate(15);

        return view('admin.inventory.index', compact('products'));
    }

    // public function adjustInventory(Request $request, Product $product)
    // {
    //     $validated = $request->validate([
    //         'quantity_change' => 'required|numeric',
    //         'action_type' => 'required|in:purchase,adjustment,return,count',
    //         'reference_type' => 'nullable|string|max:50',
    //         'reference_id' => 'nullable|string|max:50',
    //         'notes' => 'nullable|string|max:500',
    //     ]);

    //     $product->adjustInventory(
    //         $validated['quantity_change'],
    //         $validated['action_type'],
    //         request()->User()->id,
    //         $validated['reference_type'],
    //         $validated['reference_id'],
    //         $validated['notes']
    //     );

    //     return redirect()->back()->with('success', 'Inventory adjusted successfully');
    // }
    public function adjustInventory(AdjustInventoryRequest $request, Product $product)
    {
        $validated = $request->validated();

        $product->adjustInventory(
            $validated['quantity_change'],
            $validated['action_type'],
            auth()->id(),
            $validated['reference_type'] ?? null,
            $validated['reference_id'] ?? null,
            $validated['notes'] ?? null
        );

        return redirect()->back()->with('success', 'Inventory adjusted successfully');
    }

    // public function viewInventoryLogs(Product $product)
    // {
    //     $logs = $product->inventoryLogs()
    //         ->with('user')
    //         ->orderBy('created_at', 'desc')
    //         ->paginate(25);

    //     return view('admin.inventory.logs', compact('product', 'logs'));
    // }

//   /  public function viewAlerts()
    // {
    //     $alerts = InventoryAlert::with(['product', 'resolvedByUser'])
    //         ->orderBy('is_resolved')
    //         ->orderBy('created_at', 'desc')
    //         ->paginate(25);

    //     return view('admin.inventory.alerts', compact('alerts'));
    // }

    public function show(Product $product)
    {
        $product->load(['brand', 'category', 'subcategory', 'measurementUnit']);
        $recentLogs = $product->inventoryLogs()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $unresolved_alerts = $product->inventoryAlerts()
            ->where('is_resolved', false)
            ->get();

        return view('admin.inventory.show', compact('product', 'recentLogs', 'unresolved_alerts'));
    }

      /**
     * Reserve inventory for a product.
     */
    public function reserveInventory(ReserveInventoryRequest $request, Product $product)
    {
        $validated = $request->validated();

        $result = $product->reserveInventory(
            $validated['quantity'],
            $validated['order_id'] ?? null
        );

        if ($result) {
            return redirect()->back()->with('success', 'Inventory reserved successfully');
        }

        return redirect()->back()->with('error', 'Cannot reserve inventory. Insufficient available quantity.');
    }

     /**
     * Release reserved inventory for a product.
     */
    public function releaseReservedInventory(ReserveInventoryRequest $request, Product $product)
    {
        $validated = $request->validated();

        $product->releaseReservedInventory(
            $validated['quantity'],
            $validated['order_id'] ?? null
        );

        return redirect()->back()->with('success', 'Reserved inventory released successfully');
    }

    /**
     * View inventory logs for a product.
     */
    public function viewInventoryLogs(Request $request, Product $product = null)
    {
        $query = InventoryLog::with(['product', 'user']);

        if ($product) {
            $query->where('product_id', $product->id);
        }

        // Filter by action type
        if ($request->has('action_type') && !empty($request->action_type)) {
            $query->where('action_type', $request->action_type);
        }

        // Filter by date range
        if ($request->has('date_from') && !empty($request->date_from)) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && !empty($request->date_to)) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(25);

        return view('admin.inventory.logs', compact('logs', 'product'));
    }

     /**
     * View all inventory alerts.
     */
    public function viewAlerts(Request $request)
    {
        $query = InventoryAlert::with(['product', 'resolvedByUser']);

        // Filter by alert type
        if ($request->has('alert_type') && !empty($request->alert_type)) {
            $query->where('alert_type', $request->alert_type);
        }

        // Filter by resolution status
        if ($request->has('status')) {
            if ($request->status === 'resolved') {
                $query->where('is_resolved', true);
            } elseif ($request->status === 'unresolved') {
                $query->where('is_resolved', false);
            }
        }

        $alerts = $query->orderBy('is_resolved')
            ->orderBy('created_at', 'desc')
            ->paginate(25);

        return view('admin.inventory.alerts', compact('alerts'));
    }

     /**
     * Resolve an inventory alert.
     */
    public function resolveAlert(ResolveAlertRequest $request, InventoryAlert $alert)
    {
        $alert->resolve(auth()->id(), $request->notes);
        return redirect()->back()->with('success', 'Alert resolved successfully');
    }

      /**
     * Get product details via AJAX.
     */
    public function getProductDetails(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $product = Product::with('measurementUnit')
            ->findOrFail($request->product_id);

        return response()->json([
            'success' => true,
            'product' => [
                'id' => $product->id,
                'name' => $product->product_name,
                'code' => $product->product_code,
                'current_stock' => $product->product_qty,
                'available_stock' => $product->available_qty,
                'reserved_stock' => $product->reserved_qty,
                'unit' => $product->measurementUnit ? $product->measurementUnit->name : null,
                'unit_symbol' => $product->measurementUnit ? $product->measurementUnit->symbol : null,
                'is_weight_based' => $product->is_weight_based,
                'allow_decimal_qty' => $product->allow_decimal_qty,
                'min_order_qty' => $product->min_order_qty,
                'max_order_qty' => $product->max_order_qty,
                'stock_status' => $product->stock_status,
            ]
        ]);
    }

}
