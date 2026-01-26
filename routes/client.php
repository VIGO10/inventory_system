<?php

use Illuminate\Support\Facades\Route;

// All routes in this file are prefixed with /client or /my-account
Route::prefix('client')->name('client.')->middleware(['auth'])->group(function () {

    Route::get('/dashboard', function () {
        return view('client.dashboard');
    })->name('dashboard');

    Route::get('/profile', function () {
        return view('client.profile');
    })->name('profile');

    Route::get('/orders', function () {
        return view('client.orders.index');
    })->name('orders.index');

});