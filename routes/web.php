<?php
// routes/web.php
// ============================================================================
// PENJELASAN FILE INI:
// File ini adalah Routes - tempat mendefinisikan semua URL aplikasi
// Routes = peta jalan aplikasi, menghubungkan URL dengan Controller
// Format: Route::method('url', [Controller::class, 'function'])->name('nama_route');
// ============================================================================

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| Public Routes (Frontend)
|--------------------------------------------------------------------------
| Routes yang bisa diakses oleh semua orang (tidak perlu login)
| Ini adalah halaman-halaman yang dilihat pengunjung biasa
*/

// ========================================================================
// HOMEPAGE (KF-01)
// ========================================================================
// Route::get() = HTTP method GET (untuk menampilkan halaman)
// '/' = URL root (homepage, misalnya: https://website.com/)
// [HomeController::class, 'index'] = panggil method index di HomeController
// ->name('home') = kasih nama route 'home' (untuk dipanggil di view)
Route::get('/', [HomeController::class, 'index'])->name('home');

// ========================================================================
// CAREER OPPORTUNITIES - LOWONGAN KERJA (KF-02)
// ========================================================================
// Route untuk halaman daftar lowongan kerja
// URL: https://website.com/lowongan-kerja
Route::get('/lowongan-kerja', [HomeController::class, 'lowonganKerja'])->name('lowongan-kerja');

// Route untuk halaman detail lowongan kerja
// {id} = parameter dinamis, akan diganti dengan ID lowongan
// Contoh URL: https://website.com/lowongan-kerja/123
// 123 akan diterima sebagai parameter $id di method lowonganKerjaDetail()
Route::get('/lowongan-kerja/{id}', [HomeController::class, 'lowonganKerjaDetail'])->name('lowongan-kerja.detail');

// ========================================================================
// INTERNSHIP & MBKM PROGRAMS - PROGRAM MAGANG (KF-03)
// ========================================================================
// Route untuk halaman daftar program magang
Route::get('/program-magang', [HomeController::class, 'programMagang'])->name('program-magang');

// Route untuk halaman detail program magang
// {id} = ID program yang akan ditampilkan
Route::get('/program-magang/{id}', [HomeController::class, 'programMagangDetail'])->name('program-magang.detail');

// ========================================================================
// NEWS/NEWSLETTER - BERITA (KF-04)
// ========================================================================
// Route untuk halaman daftar berita
Route::get('/berita', [HomeController::class, 'berita'])->name('berita');

// Route untuk halaman detail berita
// {slug} = slug berita (bukan ID), lebih SEO friendly
// Contoh URL: https://website.com/berita/cara-membuat-website-laravel
Route::get('/berita/{slug}', [HomeController::class, 'beritaDetail'])->name('berita.detail');

// ========================================================================
// ABOUT PAGE - TENTANG (KF-05)
// ========================================================================
// Route untuk halaman tentang (sejarah, visi, misi, tujuan)
Route::get('/tentang', [HomeController::class, 'tentang'])->name('tentang');

// ========================================================================
// CONTACT PAGE - KONTAK (KF-06)
// ========================================================================
// Route untuk halaman kontak (alamat, telepon, email, map)
Route::get('/kontak', [HomeController::class, 'kontak'])->name('kontak');

// ========================================================================
// NEWSLETTER SUBSCRIPTION (KF-08)
// ========================================================================
// Route::post() = HTTP method POST (untuk mengirim/menyimpan data)
// URL: /newsletter/subscribe
// Ini adalah endpoint untuk form subscription newsletter
Route::post('/newsletter/subscribe', [HomeController::class, 'subscribeNewsletter'])->name('newsletter.subscribe');

// Route untuk unsubscribe newsletter
// {email} = email subscriber yang ingin berhenti
// Contoh URL: https://website.com/newsletter/unsubscribe/user@email.com
Route::get('/newsletter/unsubscribe/{email}', [HomeController::class, 'unsubscribeNewsletter'])->name('newsletter.unsubscribe');

// ========================================================================
// REKAP BORANG/MAGANG (NEW)
// ========================================================================
Route::get('/rekap/magang', [HomeController::class, 'rekapMagang'])->name('rekap.magang');

// ========================================================================
// REKAP BORANG/MBKM (NEW)
// ========================================================================
Route::get('/rekap/mbkm', [HomeController::class, 'rekapMbkm'])->name('rekap.mbkm');

