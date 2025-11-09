<?php
// app/Http/Controllers/AuthController.php
// Controller untuk menangani semua proses authentication (login, logout, profile, password)
// Controller adalah tempat logic/proses bisnis aplikasi

namespace App\Http\Controllers;

// Import class yang dibutuhkan
use Illuminate\Http\Request; // Untuk handling HTTP request
use Illuminate\Support\Facades\Auth; // Untuk authentication
use Illuminate\Support\Facades\Validator; // Untuk validasi input
use App\Models\ActivityLog; // Model untuk mencatat aktivitas

class AuthController extends Controller
{
    /**
     * Display login form
     * Menampilkan halaman form login
     * URL: GET /login
     */
    public function showLoginForm()
    {
        // Cek apakah user sudah login DAN adalah admin
        if (Auth::check() && Auth::user()->isAdmin()) {
            // Jika sudah login, redirect langsung ke dashboard admin
            // Tidak perlu tampilkan form login lagi
            return redirect()->route('admin.dashboard');
        }

        // Jika belum login, tampilkan view form login
        // view('auth.login') = resources/views/auth/login.blade.php
        return view('auth.login');
    }

    /**
     * Handle login request
     * Memproses data login yang dikirim dari form
     * URL: POST /login
     */
    public function login(Request $request)
    {
        // STEP 1: VALIDASI INPUT
        // Membuat validator untuk cek apakah input valid
        $validator = Validator::make($request->all(), [
            // Rules validasi:
            'email' => 'required|email',           // Email wajib diisi & format valid
            'password' => 'required|string|min:6', // Password wajib diisi & min 6 karakter
        ], [
            // Custom error messages dalam Bahasa Indonesia
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
        ]);

        // Jika validasi gagal, kembali ke halaman sebelumnya dengan error
        if ($validator->fails()) {
            return back()
                ->withErrors($validator)        // Kirim pesan error
                ->withInput($request->only('email')); // Kirim input email (bukan password)
        }

        // STEP 2: SANITIZE INPUT
        // Membersihkan input dari spasi yang tidak perlu
        $credentials = [
            'email' => trim($request->email),    // Hapus spasi di awal/akhir
            'password' => $request->password,
        ];

        // Cek apakah input kosong atau hanya spasi
        if (empty($credentials['email']) || empty($credentials['password'])) {
            return back()
                ->withErrors(['email' => 'Email dan password tidak boleh kosong.'])
                ->withInput($request->only('email'));
        }

        // STEP 3: ATTEMPT LOGIN
        // Coba login dengan credentials yang diberikan
        $remember = $request->has('remember'); // Cek apakah checkbox "Remember Me" dicentang

        // Auth::attempt() = coba login, return true jika berhasil
        // Parameter 1: array credentials (email & password)
        // Parameter 2: boolean remember (untuk fitur "Remember Me")
        if (Auth::attempt($credentials, $remember)) {
            // Login berhasil!
            $user = Auth::user(); // Ambil data user yang baru login

            // STEP 4: CEK ROLE
            // Pastikan user yang login adalah admin
            if (!$user->isAdmin()) {
                // Jika bukan admin, logout
                Auth::logout();
                return back()
                    ->withErrors(['email' => 'Akses ditolak. Hanya admin yang dapat login.'])
                    ->withInput($request->only('email'));
            }

            // STEP 5: UPDATE & LOG
            // Update waktu login terakhir
            $user->updateLastLogin();

            // Catat aktivitas login di activity log
            ActivityLog::log('Admin login', $user, 'login', [
                'ip_address' => $request->ip(), // Simpan IP address
            ]);

            // STEP 6: SECURITY
            // Regenerate session untuk mencegah session fixation attack
            $request->session()->regenerate();

            // STEP 7: REDIRECT
            // Redirect ke dashboard dengan pesan sukses
            // intended() = ke URL yang dituju sebelumnya, atau default ke dashboard
            return redirect()->intended(route('admin.dashboard'))
                ->with('success', 'Selamat datang, ' . $user->name . '!');
        }

        // Jika Auth::attempt() return false = Login gagal
        return back()
            ->withErrors(['email' => 'Email atau password salah.'])
            ->withInput($request->only('email'));
    }

