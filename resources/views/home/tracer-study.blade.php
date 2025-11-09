@extends('layouts.app')

@section('title', 'Tracer Study - CDC Polibatam')

@section('content')
<!-- Hero Section -->
<section class="bg-gradient-primary text-white py-5">
    <div class="container py-5">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <div class="mb-4">
                    <i class="bi bi-graph-up-arrow display-1"></i>
                </div>
                <h1 class="display-4 fw-bold mb-3">Tracer Study</h1>
                <p class="lead mb-4">Sistem pelacakan alumni untuk mengetahui perkembangan karir dan memberikan masukan bagi peningkatan kualitas pendidikan</p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="{{ $links[0]['url'] }}" target="_blank" class="btn btn-light btn-lg">
                        <i class="bi bi-box-arrow-up-right me-2"></i>Akses Tracer Study
                    </a>
                    <a href="{{ route('kontak') }}" class="btn btn-outline-light btn-lg">
                        <i class="bi bi-envelope me-2"></i>Hubungi Kami
                    </a>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card border-0 shadow-lg bg-white">
                    <div class="card-body p-5 text-center">
                        <img src="{{ asset('images/no-image.png') }}" alt="Tracer Study Illustration" class="img-fluid rounded mb-4" style="max-height: 300px;">
                        <h4 class="fw-bold text-primary mb-3">Mengapa Tracer Study Penting?</h4>
                        <p class="text-muted mb-0">Membantu institusi memahami kualitas lulusan dan meningkatkan kurikulum berdasarkan kebutuhan industri</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- About Tracer Study -->
