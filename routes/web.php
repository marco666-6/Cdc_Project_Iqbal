<?php
// routes/web.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| Public Routes (Frontend)
|--------------------------------------------------------------------------
*/

// Homepage (KF-01)
Route::get('/', [HomeController::class, 'index'])->name('home');

// Career Opportunities (KF-02)
Route::get('/lowongan-kerja', [HomeController::class, 'lowonganKerja'])->name('lowongan-kerja');
Route::get('/lowongan-kerja/{id}', [HomeController::class, 'lowonganKerjaDetail'])->name('lowongan-kerja.detail');

// Internship & MBKM Programs (KF-03)
Route::get('/program-magang', [HomeController::class, 'programMagang'])->name('program-magang');
Route::get('/program-magang/{id}', [HomeController::class, 'programMagangDetail'])->name('program-magang.detail');

// News/Newsletter (KF-04)
Route::get('/berita', [HomeController::class, 'berita'])->name('berita');
Route::get('/berita/{slug}', [HomeController::class, 'beritaDetail'])->name('berita.detail');

// About Page (KF-05)
Route::get('/tentang', [HomeController::class, 'tentang'])->name('tentang');

// Contact Page (KF-06)
Route::get('/kontak', [HomeController::class, 'kontak'])->name('kontak');

// Newsletter Subscription (KF-08)
Route::post('/newsletter/subscribe', [HomeController::class, 'subscribeNewsletter'])->name('newsletter.subscribe');
Route::get('/newsletter/unsubscribe/{email}', [HomeController::class, 'unsubscribeNewsletter'])->name('newsletter.unsubscribe');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    // Login (KF-09)
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
});

Route::middleware('auth')->group(function () {
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Profile Management
    Route::get('/profile', [AuthController::class, 'showProfile'])->name('profile');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::get('/profile/password', [AuthController::class, 'showPasswordForm'])->name('profile.password');
    Route::put('/profile/password', [AuthController::class, 'updatePassword'])->name('profile.password');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard (KF-10)
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Lowongan Kerja Management (KF-11)
    Route::prefix('lowongan-kerja')->name('lowongan-kerja.')->group(function () {
        Route::get('/', [AdminController::class, 'lowonganKerjaIndex'])->name('index');
        Route::get('/create', [AdminController::class, 'lowonganKerjaCreate'])->name('create');
        Route::post('/', [AdminController::class, 'lowonganKerjaStore'])->name('store');
        Route::get('/{id}/edit', [AdminController::class, 'lowonganKerjaEdit'])->name('edit');
        Route::put('/{id}', [AdminController::class, 'lowonganKerjaUpdate'])->name('update');
        Route::delete('/{id}', [AdminController::class, 'lowonganKerjaDestroy'])->name('destroy');
        
        // Bulk Actions
        Route::post('/bulk-status', [AdminController::class, 'lowonganKerjaBulkStatus'])->name('bulk-status');
        Route::post('/bulk-delete', [AdminController::class, 'lowonganKerjaBulkDelete'])->name('bulk-delete');
    });
    
    // Program Magang Management (KF-12)
    Route::prefix('program-magang')->name('program-magang.')->group(function () {
        Route::get('/', [AdminController::class, 'programMagangIndex'])->name('index');
        Route::get('/create', [AdminController::class, 'programMagangCreate'])->name('create');
        Route::post('/', [AdminController::class, 'programMagangStore'])->name('store');
        Route::get('/{id}/edit', [AdminController::class, 'programMagangEdit'])->name('edit');
        Route::put('/{id}', [AdminController::class, 'programMagangUpdate'])->name('update');
        Route::delete('/{id}', [AdminController::class, 'programMagangDestroy'])->name('destroy');
        
        // Bulk Actions
        Route::post('/bulk-status', [AdminController::class, 'programMagangBulkStatus'])->name('bulk-status');
        Route::post('/bulk-delete', [AdminController::class, 'programMagangBulkDelete'])->name('bulk-delete');
    });
    
    // Berita/Newsletter Management (KF-13)
    Route::prefix('berita')->name('berita.')->group(function () {
        Route::get('/', [AdminController::class, 'beritaIndex'])->name('index');
        Route::get('/create', [AdminController::class, 'beritaCreate'])->name('create');
        Route::post('/', [AdminController::class, 'beritaStore'])->name('store');
        Route::get('/{id}/edit', [AdminController::class, 'beritaEdit'])->name('edit');
        Route::put('/{id}', [AdminController::class, 'beritaUpdate'])->name('update');
        Route::delete('/{id}', [AdminController::class, 'beritaDestroy'])->name('destroy');
        
        // Bulk Actions
        Route::post('/bulk-status', [AdminController::class, 'beritaBulkStatus'])->name('bulk-status');
        Route::post('/bulk-delete', [AdminController::class, 'beritaBulkDelete'])->name('bulk-delete');
        Route::post('/bulk-featured', [AdminController::class, 'beritaBulkFeatured'])->name('bulk-featured');
    });
    
    // Tentang Management (KF-14)
    Route::prefix('tentang')->name('tentang.')->group(function () {
        Route::get('/edit', [AdminController::class, 'tentangEdit'])->name('edit');
        Route::put('/update', [AdminController::class, 'tentangUpdate'])->name('update');
    });
    
    // Kontak Management (KF-15)
    Route::prefix('kontak')->name('kontak.')->group(function () {
        Route::get('/edit', [AdminController::class, 'kontakEdit'])->name('edit');
        Route::put('/update', [AdminController::class, 'kontakUpdate'])->name('update');
    });
    
    // Newsletter Subscribers Management
    Route::prefix('newsletter')->name('newsletter.')->group(function () {
        Route::get('/', [AdminController::class, 'newsletterIndex'])->name('index');
        Route::delete('/{id}', [AdminController::class, 'newsletterDestroy'])->name('destroy');
        Route::post('/bulk-delete', [AdminController::class, 'newsletterBulkDelete'])->name('bulk-delete');
    });
    
    // Activity Logs
    Route::prefix('activity-logs')->name('activity-logs.')->group(function () {
        Route::get('/', [AdminController::class, 'activityLogs'])->name('index');
        Route::post('/clear', [AdminController::class, 'activityLogsClear'])->name('clear');
    });
});

/*
|--------------------------------------------------------------------------
| Fallback Route
|--------------------------------------------------------------------------
*/

Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});