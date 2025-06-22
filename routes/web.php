<?php

use GuzzleHttp\Promise\Create;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Vendor\VendorController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Auth\VendorLoginController;
use App\Http\Controllers\Admin\SubcategoryController;
use App\Http\Controllers\Admin\AdminProfileController;
use App\Http\Controllers\Vendor\VendorStoreController;
use App\Http\Controllers\Admin\AdvertisementController;
use App\Http\Controllers\Vendor\VendorProductController;
use App\Http\Controllers\Vendor\VendorProfileController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\Admin\MeasurementUnitController;
use App\Http\Controllers\Vendor\VendorDocumentController;
use App\Http\Controllers\Admin\VendorManagementController;
use App\Http\Controllers\Auth\VendorRegistrationController;
use App\Http\Controllers\Admin\AdminCustomerManagerController;
use App\Http\Controllers\Admin\ManageVendorDocumentController;
use App\Http\Controllers\Admin\AdminVendorStoreDetailController;
use App\Http\Controllers\Admin\ManageVendorAdvertisementSubscriptionController;
use App\Http\Controllers\Vendor\VendorAdvertisementController;

Route::get('/', function () {
    return view('home');
});


// Vendor Login Routes
Route::controller(VendorLoginController::class)->group(function () {
    Route::get('vendor/login', 'showLoginForm')->name('vendor.login.view');
    Route::post('vendor/login', 'login')->name('vendor.login');
    Route::post('vendor/logout', 'logout')->name('vendor.logout');
});


Route::controller(VendorRegistrationController::class)->group(function () {
    // Route::get('vendor/register', 'create')->name('vendor.register.view');
    // Route::post('vendor/register', 'store')->name('vendor.register');

    // Route::get('vendor/login', 'login')->name('vendor.login.view');
    // Route::post('vendor/login', 'login')->name('vendor.login');


    // Registration routes - Multi-step process
    Route::get('vendor/register', 'showStep1Form')->name('vendor.register.step1');
    Route::post('vendor/register/step1', 'processStep1')->name('vendor.register.step1.store');

    Route::get('vendor/register/step2', 'showStep2Form')->name('vendor.register.step2');
    Route::post('vendor/register/step2', 'processStep2')->name('vendor.register.step2.store');

    Route::get('vendor/register/step3', 'showStep3Form')->name('vendor.register.step3');
    Route::post('vendor/register/step3', 'processStep3')->name('vendor.register.step3.store');

    Route::get('vendor/register/step4', 'showStep4Form')->name('vendor.register.step4');
    Route::post('vendor/register/complete', 'complete')->name('vendor.register.complete');

    Route::get('pending',  'pending')->name('vendor.pending');


    Route::post('register/send-code',  'sendVerificationCode')->name('vendor.register.send-code');
    Route::post('/vendor/register/send-phone-code', 'sendPhoneVerificationCode')->name('vendor.register.send-phone-code');
});


// Vendor Registration Routes
// Route::prefix('vendor')->name('vendor.')->group(function () {
//     // Login routes
//     Route::get('login', [VendorRegistrationController::class, 'showLoginForm'])->name('login.view');


//     // Registration routes - Multi-step process
//     Route::get('register', [VendorRegistrationController::class, 'showStep1Form'])->name('register.step1');
//     Route::post('register/step1', [VendorRegistrationController::class, 'processStep1'])->name('register.step1.store');

//     Route::get('register/step2', [VendorRegistrationController::class, 'showStep2Form'])->name('register.step2');
//     Route::post('register/step2', [VendorRegistrationController::class, 'processStep2'])->name('register.step2.store');

//     Route::get('register/step3', [VendorRegistrationController::class, 'showStep3Form'])->name('register.step3');
//     Route::post('register/step3', [VendorRegistrationController::class, 'processStep3'])->name('register.step3.store');

//     Route::get('register/step4', [VendorRegistrationController::class, 'showStep4Form'])->name('register.step4');
//     Route::post('register/complete', [VendorRegistrationController::class, 'complete'])->name('register.complete');

//     // Pending approval route
//     Route::get('pending', [VendorRegistrationController::class, 'pending'])->name('pending');
// });

