<?php
// app/Models/Tentang.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Tentang extends Model
{
    use HasFactory;

    protected $table = 'tentang';

    protected $fillable = [
        'updated_by',
        'sejarah',
        'visi',
        'misi',
        'tujuan',
        'gambar',
    ];

    // Relationships
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Accessors
    public function getGambarUrlAttribute()
    {
        if ($this->gambar && Storage::disk('public')->exists($this->gambar)) {
            return asset('storage/' . $this->gambar);
        }
        return asset('images/no-image.png');
    }

    // Boot method for model events
    protected static function boot()
    {
        parent::boot();

        static::updating(function ($tentang) {
            // Delete old image if new one is uploaded
            if ($tentang->isDirty('gambar') && $tentang->getOriginal('gambar')) {
                $oldImage = $tentang->getOriginal('gambar');
                if (Storage::disk('public')->exists($oldImage)) {
                    Storage::disk('public')->delete($oldImage);
                }
            }
        });
    }
}