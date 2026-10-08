@extends('layouts.app')

@section('title', 'Pemeriksaan Jurnal Kelas - JurnalKita')

@section('sidebar')
    @include('layouts.guru-pengajar.sidebar', ['activePage' => 'piket'])
@endsection

@section('navbar')
    @include('layouts.guru-pengajar.navbar', ['activePage' => 'piket'])
@endsection

@section('content')
    <div class="min-h-full bg-slate-50 px-4 py-5 pb-24 font-sans sm:px-6 lg:px-8">
        <div class="mx-auto max-w-5xl">
            <a href="{{ route('dashboard.piket') }}" class="inline-flex items-center gap-2 rounded-xl border border-indigo-200 bg-indigo-50 px-3 py-2 text-xs font-bold text-indigo-700 transition hover:bg-indigo-100"><i class="bi bi-arrow-left"></i>Kembali</a>

            @if(session('success'))
                <div class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-semibold text-emerald-800">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="mt-4 rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm font-semibold text-rose-800">{{ session('error') }}</div>
            @endif

            <header class="mt-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-emerald-700">Pemeriksaan jurnal kelas</p>
                        <h1 class="mt-1 text-2xl font-extrabold text-slate-900">Kelas {{ $kelas->nama_kelas }}</h1>
                        <p class="mt-1 text-sm text-slate-500">{{ \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d F Y') }} · {{ $classSummary->total_terisi }}/{{ $classSummary->total_sesi }} sesi jurnal terisi</p>
                    </div>
                    @if($classSummary->persetujuan)
                        <span class="inline-flex w-fit items-center gap-1.5 rounded-full bg-emerald-100 px-3 py-2 text-xs font-extrabold text-emerald-800"><i class="bi bi-patch-check-fill"></i>Disetujui {{ $classSummary->persetujuan->piket?->name ?? 'Guru Piket' }}</span>
                    @elseif($classSummary->siap_disetujui_piket)
                        <span class="inline-flex w-fit items-center gap-1.5 rounded-full bg-indigo-100 px-3 py-2 text-xs font-extrabold text-indigo-800"><i class="bi bi-clipboard2-check"></i>Siap divalidasi piket</span>
                    @else
                        <span class="inline-flex w-fit items-center gap-1.5 rounded-full bg-amber-100 px-3 py-2 text-xs font-extrabold text-amber-800"><i class="bi bi-hourglass-split"></i>Belum siap divalidasi</span>
                    @endif
                </div>

                @if(! $classSummary->persetujuan && $classSummary->siap_disetujui_piket)
                    <form method="POST" action="{{ route('piket.rekap-jurnal.kelas.approve', $kelas) }}" class="mt-5 border-t border-slate-100 pt-4">
                        @csrf
                        <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <p class="text-sm text-slate-600">Seluruh sesi sudah terisi dan divalidasi pengurus kelas. Periksa detailnya sebelum menyetujui.</p>
                            <button type="submit" class="inline-flex shrink-0 items-center justify-center gap-1.5 rounded-xl bg-[#155d50] px-4 py-2.5 text-sm font-bold text-white transition hover:bg-[#0f463c]"><i class="bi bi-pen-fill"></i>Setujui Jurnal Kelas</button>
                        </div>
                    </form>
                @elseif(! $classSummary->persetujuan && $classSummary->total_sesi > 0)
                    <p class="mt-5 border-t border-slate-100 pt-4 text-sm text-slate-600">Validasi piket akan tersedia setelah <strong>{{ $classSummary->total_sesi }}/{{ $classSummary->total_sesi }}</strong> sesi terisi dan telah disetujui pengurus kelas. Saat ini tervalidasi <strong>{{ $classSummary->total_tervalidasi }}/{{ $classSummary->total_sesi }}</strong> sesi.</p>
                @endif
            </header>

            <section class="mt-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm" aria-labelledby="session-list-title">
                <div class="border-b border-slate-100 px-5 py-4"><h2 id="session-list-title" class="font-extrabold text-slate-800">Sesi pembelajaran</h2><p class="mt-1 text-xs text-slate-500">Pilih sesi yang terisi untuk melihat detail pengisian jurnal guru.</p></div>
                <div class="divide-y divide-slate-100">
                    @forelse($classSummary->sesi_items as $session)
                        @php
                            $statusClass = ! $session->is_terisi ? 'bg-rose-100 text-rose-800' : ($session->is_validated ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800');
                            $statusLabel = ! $session->is_terisi ? 'Belum diisi' : ($session->is_validated ? 'Tervalidasi pengurus' : 'Menunggu validasi pengurus');
                            $si = $session->status_info ?? null;
                        @endphp
                        <div class="p-4 sm:p-5">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2"><span class="rounded-md bg-slate-100 px-2 py-1 text-xs font-extrabold text-slate-700">{{ $session->jam_ke_formatted }}</span><span class="rounded-full px-2.5 py-1 text-[11px] font-bold {{ $statusClass }}">{{ $statusLabel }}</span>@if($si)<span class="rounded-full px-2.5 py-1 text-[11px] font-bold {{ $si['badge_class'] }}" title="{{ $si['reason'] ?? '' }}"><i class="{{ $si['icon'] }}"></i> {{ $si['label'] }}</span>@endif</div>
                                    <h3 class="mt-2 font-extrabold text-slate-800">{{ $session->mapel }}</h3>
                                    <p class="mt-0.5 text-sm text-slate-500">{{ $session->guru }} · {{ $session->waktu_mulai }}–{{ $session->waktu_selesai }}</p>
                                    @if($session->is_terisi)
                                        <p class="mt-2 line-clamp-2 text-xs text-slate-600"><strong>Materi:</strong> {{ $session->jurnal->materi ?: 'Belum ada materi yang dicatat.' }}</p>
                                    @endif
                                </div>
                                @if($session->is_terisi)
                                    <a href="{{ route('piket.jurnal.show', ['jurnal' => $session->jurnal, 'back_url' => url()->full()]) }}" class="inline-flex shrink-0 items-center justify-center gap-1.5 rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs font-bold text-slate-700 transition hover:bg-slate-50"><i class="bi bi-file-text"></i>Lihat Detail Jurnal</a>
                                @else
                                    <span class="inline-flex shrink-0 items-center justify-center gap-1.5 rounded-xl bg-slate-100 px-3 py-2 text-xs font-bold text-slate-500"><i class="bi bi-hourglass"></i>Belum ada jurnal</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-12 text-center text-sm text-slate-500">Tidak ada jadwal pembelajaran untuk kelas ini pada tanggal tersebut.</div>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
@endsection
