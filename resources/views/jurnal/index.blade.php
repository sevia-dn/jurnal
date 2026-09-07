@extends('layouts.app')

@section('content')
<!-- Welcome Section -->
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between">
    <div>
        <h2 class="text-2xl md:text-3xl font-extrabold text-[#0d6e59] leading-tight">Selamat Datang,<br class="md:hidden"> Sekretaris</h2>
        <p class="text-xs md:text-sm text-[#5e7e75] font-medium mt-1">Kamis, 24 Agustus 2023</p>
    </div>
</div>

<!-- 4 Card Ringkasan Statistik -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    
   <!-- Card 1: Kelas Hari Ini (Diubah jadi 10 Sesi & Bisa Diklik) -->
    <a href="{{ route('sekretaris.jadwal') }}" class="bg-[#eaf6f2] p-5 rounded-2xl flex justify-between items-start border border-[#d6ebe3] shadow-sm hover:border-[#0d6e59] transition group">
        <div>
            <h3 class="font-bold text-[#0f3d32] text-sm md:text-base group-hover:text-[#0d6e59]">Kelas Hari Ini</h3>
            <p class="text-4xl md:text-5xl font-extrabold text-[#0d6e59] mt-6">10</p>
            <p class="text-xs text-[#5e7e75] mt-1 flex items-center gap-1">
                Total sesi kelas <i class="bi bi-arrow-right"></i>
            </p>
        </div>
        <div class="bg-[#d2ebe2] p-2.5 rounded-xl text-[#0d6e59]">
            <i class="bi bi-calendar-week-fill text-lg"></i>
        </div>
    </a>

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
@endsection