<?php
// app/Http/Middleware/AdminMiddleware.php
// Middleware adalah filter yang berjalan sebelum request mencapai controller
// Middleware ini khusus untuk memastikan hanya admin yang bisa akses halaman admin

namespace App\Http\Middleware;

// Import class yang dibutuhkan
use Closure;  // Untuk next() function
use Illuminate\Http\Request; // Untuk handling HTTP request
use Illuminate\Support\Facades\Auth; // Untuk cek authentication
use Symfony\Component\HttpFoundation\Response; // Untuk HTTP response

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     * Method ini dijalankan setiap kali ada request yang melewati middleware ini
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return Response
     * 
     * Flow: Request -> Middleware (check) -> Controller (jika lolos) atau Redirect (jika tidak lolos)
     */
    public function handle(Request $request, Closure $next): Response
    {
        // PENGECEKAN 1: Cek apakah user sudah login atau belum
        // auth()->check() return true jika user sudah login, false jika belum
        if (!auth()->check()) {
            // Jika belum login, redirect ke halaman login
            // with('error', ...) = set flash message yang muncul 1x di halaman berikutnya
            return redirect()->route('login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        // PENGECEKAN 2: Cek apakah user yang login adalah admin
        // auth()->user() = mendapatkan object user yang sedang login
        // Cek apakah role-nya 'admin' atau bukan
        if (auth()->user()->role !== 'admin') {
            // Jika bukan admin, logout user tersebut
            auth()->logout();
            
            // Redirect ke halaman login dengan pesan error
            return redirect()->route('login')
                ->with('error', 'Akses ditolak. Hanya admin yang dapat mengakses.');
        }

        // Jika semua pengecekan lolos (user login DAN role admin):
        // Lanjutkan request ke controller dengan $next($request)
        return $next($request);
    }
}

/*
 * CARA KERJA MIDDLEWARE INI:
 * 
 * 1. User mencoba akses URL admin (misal: /admin/dashboard)
 * 2. Laravel menjalankan middleware ini SEBELUM menjalankan controller
 * 3. Middleware cek:
 *    - Apakah user sudah login? Jika belum -> redirect ke login
 *    - Apakah user adalah admin? Jika bukan -> logout & redirect ke login
 * 4. Jika lolos semua pengecekan -> request dilanjutkan ke controller
 * 5. Controller menjalankan logic dan return response
 * 
 * CARA PENGGUNAAN:
 * Di routes/web.php:
 * Route::middleware(['auth', 'admin'])->group(function() {
 *     Route::get('/admin/dashboard', [AdminController::class, 'index']);
 * });
 * 
 * Atau di Controller:
 * public function __construct() {
 *     $this->middleware('admin');
 * }
 */