<?php
// database/migrations/2024_01_01_000003_create_program_magang_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('program_magang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->string('judul');
            $table->string('perusahaan');
            $table->text('deskripsi');
            $table->text('persyaratan');
            $table->text('benefit');
            $table->string('lokasi');
            $table->enum('tipe', ['mbkm', 'magang_reguler', 'magang_independen'])->default('magang_reguler');
            $table->integer('durasi'); // dalam bulan
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_berakhir');
            $table->string('link_pendaftaran');
            $table->string('gambar')->nullable();
            $table->boolean('status')->default(true);
            $table->integer('views_count')->default(0);
            $table->integer('kuota')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['status', 'tanggal_berakhir']);
            $table->index('tipe');
            $table->fullText(['judul', 'perusahaan', 'deskripsi']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('program_magang');
    }
};