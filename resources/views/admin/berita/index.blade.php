{{-- resources/views/admin/berita/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Manajemen Berita')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h1 class="h3 mb-2 fw-bold text-gradient">
                        <i class="bi bi-newspaper me-2"></i>Manajemen Berita & Newsletter
                    </h1>
                    <p class="text-muted mb-0">Kelola semua berita dan artikel</p>
                </div>
                <a href="{{ route('admin.berita.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Tambah Berita
                </a>
            </div>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.berita.index') }}" id="filterForm">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Cari</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" class="form-control" name="search" 
                                   placeholder="Cari judul, konten..." 
                                   value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status">
                            <option value="">Semua</option>
                            <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Kategori</label>
                        <select class="form-select" name="kategori">
                            <option value="all">Semua</option>
                            <option value="karir" {{ request('kategori') == 'karir' ? 'selected' : '' }}>Karir</option>
                            <option value="mbkm" {{ request('kategori') == 'mbkm' ? 'selected' : '' }}>MBKM</option>
                            <option value="magang" {{ request('kategori') == 'magang' ? 'selected' : '' }}>Magang</option>
                            <option value="umum" {{ request('kategori') == 'umum' ? 'selected' : '' }}>Umum</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Featured</label>
                        <select class="form-select" name="featured">
                            <option value="">Semua</option>
                            <option value="1" {{ request('featured') == '1' ? 'selected' : '' }}>Ya</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-fill">
                                <i class="bi bi-funnel"></i>
                            </button>
                            <a href="{{ route('admin.berita.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Bulk Actions -->
    <div class="card mb-4" id="bulkActions" style="display: none;">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <strong><span id="selectedCount">0</span></strong> item dipilih
                </div>
                <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success" onclick="bulkAction('publish')">
                        <i class="bi bi-check-circle me-1"></i>Publish
                    </button>
                    <button type="button" class="btn btn-sm btn-warning" onclick="bulkAction('draft')">
                        <i class="bi bi-file-earmark me-1"></i>Draft
                    </button>
                    <button type="button" class="btn btn-sm btn-info" onclick="bulkAction('feature')">
                        <i class="bi bi-star me-1"></i>Featured
                    </button>
                    <button type="button" class="btn btn-sm btn-danger" onclick="bulkAction('delete')">
                        <i class="bi bi-trash me-1"></i>Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Berita Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th width="50">
                                <input type="checkbox" class="form-check-input" id="selectAll">
                            </th>
                            <th>Berita</th>
                            <th>Kategori</th>
                            <th>Penulis</th>
                            <th>Tanggal</th>
                            <th>Views</th>
                            <th>Featured</th>
                            <th>Status</th>
                            <th width="150">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($berita as $item)
                        <tr>
                            <td>
                                <input type="checkbox" class="form-check-input row-checkbox" value="{{ $item->id }}">
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <img src="{{ $item->gambar_url }}" alt="{{ $item->judul }}" 
                                         class="rounded" style="width: 60px; height: 60px; object-fit: cover;">
                                    <div>
                                        <h6 class="mb-1">{{ Str::limit($item->judul, 50) }}</h6>
                                        <small class="text-muted">{{ $item->excerpt }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-secondary-subtle text-secondary">
                                    {{ $item->kategori_formatted }}
                                </span>
                            </td>
                            <td>{{ $item->penulis }}</td>
                            <td>{{ $item->tanggal_publikasi->format('d M Y') }}</td>
                            <td>
                                <span class="badge bg-primary-subtle text-primary">
                                    <i class="bi bi-eye"></i> {{ $item->views_count }}
                                </span>
                            </td>
                            <td>
                                @if($item->is_featured)
                                    <i class="bi bi-star-fill text-warning" title="Featured"></i>
                                @else
                                    <i class="bi bi-star text-muted" title="Not Featured"></i>
                                @endif
                            </td>
                            <td>
                                @if($item->status)
                                    <span class="badge bg-success">Published</span>
                                @else
                                    <span class="badge bg-secondary">Draft</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('berita.detail', $item->slug) }}" 
                                       class="btn btn-outline-info" title="Lihat" target="_blank">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.berita.edit', $item->id) }}" 
                                       class="btn btn-outline-primary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-danger" 
                                            onclick="deleteItem({{ $item->id }}, '{{ $item->judul }}')" 
                                            title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                                    <p class="mb-0">Tidak ada data berita</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($berita->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    Menampilkan {{ $berita->firstItem() }} - {{ $berita->lastItem() }} dari {{ $berita->total() }} data
                </div>
                {{ $berita->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Form -->
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<!-- Bulk Action Forms -->
<form id="bulkStatusForm" method="POST" action="{{ route('admin.berita.bulk-status') }}" style="display: none;">
    @csrf
    <input type="hidden" name="ids" id="bulkStatusIds">
    <input type="hidden" name="status" id="bulkStatus">
</form>

<form id="bulkDeleteForm" method="POST" action="{{ route('admin.berita.bulk-delete') }}" style="display: none;">
    @csrf
    <input type="hidden" name="ids" id="bulkDeleteIds">
</form>

<form id="bulkFeaturedForm" method="POST" action="{{ route('admin.berita.bulk-featured') }}" style="display: none;">
    @csrf
    <input type="hidden" name="ids" id="bulkFeaturedIds">
    <input type="hidden" name="is_featured" id="bulkFeatured">
</form>

@push('styles')
<style>
    .text-gradient {
        background: linear-gradient(135deg, #0096FF 0%, #0077CC 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .table th {
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        color: var(--gray-600);
    }

    .table tbody tr {
        transition: all 0.3s ease;
    }

    .table tbody tr:hover {
        background: var(--gray-50);
        transform: scale(1.01);
        box-shadow: var(--shadow-sm);
    }
</style>
@endpush

@push('scripts')
<script>
    // Select all checkbox
    $('#selectAll').on('change', function() {
        $('.row-checkbox').prop('checked', $(this).prop('checked'));
        updateBulkActions();
    });

    // Individual checkbox
    $('.row-checkbox').on('change', function() {
        updateBulkActions();
        
        const totalRows = $('.row-checkbox').length;
        const checkedRows = $('.row-checkbox:checked').length;
        $('#selectAll').prop('checked', totalRows === checkedRows);
    });

    // Update bulk actions visibility
    function updateBulkActions() {
        const checkedCount = $('.row-checkbox:checked').length;
        $('#selectedCount').text(checkedCount);
        
        if (checkedCount > 0) {
            $('#bulkActions').slideDown();
        } else {
            $('#bulkActions').slideUp();
        }
    }

    // Bulk action handler
    function bulkAction(action) {
        const selectedIds = $('.row-checkbox:checked').map(function() {
            return $(this).val();
        }).get();

        if (selectedIds.length === 0) {
            alert('Pilih minimal satu item');
            return;
        }

        if (action === 'delete') {
            if (!confirm(`Apakah Anda yakin ingin menghapus ${selectedIds.length} berita?`)) {
                return;
            }
            $('#bulkDeleteIds').val(JSON.stringify(selectedIds));
            $('#bulkDeleteForm').submit();
        } else if (action === 'publish' || action === 'draft') {
            const status = action === 'publish' ? 1 : 0;
            const statusText = action === 'publish' ? 'publish' : 'jadikan draft';
            
            if (!confirm(`Apakah Anda yakin ingin ${statusText} ${selectedIds.length} berita?`)) {
                return;
            }
            
            $('#bulkStatusIds').val(JSON.stringify(selectedIds));
            $('#bulkStatus').val(status);
            $('#bulkStatusForm').submit();
        } else if (action === 'feature') {
            if (!confirm(`Apakah Anda yakin ingin menandai ${selectedIds.length} berita sebagai featured?`)) {
                return;
            }
            
            $('#bulkFeaturedIds').val(JSON.stringify(selectedIds));
            $('#bulkFeatured').val(1);
            $('#bulkFeaturedForm').submit();
        }
    }

    // Delete single item
    function deleteItem(id, title) {
        if (confirm(`Apakah Anda yakin ingin menghapus berita "${title}"?`)) {
            const form = $('#deleteForm');
            form.attr('action', `/admin/berita/${id}`);
            form.submit();
        }
    }
</script>
@endpush
@endsection