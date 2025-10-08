{{-- resources/views/admin/dashboard.blade.php --}}
@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-2 fw-bold text-gradient">Dashboard Overview</h1>
                    <p class="text-muted mb-0">Selamat datang kembali, {{ auth()->user()->name }}!</p>
                </div>
                <div class="text-end">
                    <div class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill">
                        <i class="bi bi-calendar3 me-2"></i>
                        {{ now()->isoFormat('dddd, D MMMM YYYY') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <!-- Job Statistics -->
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card primary">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <p class="text-white-50 mb-2 fw-semibold">Total Lowongan</p>
                            <h2 class="mb-0 fw-bold">{{ $stats['total_jobs'] }}</h2>
                        </div>
                        <div class="stat-icon">
                            <i class="bi bi-briefcase fs-1"></i>
                        </div>
                    </div>
                    <div class="d-flex gap-3 mt-3 pt-3 border-top border-white border-opacity-10">
                        <div class="flex-fill">
                            <div class="small text-white-50">Aktif</div>
                            <div class="fw-bold">{{ $stats['active_jobs'] }}</div>
                        </div>
                        <div class="flex-fill text-end">
                            <div class="small text-white-50">Expired</div>
                            <div class="fw-bold">{{ $stats['expired_jobs'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Program Statistics -->
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card success">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <p class="text-white-50 mb-2 fw-semibold">Program Magang</p>
                            <h2 class="mb-0 fw-bold">{{ $stats['total_programs'] }}</h2>
                        </div>
                        <div class="stat-icon">
                            <i class="bi bi-people fs-1"></i>
                        </div>
                    </div>
                    <div class="d-flex gap-3 mt-3 pt-3 border-top border-white border-opacity-10">
                        <div class="flex-fill">
                            <div class="small text-white-50">Aktif</div>
                            <div class="fw-bold">{{ $stats['active_programs'] }}</div>
                        </div>
                        <div class="flex-fill text-end">
                            <div class="small text-white-50">Expired</div>
                            <div class="fw-bold">{{ $stats['expired_programs'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- News Statistics -->
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card warning">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <p class="text-white-50 mb-2 fw-semibold">Total Berita</p>
                            <h2 class="mb-0 fw-bold">{{ $stats['total_news'] }}</h2>
                        </div>
                        <div class="stat-icon">
                            <i class="bi bi-newspaper fs-1"></i>
                        </div>
                    </div>
                    <div class="d-flex gap-3 mt-3 pt-3 border-top border-white border-opacity-10">
                        <div class="flex-fill">
                            <div class="small text-white-50">Published</div>
                            <div class="fw-bold">{{ $stats['published_news'] }}</div>
                        </div>
                        <div class="flex-fill text-end">
                            <div class="small text-white-50">Draft</div>
                            <div class="fw-bold">{{ $stats['total_news'] - $stats['published_news'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Subscribers Statistics -->
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card info">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <p class="text-white-50 mb-2 fw-semibold">Subscribers</p>
                            <h2 class="mb-0 fw-bold">{{ $stats['total_subscribers'] }}</h2>
                        </div>
                        <div class="stat-icon">
                            <i class="bi bi-envelope-heart fs-1"></i>
                        </div>
                    </div>
                    <div class="pt-3 mt-3 border-top border-white border-opacity-10">
                        <div class="small text-white-50">Active Newsletter Subscribers</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Sections -->
    <div class="row g-4">
        <!-- Popular Content -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-fire text-danger me-2"></i>Konten Populer</h5>
                </div>
                <div class="card-body">
                    <!-- Tabs -->
                    <ul class="nav nav-pills mb-4" id="popularTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="popular-jobs-tab" data-bs-toggle="pill" data-bs-target="#popular-jobs" type="button">
                                <i class="bi bi-briefcase me-2"></i>Lowongan
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="popular-programs-tab" data-bs-toggle="pill" data-bs-target="#popular-programs" type="button">
                                <i class="bi bi-people me-2"></i>Program Magang
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="popular-news-tab" data-bs-toggle="pill" data-bs-target="#popular-news" type="button">
                                <i class="bi bi-newspaper me-2"></i>Berita
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="popularTabContent">
                        <!-- Popular Jobs -->
                        <div class="tab-pane fade show active" id="popular-jobs">
                            @forelse($popularJobs as $job)
                            <div class="d-flex align-items-center p-3 mb-2 rounded-3 hover-card">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar-sm bg-primary bg-opacity-10 rounded-3 d-flex align-items-center justify-content-center">
                                        <i class="bi bi-briefcase text-primary fs-5"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">{{ $job->judul }}</h6>
                                    <small class="text-muted">{{ $job->perusahaan }}</small>
                                </div>
                                <div class="text-end">
                                    <div class="badge bg-info-subtle text-info mb-1">
                                        <i class="bi bi-eye me-1"></i>{{ $job->views_count }}
                                    </div>
                                    <div class="small text-muted">views</div>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Belum ada data
                            </div>
                            @endforelse
                        </div>

                        <!-- Popular Programs -->
                        <div class="tab-pane fade" id="popular-programs">
                            @forelse($popularPrograms as $program)
                            <div class="d-flex align-items-center p-3 mb-2 rounded-3 hover-card">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar-sm bg-success bg-opacity-10 rounded-3 d-flex align-items-center justify-content-center">
                                        <i class="bi bi-people text-success fs-5"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">{{ $program->judul }}</h6>
                                    <small class="text-muted">{{ $program->perusahaan }}</small>
                                </div>
                                <div class="text-end">
                                    <div class="badge bg-info-subtle text-info mb-1">
                                        <i class="bi bi-eye me-1"></i>{{ $program->views_count }}
                                    </div>
                                    <div class="small text-muted">views</div>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Belum ada data
                            </div>
                            @endforelse
                        </div>

                        <!-- Popular News -->
                        <div class="tab-pane fade" id="popular-news">
                            @forelse($popularNews as $news)
                            <div class="d-flex align-items-center p-3 mb-2 rounded-3 hover-card">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar-sm bg-warning bg-opacity-10 rounded-3 d-flex align-items-center justify-content-center">
                                        <i class="bi bi-newspaper text-warning fs-5"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">{{ $news->judul }}</h6>
                                    <small class="text-muted">{{ $news->tanggal_publikasi->format('d M Y') }}</small>
                                </div>
                                <div class="text-end">
                                    <div class="badge bg-info-subtle text-info mb-1">
                                        <i class="bi bi-eye me-1"></i>{{ $news->views_count }}
                                    </div>
                                    <div class="small text-muted">views</div>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Belum ada data
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-clock-history text-primary me-2"></i>Aktivitas Terbaru</h5>
                </div>
                <div class="card-body">
                    <div class="activity-timeline">
                        @forelse($recentActivities as $activity)
                        <div class="activity-item mb-4">
                            <div class="d-flex gap-3">
                                <div class="activity-icon bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; flex-shrink: 0;">
                                    @if($activity->event == 'create')
                                        <i class="bi bi-plus-circle"></i>
                                    @elseif($activity->event == 'update')
                                        <i class="bi bi-pencil"></i>
                                    @elseif($activity->event == 'delete')
                                        <i class="bi bi-trash"></i>
                                    @else
                                        <i class="bi bi-activity"></i>
                                    @endif
                                </div>
                                <div class="flex-grow-1">
                                    <p class="mb-1 small">{{ $activity->description }}</p>
                                    <div class="d-flex align-items-center gap-2 text-muted small">
                                        <span><i class="bi bi-person me-1"></i>{{ $activity->user->name }}</span>
                                        <span>•</span>
                                        <span>{{ $activity->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            Belum ada aktivitas
                        </div>
                        @endforelse
                    </div>
                    
                    @if($recentActivities->count() > 0)
                    <div class="text-center mt-3">
                        <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-sm btn-outline-primary rounded-pill">
                            Lihat Semua Aktivitas
                            <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Content -->
    <div class="row g-4 mt-2">
        <!-- Recent Jobs -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-briefcase text-primary me-2"></i>Lowongan Terbaru</h5>
                    <a href="{{ route('admin.lowongan-kerja.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                </div>
                <div class="card-body">
                    @forelse($recentJobs as $job)
                    <div class="mb-3 p-3 rounded-3 border">
                        <h6 class="mb-2">{{ $job->judul }}</h6>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">{{ $job->perusahaan }}</small>
                            <span class="badge {{ $job->status ? 'bg-success' : 'bg-secondary' }}">
                                {{ $job->status ? 'Aktif' : 'Tidak Aktif' }}
                            </span>
                        </div>
                    </div>
                    @empty
                    <p class="text-muted text-center py-3">Belum ada lowongan</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Recent Programs -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-people text-success me-2"></i>Program Terbaru</h5>
                    <a href="{{ route('admin.program-magang.index') }}" class="btn btn-sm btn-outline-success">Lihat Semua</a>
                </div>
                <div class="card-body">
                    @forelse($recentPrograms as $program)
                    <div class="mb-3 p-3 rounded-3 border">
                        <h6 class="mb-2">{{ $program->judul }}</h6>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">{{ $program->perusahaan }}</small>
                            <span class="badge {{ $program->status ? 'bg-success' : 'bg-secondary' }}">
                                {{ $program->status ? 'Aktif' : 'Tidak Aktif' }}
                            </span>
                        </div>
                    </div>
                    @empty
                    <p class="text-muted text-center py-3">Belum ada program</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Recent News -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-newspaper text-warning me-2"></i>Berita Terbaru</h5>
                    <a href="{{ route('admin.berita.index') }}" class="btn btn-sm btn-outline-warning">Lihat Semua</a>
                </div>
                <div class="card-body">
                    @forelse($recentNews as $news)
                    <div class="mb-3 p-3 rounded-3 border">
                        <h6 class="mb-2">{{ $news->judul }}</h6>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">{{ $news->tanggal_publikasi->format('d M Y') }}</small>
                            <span class="badge {{ $news->status ? 'bg-success' : 'bg-secondary' }}">
                                {{ $news->status ? 'Published' : 'Draft' }}
                            </span>
                        </div>
                    </div>
                    @empty
                    <p class="text-muted text-center py-3">Belum ada berita</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .text-gradient {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .stat-icon {
        opacity: 0.3;
    }

    .hover-card {
        transition: all 0.3s ease;
        border: 1px solid transparent;
    }

    .hover-card:hover {
        background: var(--gray-50);
        border-color: var(--gray-200);
        transform: translateX(5px);
    }

    .avatar-sm {
        width: 48px;
        height: 48px;
    }

    .nav-pills .nav-link {
        border-radius: 12px;
        padding: 0.5rem 1rem;
        font-weight: 600;
        color: var(--gray-600);
        transition: all 0.3s ease;
    }

    .nav-pills .nav-link:hover {
        background: var(--gray-100);
        color: var(--primary);
    }

    .nav-pills .nav-link.active {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        color: white;
    }

    .activity-timeline {
        max-height: 400px;
        overflow-y: auto;
    }

    .activity-timeline::-webkit-scrollbar {
        width: 4px;
    }

    .activity-timeline::-webkit-scrollbar-track {
        background: var(--gray-100);
        border-radius: 4px;
    }

    .activity-timeline::-webkit-scrollbar-thumb {
        background: var(--primary);
        border-radius: 4px;
    }
</style>
@endpush
@endsection