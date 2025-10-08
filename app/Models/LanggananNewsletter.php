<?php
// app/Models/LanggananNewsletter.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class LanggananNewsletter extends Model
{
    use HasFactory;

    protected $table = 'langganan_newsletter';

    protected $fillable = [
        'email',
        'nama',
        'status',
        'verified_at',
        'verification_token',
        'unsubscribed_at',
    ];

    protected $casts = [
        'status' => 'boolean',
        'verified_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', true)->whereNotNull('verified_at');
    }

    public function scopeUnverified($query)
    {
        return $query->whereNull('verified_at');
    }

    public function scopeUnsubscribed($query)
    {
        return $query->where('status', false)->whereNotNull('unsubscribed_at');
    }

    // Accessors
    public function getIsVerifiedAttribute()
    {
        return !is_null($this->verified_at);
    }

    public function getIsActiveAttribute()
    {
        return $this->status && $this->is_verified;
    }

    // Methods
    public function verify()
    {
        $this->update([
            'verified_at' => now(),
            'verification_token' => null,
        ]);
    }

    public function unsubscribe()
    {
        $this->update([
            'status' => false,
            'unsubscribed_at' => now(),
        ]);
    }

    public function resubscribe()
    {
        $this->update([
            'status' => true,
            'unsubscribed_at' => null,
        ]);
    }

    // Boot method for model events
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($newsletter) {
            if (empty($newsletter->verification_token)) {
                $newsletter->verification_token = Str::random(64);
            }
        });
    }
}