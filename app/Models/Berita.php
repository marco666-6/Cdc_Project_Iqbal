<?php
// app/Models/Berita.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Berita extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'berita';

    protected $fillable = [
        'created_by',
        'updated_by',
        'judul',
        'slug',
        'konten',
        'ringkasan',
        'gambar',
        'kategori',
        'penulis',
        'tanggal_publikasi',
        'status',
        'is_featured',
        'views_count',
    ];

    protected $casts = [
        'tanggal_publikasi' => 'date',
        'status' => 'boolean',
        'is_featured' => 'boolean',
        'views_count' => 'integer',
    ];

    // Relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', true)
                     ->where('tanggal_publikasi', '<=', now()->toDateString());
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByKategori($query, $kategori)
    {
        return $query->where('kategori', $kategori);
    }

    public function scopeSearch($query, $keyword)
    {
        return $query->where(function($q) use ($keyword) {
            $q->where('judul', 'like', "%{$keyword}%")
              ->orWhere('konten', 'like', "%{$keyword}%")
              ->orWhere('ringkasan', 'like', "%{$keyword}%");
        });
    }

    public function scopePopular($query, $limit = 10)
    {
        return $query->orderBy('views_count', 'desc')->limit($limit);
    }

    public function scopeLatest($query)
    {
        return $query->orderBy('tanggal_publikasi', 'desc');
    }

    // Accessors
    public function getGambarUrlAttribute()
    {
        if ($this->gambar && Storage::disk('public')->exists($this->gambar)) {
            return asset('storage/' . $this->gambar);
        }
        return asset('images/no-image.png');
    }

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

    public function getExcerptAttribute()
    {
        return Str::limit(strip_tags($this->ringkasan), 150);
    }

    public function getReadingTimeAttribute()
    {
        $words = str_word_count(strip_tags($this->konten));
        $minutes = ceil($words / 200); // Average reading speed
        return $minutes . ' menit';
    }

    // Mutators
    public function setJudulAttribute($value)
    {
        $this->attributes['judul'] = trim($value);
        // Auto-generate slug if not manually set
        if (empty($this->attributes['slug'])) {
            $this->attributes['slug'] = Str::slug($value);
        }
    }

    // Methods
    public function incrementViews()
    {
        $this->increment('views_count');
    }

    public static function getKategoriOptions()
    {
        return [
            'karir' => 'Karir',
            'mbkm' => 'MBKM',
            'magang' => 'Magang',
            'umum' => 'Umum',
        ];
    }

    public static function generateUniqueSlug($judul, $id = null)
    {
        $slug = Str::slug($judul);
        $originalSlug = $slug;
        $count = 1;

        while (static::where('slug', $slug)->when($id, function($query, $id) {
            return $query->where('id', '!=', $id);
        })->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }

    // Boot method for model events
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($berita) {
            if (empty($berita->slug)) {
                $berita->slug = static::generateUniqueSlug($berita->judul);
            }
        });

        static::updating(function ($berita) {
            if ($berita->isDirty('judul') && empty($berita->slug)) {
                $berita->slug = static::generateUniqueSlug($berita->judul, $berita->id);
            }
        });

        static::deleting(function ($berita) {
            // Delete image when model is deleted
            if ($berita->gambar && Storage::disk('public')->exists($berita->gambar)) {
                Storage::disk('public')->delete($berita->gambar);
            }
        });
    }
}