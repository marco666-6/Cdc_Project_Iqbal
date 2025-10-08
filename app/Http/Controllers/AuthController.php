<?php
// app/Http/Controllers/AuthController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\ActivityLog;

class AuthController extends Controller
{
    /**
     * Display login form
     */
    public function showLoginForm()
    {
        // Redirect if already logged in
        if (Auth::check() && Auth::user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        return view('auth.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput($request->only('email'));
        }

        // Sanitize input
        $credentials = [
            'email' => trim($request->email),
            'password' => $request->password,
        ];

        // Check for empty or whitespace-only inputs
        if (empty($credentials['email']) || empty($credentials['password'])) {
            return back()
                ->withErrors(['email' => 'Email dan password tidak boleh kosong.'])
                ->withInput($request->only('email'));
        }

        // Attempt login
        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            // Check if user is admin
            if (!$user->isAdmin()) {
                Auth::logout();
                return back()
                    ->withErrors(['email' => 'Akses ditolak. Hanya admin yang dapat login.'])
                    ->withInput($request->only('email'));
            }

            // Update last login
            $user->updateLastLogin();

            // Log activity
            ActivityLog::log('Admin login', $user, 'login', [
                'ip_address' => $request->ip(),
            ]);

            // Regenerate session
            $request->session()->regenerate();

            return redirect()->intended(route('admin.dashboard'))
                ->with('success', 'Selamat datang, ' . $user->name . '!');
        }

        // Login failed
        return back()
            ->withErrors(['email' => 'Email atau password salah.'])
            ->withInput($request->only('email'));
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Log activity
            ActivityLog::log('Admin logout', $user, 'logout', [
                'ip_address' => $request->ip(),
            ]);
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Anda telah berhasil logout.');
    }

    /**
     * Show profile page
     */
    public function showProfile()
    {
        $user = Auth::user();
        return view('auth.profile', compact('user'));
    }

    /**
     * Update profile
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
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

        // Sanitize and validate input
        $name = trim($request->name);
        $email = trim($request->email);

        if (empty($name) || empty($email)) {
            return back()
                ->withErrors(['name' => 'Nama dan email tidak boleh kosong.'])
                ->withInput();
        }

        $user->update([
            'name' => $name,
            'email' => $email,
        ]);

        // Log activity
        ActivityLog::log('Profile updated', $user, 'update');

        return back()->with('success', 'Profile berhasil diperbarui.');
    }

    /**
     * Show password change form
     */
    public function showPasswordForm()
    {
        return view('auth.password');
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|string|min:6|confirmed',
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

        // Check current password
        if (!password_verify($request->current_password, $user->password)) {
            return back()
                ->withErrors(['current_password' => 'Password saat ini salah.'])
                ->withInput();
        }

        // Validate new password is not empty or whitespace
        if (empty(trim($request->new_password))) {
            return back()
                ->withErrors(['new_password' => 'Password baru tidak boleh kosong.'])
                ->withInput();
        }

        // Update password
        $user->password = $request->new_password;
        $user->save();

        // Log activity
        ActivityLog::log('Password changed', $user, 'update');

        return back()->with('success', 'Password berhasil diperbarui.');
    }
}