<?php
// app/Models/LowonganKerja.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class LowonganKerja extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'lowongan_kerja';

    protected $fillable = [
        'created_by',
        'updated_by',
        'judul',
        'perusahaan',
        'deskripsi',
        'lokasi',
        'tipe',
        'kategori',
        'gaji_min',
        'gaji_max',
        'gaji_negotiable',
        'email_aplikasi',
        'tanggal_berakhir',
        'gambar',
        'status',
        'views_count',
    ];

    protected $casts = [
        'tanggal_berakhir' => 'date',
        'gaji_negotiable' => 'boolean',
        'status' => 'boolean',
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
    public function scopeActive($query)
    {
        return $query->where('status', true)
                     ->where('tanggal_berakhir', '>=', now()->toDateString());
    }

    public function scopeExpired($query)
    {
        return $query->where('tanggal_berakhir', '<', now()->toDateString());
    }

    public function scopeByTipe($query, $tipe)
    {
        return $query->where('tipe', $tipe);
    }

    public function scopeByKategori($query, $kategori)
    {
        return $query->where('kategori', $kategori);
    }

    public function scopeByLokasi($query, $lokasi)
    {
        return $query->where('lokasi', 'like', "%{$lokasi}%");
    }

    public function scopeSearch($query, $keyword)
    {
        return $query->where(function($q) use ($keyword) {
            $q->where('judul', 'like', "%{$keyword}%")
              ->orWhere('perusahaan', 'like', "%{$keyword}%")
              ->orWhere('deskripsi', 'like', "%{$keyword}%");
        });
    }

    public function scopePopular($query, $limit = 10)
    {
        return $query->orderBy('views_count', 'desc')->limit($limit);
    }

    // Accessors
    public function getGambarUrlAttribute()
    {
        if ($this->gambar && Storage::disk('public')->exists($this->gambar)) {
            return asset('storage/' . $this->gambar);
        }
        return asset('images/no-image.png');
    }

    public function getIsExpiredAttribute()
    {
        return $this->tanggal_berakhir < now()->toDateString();
    }

    public function getTipeFormattedAttribute()
    {
        $tipes = [
            'full_time' => 'Full Time',
            'part_time' => 'Part Time',
            'kontrak' => 'Kontrak',
            'magang' => 'Magang',
        ];
        return $tipes[$this->tipe] ?? $this->tipe;
    }

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

    public function getGajiFormattedAttribute()
    {
        if ($this->gaji_negotiable) {
            return 'Negotiable';
        }
        
        if ($this->gaji_min && $this->gaji_max) {
            return 'Rp ' . number_format($this->gaji_min, 0, ',', '.') . ' - Rp ' . number_format($this->gaji_max, 0, ',', '.');
        } elseif ($this->gaji_min) {
            return 'Rp ' . number_format($this->gaji_min, 0, ',', '.');
        }
        
        return 'Tidak disebutkan';
    }

    // Methods
    public function incrementViews()
    {
        $this->increment('views_count');
    }

    public static function getTipeOptions()
    {
        return [
            'full_time' => 'Full Time',
            'part_time' => 'Part Time',
            'kontrak' => 'Kontrak',
            'magang' => 'Magang',
        ];
    }

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

    // Boot method for model events
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($lowongan) {
            // Delete image when model is deleted
            if ($lowongan->gambar && Storage::disk('public')->exists($lowongan->gambar)) {
                Storage::disk('public')->delete($lowongan->gambar);
            }
        });
    }
}