@extends('layouts.app')

@section('title', 'Rekap Borang MBKM - CDC Polibatam')

@section('content')
<!-- Hero Section -->
<section class="bg-gradient-primary text-white py-5">
    <div class="container py-5">
        <div class="row align-items-center">
            <div class="col-lg-8 mx-auto text-center">
                <div class="mb-4">
                    <i class="bi bi-journal-code display-1 mb-3"></i>
                </div>
                <h1 class="display-4 fw-bold mb-3">Rekap Borang MBKM</h1>
                <p class="lead mb-4">Akses data dan rekapitulasi program Merdeka Belajar Kampus Merdeka (MBKM) mahasiswa Polibatam</p>
                <div class="d-inline-flex align-items-center bg-white bg-opacity-10 rounded-pill px-4 py-2">
                    <i class="bi bi-info-circle me-2"></i>
                    <span>{{ count($links) }} Dokumen & Link Tersedia</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Main Content -->
<section class="py-5">
    <div class="container">
        <!-- Introduction Card -->
        <div class="row mb-5">
            <div class="col-lg-10 mx-auto">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-start">
                            <div class="flex-shrink-0 me-3">
                                <div class="bg-primary bg-opacity-10 rounded p-3">
                                    <i class="bi bi-info-circle text-primary fs-2"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h4 class="fw-bold mb-3">Tentang Rekap Borang MBKM</h4>
                                <p class="text-muted mb-3">
                                    Halaman ini menyediakan akses ke berbagai data dan rekapitulasi program MBKM (Merdeka Belajar Kampus Merdeka) 
                                    yang meliputi MSIB (Magang dan Studi Independen Bersertifikat), PMM (Pertukaran Mahasiswa Merdeka), 
                                    serta program IISMAVO.
                                </p>
                                <div class="alert alert-info mb-0">
                                    <i class="bi bi-lightbulb me-2"></i>
                                    <strong>Info:</strong> Data yang tersedia mencakup berbagai batch dan periode pelaksanaan program MBKM.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Category Sections -->
        
        <!-- IISMAVO Section -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="d-flex align-items-center mb-4">
                    <div class="bg-primary bg-opacity-10 rounded p-2 me-3">
                        <i class="bi bi-award text-primary fs-4"></i>
                    </div>
                    <h3 class="fw-bold mb-0">Program IISMAVO</h3>
                </div>
            </div>
            @foreach($links as $link)
                @if(str_contains($link['title'], 'IISMAVO'))
                <div class="col-md-6 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-start mb-3">
                                <div class="flex-shrink-0 me-3">
                                    <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                                        <i class="{{ $link['icon'] }} text-warning fs-4"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <span class="badge bg-warning bg-opacity-10 text-warning mb-2">IISMAVO</span>
                                </div>
                            </div>
                            <h5 class="card-title fw-bold mb-3">{{ $link['title'] }}</h5>
                            <p class="card-text text-muted mb-4">{{ $link['description'] }}</p>
                            <a href="{{ $link['url'] }}" target="_blank" class="btn btn-warning w-100">
                                <i class="bi bi-box-arrow-up-right me-2"></i>Klik Disini
                            </a>
                        </div>
                    </div>
                </div>
                @endif
            @endforeach
        </div>

        <!-- MSIB Section -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="d-flex align-items-center mb-4">
                    <div class="bg-primary bg-opacity-10 rounded p-2 me-3">
                        <i class="bi bi-journal-text text-primary fs-4"></i>
                    </div>
                    <h3 class="fw-bold mb-0">Program MSIB</h3>
                </div>
            </div>
            @foreach($links as $link)
                @if(str_contains($link['title'], 'MSIB'))
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-start mb-3">
                                <div class="flex-shrink-0 me-3">
                                    <div class="bg-success bg-opacity-10 rounded-circle p-3">
                                        <i class="{{ $link['icon'] }} text-success fs-4"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <span class="badge bg-success bg-opacity-10 text-success mb-2">MSIB</span>
                                </div>
                            </div>
                            <h5 class="card-title fw-bold mb-3">{{ $link['title'] }}</h5>
                            <p class="card-text text-muted mb-4">{{ $link['description'] }}</p>
                            <a href="{{ $link['url'] }}" target="_blank" class="btn btn-success w-100">
                                <i class="bi bi-box-arrow-up-right me-2"></i>Klik Disini
                            </a>
                        </div>
                    </div>
                </div>
                @endif
            @endforeach
        </div>

        <!-- PMM Section -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="d-flex align-items-center mb-4">
                    <div class="bg-primary bg-opacity-10 rounded p-2 me-3">
                        <i class="bi bi-people text-primary fs-4"></i>
                    </div>
                    <h3 class="fw-bold mb-0">Program PMM</h3>
                </div>
            </div>
            @foreach($links as $link)
                @if(str_contains($link['title'], 'PMM'))
                <div class="col-md-6 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-start mb-3">
                                <div class="flex-shrink-0 me-3">
                                    <div class="bg-info bg-opacity-10 rounded-circle p-3">
                                        <i class="{{ $link['icon'] }} text-info fs-4"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <span class="badge bg-info bg-opacity-10 text-info mb-2">PMM</span>
                                </div>
                            </div>
                            <h5 class="card-title fw-bold mb-3">{{ $link['title'] }}</h5>
                            <p class="card-text text-muted mb-4">{{ $link['description'] }}</p>
                            <a href="{{ $link['url'] }}" target="_blank" class="btn btn-info w-100">
                                <i class="bi bi-box-arrow-up-right me-2"></i>Klik Disini
                            </a>
                        </div>
                    </div>
                </div>
                @endif
            @endforeach
        </div>

        <!-- Help Section -->
        <div class="row mt-5">
            <div class="col-lg-10 mx-auto">
                <div class="card border-0 bg-light">
                    <div class="card-body p-4 text-center">
                        <i class="bi bi-question-circle text-primary fs-1 mb-3"></i>
                        <h5 class="fw-bold mb-3">Butuh Bantuan?</h5>
                        <p class="text-muted mb-3">
                            Jika Anda memiliki pertanyaan terkait program MBKM atau mengalami kesulitan mengakses data, 
                            silakan hubungi tim Career Development Center.
                        </p>
                        <a href="{{ route('kontak') }}" class="btn btn-outline-primary">
                            <i class="bi bi-envelope me-2"></i>Hubungi Kami
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Quick Info -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-md-3 mb-4 mb-md-0">
                <div class="text-center">
                    <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                        <i class="bi bi-award text-warning fs-2"></i>
                    </div>
                    <h5 class="fw-bold">IISMAVO</h5>
                    <p class="text-muted small mb-0">Program Beasiswa</p>
                </div>
            </div>
            <div class="col-md-3 mb-4 mb-md-0">
                <div class="text-center">
                    <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                        <i class="bi bi-journal-text text-success fs-2"></i>
                    </div>
                    <h5 class="fw-bold">MSIB</h5>
                    <p class="text-muted small mb-0">Magang Bersertifikat</p>
                </div>
            </div>
            <div class="col-md-3 mb-4 mb-md-0">
                <div class="text-center">
                    <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                        <i class="bi bi-people text-info fs-2"></i>
                    </div>
                    <h5 class="fw-bold">PMM</h5>
                    <p class="text-muted small mb-0">Pertukaran Mahasiswa</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="text-center">
                    <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                        <i class="bi bi-file-earmark-spreadsheet text-primary fs-2"></i>
                    </div>
                    <h5 class="fw-bold">{{ count($links) }}</h5>
                    <p class="text-muted small mb-0">Total Data</p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection