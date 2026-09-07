@extends('layouts.app')

@section('content')
<div class="w-full max-w-xl md:max-w-3xl lg:max-w-5xl mx-auto bg-[#f4faf7] min-h-screen p-4 md:p-8 pb-28 font-sans text-gray-800">

    <!-- Header UI -->
    <div class="flex justify-between items-center mb-4">
        <div class="flex items-center gap-3">
            <a href="#" class="bg-white text-[#0d6e59] p-2 rounded-xl shadow-sm hover:bg-[#e4f3ed] transition md:hidden">
                <i class="bi bi-arrow-left text-lg"></i>
            </a>
            <h1 class="text-xl md:text-2xl font-bold text-[#0d6e59]">Notifikasi Sekretariat</h1>
        </div>
        <button type="button" class="text-xs md:text-sm font-semibold text-[#0d6e59] hover:underline">
            Tandai Semua Dibaca
        </button>
    </div>

    <!-- Filter Tab (Responsive Scrollable) -->
    <div class="flex gap-2 overflow-x-auto pb-2 mb-4 scrollbar-none">
        <button class="bg-[#0d6e59] text-white text-xs md:text-sm font-semibold px-4 py-2 rounded-xl whitespace-nowrap shadow-sm">
            Semua (5)
        </button>
        <button class="bg-white text-gray-600 hover:bg-gray-50 text-xs md:text-sm font-semibold px-4 py-2 rounded-xl whitespace-nowrap border border-gray-100 shadow-sm">
            Belum Dibaca (2)
        </button>
        <button class="bg-white text-gray-600 hover:bg-gray-50 text-xs md:text-sm font-semibold px-4 py-2 rounded-xl whitespace-nowrap border border-gray-100 shadow-sm">
            Izin & Sakit (3)
        </button>
    </div>

    <!-- List Notifikasi Container -->
    <div class="space-y-3">

        <!-- Card Notifikasi 1 (Unread) -->
        <div class="bg-white p-4 rounded-2xl border-l-4 border-[#0d6e59] shadow-sm hover:shadow-md transition">
            <div class="flex items-start gap-3 md:gap-4">
                <div class="bg-amber-100 text-amber-600 p-2.5 md:p-3 rounded-xl shrink-0 text-lg md:text-xl">
                    <i class="bi bi-exclamation-circle-fill"></i>
                </div>
                <div class="flex-1">
                    <div class="flex justify-between items-start gap-2 mb-1">
                        <h4 class="text-xs md:text-sm font-bold text-gray-800">Keterangan Izin Guru</h4>
                        <span class="text-[10px] md:text-xs text-gray-400 whitespace-nowrap">10 menit yang lalu</span>
                    </div>
                    <p class="text-xs md:text-sm text-gray-600 leading-relaxed mb-2">
                        <span class="font-semibold text-gray-800">Dewi Anjani, S.Pd</span> mengajukan izin tidak mengajar di kelas <span class="font-semibold text-gray-800">XI RPL 2</span> karena workshop MGMP.
                    </p>
                    <div class="flex gap-2">
                        <button class="bg-[#0d6e59] text-white text-[11px] md:text-xs font-semibold px-3 py-1 rounded-lg hover:bg-[#095243] transition">
                            Lihat Detail
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Notifikasi 2 (Unread) -->
        <div class="bg-white p-4 rounded-2xl border-l-4 border-[#3db892] shadow-sm hover:shadow-md transition">
            <div class="flex items-start gap-3 md:gap-4">
                <div class="bg-[#e4f3ed] text-[#0d6e59] p-2.5 md:p-3 rounded-xl shrink-0 text-lg md:text-xl">
                    <i class="bi bi-journal-check"></i>
                </div>
                <div class="flex-1">
                    <div class="flex justify-between items-start gap-2 mb-1">
                        <h4 class="text-xs md:text-sm font-bold text-gray-800">Logbook Dikirim</h4>
                        <span class="text-[10px] md:text-xs text-gray-400 whitespace-nowrap">45 menit yang lalu</span>
                    </div>
                    <p class="text-xs md:text-sm text-gray-600 leading-relaxed">
                        <span class="font-semibold text-gray-800">Yusuf Hidayat, S.Kom</span> telah mengisikan jurnal mengajar kelas <span class="font-semibold text-gray-800">XI RPL 1</span>.
                    </p>
                </div>
            </div>
        </div>

        <!-- Card Notifikasi 3 (Read) -->
        <div class="bg-white/70 p-4 rounded-2xl border-l-4 border-gray-300 shadow-sm">
            <div class="flex items-start gap-3 md:gap-4">
                <div class="bg-red-50 text-red-500 p-2.5 md:p-3 rounded-xl shrink-0 text-lg md:text-xl">
                    <i class="bi bi-person-x-fill"></i>
                </div>
                <div class="flex-1">
                    <div class="flex justify-between items-start gap-2 mb-1">
                        <h4 class="text-xs md:text-sm font-bold text-gray-700">Guru Tidak Hadir (Sakit)</h4>
                        <span class="text-[10px] md:text-xs text-gray-400 whitespace-nowrap">2 jam yang lalu</span>
                    </div>
                    <p class="text-xs md:text-sm text-gray-500 leading-relaxed">
                        <span class="font-semibold text-gray-700">Bambang Wijaya, S.Pd</span> berhalangan hadir di kelas <span class="font-semibold text-gray-700">X RPL 2</span>. Tugas mandiri telah diberikan melalui guru piket.
                    </p>
                </div>
            </div>
        </div>

    </div>

    <!-- Bottom Navigation Bar Sekre -->
    <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-100 py-2.5 px-6 flex justify-around items-center max-w-xl md:max-w-3xl lg:max-w-5xl mx-auto shadow-lg z-50">
        <a href="#" class="flex flex-col items-center gap-0.5 text-gray-500 hover:text-[#0d6e59]">
            <i class="bi bi-grid text-sm md:text-base"></i>
            <span class="text-[10px] md:text-xs font-medium">Dashboard</span>
        </a>
        <a href="#" class="flex flex-col items-center gap-0.5 text-gray-500 hover:text-[#0d6e59]">
            <i class="bi bi-journal-text text-sm md:text-base"></i>
            <span class="text-[10px] md:text-xs font-medium">Rekap Jurnal</span>
        </a>
        <a href="#" class="flex flex-col items-center gap-0.5 bg-[#a3e3cd] text-[#0d6e59] px-5 py-1.5 rounded-2xl">
            <i class="bi bi-bell-fill text-sm md:text-base"></i>
            <span class="text-[10px] md:text-xs font-bold">Notifikasi</span>
        </a>
        <a href="#" class="flex flex-col items-center gap-0.5 text-gray-500 hover:text-[#0d6e59]">
            <i class="bi bi-person text-sm md:text-base"></i>
            <span class="text-[10px] md:text-xs font-medium">Profil</span>
        </a>
    </div>

</div>
@endsection