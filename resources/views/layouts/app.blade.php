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
</head>

<body class="bg-slate-50 text-slate-800 font-sans antialiased flex h-screen overflow-hidden">

    <aside class="w-64 h-full shrink-0 z-20">
        @yield('sidebar')
    </aside>

    <div class="flex-1 flex flex-col h-full w-full overflow-hidden relative">
        
        <header class="shrink-0 w-full z-10 bg-white">
            @yield('navbar')
        </header>

        <main class="flex-1 overflow-y-auto">
            @yield('content')
        </main>

    </div>

</body>
</html>