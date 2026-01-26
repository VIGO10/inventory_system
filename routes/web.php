<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;

// 1. Root → always shows login if guest, redirects if authenticated
Route::get('/', function () {
    return view('auth.login');
})->middleware('guest');   // ← very important!

// 2. Login routes — protect with guest middleware
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [LoginController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [LoginController::class, 'register']);
});

// 3. Logout (only for authenticated users)
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// 4. Your other route groups
require __DIR__ . '/admin.php';
require __DIR__ . '/client.php';