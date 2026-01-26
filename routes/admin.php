<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\VendorController;

Route::middleware('guest')->group(function () {
    Route::redirect('/admin', '/admin/dashboard');
    Route::redirect('/admin/user', '/admin/dashboard');
    Route::redirect('/admin/vendor', '/admin/dashboard');
});

// All routes in this file are prefixed with /admin
Route::prefix('admin')->name('admin.')->middleware(['auth', 'PreventBackHistory'])->group(function () {

    Route::get('/dashboard', [AdminController::class, 'showDashboard'])->name('dashboard');

    Route::prefix('user')->name('user.')->group(function(){
        Route::controller(AdminController::class)->group(function(){
            // List all users
            Route::get('/index', 'index')
                ->name('index');
            
            // Show edit form
            Route::post('/{user}/verify', '_verifyUser')
                ->name('verify');

            // Delete user
            Route::delete('/{user}', '_deleteUser')
                ->name('delete');
        });
    });

    Route::prefix('vendor')->name('vendor.')->group(function(){
        Route::controller(VendorController::class)->group(function(){
            // List all vendors
            Route::get('/index', 'index')
                ->name('index');

            // Show create form
            Route::get('/create', 'createNewVendor')
                ->name('create');

            // Create vendor
            Route::post('/store', '_createNewVendor')
                ->name('store');

            // Show edit form
            Route::get('/{vendor}/edit', 'editVendor')
                ->name('edit');

            // Delete vendor
            Route::delete('/{vendor}', '_deleteVendor')
                ->name('delete');
        });
    });
});