// ========================================================================
// TRACER STUDY (NEW)
// ========================================================================
Route::get('/tracer-study', [HomeController::class, 'tracerStudy'])->name('tracer-study');
/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
| Routes untuk login, logout, dan profile management
*/

// ========================================================================
// GUEST ROUTES (HANYA UNTUK YANG BELUM LOGIN)
// ========================================================================
// middleware('guest') = hanya bisa diakses jika belum login
// Jika sudah login, akan di-redirect ke dashboard
Route::middleware('guest')->group(function () {
    // LOGIN (KF-09)
    // GET = menampilkan form login
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    
    // POST = memproses form login (submit username & password)
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
});

// ========================================================================
// AUTH ROUTES (HANYA UNTUK YANG SUDAH LOGIN)
// ========================================================================
// middleware('auth') = hanya bisa diakses jika sudah login
// Jika belum login, akan di-redirect ke halaman login
Route::middleware('auth')->group(function () {
    // LOGOUT
    // POST = untuk keamanan, logout menggunakan POST bukan GET
    // Kenapa POST? Untuk mencegah CSRF attack
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // PROFILE MANAGEMENT
    // GET = menampilkan halaman profile
    Route::get('/profile', [AuthController::class, 'showProfile'])->name('profile');
    
    // PUT = update data profile (nama, email, dll)
    // PUT/PATCH digunakan untuk update data yang sudah ada
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
    
    // GET = menampilkan form ubah password
    Route::get('/profile/password', [AuthController::class, 'showPasswordForm'])->name('profile.password');
    
    // PUT = update password
    Route::put('/profile/password', [AuthController::class, 'updatePassword'])->name('profile.password.update');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
| Routes untuk halaman admin/dashboard (backend)
| Hanya bisa diakses oleh user dengan role admin
*/

// ========================================================================
// ADMIN MIDDLEWARE GROUP
// ========================================================================
// middleware(['auth', 'admin']) = harus login DAN harus role admin
// prefix('admin') = semua URL dimulai dengan /admin
//   Contoh: /admin/dashboard, /admin/lowongan-kerja, dll
// name('admin.') = semua nama route dimulai dengan 'admin.'
//   Contoh: admin.dashboard, admin.lowongan-kerja.index, dll
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // ========================================================================
    // DASHBOARD (KF-10)
    // ========================================================================
    // URL: /admin/dashboard
    // Route name: admin.dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // ========================================================================
    // LOWONGAN KERJA MANAGEMENT (KF-11)
    // ========================================================================
    // prefix('lowongan-kerja') = URL jadi /admin/lowongan-kerja
    // name('lowongan-kerja.') = nama route jadi admin.lowongan-kerja.*
    Route::prefix('lowongan-kerja')->name('lowongan-kerja.')->group(function () {
        // INDEX = Daftar semua lowongan
        // GET /admin/lowongan-kerja
        // Route name: admin.lowongan-kerja.index
        Route::get('/', [AdminController::class, 'lowonganKerjaIndex'])->name('index');
        
        // CREATE = Form tambah lowongan baru
        // GET /admin/lowongan-kerja/create
        // Route name: admin.lowongan-kerja.create
        Route::get('/create', [AdminController::class, 'lowonganKerjaCreate'])->name('create');
        
        // STORE = Simpan lowongan baru ke database
        // POST /admin/lowongan-kerja
        // Route name: admin.lowongan-kerja.store
        Route::post('/', [AdminController::class, 'lowonganKerjaStore'])->name('store');
        
        // EDIT = Form edit lowongan
        // GET /admin/lowongan-kerja/{id}/edit
        // {id} = ID lowongan yang akan diedit
        // Route name: admin.lowongan-kerja.edit
        Route::get('/{id}/edit', [AdminController::class, 'lowonganKerjaEdit'])->name('edit');
        
        // UPDATE = Simpan perubahan lowongan
        // PUT /admin/lowongan-kerja/{id}
        // PUT = method untuk update data yang sudah ada
        // Route name: admin.lowongan-kerja.update
        Route::put('/{id}', [AdminController::class, 'lowonganKerjaUpdate'])->name('update');
        
        // DESTROY = Hapus lowongan
        // DELETE /admin/lowongan-kerja/{id}
        // DELETE = method untuk hapus data
        // Route name: admin.lowongan-kerja.destroy
        Route::delete('/{id}', [AdminController::class, 'lowonganKerjaDestroy'])->name('destroy');
        
        // ========================================================================
        // BULK ACTIONS (AKSI MASSAL)
        // ========================================================================
        // Bulk = aksi yang dilakukan ke banyak data sekaligus
        
        // BULK STATUS = Update status banyak lowongan sekaligus
        // POST /admin/lowongan-kerja/bulk-status
        // Contoh: aktifkan/nonaktifkan 10 lowongan sekaligus
        Route::post('/bulk-status', [AdminController::class, 'lowonganKerjaBulkStatus'])->name('bulk-status');
        
        // BULK DELETE = Hapus banyak lowongan sekaligus
        // POST /admin/lowongan-kerja/bulk-delete
        // Contoh: hapus 5 lowongan yang dipilih
        Route::post('/bulk-delete', [AdminController::class, 'lowonganKerjaBulkDelete'])->name('bulk-delete');
    });
    
    // ========================================================================
    // PROGRAM MAGANG MANAGEMENT (KF-12)
    // ========================================================================
    // Struktur sama persis dengan Lowongan Kerja
    Route::prefix('program-magang')->name('program-magang.')->group(function () {
        // INDEX = Daftar semua program
        Route::get('/', [AdminController::class, 'programMagangIndex'])->name('index');
        
        // CREATE = Form tambah program baru
        Route::get('/create', [AdminController::class, 'programMagangCreate'])->name('create');
        
        // STORE = Simpan program baru
        Route::post('/', [AdminController::class, 'programMagangStore'])->name('store');
        
        // EDIT = Form edit program
        Route::get('/{id}/edit', [AdminController::class, 'programMagangEdit'])->name('edit');
        
        // UPDATE = Simpan perubahan program
        Route::put('/{id}', [AdminController::class, 'programMagangUpdate'])->name('update');
        
        // DESTROY = Hapus program
        Route::delete('/{id}', [AdminController::class, 'programMagangDestroy'])->name('destroy');
        
        // BULK ACTIONS
        Route::post('/bulk-status', [AdminController::class, 'programMagangBulkStatus'])->name('bulk-status');
        Route::post('/bulk-delete', [AdminController::class, 'programMagangBulkDelete'])->name('bulk-delete');
    });
    
    // ========================================================================
    // BERITA/NEWSLETTER MANAGEMENT (KF-13)
    // ========================================================================
    Route::prefix('berita')->name('berita.')->group(function () {
        // INDEX = Daftar semua berita
        Route::get('/', [AdminController::class, 'beritaIndex'])->name('index');
        
        // CREATE = Form tambah berita baru
        Route::get('/create', [AdminController::class, 'beritaCreate'])->name('create');
        
        // STORE = Simpan berita baru
        Route::post('/', [AdminController::class, 'beritaStore'])->name('store');
        
        // EDIT = Form edit berita
        Route::get('/{id}/edit', [AdminController::class, 'beritaEdit'])->name('edit');
        
        // UPDATE = Simpan perubahan berita
        Route::put('/{id}', [AdminController::class, 'beritaUpdate'])->name('update');
        
        // DESTROY = Hapus berita
        Route::delete('/{id}', [AdminController::class, 'beritaDestroy'])->name('destroy');
        
        // BULK ACTIONS
        // Bulk status = publish/draft banyak berita sekaligus
        Route::post('/bulk-status', [AdminController::class, 'beritaBulkStatus'])->name('bulk-status');
        
        // Bulk delete = hapus banyak berita sekaligus
        Route::post('/bulk-delete', [AdminController::class, 'beritaBulkDelete'])->name('bulk-delete');
        
        // Bulk featured = set/unset featured banyak berita sekaligus
        Route::post('/bulk-featured', [AdminController::class, 'beritaBulkFeatured'])->name('bulk-featured');
    });
    
    // ========================================================================
    // TENTANG MANAGEMENT (KF-14)
    // ========================================================================
    // Hanya ada EDIT dan UPDATE (tidak ada create/delete)
    // Karena halaman tentang hanya 1 record saja
    Route::prefix('tentang')->name('tentang.')->group(function () {
        // EDIT = Form edit halaman tentang
        // GET /admin/tentang/edit
        Route::get('/edit', [AdminController::class, 'tentangEdit'])->name('edit');
        
        // UPDATE = Simpan perubahan halaman tentang
        // PUT /admin/tentang/update
        Route::put('/update', [AdminController::class, 'tentangUpdate'])->name('update');
    });
    
    // ========================================================================
    // KONTAK MANAGEMENT (KF-15)
    // ========================================================================
    // Struktur sama dengan Tentang (hanya edit & update)
    Route::prefix('kontak')->name('kontak.')->group(function () {
        // EDIT = Form edit halaman kontak
        Route::get('/edit', [AdminController::class, 'kontakEdit'])->name('edit');
        
        // UPDATE = Simpan perubahan halaman kontak
        Route::put('/update', [AdminController::class, 'kontakUpdate'])->name('update');
    });
    
    // ========================================================================
    // NEWSLETTER SUBSCRIBERS MANAGEMENT
    // ========================================================================
    // Untuk mengelola daftar subscriber newsletter
    Route::prefix('newsletter')->name('newsletter.')->group(function () {
        // INDEX = Daftar semua subscriber
        Route::get('/', [AdminController::class, 'newsletterIndex'])->name('index');
        
        // DESTROY = Hapus subscriber
        Route::delete('/{id}', [AdminController::class, 'newsletterDestroy'])->name('destroy');
        
        // BULK DELETE = Hapus banyak subscriber sekaligus
        Route::post('/bulk-delete', [AdminController::class, 'newsletterBulkDelete'])->name('bulk-delete');
    });
    
    // ========================================================================
    // ACTIVITY LOGS
    // ========================================================================
    // Untuk melihat log aktivitas admin (siapa melakukan apa, kapan)
    Route::prefix('activity-logs')->name('activity-logs.')->group(function () {
        // INDEX = Daftar semua log aktivitas
        Route::get('/', [AdminController::class, 'activityLogs'])->name('index');
        
        // CLEAR = Hapus log lama (untuk membersihkan database)
        // POST karena ini adalah action yang mengubah data
        Route::post('/clear', [AdminController::class, 'activityLogsClear'])->name('clear');
    });
});

