@extends('layouts.app')

@section('content')
<!-- Header Mobile (Hanya tampil di Layar HP) -->
<div class="flex md:hidden justify-between items-center mb-6">
    <div class="flex items-center gap-2">
        <div class="bg-[#3db892] text-white p-2 rounded-xl flex items-center justify-center">
            <i class="bi bi-mortarboard-fill text-xl"></i>
        </div>
        <h1 class="text-xl font-bold text-[#0d6e59]">JurnalKita</h1>
    </div>
    <button class="text-[#0d6e59] text-xl p-1 relative">
        <i class="bi bi-bell"></i>
    </button>
</div>

<!-- Welcome Section -->
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between">
    <div>
        <h2 class="text-2xl md:text-3xl font-extrabold text-[#0d6e59] leading-tight">Selamat Datang,<br class="md:hidden"> Sekretaris</h2>
        <p class="text-xs md:text-sm text-[#5e7e75] font-medium mt-1">Kamis, 24 Agustus 2023</p>
    </div>
    
</div>

<!-- Grid Utama Dashboard -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- Kolom Kiri: 4 Card Ringkasan Statistik (2 Kolom di Desktop) -->
    <div class="lg:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-4 h-fit">
        
        <!-- Card 1: Kelas Hari Ini -->
        <div class="bg-[#eaf6f2] p-5 rounded-2xl flex justify-between items-start border border-[#d6ebe3] shadow-sm">
            <div>
                <h3 class="font-bold text-[#0f3d32] text-sm md:text-base">Kelas Hari Ini</h3>
                <p class="text-4xl md:text-5xl font-extrabold text-[#0d6e59] mt-6">6</p>
                <p class="text-xs text-[#5e7e75] mt-1">Total sesi kelas</p>
            </div>
            <div class="bg-[#d2ebe2] p-2.5 rounded-xl text-[#0d6e59]">
                <i class="bi bi-bookmark-fill text-lg"></i>
            </div>
        </div>

        <!-- Card 2: Perlu Persetujuan -->
        <div class="bg-[#f7f6e6] p-5 rounded-2xl flex justify-between items-start border border-[#eae8cb] shadow-sm">
            <div>
                <h3 class="font-bold text-[#0f3d32] text-sm md:text-base">Perlu Persetujuan</h3>
                <p class="text-4xl md:text-5xl font-extrabold text-[#807200] mt-6">3</p>
                <p class="text-xs text-[#5e7e75] mt-1">Jurnal menunggu validasi</p>
            </div>
            <div class="bg-[#eeeab8] p-2.5 rounded-xl text-[#807200]">
                <i class="bi bi-clipboard-check text-lg"></i>
            </div>
        </div>

        <!-- Card 3: Kehadiran Guru -->
        <div class="bg-[#eaf6f2] p-5 rounded-2xl flex justify-between items-start border border-[#d6ebe3] shadow-sm">
            <div>
                <h3 class="font-bold text-[#0f3d32] text-sm md:text-base">Kehadiran Guru</h3>
                <p class="text-4xl md:text-5xl font-extrabold text-[#0d6e59] mt-6">3/4</p>
            </div>
            <div class="bg-[#d2ebe2] p-2.5 rounded-xl text-[#0d6e59]">
                <i class="bi bi-person-badge-fill text-lg"></i>
            </div>
        </div>

        <!-- Card 4: Kehadiran Siswa -->
        <div class="bg-[#eaf6f2] p-5 rounded-2xl flex justify-between items-start border border-[#d6ebe3] shadow-sm">
            <div>
                <h3 class="font-bold text-[#0f3d32] text-sm md:text-base">Kehadiran Siswa</h3>
                <p class="text-4xl md:text-5xl font-extrabold text-[#0d6e59] mt-6">30/36</p>
            </div>
            <div class="bg-[#d2ebe2] p-2.5 rounded-xl text-[#0d6e59]">
                <i class="bi bi-people-fill text-lg"></i>
            </div>
        </div>

    </div>

    <!-- Kolom Kanan: Widget Jadwal Hari Ini -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-3xl p-5 shadow-sm border-l-4 border-[#0d6e59]">
            <div class="flex justify-between items-center mb-5">
                <h3 class="font-extrabold text-lg text-[#0f3d32]">Jadwal Hari Ini</h3>
                <a href="#" class="text-xs font-semibold text-[#0d6e59] hover:underline">Lihat Semua</a>
            </div>

            <div class="space-y-4">
                <!-- Item 1: Matematika (Guru Hadir) -->
                <div class="bg-[#f4faf7] p-4 rounded-2xl border-l-4 border-[#0d6e59] border-t border-r border-b border-[#e1ede8]">
                    <div class="flex gap-3">
                        <div class="bg-[#e4f3ed] p-2.5 rounded-xl text-center flex flex-col justify-center min-w-[75px]">
                            <span class="text-sm font-bold text-[#0d6e59]">07:00</span>
                            <span class="text-[10px] text-gray-500">08:30</span>
                        </div>
                        <div class="flex-1">
                            <div class="flex gap-1.5 mb-1">
                                <span class="bg-[#0d6e59] text-white text-[10px] px-2 py-0.5 rounded-md font-medium">Matematika</span>
                                <span class="bg-[#dbece5] text-[#0d6e59] text-[10px] px-2 py-0.5 rounded-md font-medium">XII IPA 1</span>
                            </div>
                            <h4 class="font-bold text-xs md:text-sm text-[#0f3d32]">Bpk. Budi Santoso, S.Pd</h4>
                            <p class="text-[11px] text-gray-500 mt-1"><i class="bi bi-geo-alt-fill text-xs"></i> Ruang 101</p>
                        </div>
                    </div>
                    <button class="w-full mt-3 bg-[#0d6e59] text-white text-xs py-2.5 rounded-xl font-bold flex items-center justify-center gap-1.5 shadow-sm">
                        <i class="bi bi-check-circle-fill"></i> Guru Hadir
                    </button>
                </div>

                <!-- Item 2: Fisika (Absen) -->
                <div class="bg-[#f4faf7] p-4 rounded-2xl border-l-4 border-[#0d6e59] border-t border-r border-b border-[#e1ede8]">
                    <div class="flex gap-3">
                        <div class="bg-[#e4f3ed] p-2.5 rounded-xl text-center flex flex-col justify-center min-w-[75px]">
                            <span class="text-sm font-bold text-[#0d6e59]">08:30</span>
                            <span class="text-[10px] text-gray-500">10:00</span>
                        </div>
                        <div class="flex-1">
                            <div class="flex gap-1.5 mb-1">
                                <span class="bg-[#bca031] text-white text-[10px] px-2 py-0.5 rounded-md font-medium">Fisika</span>
                                <span class="bg-[#dbece5] text-[#0d6e59] text-[10px] px-2 py-0.5 rounded-md font-medium">XII IPA 1</span>
                            </div>
                            <h4 class="font-bold text-xs md:text-sm text-[#0f3d32]">Ibu Siti Aminah, M.Si</h4>
                            <p class="text-[11px] text-gray-500 mt-1"><i class="bi bi-geo-alt-fill text-xs"></i> Lab Fisika</p>
                        </div>
                    </div>
                    <button class="w-full mt-3 bg-white text-[#c53030] border border-[#c53030] text-xs py-2.5 rounded-xl font-bold flex items-center justify-center gap-1.5">
                        <i class="bi bi-exclamation-triangle-fill"></i> Absen
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection