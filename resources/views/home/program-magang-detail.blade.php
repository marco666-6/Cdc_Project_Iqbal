@extends('layouts.app')

@section('title', $program->judul . ' - CDC Polibatam')

@section('content')
<section class="py-5">
    <div class="container">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
                <li class="breadcrumb-item"><a href="{{ route('program-magang') }}">Program Magang</a></li>
                <li class="breadcrumb-item active">{{ Str::limit($program->judul, 30) }}</li>
            </ol>
        </nav>

        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
                    @if($program->gambar)
                    <img src="{{ $program->gambar_url }}" class="card-img-top" alt="{{ $program->judul }}" style="max-height: 400px; object-fit: cover;">
                    @endif
                    
                    <div class="card-body">
                        <div class="mb-3">
                            <span class="badge bg-primary">{{ $program->tipe_formatted }}</span>
                            @if($program->is_expired)
                            <span class="badge bg-danger">Expired</span>
                            @endif
                        </div>

                        <h1 class="fw-bold mb-3">{{ $program->judul }}</h1>

                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <p class="mb-2">
                                    <i class="bi bi-building text-primary me-2"></i>
                                    <strong>Perusahaan:</strong> {{ $program->perusahaan }}
                                </p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <p class="mb-2">
                                    <i class="bi bi-geo-alt text-primary me-2"></i>
                                    <strong>Lokasi:</strong> {{ $program->lokasi }}
                                </p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <p class="mb-2">
                                    <i class="bi bi-clock text-primary me-2"></i>
                                    <strong>Durasi:</strong> {{ $program->durasi_formatted }}
                                </p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <p class="mb-2">
                                    <i class="bi bi-calendar-check text-primary me-2"></i>
                                    <strong>Mulai:</strong> {{ $program->tanggal_mulai->format('d F Y') }}
                                </p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <p class="mb-2">
                                    <i class="bi bi-calendar text-primary me-2"></i>
                                    <strong>Berakhir:</strong> {{ $program->tanggal_berakhir->format('d F Y') }}
                                </p>
                            </div>
                            @if($program->kuota)
                            <div class="col-md-6 mb-3">
                                <p class="mb-2">
                                    <i class="bi bi-people text-primary me-2"></i>
                                    <strong>Kuota:</strong> {{ $program->kuota_formatted }}
                                </p>
                            </div>
                            @endif
                            <div class="col-md-6 mb-3">
                                <p class="mb-2">
                                    <i class="bi bi-eye text-primary me-2"></i>
                                    <strong>Dilihat:</strong> {{ $program->views_count }} kali
                                </p>
                            </div>
                        </div>

                        <hr>

                        <h4 class="fw-bold mb-3">Deskripsi Program</h4>
                        <div class="mb-4">
                            {!! nl2br(e($program->deskripsi)) !!}
                        </div>

                        @if($program->persyaratan)
                        <h4 class="fw-bold mb-3">Persyaratan</h4>
                        <div class="mb-4">
                            {!! nl2br(e($program->persyaratan)) !!}
                        </div>
                        @endif

                        @if($program->benefit)
                        <h4 class="fw-bold mb-3">Benefit</h4>
                        <div class="mb-4">
                            {!! nl2br(e($program->benefit)) !!}
                        </div>
                        @endif

                        @if(!$program->is_expired && $program->link_pendaftaran)
                        <div class="d-grid gap-2">
                            <a href="{{ $program->link_pendaftaran }}" target="_blank" class="btn btn-gradient btn-lg">
                                <i class="bi bi-box-arrow-up-right me-2"></i>Daftar Sekarang
                            </a>
                        </div>
                        @elseif($program->is_expired)
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>Pendaftaran program ini sudah ditutup
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Program Info -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">Informasi Program</h5>
                        <p class="mb-2"><strong>{{ $program->perusahaan }}</strong></p>
                        <p class="text-muted mb-3">
                            <i class="bi bi-geo-alt me-1"></i>{{ $program->lokasi }}
                        </p>
                        <hr>
                        <div class="mb-2">
                            <small class="text-muted">Tipe Program</small>
                            <p class="mb-0 fw-bold">{{ $program->tipe_formatted }}</p>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted">Durasi</small>
                            <p class="mb-0 fw-bold">{{ $program->durasi_formatted }}</p>
                        </div>
                        @if($program->kuota)
                        <div class="mb-2">
                            <small class="text-muted">Kuota</small>
                            <p class="mb-0 fw-bold">{{ $program->kuota_formatted }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Registration Info -->
                @if(!$program->is_expired && $program->link_pendaftaran)
                <div class="card shadow-sm mb-4 border-primary">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3 text-primary">Cara Mendaftar</h5>
                        <p class="mb-3">Klik tombol di bawah untuk mengisi formulir pendaftaran</p>
                        <a href="{{ $program->link_pendaftaran }}" target="_blank" class="btn btn-outline-primary w-100 mb-2">
                            <i class="bi bi-box-arrow-up-right me-2"></i>Link Pendaftaran
                        </a>
                        <small class="text-muted">Batas akhir: {{ $program->tanggal_berakhir->format('d F Y') }}</small>
                    </div>
                </div>
                @endif

                <!-- Share -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">Bagikan</h5>
                        <div class="d-flex gap-2">
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(Request::fullUrl()) }}" target="_blank" class="btn btn-outline-primary flex-fill">
                                <i class="bi bi-facebook"></i>
                            </a>
                            <a href="https://twitter.com/intent/tweet?url={{ urlencode(Request::fullUrl()) }}&text={{ urlencode($program->judul) }}" target="_blank" class="btn btn-outline-info flex-fill">
                                <i class="bi bi-twitter"></i>
                            </a>
                            <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(Request::fullUrl()) }}" target="_blank" class="btn btn-outline-primary flex-fill">
                                <i class="bi bi-linkedin"></i>
                            </a>
                            <a href="https://wa.me/?text={{ urlencode($program->judul . ' - ' . Request::fullUrl()) }}" target="_blank" class="btn btn-outline-success flex-fill">
                                <i class="bi bi-whatsapp"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Programs -->
        @if($relatedPrograms->count() > 0)
        <section class="mt-5">
            <h3 class="fw-bold mb-4">Program Terkait</h3>
            <div class="row">
                @foreach($relatedPrograms as $prog)
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        @if($prog->gambar)
                        <img src="{{ $prog->gambar_url }}" class="card-img-top" alt="{{ $prog->judul }}" style="height: 150px; object-fit: cover;">
                        @endif
                        <div class="card-body">
                            <span class="badge bg-primary mb-2">{{ $prog->tipe_formatted }}</span>
                            <h5 class="card-title fw-bold">{{ Str::limit($prog->judul, 40) }}</h5>
                            <p class="text-muted mb-2">
                                <i class="bi bi-building me-1"></i>{{ $prog->perusahaan }}
                            </p>
                            <p class="text-muted mb-3">
                                <i class="bi bi-geo-alt me-1"></i>{{ $prog->lokasi }}
                            </p>
                            <a href="{{ route('program-magang.detail', $prog->id) }}" class="btn btn-outline-primary btn-sm w-100">
                                Lihat Detail
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </section>
        @endif
    </div>
</section>
@endsection