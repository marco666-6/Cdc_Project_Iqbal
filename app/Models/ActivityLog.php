<?php
// ==================== ACTIVITY LOG MODEL ====================
// app/Models/ActivityLog.php
// Model untuk mencatat semua aktivitas admin di sistem
// Berguna untuk audit trail dan tracking perubahan

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    // Kolom yang bisa diisi
    protected $fillable = [
        'user_id',        // ID user yang melakukan aktivitas
        'log_name',       // Nama log/grup log (default, auth, dll)
        'description',    // Deskripsi aktivitas yang dilakukan
        'subject_type',   // Tipe model yang terkait (LowonganKerja, Berita, dll)
        'subject_id',     // ID record yang terkait
        'event',          // Jenis event (create, update, delete, login, dll)
        'properties',     // Data tambahan dalam format array/JSON
        'user_agent',     // Info browser/device yang digunakan
    ];

    // Casting properties dari JSON string ke array
    protected $casts = [
        'properties' => 'array',
    ];

    // ==================== RELATIONSHIPS ====================
    // Relasi ke user yang melakukan aktivitas
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi polymorphic ke model manapun yang terkait
    // Bisa ke LowonganKerja, Berita, ProgramMagang, dll
    public function subject()
    {
        return $this->morphTo();
        // morphTo = relasi polymorphic (bisa ke berbagai model)
    }

    // ==================== SCOPES ====================
    // Scope untuk filter log berdasarkan user
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Scope untuk filter berdasarkan nama log
    public function scopeByLogName($query, $logName)
    {
        return $query->where('log_name', $logName);
    }

    // Scope untuk filter berdasarkan event
    public function scopeByEvent($query, $event)
    {
        return $query->where('event', $event);
    }

    // Scope untuk mendapatkan log dari subject tertentu
    // Contoh: ActivityLog::forSubject($lowongan)->get()
    public function scopeForSubject($query, $subject)
    {
        return $query->where('subject_type', get_class($subject))
                     ->where('subject_id', $subject->id);
    }

    // ==================== HELPER METHODS ====================
    // Static method untuk membuat log dengan mudah
    // Cara pakai: ActivityLog::log('User login', $user, 'login')
    public static function log($description, $subject = null, $event = null, $properties = [])
    {
        return static::create([
            'user_id' => auth()->id(),           // ID user yang sedang login
            'log_name' => 'default',             // Default log name
            'description' => $description,        // Deskripsi aktivitas
            'subject_type' => $subject ? get_class($subject) : null, // Nama class model
            'subject_id' => $subject ? $subject->id : null,          // ID record
            'event' => $event,                   // Jenis event
            'properties' => $properties,         // Data tambahan
            'user_agent' => request()->userAgent(), // Info browser
        ]);
    }
}