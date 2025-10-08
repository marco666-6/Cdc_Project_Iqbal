@extends('layouts.app')

@section('title', $lowongan->judul . ' - CDC Polibatam')

@section('content')
<section class="py-5">
    <div class="container">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
                <li class="breadcrumb-item"><a href="{{ route('lowongan-kerja') }}">Lowongan Kerja</a></li>
                <li class="breadcrumb-item active">{{ Str::limit($lowongan->judul, 30) }}</li>
            </ol>
        </nav>

        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
                    @if($lowongan->gambar)
                    <img src="{{ $lowongan->gambar_url }}" class="card-img-top" alt="{{ $lowongan->judul }}" style="max-height: 400px; object-fit: cover;">
                    @endif
                    
                    <div class="card-body">
                        <div class="mb-3">
                            <span class="badge bg-primary">{{ $lowongan->tipe_formatted }}</span>
                            <span class="badge bg-secondary">{{ $lowongan->kategori_formatted }}</span>
                            @if($lowongan->is_expired)
                            <span class="badge bg-danger">Expired</span>
                            @endif
                        </div>

                        <h1 class="fw-bold mb-3">{{ $lowongan->judul }}</h1>

                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <p class="mb-2">
                                    <i class="bi bi-building text-primary me-2"></i>
                                    <strong>Perusahaan:</strong> {{ $lowongan->perusahaan }}
                                </p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <p class="mb-2">
                                    <i class="bi bi-geo-alt text-primary me-2"></i>
                                    <strong>Lokasi:</strong> {{ $lowongan->lokasi }}
                                </p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <p class="mb-2">
                                    <i class="bi bi-cash text-primary me-2"></i>
                                    <strong>Gaji:</strong> {{ $lowongan->gaji_formatted }}
                                </p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <p class="mb-2">
                                    <i class="bi bi-calendar text-primary me-2"></i>
                                    <strong>Berakhir:</strong> {{ $lowongan->tanggal_berakhir->format('d F Y') }}
                                </p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <p class="mb-2">
                                    <i class="bi bi-eye text-primary me-2"></i>
                                    <strong>Dilihat:</strong> {{ $lowongan->views_count }} kali
                                </p>
                            </div>
                        </div>

                        <hr>

                        <h4 class="fw-bold mb-3">Deskripsi Pekerjaan</h4>
                        <div class="mb-4">
                            {!! nl2br(e($lowongan->deskripsi)) !!}
                        </div>

                        @if(!$lowongan->is_expired)
                        <div class="d-grid gap-2">
                            <a href="mailto:{{ $lowongan->email_aplikasi }}" class="btn btn-gradient btn-lg">
                                <i class="bi bi-envelope-fill me-2"></i>Lamar Sekarang
                            </a>
                        </div>
                        @else
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>Lowongan ini sudah berakhir
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Company Info -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">Informasi Perusahaan</h5>
                        <p class="mb-2"><strong>{{ $lowongan->perusahaan }}</strong></p>
                        <p class="text-muted mb-0">
                            <i class="bi bi-geo-alt me-1"></i>{{ $lowongan->lokasi }}
                        </p>
                    </div>
                </div>

                <!-- Application Info -->
                @if(!$lowongan->is_expired)
                <div class="card shadow-sm mb-4 border-primary">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3 text-primary">Cara Melamar</h5>
                        <p class="mb-3">Kirimkan CV dan surat lamaran Anda ke:</p>
                        <a href="mailto:{{ $lowongan->email_aplikasi }}" class="btn btn-outline-primary w-100 mb-2">
                            <i class="bi bi-envelope me-2"></i>{{ $lowongan->email_aplikasi }}
                        </a>
                        <small class="text-muted">Batas akhir: {{ $lowongan->tanggal_berakhir->format('d F Y') }}</small>
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
                            <a href="https://twitter.com/intent/tweet?url={{ urlencode(Request::fullUrl()) }}&text={{ urlencode($lowongan->judul) }}" target="_blank" class="btn btn-outline-info flex-fill">
                                <i class="bi bi-twitter"></i>
                            </a>
                            <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(Request::fullUrl()) }}" target="_blank" class="btn btn-outline-primary flex-fill">
                                <i class="bi bi-linkedin"></i>
                            </a>
                            <a href="https://wa.me/?text={{ urlencode($lowongan->judul . ' - ' . Request::fullUrl()) }}" target="_blank" class="btn btn-outline-success flex-fill">
                                <i class="bi bi-whatsapp"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Jobs -->
        @if($relatedJobs->count() > 0)
        <section class="mt-5">
            <h3 class="fw-bold mb-4">Lowongan Terkait</h3>
            <div class="row">
                @foreach($relatedJobs as $job)
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        @if($job->gambar)
                        <img src="{{ $job->gambar_url }}" class="card-img-top" alt="{{ $job->judul }}" style="height: 150px; object-fit: cover;">
                        @endif
                        <div class="card-body">
                            <span class="badge bg-primary mb-2">{{ $job->tipe_formatted }}</span>
                            <h5 class="card-title fw-bold">{{ Str::limit($job->judul, 40) }}</h5>
                            <p class="text-muted mb-2">
                                <i class="bi bi-building me-1"></i>{{ $job->perusahaan }}
                            </p>
                            <p class="text-muted mb-3">
                                <i class="bi bi-geo-alt me-1"></i>{{ $job->lokasi }}
                            </p>
                            <a href="{{ route('lowongan-kerja.detail', $job->id) }}" class="btn btn-outline-primary btn-sm w-100">
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