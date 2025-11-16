{{-- resources/views/admin/newsletter/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Newsletter Subscribers')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h1 class="h3 mb-2 fw-bold text-gradient">
                        <i class="bi bi-envelope-heart me-2"></i>Newsletter Subscribers
                    </h1>
                    <p class="text-muted mb-0">Kelola daftar pelanggan newsletter</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle bg-primary bg-opacity-10 p-3">
                                <i class="bi bi-people fs-4 text-primary"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Total</h6>
                            <h3 class="mb-0">{{ $stats['total'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle bg-success bg-opacity-10 p-3">
                                <i class="bi bi-check-circle fs-4 text-success"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Active</h6>
                            <h3 class="mb-0">{{ $stats['active'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle bg-warning bg-opacity-10 p-3">
                                <i class="bi bi-hourglass-split fs-4 text-warning"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Unverified</h6>
                            <h3 class="mb-0">{{ $stats['unverified'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle bg-danger bg-opacity-10 p-3">
                                <i class="bi bi-x-circle fs-4 text-danger"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Unsubscribed</h6>
                            <h3 class="mb-0">{{ $stats['unsubscribed'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.newsletter.index') }}" id="filterForm">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Cari</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" class="form-control" name="search" 
                                   placeholder="Cari email atau nama..." 
                                   value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status">
                            <option value="">Semua</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="unverified" {{ request('status') == 'unverified' ? 'selected' : '' }}>Unverified</option>
                            <option value="unsubscribed" {{ request('status') == 'unsubscribed' ? 'selected' : '' }}>Unsubscribed</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-fill">
                                <i class="bi bi-funnel"></i>
                            </button>
                            <a href="{{ route('admin.newsletter.index') }}" class="btn btn-outline-secondary">
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
                    <button type="button" class="btn btn-sm btn-danger" onclick="bulkAction('delete')">
                        <i class="bi bi-trash me-1"></i>Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Subscribers Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th width="50">
                                <input type="checkbox" class="form-check-input" id="selectAll">
                            </th>
                            <th>Email</th>
                            <th>Nama</th>
                            <th>Tanggal Daftar</th>
                            <th>Verified</th>
                            <th>Status</th>
                            <th width="100">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($subscribers as $item)
                        <tr>
                            <td>
                                <input type="checkbox" class="form-check-input row-checkbox" value="{{ $item->id }}">
                            </td>
                            <td>
                                <i class="bi bi-envelope text-muted me-2"></i>
                                {{ $item->email }}
                            </td>
                            <td>{{ $item->nama ?: '-' }}</td>
                            <td>{{ $item->created_at->format('d M Y H:i') }}</td>
                            <td>
                                @if($item->is_verified)
                                    <span class="badge bg-success-subtle text-success">
                                        <i class="bi bi-check-circle"></i> Verified
                                    </span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning">
                                        <i class="bi bi-hourglass-split"></i> Unverified
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($item->status && $item->is_verified)
                                    <span class="badge bg-success">Active</span>
                                @elseif(!$item->status)
                                    <span class="badge bg-danger">Unsubscribed</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-danger" 
                                        onclick="deleteItem({{ $item->id }}, '{{ $item->email }}')" 
                                        title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                                    <p class="mb-0">Tidak ada data subscriber</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($subscribers->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    Menampilkan {{ $subscribers->firstItem() }} - {{ $subscribers->lastItem() }} dari {{ $subscribers->total() }} data
                </div>
                {{ $subscribers->links() }}
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

<!-- Bulk Delete Form -->
<form id="bulkDeleteForm" method="POST" action="{{ route('admin.newsletter.bulk-delete') }}" style="display: none;">
    @csrf
    <input type="hidden" name="ids" id="bulkDeleteIds">
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
        const selected = Array.from(document.querySelectorAll('.row-checkbox:checked')).map(cb => cb.value);

        if (selected.length === 0) {
            alert('Pilih minimal satu item.');
            return;
        }

        let url = '';
        let method = 'POST';
        let data = {};
        let confirmMessage = '';

        if (action === 'delete') {
            confirmMessage = `Apakah Anda yakin ingin menghapus ${selected.length} subscriber?`;
            url = '{{ route("admin.newsletter.bulk-delete") }}';
            data = {
                ids: selected, // <-- true array, not JSON string
                _token: '{{ csrf_token() }}'
            };
        }

        if (!confirm(confirmMessage)) return;

        fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(data)
        })
        .then(res => {
            if (!res.ok) throw new Error('Gagal melakukan aksi.');
            return res.json().catch(() => ({}));
        })
        .then(() => location.reload())
        .catch(err => alert(err.message));
    }

    // Delete single item
    function deleteItem(id, email) {
        if (confirm(`Apakah Anda yakin ingin menghapus subscriber "${email}"?`)) {
            const form = $('#deleteForm');
            form.attr('action', `/admin/newsletter/${id}`);
            form.submit();
        }
    }
</script>
@endpush
@endsection