// Email Verification Routes
Route::middleware('auth')->group(function () {
    Route::get('email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    Route::get('email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();

        // Redirect based on user role
        $user = request()->user();
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role === 'vendor') {
            return redirect()->route('vendor.dashboard');
        } else {
            return redirect()->route('customer.dashboard');
        }
    })->middleware(['signed', 'throttle:6,1'])->name('verification.verify');

    Route::post('email/verification-notification', function (\Illuminate\Http\Request $request) {
        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    })->middleware(['throttle:6,1'])->name('verification.send');
});

// dashboard auth invokable
Route::get('dashboard', DashboardController::class)->middleware(['auth', 'verified'])->name('dashboard');

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => ['auth', 'role:admin']], function () {
    Route::controller(AdminController::class)->group(function () {
        Route::get('dashboard', 'dashboard')->name('dashboard');
        Route::get('logout', 'logout')->name('logout');
    });

    Route::controller(AdvertisementController::class)->group(function () {
        Route::get('advertisement-packages', 'index')->name('advertisement.packages');
        Route::get('advertisement-packages/create', 'create')->name('advertisement.packages.create');
        Route::post('advertisement-packages/store', 'store')->name('advertisement.packages.store');
        Route::get('advertisement-packages/{package}/show', 'show')->name('advertisement.packages.show');
        Route::get('advertisement-packages/{package}/edit', 'edit')->name('advertisement.packages.edit');
        Route::put('advertisement-packages/{package}/update', 'update')->name('advertisement.packages.update');
        Route::delete('advertisement-packages/{package}/delete', 'destroy')->name('advertisement.packages.destroy');

        // Route::post('/vendor/advertisements/{vendor}', 'vendorAdvertisements')->name('vendor.advertisements');
    });

    Route::controller(ManageVendorAdvertisementSubscriptionController::class)->group(function () {
        Route::get('vendor-advertisements', 'index')->name('vendor.advertisements');
        Route::get('vendor-advertisements/{advertisement}', 'show')->name('vendor.advertisements.show');
        Route::post('vendor-advertisements/{advertisement}/approve', 'approve')->name('vendor.advertisements.approve');
        Route::post('vendor-advertisements/{advertisement}/reject', 'reject')->name('vendor.advertisements.reject');
        Route::delete('vendor-advertisements/{advertisement}', 'destroy')->name('vendor.advertisements.destroy');


        Route::get('pending-advertisements', 'pendingAds')->name('vendor.advertisements.pending');
        Route::get('suspended-advertisements', 'suspendedAds')->name('vendor.advertisements.suspended_details');
        Route::get('active-advertisements', 'activeAds')->name('vendor.advertisements.active');
        Route::get('expired-advertisements', 'expiredAds')->name('vendor.advertisements.expired');

        Route::post('vendor-advertisements/{advertisement}/suspend', 'suspend')->name('vendor.advertisements.suspended');
        Route::post('vendor-advertisements/{advertisement}/reactivate', 'reactivate')->name('vendor.advertisements.reactivate');




        Route::post('bulk-advertisement-action', 'bulkAction')->name('vendor.advertisements.bulk-action');

        Route::get('vendor-advertisements/{advertisementId}/send-message', 'sendVendorMessage')->name('vendor.advertisements.send-message');

        Route::get('advertisement-analytics', 'analytics')->name('advertisement.analytics');
    });

    Route::controller(AdminProfileController::class)->group(function () {
        Route::get('profile', 'index')->name('profile');
        Route::put('update-profile', 'update')->name('profile.update');
        Route::put('password', 'updatePassword')->name('profile.password');

        Route::post('/profile/photo', 'updatePhoto')->name('profile.photo');
        Route::delete('/profile/photo', 'deletePhoto')->name('profile.photo.delete');
        Route::delete('/profile/sessions', 'logoutSession')->name('profile.logout-session');
    });

    Route::controller(BrandController::class)->group(function () {
        Route::get('brands', 'index')->name('brands');
        Route::get('create', 'create')->name('brands.create');
        Route::post('store', 'store')->name('brands.store');
        Route::get('edit/{brand}', 'edit')->name('brands.edit');
        Route::put('update/{brand}', 'update')->name('brands.update');
        Route::delete('destroy/{brand}/brand', 'destroy')->name('brands.destroy');
        Route::patch('status/{brand}/toggle', 'toggleStatus')->name('brands.toggle-status');
    });

    Route::controller(CategoryController::class)->group(function () {
        Route::get('categories', 'index')->name('categories');
        Route::post('category/store', 'store')->name('categories.store');
        Route::put('category/{category}', 'update')->name('categories.update');
        Route::delete('category/{category}/destroy', 'destroy')->name('categories.destroy');
    });

    Route::controller(SubcategoryController::class)->group(function () {
        Route::get('subcategories', 'index')->name('subcategories');
        Route::post('subcategory/store', 'store')->name('subcategories.store');
        Route::put('subcategory/{subcategory}', 'update')->name('subcategories.update');
        Route::delete('subcategory/{subcategory}/destroy', 'destroy')->name('subcategories.destroy');
    });

    Route::controller(VendorManagementController::class)->group(function () {
        Route::get('vendors', 'index')->name('vendors');
        Route::get('vendors/{user}', 'showVendor')->name('vendors.show');
        Route::patch('vendor/approve/{user}', 'approveVendor')->name('vendors.approve');
        Route::patch('vendor/deactivate/{user}', 'deactivateVendor')->name('vendors.deactivate');
        Route::delete('vendor/delete/{user}', 'deleteVendor')->name('vendors.delete');

        Route::get('vendor/create', 'createVendor')->name('create.vendors');
        Route::post('store/vendor', 'storeVendor')->name('vendors.store');
        Route::get('vendor/edit/{user}', 'editVendor')->name('vendors.edit');
        Route::put('vendor/update/{user}', 'updateVendor')->name('vendors.update');
    });

    Route::controller(AdminVendorStoreDetailController::class)->group(function () {
        Route::get('vendor/stores', 'index')->name('vendor.stores');
        Route::get('vendor/store/{store:store_slug}', 'show')->name('vendor.stores.show');

        Route::post('vendor/{store}/approve', 'approve')->name('vendor.stores.approve');
        Route::post('vendor/{store}/reject', 'reject')->name('vendor.stores.reject');
        Route::delete('vendor/{store}', 'destroy')->name('vendor.stores.destroy');
        Route::get('vendor/{store}/documents', 'documents')->name('vendor.stores.documents');
        Route::post('vendor/{store}/toggle-featured', 'toggleFeatured')->name('vendor.stores.toggle-featured');
    });






    Route::controller(ManageVendorDocumentController::class)->group(function () {
        Route::get('vendor/documents/{user?}', 'index')->name('vendors.documents');
        Route::get('vendor/documents/{user}/create', 'create')->name('vendors.documents.create');

        Route::get('vendor/{user}/{document}/show', 'show')->name('vendors.documents.show');
        Route::patch('vendor/{user}/{document}/approve', 'approve')->name('vendors.documents.approve');
        Route::patch('vendor/{user}/{document}/reject', 'reject')->name('vendors.documents.reject');
        Route::get('vendor/{user}/{document}/delete', 'delete')->name('vendors.documents.destroy');


        Route::post('vendor/documents/{user}', 'store')->name('vendors.documents.store');
    });


    Route::controller(MeasurementUnitController::class)->group(function () {

        Route::get('measurement-units', 'index')->name('measurement-units');
        Route::get('measurement-units/create', 'create')->name('measurement-units.create');
        Route::post('measurement-units/store', 'store')->name('measurement-units.store');
        Route::get('measurement-units/{measurement_unit}/show', 'show')->name('measurement-units.show');
        Route::get('measurement-units/{measurement_unit}/edit', 'edit')->name('measurement-units.edit');
        Route::put('measurement-units/{measurement_unit}/update', 'update')->name('measurement-units.update');
        Route::delete('measurement-units/{measurement_unit}/delete', 'destroy')->name('measurement-units.destroy');
        Route::patch('measurement-units/{measurement_unit}/toggle-active', 'toggleActive')->name('measurement-units.toggle-active');

        Route::get('measurement-units/get-unit-details', 'getUnitDetails')->name('measurement-units.get-unit-details');
    });


    // Route::controller(InventoryController::class)->group(function(){
    //     Route::get('inventory', 'index')->name('inventory');
    //     Route::get('inventory/{product}/show', 'show')->name('inventory.show');
    //     Route::post('inventory/{product}/store', 'store')->name('inventory.store');
    //     Route::put('inventory/{product}/update', 'update')->name('inventory.update');
    //     Route::delete('inventory/{product}/delete', 'destroy')->name('inventory.destroy');

    //     Route::get('get-product-details', 'getProductDetails')->name('get.product.details');

    //     Route::get('get-product-logs', 'getProductLogs')->name('inventory.logs');
    //     Route::get('product-inventory-alert', 'getProductLogs')->name('inventory.alerts');

    //     Route::post('adjust-inventory', 'getProductAlerts')->name('inventory.adjust');

    // });

    Route::controller(InventoryController::class)->group(function () {
        Route::get('inventory', 'index')->name('inventory');
        Route::get('inventory/{product}/logs', 'viewInventoryLogs')->name('inventory.logs');
        Route::get('inventory/alerts', 'viewAlerts')->name('inventory.alerts');
        Route::post('inventory/{product}/adjust', 'adjustInventory')->name('inventory.adjust');

        // For individual product inventory management
        Route::get('inventory/{product}', 'show')->name('inventory.show');
        Route::post('inventory/{product}/reserve', 'reserveInventory')->name('inventory.reserve');
        Route::post('inventory/{product}/release', 'releaseReservedInventory')->name('inventory.release');

        // Product lookup for AJAX
        Route::get('get-product-details', 'getProductDetails')->name('get.product.details');
        Route::post('resolve', 'resolveAlert')->name('inventory.alerts.resolve');
    });

    Route::controller(ProductController::class)->group(function () {

        Route::get('products', 'index')->name('products');
        Route::get('products/create', 'create')->name('products.create');
        Route::post('products/store', 'store')->name('products.store');
        Route::get('products/{product}/show', 'show')->name('products.show');
        Route::get('products/{product}/edit', 'edit')->name('products.edit');
        Route::put('products/{product}/update', 'update')->name('products.update');
        Route::delete('products/{product}/delete', 'destroy')->name('products.destroy');

        Route::get('get-subcategories', 'getSubcategories')->name('get.subcategories');
        Route::get('get-category', 'getCategories')->name('get.categories');

        // Delete multi-image
        Route::delete('/product-multi-image/{id}', 'deleteMultiImage')->name('product.delete.multi-image');
        Route::post('products/{product}/toggle-status',  'toggleStatus')->name('products.toggle-status');


        Route::get('get-brands', 'getBrands')->name('get.brands');
        Route::get('get-vendors', 'getVendors')->name('get.vendors');
        Route::get('get-vendor-documents', 'getVendorDocuments')->name('get.vendor.documents');
    });

    Route::controller(AdminCustomerManagerController::class)->group(function () {
        Route::get('customers', 'index')->name('customers');
        Route::get('customers/{customer}/show', 'show')->name('customers.show');
        Route::get('customers/{customer}/edit', 'edit')->name('customers.edit');
        Route::put('customers/{customer}/update', 'update')->name('customers.update');
        Route::delete('customers/{customer}/delete', 'destroy')->name('customers.destroy');

        Route::get('customers/map-data', 'mapData')->name('customers.map-data');
        Route::get('products-lists', 'list')->name('products.list');
        Route::get('products-customer/{product}/show', 'productShow')->name('customer.products.show');
        Route::post('customers/bulk-email', 'bulkEmail')->name('customers.bulk-email');

        Route::get('customers/export', 'getCustomerExport')->name('customers.export');
    });
});



