<?php
// ==================== TENTANG MODEL ====================
// app/Models/Tentang.php
// Model untuk halaman "Tentang Kami" (biasanya hanya 1 record)
// Menyimpan sejarah, visi, misi, tujuan organisasi

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Tentang extends Model
{
    use HasFactory;

    protected $table = 'tentang';

    // Kolom yang bisa diisi
    protected $fillable = [
        'updated_by',  // ID user terakhir yang update
        'sejarah',     // Sejarah organisasi (text/HTML)
        'visi',        // Visi organisasi
        'misi',        // Misi organisasi (bisa multiple, text/HTML)
        'tujuan',      // Tujuan organisasi
        'gambar',      // Path gambar untuk halaman tentang
    ];

    // ==================== RELATIONSHIPS ====================
    // Relasi ke user yang terakhir update
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
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

    // ==================== MODEL EVENTS ====================
    protected static function boot()
    {
        parent::boot();

        // Event sebelum record diupdate
        static::updating(function ($tentang) {
            // Jika gambar diubah, hapus gambar lama
            if ($tentang->isDirty('gambar') && $tentang->getOriginal('gambar')) {
                $oldImage = $tentang->getOriginal('gambar');
                if (Storage::disk('public')->exists($oldImage)) {
                    Storage::disk('public')->delete($oldImage);
                }
            }
        });
    }
}