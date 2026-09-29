<!doctype html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Sign In - JurnalKita</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('img/logo-mark-64.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('img/apple-touch-icon.png') }}">
    
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
              'jk-green': '#0D6B5A',
              'jk-btn': '#1BA886',
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
            <img src="{{ asset('img/logo-rounded.png') }}" alt="Logo JurnalKita" class="w-12 h-12 rounded-xl shadow-md border border-white/20 object-cover">
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
                <img src="{{ asset('img/logo-rounded.png') }}" alt="Logo JurnalKita" class="w-12 h-12 rounded-xl shadow-md border border-[#1BA886]/30 object-cover">
                <span class="font-bold text-2xl text-jk-green tracking-tight">JurnalKita</span>
            </div>

            <!-- Kartu Form Login -->
            <div class="w-full max-w-[420px] bg-white rounded-2xl shadow-[0_10px_40px_rgba(19,43,40,0.08)] p-8 border border-slate-100">
                
                <h2 class="text-2xl font-bold text-jk-dark mb-1">Sign In</h2>
                <p class="text-slate-500 text-sm mb-6">Selamat Datang.</p>

               <form action="{{ route('login') }}" method="POST">
                    @csrf
                    @if ($errors->any())
                        <div class="mb-4 p-3 bg-red-100 border border-red-300 text-red-700 text-xs rounded-lg">
                            {{ $errors->first() }}
                        </div>
                    @endif
                    
                    <!-- Input Username -->
                    <div class="mb-4">
                        <label class="block text-xs font-semibold text-slate-700 mb-2">NIP / Username</label>
                        <div class="relative">
                            <i class="bi bi-person absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-base pointer-events-none"></i>
                            <input 
                                type="text" 
                                name="identity"
                                value="{{ old('identity') }}"
                                class="w-full pl-10 pr-4 py-2.5 text-sm rounded-lg border border-slate-200 focus:outline-none focus:border-jk-btn focus:ring-2 focus:ring-jk-btn/25 transition placeholder:text-slate-400" 
                                placeholder="nip or username"
                                required
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
                                type="password" 
                                name="password"
                                class="w-full pl-10 pr-10 py-2.5 text-sm rounded-lg border border-slate-200 focus:outline-none focus:border-jk-btn focus:ring-2 focus:ring-jk-btn/25 transition placeholder:text-slate-400" 
                                placeholder="............"
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

<button type="submit" class="w-full py-3 bg-jk-green hover:bg-[#0a5547] text-white font-semibold text-sm rounded-lg shadow-sm transition duration-200">
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

            <div class="mt-5 flex flex-wrap justify-center gap-x-4 gap-y-2 text-xs">
                @if (\App\Models\Pengaturan::getValue('publik_riwayat_aktif', 1))
                    <a href="{{ route('public.jurnal') }}" class="font-semibold text-jk-green hover:underline"><i class="bi bi-clock-history mr-1"></i>Riwayat jurnal</a>
                @else
                    <span class="text-slate-400"><i class="bi bi-lock-fill mr-1"></i>Riwayat jurnal dikunci</span>
                @endif
            </div>

            <!-- Copyright Text (Absolute di Desktop & Mobile) -->
        <div class="absolute bottom-6 left-0 w-full text-center text-xs text-slate-400">
            © 2026 JurnalKita Management System. All rights reserved.
        </div>

    </div>
</div> <!-- Penutup grid container -->

    <!-- MODAL BANTUAN ADMIN & LUPA PASSWORD (Tailwind Backdrop & Dialog) -->
    <div id="adminHelpModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs z-50 flex items-center justify-center p-4 opacity-0 pointer-events-none transition-opacity duration-200">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl relative transform scale-95 transition-transform duration-200" id="modalCard">

            <div class="flex justify-between items-center mb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-emerald-50 text-[#0D6B5A] flex items-center justify-center text-lg shadow-2xs">
                        <i class="bi bi-shield-lock"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900 leading-tight">Lupa Kata Sandi</h3>
                        <p class="text-[11px] text-slate-500">Ajukan permohonan reset password ke Admin via WhatsApp</p>
                    </div>
                </div>
                <button type="button" onclick="toggleModal(false)" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">
                    <i class="bi bi-x-lg text-sm"></i>
                </button>
            </div>

            <form action="{{ route('password.request.submit') }}" method="POST" class="space-y-3 mt-3">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        NIP atau Username Anda <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <i class="bi bi-person absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                        <input
                            type="text"
                            name="identity"
                            id="modalIdentityInput"
                            required
                            placeholder="Contoh: 19800101... atau budisantoso"
                            class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:border-[#0D6B5A] focus:ring-2 focus:ring-[#0D6B5A]/15 transition placeholder:text-slate-400"
                        >
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Nomor WhatsApp Anda <span class="text-slate-400 font-normal">(Opsional)</span>
                    </label>
                    <div class="relative">
                        <i class="bi bi-whatsapp absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                        <input
                            type="tel"
                            name="no_hp"
                            placeholder="Contoh: 081234567890"
                            class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:border-[#0D6B5A] focus:ring-2 focus:ring-[#0D6B5A]/15 transition placeholder:text-slate-400"
                        >
                    </div>
                    <span class="text-[10px] text-slate-400 mt-0.5 block">Nomor kontak agar Admin dapat membalas dan mengirimkan password baru Anda.</span>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Keterangan Kendala <span class="text-slate-400 font-normal">(Opsional)</span>
                    </label>
                    <textarea
                        name="alasan"
                        rows="2"
                        placeholder="Contoh: Lupa password lama setelah logout / ganti perangkat..."
                        class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:border-[#0D6B5A] focus:ring-2 focus:ring-[#0D6B5A]/15 transition resize-none placeholder:text-slate-400"
                    ></textarea>
                </div>

                <button
                    type="submit"
                    class="w-full py-2.5 bg-[#0D6B5A] hover:bg-[#0a5547] text-white font-semibold text-xs rounded-xl shadow-xs transition flex items-center justify-center gap-2 cursor-pointer mt-1"
                >
                    <i class="bi bi-whatsapp text-sm"></i> Kirim Permohonan ke WhatsApp Admin
                </button>
            </form>

            <div class="mt-3 pt-3 border-t border-slate-100 text-center">
                <a
                    href="{{ $adminWaUrl ?? ('https://wa.me/6283838606396?text=' . urlencode('Halo Admin JurnalKita, saya butuh bantuan terkait lupa password akun saya.')) }}"
                    target="_blank"
                    class="inline-flex items-center gap-1.5 text-xs text-slate-500 hover:text-[#0D6B5A] transition font-medium"
                >
                    <i class="bi bi-chat-dots"></i> Langsung Chat WhatsApp Admin ({{ $adminWaNumber ?? '083838606396' }})
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
                // Auto-fill NIP/Username jika sudah diisi di form login
                const mainIdentity = document.querySelector('input[name="identity"]')?.value;
                const modalInput = document.getElementById('modalIdentityInput');
                if (mainIdentity && modalInput && !modalInput.value) {
                    modalInput.value = mainIdentity;
                }

                modal.classList.remove('opacity-0', 'pointer-events-none');
                card.classList.remove('scale-95');
                card.classList.add('scale-100');
            } else {
                modal.classList.add('opacity-0', 'pointer-events-none');
                card.classList.remove('scale-100');
                card.classList.add('scale-95');
            }
        }
    </script>
</body>
</html>