Route::group(['prefix' => 'vendor', 'as' => 'vendor.', 'middleware' => ['auth', 'role:vendor', 'vendor.member']], function () {
    Route::controller(VendorController::class)->group(function () {
        Route::get('dashboard', 'dashboard')->name('dashboard');
        Route::get('logout', 'logout')->name('logout');
    });

    Route::controller(VendorProfileController::class)->group(function () {
        Route::get('profile', 'index')->name('profile');
        Route::put('update-profile', 'update')->name('profile.update');
        Route::put('password', 'updatePassword')->name('profile.password');

        Route::post('/profile/photo', 'updatePhoto')->name('profile.photo');
        Route::delete('/profile/photo', 'deletePhoto')->name('profile.photo.delete');

        Route::delete('/profile/sessions', 'destroySession')->name('sessions.destroy');
    });

    Route::controller(VendorDocumentController::class)->group(function () {
        Route::get('verified-documents', 'index')->name('documents');
        Route::get('create-documents', 'create')->name('documents.create');
        Route::post('store-documents', 'store')->name('documents.store');
        Route::get('verified-documents/{document}/show', 'show')->name('documents.show');
        Route::delete('verified-documents/{document}/delete', 'destroy')->name('documents.destroy');
    });

    Route::controller(VendorStoreController::class)->group(function () {
        Route::get('store', 'index')->name('stores');
        Route::post('edit/store', 'update')->name('stores.update');
        Route::get('store/detail', 'show')->name('stores.show');
        Route::delete('store/logo/delete', 'deleteLogo')->name('stores.delete.logo');
        Route::delete('store/banner/delete', 'deleteBanner')->name('stores.delete.banner');
    });

    Route::controller(VendorProductController::class)->group(function () {


        // API endpoints for product management
        Route::get('/products/get-subcategories', 'getSubcategories')->name('get.getSubcategories');
        Route::post('/products/{product}/toggle-status', 'toggleStatus')->name('products.toggle-status');
        Route::delete('/products/multi-image/{id}', 'deleteMultiImage')->name('products.delete-multi-image');

        Route::get('measurement-units/get-unit-details', 'getUnitDetails')->name('measurement-units.get-unit-details');

        Route::get('products', 'index')->name('products');
        Route::get('products/create', 'create')->name('products.create');
        Route::get('products/{product}', 'show')->name('products.show');
        Route::get('products/edit/{product}', 'edit')->name('products.edit');
        Route::delete('products/delete/{product}', 'destroy')->name('products.destroy');
        Route::post('products/store', 'store')->name('products.store');
        Route::put('products/update/{product}', 'update')->name('products.update');
    });

    Route::controller(VendorAdvertisementController::class)->group(function () {
        Route::get('advertisment', 'index')->name('advertisement');
        Route::get('advertisment/create', 'create')->name('advertisements.create');
        Route::get('advertisment/{advertisment}', 'show')->name('advertisements.show');
        Route::get('advertisment/{advertisment}/edit', 'edit')->name('advertisements.edit');
        Route::put('advertisment/{advertisment}', 'update')->name('advertisements.update');
        Route::delete('advertisment/{advertisment}', 'destroy')->name('advertisements.destroy');

        Route::get('advertisment/subscribe/{packageId?}', 'subscribe')->name('advertisements.subscribe');
        Route::post('advertisment/store', 'store')->name('advertisements.process-subscription');

        // call back
        Route::get('/advertisements/payment/callback',  'paymentCallback')
            ->name('advertisements.payment.callback');

        Route::get('advert/packages', 'packages')->name('advertisements.packages');
        Route::get('packages/{package}', 'showPackage')->name('advertisements.package.show');

        // Cancellation routes
        Route::get('/{advertisement}/cancel', 'showCancelForm')->name('advertisements.cancel.form');
        Route::post('/{advertisement}/cancel', 'cancel')->name('advertisements.cancel');
    });
});


