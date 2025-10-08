<?php
// app/Models/User.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Relationships
    public function lowonganKerjaCreated()
    {
        return $this->hasMany(LowonganKerja::class, 'created_by');
    }

    public function lowonganKerjaUpdated()
    {
        return $this->hasMany(LowonganKerja::class, 'updated_by');
    }

    public function programMagangCreated()
    {
        return $this->hasMany(ProgramMagang::class, 'created_by');
    }

    public function programMagangUpdated()
    {
        return $this->hasMany(ProgramMagang::class, 'updated_by');
    }

    public function beritaCreated()
    {
        return $this->hasMany(Berita::class, 'created_by');
    }

    public function beritaUpdated()
    {
        return $this->hasMany(Berita::class, 'updated_by');
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    // Scopes
    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    // Accessors & Mutators
    public function setPasswordAttribute($value)
    {
        if (!empty($value)) {
            $this->attributes['password'] = Hash::make($value);
        }
    }

    // Helper Methods
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function updateLastLogin()
    {
        $this->update(['last_login_at' => now()]);
    }
}