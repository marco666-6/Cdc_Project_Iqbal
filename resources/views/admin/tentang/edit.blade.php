{{-- resources/views/admin/tentang/edit.blade.php --}}
@extends('layouts.admin')

@section('title', 'Edit Halaman Tentang')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-2 fw-bold text-gradient">
                        <i class="bi bi-info-circle me-2"></i>Edit Halaman Tentang
                    </h1>
                    <p class="text-muted mb-0">Kelola informasi tentang organisasi</p>
                </div>
                <a href="{{ route('tentang') }}" class="btn btn-outline-info" target="_blank">
                    <i class="bi bi-eye me-2"></i>Lihat Halaman
                </a>
            </div>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ route('admin.tentang.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="row">
            <div class="col-lg-8">
                <!-- Sejarah -->
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-clock-history text-primary me-2"></i>Sejarah
                        </h5>
                    </div>
                    <div class="card-body">
                        <textarea class="form-control @error('sejarah') is-invalid @enderror" 
                                  id="sejarah" name="sejarah" rows="8" required>{{ old('sejarah', $tentang->sejarah) }}</textarea>
                        @error('sejarah')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Visi -->
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-eye text-info me-2"></i>Visi
                        </h5>
                    </div>
                    <div class="card-body">
                        <textarea class="form-control @error('visi') is-invalid @enderror" 
                                  id="visi" name="visi" rows="5" required>{{ old('visi', $tentang->visi) }}</textarea>
                        @error('visi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Misi -->
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-list-check text-success me-2"></i>Misi
                        </h5>
                    </div>
                    <div class="card-body">
                        <textarea class="form-control @error('misi') is-invalid @enderror" 
                                  id="misi" name="misi" rows="8" required>{{ old('misi', $tentang->misi) }}</textarea>
                        @error('misi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Pisahkan setiap poin misi dengan enter</small>
                    </div>
                </div>

                <!-- Tujuan -->
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-bullseye text-warning me-2"></i>Tujuan
                        </h5>
                    </div>
                    <div class="card-body">
                        <textarea class="form-control @error('tujuan') is-invalid @enderror" 
                                  id="tujuan" name="tujuan" rows="8" required>{{ old('tujuan', $tentang->tujuan) }}</textarea>
                        @error('tujuan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Pisahkan setiap poin tujuan dengan enter</small>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Gambar -->
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">Gambar</h5>
                    </div>
                    <div class="card-body">
                        @if($tentang->gambar)
                        <div class="mb-3">
                            <label class="form-label">Gambar Saat Ini</label>
                            <img src="{{ $tentang->gambar_url }}" alt="Tentang" 
                                 class="img-fluid rounded">
                        </div>
                        @endif
                        
                        <div class="mb-3">
                            <label for="gambar" class="form-label">Upload Gambar Baru</label>
                            <input type="file" class="form-control @error('gambar') is-invalid @enderror" 
                                   id="gambar" name="gambar" accept="image/*" onchange="previewImage(event)">
                            @error('gambar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Format: JPG, PNG. Max: 2MB</small>
                        </div>
                        
                        <div id="imagePreview" class="mt-3" style="display: none;">
                            <label class="form-label">Preview Gambar Baru</label>
                            <img id="preview" src="" alt="Preview" class="img-fluid rounded">
                        </div>
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
                            Halaman ini berisi informasi tentang organisasi yang akan ditampilkan ke publik.
                        </div>
                        
                        @if($tentang->updated_at)
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Terakhir Update:</span>
                            <strong>{{ $tentang->updated_at->format('d M Y H:i') }}</strong>
                        </div>
                        @endif
                        
                        @if($tentang->updater)
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Oleh:</span>
                            <strong>{{ $tentang->updater->name }}</strong>
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
    // Image preview
    function previewImage(event) {
        const input = event.target;
        const preview = document.getElementById('preview');
        const previewContainer = document.getElementById('imagePreview');
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                preview.src = e.target.result;
                previewContainer.style.display = 'block';
            };
            
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush
@endsection