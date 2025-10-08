@extends('layouts.app')

@section('title', 'Program Magang & MBKM - CDC Polibatam')

@section('content')
<!-- Page Header -->
<section class="bg-gradient-primary text-white py-5">
    <div class="container">
        <h1 class="display-5 fw-bold mb-2">Program Magang & MBKM</h1>
        <p class="lead">Tingkatkan pengalaman dan keterampilan Anda melalui program magang berkualitas</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <!-- Search & Filter -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form action="{{ route('program-magang') }}" method="GET">
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label">Cari Program</label>
                            <input type="text" name="search" class="form-control" placeholder="Judul, perusahaan..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tipe Program</label>
                            <select name="tipe" class="form-select">
                                <option value="all">Semua Tipe</option>
                                @foreach($tipeOptions as $key => $value)
                                <option value="{{ $key }}" {{ request('tipe') == $key ? 'selected' : '' }}>{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Lokasi</label>
                            <input type="text" name="lokasi" class="form-control" placeholder="Kota..." value="{{ request('lokasi') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Urutkan</label>
                            <select name="sort" class="form-select">
                                <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Terbaru</option>
                                <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Terpopuler</option>
                                <option value="ending_soon" {{ request('sort') == 'ending_soon' ? 'selected' : '' }}>Segera Berakhir</option>
                            </select>
                        </div>
                        <div class="col-12 d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search me-2"></i>Cari
                            </button>
                            <a href="{{ route('program-magang') }}" class="btn btn-outline-secondary">
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
                Menampilkan {{ $programs->firstItem() ?? 0 }} - {{ $programs->lastItem() ?? 0 }} dari {{ $programs->total() }} program
            </p>
        </div>

        <!-- Program Listings -->
        @if($programs->count() > 0)
        <div class="row">
            @foreach($programs as $program)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100">
                    @if($program->gambar)
                    <img src="{{ $program->gambar_url }}" class="card-img-top" alt="{{ $program->judul }}" style="height: 200px; object-fit: cover;">
                    @endif
                    <div class="card-body">
                        <div class="mb-2">
                            <span class="badge bg-primary">{{ $program->tipe_formatted }}</span>
                        </div>
                        <h5 class="card-title fw-bold">{{ Str::limit($program->judul, 50) }}</h5>
                        <p class="text-muted mb-2">
                            <i class="bi bi-building me-1"></i>{{ $program->perusahaan }}
                        </p>
                        <p class="text-muted mb-2">
                            <i class="bi bi-geo-alt me-1"></i>{{ $program->lokasi }}
                        </p>
                        <p class="text-muted mb-2">
                            <i class="bi bi-clock me-1"></i>{{ $program->durasi_formatted }}
                        </p>
                        @if($program->kuota)
                        <p class="text-muted mb-3">
                            <i class="bi bi-people me-1"></i>{{ $program->kuota_formatted }}
                        </p>
                        @endif
                        <a href="{{ route('program-magang.detail', $program->id) }}" class="btn btn-primary w-100">
                            Lihat Detail
                        </a>
                    </div>
                    <div class="card-footer bg-white border-top-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="bi bi-calendar me-1"></i>{{ $program->tanggal_berakhir->format('d M Y') }}
                            </small>
                            <small class="text-muted">
                                <i class="bi bi-eye me-1"></i>{{ $program->views_count }} views
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $programs->links() }}
        </div>
        @else
        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>Tidak ada program magang yang sesuai dengan kriteria pencarian Anda.
        </div>
        @endif
    </div>
</section>
@endsection