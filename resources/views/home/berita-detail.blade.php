@extends('layouts.app')

@section('title', $berita->judul . ' - CDC Polibatam')

@section('content')
<section class="py-5">
    <div class="container">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
                <li class="breadcrumb-item"><a href="{{ route('berita') }}">Berita</a></li>
                <li class="breadcrumb-item active">{{ Str::limit($berita->judul, 30) }}</li>
            </ol>
        </nav>

        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <article class="card shadow-sm mb-4">
                    @if($berita->gambar)
                    <img src="{{ $berita->gambar_url }}" class="card-img-top" alt="{{ $berita->judul }}" style="max-height: 500px; object-fit: cover;">
                    @endif
                    
                    <div class="card-body">
                        <div class="mb-3">
                            <span class="badge bg-primary">{{ $berita->kategori_formatted }}</span>
                            @if($berita->is_featured)
                            <span class="badge bg-warning text-dark">
                                <i class="bi bi-star-fill"></i> Featured
                            </span>
                            @endif
                        </div>

                        <h1 class="fw-bold mb-3">{{ $berita->judul }}</h1>

                        <div class="d-flex flex-wrap gap-3 mb-4 pb-3 border-bottom">
                            <div class="text-muted">
                                <i class="bi bi-person-circle me-1"></i>
                                <strong>Penulis:</strong> {{ $berita->penulis }}
                            </div>
                            <div class="text-muted">
                                <i class="bi bi-calendar me-1"></i>
                                {{ $berita->tanggal_publikasi->format('d F Y') }}
                            </div>
                            <div class="text-muted">
                                <i class="bi bi-clock me-1"></i>
                                {{ $berita->reading_time }}
                            </div>
                            <div class="text-muted">
                                <i class="bi bi-eye me-1"></i>
                                {{ $berita->views_count }} views
                            </div>
                        </div>

                        @if($berita->ringkasan)
                        <div class="alert alert-light border-start border-primary border-4 mb-4">
                            <h5 class="fw-bold mb-2">Ringkasan</h5>
                            <p class="mb-0">{{ $berita->ringkasan }}</p>
                        </div>
                        @endif

                        <div class="article-content">
                            {!! nl2br(e($berita->konten)) !!}
                        </div>

                        <hr class="my-4">

                        <!-- Share Buttons -->
                        <div class="d-flex align-items-center gap-2">
                            <strong>Bagikan:</strong>
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(Request::fullUrl()) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-facebook"></i>
                            </a>
                            <a href="https://twitter.com/intent/tweet?url={{ urlencode(Request::fullUrl()) }}&text={{ urlencode($berita->judul) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                <i class="bi bi-twitter"></i>
                            </a>
                            <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(Request::fullUrl()) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-linkedin"></i>
                            </a>
                            <a href="https://wa.me/?text={{ urlencode($berita->judul . ' - ' . Request::fullUrl()) }}" target="_blank" class="btn btn-sm btn-outline-success">
                                <i class="bi bi-whatsapp"></i>
                            </a>
                        </div>
                    </div>
                </article>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Article Info -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">Informasi Artikel</h5>
                        <div class="mb-2">
                            <small class="text-muted">Kategori</small>
                            <p class="mb-0 fw-bold">{{ $berita->kategori_formatted }}</p>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted">Penulis</small>
                            <p class="mb-0 fw-bold">{{ $berita->penulis }}</p>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted">Dipublikasikan</small>
                            <p class="mb-0 fw-bold">{{ $berita->tanggal_publikasi->format('d F Y') }}</p>
                        </div>
                        <div class="mb-0">
                            <small class="text-muted">Waktu Baca</small>
                            <p class="mb-0 fw-bold">{{ $berita->reading_time }}</p>
                        </div>
                    </div>
                </div>

                <!-- Newsletter Subscribe -->
                <div class="card shadow-sm mb-4 bg-gradient-primary text-white">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">Berlangganan Newsletter</h5>
                        <p class="mb-3">Dapatkan berita terbaru langsung di email Anda</p>
                        <form action="{{ route('newsletter.subscribe') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <input type="text" name="nama" class="form-control" placeholder="Nama (opsional)">
                            </div>
                            <div class="mb-3">
                                <input type="email" name="email" class="form-control" placeholder="Email Anda" required>
                            </div>
                            <button type="submit" class="btn btn-light w-100">
                                <i class="bi bi-envelope me-2"></i>Berlangganan
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related News -->
        @if($relatedNews->count() > 0)
        <section class="mt-5">
            <h3 class="fw-bold mb-4">Berita Terkait</h3>
            <div class="row">
                @foreach($relatedNews as $news)
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        @if($news->gambar)
                        <img src="{{ $news->gambar_url }}" class="card-img-top" alt="{{ $news->judul }}" style="height: 150px; object-fit: cover;">
                        @endif
                        <div class="card-body">
                            <span class="badge bg-primary mb-2">{{ $news->kategori_formatted }}</span>
                            <h5 class="card-title fw-bold">{{ Str::limit($news->judul, 50) }}</h5>
                            <p class="card-text text-muted small">{{ Str::limit($news->excerpt, 100) }}</p>
                            <a href="{{ route('berita.detail', $news->slug) }}" class="btn btn-outline-primary btn-sm w-100">
                                Baca Selengkapnya
                            </a>
                        </div>
                        <div class="card-footer bg-white border-top-0">
                            <small class="text-muted">
                                <i class="bi bi-calendar me-1"></i>{{ $news->tanggal_publikasi->format('d M Y') }}
                            </small>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </section>
        @endif
    </div>
</section>

@section('styles')
<style>
    .article-content {
        font-size: 1.1rem;
        line-height: 1.8;
        color: #333;
    }
    
    .article-content p {
        margin-bottom: 1.5rem;
    }
</style>
@endsection
@endsection