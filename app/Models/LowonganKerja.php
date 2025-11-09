<?php
// app/Models/LowonganKerja.php
// Model ini menangani data lowongan pekerjaan
// Merepresentasikan tabel 'lowongan_kerja' di database

namespace App\Models;

// Import class yang dibutuhkan
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // Untuk soft delete
use Illuminate\Support\Facades\Storage; // Untuk mengelola file/gambar

class LowonganKerja extends Model
{
    use HasFactory;  // Untuk testing
    use SoftDeletes; // Data tidak benar-benar dihapus, hanya ditandai

    // Nama tabel di database (jika tidak sesuai konvensi Laravel)
    protected $table = 'lowongan_kerja';

    // Kolom yang boleh diisi secara mass assignment
    protected $fillable = [
        'created_by',         // ID user yang membuat
        'updated_by',         // ID user yang mengupdate
        'judul',              // Judul lowongan
        'perusahaan',         // Nama perusahaan
        'deskripsi',          // Deskripsi lengkap lowongan
        'lokasi',             // Lokasi kerja
        'tipe',               // Tipe pekerjaan (full_time, part_time, dll)
        'kategori',           // Kategori industri (teknologi, manufaktur, dll)
        'gaji_min',           // Gaji minimum
        'gaji_max',           // Gaji maksimum
        'gaji_negotiable',    // Apakah gaji bisa dinegosiasikan
        'email_aplikasi',     // Email untuk melamar
        'tanggal_berakhir',   // Tanggal penutupan lowongan
        'gambar',             // Path gambar lowongan
        'status',             // Status aktif/tidak
        'views_count',        // Jumlah yang melihat lowongan ini
    ];

    // Casting otomatis tipe data
    protected $casts = [
        'tanggal_berakhir' => 'date',     // String ke Carbon date
        'gaji_negotiable' => 'boolean',   // 0/1 ke true/false
        'status' => 'boolean',            // 0/1 ke true/false
        'views_count' => 'integer',       // String ke integer
    ];

