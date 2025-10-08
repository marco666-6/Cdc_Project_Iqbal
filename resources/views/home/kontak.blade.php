@extends('layouts.app')

@section('title', 'Kontak - CDC Polibatam')

@section('content')
<!-- Page Header -->
<section class="bg-gradient-primary text-white py-5">
    <div class="container">
        <h1 class="display-5 fw-bold mb-2">Hubungi Kami</h1>
        <p class="lead">Kami siap membantu Anda dalam mengembangkan karir</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row">
            <!-- Contact Information -->
            <div class="col-lg-4 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body p-4">
                        <h3 class="fw-bold mb-4">Informasi Kontak</h3>
                        
                        @if($kontak->alamat)
                        <div class="mb-4">
                            <div class="d-flex align-items-start">
                                <div class="bg-primary bg-opacity-10 rounded p-3 me-3">
                                    <i class="bi bi-geo-alt-fill text-primary fs-4"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-2">Alamat</h5>
                                    <p class="text-muted mb-0">{!! nl2br(e($kontak->alamat)) !!}</p>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($kontak->telepon)
                        <div class="mb-4">
                            <div class="d-flex align-items-start">
                                <div class="bg-primary bg-opacity-10 rounded p-3 me-3">
                                    <i class="bi bi-telephone-fill text-primary fs-4"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-2">Telepon</h5>
                                    <a href="tel:{{ $kontak->telepon }}" class="text-decoration-none">{{ $kontak->telepon }}</a>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($kontak->email)
                        <div class="mb-4">
                            <div class="d-flex align-items-start">
                                <div class="bg-primary bg-opacity-10 rounded p-3 me-3">
                                    <i class="bi bi-envelope-fill text-primary fs-4"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-2">Email</h5>
                                    <a href="mailto:{{ $kontak->email }}" class="text-decoration-none">{{ $kontak->email }}</a>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($kontak->whatsapp)
                        <div class="mb-4">
                            <div class="d-flex align-items-start">
                                <div class="bg-success bg-opacity-10 rounded p-3 me-3">
                                    <i class="bi bi-whatsapp text-success fs-4"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-2">WhatsApp</h5>
                                    <a href="{{ $kontak->whatsapp_link }}" target="_blank" class="text-decoration-none">{{ $kontak->whatsapp }}</a>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($kontak->jam_operasional)
                        <div class="mb-4">
                            <div class="d-flex align-items-start">
                                <div class="bg-primary bg-opacity-10 rounded p-3 me-3">
                                    <i class="bi bi-clock-fill text-primary fs-4"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-2">Jam Operasional</h5>
                                    <p class="text-muted mb-0">{!! nl2br(e($kontak->jam_operasional)) !!}</p>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Social Media -->
                        <div class="mt-4">
                            <h5 class="fw-bold mb-3">Ikuti Kami</h5>
                            <div class="d-flex gap-2">
                                @if($kontak->facebook)
                                <a href="{{ $kontak->facebook }}" target="_blank" class="btn btn-outline-primary">
                                    <i class="bi bi-facebook fs-5"></i>
                                </a>
                                @endif
                                @if($kontak->instagram)
                                <a href="{{ $kontak->instagram }}" target="_blank" class="btn btn-outline-danger">
                                    <i class="bi bi-instagram fs-5"></i>
                                </a>
                                @endif
                                @if($kontak->linkedin)
                                <a href="{{ $kontak->linkedin }}" target="_blank" class="btn btn-outline-primary">
                                    <i class="bi bi-linkedin fs-5"></i>
                                </a>
                                @endif
                                @if($kontak->twitter)
                                <a href="{{ $kontak->twitter }}" target="_blank" class="btn btn-outline-info">
                                    <i class="bi bi-twitter fs-5"></i>
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Map -->
            <div class="col-lg-8 mb-4">
                @if($kontak->google_maps_embed)
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body p-0">
                        <div class="ratio ratio-4x3">
                            {!! $kontak->google_maps_embed !!}
                        </div>
                    </div>
                </div>
                @else
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body d-flex align-items-center justify-content-center" style="min-height: 400px;">
                        <div class="text-center text-muted">
                            <i class="bi bi-map fs-1 mb-3"></i>
                            <p>Peta lokasi belum tersedia</p>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Quick Links -->
        <div class="row mt-5">
            <div class="col-12">
                <h3 class="fw-bold mb-4 text-center">Akses Cepat</h3>
            </div>
            <div class="col-md-3 mb-4">
                <a href="{{ route('lowongan-kerja') }}" class="text-decoration-none">
                    <div class="card shadow-sm border-0 h-100 hover-card">
                        <div class="card-body text-center p-4">
                            <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex p-3 mb-3">
                                <i class="bi bi-briefcase-fill text-primary fs-2"></i>
                            </div>
                            <h5 class="fw-bold text-dark">Lowongan Kerja</h5>
                            <p class="text-muted small mb-0">Temukan peluang karir terbaik</p>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-3 mb-4">
                <a href="{{ route('program-magang') }}" class="text-decoration-none">
                    <div class="card shadow-sm border-0 h-100 hover-card">
                        <div class="card-body text-center p-4">
                            <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex p-3 mb-3">
                                <i class="bi bi-people-fill text-primary fs-2"></i>
                            </div>
                            <h5 class="fw-bold text-dark">Program Magang</h5>
                            <p class="text-muted small mb-0">Dapatkan pengalaman kerja</p>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-3 mb-4">
                <a href="{{ route('berita') }}" class="text-decoration-none">
                    <div class="card shadow-sm border-0 h-100 hover-card">
                        <div class="card-body text-center p-4">
                            <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex p-3 mb-3">
                                <i class="bi bi-newspaper text-primary fs-2"></i>
                            </div>
                            <h5 class="fw-bold text-dark">Berita & Artikel</h5>
                            <p class="text-muted small mb-0">Informasi terkini karir</p>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-3 mb-4">
                <a href="{{ route('tentang') }}" class="text-decoration-none">
                    <div class="card shadow-sm border-0 h-100 hover-card">
                        <div class="card-body text-center p-4">
                            <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex p-3 mb-3">
                                <i class="bi bi-info-circle-fill text-primary fs-2"></i>
                            </div>
                            <h5 class="fw-bold text-dark">Tentang CDC</h5>
                            <p class="text-muted small mb-0">Kenali lebih dekat kami</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Newsletter -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card bg-gradient-primary text-white shadow-sm border-0">
                    <div class="card-body p-5 text-center">
                        <h3 class="fw-bold mb-3">Berlangganan Newsletter</h3>
                        <p class="lead mb-4">Dapatkan informasi terbaru tentang lowongan kerja dan program magang</p>
                        <form action="{{ route('newsletter.subscribe') }}" method="POST" class="row g-3 justify-content-center">
                            @csrf
                            <div class="col-md-4">
                                <input type="text" name="nama" class="form-control form-control-lg" placeholder="Nama (opsional)">
                            </div>
                            <div class="col-md-4">
                                <input type="email" name="email" class="form-control form-control-lg" placeholder="Email Anda" required>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-light btn-lg w-100">
                                    <i class="bi bi-send me-2"></i>Kirim
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@section('styles')
<style>
    .hover-card {
        transition: all 0.3s ease;
    }
    
    .hover-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 16px rgba(0,150,255,0.2) !important;
    }
</style>
@endsection
@endsection