{{-- resources/views/auth/password.blade.php --}}
@extends('layouts.admin')

@section('title', 'Change Password')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-2 fw-bold text-gradient">
                        <i class="bi bi-key me-2"></i>Change Password
                    </h1>
                    <p class="text-muted mb-0">Perbarui password akun Anda</p>
                </div>
                <a href="{{ route('profile') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Kembali ke Profile
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 mx-auto">
            <!-- Change Password Form -->
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-shield-lock text-warning me-2"></i>Update Password
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Security Notice -->
                    <div class="alert alert-info border-0 mb-4">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Tips Keamanan:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Gunakan minimal 8 karakter</li>
                            <li>Kombinasikan huruf besar, huruf kecil, angka, dan simbol</li>
                            <li>Jangan gunakan password yang mudah ditebak</li>
                            <li>Ubah password secara berkala</li>
                        </ul>
                    </div>

                    <form action="{{ route('profile.password') }}" method="POST" id="changePasswordForm">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-4">
                            <label for="current_password" class="form-label">
                                Password Saat Ini <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-white">
                                    <i class="bi bi-lock"></i>
                                </span>
                                <input type="password" class="form-control @error('current_password') is-invalid @enderror" 
                                       id="current_password" name="current_password" required 
                                       placeholder="Masukkan password saat ini">
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('current_password')">
                                    <i class="bi bi-eye" id="current_password_icon"></i>
                                </button>
                                @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="new_password" class="form-label">
                                Password Baru <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-white">
                                    <i class="bi bi-lock-fill"></i>
                                </span>
                                <input type="password" class="form-control @error('new_password') is-invalid @enderror" 
                                       id="new_password" name="new_password" required 
                                       placeholder="Masukkan password baru"
                                       minlength="8">
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('new_password')">
                                    <i class="bi bi-eye" id="password_icon"></i>
                                </button>
                                @error('new_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <!-- Password Strength Indicator -->
                            <div class="mt-2">
                                <div class="progress" style="height: 5px;">
                                    <div id="passwordStrength" class="progress-bar" role="progressbar" style="width: 0%"></div>
                                </div>
                                <small id="passwordStrengthText" class="text-muted"></small>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="new_password_confirmation" class="form-label">
                                Konfirmasi Password Baru <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-white">
                                    <i class="bi bi-lock-fill"></i>
                                </span>
                                <input type="password" class="form-control @error('new_password_confirmation') is-invalid @enderror" 
                                       id="new_password_confirmation" name="new_password_confirmation" required 
                                       placeholder="Konfirmasi password baru"
                                       minlength="8">
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('new_password_confirmation')">
                                    <i class="bi bi-eye" id="new_password_confirmation_icon"></i>
                                </button>
                                @error('new_password_confirmation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small id="passwordMatch" class="text-muted"></small>
                        </div>

                        <div class="border-top pt-3">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-key me-2"></i>Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Additional Info -->
            <div class="card mt-4 shadow-sm border-warning">
                <div class="card-body">
                    <div class="d-flex align-items-start">
                        <div class="flex-shrink-0">
                            <i class="bi bi-exclamation-triangle-fill text-warning fs-3"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-2">Perhatian</h6>
                            <p class="text-muted mb-0 small">
                                Setelah mengubah password, Anda akan tetap login di sesi ini. 
                                Pastikan Anda mengingat password baru untuk login berikutnya.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
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

    .card {
        border: none;
        transition: all 0.3s ease;
    }

    .input-group-text {
        border-right: 0;
    }

    .input-group .form-control {
        border-left: 0;
        border-right: 0;
    }

    .input-group .btn-outline-secondary {
        border-left: 0;
    }

    .input-group .form-control:focus {
        box-shadow: none;
        border-color: #dee2e6;
    }

    .progress {
        background-color: #e9ecef;
    }
</style>
@endpush

@push('scripts')
<script>
    // Toggle password visibility
    function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        const icon = document.getElementById(fieldId + '_icon');
        
        if (field.type === 'password') {
            field.type = 'text';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            field.type = 'password';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    }

    // Password strength checker
    $('#new_password').on('input', function() {
        const password = $(this).val();
        const strength = checkPasswordStrength(password);
        
        const strengthBar = $('#passwordStrength');
        const strengthText = $('#passwordStrengthText');
        
        strengthBar.removeClass('bg-danger bg-warning bg-info bg-success');
        
        if (password.length === 0) {
            strengthBar.css('width', '0%');
            strengthText.text('');
        } else if (strength.score < 2) {
            strengthBar.addClass('bg-danger').css('width', '25%');
            strengthText.text('Lemah').removeClass('text-muted').addClass('text-danger');
        } else if (strength.score < 3) {
            strengthBar.addClass('bg-warning').css('width', '50%');
            strengthText.text('Cukup').removeClass('text-muted text-danger').addClass('text-warning');
        } else if (strength.score < 4) {
            strengthBar.addClass('bg-info').css('width', '75%');
            strengthText.text('Baik').removeClass('text-muted text-danger text-warning').addClass('text-info');
        } else {
            strengthBar.addClass('bg-success').css('width', '100%');
            strengthText.text('Sangat Baik').removeClass('text-muted text-danger text-warning text-info').addClass('text-success');
        }
    });

    // Check password match
    $('#new_password_confirmation').on('input', function() {
        const password = $('#new_password').val();
        const confirmation = $(this).val();
        const matchText = $('#passwordMatch');
        
        if (confirmation.length === 0) {
            matchText.text('').removeClass('text-success text-danger');
        } else if (password === confirmation) {
            matchText.text('✓ Password cocok').removeClass('text-danger').addClass('text-success');
        } else {
            matchText.text('✗ Password tidak cocok').removeClass('text-success').addClass('text-danger');
        }
    });

    // Password strength calculation
    function checkPasswordStrength(password) {
        let score = 0;
        
        if (password.length >= 8) score++;
        if (password.length >= 12) score++;
        if (/[a-z]/.test(password) && /[A-Z]/.test(password)) score++;
        if (/\d/.test(password)) score++;
        if (/[^a-zA-Z0-9]/.test(password)) score++;
        
        return { score: score };
    }

    // Form validation
    $('#changePasswordForm').on('submit', function(e) {
        const password = $('#new_password').val();
        const confirmation = $('#new_password_confirmation').val();
        
        if (password !== confirmation) {
            e.preventDefault();
            alert('Password dan konfirmasi password tidak cocok!');
            $('#new_password_confirmation').focus();
            return false;
        }
        
        if (password.length < 8) {
            e.preventDefault();
            alert('Password harus minimal 8 karakter!');
            $('#new_password').focus();
            return false;
        }
    });
</script>
@endpush
@endsection