<section class="py-5">
    <div class="container">
        <div class="row mb-5">
            <div class="col-lg-10 mx-auto">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                <i class="bi bi-info-circle text-primary fs-1"></i>
                            </div>
                            <h2 class="fw-bold">Tentang Tracer Study</h2>
                        </div>
                        <p class="text-muted text-center mb-4">
                            Tracer Study adalah sistem pelacakan jejak lulusan yang bertujuan untuk mendapatkan informasi mengenai 
                            perkembangan karir alumni, masa tunggu kerja, kesesuaian bidang kerja, dan kepuasan pengguna lulusan. 
                            Data ini sangat penting untuk evaluasi dan peningkatan kualitas pendidikan di Politeknik Negeri Batam.
                        </p>
                        
                        <div class="row mt-5">
                            <div class="col-md-6 mb-4">
                                <div class="d-flex align-items-start">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                            <i class="bi bi-check-circle text-primary fs-4"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold mb-2">Tujuan Utama</h5>
                                        <p class="text-muted mb-0">Mengetahui outcome pendidikan dalam bentuk transisi dari dunia pendidikan ke dunia kerja</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <div class="d-flex align-items-start">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                            <i class="bi bi-graph-up text-primary fs-4"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold mb-2">Manfaat Data</h5>
                                        <p class="text-muted mb-0">Sebagai dasar untuk perbaikan kurikulum dan sistem pembelajaran</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <div class="d-flex align-items-start">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                            <i class="bi bi-people text-primary fs-4"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold mb-2">Stakeholder</h5>
                                        <p class="text-muted mb-0">Melibatkan alumni, institusi, dan pengguna lulusan (perusahaan)</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <div class="d-flex align-items-start">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                            <i class="bi bi-shield-check text-primary fs-4"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold mb-2">Akreditasi</h5>
                                        <p class="text-muted mb-0">Data tracer study menjadi syarat penting dalam akreditasi institusi</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Access Links -->
        <div class="row">
            <div class="col-lg-10 mx-auto">
                <div class="text-center mb-4">
                    <h2 class="fw-bold">Akses Sistem Tracer Study</h2>
                    <p class="text-muted">Pilih portal yang sesuai untuk mengakses sistem tracer study</p>
                </div>
                
                <div class="row">
                    @foreach($links as $index => $link)
                    <div class="col-md-6 mb-4">
                        <div class="card h-100 border-0 shadow-sm {{ $link['featured'] ? 'border-primary border-2' : '' }}">
                            @if($link['featured'])
                            <div class="card-header bg-primary text-white text-center py-2 border-0">
                                <small class="fw-bold"><i class="bi bi-star-fill me-1"></i>PORTAL UTAMA</small>
                            </div>
                            @endif
                            <div class="card-body p-4">
                                <div class="text-center mb-4">
                                    <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                        <i class="{{ $link['icon'] }} text-primary" style="font-size: 2.5rem;"></i>
                                    </div>
                                    <h4 class="fw-bold mb-2">{{ $link['title'] }}</h4>
                                    <p class="text-muted mb-0">{{ $link['description'] }}</p>
                                </div>
                                <a href="{{ $link['url'] }}" target="_blank" class="btn {{ $link['featured'] ? 'btn-primary' : 'btn-outline-primary' }} w-100 btn-lg">
                                    <i class="bi bi-box-arrow-up-right me-2"></i>Akses Sekarang
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- How It Works -->
        <div class="row mt-5">
            <div class="col-lg-10 mx-auto">
                <div class="text-center mb-4">
                    <h2 class="fw-bold">Cara Mengisi Tracer Study</h2>
                    <p class="text-muted">Ikuti langkah-langkah berikut untuk mengisi kuisioner tracer study</p>
                </div>
                
                <div class="row">
                    <div class="col-md-3 mb-4">
                        <div class="text-center">
                            <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                <span class="fs-4 fw-bold">1</span>
                            </div>
                            <h5 class="fw-bold mb-2">Akses Portal</h5>
                            <p class="text-muted small mb-0">Klik tombol "Akses Sekarang" pada portal yang tersedia</p>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="text-center">
                            <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                <span class="fs-4 fw-bold">2</span>
                            </div>
                            <h5 class="fw-bold mb-2">Login</h5>
                            <p class="text-muted small mb-0">Masuk menggunakan akun mahasiswa atau email alumni</p>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="text-center">
                            <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                <span class="fs-4 fw-bold">3</span>
                            </div>
                            <h5 class="fw-bold mb-2">Isi Kuisioner</h5>
                            <p class="text-muted small mb-0">Lengkapi seluruh pertanyaan dengan jujur dan akurat</p>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="text-center">
                            <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                <span class="fs-4 fw-bold">4</span>
                            </div>
                            <h5 class="fw-bold mb-2">Submit</h5>
                            <p class="text-muted small mb-0">Kirim data dan terima konfirmasi pengisian</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CTA Section -->
        <div class="row mt-5">
            <div class="col-lg-10 mx-auto">
                <div class="card border-0 bg-primary text-white">
                    <div class="card-body p-5 text-center">
                        <i class="bi bi-megaphone fs-1 mb-3"></i>
                        <h3 class="fw-bold mb-3">Partisipasi Anda Sangat Berarti!</h3>
                        <p class="mb-4">
                            Dengan mengisi tracer study, Anda membantu Polibatam untuk terus meningkatkan kualitas pendidikan 
                            dan mempersiapkan mahasiswa yang lebih siap menghadapi dunia kerja.
                        </p>
                        <div class="d-flex justify-content-center gap-3 flex-wrap">
                            <a href="{{ $links[0]['url'] }}" target="_blank" class="btn btn-light btn-lg">
                                <i class="bi bi-box-arrow-up-right me-2"></i>Isi Tracer Study
                            </a>
                            <a href="{{ route('kontak') }}" class="btn btn-outline-light btn-lg">
                                <i class="bi bi-question-circle me-2"></i>Butuh Bantuan?
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Statistics -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row text-center">
            <div class="col-md-4 mb-4 mb-md-0">
                <div class="bg-white rounded p-4 shadow-sm h-100">
                    <i class="bi bi-people-fill text-primary fs-1 mb-3"></i>
                    <h3 class="fw-bold text-primary mb-2">Alumni</h3>
                    <p class="text-muted mb-0">Ribuan lulusan telah berpartisipasi dalam tracer study</p>
                </div>
            </div>
            <div class="col-md-4 mb-4 mb-md-0">
                <div class="bg-white rounded p-4 shadow-sm h-100">
                    <i class="bi bi-building text-primary fs-1 mb-3"></i>
                    <h3 class="fw-bold text-primary mb-2">Perusahaan</h3>
                    <p class="text-muted mb-0">Feedback dari ratusan perusahaan pengguna lulusan</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="bg-white rounded p-4 shadow-sm h-100">
                    <i class="bi bi-graph-up text-primary fs-1 mb-3"></i>
                    <h3 class="fw-bold text-primary mb-2">Peningkatan</h3>
                    <p class="text-muted mb-0">Data untuk meningkatkan kualitas pendidikan secara berkelanjutan</p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection