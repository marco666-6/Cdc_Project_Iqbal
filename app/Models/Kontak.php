<?php
// app/Models/Kontak.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kontak extends Model
{
    use HasFactory;

    protected $table = 'kontak';

    protected $fillable = [
        'updated_by',
        'alamat',
        'telepon',
        'email',
        'whatsapp',
        'google_maps_url',
        'google_maps_embed',
        'jam_operasional',
        'facebook',
        'instagram',
        'linkedin',
        'twitter',
    ];

    // Relationships
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Accessors
    public function getWhatsappLinkAttribute()
    {
        if ($this->whatsapp) {
            $number = preg_replace('/[^0-9]/', '', $this->whatsapp);
            if (substr($number, 0, 1) === '0') {
                $number = '62' . substr($number, 1);
            }
            return 'https://wa.me/' . $number;
        }
        return null;
    }

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