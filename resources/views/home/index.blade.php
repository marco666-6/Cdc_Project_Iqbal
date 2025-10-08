@extends('layouts.app')

@section('title', 'Beranda - CDC Polibatam')

@section('content')
<!-- Hero Section -->
<section class="bg-gradient-primary text-white py-5">
    <div class="container py-5">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <h1 class="display-4 fw-bold mb-3">Temukan Karir Impian Anda</h1>
                <p class="lead mb-4">Career Development Center Polibatam membantu mahasiswa menemukan peluang karir terbaik dan program magang berkualitas.</p>
                <div class="d-flex gap-3">
                    <a href="{{ route('lowongan-kerja') }}" class="btn btn-light btn-lg">
                        <i class="bi bi-search me-2"></i>Cari Lowongan
                    </a>
                    <a href="{{ route('program-magang') }}" class="btn btn-outline-light btn-lg">
                        Program Magang
                    </a>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body text-center">
                                <i class="bi bi-briefcase-fill text-primary fs-1 mb-3"></i>
                                <h3 class="fw-bold text-primary mb-0">{{ $stats['total_jobs'] }}</h3>
                                <p class="text-muted mb-0">Lowongan Kerja</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body text-center">
                                <i class="bi bi-people-fill text-primary fs-1 mb-3"></i>
                                <h3 class="fw-bold text-primary mb-0">{{ $stats['total_programs'] }}</h3>
                                <p class="text-muted mb-0">Program Magang</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body text-center">
                                <i class="bi bi-newspaper text-primary fs-1 mb-3"></i>
                                <h3 class="fw-bold text-primary mb-0">{{ $stats['total_news'] }}</h3>
                                <p class="text-muted mb-0">Berita Terbaru</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body text-center">
                                <i class="bi bi-building text-primary fs-1 mb-3"></i>
                                <h3 class="fw-bold text-primary mb-0">{{ $stats['total_companies'] }}</h3>
                                <p class="text-muted mb-0">Perusahaan</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Alerts -->
@if(session('success'))
<div class="container mt-4">
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
</div>
@endif

@if(session('error'))
<div class="container mt-4">
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
</div>
@endif

<!-- Featured Jobs -->
<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold">Lowongan Kerja Terbaru</h2>
            <a href="{{ route('lowongan-kerja') }}" class="btn btn-outline-primary">
                Lihat Semua <i class="bi bi-arrow-right ms-2"></i>
            </a>
        </div>
        
        @if($featuredJobs->count() > 0)
        <div class="row">
            @foreach($featuredJobs as $job)
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    @if($job->gambar)
                    <img src="{{ $job->gambar_url }}" class="card-img-top" alt="{{ $job->judul }}" style="height: 200px; object-fit: cover;">
                    @endif
                    <div class="card-body">
                        <div class="mb-2">
                            <span class="badge bg-primary">{{ $job->tipe_formatted }}</span>
                            <span class="badge bg-secondary">{{ $job->kategori_formatted }}</span>
                        </div>
                        <h5 class="card-title fw-bold">{{ $job->judul }}</h5>
                        <p class="text-muted mb-2">
                            <i class="bi bi-building me-1"></i>{{ $job->perusahaan }}
                        </p>
                        <p class="text-muted mb-2">
                            <i class="bi bi-geo-alt me-1"></i>{{ $job->lokasi }}
                        </p>
                        <p class="text-muted mb-3">
                            <i class="bi bi-cash me-1"></i>{{ $job->gaji_formatted }}
                        </p>
                        <a href="{{ route('lowongan-kerja.detail', $job->id) }}" class="btn btn-primary w-100">
                            Lihat Detail
                        </a>
                    </div>
                    <div class="card-footer bg-white border-top-0">
                        <small class="text-muted">
                            <i class="bi bi-calendar me-1"></i>Berakhir: {{ $job->tanggal_berakhir->format('d M Y') }}
                        </small>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>Belum ada lowongan kerja tersedia saat ini.
        </div>
        @endif
    </div>
</section>

<!-- Featured Programs -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold">Program Magang & MBKM</h2>
            <a href="{{ route('program-magang') }}" class="btn btn-outline-primary">
                Lihat Semua <i class="bi bi-arrow-right ms-2"></i>
            </a>
        </div>
        
        @if($featuredPrograms->count() > 0)
        <div class="row">
            @foreach($featuredPrograms as $program)
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    @if($program->gambar)
                    <img src="{{ $program->gambar_url }}" class="card-img-top" alt="{{ $program->judul }}" style="height: 200px; object-fit: cover;">
                    @endif
                    <div class="card-body">
                        <div class="mb-2">
                            <span class="badge bg-primary">{{ $program->tipe_formatted }}</span>
                        </div>
                        <h5 class="card-title fw-bold">{{ $program->judul }}</h5>
                        <p class="text-muted mb-2">
                            <i class="bi bi-building me-1"></i>{{ $program->perusahaan }}
                        </p>
                        <p class="text-muted mb-2">
                            <i class="bi bi-geo-alt me-1"></i>{{ $program->lokasi }}
                        </p>
                        <p class="text-muted mb-3">
                            <i class="bi bi-clock me-1"></i>{{ $program->durasi_formatted }}
                        </p>
                        <a href="{{ route('program-magang.detail', $program->id) }}" class="btn btn-primary w-100">
                            Lihat Detail
                        </a>
                    </div>
                    <div class="card-footer bg-white border-top-0">
                        <small class="text-muted">
                            <i class="bi bi-calendar me-1"></i>Berakhir: {{ $program->tanggal_berakhir->format('d M Y') }}
                        </small>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>Belum ada program magang tersedia saat ini.
        </div>
        @endif
    </div>
</section>

<!-- Featured News -->
@if($featuredNews->count() > 0)
<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold">Berita & Artikel Terbaru</h2>
            <a href="{{ route('berita') }}" class="btn btn-outline-primary">
                Lihat Semua <i class="bi bi-arrow-right ms-2"></i>
            </a>
        </div>
        
        <div class="row">
            @foreach($featuredNews as $news)
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    @if($news->gambar)
                    <img src="{{ $news->gambar_url }}" class="card-img-top" alt="{{ $news->judul }}" style="height: 200px; object-fit: cover;">
                    @endif
                    <div class="card-body">
                        <span class="badge bg-primary mb-2">{{ $news->kategori_formatted }}</span>
                        <h5 class="card-title fw-bold">{{ $news->judul }}</h5>
                        <p class="card-text text-muted">{{ $news->excerpt }}</p>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <small class="text-muted">
                                <i class="bi bi-calendar me-1"></i>{{ $news->tanggal_publikasi->format('d M Y') }}
                            </small>
                            <a href="{{ route('berita.detail', $news->slug) }}" class="btn btn-sm btn-outline-primary">
                                Baca Selengkapnya
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- CTA Section -->
<section class="bg-gradient-primary text-white py-5">
    <div class="container text-center">
        <h2 class="fw-bold mb-3">Siap Memulai Karir Anda?</h2>
        <p class="lead mb-4">Bergabunglah dengan ribuan mahasiswa yang telah menemukan peluang karir mereka</p>
        <a href="{{ route('lowongan-kerja') }}" class="btn btn-light btn-lg me-3">
            <i class="bi bi-search me-2"></i>Cari Lowongan
        </a>
        <a href="{{ route('kontak') }}" class="btn btn-outline-light btn-lg">
            <i class="bi bi-envelope me-2"></i>Hubungi Kami
        </a>
    </div>
</section>
@endsection