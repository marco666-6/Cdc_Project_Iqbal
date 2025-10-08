<?php
// database/migrations/2024_01_01_000006_create_kontak_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kontak', function (Blueprint $table) {
            $table->id();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->string('alamat');
            $table->string('telepon');
            $table->string('email');
            $table->string('whatsapp')->nullable();
            $table->text('google_maps_url')->nullable();
            $table->text('google_maps_embed')->nullable();
            $table->text('jam_operasional');
            $table->string('facebook')->nullable();
            $table->string('instagram')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('twitter')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kontak');
    }
};