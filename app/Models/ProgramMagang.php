<?php
// app/Models/ProgramMagang.php
// Model untuk menangani data program magang/internship
// Merepresentasikan tabel 'program_magang' di database

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class ProgramMagang extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'program_magang';

    // Kolom yang bisa diisi
    protected $fillable = [
        'created_by',          // ID user pembuat
        'updated_by',          // ID user pengupdate
        'judul',               // Judul program magang
        'perusahaan',          // Nama perusahaan/institusi
        'deskripsi',           // Deskripsi program
        'persyaratan',         // Syarat yang harus dipenuhi
        'benefit',             // Keuntungan yang didapat peserta
        'lokasi',              // Lokasi program
        'tipe',                // Tipe: mbkm, magang_reguler, magang_independen
        'durasi',              // Durasi dalam bulan
        'tanggal_mulai',       // Tanggal mulai program
        'tanggal_berakhir',    // Tanggal berakhir program
        'link_pendaftaran',    // URL link untuk mendaftar
        'gambar',              // Path file gambar
        'status',              // Status aktif/nonaktif
        'views_count',         // Jumlah views
        'kuota',               // Kuota peserta (jika ada)
    ];

    // Casting tipe data otomatis
    protected $casts = [
        'tanggal_mulai' => 'date',      // String ke Carbon date
        'tanggal_berakhir' => 'date',   // String ke Carbon date
        'status' => 'boolean',          // 0/1 ke true/false
        'views_count' => 'integer',     // String ke integer
        'durasi' => 'integer',          // String ke integer (dalam bulan)
        'kuota' => 'integer',           // String ke integer
    ];

    // ==================== RELATIONSHIPS ====================
    // Relasi ke user yang membuat program
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relasi ke user yang mengupdate program
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // ==================== SCOPES ====================
    // Scope untuk program yang masih aktif
    // Aktif = status true DAN belum melewati tanggal berakhir
    public function scopeActive($query)
    {
        return $query->where('status', true)
                     ->where('tanggal_berakhir', '>=', now()->toDateString());
    }

    // Scope untuk program yang sudah berakhir/expired
    public function scopeExpired($query)
    {
        return $query->where('tanggal_berakhir', '<', now()->toDateString());
    }

    // Scope untuk filter berdasarkan tipe program
    // Contoh: ProgramMagang::byTipe('mbkm')->get()
    public function scopeByTipe($query, $tipe)
    {
        return $query->where('tipe', $tipe);
    }

    // Scope untuk filter berdasarkan lokasi (pencarian partial)
    public function scopeByLokasi($query, $lokasi)
    {
        return $query->where('lokasi', 'like', "%{$lokasi}%");
    }

    // Scope untuk pencarian dengan keyword
    // Mencari di judul, perusahaan, dan deskripsi
    public function scopeSearch($query, $keyword)
    {
        return $query->where(function($q) use ($keyword) {
            $q->where('judul', 'like', "%{$keyword}%")
              ->orWhere('perusahaan', 'like', "%{$keyword}%")
              ->orWhere('deskripsi', 'like', "%{$keyword}%");
        });
    }

    // Scope untuk mendapatkan program populer (paling banyak dilihat)
    public function scopePopular($query, $limit = 10)
    {
        return $query->orderBy('views_count', 'desc')->limit($limit);
    }

    // Scope khusus untuk program MBKM (Merdeka Belajar Kampus Merdeka)
    public function scopeMbkm($query)
    {
        return $query->where('tipe', 'mbkm');
    }

    // ==================== ACCESSORS ====================
    // Accessor untuk mendapatkan URL gambar lengkap
    // Cara akses: $program->gambar_url
    public function getGambarUrlAttribute()
    {
        // Cek apakah gambar ada dan file exist
        if ($this->gambar && Storage::disk('public')->exists($this->gambar)) {
            return asset('storage/' . $this->gambar);
        }
        // Return gambar default jika tidak ada
        return asset('images/no-image.png');
    }

    // Accessor untuk mengecek apakah program sudah berakhir
    // Return: true/false
    public function getIsExpiredAttribute()
    {
        return $this->tanggal_berakhir < now()->toDateString();
    }

    // Accessor untuk format tipe yang readable
    // mbkm -> MBKM
    public function getTipeFormattedAttribute()
    {
        $tipes = [
            'mbkm' => 'MBKM',
            'magang_reguler' => 'Magang Reguler',
            'magang_independen' => 'Magang Independen',
        ];
        return $tipes[$this->tipe] ?? $this->tipe;
    }

    // Accessor untuk format durasi dengan satuan
    // 6 -> 6 Bulan
    public function getDurasiFormattedAttribute()
    {
        return $this->durasi . ' Bulan';
    }

    // Accessor untuk format kuota
    // Jika ada kuota -> "20 Orang"
    // Jika tidak ada -> "Tidak Terbatas"
    public function getKuotaFormattedAttribute()
    {
        if ($this->kuota) {
            return $this->kuota . ' Orang';
        }
        return 'Tidak Terbatas';
    }

    // ==================== METHODS ====================
    // Method untuk menambah jumlah views/pengunjung
    public function incrementViews()
    {
        $this->increment('views_count');
    }

    // Static method untuk mendapatkan options tipe program
    // Digunakan untuk dropdown di form
    public static function getTipeOptions()
    {
        return [
            'mbkm' => 'MBKM',
            'magang_reguler' => 'Magang Reguler',
            'magang_independen' => 'Magang Independen',
        ];
    }

    // ==================== MODEL EVENTS ====================
    // Boot method untuk register event listener
    protected static function boot()
    {
        parent::boot();

        // Event sebelum program dihapus
        // Hapus gambar terkait untuk membersihkan storage
        static::deleting(function ($program) {
            if ($program->gambar && Storage::disk('public')->exists($program->gambar)) {
                Storage::disk('public')->delete($program->gambar);
            }
        });
    }
}