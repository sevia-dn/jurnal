@extends('layouts.app')

@section('content')
<div class="w-full max-w-7xl mx-auto p-4 md:p-6 font-sans">
    <!-- Welcome Section -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl md:text-3xl font-extrabold text-[#0d6e59] leading-tight">Selamat Datang,<br class="md:hidden"> Sekretaris</h2>
            <p class="text-xs md:text-sm text-[#5e7e75] font-medium mt-1">{{ now()->translatedFormat('l, d F Y') }}</p>
        </div>

        <!-- Tombol Lonceng Notifikasi -->
        <a href="{{ route('sekretaris.notifikasi') }}" class="relative p-2.5 md:p-3 rounded-2xl bg-white text-[#0d6e59] border border-[#d6ebe3] hover:bg-[#eaf6f2] shadow-2xs hover:shadow-sm transition flex items-center justify-center !no-underline" title="Notifikasi Sekretaris">
            <i class="bi bi-bell text-xl"></i>
            @if(($unreadCount ?? 0) > 0)
                <span class="absolute -top-1.5 -right-1.5 bg-red-500 text-white text-[10px] font-extrabold w-5 h-5 rounded-full flex items-center justify-center shadow-xs ring-2 ring-white">
                    {{ ($unreadCount > 9) ? '9+' : $unreadCount }}
                </span>
            @endif
        </a>
    </div>

    <!-- 4 Card Ringkasan Statistik -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- Card 1: Kelas Hari Ini (Diubah jadi 10 Sesi & Bisa Diklik) -->
        <a href="{{ route('sekretaris.jadwal') }}" class="bg-[#eaf6f2] p-5 rounded-2xl flex justify-between items-start border border-[#d6ebe3] shadow-2xs hover:shadow-sm hover:border-[#0d6e59] transition group !no-underline">
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
        <a href="{{ route('sekretaris.notifikasi', ['tab' => 'unread']) }}" class="bg-[#f7f6e6] p-5 rounded-2xl flex justify-between items-start border border-[#eae8cb] shadow-2xs hover:shadow-sm transition group !no-underline">
            <div>
                <h3 class="font-bold text-[#0f3d32] text-sm md:text-base group-hover:text-[#807200]">Perlu Validasi</h3>
                <p class="text-4xl md:text-5xl font-extrabold text-[#807200] mt-6">{{ $perluPersetujuan ?? 3 }}</p>
                <p class="text-xs text-[#5e7e75] mt-1 flex items-center gap-1">
                    Jurnal menunggu validasi <i class="bi bi-arrow-right"></i>
                </p>
            </div>
            <div class="bg-[#eeeab8] p-2.5 rounded-xl text-[#807200]">
                <i class="bi bi-clipboard-check text-lg"></i>
            </div>
        </a>

        <!-- Card 3: Kehadiran Guru -->
        <div class="bg-[#eaf6f2] p-5 rounded-2xl flex justify-between items-start border border-[#d6ebe3] shadow-2xs">
            <div>
                <h3 class="font-bold text-[#0f3d32] text-sm md:text-base">Kehadiran Guru</h3>
                <p class="text-4xl md:text-5xl font-extrabold text-[#0d6e59] mt-6">
                    {{ ($kehadiranGuruHadir ?? 8) }}/{{ ($kehadiranGuruTotal ?? 10) > 0 ? ($kehadiranGuruTotal ?? 10) : 10 }}
                </p>
                <p class="text-xs text-[#5e7e75] mt-1">Guru mengajar hari ini</p>
            </div>
            <div class="bg-[#d2ebe2] p-2.5 rounded-xl text-[#0d6e59]">
                <i class="bi bi-person-badge-fill text-lg"></i>
            </div>
        </div>

        <!-- Card 4: Kehadiran Siswa -->
        <div class="bg-[#eaf6f2] p-5 rounded-2xl flex justify-between items-start border border-[#d6ebe3] shadow-2xs">
            <div>
                <h3 class="font-bold text-[#0f3d32] text-sm md:text-base">Kehadiran Siswa</h3>
                <p class="text-4xl md:text-5xl font-extrabold text-[#0d6e59] mt-6">
                    {{ ($kehadiranSiswaHadir ?? 32) }}/{{ ($kehadiranSiswaTotal ?? 36) > 0 ? ($kehadiranSiswaTotal ?? 36) : 36 }}
                </p>
                <p class="text-xs text-[#5e7e75] mt-1">Siswa hadir dalam sesi</p>
            </div>
            <div class="bg-[#d2ebe2] p-2.5 rounded-xl text-[#0d6e59]">
                <i class="bi bi-people-fill text-lg"></i>
            </div>
        </div>

    </div>

    <!-- Quick Action Banner -->
    <div class="mt-8 bg-gradient-to-r from-[#0d6e59] to-[#155d50] rounded-2xl p-6 text-white flex flex-col md:flex-row items-start md:items-center justify-between gap-4 shadow-sm">
        <div>
            <span class="bg-white/20 text-white text-[10px] font-bold px-2.5 py-0.5 rounded-md uppercase tracking-wider">Aktivitas Cepat</span>
            <h3 class="text-lg md:text-xl font-bold mt-1">Isi Jurnal Pembelajaran Hari Ini</h3>
            <p class="text-xs md:text-sm text-emerald-100 mt-0.5">Catat kehadiran guru pengampu dan rincian materi sesi kelas Anda secara berkala.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('sekretaris.notifikasi') }}" class="bg-white/10 hover:bg-white/20 text-white text-xs font-bold px-4 py-2.5 rounded-xl border border-white/20 transition !no-underline flex items-center gap-1.5">
                <i class="bi bi-bell"></i>
                <span>Cek Notifikasi</span>
            </a>
            <a href="{{ route('sekretaris.jurnal.create') }}" class="bg-white hover:bg-emerald-50 text-[#0d6e59] text-xs font-bold px-5 py-2.5 rounded-xl shadow-xs transition !no-underline flex items-center gap-1.5">
                <i class="bi bi-pencil-square"></i>
                <span>Input Presensi</span>
            </a>
        </div>
    </div>
</div>
@endsection
