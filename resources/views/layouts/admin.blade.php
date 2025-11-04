<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin Dashboard') - CDC Polibatam</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/cdcp.png') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        :root {
            --primary: #0096FF;
            --primary-dark: #0077CC;
            --primary-light: #33AAFF;
            --secondary: #00D4FF;
            --accent: #0088E6;
            --warning: #f59e0b;
            --danger: #ef4444;
            --info: #3b82f6;
            --success: #10b981;
            --light: #f8fafc;
            --dark: #1f2937;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --gray-900: #111827;
            --sidebar-width: 280px;
            --sidebar-collapsed: 80px;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, var(--gray-50) 0%, var(--gray-100) 100%);
            overflow-x: hidden;
            color: var(--gray-700);
        }

        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: linear-gradient(180deg, var(--gray-900) 0%, var(--gray-800) 100%);
            backdrop-filter: blur(10px);
            border-right: 1px solid rgba(0, 150, 255, 0.1);
            box-shadow: var(--shadow-xl);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1000;
            overflow: hidden;
            transform: translateX(-100%);
        }

        .sidebar.show {
            transform: translateX(0);
        }

        .sidebar.collapsed {
            width: var(--sidebar-collapsed);
        }

        .sidebar-header {
            padding: 1.5rem 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            display: flex;
            align-items: center;
            gap: 1rem;
            position: relative;
        }

        .sidebar-header::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--primary), transparent);
        }

        .logo {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            position: relative;
            overflow: hidden;
        }

        .logo::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.2) 0%, transparent 100%);
        }

        .logo i {
            color: white;
            font-size: 1.5rem;
            z-index: 1;
        }

        .brand {
            color: white;
            font-size: 1.25rem;
            font-weight: 800;
            font-family: 'Poppins', sans-serif;
            white-space: nowrap;
            transition: all 0.4s ease;
            background: linear-gradient(135deg, #ffffff 0%, var(--primary-light) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .sidebar.collapsed .brand {
            opacity: 0;
            transform: translateX(-20px);
        }

        .sidebar-nav {
            padding: 1rem 0;
            height: calc(100vh - 120px);
            overflow-y: auto;
            overflow-x: hidden;
            scrollbar-width: thin;
            scrollbar-color: var(--primary) transparent;
        }

        .sidebar-nav::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar-nav::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar-nav::-webkit-scrollbar-thumb {
            background: var(--primary);
            border-radius: 4px;
        }

        .nav-section {
            margin-bottom: 2rem;
        }

        .nav-section-title {
            color: var(--gray-400);
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            padding: 0 1.5rem;
            margin-bottom: 0.75rem;
            transition: all 0.4s ease;
        }

        .sidebar.collapsed .nav-section-title {
            opacity: 0;
            transform: translateX(-20px);
        }

        .nav-item {
            margin: 0.25rem 0.75rem;
            position: relative;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.875rem 1.25rem;
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            border-radius: 16px;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            font-weight: 500;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
        }

        .nav-link::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            opacity: 0;
            transition: all 0.4s ease;
        }

        .nav-link:hover::before {
            opacity: 0.15;
        }

        .nav-link.active::before {
            opacity: 1;
        }

        .nav-link:hover {
            color: white;
            transform: translateX(4px);
            box-shadow: var(--shadow-lg);
        }

        .nav-link.active {
            color: white;
            box-shadow: 0 8px 20px rgba(0, 150, 255, 0.3);
        }

        .nav-link i {
            font-size: 1.25rem;
            width: 24px;
            text-align: center;
            flex-shrink: 0;
            z-index: 1;
        }

        .nav-link span {
            white-space: nowrap;
            transition: all 0.4s ease;
            z-index: 1;
        }

        .sidebar.collapsed .nav-link span {
            opacity: 0;
            transform: translateX(-20px);
        }

        /* Main Content */
        .main-content {
            margin-left: 0;
            transition: margin-left 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            min-height: 100vh;
        }

        @media (min-width: 768px) {
            .sidebar {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: var(--sidebar-width);
            }

            .sidebar.collapsed ~ .main-content {
                margin-left: var(--sidebar-collapsed);
            }
        }

        /* Top Navigation */
        .top-nav {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0, 150, 255, 0.1);
            padding: 1rem 2rem;
            box-shadow: var(--shadow);
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .nav-left {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .sidebar-toggle {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--gray-600);
            cursor: pointer;
            padding: 0.75rem;
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .sidebar-toggle:hover {
            background: var(--gray-100);
            color: var(--primary);
            transform: scale(1.05);
        }

        .navbar-brand {
            font-size: 1.25rem;
            font-weight: 700;
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin: 0;
        }

        .nav-right {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.5rem 1rem;
            border-radius: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid transparent;
            text-decoration: none;
            color: inherit;
        }

        .user-profile:hover {
            background: var(--gray-100);
            border-color: var(--primary);
            color: inherit;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .user-avatar::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.2) 0%, transparent 100%);
        }

        .avatar-letter {
            color: white;
            font-weight: 700;
            font-size: 0.875rem;
            z-index: 1;
        }

        .user-info {
            display: flex;
            flex-direction: column;
        }

        .user-name {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--gray-700);
            line-height: 1.2;
        }

        .user-role {
            font-size: 0.75rem;
            color: var(--gray-500);
            line-height: 1.2;
            text-transform: capitalize;
        }

        /* Page Content */
        .page-content {
            padding: 2rem;
        }

        /* Alert Styles */
        .alert {
            border: none;
            border-radius: 16px;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: var(--shadow);
        }

        .alert-success {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(16, 185, 129, 0.05) 100%);
            border-left: 4px solid var(--success);
            color: #065f46;
        }

        .alert-danger {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.1) 0%, rgba(239, 68, 68, 0.05) 100%);
            border-left: 4px solid var(--danger);
            color: #7f1d1d;
        }

        /* Statistics Cards */
        .stat-card {
            border: none;
            border-radius: 20px;
            overflow: hidden;
            transition: all 0.4s ease;
            position: relative;
            color: white;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-xl);
        }

        .stat-card.primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        }

        .stat-card.success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        .stat-card.warning {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }

        .stat-card.info {
            background: linear-gradient(135deg, var(--secondary) 0%, var(--accent) 100%);
        }

        /* Card Styles */
        .card {
            border: none;
            border-radius: 20px;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .card:hover {
            box-shadow: var(--shadow-lg);
        }

        .card-header {
            background: linear-gradient(135deg, rgba(0, 150, 255, 0.05) 0%, rgba(0, 212, 255, 0.05) 100%);
            border-bottom: 1px solid rgba(0, 150, 255, 0.1);
            padding: 1.5rem;
        }

        .card-header h5 {
            color: var(--primary);
            font-weight: 700;
            margin: 0;
        }

        /* Button Styles */
        .btn {
            border-radius: 12px;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            transition: all 0.3s ease;
            border: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
        }

        .navbar-brand-logo {
            height: 45px;
            object-fit: contain;
            background: lightblue;
            padding: 5px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--accent) 100%);
        }

        .btn-outline-primary {
            border: 2px solid var(--primary);
            color: var(--primary);
            background: transparent;
        }

        .btn-outline-primary:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-2px);
        }

        /* Table Styles */
        .table {
            border-radius: 16px;
            overflow: hidden;
        }

        .table th {
            background: var(--gray-50);
            border: none;
            font-weight: 700;
            color: var(--gray-700);
            padding: 1rem;
        }

        .table td {
            border: none;
            padding: 1rem;
            border-bottom: 1px solid var(--gray-100);
        }

        /* Badge Styles */
        .badge {
            padding: 0.5rem 1rem;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.75rem;
        }

        /* Form Styles */
        .form-control, .form-select {
            border: 2px solid var(--gray-200);
            border-radius: 12px;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(0, 150, 255, 0.25);
        }

        .form-label {
            font-weight: 600;
            color: var(--gray-700);
            margin-bottom: 0.5rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .top-nav {
                padding: 1rem;
            }

            .page-content {
                padding: 1rem;
            }

            .user-info {
                display: none;
            }

            .navbar-brand {
                font-size: 1rem;
            }
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Mobile Sidebar Overlay */
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .sidebar-overlay.show {
            opacity: 1;
            visibility: visible;
        }

        @media (min-width: 768px) {
            .sidebar-overlay {
                display: none;
            }
        }
    </style>

    @stack('styles')
</head>
<body>
    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <img src="{{ asset('images/cdcp.png') }}" alt="CDC Polibatam Logo" class="navbar-brand-logo">
            <div class="brand">CDC Admin</div>
        </div>
        
        <div class="sidebar-nav">
            <!-- Main Section -->
            <div class="nav-section">
                <div class="nav-section-title">Main</div>
                <div class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" 
                       href="{{ route('admin.dashboard') }}">
                        <i class="bi bi-speedometer2"></i>
                        <span>Dashboard</span>
                    </a>
                </div>
            </div>

            <!-- Management Section -->
            <div class="nav-section">
                <div class="nav-section-title">Management</div>
                <div class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.lowongan-kerja*') ? 'active' : '' }}" 
                       href="{{ route('admin.lowongan-kerja.index') }}">
                        <i class="bi bi-briefcase"></i>
                        <span>Lowongan Kerja</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.program-magang*') ? 'active' : '' }}" 
                       href="{{ route('admin.program-magang.index') }}">
                        <i class="bi bi-people"></i>
                        <span>Program Magang</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.berita*') ? 'active' : '' }}" 
                       href="{{ route('admin.berita.index') }}">
                        <i class="bi bi-newspaper"></i>
                        <span>Berita</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.newsletter*') ? 'active' : '' }}" 
                       href="{{ route('admin.newsletter.index') }}">
                        <i class="bi bi-envelope"></i>
                        <span>Newsletter</span>
                    </a>
                </div>
            </div>

            <!-- Content Section -->
            <div class="nav-section">
                <div class="nav-section-title">Content</div>
                <div class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.tentang*') ? 'active' : '' }}" 
                       href="{{ route('admin.tentang.edit') }}">
                        <i class="bi bi-info-circle"></i>
                        <span>Tentang</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.kontak*') ? 'active' : '' }}" 
                       href="{{ route('admin.kontak.edit') }}">
                        <i class="bi bi-telephone"></i>
                        <span>Kontak</span>
                    </a>
                </div>
            </div>

            <!-- System Section -->
            <div class="nav-section">
                <div class="nav-section-title">System</div>
                <div class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.activity-logs*') ? 'active' : '' }}" 
                       href="{{ route('admin.activity-logs.index') }}">
                        <i class="bi bi-clock-history"></i>
                        <span>Activity Logs</span>
                    </a>
                </div>
            </div>

            <!-- Account Section -->
            <div class="nav-section">
                <div class="nav-section-title">Account</div>
                <div class="nav-item">
                    <a class="nav-link" href="{{ route('home') }}" target="_blank">
                        <i class="bi bi-globe"></i>
                        <span>Lihat Website</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a class="nav-link" href="{{ route('profile') }}">
                        <i class="bi bi-person"></i>
                        <span>Profile</span>
                    </a>
                </div>
                <div class="nav-item">
                    <form method="POST" action="{{ route('logout') }}" class="d-inline w-100">
                        @csrf
                        <button type="submit" class="nav-link w-100">
                            <i class="bi bi-box-arrow-right"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navigation -->
        <div class="top-nav">
            <div class="nav-left">
                <button class="sidebar-toggle" onclick="toggleSidebar()">
                    <i class="bi bi-list"></i>
                </button>
                
                <span class="navbar-brand">
                    CDC Polibatam - Admin Panel
                </span>
            </div>
            
            <div class="nav-right">
                <div class="user-profile">
                    <div class="user-avatar">
                        <span class="avatar-letter">{{ substr(auth()->user()->name, 0, 1) }}</span>
                    </div>
                    <div class="user-info">
                        <div class="user-name">{{ auth()->user()->name }}</div>
                        <div class="user-role">{{ auth()->user()->role }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Page Content -->
        <div class="page-content">
            <!-- Alert Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <ul class="mb-0 ms-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        // CSRF Token Setup
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Toggle sidebar
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            
            if (window.innerWidth < 768) {
                sidebar.classList.toggle('show');
                overlay.classList.toggle('show');
            } else {
                sidebar.classList.toggle('collapsed');
            }
        }

        // Auto-hide alerts
        setTimeout(function() {
            $('.alert').alert('close');
        }, 5000);

        // Preview image upload
        function previewImage(input, previewId = 'image-preview') {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#' + previewId).attr('src', e.target.result).show();
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Close sidebar on mobile when clicking outside
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.querySelector('.sidebar-toggle');
            
            if (window.innerWidth < 768 && 
                !sidebar.contains(event.target) && 
                !sidebarToggle.contains(event.target) &&
                sidebar.classList.contains('show')) {
                toggleSidebar();
            }
        });

        // Handle window resize
        window.addEventListener('resize', function() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            
            if (window.innerWidth >= 768) {
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
            }
        });
    </script>

    @stack('scripts')
</body>
</html>