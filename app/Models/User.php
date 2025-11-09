<?php
// app/Models/User.php
// File ini adalah Model User yang menangani data pengguna dalam aplikasi
// Model ini merepresentasikan tabel 'users' di database

namespace App\Models;

// Import class-class yang dibutuhkan dari Laravel
use Illuminate\Database\Eloquent\Factories\HasFactory; // Untuk membuat data dummy/testing
use Illuminate\Database\Eloquent\SoftDeletes; // Untuk soft delete (data tidak benar-benar dihapus)
use Illuminate\Foundation\Auth\User as Authenticatable; // Base class untuk user yang bisa login
use Illuminate\Notifications\Notifiable; // Untuk mengirim notifikasi
use Illuminate\Support\Facades\Hash; // Untuk enkripsi password

class User extends Authenticatable
{
    // Menggunakan trait-trait Laravel untuk menambahkan fitur ke model ini
    use HasFactory;    // Memungkinkan pembuatan factory untuk testing
    use Notifiable;    // Memungkinkan user menerima notifikasi
    use SoftDeletes;   // Memungkinkan soft delete (data tetap ada tapi ditandai sebagai dihapus)

    // Kolom-kolom yang boleh diisi secara mass assignment (insert/update massal)
    // Ini adalah whitelist untuk keamanan, hanya kolom ini yang bisa diisi
    protected $fillable = [
        'name',           // Nama user
        'email',          // Email user (untuk login)
        'password',       // Password user (akan di-hash otomatis)
        'role',           // Role/peran user (admin atau user biasa)
        'last_login_at',  // Waktu terakhir user login
    ];

    // Kolom-kolom yang disembunyikan saat model di-convert ke array/JSON
    // Biasanya untuk data sensitif yang tidak boleh dikirim ke frontend
    protected $hidden = [
        'password',       // Password tidak boleh ditampilkan
        'remember_token', // Token untuk "Remember Me" tidak boleh ditampilkan
    ];

    // Casting tipe data kolom agar otomatis dikonversi ke tipe yang benar
    protected $casts = [
        'email_verified_at' => 'datetime', // Konversi ke Carbon datetime object
        'last_login_at' => 'datetime',     // Konversi ke Carbon datetime object
        'password' => 'hashed',             // Laravel 10+ otomatis hash password
    ];

    // ==================== RELATIONSHIPS ====================
    // Relasi one-to-many: Satu user bisa membuat banyak lowongan kerja
    // Relasi ini menghubungkan user sebagai creator (pembuat)
    public function lowonganKerjaCreated()
    {
        return $this->hasMany(LowonganKerja::class, 'created_by');
        // hasMany = relasi satu ke banyak
        // LowonganKerja::class = model yang dihubungkan
        // 'created_by' = foreign key di tabel lowongan_kerja
    }

    // Relasi one-to-many: Satu user bisa mengupdate banyak lowongan kerja
    // Relasi ini menghubungkan user sebagai updater (pengupdate)
    public function lowonganKerjaUpdated()
    {
        return $this->hasMany(LowonganKerja::class, 'updated_by');
    }

    // Relasi one-to-many: Satu user bisa membuat banyak program magang
    public function programMagangCreated()
    {
        return $this->hasMany(ProgramMagang::class, 'created_by');
    }

    // Relasi one-to-many: Satu user bisa mengupdate banyak program magang
    public function programMagangUpdated()
    {
        return $this->hasMany(ProgramMagang::class, 'updated_by');
    }

    // Relasi one-to-many: Satu user bisa membuat banyak berita
    public function beritaCreated()
    {
        return $this->hasMany(Berita::class, 'created_by');
    }

    // Relasi one-to-many: Satu user bisa mengupdate banyak berita
    public function beritaUpdated()
    {
        return $this->hasMany(Berita::class, 'updated_by');
    }

    // Relasi one-to-many: Satu user punya banyak log aktivitas
    // Log ini mencatat semua aktivitas yang dilakukan user
    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    // ==================== SCOPES ====================
    // Scope adalah method untuk filter query yang sering dipakai
    // Scope membuat query lebih mudah dibaca dan reusable
    
    // Scope untuk mendapatkan hanya user dengan role admin
    // Cara pakai: User::admins()->get()
    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    // ==================== ACCESSORS & MUTATORS ====================
    // Mutator untuk kolom password
    // Otomatis dijalankan saat password di-set/diisi
    // Ini memastikan password selalu di-hash sebelum disimpan ke database
    public function setPasswordAttribute($value)
    {
        // Cek apakah password tidak kosong
        if (!empty($value)) {
            // Hash password menggunakan bcrypt sebelum disimpan
            $this->attributes['password'] = Hash::make($value);
        }
    }

    // ==================== HELPER METHODS ====================
    // Method untuk mengecek apakah user adalah admin
    // Return: true jika admin, false jika bukan
    // Cara pakai: if ($user->isAdmin()) { ... }
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    // Method untuk update waktu login terakhir
    // Biasanya dipanggil setelah user berhasil login
    // Cara pakai: $user->updateLastLogin()
    public function updateLastLogin()
    {
        $this->update(['last_login_at' => now()]);
        // now() adalah helper Laravel yang return waktu sekarang
    }
}