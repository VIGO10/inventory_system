<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\CatalogSupplierController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\OtherCostController;

Route::middleware('guest')->group(function () {
    Route::redirect('/admin', '/admin/dashboard');
    Route::redirect('/admin/user', '/admin/dashboard');
    Route::redirect('/admin/vendor', '/admin/dashboard');
    Route::redirect('/admin/supplier', '/admin/dashboard');
    Route::redirect('/admin/catalog', '/admin/dashboard');
    Route::redirect('/admin/transaction', '/admin/dashboard');
    Route::redirect('/admin/other-cost', '/admin/dashboard');
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
            Route::get('/{vendor:slug}/edit', 'editVendor')
                ->name('edit');

            // Update vendor
            Route::put('/{vendor:slug}', '_editVendor')
                ->name('update');

            // Delete vendor
            Route::delete('/{vendor:slug}', '_deleteVendor')
                ->name('delete');
        });
    });

    Route::prefix('supplier')->name('supplier.')->group(function(){
        Route::controller(SupplierController::class)->group(function(){
            // List all suppliers
            Route::get('/index', 'index')
                ->name('index');

            // Show create form
            Route::get('/create', 'createNewSupplier')
                ->name('create');

            // Create supplier
            Route::post('/store', '_createNewSupplier')
                ->name('store');

            // Show edit form
            Route::get('/{supplier:slug}/edit', 'editSupplier')
                ->name('edit');

            // Update supplier
            Route::put('/{supplier:slug}', '_editSupplier')
                ->name('update');

            // Delete supplier
            Route::delete('/{supplier:slug}', '_deletesupplier')
                ->name('delete');
        });

        Route::prefix('catalog')->name('catalog.')->group(function(){
            Route::controller(CatalogSupplierController::class)->group(function(){
                // Show supplier catalog
                Route::get('/{supplier:slug}/index', 'index')
                    ->name('index');

                // Show create form
                Route::get('/{supplier:slug}/create', 'createNewCatalogSupplier')
                    ->name('create');

                // Create catalog supplier
                Route::post('/{supplier:slug}/store', '_createNewCatalogSupplier')
                    ->name('store');

                // Show edit form
                Route::get('/{supplier:slug}/catalog/{catalogSupplier}/edit', 'editCatalogSupplier')
                    ->name('edit');

                // Update catalog supplier
                Route::put('/{supplier:slug}/catalog/{catalogSupplier}', '_editCatalogSupplier')
                    ->name('update');

                // Toggle Active/Inactive status
                Route::post('/{supplier:slug}/catalog/{catalogSupplier}/toggle', '_toggleCatalogSupplierStatus')
                    ->name('toggle');

                // Delete catalog supplier
                Route::delete('/{supplier:slug}/catalog/{catalogSupplier}', '_deleteCatalogSupplier')
                    ->name('delete');

                // Get catalogs by supplier (AJAX)
                Route::get('/getBySupplier/{supplierId}', 'getBySupplier')
                    ->name('getBySupplier');
            });
        });
    });

    Route::prefix('catalog')->name('catalog.')->group(function(){
        Route::controller(CatalogController::class)->group(function(){
            // Show catalog
            Route::get('index', 'index')
                ->name('index');

            // Show create form
            Route::get('create', 'createNewCatalog')
                ->name('create');

            // Create catalog
            Route::post('store', '_createNewCatalog')
                ->name('store');

            // Show edit form
            Route::get('catalog/{catalog}/edit', 'editCatalog')
                ->name('edit');

            // Update catalog
            Route::put('catalog/{catalog}', '_editCatalog')
                ->name('update');

            // Toggle Active/Inactive status
            Route::post('catalog/{catalog}/toggle', '_toggleCatalogStatus')
                ->name('toggle');

            // Delete catalog
            Route::delete('catalog/{catalog}', '_deleteCatalog')
                ->name('delete');

            // Get catalogs (AJAX)
            Route::get('/getAvailableCatalog', 'getAvailableCatalog')
                ->name('getAvailableCatalog');
        });
    });

    Route::prefix('transaction')->name('transaction.')->group(function(){
        Route::controller(TransactionController::class)->group(function(){
            // List all transactions
            Route::get('/index', 'index')
                ->name('index');
            
            Route::prefix('inbound')->name('inbound.')->group(function(){
                // Show inbound transaction
                Route::get('/{transaction:reference_number}/detail', 'detailInboundTransaction')
                    ->name('detail');

                // Show create form
                Route::get('/create', 'createNewInboundTransaction')
                    ->name('create');

                // Create inbound transaction
                Route::post('/store', '_createNewInboundTransaction')
                    ->name('store');

                // Show edit form
                Route::get('/{transaction:reference_number}/edit', 'editInboundTransaction')
                    ->name('edit');

                // Update inbound transaction
                Route::put('/{transaction:reference_number}', '_editInboundTransaction')
                    ->name('update');

                // Publish inbound transaction
                Route::post('/{transaction:reference_number}/publish', '_publishInboundTransaction')
                    ->name('publish');

                // Complete inbound transaction
                Route::post('/{transaction:reference_number}/complete', '_completeInboundTransaction')
                    ->name('complete');

                // Delete inbound transaction
                Route::delete('/{transaction:reference_number}', '_deleteInboundTransaction')
                    ->name('delete');

                // Set inbound transaction paid
                Route::post('/{transaction:reference_number}/set-paid', '_setInboundTransactionPaid')
                    ->name('paid');
            });

            Route::prefix('outbound')->name('outbound.')->group(function(){
                // Show outbound transaction
                Route::get('/{transaction:reference_number}/detail', 'detailOutboundTransaction')
                    ->name('detail');

                // Show create form
                Route::get('/create', 'createNewOutboundTransaction')
                    ->name('create');

                // Create Outbound transaction
                Route::post('/store', '_createNewOutboundTransaction')
                    ->name('store');

                // Show edit form
                Route::get('/{transaction:reference_number}/edit', 'editOutboundTransaction')
                    ->name('edit');

                // Update Outbound transaction
                Route::put('/{transaction:reference_number}', '_editOutboundTransaction')
                    ->name('update');

                // Publish Outbound transaction
                Route::post('/{transaction:reference_number}/publish', '_publishOutboundTransaction')
                    ->name('publish');

                // Complete Outbound transaction
                Route::post('/{transaction:reference_number}/complete', '_completeOutboundTransaction')
                    ->name('complete');

                // Delete Outbound transaction
                Route::delete('/{transaction:reference_number}', '_deleteOutboundTransaction')
                    ->name('delete');

                // Set Outbound transaction paid
                Route::post('/{transaction:reference_number}/set-paid', '_setOutboundTransactionPaid')
                    ->name('paid');
            });
        });
    });

    Route::prefix('other-cost')->name('other-cost.')->group(function(){
        Route::controller(OtherCostController::class)->group(function(){
            // Show report
            Route::get('index', 'index')
                ->name('index');

            // Show create form
            Route::get('create', 'createNewOtherCost')
                ->name('create');

            // Create other cost
            Route::post('store', '_createNewOtherCost')
                ->name('store');

            // Delete other cost
            Route::delete('other-cost/{other_cost}', '_deleteOtherCost')
                ->name('delete');
        });
    });
});