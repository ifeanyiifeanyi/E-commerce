<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Services\LocationService;
use App\Mail\CustomerGeneralEmail;
use Illuminate\Support\Facades\DB;
use App\Models\CustomerActivityLog;
use App\Http\Controllers\Controller;
use App\Models\CustomerLoginHistory;
use App\Models\CustomerNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Models\CustomerEmailCampaign;
use App\Services\CustomerEmailService;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class AdminCustomerManagerController extends Controller
{
    protected $locationService;
    protected $emailService;

    public function __construct(LocationService $locationService, CustomerEmailService $emailService)
    {
        $this->locationService = $locationService;
        $this->emailService = $emailService;
    }

    /**
     * Display a listing of customers
     */
    public function index(Request $request)
    {
            $customers = User::activeCustomers()
                ->select(['id', 'name', 'email', 'phone', 'country', 'status', 'created_at', 'last_login_at', 'account_status', 'customer_segment'])->simplePaginate(100);


        $totalCustomers = User::where('role', '!=', 'admin')->where('role', '!=', 'vendor')->count();
        $activeCustomers = User::where('role', '!=', 'admin')->where('role', '!=', 'vendor')->where('account_status', 'active')->count();
        $newCustomersThisMonth = User::where('role', '!=', 'admin')->where('role', '!=', 'vendor')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        $customersByCountry = User::where('role', '!=', 'admin')->where('role', '!=', 'vendor')
            ->select('country', DB::raw('count(*) as total'))
            ->groupBy('country')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        return view('admin.customer-manager.index', compact(
            'customers',
            'totalCustomers',
            'activeCustomers',
            'newCustomersThisMonth',
            'customersByCountry'
        ));
    }

    /**
     * Display customer details
     */
    public function show(User $customer)
    {
        // Get customer orders
        $orders = Order::where('user_id', $customer->id)
            ->latest()
            ->limit(10)
            ->get();

        // Get login history
        $loginHistory = CustomerLoginHistory::where('user_id', $customer->id)
            ->latest()
            ->limit(10)
            ->get();

        // Get activity logs
        $activityLogs = CustomerActivityLog::where('user_id', $customer->id)
            ->latest()
            ->limit(10)
            ->get();

        // Get email history
        $emailHistory = CustomerEmailCampaign::where('user_id', $customer->id)
            ->latest()
            ->limit(10)
            ->get();

        // Calculate lifetime value
        $lifetimeValue = Order::where('user_id', $customer->id)
            ->where('payment_status', 'paid')
            ->sum('total_amount');

        // Calculate days since registration
        $daysSinceRegistration = $customer->created_at->diffInDays(now());

        // Get addresses
        $addresses = $customer->addresses;

        // Get latest notification
        $latestNotification = CustomerNotification::where('user_id', $customer->id)
            ->latest()
            ->first();

        return view('admin.customer-manager.show', compact(
            'customer',
            'orders',
            'loginHistory',
            'activityLogs',
            'emailHistory',
            'lifetimeValue',
            'daysSinceRegistration',
            'addresses',
            'latestNotification'
        ));
    }

    /**
     * Update customer information
     */
    public function update(Request $request, User $customer)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $customer->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'account_status' => 'required|in:active,inactive,suspended,banned',
            'customer_segment' => 'required|in:vip,regular,new,at_risk,dormant',
            'customer_notes' => 'nullable|string',
            'password' => 'nullable|string|min:8|confirmed',
            'photo' => 'nullable|image|max:2048'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Handle password update
        if ($request->filled('password')) {
            $request->merge([
                'password' => Hash::make($request->password)
            ]);
        } else {
            $request->request->remove('password');
        }

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('uploads/customers', 'public');
            $request->merge(['photo' => 'storage/' . $photoPath]);
        }

        // Update customer data
        $customer->update($request->all());

        // Log activity
        CustomerActivityLog::log(
            $customer->id,
            'profile_updated',
            'Profile updated by admin',
            null,
            $request->ip()
        );

        return redirect()
            ->route('admin.customers.show', $customer)
            ->with('success', 'Customer information updated successfully.');
    }

    /**
     * Delete customer account
     */
    public function destroy(User $customer)
    {
        // Cannot delete admin or vendor
        if ($customer->isAdmin() || $customer->isVendor()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete admin or vendor accounts.'
            ]);
        }

        // Delete customer
        $customer->delete();

        return response()->json([
            'success' => true,
            'message' => 'Customer deleted successfully.'
        ]);
    }

    /**
     * Send email to customer
     */
    public function sendEmail(Request $request, User $customer)
    {
        $validator = Validator::make($request->all(), [
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'email_type' => 'required|in:general,promotion,product_recommendation,account',
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'exists:products,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Create email campaign record
        $campaign = CustomerEmailCampaign::create([
            'user_id' => $customer->id,
            'email_type' => $request->email_type,
            'subject' => $request->subject,
            'content' => $request->message,
        ]);

        // Handle product recommendations
        if ($request->email_type === 'product_recommendation' && !empty($request->product_ids)) {
            $products = Product::whereIn('id', $request->product_ids)->get();
            $this->emailService->sendProductRecommendations($customer, $products->toArray(), $request->subject);
        } else {
            // Send general email
            Mail::to($customer)->send(new CustomerGeneralEmail(
                $customer,
                $request->subject,
                $request->message,
                $campaign->id
            ));
        }

        // Update sent timestamp
        $campaign->update(['sent_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'Email sent successfully to ' . $customer->email
        ]);
    }

    /**
     * Update customer status (AJAX)
     */
    public function updateStatus(Request $request, User $customer)
    {
        $validator = Validator::make($request->all(), [
            'account_status' => 'required|in:active,inactive,suspended,banned',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $customer->account_status = $request->account_status;
        $customer->save();

        // Log activity
        CustomerActivityLog::log(
            $customer->id,
            'status_updated',
            'Account status updated to ' . $request->account_status,
            null,
            $request->ip()
        );

        return response()->json([
            'success' => true,
            'message' => 'Customer status updated successfully.'
        ]);
    }

    /**
     * Update customer segment (AJAX)
     */
    public function updateSegment(Request $request, User $customer)
    {
        $validator = Validator::make($request->all(), [
            'customer_segment' => 'required|in:vip,regular,new,at_risk,dormant',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $customer->customer_segment = $request->customer_segment;
        $customer->save();

        return response()->json([
            'success' => true,
            'message' => 'Customer segment updated successfully.'
        ]);
    }

    /**
     * Send notification to customer
     */
    public function sendNotification(Request $request, User $customer)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'notification_type' => 'required|in:system,order,promotion',
            'link_url' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Create notification
        CustomerNotification::create([
            'user_id' => $customer->id,
            'title' => $request->title,
            'message' => $request->message,
            'notification_type' => $request->notification_type,
            'link_url' => $request->link_url,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Notification sent successfully.'
        ]);
    }

    /**
     * Get customer orders (AJAX)
     */
    public function getOrders(Request $request, User $customer)
    {
        $orders = Order::where('user_id', $customer->id)
            ->latest()
            ->paginate(5);

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    /**
     * Get login history (AJAX)
     */
    public function getLoginHistory(Request $request, User $customer)
    {
        $loginHistory = CustomerLoginHistory::where('user_id', $customer->id)
            ->latest()
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $loginHistory
        ]);
    }

    /**
     * Get activity logs (AJAX)
     */
    public function getActivityLogs(Request $request, User $customer)
    {
        $activityLogs = CustomerActivityLog::where('user_id', $customer->id)
            ->latest()
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $activityLogs
        ]);
    }

    /**
     * Get customer statistics dashboard (AJAX)
     */
    public function getCustomerStats()
    {
        $totalCustomers = User::where('role', '!=', 'admin')->where('role', '!=', 'vendor')->count();
        $activeCustomers = User::where('role', '!=', 'admin')->where('role', '!=', 'vendor')->where('account_status', 'active')->count();
        $newCustomersThisMonth = User::where('role', '!=', 'admin')->where('role', '!=', 'vendor')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // Get customers by country
        $customersByCountry = User::where('role', '!=', 'admin')->where('role', '!=', 'vendor')
            ->select('country', DB::raw('count(*) as total'))
            ->groupBy('country')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        // Get customers by device type
        $customersByDevice = CustomerLoginHistory::select('device_type', DB::raw('count(distinct user_id) as total'))
            ->groupBy('device_type')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // Get customer growth trend
        $customerGrowth = User::where('role', '!=', 'admin')->where('role', '!=', 'vendor')
            ->select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'), DB::raw('count(*) as total'))
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'totalCustomers' => $totalCustomers,
                'activeCustomers' => $activeCustomers,
                'newCustomersThisMonth' => $newCustomersThisMonth,
                'customersByCountry' => $customersByCountry,
                'customersByDevice' => $customersByDevice,
                'customerGrowth' => $customerGrowth
            ]
        ]);
    }
}
