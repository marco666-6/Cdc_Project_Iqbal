<?php
// database/migrations/2024_01_01_000004_create_berita_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('berita', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->string('judul');
            $table->string('slug')->unique();
            $table->text('konten');
            $table->text('ringkasan');
            $table->string('gambar')->nullable();
            $table->enum('kategori', ['karir', 'mbkm', 'magang', 'umum'])->default('umum');
            $table->string('penulis')->default('CDC Polibatam');
            $table->date('tanggal_publikasi');
            $table->boolean('status')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->integer('views_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['status', 'tanggal_publikasi']);
            $table->index('kategori');
            $table->index('is_featured');
            $table->fullText(['judul', 'konten', 'ringkasan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('berita');
    }
};