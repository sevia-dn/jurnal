<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'JurnalKita')</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('img/logo-mark-64.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('img/apple-touch-icon.png') }}">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: { fontFamily: { sans: ['Inter', 'sans-serif'] } }
            }
        }

        function searchableSelect(selected, label, options) {
            return {
                selected: selected ? String(selected) : '',
                query: label || '',
                options,
                open: false,
                filteredOptions() {
                    const keyword = (this.query || '').toLowerCase().trim();
                    if (!keyword) return this.options;
                    const terms = keyword.split(/\s+/).filter(Boolean);
                    return this.options.filter((option) => {
                        const lbl = (option.label || '').toLowerCase();
                        return terms.every(term => lbl.includes(term));
                    });
                },
                choose(option) {
                    this.selected = option.value;
                    this.query = option.label;
                    this.open = false;
                    this.$nextTick(() => this.$refs.value.dispatchEvent(new Event('change', { bubbles: true })));
                },
                clear() {
                    this.selected = '';
                    this.query = '';
                    this.open = false;
                    this.$nextTick(() => this.$refs.value.dispatchEvent(new Event('change', { bubbles: true })));
                },
                setExternalValue(detail) {
                    if (!detail || this.$refs.value.id !== detail.id) {
                        return;
                    }

                    const option = this.options.find((item) => item.value === String(detail.value));
                    if (option) {
                        this.selected = option.value;
                        this.query = option.label;
                    } else {
                        this.selected = '';
                        this.query = '';
                    }
                },
            };
        }
    </script>
    <style>[x-cloak] { display: none !important; }</style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
</head>

<!-- PERBAIKAN 1: Ganti h-screen menjadi h-[100dvh] -->
<body class="bg-slate-50 text-slate-800 font-sans antialiased flex h-[100dvh] overflow-hidden">

    <aside class="hidden md:block w-64 h-full shrink-0 z-20">
        @yield('sidebar')
    </aside>

    <div class="flex-1 flex flex-col h-full w-full overflow-hidden relative">
        
        <header class="shrink-0 w-full z-10 bg-white">
            @yield('navbar')
        </header>

        <!-- PERBAIKAN 2: Tambahkan pb-20 md:pb-0 agar konten terbawah tidak tertutup navbar -->
        <main class="flex-1 overflow-y-auto pb-20 md:pb-0 relative z-0">
            @yield('content')
        </main>

    </div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('form[action*="logout"]').forEach(function (form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Keluar dari aplikasi?',
                    text: 'Apakah Anda yakin ingin keluar dari sesi ini?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#0D9488',
                    cancelButtonColor: '#64748B',
                    confirmButtonText: 'Ya, Keluar',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                }).then(function (result) {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>
</body>
</html>
