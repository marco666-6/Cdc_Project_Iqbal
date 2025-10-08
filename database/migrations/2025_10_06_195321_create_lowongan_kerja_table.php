<?php
// database/migrations/2024_01_01_000002_create_lowongan_kerja_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lowongan_kerja', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->string('judul');
            $table->string('perusahaan');
            $table->text('deskripsi');
            $table->string('lokasi');
            $table->enum('tipe', ['full_time', 'part_time', 'kontrak', 'magang'])->default('full_time');
            $table->enum('kategori', ['teknologi', 'manufaktur', 'perdagangan', 'jasa', 'lainnya'])->default('lainnya');
            $table->string('gaji_min')->nullable();
            $table->string('gaji_max')->nullable();
            $table->boolean('gaji_negotiable')->default(false);
            $table->string('email_aplikasi');
            $table->date('tanggal_berakhir');
            $table->string('gambar')->nullable();
            $table->boolean('status')->default(true);
            $table->integer('views_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['status', 'tanggal_berakhir']);
            $table->index('created_at');
            $table->fullText(['judul', 'perusahaan', 'deskripsi']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lowongan_kerja');
    }
};