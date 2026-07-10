<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventHubController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MidtransCallbackController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryController; 
use App\Http\Controllers\Admin\EventController;    
use App\Http\Controllers\Admin\TransactionController; // BARU: Namespace Controller Transaksi Admin
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// 1. Katalog / Landing Page & Detail (Bisa diakses publik tanpa login)
Route::get('/', [EventHubController::class, 'index'])->name('home');
Route::get('/event/{event}', [EventHubController::class, 'show'])->name('event.detail');


// 2. Route Guest (Hanya bisa diakses jika BELUM login)
Route::middleware('guest')->group(function () {
    // Registrasi & Login User Umum
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    // Halaman & Proses Login Khusus Admin
    Route::get('/admin/login', [AuthController::class, 'showAdminLogin'])->name('admin.login');
    Route::post('/admin/login', [AuthController::class, 'adminLogin']);
});


// 3. Route Terproteksi (Harus login terlebih dahulu)
Route::middleware('auth')->group(function () {
    
    // Proses Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Rute Keranjang Belanja & Checkout (User)
    Route::get('/cart', [OrderController::class, 'viewCart'])->name('cart.view');
    Route::post('/cart/add/{event}', [OrderController::class, 'addToCart'])->name('cart.add');
    Route::delete('/cart/remove/{id}', [OrderController::class, 'removeFromCart'])->name('cart.remove');
    Route::post('/checkout', [OrderController::class, 'checkout'])->name('cart.checkout');

    // Dashboard User
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('user.dashboard');
    
    // Space untuk Superuser (Admin) - Diproteksi middleware 'admin' & Prefix '/admin'
    Route::middleware('admin')->prefix('admin')->group(function () {
        
        // Dashboard Admin
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

        // CRUD Kategori (Manual/Presisi Style)
        Route::get('/categories', [CategoryController::class, 'index'])->name('admin.categories.index');
        Route::post('/categories', [CategoryController::class, 'store'])->name('admin.categories.store');
        Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('admin.categories.update');
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('admin.categories.destroy');

        // CRUD Event
        Route::get('/events', [EventController::class, 'index'])->name('admin.events.index');
        Route::post('/events', [EventController::class, 'store'])->name('admin.events.store');
        Route::put('/events/{event}', [EventController::class, 'update'])->name('admin.events.update');
        Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('admin.events.destroy');
        
        // BARU: Rute Transaksi Admin (Otomatis mewarisi prefix 'admin/' dan name 'admin.transactions...')
        Route::get('/transactions', [TransactionController::class, 'index'])->name('admin.transactions.index');
        Route::patch('/transactions/{id}/status', [TransactionController::class, 'updateStatus'])->name('admin.transactions.update-status');
        
    });
});


// 4. Webhook / Callback Midtrans (Diletakkan di luar kelompok auth & guest)
Route::post('/midtrans/callback', [MidtransCallbackController::class, 'handleNotification']);