// Route::group(['prefix' => 'customer', 'as' => 'customer.', 'middleware' => ['auth', 'role:user']], function () {
//     Route::controller(UserController::class)->group(function () {
//         Route::get('dashboard', 'dashboard')->name('dashboard');
//     });
// });

Route::group(['prefix' => 'customer', 'as' => 'user.', 'middleware' => ['auth', 'role:user']], function () {

    Route::controller(UserController::class)->group(function () {
        // Dashboard
        Route::get('dashboard', 'dashboard')->name('dashboard');

        // Profile Management
        Route::get('profile', 'profile')->name('profile');
        Route::put('profile', 'updateProfile')->name('profile.update');
        Route::put('profile/password', 'changePassword')->name('profile.password');

        // Address Management
        Route::get('addresses', 'addresses')->name('addresses');
        Route::post('addresses', 'storeAddress')->name('addresses.store');
        Route::put('addresses/{address}', 'updateAddress')->name('addresses.update');
        Route::delete('addresses/{address}', 'deleteAddress')->name('addresses.delete');



        // Security & Activity
        Route::get('security', 'security')->name('security');
        Route::get('activity', 'activityLog')->name('activity');

        // Notifications
        Route::get('notifications', 'notifications')->name('notifications');
        Route::post('notifications/{notification}/read', 'markNotificationAsRead')->name('notifications.read');
        Route::post('notifications/read-all', 'markAllNotificationsAsRead')->name('notifications.read-all');
    });
});
require __DIR__ . '/auth.php';
