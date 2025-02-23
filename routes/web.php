<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminProfileController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Vendor\VendorController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// dashboard auth invokable
Route::get('dashboard', DashboardController::class)->middleware(['auth', 'verified'])->name('dashboard');

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => ['auth', 'role:admin'] ], function(){
    Route::controller(AdminController::class)->group(function(){
        Route::get('dashboard', 'dashboard')->name('dashboard');
        Route::get('logout', 'logout')->name('logout');
    });

    Route::controller(AdminProfileController::class)->group(function(){
        Route::get('profile', 'index')->name('profile');
        Route::put('update-profile', 'update')->name('profile.update');
        Route::put('password', 'updatePassword')->name('profile.password');

        Route::post('/profile/photo', 'updatePhoto')->name('profile.photo');
        Route::delete('/profile/photo', 'deletePhoto')->name('profile.photo.delete');
        Route::delete('/profile/sessions', 'logoutSession')->name('profile.logout-session');
    });

    Route::controller(BrandController::class)->group(function(){
        Route::get('brands', 'index')->name('brands');
        Route::get('create', 'create')->name('brands.create');
        Route::post('store', 'store')->name('brands.store');
        Route::get('edit/{brand}', 'edit')->name('brands.edit');
        Route::put('update/{brand}', 'update')->name('brands.update');
        Route::delete('destroy/{brand}/brand', 'destroy')->name('brands.destroy');
        Route::patch('status/{brand}/toggle', 'toggleStatus')->name('brands.toggle-status');
    });
});



Route::group(['prefix' => 'vendor', 'as' => 'vendor.', 'middleware' => ['auth', 'role:vendor'] ], function(){
    Route::controller(VendorController::class)->group(function(){
        Route::get('dashboard', 'dashboard')->name('dashboard');
        Route::get('logout', 'logout')->name('logout');
    });
});


Route::group(['prefix' => 'customer', 'as' => 'customer.', 'middleware' => ['auth', 'role:user'] ], function(){
    Route::controller(UserController::class)->group(function(){
        Route::get('dashboard', 'dashboard')->name('dashboard');
    });
});
require __DIR__.'/auth.php';
