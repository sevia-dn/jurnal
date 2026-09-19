@extends('layouts.app')

@section('content')
<!-- Container Utama Responsive -->
<div class="w-full max-w-xl md:max-w-3xl lg:max-w-4xl mx-auto bg-[#f4faf7] min-h-screen p-4 md:p-8 pb-20 font-sans text-gray-800">

    <!-- Tombol Kembali -->
    <a href="{{ route('pengurus-kelas.jurnal.index') }}" class="inline-flex items-center gap-1.5 text-xs md:text-sm font-bold text-[#0d6e59] mb-4 hover:underline">
        <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
    </a>

    <!-- Header Halaman -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h2 class="text-xl md:text-2xl lg:text-3xl font-extrabold text-[#0f3d32]">Jadwal Kelas Hari Ini</h2>
            <p class="text-xs md:text-sm text-[#5e7e75] mt-0.5">
                Daftar urutan jam pelajaran dan waktu istirahat untuk kelas {{ optional($kelas)->nama_kelas ?? 'Anda' }}.
            </p>
        </div>
        <span class="text-xs md:text-sm font-bold bg-[#e4f3ed] text-[#0d6e59] px-3.5 py-1.5 rounded-xl self-start sm:self-auto shrink-0 shadow-sm">
            <i class="bi bi-calendar-event mr-1"></i> {{ $namaHari }}, {{ now()->translatedFormat('d F Y') }}
        </span>
    </div>

    <!-- List Sesi Pelajaran & Istirahat -->
    <div class="space-y-3">
        @php
            $currentTime = now()->format('H:i:s');
        @endphp

        @forelse($jadwals as $j)
            @php
                $isIstirahat = str_contains(strtolower($j->mapel), 'istirahat');
                $isUpacara = str_contains(strtolower($j->mapel), 'upacara');
                $isPembiasaan = str_contains(strtolower($j->mapel), 'pembiasaan');
                $mulai = $j->jam_mulai;
                $selesai = $j->jam_selesai;

                if ($currentTime < $mulai) {
                    $status = 'Belum Mulai';
                    $statusClass = 'bg-gray-100 text-gray-500';
                } elseif ($currentTime >= $mulai && $currentTime <= $selesai) {
                    $status = 'Berlangsung';
                    $statusClass = 'bg-emerald-100 text-emerald-800 animate-pulse';
                } else {
                    $status = 'Selesai';
                    $statusClass = 'bg-gray-100 text-gray-600';
                }
            @endphp

            @if($isIstirahat)
                <!-- Card Istirahat -->
                <div class="bg-amber-50/70 p-3 rounded-xl border border-dashed border-amber-300 flex items-center justify-between text-xs md:text-sm text-amber-900 font-medium shadow-2xs">
                    <div class="flex items-center gap-2">
                        <span class="w-7 h-7 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center font-bold text-sm">
                            <i class="bi bi-cup-hot-fill"></i>
                        </span>
                        <div>
                            <span class="font-bold">{{ $j->mapel }}</span>
                            <span class="text-amber-700 font-mono text-[11px] block sm:inline sm:ml-2">
                                ({{ date('H:i', strtotime($mulai)) }} – {{ date('H:i', strtotime($selesai)) }} WIB)
                            </span>
                        </div>
                    </div>
                    <span class="text-[10px] md:text-xs font-semibold bg-amber-200/70 text-amber-800 px-2.5 py-1 rounded-lg">
                        Istirahat
                    </span>
                </div>
            @else
                <!-- Card Sesi Pelajaran -->
                <div class="bg-white p-3.5 md:p-4 rounded-2xl border-l-4 {{ $isUpacara || $isPembiasaan ? 'border-amber-500' : 'border-[#0d6e59]' }} shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-start sm:items-center gap-3">
                        <span class="{{ $isUpacara || $isPembiasaan ? 'bg-amber-50 text-amber-800' : 'bg-[#e4f3ed] text-[#0d6e59]' }} font-extrabold text-xs md:text-sm px-2.5 py-1.5 md:px-3 md:py-2 rounded-xl shrink-0">
                            {{ $j->jam_ke > 0 ? 'Jam ' . $j->jam_ke : 'Khusus' }}
                        </span>
                        <div>
                            <h4 class="text-xs md:text-sm font-bold text-[#0f3d32]">{{ $j->mapel }}</h4>
                            <p class="text-[11px] md:text-xs text-gray-500 mt-0.5">
                                {{ date('H:i', strtotime($mulai)) }} – {{ date('H:i', strtotime($selesai)) }} WIB
                                @if($j->guru)
                                    • {{ $j->guru->name }}
                                @endif
                            </p>
                        </div>
                    </div>
                    <span class="self-end sm:self-center text-[10px] md:text-xs font-bold {{ $statusClass }} px-2.5 py-1 rounded-lg shrink-0">
                        {{ $status }}
                    </span>
                </div>
            @endif
        @empty
            <div class="bg-white p-8 rounded-2xl text-center text-gray-400 text-sm">
                <i class="bi bi-calendar-x text-3xl mb-2 block text-gray-300"></i>
                Tidak ada jadwal pelajaran untuk hari ini.
            </div>
        @endforelse
    </div>

</div>
@endsection