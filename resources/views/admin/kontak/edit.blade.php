{{-- resources/views/admin/kontak/edit.blade.php --}}
@extends('layouts.admin')

@section('title', 'Edit Halaman Kontak')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-2 fw-bold text-gradient">
                        <i class="bi bi-envelope me-2"></i>Edit Halaman Kontak
                    </h1>
                    <p class="text-muted mb-0">Kelola informasi kontak organisasi</p>
                </div>
                <a href="{{ route('kontak') }}" class="btn btn-outline-info" target="_blank">
                    <i class="bi bi-eye me-2"></i>Lihat Halaman
                </a>
            </div>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ route('admin.kontak.update') }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="row">
            <div class="col-lg-8">
                <!-- Informasi Kontak Utama -->
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-building text-primary me-2"></i>Informasi Kontak Utama
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="alamat" class="form-label">Alamat Lengkap <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('alamat') is-invalid @enderror" 
                                      id="alamat" name="alamat" rows="3" required>{{ old('alamat', $kontak->alamat) }}</textarea>
                            @error('alamat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="telepon" class="form-label">Telepon <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('telepon') is-invalid @enderror" 
                                       id="telepon" name="telepon" value="{{ old('telepon', $kontak->telepon) }}" 
                                       placeholder="+62 21 12345678" required>
                                @error('telepon')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="whatsapp" class="form-label">WhatsApp</label>
                                <input type="text" class="form-control @error('whatsapp') is-invalid @enderror" 
                                       id="whatsapp" name="whatsapp" value="{{ old('whatsapp', $kontak->whatsapp) }}" 
                                       placeholder="+62 812 3456 7890">
                                @error('whatsapp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email', $kontak->email) }}" 
                                   placeholder="info@example.com" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="jam_operasional" class="form-label">Jam Operasional <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('jam_operasional') is-invalid @enderror" 
                                      id="jam_operasional" name="jam_operasional" rows="3" required>{{ old('jam_operasional', $kontak->jam_operasional) }}</textarea>
                            @error('jam_operasional')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Contoh: Senin - Jumat: 08:00 - 17:00</small>
                        </div>
                    </div>
                </div>

                <!-- Google Maps -->
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-geo-alt text-danger me-2"></i>Google Maps
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="google_maps_url" class="form-label">Google Maps URL</label>
                            <input type="url" class="form-control @error('google_maps_url') is-invalid @enderror" 
                                   id="google_maps_url" name="google_maps_url" 
                                   value="{{ old('google_maps_url', $kontak->google_maps_url) }}" 
                                   placeholder="https://maps.google.com/...">
                            @error('google_maps_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Link ke lokasi di Google Maps</small>
                        </div>

                        <div class="mb-3">
                            <label for="google_maps_embed" class="form-label">Google Maps Embed Code</label>
                            <textarea class="form-control @error('google_maps_embed') is-invalid @enderror" 
                                      id="google_maps_embed" name="google_maps_embed" rows="4">{{ old('google_maps_embed', $kontak->google_maps_embed) }}</textarea>
                            @error('google_maps_embed')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Embed code iframe dari Google Maps</small>
                        </div>
                    </div>
                </div>

                <!-- Social Media -->
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-share text-info me-2"></i>Media Sosial
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="facebook" class="form-label">
                                    <i class="bi bi-facebook text-primary"></i> Facebook
                                </label>
                                <input type="url" class="form-control @error('facebook') is-invalid @enderror" 
                                       id="facebook" name="facebook" value="{{ old('facebook', $kontak->facebook) }}" 
                                       placeholder="https://facebook.com/...">
                                @error('facebook')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="instagram" class="form-label">
                                    <i class="bi bi-instagram text-danger"></i> Instagram
                                </label>
                                <input type="url" class="form-control @error('instagram') is-invalid @enderror" 
                                       id="instagram" name="instagram" value="{{ old('instagram', $kontak->instagram) }}" 
                                       placeholder="https://instagram.com/...">
                                @error('instagram')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="linkedin" class="form-label">
                                    <i class="bi bi-linkedin text-primary"></i> LinkedIn
                                </label>
                                <input type="url" class="form-control @error('linkedin') is-invalid @enderror" 
                                       id="linkedin" name="linkedin" value="{{ old('linkedin', $kontak->linkedin) }}" 
                                       placeholder="https://linkedin.com/company/...">
                                @error('linkedin')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="twitter" class="form-label">
                                    <i class="bi bi-twitter text-info"></i> Twitter
                                </label>
                                <input type="url" class="form-control @error('twitter') is-invalid @enderror" 
                                       id="twitter" name="twitter" value="{{ old('twitter', $kontak->twitter) }}" 
                                       placeholder="https://twitter.com/...">
                                @error('twitter')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Preview -->
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">Preview</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>Alamat:</strong>
                            <p id="preview-alamat" class="text-muted mb-0">{{ $kontak->alamat }}</p>
                        </div>
                        <div class="mb-3">
                            <strong>Telepon:</strong>
                            <p id="preview-telepon" class="text-muted mb-0">{{ $kontak->telepon }}</p>
                        </div>
                        <div class="mb-3">
                            <strong>Email:</strong>
                            <p id="preview-email" class="text-muted mb-0">{{ $kontak->email }}</p>
                        </div>
                        @if($kontak->whatsapp)
                        <div class="mb-3">
                            <strong>WhatsApp:</strong>
                            <p id="preview-whatsapp" class="text-muted mb-0">{{ $kontak->whatsapp }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Info -->
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">Informasi</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            Informasi kontak akan ditampilkan di halaman kontak dan footer website.
                        </div>
                        
                        @if($kontak->updated_at)
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Terakhir Update:</span>
                            <strong>{{ $kontak->updated_at->format('d M Y H:i') }}</strong>
                        </div>
                        @endif
                        
                        @if($kontak->updater)
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Oleh:</span>
                            <strong>{{ $kontak->updater->name }}</strong>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="card">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-save me-2"></i>Simpan Perubahan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('styles')
<style>
    .text-gradient {
        background: linear-gradient(135deg, #0096FF 0%, #0077CC 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
</style>
@endpush

@push('scripts')
<script>
    // Live preview
    $('#alamat').on('input', function() {
        $('#preview-alamat').text($(this).val() || '-');
    });
    
    $('#telepon').on('input', function() {
        $('#preview-telepon').text($(this).val() || '-');
    });
    
    $('#email').on('input', function() {
        $('#preview-email').text($(this).val() || '-');
    });
    
    $('#whatsapp').on('input', function() {
        $('#preview-whatsapp').text($(this).val() || '-');
    });
</script>
@endpush
@endsection