    /**
     * Handle logout request
     * Memproses logout dan membersihkan session
     * URL: POST /logout
     */
    public function logout(Request $request)
    {
        // Cek apakah ada user yang login
        if (Auth::check()) {
            $user = Auth::user();

            // Catat aktivitas logout di log
            ActivityLog::log('Admin logout', $user, 'logout', [
                'ip_address' => $request->ip(),
            ]);
        }

        // Logout user
        Auth::logout();

        // Hapus semua data session
        $request->session()->invalidate();

        // Generate token CSRF baru untuk keamanan
        $request->session()->regenerateToken();

        // Redirect ke halaman login dengan pesan sukses
        return redirect()->route('login')
            ->with('success', 'Anda telah berhasil logout.');
    }

    /**
     * Show profile page
     * Menampilkan halaman profile user yang sedang login
     * URL: GET /admin/profile
     */
    public function showProfile()
    {
        $user = Auth::user(); // Ambil data user yang sedang login
        
        // Tampilkan view profile dengan data user
        // compact('user') = passing variable $user ke view
        return view('auth.profile', compact('user'));
    }

    /**
     * Update profile
     * Memproses update data profile (nama & email)
     * URL: POST /admin/profile
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user(); // Ambil data user saat ini

        // VALIDASI INPUT
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            // Email harus unik, tapi ignore ID user sendiri (boleh sama dengan email sendiri)
            'email' => 'required|email|unique:users,email,' . $user->id,
        ], [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // SANITIZE INPUT
        $name = trim($request->name);
        $email = trim($request->email);

        // Cek input kosong
        if (empty($name) || empty($email)) {
            return back()
                ->withErrors(['name' => 'Nama dan email tidak boleh kosong.'])
                ->withInput();
        }

        // UPDATE DATA
        $user->update([
            'name' => $name,
            'email' => $email,
        ]);

        // LOG ACTIVITY
        ActivityLog::log('Profile updated', $user, 'update');

        // REDIRECT dengan pesan sukses
        return back()->with('success', 'Profile berhasil diperbarui.');
    }

    /**
     * Show password change form
     * Menampilkan form ganti password
     * URL: GET /admin/password
     */
    public function showPasswordForm()
    {
        return view('auth.password');
    }

    /**
     * Update password
     * Memproses ganti password
     * URL: POST /admin/password
     */
    public function updatePassword(Request $request)
    {
        // VALIDASI INPUT
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',             // Password lama wajib
            'new_password' => 'required|string|min:6|confirmed', // Password baru min 6 & harus match dengan konfirmasi
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'new_password.required' => 'Password baru wajib diisi.',
            'new_password.min' => 'Password baru minimal 6 karakter.',
            'new_password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = Auth::user();

        // CEK PASSWORD LAMA
        // password_verify() = fungsi PHP untuk cek apakah password cocok dengan hash
        if (!password_verify($request->current_password, $user->password)) {
            return back()
                ->withErrors(['current_password' => 'Password saat ini salah.'])
                ->withInput();
        }

        // VALIDASI PASSWORD BARU tidak kosong
        if (empty(trim($request->new_password))) {
            return back()
                ->withErrors(['new_password' => 'Password baru tidak boleh kosong.'])
                ->withInput();
        }

        // UPDATE PASSWORD
        // Karena ada mutator di model User, password otomatis di-hash
        $user->password = $request->new_password;
        $user->save();

        // LOG ACTIVITY
        ActivityLog::log('Password changed', $user, 'update');

        // REDIRECT dengan sukses
        return back()->with('success', 'Password berhasil diperbarui.');
    }
}

/*
 * RINGKASAN FLOW AUTH:
 * 
 * 1. LOGIN:
 *    User isi form -> POST ke /login -> Validasi -> Auth::attempt() 
 *    -> Cek role admin -> Update last_login -> Log activity -> Redirect dashboard
 * 
 * 2. LOGOUT:
 *    User klik logout -> POST ke /logout -> Log activity 
 *    -> Auth::logout() -> Hapus session -> Redirect login
 * 
 * 3. UPDATE PROFILE:
 *    User isi form -> POST ke /admin/profile -> Validasi 
 *    -> Update name & email -> Log activity -> Redirect back
 * 
 * 4. CHANGE PASSWORD:
 *    User isi form -> POST ke /admin/password -> Validasi 
 *    -> Cek password lama -> Update password -> Log activity -> Redirect back
 */