    // ==================== RELATIONSHIPS ====================
    // Relasi many-to-one: Banyak lowongan dibuat oleh satu user
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
        // belongsTo = relasi banyak ke satu (kebalikan hasMany)
        // 'created_by' = foreign key di tabel ini
    }

    // Relasi many-to-one: Banyak lowongan diupdate oleh satu user
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // ==================== SCOPES ====================
    // Scope untuk mendapatkan lowongan yang masih aktif
    // Aktif = status true DAN belum melewati tanggal berakhir
    public function scopeActive($query)
    {
        return $query->where('status', true)
                     ->where('tanggal_berakhir', '>=', now()->toDateString());
    }

    // Scope untuk mendapatkan lowongan yang sudah expired/kadaluarsa
    public function scopeExpired($query)
    {
        return $query->where('tanggal_berakhir', '<', now()->toDateString());
    }

    // Scope untuk filter berdasarkan tipe pekerjaan
    // Cara pakai: LowonganKerja::byTipe('full_time')->get()
    public function scopeByTipe($query, $tipe)
    {
        return $query->where('tipe', $tipe);
    }

    // Scope untuk filter berdasarkan kategori
    public function scopeByKategori($query, $kategori)
    {
        return $query->where('kategori', $kategori);
    }

    // Scope untuk filter berdasarkan lokasi (pencarian sebagian)
    // Menggunakan LIKE untuk pencarian fleksibel
    public function scopeByLokasi($query, $lokasi)
    {
        return $query->where('lokasi', 'like', "%{$lokasi}%");
    }

    // Scope untuk pencarian berdasarkan keyword
    // Mencari di judul, perusahaan, dan deskripsi
    public function scopeSearch($query, $keyword)
    {
        return $query->where(function($q) use ($keyword) {
            $q->where('judul', 'like', "%{$keyword}%")
              ->orWhere('perusahaan', 'like', "%{$keyword}%")
              ->orWhere('deskripsi', 'like', "%{$keyword}%");
        });
    }

    // Scope untuk mendapatkan lowongan populer (paling banyak dilihat)
    public function scopePopular($query, $limit = 10)
    {
        return $query->orderBy('views_count', 'desc')->limit($limit);
    }

    // ==================== ACCESSORS ====================
    // Accessor untuk mendapatkan URL gambar lengkap
    // Otomatis dipanggil saat akses: $lowongan->gambar_url
    public function getGambarUrlAttribute()
    {
        // Cek apakah ada gambar dan file-nya exist
        if ($this->gambar && Storage::disk('public')->exists($this->gambar)) {
            // Return URL lengkap ke file di storage
            return asset('storage/' . $this->gambar);
        }
        // Jika tidak ada gambar, return gambar default
        return asset('images/no-image.png');
    }

    // Accessor untuk mengecek apakah lowongan sudah expired
    // Cara pakai: $lowongan->is_expired (return true/false)
    public function getIsExpiredAttribute()
    {
        return $this->tanggal_berakhir < now()->toDateString();
    }

    // Accessor untuk mendapatkan tipe pekerjaan dalam format yang readable
    // full_time -> Full Time
    public function getTipeFormattedAttribute()
    {
        $tipes = [
            'full_time' => 'Full Time',
            'part_time' => 'Part Time',
            'kontrak' => 'Kontrak',
            'magang' => 'Magang',
        ];
        // Return format readable, atau return aslinya jika tidak ada di array
        return $tipes[$this->tipe] ?? $this->tipe;
    }

    // Accessor untuk mendapatkan kategori dalam format yang readable
    public function getKategoriFormattedAttribute()
    {
        $kategoris = [
            'teknologi' => 'Teknologi',
            'manufaktur' => 'Manufaktur',
            'perdagangan' => 'Perdagangan',
            'jasa' => 'Jasa',
            'lainnya' => 'Lainnya',
        ];
        return $kategoris[$this->kategori] ?? $this->kategori;
    }

    // Accessor untuk format tampilan gaji
    // Menghandle berbagai kondisi: negotiable, range, atau tidak disebutkan
    public function getGajiFormattedAttribute()
    {
        // Jika gaji negotiable
        if ($this->gaji_negotiable) {
            return 'Negotiable';
        }
        
        // Jika ada gaji min dan max (range)
        if ($this->gaji_min && $this->gaji_max) {
            return 'Rp ' . number_format($this->gaji_min, 0, ',', '.') . 
                   ' - Rp ' . number_format($this->gaji_max, 0, ',', '.');
        } 
        // Jika hanya ada gaji min
        elseif ($this->gaji_min) {
            return 'Rp ' . number_format($this->gaji_min, 0, ',', '.');
        }
        
        // Jika tidak ada info gaji sama sekali
        return 'Tidak disebutkan';
    }

    // ==================== METHODS ====================
    // Method untuk menambah jumlah views
    // Dipanggil ketika ada yang melihat detail lowongan
    public function incrementViews()
    {
        $this->increment('views_count'); // Tambah 1 ke kolom views_count
    }

    // Static method untuk mendapatkan pilihan tipe pekerjaan
    // Digunakan untuk dropdown/select di form
    public static function getTipeOptions()
    {
        return [
            'full_time' => 'Full Time',
            'part_time' => 'Part Time',
            'kontrak' => 'Kontrak',
            'magang' => 'Magang',
        ];
    }

    // Static method untuk mendapatkan pilihan kategori
    public static function getKategoriOptions()
    {
        return [
            'teknologi' => 'Teknologi',
            'manufaktur' => 'Manufaktur',
            'perdagangan' => 'Perdagangan',
            'jasa' => 'Jasa',
            'lainnya' => 'Lainnya',
        ];
    }

    // ==================== MODEL EVENTS ====================
    // Boot method dipanggil saat model di-load
    // Digunakan untuk mendaftarkan event listener
    protected static function boot()
    {
        parent::boot(); // Panggil boot dari parent class

        // Event yang dijalankan sebelum model dihapus (soft delete)
        static::deleting(function ($lowongan) {
            // Hapus file gambar jika ada
            if ($lowongan->gambar && Storage::disk('public')->exists($lowongan->gambar)) {
                Storage::disk('public')->delete($lowongan->gambar);
            }
        });
    }
}