<?php
// ==================== NEWSLETTER MODEL ====================
// app/Models/LanggananNewsletter.php
// Model untuk subscriber newsletter
// Menangani email subscription dengan verifikasi

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class LanggananNewsletter extends Model
{
    use HasFactory;

    protected $table = 'langganan_newsletter';

    // Kolom yang bisa diisi
    protected $fillable = [
        'email',              // Email subscriber
        'nama',               // Nama subscriber (opsional)
        'status',             // Status aktif/nonaktif
        'verified_at',        // Waktu verifikasi email
        'verification_token', // Token untuk verifikasi email
        'unsubscribed_at',    // Waktu unsubscribe
    ];

    // Casting tipe data
    protected $casts = [
        'status' => 'boolean',       // 0/1 ke true/false
        'verified_at' => 'datetime', // String ke Carbon datetime
        'unsubscribed_at' => 'datetime',
    ];

    // ==================== SCOPES ====================
    // Scope untuk subscriber yang aktif dan sudah verifikasi
    public function scopeActive($query)
    {
        return $query->where('status', true)->whereNotNull('verified_at');
    }

    // Scope untuk subscriber yang belum verifikasi email
    public function scopeUnverified($query)
    {
        return $query->whereNull('verified_at');
    }

    // Scope untuk subscriber yang sudah unsubscribe
    public function scopeUnsubscribed($query)
    {
        return $query->where('status', false)->whereNotNull('unsubscribed_at');
    }

    // ==================== ACCESSORS ====================
    // Accessor untuk mengecek apakah email sudah diverifikasi
    public function getIsVerifiedAttribute()
    {
        return !is_null($this->verified_at);
    }

    // Accessor untuk mengecek apakah subscriber aktif
    // Aktif = status true DAN sudah verifikasi
    public function getIsActiveAttribute()
    {
        return $this->status && $this->is_verified;
    }

    // ==================== METHODS ====================
    // Method untuk verifikasi email subscriber
    // Dipanggil ketika user klik link verifikasi di email
    public function verify()
    {
        $this->update([
            'verified_at' => now(),          // Set waktu verifikasi
            'verification_token' => null,    // Hapus token (sudah tidak dipakai)
        ]);
    }

    // Method untuk unsubscribe
    public function unsubscribe()
    {
        $this->update([
            'status' => false,              // Set status jadi nonaktif
            'unsubscribed_at' => now(),     // Set waktu unsubscribe
        ]);
    }

    // Method untuk subscribe ulang (reactivate)
    public function resubscribe()
    {
        $this->update([
            'status' => true,               // Set status jadi aktif
            'unsubscribed_at' => null,      // Hapus waktu unsubscribe
        ]);
    }

    // ==================== MODEL EVENTS ====================
    protected static function boot()
    {
        parent::boot();

        // Event sebelum subscriber dibuat
        static::creating(function ($newsletter) {
            // Generate verification token jika belum ada
            if (empty($newsletter->verification_token)) {
                $newsletter->verification_token = Str::random(64);
                // Generate string acak 64 karakter untuk keamanan
            }
        });
    }
}