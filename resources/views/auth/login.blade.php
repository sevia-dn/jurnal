<!doctype html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Sign In - JurnalKita</title>
    
    <!-- Google Fonts Inter & Bootstrap Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Tailwind CSS (via Vite atau CDN CDN CDN) -->

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = {
        theme: {
          extend: {
            fontFamily: {
              sans: ['Inter', 'sans-serif'],
            },
            colors: {
              'jk-green': '#155d50',
              'jk-btn': '#5fc29b',
              'jk-dark': '#0b2b24',
            }
          }
        }
      }
    </script>
</head>
<body class="h-full font-sans antialiased bg-white text-slate-800">

    <div class="min-h-screen grid grid-cols-1 md:grid-cols-2">
        
<!-- KOLOM KIRI: BRAND AREA (Sembunyi di Mobile, Muncul di MD ke Atas) -->
<div class="hidden md:flex flex-col bg-jk-green text-white p-8 lg:p-12 relative overflow-hidden h-screen">
    
    <!-- Bagian Atas: Logo & Teks -->
    <div class="z-10">
        <!-- Logo Top Kiri -->
        <div class="flex items-center gap-3 mb-8 lg:mb-12">
            <div class="w-11 h-11 bg-jk-btn text-jk-dark rounded-xl flex items-center justify-center text-lg shadow-sm">
                <i class="bi bi-mortarboard-fill"></i>
            </div>
            <span class="font-bold text-xl tracking-tight text-white">JurnalKita</span>
        </div>

        <!-- Konten Utama Brand -->
        <div class="max-w-lg">
            <h1 class="text-3xl lg:text-4xl font-extrabold tracking-tight mb-4 text-white">
                JURNAL ESEMKITA
            </h1>
            <p class="text-white/80 text-sm lg:text-base leading-relaxed">
                The unified portal for academic tracking, lesson plans, and seamless school administrative management.
            </p>
        </div>
    </div>

    <!-- Bagian Tengah/Bawah: Ilustrasi -->
    <!-- flex-1 akan mengambil seluruh sisa ruang ke bawah, lalu justify-center & items-center menaruh gambar persis di tengah ruang tersebut -->
    <div class="flex-1 flex justify-center items-center z-10 w-full mt-4">
        <!-- Ukuran gambar dibesarkan menggunakan max-w-md atau lg:max-w-[80%] -->
        <img src="{{ asset('img/hero-illustration.png') }}" alt="Ilustrasi JurnalKita" class="max-w-sm lg:max-w-md xl:max-w-[80%] w-full object-contain">
    </div>

</div>

        
        <!-- KOLOM KANAN: LOGIN AREA (Responsive Desktop & Mobile) -->
        <div class="flex flex-col justify-center items-center p-6 lg:p-12 relative min-h-screen md:min-h-0 bg-white">
            
            <!-- Logo Khusus Tampilan Mobile (Sembunyi di Desktop) -->
            <div class="flex md:hidden items-center gap-3 mb-8">
                <div class="w-11 h-11 bg-jk-btn text-jk-dark rounded-xl flex items-center justify-center text-lg shadow-sm">
                    <i class="bi bi-mortarboard-fill"></i>
                </div>
                <span class="font-bold text-2xl text-jk-green tracking-tight">JurnalKita</span>
            </div>

            <!-- Kartu Form Login -->
            <div class="w-full max-w-[420px] bg-white rounded-2xl shadow-[0_10px_40px_rgba(19,43,40,0.08)] p-8 border border-slate-100">
                
                <h2 class="text-2xl font-bold text-jk-dark mb-1">Sign In</h2>
                <p class="text-slate-500 text-sm mb-5">Selamat Datang di Portal JurnalKita.</p>

                @if($errors->any())
                    <div class="mb-4 p-3.5 bg-red-50 border border-red-200 rounded-xl text-xs text-red-700 font-medium flex items-center gap-2">
                        <i class="bi bi-exclamation-circle-fill text-red-500 text-sm shrink-0"></i>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                <form action="{{ route('login.post') }}" method="POST">
                    @csrf
                    
                    <!-- Input Username -->
                    <div class="mb-4">
                        <label class="block text-xs font-semibold text-slate-700 mb-2">NIP / Username</label>
                        <div class="relative">
                            <i class="bi bi-person absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-base pointer-events-none"></i>
                            <input 
                                name="login"
                                type="text" 
                                value="{{ old('login') }}"
                                class="w-full pl-10 pr-4 py-2.5 text-sm rounded-lg border border-slate-200 focus:outline-none focus:border-jk-btn focus:ring-2 focus:ring-jk-btn/25 transition placeholder:text-slate-400" 
                                placeholder="Masukkan NIP atau Username"
                                required
                                autofocus
                            >
                        </div>
                    </div>

                    <!-- Input Password -->
                    <div class="mb-2">
                        <label class="block text-xs font-semibold text-slate-700 mb-2">Password</label>
                        <div class="relative">
                            <i class="bi bi-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-base pointer-events-none"></i>
                            <input 
                                id="passwordField" 
                                name="password"
                                type="password" 
                                class="w-full pl-10 pr-10 py-2.5 text-sm rounded-lg border border-slate-200 focus:outline-none focus:border-jk-btn focus:ring-2 focus:ring-jk-btn/25 transition placeholder:text-slate-400" 
                                placeholder="Masukkan password"
                                required
                            >
                            <i id="togglePassword" class="bi bi-eye absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-base cursor-pointer hover:text-slate-600 transition" title="Show / hide password"></i>
                        </div>
                    </div>

                    <!-- Lupa Password Link -->
                    <div class="flex justify-end mb-6">
                        <button type="button" onclick="toggleModal(true)" class="text-xs font-medium text-slate-500 hover:text-jk-green transition">
                            Lupa Password ?
                        </button>
                    </div>

                    <!-- Tombol Sign In -->
                    <button type="submit" class="w-full py-3 bg-jk-btn hover:bg-jk-green text-white font-semibold text-sm rounded-lg shadow-sm transition duration-200 cursor-pointer">
                        Sign In
                    </button>
                </form>

                <!-- Contact Admin -->
                <p class="text-center text-xs text-slate-500 mt-6">
                    Belum punya akun ? 
                    <button type="button" onclick="toggleModal(true)" class="font-bold text-jk-green hover:underline ml-0.5">
                        Contact Admin.
                    </button>
                </p>

            </div>

            <!-- Copyright Text (Absolute di Desktop & Mobile) -->
        <div class="absolute bottom-6 left-0 w-full text-center text-xs text-slate-400">
            © 2026 JurnalKita Management System. All rights reserved.
        </div>

    </div>
