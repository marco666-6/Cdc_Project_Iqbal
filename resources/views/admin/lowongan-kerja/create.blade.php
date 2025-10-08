{{-- resources/views/admin/lowongan-kerja/create.blade.php --}}
@extends('layouts.admin')

@section('title', 'Tambah Lowongan Kerja')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-2 fw-bold text-gradient">
                        <i class="bi bi-plus-circle me-2"></i>Tambah Lowongan Kerja
                    </h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.lowongan-kerja.index') }}">Lowongan Kerja</a></li>
                            <li class="breadcrumb-item active">Tambah</li>
                        </ol>
                    </nav>
                </div>
                <a href="{{ route('admin.lowongan-kerja.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ route('admin.lowongan-kerja.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Informasi Lowongan</h5>
                    </div>
                    <div class="card-body">
                        <!-- Judul -->
                        <div class="mb-3">
                            <label class="form-label required">Judul Lowongan</label>
                            <input type="text" class="form-control @error('judul') is-invalid @enderror" 
                                   name="judul" value="{{ old('judul') }}" required>
                            @error('judul')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Perusahaan -->
                        <div class="mb-3">
                            <label class="form-label required">Nama Perusahaan</label>
                            <input type="text" class="form-control @error('perusahaan') is-invalid @enderror" 
                                   name="perusahaan" value="{{ old('perusahaan') }}" required>
                            @error('perusahaan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Deskripsi -->
                        <div class="mb-3">
                            <label class="form-label required">Deskripsi Pekerjaan</label>
                            <textarea class="form-control @error('deskripsi') is-invalid @enderror" 
                                      name="deskripsi" rows="10" required>{{ old('deskripsi') }}</textarea>
                            <small class="text-muted">Jelaskan deskripsi pekerjaan, kualifikasi, dan tanggung jawab</small>
                            @error('deskripsi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Lokasi -->
                        <div class="mb-3">
                            <label class="form-label required">Lokasi</label>
                            <input type="text" class="form-control @error('lokasi') is-invalid @enderror" 
                                   name="lokasi" value="{{ old('lokasi') }}" placeholder="Contoh: Jakarta Selatan" required>
                            @error('lokasi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email Aplikasi -->
                        <div class="mb-3">
                            <label class="form-label required">Email untuk Aplikasi</label>
                            <input type="email" class="form-control @error('email_aplikasi') is-invalid @enderror" 
                                   name="email_aplikasi" value="{{ old('email_aplikasi') }}" required>
                            <small class="text-muted">Email tujuan untuk mengirim lamaran</small>
                            @error('email_aplikasi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Gaji Section -->
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Informasi Gaji</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Gaji Minimum (Rp)</label>
                                <input type="number" class="form-control @error('gaji_min') is-invalid @enderror" 
                                       name="gaji_min" value="{{ old('gaji_min') }}" min="0" step="100000">
                                @error('gaji_min')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Gaji Maximum (Rp)</label>
                                <input type="number" class="form-control @error('gaji_max') is-invalid @enderror" 
                                       name="gaji_max" value="{{ old('gaji_max') }}" min="0" step="100000">
                                @error('gaji_max')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="gaji_negotiable" 
                                   id="gaji_negotiable" value="1" {{ old('gaji_negotiable') ? 'checked' : '' }}>
                            <label class="form-check-label" for="gaji_negotiable">
                                Gaji dapat dinegosiasikan
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Status & Image -->
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Publikasi</h5>
                    </div>
                    <div class="card-body">
                        <!-- Status -->
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select @error('status') is-invalid @enderror" name="status">
                                <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>Aktif</option>
                                <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Tidak Aktif</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Tanggal Berakhir -->
                        <div class="mb-3">
                            <label class="form-label required">Tanggal Berakhir</label>
                            <input type="date" class="form-control @error('tanggal_berakhir') is-invalid @enderror" 
                                   name="tanggal_berakhir" value="{{ old('tanggal_berakhir') }}" required>
                            @error('tanggal_berakhir')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Gambar -->
                        <div class="mb-3">
                            <label class="form-label">Gambar</label>
                            <input type="file" class="form-control @error('gambar') is-invalid @enderror" 
                                   name="gambar" accept="image/jpeg,image/png,image/jpg" id="imageInput">
                            <small class="text-muted">Format: JPG, PNG. Max: 2MB</small>
                            @error('gambar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            
                            <!-- Image Preview -->
                            <div id="imagePreview" class="mt-3" style="display: none;">
                                <img src="" alt="Preview" class="img-fluid rounded" style="max-height: 200px;">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kategori -->
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Kategori</h5>
                    </div>
                    <div class="card-body">
                        <!-- Tipe -->
                        <div class="mb-3">
                            <label class="form-label required">Tipe Pekerjaan</label>
                            <select class="form-select @error('tipe') is-invalid @enderror" name="tipe" required>
                                <option value="">Pilih Tipe</option>
                                <option value="full_time" {{ old('tipe') == 'full_time' ? 'selected' : '' }}>Full Time</option>
                                <option value="part_time" {{ old('tipe') == 'part_time' ? 'selected' : '' }}>Part Time</option>
                                <option value="kontrak" {{ old('tipe') == 'kontrak' ? 'selected' : '' }}>Kontrak</option>
                                <option value="magang" {{ old('tipe') == 'magang' ? 'selected' : '' }}>Magang</option>
                            </select>
                            @error('tipe')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Kategori -->
                        <div class="mb-3">
                            <label class="form-label required">Kategori</label>
                            <select class="form-select @error('kategori') is-invalid @enderror" name="kategori" required>
                                <option value="">Pilih Kategori</option>
                                <option value="teknologi" {{ old('kategori') == 'teknologi' ? 'selected' : '' }}>Teknologi</option>
                                <option value="manufaktur" {{ old('kategori') == 'manufaktur' ? 'selected' : '' }}>Manufaktur</option>
                                <option value="perdagangan" {{ old('kategori') == 'perdagangan' ? 'selected' : '' }}>Perdagangan</option>
                                <option value="jasa" {{ old('kategori') == 'jasa' ? 'selected' : '' }}>Jasa</option>
                                <option value="lainnya" {{ old('kategori') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                            @error('kategori')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="card">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary w-100 mb-2">
                            <i class="bi bi-save me-2"></i>Simpan Lowongan
                        </button>
                        <a href="{{ route('admin.lowongan-kerja.index') }}" class="btn btn-outline-secondary w-100">
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
        background: linear-gradient(135deg, #0096FF 0%, #0070CC 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    .required::after {
        content: " *";
        color: #dc3545;
    }

    .card {
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }

    .card-header {
        border-bottom: 1px solid #e9ecef;
        padding: 1rem 1.25rem;
    }
</style>
@endpush

@push('scripts')
<script>
    // Image preview
    $('#imageInput').on('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#imagePreview img').attr('src', e.target.result);
                $('#imagePreview').show();
            }
            reader.readAsDataURL(file);
        } else {
            $('#imagePreview').hide();
        }
    });
</script>
@endpush
@endsection