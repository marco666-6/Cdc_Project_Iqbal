<?php
// ==================== KONTAK MODEL ====================
// app/Models/Kontak.php
// Model untuk menangani info kontak website (biasanya hanya 1 record)
// Menyimpan alamat, telepon, email, social media, dll

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kontak extends Model
{
    use HasFactory;

    protected $table = 'kontak';

    // Kolom yang bisa diisi
    protected $fillable = [
        'updated_by',         // ID user terakhir yang update
        'alamat',             // Alamat kantor/institusi
        'telepon',            // Nomor telepon
        'email',              // Email kontak
        'whatsapp',           // Nomor WhatsApp
        'google_maps_url',    // Link Google Maps
        'google_maps_embed',  // Embed code Google Maps untuk iframe
        'jam_operasional',    // Jam operasional (text/HTML)
        'facebook',           // URL Facebook
        'instagram',          // URL Instagram
        'linkedin',           // URL LinkedIn
        'twitter',            // URL Twitter
    ];

    // ==================== RELATIONSHIPS ====================
    // Relasi ke user yang terakhir update
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // ==================== ACCESSORS ====================
    // Accessor untuk generate link WhatsApp chat
    // Format: https://wa.me/628123456789
    public function getWhatsappLinkAttribute()
    {
        if ($this->whatsapp) {
            // Hapus semua karakter non-digit
            $number = preg_replace('/[^0-9]/', '', $this->whatsapp);
            
            // Jika nomor dimulai dengan 0, ganti dengan 62 (kode Indonesia)
            if (substr($number, 0, 1) === '0') {
                $number = '62' . substr($number, 1);
            }
            
            return 'https://wa.me/' . $number;
        }
        return null;
    }

    // Accessor untuk mendapatkan semua link social media dalam array
    // Berguna untuk loop di view
    public function getSocialMediaLinksAttribute()
    {
        return [
            'facebook' => $this->facebook,
            'instagram' => $this->instagram,
            'linkedin' => $this->linkedin,
            'twitter' => $this->twitter,
        ];
    }
}