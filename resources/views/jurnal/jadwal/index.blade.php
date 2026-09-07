@extends('layouts.app')

@section('content')
<!-- Container Utama Responsive -->
<div class="w-full max-w-xl md:max-w-3xl lg:max-w-4xl mx-auto bg-[#f4faf7] min-h-screen p-4 md:p-8 pb-20 font-sans text-gray-800">

    <!-- Tombol Kembali -->
    <a href="{{ route('sekretaris.jurnal.index') }}" class="inline-flex items-center gap-1.5 text-xs md:text-sm font-bold text-[#0d6e59] mb-4 hover:underline">
        <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
    </a>

    <!-- Header Halaman -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h2 class="text-xl md:text-2xl lg:text-3xl font-extrabold text-[#0f3d32]">Jadwal & 10 Sesi Kelas Hari Ini</h2>
            <p class="text-xs md:text-sm text-[#5e7e75] mt-0.5">Daftar lengkap urutan jam/sesi pelajaran untuk kelas XI RPL.</p>
        </div>
        <span class="text-xs md:text-sm font-bold bg-[#e4f3ed] text-[#0d6e59] px-3.5 py-1.5 rounded-xl self-start sm:self-auto shrink-0 shadow-sm">
            <i class="bi bi-calendar-event mr-1"></i> Kamis, 24 Agustus
        </span>
    </div>

    <!-- List 10 Sesi Pelajaran -->
    <div class="space-y-3">

        <!-- Sesi 1 & 2 -->
        <div class="bg-white p-3.5 md:p-4 rounded-2xl border-l-4 border-[#0d6e59] shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-start sm:items-center gap-3">
                <span class="bg-[#e4f3ed] text-[#0d6e59] font-extrabold text-xs md:text-sm px-2.5 py-1.5 md:px-3 md:py-2 rounded-xl shrink-0">
                    Jam 1-2
                </span>
                <div>
                    <h4 class="text-xs md:text-sm font-bold text-[#0f3d32]">Upacara / Pembinaan Wali Kelas</h4>
                    <p class="text-[11px] md:text-xs text-gray-500 mt-0.5">07.00 – 08.30 WIB • Lapangan Utama</p>
                </div>
            </div>
            <span class="self-end sm:self-center text-[10px] md:text-xs font-bold bg-gray-100 text-gray-600 px-2.5 py-1 rounded-lg shrink-0">
                Selesai
            </span>
        </div>

        <!-- Sesi 3 & 4 -->
        <div class="bg-white p-3.5 md:p-4 rounded-2xl border-l-4 border-[#0d6e59] shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-start sm:items-center gap-3">
                <span class="bg-[#e4f3ed] text-[#0d6e59] font-extrabold text-xs md:text-sm px-2.5 py-1.5 md:px-3 md:py-2 rounded-xl shrink-0">
                    Jam 3-4
                </span>
                <div>
                    <h4 class="text-xs md:text-sm font-bold text-[#0f3d32]">Pemrograman Web</h4>
                    <p class="text-[11px] md:text-xs text-gray-500 mt-0.5">08.30 – 10.00 WIB • Lab RPL 2 • Bpk. Aris, S.Kom</p>
                </div>
            </div>
            <span class="self-end sm:self-center text-[10px] md:text-xs font-bold bg-[#e4f3ed] text-[#0d6e59] px-2.5 py-1 rounded-lg shrink-0">
                Berlangsung
            </span>
        </div>

        <!-- Istirahat Pertama -->
        <div class="bg-[#f8faf9] p-2.5 md:p-3 rounded-xl border border-dashed border-gray-300 text-center text-xs md:text-sm text-gray-500 font-medium">
            <i class="bi bi-cup-hot mr-1"></i> Istirahat I (10.00 – 10.15 WIB)
        </div>

        <!-- Sesi 5 & 6 -->
        <div class="bg-white p-3.5 md:p-4 rounded-2xl border-l-4 border-gray-300 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-start sm:items-center gap-3">
                <span class="bg-gray-100 text-gray-600 font-extrabold text-xs md:text-sm px-2.5 py-1.5 md:px-3 md:py-2 rounded-xl shrink-0">
                    Jam 5-6
                </span>
                <div>
                    <h4 class="text-xs md:text-sm font-bold text-[#0f3d32]">Basis Data</h4>
                    <p class="text-[11px] md:text-xs text-gray-500 mt-0.5">10.15 – 11.45 WIB • Lab RPL 1 • Ibu Nurul, M.T</p>
                </div>
            </div>
            <span class="self-end sm:self-center text-[10px] md:text-xs font-bold bg-gray-100 text-gray-500 px-2.5 py-1 rounded-lg shrink-0">
                Belum Mulai
            </span>
        </div>

        <!-- Istirahat Kedua / ISHOMA -->
        <div class="bg-[#f8faf9] p-2.5 md:p-3 rounded-xl border border-dashed border-gray-300 text-center text-xs md:text-sm text-gray-500 font-medium">
            <i class="bi bi-moon-stars mr-1"></i> Istirahat II / ISHOMA (11.45 – 12.30 WIB)
        </div>

        <!-- Sesi 7 & 8 -->
        <div class="bg-white p-3.5 md:p-4 rounded-2xl border-l-4 border-gray-300 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-start sm:items-center gap-3">
                <span class="bg-gray-100 text-gray-600 font-extrabold text-xs md:text-sm px-2.5 py-1.5 md:px-3 md:py-2 rounded-xl shrink-0">
                    Jam 7-8
                </span>
                <div>
                    <h4 class="text-xs md:text-sm font-bold text-[#0f3d32]">Matematika</h4>
                    <p class="text-[11px] md:text-xs text-gray-500 mt-0.5">12.30 – 14.00 WIB • Ruang Teori XI RPL • Bpk. Budi, S.Pd</p>
                </div>
            </div>
            <span class="self-end sm:self-center text-[10px] md:text-xs font-bold bg-gray-100 text-gray-500 px-2.5 py-1 rounded-lg shrink-0">
                Belum Mulai
            </span>
        </div>

        <!-- Sesi 9 & 10 -->
        <div class="bg-white p-3.5 md:p-4 rounded-2xl border-l-4 border-gray-300 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-start sm:items-center gap-3">
                <span class="bg-gray-100 text-gray-600 font-extrabold text-xs md:text-sm px-2.5 py-1.5 md:px-3 md:py-2 rounded-xl shrink-0">
                    Jam 9-10
                </span>
                <div>
                    <h4 class="text-xs md:text-sm font-bold text-[#0f3d32]">Bahasa Inggris</h4>
                    <p class="text-[11px] md:text-xs text-gray-500 mt-0.5">14.00 – 15.30 WIB • Ruang Teori XI RPL • Ibu Dian, M.Pd</p>
                </div>
            </div>
            <span class="self-end sm:self-center text-[10px] md:text-xs font-bold bg-gray-100 text-gray-500 px-2.5 py-1 rounded-lg shrink-0">
                Belum Mulai
            </span>
        </div>

    </div>

</div>
@endsection