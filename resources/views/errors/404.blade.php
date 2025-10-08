<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Halaman Tidak Ditemukan</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        :root {
            --primary-color: #0096FF;
            --gradient-start: #0096FF;
            --gradient-end: #00D4FF;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--gradient-start) 0%, var(--gradient-end) 100%);
        }
        
        .error-container {
            text-align: center;
            padding: 2rem;
            color: white;
        }
        
        .error-code {
            font-size: 10rem;
            font-weight: 900;
            line-height: 1;
            margin-bottom: 1rem;
            text-shadow: 0 5px 20px rgba(0,0,0,0.2);
        }
        
        .error-icon {
            font-size: 5rem;
            margin-bottom: 2rem;
            animation: bounce 2s infinite;
        }
        
        @keyframes bounce {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-20px);
            }
        }
        
        .btn-light {
            padding: 0.75rem 2rem;
            font-weight: 600;
            border-radius: 50px;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">
            <i class="bi bi-exclamation-triangle-fill"></i>
        </div>
        <div class="error-code">404</div>
        <h1 class="display-5 fw-bold mb-3">Halaman Tidak Ditemukan</h1>
        <p class="lead mb-4">Maaf, halaman yang Anda cari tidak dapat ditemukan atau telah dipindahkan.</p>
        <div class="d-flex gap-3 justify-content-center flex-wrap">
            <a href="{{ route('home') }}" class="btn btn-light btn-lg">
                <i class="bi bi-house-fill me-2"></i>Kembali ke Beranda
            </a>
            <a href="{{ route('lowongan-kerja') }}" class="btn btn-outline-light btn-lg">
                <i class="bi bi-search me-2"></i>Cari Lowongan
            </a>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>