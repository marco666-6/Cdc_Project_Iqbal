<?php
// app/Models/Berita.php
// Model untuk menangani data berita/artikel
// Merepresentasikan tabel 'berita' di database

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str; // Helper untuk manipulasi string

class Berita extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'berita';

    // Kolom yang bisa diisi
    protected $fillable = [
        'created_by',          // ID user pembuat
        'updated_by',          // ID user pengupdate
        'judul',               // Judul berita
        'slug',                // URL-friendly version dari judul
        'konten',              // Isi berita lengkap (bisa HTML)
        'ringkasan',           // Ringkasan singkat berita
        'gambar',              // Path gambar featured/thumbnail
        'kategori',            // Kategori berita (karir, mbkm, magang, umum)
        'penulis',             // Nama penulis
        'tanggal_publikasi',   // Tanggal berita dipublikasikan
        'status',              // Status publish/draft
        'is_featured',         // Apakah berita ditampilkan di featured section
        'views_count',         // Jumlah pembaca
    ];

    // Casting otomatis tipe data
    protected $casts = [
        'tanggal_publikasi' => 'date', // String ke Carbon date
        'status' => 'boolean',         // 0/1 ke true/false
        'is_featured' => 'boolean',    // 0/1 ke true/false
        'views_count' => 'integer',    // String ke integer
    ];

    // ==================== RELATIONSHIPS ====================
    // Relasi ke user yang membuat berita
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relasi ke user yang mengupdate berita
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // ==================== SCOPES ====================
    // Scope untuk berita yang sudah dipublish
    // Published = status true DAN tanggal publikasi sudah lewat
    public function scopePublished($query)
    {
        return $query->where('status', true)
                     ->where('tanggal_publikasi', '<=', now()->toDateString());
    }

    // Scope untuk berita yang di-featured (ditampilkan di halaman utama)
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    // Scope untuk filter berdasarkan kategori
    public function scopeByKategori($query, $kategori)
    {
        return $query->where('kategori', $kategori);
    }

    // Scope untuk pencarian berita
    // Mencari di judul, konten, dan ringkasan
    public function scopeSearch($query, $keyword)
    {
        return $query->where(function($q) use ($keyword) {
            $q->where('judul', 'like', "%{$keyword}%")
              ->orWhere('konten', 'like', "%{$keyword}%")
              ->orWhere('ringkasan', 'like', "%{$keyword}%");
        });
    }

    // Scope untuk berita populer (paling banyak dibaca)
    public function scopePopular($query, $limit = 10)
    {
        return $query->orderBy('views_count', 'desc')->limit($limit);
    }

    // Scope untuk berita terbaru
    // Diurutkan dari tanggal publikasi paling baru
    public function scopeLatest($query)
    {
        return $query->orderBy('tanggal_publikasi', 'desc');
    }

    // ==================== ACCESSORS ====================
    // Accessor untuk URL gambar lengkap
    public function getGambarUrlAttribute()
    {
        if ($this->gambar && Storage::disk('public')->exists($this->gambar)) {
            return asset('storage/' . $this->gambar);
        }
        return asset('images/no-image.png');
    }

    // Accessor untuk format kategori yang readable
    public function getKategoriFormattedAttribute()
    {
        $kategoris = [
            'karir' => 'Karir',
            'mbkm' => 'MBKM',
            'magang' => 'Magang',
            'umum' => 'Umum',
        ];
        return $kategoris[$this->kategori] ?? $this->kategori;
    }

    // Accessor untuk excerpt (kutipan singkat)
    // Mengambil 150 karakter pertama dari ringkasan
    // strip_tags = menghilangkan tag HTML
    public function getExcerptAttribute()
    {
        return Str::limit(strip_tags($this->ringkasan), 150);
    }

    // Accessor untuk estimasi waktu baca
    // Menghitung jumlah kata dan dibagi rata-rata kecepatan baca (200 kata/menit)
    public function getReadingTimeAttribute()
    {
        $words = str_word_count(strip_tags($this->konten)); // Hitung jumlah kata
        $minutes = ceil($words / 200); // Pembulatan ke atas
        return $minutes . ' menit';
    }

    // ==================== MUTATORS ====================
    // Mutator untuk kolom judul
    // Dijalankan otomatis saat judul di-set
    public function setJudulAttribute($value)
    {
        $this->attributes['judul'] = trim($value); // Hapus spasi di awal/akhir
        
        // Auto-generate slug jika belum ada
        // Slug digunakan untuk URL yang SEO-friendly
        if (empty($this->attributes['slug'])) {
            $this->attributes['slug'] = Str::slug($value);
            // Str::slug('Judul Berita') -> 'judul-berita'
        }
    }

    // ==================== METHODS ====================
    // Method untuk menambah jumlah views
    public function incrementViews()
    {
        $this->increment('views_count');
    }

    // Static method untuk options kategori
    public static function getKategoriOptions()
    {
        return [
            'karir' => 'Karir',
            'mbkm' => 'MBKM',
            'magang' => 'Magang',
            'umum' => 'Umum',
        ];
    }

    // Static method untuk generate slug yang unik
    // Jika slug sudah dipakai, akan ditambah angka di belakang (judul-2, judul-3, dst)
    public static function generateUniqueSlug($judul, $id = null)
    {
        $slug = Str::slug($judul); // Convert judul ke slug
        $originalSlug = $slug;
        $count = 1;

        // Loop sampai dapat slug yang unik
        while (static::where('slug', $slug)->when($id, function($query, $id) {
            // Jika update, exclude ID sendiri dari pengecekan
            return $query->where('id', '!=', $id);
        })->exists()) {
            // Jika slug sudah ada, tambah angka di belakang
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }

    // ==================== MODEL EVENTS ====================
    protected static function boot()
    {
        parent::boot();

        // Event sebelum berita dibuat (insert)
        static::creating(function ($berita) {
            // Generate slug jika belum ada
            if (empty($berita->slug)) {
                $berita->slug = static::generateUniqueSlug($berita->judul);
            }
        });

        // Event sebelum berita diupdate
        static::updating(function ($berita) {
            // Jika judul berubah dan slug kosong, generate slug baru
            if ($berita->isDirty('judul') && empty($berita->slug)) {
                $berita->slug = static::generateUniqueSlug($berita->judul, $berita->id);
            }
        });

        // Event sebelum berita dihapus
        static::deleting(function ($berita) {
            // Hapus gambar untuk membersihkan storage
            if ($berita->gambar && Storage::disk('public')->exists($berita->gambar)) {
                Storage::disk('public')->delete($berita->gambar);
            }
        });
    }
}