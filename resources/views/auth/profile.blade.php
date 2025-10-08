{{-- resources/views/auth/profile.blade.php --}}
@extends('layouts.admin')

@section('title', 'Edit Profile')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-2 fw-bold text-gradient">
                        <i class="bi bi-person-circle me-2"></i>Edit Profile
                    </h1>
                    <p class="text-muted mb-0">Kelola informasi akun Anda</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <!-- Profile Information -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-person text-primary me-2"></i>Informasi Profile
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', auth()->user()->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email', auth()->user()->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="border-top pt-3 mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-2"></i>Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Account Information -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-info-circle text-info me-2"></i>Informasi Akun
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Role</label>
                            <div>
                                <span class="badge bg-primary fs-6">
                                    <i class="bi bi-shield-check me-1"></i>
                                    {{ strtoupper(auth()->user()->role) }}
                                </span>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Status</label>
                            <div>
                                <span class="badge bg-success fs-6">
                                    <i class="bi bi-check-circle me-1"></i>
                                    ACTIVE
                                </span>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Member Since</label>
                            <div class="fw-bold">
                                <i class="bi bi-calendar-event me-2 text-muted"></i>
                                {{ auth()->user()->created_at->format('d M Y') }}
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Last Login</label>
                            <div class="fw-bold">
                                <i class="bi bi-clock me-2 text-muted"></i>
                                {{ auth()->user()->last_login_at ? auth()->user()->last_login_at->format('d M Y H:i') : 'Never' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Change Password Link -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="mb-1">
                                <i class="bi bi-key text-warning me-2"></i>Password
                            </h6>
                            <p class="text-muted mb-0 small">Ubah password akun Anda</p>
                        </div>
                        <a href="{{ route('profile.password') }}" class="btn btn-outline-primary">
                            <i class="bi bi-lock me-2"></i>Change Password
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .text-gradient {
        background: linear-gradient(135deg, #0096FF 0%, #0077CC 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .card {
        border: none;
        transition: all 0.3s ease;
    }

    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0, 150, 255, 0.15) !important;
    }
</style>
@endpush
@endsection