</div> <!-- Penutup grid container -->

    <!-- MODAL BANTUAN ADMIN & LUPA PASSWORD (Tailwind Backdrop & Dialog) -->
    <div id="adminHelpModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs z-50 flex items-center justify-center p-4 opacity-0 pointer-events-none transition-opacity duration-200">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl relative transform scale-95 transition-transform duration-200" id="modalCard">
            
            <div class="flex justify-between items-center mb-4 pb-2 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <i class="bi bi-key-fill text-jk-green text-lg"></i>
                    <h3 class="text-base font-bold text-slate-900">Bantuan Akses & Reset Password</h3>
                </div>
                <button type="button" onclick="toggleModal(false)" class="text-slate-400 hover:text-slate-600 p-1 cursor-pointer">
                    <i class="bi bi-x-lg text-lg"></i>
                </button>
            </div>

            @if(session('success_reset'))
                <div class="mb-4 p-3 bg-emerald-50 border border-emerald-200 rounded-xl text-xs text-emerald-800 font-medium flex items-center gap-2">
                    <i class="bi bi-check-circle-fill text-emerald-600 text-sm shrink-0"></i>
                    <span>{{ session('success_reset') }}</span>
                </div>
            @endif

            @if(session('error_reset'))
                <div class="mb-4 p-3 bg-rose-50 border border-rose-200 rounded-xl text-xs text-rose-800 font-medium flex items-center gap-2">
                    <i class="bi bi-exclamation-circle-fill text-rose-600 text-sm shrink-0"></i>
                    <span>{{ session('error_reset') }}</span>
                </div>
            @endif

            @if(session('info_reset'))
                <div class="mb-4 p-3 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-800 font-medium flex items-center gap-2">
                    <i class="bi bi-info-circle-fill text-amber-600 text-sm shrink-0"></i>
                    <span>{{ session('info_reset') }}</span>
                </div>
            @endif

            <!-- Tab Switcher Modal -->
            <div class="flex gap-2 mb-4 bg-slate-100 p-1 rounded-xl text-xs font-semibold text-slate-600">
                <button type="button" onclick="switchHelpTab('form')" id="tabBtnForm" class="flex-1 py-1.5 rounded-lg bg-white text-jk-green font-bold shadow-xs transition cursor-pointer">
                    Ajukan Reset ke Admin
                </button>
                <button type="button" onclick="switchHelpTab('wa')" id="tabBtnWa" class="flex-1 py-1.5 rounded-lg text-slate-500 hover:text-slate-800 transition cursor-pointer">
                    Kontak WhatsApp
                </button>
            </div>

            <!-- Tab 1: Form Ajukan Reset Password ke Admin -->
            <div id="tabContentForm">
                <form action="{{ route('laporan-pw.kirim') }}" method="POST" class="space-y-3.5">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">
                            NIP / Username Anda <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <i class="bi bi-person absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                            <input 
                                type="text" 
                                name="login" 
                                required
                                placeholder="Contoh: guru_honorer / piket / NIP" 
                                class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:border-jk-btn focus:ring-2 focus:ring-jk-btn/20 transition"
                            >
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">
                            Alasan Permintaan Reset <span class="text-slate-400 font-normal">(Opsional)</span>
                        </label>
                        <textarea 
                            name="alasan" 
                            rows="2" 
                            placeholder="Contoh: Lupa password lama setelah ganti HP..."
                            class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:border-jk-btn focus:ring-2 focus:ring-jk-btn/20 transition resize-none"
                        ></textarea>
                    </div>

                    <p class="text-[11px] text-slate-500 leading-relaxed bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                        <i class="bi bi-info-circle text-jk-green"></i> 
                        Permintaan Anda akan langsung tampil di menu notifikasi Admin. Admin akan mereset dan memberikan password baru Anda.
                    </p>

                    <button type="submit" class="w-full py-2.5 bg-jk-btn hover:bg-jk-green text-white font-bold text-xs rounded-xl shadow-xs transition duration-200 cursor-pointer flex items-center justify-center gap-1.5">
                        <i class="bi bi-send-fill"></i> Kirim Laporan ke Admin
                    </button>
                </form>
            </div>

            <!-- Tab 2: WhatsApp Info -->
            <div id="tabContentWa" class="hidden text-center pb-2">
                <div class="w-14 h-14 bg-emerald-50 text-jk-green rounded-full flex items-center justify-center text-2xl mx-auto mb-3">
                    <i class="bi bi-whatsapp"></i>
                </div>
                
                <p class="text-xs text-slate-700 font-medium mb-1">
                    Hubungi Administrator Sekolah secara langsung melalui WhatsApp untuk bantuan darurat.
                </p>
                <p class="text-[11px] text-slate-400 mb-4">
                    Jam layanan operasional: Senin - Jumat (07:00 - 16:00 WIB)
                </p>
                
                <a href="https://wa.me/6281234567890?text=Halo%20Admin,%20saya%20butuh%20bantuan%20terkait%20reset%20password%20akun%20JurnalKita%20saya." target="_blank" class="flex items-center justify-center gap-2 w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs rounded-xl py-2.5 shadow-sm transition">
                    <i class="bi bi-whatsapp text-sm"></i> Buka Chat WhatsApp Admin
                </a>
            </div>

        </div>
    </div>

    <!-- JavaScript Vanilla untuk Toggle Password & Modal -->
    <script>
        // Toggle Show/Hide Password
        const pwd = document.getElementById('passwordField');
        const toggle = document.getElementById('togglePassword');
        
        if (toggle && pwd) {
            toggle.addEventListener('click', function() {
                const isPassword = pwd.type === 'password';
                pwd.type = isPassword ? 'text' : 'password';
                this.classList.toggle('bi-eye');
                this.classList.toggle('bi-eye-slash');
            });
        }

        // Toggle Modal Function
        function toggleModal(show) {
            const modal = document.getElementById('adminHelpModal');
            const card = document.getElementById('modalCard');
            if (show) {
                modal.classList.remove('opacity-0', 'pointer-events-none');
                card.classList.remove('scale-95');
                card.classList.add('scale-100');
            } else {
                modal.classList.add('opacity-0', 'pointer-events-none');
                card.classList.remove('scale-100');
                card.classList.add('scale-95');
            }
        }

        // Switch Help Tab
        function switchHelpTab(tab) {
            const tabForm = document.getElementById('tabContentForm');
            const tabWa = document.getElementById('tabContentWa');
            const btnForm = document.getElementById('tabBtnForm');
            const btnWa = document.getElementById('tabBtnWa');

            if (tab === 'form') {
                tabForm.classList.remove('hidden');
                tabWa.classList.add('hidden');
                btnForm.classList.add('bg-white', 'text-jk-green', 'font-bold', 'shadow-xs');
                btnForm.classList.remove('text-slate-500');
                btnWa.classList.remove('bg-white', 'text-jk-green', 'font-bold', 'shadow-xs');
                btnWa.classList.add('text-slate-500');
            } else {
                tabForm.classList.add('hidden');
                tabWa.classList.remove('hidden');
                btnWa.classList.add('bg-white', 'text-jk-green', 'font-bold', 'shadow-xs');
                btnWa.classList.remove('text-slate-500');
                btnForm.classList.remove('bg-white', 'text-jk-green', 'font-bold', 'shadow-xs');
                btnForm.classList.add('text-slate-500');
            }
        }

        @if(session('open_reset_modal') || session('success_reset') || session('error_reset') || session('info_reset'))
            document.addEventListener('DOMContentLoaded', function() {
                toggleModal(true);
            });
        @endif
    </script>
</body>
</html>