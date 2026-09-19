<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'JurnalKita')</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: { fontFamily: { sans: ['Inter', 'sans-serif'] } }
            }
        }
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-50 text-slate-800 font-sans antialiased flex h-screen overflow-hidden">

    @hasSection('sidebar')
    <aside class="w-64 h-full shrink-0 z-20">
        @yield('sidebar')
    </aside>
    @endif

    <div class="flex-1 flex flex-col h-full w-full overflow-hidden relative">
        
        @hasSection('navbar')
        <header class="shrink-0 w-full z-10 bg-white">
            @yield('navbar')
        </header>
        @endif

        <main class="flex-1 overflow-y-auto">
            @yield('content')
        </main>

    </div>

    {{-- Global: Matikan popup riwayat autocomplete browser di seluruh form dan input --}}
    <script>
        (function() {
            function disableBrowserAutocomplete() {
                document.querySelectorAll('form').forEach(function(f) {
                    if (f.getAttribute('autocomplete') !== 'off') {
                        f.setAttribute('autocomplete', 'off');
                    }
                });
                document.querySelectorAll('input:not([type="hidden"]):not([type="checkbox"]):not([type="radio"])').forEach(function(inp) {
                    if (inp.getAttribute('autocomplete') !== 'off' && inp.getAttribute('autocomplete') !== 'new-password') {
                        inp.setAttribute('autocomplete', 'off');
                    }
                });
            }

            disableBrowserAutocomplete();
            document.addEventListener('DOMContentLoaded', disableBrowserAutocomplete);
            ['focusin', 'pointerdown', 'mousedown'].forEach(function(evt) {
                document.addEventListener(evt, function(e) {
                    if (e.target && e.target.tagName === 'INPUT' && e.target.type !== 'checkbox' && e.target.type !== 'radio' && e.target.type !== 'hidden') {
                        if (e.target.getAttribute('autocomplete') !== 'off' && e.target.getAttribute('autocomplete') !== 'new-password') {
                            e.target.setAttribute('autocomplete', 'off');
                        }
                    }
                }, true);
            });

            if (window.MutationObserver) {
                new MutationObserver(disableBrowserAutocomplete).observe(document.documentElement, {
                    childList: true,
                    subtree: true
                });
            }
        })();
    </script>
</body>
</html>