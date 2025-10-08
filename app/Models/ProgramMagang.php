<?php
// app/Models/ProgramMagang.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class ProgramMagang extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'program_magang';

    protected $fillable = [
        'created_by',
        'updated_by',
        'judul',
        'perusahaan',
        'deskripsi',
        'persyaratan',
        'benefit',
        'lokasi',
        'tipe',
        'durasi',
        'tanggal_mulai',
        'tanggal_berakhir',
        'link_pendaftaran',
        'gambar',
        'status',
        'views_count',
        'kuota',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_berakhir' => 'date',
        'status' => 'boolean',
        'views_count' => 'integer',
        'durasi' => 'integer',
        'kuota' => 'integer',
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

    public function scopeMbkm($query)
    {
        return $query->where('tipe', 'mbkm');
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
            'mbkm' => 'MBKM',
            'magang_reguler' => 'Magang Reguler',
            'magang_independen' => 'Magang Independen',
        ];
        return $tipes[$this->tipe] ?? $this->tipe;
    }

    public function getDurasiFormattedAttribute()
    {
        return $this->durasi . ' Bulan';
    }

    public function getKuotaFormattedAttribute()
    {
        if ($this->kuota) {
            return $this->kuota . ' Orang';
        }
        return 'Tidak Terbatas';
    }

    // Methods
    public function incrementViews()
    {
        $this->increment('views_count');
    }

    public static function getTipeOptions()
    {
        return [
            'mbkm' => 'MBKM',
            'magang_reguler' => 'Magang Reguler',
            'magang_independen' => 'Magang Independen',
        ];
    }

    // Boot method for model events
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($program) {
            // Delete image when model is deleted
            if ($program->gambar && Storage::disk('public')->exists($program->gambar)) {
                Storage::disk('public')->delete($program->gambar);
            }
        });
    }
}