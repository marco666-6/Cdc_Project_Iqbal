<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'CDC Polibatam - Career Development Center')</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/cdcp.png') }}">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        :root {
            --primary-color: #0096FF;
            --primary-dark: #0077CC;
            --primary-light: #33AAFF;
            --gradient-start: #0096FF;
            --gradient-end: #00D4FF;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            padding-top: 76px; /* Height of fixed navbar */
        }
        
        /* Improved Navbar Styles */
        .navbar {
            background: linear-gradient(135deg, var(--gradient-start) 0%, var(--gradient-end) 100%);
            box-shadow: 0 4px 12px rgba(0, 150, 255, 0.3);
            padding: 0.75rem 0;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1030;
            transition: all 0.3s ease;
        }
        
        .navbar.scrolled {
            padding: 0.5rem 0;
            box-shadow: 0 6px 20px rgba(0, 150, 255, 0.4);
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: white !important;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transition: transform 0.3s ease;
        }
        
        .navbar-brand:hover {
            transform: translateY(-2px);
        }
        
        .navbar-brand-logo {
            height: 45px;
            object-fit: contain;
            background: lightblue;
            padding: 5px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        
        .navbar-brand-text {
            display: flex;
            flex-direction: column;
            line-height: 1.2;
        }
        
        .navbar-brand-title {
            font-size: 1.25rem;
            font-weight: 700;
        }
        
        .navbar-brand-subtitle {
            font-size: 0.75rem;
            opacity: 0.9;
            font-weight: 400;
        }
        
        .navbar-toggler {
            border: 2px solid rgba(255, 255, 255, 0.5);
            padding: 0.5rem 0.75rem;
            transition: all 0.3s ease;
        }
        
        .navbar-toggler:hover {
            border-color: white;
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .navbar-toggler:focus {
            box-shadow: 0 0 0 0.25rem rgba(255, 255, 255, 0.25);
        }
        
        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 1%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }
        
        .navbar-nav {
            align-items: center;
            gap: 0.25rem;
        }
        
        .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            font-weight: 500;
            padding: 0.5rem 1rem !important;
            border-radius: 8px;
            transition: all 0.3s ease;
            position: relative;
            white-space: nowrap;
        }
        
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 2px;
            background: white;
            transition: width 0.3s ease;
        }
        
        .nav-link:hover {
            color: white !important;
            background-color: rgba(255, 255, 255, 0.15);
            transform: translateY(-2px);
        }
        
        .nav-link:hover::after {
            width: 60%;
        }
        
        .nav-link.active {
            color: white !important;
            background-color: rgba(255, 255, 255, 0.2);
            font-weight: 600;
        }
        
        .nav-link.active::after {
            width: 60%;
        }
        
        /* Dropdown Improvements */
        .dropdown-menu {
            border: none;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
            padding: 0.5rem;
            margin-top: 0.5rem;
            animation: dropdownFadeIn 0.3s ease;
        }
        
        @keyframes dropdownFadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .dropdown-item {
            border-radius: 8px;
            padding: 0.75rem 1rem;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .dropdown-item:hover {
            background-color: rgba(0, 150, 255, 0.1);
            color: var(--primary-color);
            transform: translateX(5px);
        }
        
        .dropdown-item.text-danger:hover {
            background-color: rgba(220, 53, 69, 0.1);
            color: #dc3545;
        }
        
        .dropdown-divider {
            margin: 0.5rem 0;
            opacity: 0.1;
        }
        
        /* Mobile Menu Improvements */
        @media (max-width: 991.98px) {
            body {
                padding-top: 70px;
            }
            
            .navbar-collapse {
                background: linear-gradient(135deg, rgba(0, 150, 255, 0.98) 0%, rgba(0, 212, 255, 0.98) 100%);
                border-radius: 12px;
                margin-top: 1rem;
                padding: 1rem;
                box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
            }
            
            .navbar-nav {
                gap: 0.5rem;
            }
            
            .nav-link {
                padding: 0.75rem 1rem !important;
            }
            
            .dropdown-menu {
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(10px);
            }
            
            .navbar-brand-subtitle {
                display: none;
            }
            
            .navbar-brand-title {
                font-size: 1.1rem;
            }
        }
        
        /* Tablet Adjustments */
        @media (max-width: 768px) {
            .navbar-brand-logo {
                width: 35px;
                height: 35px;
            }
            
            .navbar-brand-title {
                font-size: 1rem;
            }
        }
        
        /* Rest of the styles remain the same */
        .btn-primary {
            background: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background: var(--primary-dark);
            border-color: var(--primary-dark);
        }
        
        .btn-gradient {
            background: linear-gradient(135deg, var(--gradient-start) 0%, var(--gradient-end) 100%);
            border: none;
            color: white;
        }
        
        .btn-gradient:hover {
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--gradient-start) 100%);
            color: white;
        }
        
        .text-primary {
            color: var(--primary-color) !important;
        }
        
        .bg-gradient-primary {
            background: linear-gradient(135deg, var(--gradient-start) 0%, var(--gradient-end) 100%);
        }
        
        main {
            flex: 1;
        }
        
        footer {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            color: white;
            margin-top: auto;
        }
        
        footer a {
            color: var(--primary-light);
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        footer a:hover {
            color: white;
        }
        
        .card {
            transition: transform 0.3s, box-shadow 0.3s;
            border: none;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 16px rgba(0,150,255,0.2);
        }
        
        .badge-primary {
            background-color: var(--primary-color);
        }
        
        .alert {
            border-radius: 8px;
            border: none;
        }
    </style>
    
    @yield('styles')
</head>
<body>
    <!-- Improved Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <img src="{{ asset('images/cdcp.png') }}" alt="CDC Polibatam Logo" class="navbar-brand-logo">
                <div class="navbar-brand-text">
                    <span class="navbar-brand-title">CDC Polibatam</span>
                    <span class="navbar-brand-subtitle">Career Development Center</span>
                </div>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ Request::routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                            <i class="bi bi-house-door me-1"></i>Beranda
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Request::routeIs('lowongan-kerja*') ? 'active' : '' }}" href="{{ route('lowongan-kerja') }}">
                            <i class="bi bi-briefcase me-1"></i>Lowongan Kerja
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Request::routeIs('program-magang*') ? 'active' : '' }}" href="{{ route('program-magang') }}">
                            <i class="bi bi-mortarboard me-1"></i>Program Magang
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Request::routeIs('berita*') ? 'active' : '' }}" href="{{ route('berita') }}">
                            <i class="bi bi-newspaper me-1"></i>Berita
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Request::routeIs('tentang') ? 'active' : '' }}" href="{{ route('tentang') }}">
                            <i class="bi bi-info-circle me-1"></i>Tentang
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Request::routeIs('kontak') ? 'active' : '' }}" href="{{ route('kontak') }}">
                            <i class="bi bi-envelope me-1"></i>Kontak
                        </a>
                    </li>
                    
                    @auth
                    <!-- Admin Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle me-2 fs-5"></i>
                            <span>{{ Auth::user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="adminDropdown">
                            <li>
                                <a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                    <i class="bi bi-speedometer2"></i>Dashboard Admin
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('profile') }}">
                                    <i class="bi bi-person"></i>Profile
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right"></i>Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                    @else
                    <!-- Login Button -->
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">
                            <i class="bi bi-box-arrow-in-right me-1"></i>Login
                        </a>
                    </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="py-5 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5 class="fw-bold mb-3">CDC Polibatam</h5>
                    <p class="text-light">Career Development Center Politeknik Negeri Batam membantu mahasiswa dalam mengembangkan karir dan mendapatkan peluang kerja terbaik.</p>
                </div>
                <div class="col-md-4 mb-4">
                    <h5 class="fw-bold mb-3">Link Cepat</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="{{ route('lowongan-kerja') }}"><i class="bi bi-chevron-right"></i> Lowongan Kerja</a></li>
                        <li class="mb-2"><a href="{{ route('program-magang') }}"><i class="bi bi-chevron-right"></i> Program Magang</a></li>
                        <li class="mb-2"><a href="{{ route('berita') }}"><i class="bi bi-chevron-right"></i> Berita</a></li>
                        <li class="mb-2"><a href="{{ route('tentang') }}"><i class="bi bi-chevron-right"></i> Tentang Kami</a></li>
                    </ul>
                </div>
                <div class="col-md-4 mb-4">
                    <h5 class="fw-bold mb-3">Newsletter</h5>
                    <p class="text-light">Dapatkan informasi terbaru tentang lowongan kerja dan program magang.</p>
                    <form action="{{ route('newsletter.subscribe') }}" method="POST">
                        @csrf
                        <div class="input-group">
                            <input type="email" name="email" class="form-control" placeholder="Email Anda" required>
                            <button class="btn btn-light" type="submit">
                                <i class="bi bi-send"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <hr class="my-4 bg-light">
            <div class="row">
                <div class="col-md-6 text-center text-md-start">
                    <p class="mb-0 text-light">&copy; {{ date('Y') }} CDC Polibatam. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <a href="#" class="me-3"><i class="bi bi-facebook fs-5"></i></a>
                    <a href="#" class="me-3"><i class="bi bi-instagram fs-5"></i></a>
                    <a href="#" class="me-3"><i class="bi bi-linkedin fs-5"></i></a>
                    <a href="#"><i class="bi bi-twitter fs-5"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Navbar Scroll Effect -->
    <script>
        // Add scroll effect to navbar
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
        
        // Close mobile menu when clicking outside
        document.addEventListener('click', function(event) {
            const navbar = document.querySelector('.navbar-collapse');
            const toggler = document.querySelector('.navbar-toggler');
            
            if (navbar.classList.contains('show') && 
                !navbar.contains(event.target) && 
                !toggler.contains(event.target)) {
                toggler.click();
            }
        });
    </script>
    
    <!-- Alert Auto-hide -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                let alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    let bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);
        });
    </script>
    
    @yield('scripts')
</body>
</html>