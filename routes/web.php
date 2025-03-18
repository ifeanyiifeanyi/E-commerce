<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Vendor\VendorController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\SubcategoryController;
use App\Http\Controllers\Admin\AdminProfileController;
use App\Http\Controllers\Vendor\VendorProfileController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\Vendor\VendorDocumentController;
use App\Http\Controllers\Admin\VendorManagementController;
use App\Http\Controllers\Auth\VendorRegistrationController;
use App\Http\Controllers\Admin\ManageVendorDocumentController;

Route::get('/', function () {
    return view('welcome');
});


Route::controller(VendorRegistrationController::class)->group(function () {
    Route::get('vendor/register', 'create')->name('vendor.register.view');
    Route::post('vendor/register', 'store')->name('vendor.register');

    Route::get('vendor/login', 'login')->name('vendor.login.view');
});

// Email Verification Routes
Route::middleware('auth')->group(function () {
    Route::get('email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    Route::get('email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();

        // Redirect based on user role
        $user = $request->user();
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

    Route::controller(VendorManagementController::class)->group(function(){
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

    Route::controller(ManageVendorDocumentController::class)->group(function(){
        Route::get('vendor/documents/{user?}', 'index')->name('vendors.documents');
        Route::get('vendor/documents/{user}/create', 'create')->name('vendors.documents.create');

        Route::get('vendor/{user}/{document}/show', 'show')->name('vendors.documents.show');
        Route::patch('vendor/{user}/{document}/approve', 'approve')->name('vendors.documents.approve');
        Route::patch('vendor/{user}/{document}/reject', 'reject')->name('vendors.documents.reject');
        Route::get('vendor/{user}/{document}/delete', 'delete')->name('vendors.documents.destroy');


        Route::post('vendor/documents/{user}', 'store')->name('vendors.documents.store');

    });
});



Route::group(['prefix' => 'vendor', 'as' => 'vendor.', 'middleware' => ['auth', 'role:vendor']], function () {
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

    Route::controller(VendorDocumentController::class)->group(function(){
        Route::get('verified-documents', 'index')->name('documents');
        Route::get('create-documents', 'create')->name('documents.create');
        Route::post('store-documents', 'store')->name('documents.store');
        Route::get('verified-documents/{document}/show', 'show')->name('documents.show');
        Route::delete('verified-documents/{document}/delete', 'destroy')->name('documents.destroy');

    });
});


Route::group(['prefix' => 'customer', 'as' => 'customer.', 'middleware' => ['auth', 'role:user']], function () {
    Route::controller(UserController::class)->group(function () {
        Route::get('dashboard', 'dashboard')->name('dashboard');
    });
});
require __DIR__ . '/auth.php';
