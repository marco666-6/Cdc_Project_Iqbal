{{-- resources/views/admin/program-magang/edit.blade.php --}}
@extends('layouts.admin')

@section('title', 'Edit Program Magang')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-2 fw-bold text-gradient">
                        <i class="bi bi-pencil me-2"></i>Edit Program Magang
                    </h1>
                    <p class="text-muted mb-0">Perbarui informasi program magang</p>
                </div>
                <a href="{{ route('admin.program-magang.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ route('admin.program-magang.update', $program->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="row">
            <div class="col-lg-8">
                <!-- Basic Information -->
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">Informasi Dasar</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="judul" class="form-label">Judul Program <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('judul') is-invalid @enderror" 
                                   id="judul" name="judul" value="{{ old('judul', $program->judul) }}" required>
                            @error('judul')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="perusahaan" class="form-label">Nama Perusahaan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('perusahaan') is-invalid @enderror" 
                                   id="perusahaan" name="perusahaan" value="{{ old('perusahaan', $program->perusahaan) }}" required>
                            @error('perusahaan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="tipe" class="form-label">Tipe Program <span class="text-danger">*</span></label>
                                <select class="form-select @error('tipe') is-invalid @enderror" id="tipe" name="tipe" required>
                                    <option value="">Pilih Tipe</option>
                                    <option value="mbkm" {{ old('tipe', $program->tipe) == 'mbkm' ? 'selected' : '' }}>MBKM</option>
                                    <option value="magang_reguler" {{ old('tipe', $program->tipe) == 'magang_reguler' ? 'selected' : '' }}>Magang Reguler</option>
                                    <option value="magang_independen" {{ old('tipe', $program->tipe) == 'magang_independen' ? 'selected' : '' }}>Magang Independen</option>
                                </select>
                                @error('tipe')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="lokasi" class="form-label">Lokasi <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('lokasi') is-invalid @enderror" 
                                       id="lokasi" name="lokasi" value="{{ old('lokasi', $program->lokasi) }}" required>
                                @error('lokasi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="durasi" class="form-label">Durasi (Bulan) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('durasi') is-invalid @enderror" 
                                       id="durasi" name="durasi" value="{{ old('durasi', $program->durasi) }}" min="1" required>
                                @error('durasi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="kuota" class="form-label">Kuota Peserta</label>
                                <input type="number" class="form-control @error('kuota') is-invalid @enderror" 
                                       id="kuota" name="kuota" value="{{ old('kuota', $program->kuota) }}" min="1" 
                                       placeholder="Kosongkan jika tidak terbatas">
                                @error('kuota')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Kosongkan jika kuota tidak terbatas</small>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="tanggal_mulai" class="form-label">Tanggal Mulai</label>
                                <input type="date" class="form-control @error('tanggal_mulai') is-invalid @enderror" 
                                       id="tanggal_mulai" name="tanggal_mulai" 
                                       value="{{ old('tanggal_mulai', $program->tanggal_mulai ? $program->tanggal_mulai->format('Y-m-d') : '') }}">
                                @error('tanggal_mulai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi Program <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('deskripsi') is-invalid @enderror" 
                                      id="deskripsi" name="deskripsi" rows="5" required>{{ old('deskripsi', $program->deskripsi) }}</textarea>
                            @error('deskripsi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="persyaratan" class="form-label">Persyaratan <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('persyaratan') is-invalid @enderror" 
                                      id="persyaratan" name="persyaratan" rows="5" required>{{ old('persyaratan', $program->persyaratan) }}</textarea>
                            @error('persyaratan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Pisahkan setiap persyaratan dengan enter</small>
                        </div>

                        <div class="mb-3">
                            <label for="benefit" class="form-label">Benefit <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('benefit') is-invalid @enderror" 
                                      id="benefit" name="benefit" rows="5" required>{{ old('benefit', $program->benefit) }}</textarea>
                            @error('benefit')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Pisahkan setiap benefit dengan enter</small>
                        </div>

                        <div class="mb-3">
                            <label for="link_pendaftaran" class="form-label">Link Pendaftaran <span class="text-danger">*</span></label>
                            <input type="url" class="form-control @error('link_pendaftaran') is-invalid @enderror" 
                                   id="link_pendaftaran" name="link_pendaftaran" 
                                   value="{{ old('link_pendaftaran', $program->link_pendaftaran) }}" required>
                            @error('link_pendaftaran')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Gambar -->
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">Gambar Program</h5>
                    </div>
                    <div class="card-body">
                        @if($program->gambar)
                        <div class="mb-3">
                            <label class="form-label">Gambar Saat Ini</label>
                            <img src="{{ $program->gambar_url }}" alt="{{ $program->judul }}" 
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

                <!-- Settings -->
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">Pengaturan</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="tanggal_berakhir" class="form-label">Tanggal Berakhir <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('tanggal_berakhir') is-invalid @enderror" 
                                   id="tanggal_berakhir" name="tanggal_berakhir" 
                                   value="{{ old('tanggal_berakhir', $program->tanggal_berakhir->format('Y-m-d')) }}" required>
                            @error('tanggal_berakhir')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input type="hidden" name="status" value="0">
                                <input class="form-check-input" type="checkbox" id="status" name="status" 
                                       value="1" {{ old('status', $program->status) ? 'checked' : '' }}>
                                <label class="form-check-label" for="status">
                                    Status Aktif
                                </label>
                            </div>
                            <small class="text-muted">Program akan ditampilkan di halaman publik</small>
                        </div>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">Statistik</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Total Views:</span>
                            <strong>{{ $program->views_count }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Dibuat:</span>
                            <strong>{{ $program->created_at->format('d M Y') }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Terakhir Update:</span>
                            <strong>{{ $program->updated_at->format('d M Y') }}</strong>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="card">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary w-100 mb-2">
                            <i class="bi bi-save me-2"></i>Update Program
                        </button>
                        <a href="{{ route('admin.program-magang.index') }}" class="btn btn-outline-secondary w-100">
                            <i class="bi bi-x-circle me-2"></i>Batal
                        </a>
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