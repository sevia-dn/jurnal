@extends('layouts.app')

@section('title', 'Ketidakhadiran Guru - Piket JurnalKita')

@section('sidebar')
    @include('layouts.guru-pengajar.sidebar', ['activePage' => 'piket'])
@endsection

@section('navbar')
    @include('layouts.guru-pengajar.navbar', ['activePage' => 'piket'])
@endsection

@section('content')
    <div class="min-h-full bg-slate-50 p-4 pb-24 font-sans sm:p-6 lg:p-8">
        <div class="mx-auto max-w-3xl">
            <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
                <a href="{{ route('dashboard.piket') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-emerald-700 transition hover:text-emerald-800">
                    <i class="bi bi-arrow-left"></i> Kembali ke piket
                </a>
            </div>

            {{-- Header --}}
            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6 mb-4">
                <div class="flex items-start gap-3">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-xl text-amber-600">
                        <i class="bi bi-calendar-x-fill"></i>
                    </span>
                    <div class="flex-1">
                        <h1 class="text-lg font-bold text-slate-900">Pengajuan Ketidakhadiran Guru</h1>
                        <p class="mt-1 text-xs text-slate-500">Proses pengajuan izin / sakit dari guru pengajar.</p>
                    </div>
                    @if($pendingCount > 0)
                        <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-700">
                            <i class="bi bi-exclamation-circle-fill"></i> {{ $pendingCount }} menunggu
                        </span>
                    @endif
                </div>
            </section>

            {{-- Alerts --}}
            @if(session('success'))
                <div class="mb-4 flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800">
                    <i class="bi bi-check-circle-fill"></i>{{ session('success') }}
                </div>
            @endif

            {{-- Filter --}}
            <form method="GET" action="{{ route('piket.ketidakhadiran-guru.index') }}" class="mb-4 flex flex-wrap gap-2 items-center">
                <input
                    type="date"
                    name="tanggal"
                    value="{{ $tanggal }}"
                    max="{{ now('Asia/Jakarta')->toDateString() }}"
                    class="rounded-xl border border-slate-300 px-3 py-2 text-sm text-slate-700 outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100"
                >
                <select name="status" class="rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100">
                    <option value="pending" @selected($status === 'pending')>Menunggu</option>
                    <option value="disetujui" @selected($status === 'disetujui')>Disetujui</option>
                    <option value="ditolak" @selected($status === 'ditolak')>Ditolak</option>
                    <option value="all" @selected($status === 'all')>Semua</option>
                </select>
                <button type="submit" class="rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700 transition">
                    Filter
                </button>
            </form>

            {{-- List Ketidakhadiran --}}
            <div class="space-y-3">
                @forelse($ketidakhadirans as $item)
                    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                        <div class="flex items-start gap-4">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl @if($item->alasan === 'sakit') bg-rose-100 text-rose-600 @else bg-amber-100 text-amber-600 @endif text-lg">
                                @if($item->alasan === 'sakit')
                                    <i class="bi bi-thermometer-half"></i>
                                @else
                                    <i class="bi bi-calendar2-x"></i>
                                @endif
                            </span>
                            <div class="flex-1 min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <p class="text-sm font-bold text-slate-900">{{ $item->guru->name }}</p>
                                    <span class="rounded-full px-2 py-0.5 text-[11px] font-semibold @if($item->status === 'disetujui') bg-emerald-100 text-emerald-700 @elseif($item->status === 'ditolak') bg-red-100 text-red-700 @else bg-amber-100 text-amber-700 @endif">
                                        {{ ucfirst($item->status) }}
                                    </span>
                                    <span class="rounded-full px-2 py-0.5 text-[11px] font-semibold @if($item->alasan === 'sakit') bg-rose-100 text-rose-700 @else bg-amber-100 text-amber-700 @endif">
                                        {{ $item->label_alasan }}
                                    </span>
                                </div>
                                <p class="mt-0.5 text-xs text-slate-500">
                                    {{ $item->tanggal->translatedFormat('l, d F Y') }}
                                </p>
                                @if($item->keterangan)
                                    <p class="mt-1 text-xs text-slate-700">{{ $item->keterangan }}</p>
                                @endif
                                @if($item->lampiran)
                                    <a href="{{ asset('storage/'.$item->lampiran) }}" target="_blank" class="mt-1 inline-flex items-center gap-1 text-xs text-emerald-600 hover:underline">
                                        <i class="bi bi-paperclip"></i> Lihat lampiran
                                    </a>
                                @endif
                                @if($item->catatan_piket)
                                    <p class="mt-1 text-xs text-slate-500 italic">Catatan: {{ $item->catatan_piket }}</p>
                                @endif
                            </div>

                            {{-- Action buttons --}}
                            @if($item->status === 'pending')
                                <div class="flex flex-col gap-2 shrink-0" x-data="{ showReject: false }">
                                    <form method="POST" action="{{ route('piket.ketidakhadiran-guru.approve', $item) }}">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-600 px-3 py-1.5 text-xs font-bold text-white hover:bg-emerald-700 transition">
                                            <i class="bi bi-check-lg"></i> Setujui
                                        </button>
                                    </form>
                                    <button type="button" @click="showReject = !showReject" class="inline-flex items-center gap-1.5 rounded-xl border border-red-300 px-3 py-1.5 text-xs font-bold text-red-600 hover:bg-red-50 transition">
                                        <i class="bi bi-x-lg"></i> Tolak
                                    </button>
                                    <div x-show="showReject" x-cloak class="mt-1">
                                        <form method="POST" action="{{ route('piket.ketidakhadiran-guru.reject', $item) }}" class="flex flex-col gap-1">
                                            @csrf
                                            <textarea name="catatan_piket" rows="2" placeholder="Alasan penolakan..." class="w-full rounded-lg border border-slate-300 px-2 py-1.5 text-xs text-slate-700 outline-none focus:border-red-400 focus:ring-2 focus:ring-red-100"></textarea>
                                            <button type="submit" class="rounded-lg bg-red-500 px-2 py-1 text-xs font-bold text-white hover:bg-red-600 transition">Konfirmasi Tolak</button>
                                        </form>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="rounded-2xl border border-slate-200 bg-white p-8 text-center text-sm text-slate-400 shadow-sm">
                        <i class="bi bi-calendar-check text-2xl block mb-2"></i>
                        Tidak ada pengajuan ketidakhadiran untuk tanggal ini.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection

