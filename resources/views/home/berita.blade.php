@extends('layouts.app')

@section('title', 'Berita & Artikel - CDC Polibatam')

@section('content')
<!-- Page Header -->
<section class="bg-gradient-primary text-white py-5">
    <div class="container">
        <h1 class="display-5 fw-bold mb-2">Berita & Artikel</h1>
        <p class="lead">Informasi terbaru seputar karir, magang, dan pengembangan diri</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <!-- Featured News -->
        @if($featuredNews->count() > 0)
        <div class="mb-5">
            <h3 class="fw-bold mb-4">Berita Pilihan</h3>
            <div class="row">
                @foreach($featuredNews as $news)
                <div class="col-md-4 mb-4">
                    <div class="card h-100 border-primary">
                        @if($news->gambar)
                        <img src="{{ $news->gambar_url }}" class="card-img-top" alt="{{ $news->judul }}" style="height: 200px; object-fit: cover;">
                        @endif
                        <div class="card-body">
                            <span class="badge bg-primary mb-2">{{ $news->kategori_formatted }}</span>
                            <span class="badge bg-warning text-dark mb-2">
                                <i class="bi bi-star-fill"></i> Featured
                            </span>
                            <h5 class="card-title fw-bold">{{ Str::limit($news->judul, 60) }}</h5>
                            <p class="card-text text-muted">{{ $news->excerpt }}</p>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <small class="text-muted">
                                    <i class="bi bi-calendar me-1"></i>{{ $news->tanggal_publikasi->format('d M Y') }}
                                </small>
                                <small class="text-muted">
                                    <i class="bi bi-clock me-1"></i>{{ $news->reading_time }}
                                </small>
                            </div>
                            <a href="{{ route('berita.detail', $news->slug) }}" class="btn btn-outline-primary w-100 mt-3">
                                Baca Selengkapnya
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Search & Filter -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form action="{{ route('berita') }}" method="GET">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Cari Berita</label>
                            <input type="text" name="search" class="form-control" placeholder="Judul, konten..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Kategori</label>
                            <select name="kategori" class="form-select">
                                <option value="all">Semua Kategori</option>
                                @foreach($kategoriOptions as $key => $value)
                                <option value="{{ $key }}" {{ request('kategori') == $key ? 'selected' : '' }}>{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Urutkan</label>
                            <select name="sort" class="form-select">
                                <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Terbaru</option>
                                <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Terpopuler</option>
                                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Terlama</option>
                            </select>
                        </div>
                        <div class="col-12 d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search me-2"></i>Cari
                            </button>
                            <a href="{{ route('berita') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-clockwise me-2"></i>Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Results Info -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <p class="mb-0 text-muted">
                Menampilkan {{ $berita->firstItem() ?? 0 }} - {{ $berita->lastItem() ?? 0 }} dari {{ $berita->total() }} berita
            </p>
        </div>

        <!-- News Listings -->
        @if($berita->count() > 0)
        <div class="row">
            @foreach($berita as $news)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100">
                    @if($news->gambar)
                    <img src="{{ $news->gambar_url }}" class="card-img-top" alt="{{ $news->judul }}" style="height: 200px; object-fit: cover;">
                    @endif
                    <div class="card-body">
                        <div class="mb-2">
                            <span class="badge bg-primary">{{ $news->kategori_formatted }}</span>
                            @if($news->is_featured)
                            <span class="badge bg-warning text-dark">
                                <i class="bi bi-star-fill"></i>
                            </span>
                            @endif
                        </div>
                        <h5 class="card-title fw-bold">{{ Str::limit($news->judul, 60) }}</h5>
                        <p class="card-text text-muted">{{ $news->excerpt }}</p>
                        <div class="mb-3">
                            <small class="text-muted">
                                <i class="bi bi-person me-1"></i>{{ $news->penulis }}
                            </small>
                        </div>
                        <a href="{{ route('berita.detail', $news->slug) }}" class="btn btn-outline-primary w-100">
                            Baca Selengkapnya
                        </a>
                    </div>
                    <div class="card-footer bg-white border-top-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="bi bi-calendar me-1"></i>{{ $news->tanggal_publikasi->format('d M Y') }}
                            </small>
                            <small class="text-muted">
                                <i class="bi bi-eye me-1"></i>{{ $news->views_count }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $berita->links() }}
        </div>
        @else
        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>Tidak ada berita yang sesuai dengan kriteria pencarian Anda.
        </div>
        @endif
    </div>
</section>
@endsection