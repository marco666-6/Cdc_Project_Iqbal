{{-- resources/views/admin/lowongan-kerja/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Manajemen Lowongan Kerja')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h1 class="h3 mb-2 fw-bold text-gradient">
                        <i class="bi bi-briefcase me-2"></i>Manajemen Lowongan Kerja
                    </h1>
                    <p class="text-muted mb-0">Kelola semua lowongan pekerjaan</p>
                </div>
                <a href="{{ route('admin.lowongan-kerja.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Tambah Lowongan
                </a>
            </div>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.lowongan-kerja.index') }}" id="filterForm">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Cari</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" class="form-control" name="search" 
                                   placeholder="Cari judul, perusahaan..." 
                                   value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status">
                            <option value="">Semua</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Tipe</label>
                        <select class="form-select" name="tipe">
                            <option value="all">Semua</option>
                            <option value="full_time" {{ request('tipe') == 'full_time' ? 'selected' : '' }}>Full Time</option>
                            <option value="part_time" {{ request('tipe') == 'part_time' ? 'selected' : '' }}>Part Time</option>
                            <option value="kontrak" {{ request('tipe') == 'kontrak' ? 'selected' : '' }}>Kontrak</option>
                            <option value="magang" {{ request('tipe') == 'magang' ? 'selected' : '' }}>Magang</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Kategori</label>
                        <select class="form-select" name="kategori">
                            <option value="all">Semua</option>
                            <option value="teknologi" {{ request('kategori') == 'teknologi' ? 'selected' : '' }}>Teknologi</option>
                            <option value="manufaktur" {{ request('kategori') == 'manufaktur' ? 'selected' : '' }}>Manufaktur</option>
                            <option value="perdagangan" {{ request('kategori') == 'perdagangan' ? 'selected' : '' }}>Perdagangan</option>
                            <option value="jasa" {{ request('kategori') == 'jasa' ? 'selected' : '' }}>Jasa</option>
                            <option value="lainnya" {{ request('kategori') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-fill">
                                <i class="bi bi-funnel"></i>
                            </button>
                            <a href="{{ route('admin.lowongan-kerja.index') }}" class="btn btn-outline-secondary">
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
                    <button type="button" class="btn btn-sm btn-success" onclick="bulkAction('activate')">
                        <i class="bi bi-check-circle me-1"></i>Aktifkan
                    </button>
                    <button type="button" class="btn btn-sm btn-warning" onclick="bulkAction('deactivate')">
                        <i class="bi bi-x-circle me-1"></i>Nonaktifkan
                    </button>
                    <button type="button" class="btn btn-sm btn-danger" onclick="bulkAction('delete')">
                        <i class="bi bi-trash me-1"></i>Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Lowongan Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th width="50">
                                <input type="checkbox" class="form-check-input" id="selectAll">
                            </th>
                            <th>Lowongan</th>
                            <th>Perusahaan</th>
                            <th>Lokasi</th>
                            <th>Tipe</th>
                            <th>Kategori</th>
                            <th>Berakhir</th>
                            <th>Views</th>
                            <th>Status</th>
                            <th width="150">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lowongan as $item)
                        <tr>
                            <td>
                                <input type="checkbox" class="form-check-input row-checkbox" value="{{ $item->id }}">
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <img src="{{ $item->gambar_url }}" alt="{{ $item->judul }}" 
                                         class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                                    <div>
                                        <h6 class="mb-0">{{ $item->judul }}</h6>
                                        <small class="text-muted">{{ $item->created_at->format('d M Y') }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $item->perusahaan }}</td>
                            <td>
                                <i class="bi bi-geo-alt text-muted"></i>
                                {{ $item->lokasi }}
                            </td>
                            <td>
                                <span class="badge bg-info-subtle text-info">{{ $item->tipe_formatted }}</span>
                            </td>
                            <td>
                                <span class="badge bg-secondary-subtle text-secondary">{{ $item->kategori_formatted }}</span>
                            </td>
                            <td>
                                @if($item->is_expired)
                                    <span class="text-danger">
                                        <i class="bi bi-exclamation-circle"></i>
                                        {{ $item->tanggal_berakhir->format('d M Y') }}
                                    </span>
                                @else
                                    {{ $item->tanggal_berakhir->format('d M Y') }}
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-primary-subtle text-primary">
                                    <i class="bi bi-eye"></i> {{ $item->views_count }}
                                </span>
                            </td>
                            <td>
                                @if($item->status && !$item->is_expired)
                                    <span class="badge bg-success">Aktif</span>
                                @elseif($item->is_expired)
                                    <span class="badge bg-danger">Expired</span>
                                @else
                                    <span class="badge bg-secondary">Tidak Aktif</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.lowongan-kerja.edit', $item->id) }}" 
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
                            <td colspan="10" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                                    <p class="mb-0">Tidak ada data lowongan kerja</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($lowongan->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    Menampilkan {{ $lowongan->firstItem() }} - {{ $lowongan->lastItem() }} dari {{ $lowongan->total() }} data
                </div>
                {{ $lowongan->links() }}
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
<form id="bulkStatusForm" method="POST" action="{{ route('admin.lowongan-kerja.bulk-status') }}" style="display: none;">
    @csrf
    <input type="hidden" name="ids" id="bulkStatusIds">
    <input type="hidden" name="status" id="bulkStatus">
</form>

<form id="bulkDeleteForm" method="POST" action="{{ route('admin.lowongan-kerja.bulk-delete') }}" style="display: none;">
    @csrf
    <input type="hidden" name="ids" id="bulkDeleteIds">
</form>

@push('styles')
<style>
    .text-gradient {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
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
        
        // Update select all
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
            if (!confirm(`Apakah Anda yakin ingin menghapus ${selectedIds.length} lowongan kerja?`)) {
                return;
            }
            $('#bulkDeleteIds').val(JSON.stringify(selectedIds));
            $('#bulkDeleteForm').submit();
        } else if (action === 'activate' || action === 'deactivate') {
            const status = action === 'activate' ? 1 : 0;
            const statusText = action === 'activate' ? 'mengaktifkan' : 'menonaktifkan';
            
            if (!confirm(`Apakah Anda yakin ingin ${statusText} ${selectedIds.length} lowongan kerja?`)) {
                return;
            }
            
            $('#bulkStatusIds').val(JSON.stringify(selectedIds));
            $('#bulkStatus').val(status);
            $('#bulkStatusForm').submit();
        }
    }

    // Delete single item
    function deleteItem(id, title) {
        if (confirm(`Apakah Anda yakin ingin menghapus lowongan "${title}"?`)) {
            const form = $('#deleteForm');
            form.attr('action', `/admin/lowongan-kerja/${id}`);
            form.submit();
        }
    }
</script>
@endpush
@endsection