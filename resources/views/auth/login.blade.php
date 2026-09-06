<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Sign In - JurnalKita</title>
    
    <!-- CSS Dependencies -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Panggil CSS Custom via Vite -->
    @vite(['resources/css/app.css'])
</head>
<body>
    <div class="container-fluid">
        <div class="row g-0 split-row">
            
            <!-- Kolom Kiri -->
            <div class="col-12 col-md-6 brand-area d-flex flex-column">
                <div class="brand-top">
                    <div class="logo-box">
                        <i class="bi bi-mortarboard-fill"></i>
                    </div>
                    <div class="brand-text">JurnalKita</div>
                </div>

                <div class="brand-content">
                    <h1 class="brand-title">JURNAL ESEMKITA</h1>
                    <p class="brand-sub">The unified portal for academic tracking, lesson plans, and seamless school administrative management.</p>
                </div>

                <div class="brand-illustration">
                    <img src="{{ asset('img/hero-illustration.png') }}" alt="Ilustrasi JurnalKita" class="img-fluid">
                </div>
            </div>
            
            <!-- Kolom Kanan -->
            <div class="col-12 col-md-6 login-area d-flex align-items-center justify-content-center position-relative">
                <div class="login-card-logo">
                    <div class="logo-box">
                        <i class="bi bi-mortarboard-fill"></i>
                    </div>
                    <div class="brand-text-dark">JurnalKita</div>
                </div>

                <div class="login-card bg-white">
                    <h2 class="mb-0 login-title">Sign In</h2>
                    <p class="text-muted mb-4 login-subtitle">Selamat Datang.</p>

                    <form action="{{ route('dashboard') }}">
                        <div class="mb-3">
                            <label class="form-label fw-medium">NIP / Username</label>
                            <div class="input-icon-left">
                                <i class="bi bi-person start-icon"></i>
                                <input type="text" class="form-control" placeholder="nip or username" aria-label="NIP or username">
                            </div>
                        </div>

                        <div class="mb-2">
                            <label class="form-label fw-medium">Password</label>
                            <div class="input-icon-right">
                                <i class="bi bi-lock start-icon"></i>
                                <input id="passwordField" type="password" class="form-control" placeholder="............" aria-label="Password">
                                <i id="togglePassword" class="bi bi-eye end-icon" title="Show / hide password"></i>
                            </div>
                        </div>

                        <div class="mb-4 d-flex justify-content-end">
                            <a href="#" class="text-decoration-none small text-muted" data-bs-toggle="modal" data-bs-target="#adminHelpModal">
                                Lupa Password ?
                            </a>
                        </div>

                        <div class="d-grid mb-4">
                            <button type="submit" class="btn btn-signin">Sign In</button>
                        </div>
                    </form>

                    <p class="text-center text-muted mb-0 contact-admin-text">
                        Belum punya akun ? 
                        <a href="#" class="text-decoration-none fw-bold text-success" data-bs-toggle="modal" data-bs-target="#adminHelpModal">
                            Contact Admin.
                        </a>
                    </p>
                </div>

                <!-- Teks Copyright di luar kotak putih -->
                <div class="position-absolute bottom-0 start-0 w-100 text-center pb-4">
                    <small class="text-muted copyright-text">© 2026 JurnalKita Management System. All rights reserved.</small>
                </div>
            </div>

        </div>
    </div>

    <!-- Modal Bantuan Admin -->
    <div class="modal fade" id="adminHelpModal" tabindex="-1" aria-labelledby="adminHelpModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title fw-bold" id="adminHelpModalLabel">Bantuan Akses Akun</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center pb-4">
                    <i class="bi bi-shield-lock text-success modal-help-icon"></i>
                    
                    <p class="mt-3 mb-1 text-dark">
                        Pendaftaran akun baru dan pengaturan ulang kata sandi (reset password) dikelola secara terpusat.
                    </p>
                    <p class="text-muted small mb-4">
                        Silakan hubungi Administrator Sekolah atau Waka Kurikulum untuk meminta akses atau mereset password Anda.
                    </p>
                    
                    <a href="https://wa.me/6281234567890?text=Halo%20Admin,%20saya%20butuh%20bantuan%20terkait%20akun%20JurnalKita%20saya." target="_blank" class="btn btn-success w-100 fw-semibold rounded-3 py-2">
                       <i class="bi bi-whatsapp me-2"></i> Hubungi Admin via WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (function(){
            const pwd = document.getElementById('passwordField');
            const toggle = document.getElementById('togglePassword');
            
            if(toggle && pwd){
                toggle.addEventListener('click', function(){
                    const isPassword = pwd.type === 'password';
                    pwd.type = isPassword ? 'text' : 'password';
                    this.classList.toggle('bi-eye');
                    this.classList.toggle('bi-eye-slash');
                });
            }
        })();
    </script>
</body>
</html>