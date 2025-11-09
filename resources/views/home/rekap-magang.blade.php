@extends('layouts.app')

@section('title', 'Rekap Borang Magang - CDC Polibatam')

@section('content')
<!-- Hero Section -->
<section class="bg-gradient-primary text-white py-5">
    <div class="container py-5">
        <div class="row align-items-center">
            <div class="col-lg-8 mx-auto text-center">
                <div class="mb-4">
                    <i class="bi bi-journal-bookmark display-1 mb-3"></i>
                </div>
                <h1 class="display-4 fw-bold mb-3">Rekap Borang Magang</h1>
                <p class="lead mb-4">Akses berbagai dokumen, formulir, dan data terkait program magang mahasiswa Polibatam</p>
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
                                <h4 class="fw-bold mb-3">Tentang Rekap Borang Magang</h4>
                                <p class="text-muted mb-0">
                                    Halaman ini menyediakan akses cepat ke berbagai dokumen dan formulir yang diperlukan untuk proses magang mahasiswa, 
                                    termasuk borang pendaftaran, surat permohonan, konfirmasi penerimaan, hingga data BPJS ketenagakerjaan. 
                                    Klik pada kartu di bawah untuk mengakses dokumen atau formulir yang Anda butuhkan.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Links Grid -->
        <div class="row">
            @foreach($links as $index => $link)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-start mb-3">
                            <div class="flex-shrink-0 me-3">
                                <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                    <i class="{{ $link['icon'] }} text-primary fs-4"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <span class="badge bg-primary bg-opacity-10 text-primary mb-2">
                                    #{{ $index + 1 }}
                                </span>
                            </div>
                        </div>
                        <h5 class="card-title fw-bold mb-3">{{ $link['title'] }}</h5>
                        <p class="card-text text-muted mb-4">{{ $link['description'] }}</p>
                        <a href="{{ $link['url'] }}" target="_blank" class="btn btn-primary w-100">
                            <i class="bi bi-box-arrow-up-right me-2"></i>Klik Disini
                        </a>
                    </div>
                </div>
            </div>
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
                            Jika Anda mengalami kesulitan mengakses dokumen atau memiliki pertanyaan terkait program magang, 
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

<!-- Quick Stats -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-4 mb-md-0">
                <div class="text-center">
                    <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="bi bi-file-earmark-text text-primary fs-1"></i>
                    </div>
                    <h3 class="fw-bold text-primary">12</h3>
                    <p class="text-muted mb-0">Dokumen Tersedia</p>
                </div>
            </div>
            <div class="col-md-4 mb-4 mb-md-0">
                <div class="text-center">
                    <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="bi bi-link-45deg text-primary fs-1"></i>
                    </div>
                    <h3 class="fw-bold text-primary">Akses Cepat</h3>
                    <p class="text-muted mb-0">Link Eksternal</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-center">
                    <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="bi bi-shield-check text-primary fs-1"></i>
                    </div>
                    <h3 class="fw-bold text-primary">Terpercaya</h3>
                    <p class="text-muted mb-0">Data Resmi</p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection