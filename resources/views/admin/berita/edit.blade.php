{{-- resources/views/admin/berita/edit.blade.php --}}
@extends('layouts.admin')

@section('title', 'Edit Berita')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-2 fw-bold text-gradient">
                        <i class="bi bi-pencil me-2"></i>Edit Berita
                    </h1>
                    <p class="text-muted mb-0">Perbarui informasi berita</p>
                </div>
                <a href="{{ route('admin.berita.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ route('admin.berita.update', $berita->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="row">
            <div class="col-lg-8">
                <!-- Basic Information -->
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">Informasi Berita</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="judul" class="form-label">Judul Berita <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('judul') is-invalid @enderror" 
                                   id="judul" name="judul" value="{{ old('judul', $berita->judul) }}" required>
                            @error('judul')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="slug" class="form-label">Slug (URL)</label>
                            <input type="text" class="form-control @error('slug') is-invalid @enderror" 
                                   id="slug" name="slug" value="{{ old('slug', $berita->slug) }}">
                            @error('slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">URL: {{ url('/berita') }}/<strong id="slugPreview">{{ $berita->slug }}</strong></small>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="kategori" class="form-label">Kategori <span class="text-danger">*</span></label>
                                <select class="form-select @error('kategori') is-invalid @enderror" id="kategori" name="kategori" required>
                                    <option value="">Pilih Kategori</option>
                                    <option value="karir" {{ old('kategori', $berita->kategori) == 'karir' ? 'selected' : '' }}>Karir</option>
                                    <option value="mbkm" {{ old('kategori', $berita->kategori) == 'mbkm' ? 'selected' : '' }}>MBKM</option>
                                    <option value="magang" {{ old('kategori', $berita->kategori) == 'magang' ? 'selected' : '' }}>Magang</option>
                                    <option value="umum" {{ old('kategori', $berita->kategori) == 'umum' ? 'selected' : '' }}>Umum</option>
                                </select>
                                @error('kategori')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="penulis" class="form-label">Penulis <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('penulis') is-invalid @enderror" 
                                       id="penulis" name="penulis" value="{{ old('penulis', $berita->penulis) }}" required>
                                @error('penulis')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="ringkasan" class="form-label">Ringkasan <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('ringkasan') is-invalid @enderror" 
                                      id="ringkasan" name="ringkasan" rows="3" required 
                                      maxlength="500">{{ old('ringkasan', $berita->ringkasan) }}</textarea>
                            @error('ringkasan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Maksimal 500 karakter</small>
                        </div>

                        <div class="mb-3">
                            <label for="konten" class="form-label">Konten Berita <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('konten') is-invalid @enderror" 
                                      id="konten" name="konten" rows="15" required>{{ old('konten', $berita->konten) }}</textarea>
                            @error('konten')
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
                        <h5 class="card-title mb-0">Gambar Berita</h5>
                    </div>
                    <div class="card-body">
                        @if($berita->gambar)
                        <div class="mb-3">
                            <label class="form-label">Gambar Saat Ini</label>
                            <img src="{{ $berita->gambar_url }}" alt="{{ $berita->judul }}" 
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
                            <label for="tanggal_publikasi" class="form-label">Tanggal Publikasi <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('tanggal_publikasi') is-invalid @enderror" 
                                   id="tanggal_publikasi" name="tanggal_publikasi" 
                                   value="{{ old('tanggal_publikasi', $berita->tanggal_publikasi->format('Y-m-d')) }}" required>
                            @error('tanggal_publikasi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input type="hidden" name="status" value="0">
                                <input class="form-check-input" type="checkbox" id="status" name="status" 
                                       value="1" {{ old('status', $berita->status) ? 'checked' : '' }}>
                                <label class="form-check-label" for="status">
                                    Publish Berita
                                </label>
                            </div>
                            <small class="text-muted">Berita akan ditampilkan di halaman publik</small>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input type="hidden" name="is_featured" value="0">
                                <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured"
                                    value="1" {{ old('is_featured', $berita->is_featured) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_featured">
                                    Tandai sebagai Featured
                                </label>
                            </div>
                            <small class="text-muted">Berita akan ditampilkan di bagian unggulan</small>
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
                            <strong>{{ $berita->views_count }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Reading Time:</span>
                            <strong>{{ $berita->reading_time }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Dibuat:</span>
                            <strong>{{ $berita->created_at->format('d M Y') }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Terakhir Update:</span>
                            <strong>{{ $berita->updated_at->format('d M Y') }}</strong>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="card">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary w-100 mb-2">
                            <i class="bi bi-save me-2"></i>Update Berita
                        </button>
                        <a href="{{ route('berita.detail', $berita->slug) }}" class="btn btn-outline-info w-100 mb-2" target="_blank">
                            <i class="bi bi-eye me-2"></i>Lihat Berita
                        </a>
                        <a href="{{ route('admin.berita.index') }}" class="btn btn-outline-secondary w-100">
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

    // Update slug preview
    $('#slug').on('input', function() {
        $('#slugPreview').text($(this).val() || '{{ $berita->slug }}');
    });

    // Character counter for ringkasan
    $('#ringkasan').on('input', function() {
        const maxLength = 500;
        const currentLength = $(this).val().length;
        const remaining = maxLength - currentLength;
        
        if (!$('.char-counter').length) {
            $(this).after('<small class="char-counter text-muted d-block mt-1"></small>');
        }
        
        $('.char-counter').text(`${currentLength}/${maxLength} karakter`);
        
        if (remaining < 50) {
            $('.char-counter').removeClass('text-muted').addClass('text-warning');
        } else {
            $('.char-counter').removeClass('text-warning').addClass('text-muted');
        }
    });

    // Trigger on page load
    $('#ringkasan').trigger('input');
</script>
@endpush
@endsection