/*
|--------------------------------------------------------------------------
| Fallback Route
|--------------------------------------------------------------------------
| Route ini akan dijalankan jika tidak ada route yang cocok
| Digunakan untuk menampilkan halaman 404 (Not Found)
*/

// fallback() = route yang akan dipanggil jika semua route lain tidak match
Route::fallback(function () {
    // response()->view() = return view dengan status code
    // 'errors.404' = view file di resources/views/errors/404.blade.php
    // [] = data kosong
    // 404 = HTTP status code Not Found
    return response()->view('errors.404', [], 404);
});

// ============================================================================
// PENJELASAN HTTP METHODS:
// ============================================================================
// GET    = Mengambil/menampilkan data (READ)
// POST   = Mengirim/menyimpan data baru (CREATE)
// PUT    = Update data yang sudah ada (UPDATE)
// DELETE = Menghapus data (DELETE)
// PATCH  = Update sebagian data (mirip PUT)
//
// CRUD = Create, Read, Update, Delete
// ============================================================================

// ============================================================================
// PENJELASAN ROUTE NAMING CONVENTION:
// ============================================================================
// Nama route mengikuti pattern: resource.action
// Contoh:
// - admin.lowongan-kerja.index    = daftar lowongan
// - admin.lowongan-kerja.create   = form tambah lowongan
// - admin.lowongan-kerja.store    = simpan lowongan baru
// - admin.lowongan-kerja.edit     = form edit lowongan
// - admin.lowongan-kerja.update   = simpan perubahan lowongan
// - admin.lowongan-kerja.destroy  = hapus lowongan
//
// Keuntungan route naming:
// 1. Mudah dipanggil di view: route('admin.lowongan-kerja.index')
// 2. Mudah di-maintain (jika URL berubah, tinggal ubah di route saja)
// 3. Lebih readable dan terstruktur
// ============================================================================

// ============================================================================
// PENJELASAN MIDDLEWARE:
// ============================================================================
// Middleware = filter yang dijalankan sebelum request masuk ke controller
// 
// guest     = hanya untuk yang belum login
// auth      = hanya untuk yang sudah login
// admin     = hanya untuk user dengan role admin
//
// Middleware bisa di-chain: ['auth', 'admin'] = harus login DAN harus admin
// ============================================================================

// ============================================================================
// PENJELASAN ROUTE GROUPING:
// ============================================================================
// Route::group() = mengelompokkan beberapa route dengan atribut yang sama
// 
// Atribut yang bisa digunakan:
// - middleware()  = middleware untuk semua route di group
// - prefix()      = prefix URL untuk semua route di group
// - name()        = prefix nama route untuk semua route di group
// - namespace()   = namespace controller untuk semua route di group
//
// Keuntungan grouping:
// 1. Mengurangi duplikasi code
// 2. Lebih terstruktur dan mudah dibaca
// 3. Mudah apply perubahan (tinggal ubah di group saja)
// ============================================================================