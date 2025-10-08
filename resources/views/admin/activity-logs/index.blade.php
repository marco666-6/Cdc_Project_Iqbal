{{-- resources/views/admin/activity-logs/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Activity Logs')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h1 class="h3 mb-2 fw-bold text-gradient">
                        <i class="bi bi-clock-history me-2"></i>Activity Logs
                    </h1>
                    <p class="text-muted mb-0">Riwayat aktivitas admin</p>
                </div>
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#clearLogsModal">
                    <i class="bi bi-trash me-2"></i>Clear Old Logs
                </button>
            </div>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.activity-logs.index') }}" id="filterForm">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Cari</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" class="form-control" name="search" 
                                   placeholder="Cari aktivitas..." 
                                   value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">User</label>
                        <select class="form-select" name="user_id">
                            <option value="">Semua User</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Event</label>
                        <select class="form-select" name="event">
                            <option value="all">Semua</option>
                            <option value="create" {{ request('event') == 'create' ? 'selected' : '' }}>Create</option>
                            <option value="update" {{ request('event') == 'update' ? 'selected' : '' }}>Update</option>
                            <option value="delete" {{ request('event') == 'delete' ? 'selected' : '' }}>Delete</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Dari Tanggal</label>
                        <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Sampai Tanggal</label>
                        <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-funnel"></i>
                            </button>
                            <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Activity Logs -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th width="150">Waktu</th>
                            <th>User</th>
                            <th>Event</th>
                            <th>Deskripsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr>
                            <td>
                                <small class="text-muted">
                                    {{ $log->created_at->format('d M Y') }}<br>
                                    {{ $log->created_at->format('H:i:s') }}
                                </small>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-primary bg-opacity-10 p-2 me-2">
                                        <i class="bi bi-person text-primary"></i>
                                    </div>
                                    <div>
                                        <strong>{{ $log->user ? $log->user->name : 'System' }}</strong><br>
                                        <small class="text-muted">{{ $log->user ? $log->user->email : '-' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($log->event == 'create')
                                    <span class="badge bg-success-subtle text-success">
                                        <i class="bi bi-plus-circle"></i> CREATE
                                    </span>
                                @elseif($log->event == 'update')
                                    <span class="badge bg-info-subtle text-info">
                                        <i class="bi bi-pencil"></i> UPDATE
                                    </span>
                                @elseif($log->event == 'delete')
                                    <span class="badge bg-danger-subtle text-danger">
                                        <i class="bi bi-trash"></i> DELETE
                                    </span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary">
                                        {{ strtoupper($log->event) }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div>
                                    {{ $log->description }}
                                </div>
                                @if($log->subject_type)
                                    <small class="text-muted">
                                        <i class="bi bi-tag"></i> 
                                        {{ class_basename($log->subject_type) }} 
                                        @if($log->subject_id)
                                            #{{ $log->subject_id }}
                                        @endif
                                    </small>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                                    <p class="mb-0">Tidak ada data activity logs</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($logs->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    Menampilkan {{ $logs->firstItem() }} - {{ $logs->lastItem() }} dari {{ $logs->total() }} data
                </div>
                {{ $logs->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Clear Logs Modal -->
<div class="modal fade" id="clearLogsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Clear Old Activity Logs</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.activity-logs.clear') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Tindakan ini akan menghapus log aktivitas yang lebih lama dari jumlah hari yang ditentukan.
                    </div>
                    
                    <div class="mb-3">
                        <label for="days" class="form-label">Hapus log lebih lama dari (hari)</label>
                        <select class="form-select" id="days" name="days" required>
                            <option value="30">30 hari</option>
                            <option value="60">60 hari</option>
                            <option value="90" selected>90 hari</option>
                            <option value="180">180 hari</option>
                            <option value="365">365 hari</option>
                        </select>
                        <small class="text-muted">Log yang lebih lama dari periode ini akan dihapus permanen</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-2"></i>Hapus Log
                    </button>
                </div>
            </form>
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
    }

    .font-monospace {
        font-family: 'Courier New', monospace;
        font-size: 0.85rem;
    }
</style>
@endpush
@endsection