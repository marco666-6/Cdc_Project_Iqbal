@extends('layouts.app')

@section('title', 'Tentang Kami - CDC Polibatam')

@section('content')
<!-- Page Header -->
<section class="bg-gradient-primary text-white py-5">
    <div class="container">
        <h1 class="display-5 fw-bold mb-2">Tentang CDC Polibatam</h1>
        <p class="lead">Career Development Center Politeknik Negeri Batam</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        @if($tentang->gambar)
        <div class="row mb-5">
            <div class="col-12">
                <img src="{{ $tentang->gambar_url }}" alt="CDC Polibatam" class="img-fluid rounded shadow-sm" style="width: 100%; max-height: 400px; object-fit: cover;">
            </div>
        </div>
        @endif

        <!-- Sejarah -->
        @if($tentang->sejarah)
        <div class="row mb-5">
            <div class="col-lg-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <h2 class="fw-bold mb-4 text-primary">
                            <i class="bi bi-book me-2"></i>Sejarah
                        </h2>
                        <div class="content-text">
                            {!! nl2br(e($tentang->sejarah)) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Visi & Misi -->
        <div class="row mb-5">
            @if($tentang->visi)
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body p-4">
                        <h2 class="fw-bold mb-4 text-primary">
                            <i class="bi bi-eye me-2"></i>Visi
                        </h2>
                        <div class="content-text">
                            {!! nl2br(e($tentang->visi)) !!}
                        </div>
                    </div>
                </div>
            </div>
            @endif

            @if($tentang->misi)
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body p-4">
                        <h2 class="fw-bold mb-4 text-primary">
                            <i class="bi bi-target me-2"></i>Misi
                        </h2>
                        <div class="content-text">
                            {!! nl2br(e($tentang->misi)) !!}
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Tujuan -->
        @if($tentang->tujuan)
        <div class="row mb-5">
            <div class="col-lg-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <h2 class="fw-bold mb-4 text-primary">
                            <i class="bi bi-bullseye me-2"></i>Tujuan
                        </h2>
                        <div class="content-text">
                            {!! nl2br(e($tentang->tujuan)) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Services/Features -->
        <div class="row mb-5">
            <div class="col-12">
                <h2 class="fw-bold mb-4 text-center">Layanan Kami</h2>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm border-0 h-100 text-center">
                    <div class="card-body p-4">
                        <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex p-4 mb-3">
                            <i class="bi bi-briefcase-fill text-primary fs-1"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Lowongan Kerja</h4>
                        <p class="text-muted">Informasi lowongan kerja terkini dari berbagai perusahaan mitra</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm border-0 h-100 text-center">
                    <div class="card-body p-4">
                        <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex p-4 mb-3">
                            <i class="bi bi-people-fill text-primary fs-1"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Program Magang</h4>
                        <p class="text-muted">Program magang dan MBKM untuk meningkatkan kompetensi mahasiswa</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm border-0 h-100 text-center">
                    <div class="card-body p-4">
                        <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex p-4 mb-3">
                            <i class="bi bi-graph-up text-primary fs-1"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Career Development</h4>
                        <p class="text-muted">Bimbingan dan pelatihan untuk pengembangan karir mahasiswa</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- CTA -->
        <div class="row">
            <div class="col-12">
                <div class="card bg-gradient-primary text-white shadow-sm border-0">
                    <div class="card-body p-5 text-center">
                        <h3 class="fw-bold mb-3">Siap Memulai Karir Anda?</h3>
                        <p class="lead mb-4">Jelajahi peluang karir dan program magang yang tersedia</p>
                        <div class="d-flex gap-3 justify-content-center flex-wrap">
                            <a href="{{ route('lowongan-kerja') }}" class="btn btn-light btn-lg">
                                <i class="bi bi-search me-2"></i>Cari Lowongan
                            </a>
                            <a href="{{ route('kontak') }}" class="btn btn-outline-light btn-lg">
                                <i class="bi bi-envelope me-2"></i>Hubungi Kami
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@section('styles')
<style>
    .content-text {
        font-size: 1.05rem;
        line-height: 1.8;
        color: #333;
    }
</style>
@endsection
@endsection