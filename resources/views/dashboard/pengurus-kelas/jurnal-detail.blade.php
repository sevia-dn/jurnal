@extends('layouts.app')

@section('sidebar')
    @include('layouts.pengurus-kelas.sidebar', ['activePage' => 'jurnal-detail'])
@endsection

@section('navbar')
    @include('layouts.pengurus-kelas.navbar', ['activePage' => 'jurnal-detail'])
@endsection

@section('content')
<div class="mx-auto w-full max-w-4xl px-4 py-6 pb-16 sm:px-6 lg:px-8">

    {{-- Notifikasi Success/Error --}}
    @if(session('success'))
        <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-900 shadow-sm" role="alert">
            <div class="flex items-center gap-2.5">
                <i class="bi bi-check-circle-fill text-lg text-emerald-600"></i>
                <p class="text-sm font-semibold">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 p-4 text-rose-900 shadow-sm" role="alert">
            <div class="flex items-center gap-2.5">
                <i class="bi bi-exclamation-triangle-fill text-lg text-rose-600"></i>
                <p class="text-sm font-semibold">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    @if(!isset($jurnal) || !$jurnal)
        {{-- TAMPILAN JIKA BELUM MEMILIH JURNAL ATAU BELUM ADA JURNAL --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-8 text-center shadow-sm">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 text-2xl text-slate-400">
                <i class="bi bi-journal-text"></i>
            </div>
            <h2 class="mt-4 text-lg font-bold text-slate-800">Tidak Ada Logbook Dipilih</h2>
            <p class="mt-1 text-sm text-slate-500">Silakan pilih logbook dari riwayat untuk melihat detail dan melakukan validasi.</p>
            <div class="mt-6 flex justify-center gap-3">
                <a href="{{ route('pengurus-kelas.jurnal-detail') }}" class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-xs font-bold text-white shadow-sm hover:bg-emerald-700">
                    <i class="bi bi-clock-history"></i> Lihat Riwayat Logbook
                </a>
                <a href="{{ route('pengurus-kelas.dashboard') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-100">
                    <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
                </a>
            </div>
        </div>
    @else
        @php
            $statusValidasi = $jurnal->status_validasi ?? 'belum_divalidasi';
            $statusLabel = match($statusValidasi) {
                'disetujui' => 'Disetujui',
                'ditolak' => 'Perlu Revisi / Ditolak',
                default => 'Menunggu Persetujuan',
            };
            $statusColor = match($statusValidasi) {
                'disetujui' => 'emerald',
                'ditolak' => 'rose',
                default => 'amber',
            };
            $namaGuru = $jurnal->user->name ?? 'Guru Pengajar';
            $nipGuru = $jurnal->user->nip ?? '-';
            $initials = strtoupper(substr($namaGuru, 0, 1));
            $parts = explode(' ', $namaGuru);
            if (count($parts) > 1) {
                $initials .= strtoupper(substr($parts[1], 0, 1));
            }
            $tanggalLong = \Carbon\Carbon::parse($jurnal->tanggal)->translatedFormat('l, d F Y');
            $jamText = ($jurnal->jam_selesai && $jurnal->jam_selesai > $jurnal->jam_ke)
                ? "Jam ke {$jurnal->jam_ke}-{$jurnal->jam_selesai}"
                : "Jam ke {$jurnal->jam_ke}";
        @endphp

        {{-- HEADER LOGBOOK --}}
        <header class="rounded-2xl border border-{{ $statusColor }}-200 bg-{{ $statusColor }}-50/60 p-5 shadow-sm sm:p-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-4">
                    <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-emerald-700 text-lg font-extrabold text-white" aria-label="Inisial guru">{{ $initials }}</span>
                    <div>
                        <p class="text-xs font-bold text-emerald-700 uppercase tracking-wider">Logbook Guru Pengajar</p>
                        <h1 class="text-lg font-extrabold text-slate-900 sm:text-xl">{{ $namaGuru }}</h1>
                        <p class="text-xs text-slate-500">{{ $jurnal->mapel->nama_mapel ?? '-' }} · NIP: {{ $nipGuru }}</p>
                    </div>
                </div>
                <span class="inline-flex w-fit items-center gap-1.5 rounded-full bg-{{ $statusColor }}-100 px-3 py-1.5 text-xs font-bold text-{{ $statusColor }}-700 border border-{{ $statusColor }}-200">
                    @if($statusValidasi === 'disetujui')
                        <i class="bi bi-check-circle-fill"></i>
                    @elseif($statusValidasi === 'ditolak')
                        <i class="bi bi-x-circle-fill"></i>
                    @else
                        <i class="bi bi-hourglass-split"></i>
                    @endif
                    {{ $statusLabel }}
                </span>
            </div>
        </header>

        <div class="mt-5 space-y-4">

            {{-- 1. JADWAL & WAKTU --}}
            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-sm font-extrabold uppercase tracking-wide text-slate-700">Jadwal &amp; Waktu</h2>
                <dl class="mt-3 grid gap-3 sm:grid-cols-3">
                    <div class="rounded-xl bg-slate-50 p-3.5 border border-slate-100">
                        <dt class="text-[11px] font-bold uppercase tracking-wide text-slate-400">Tanggal</dt>
                        <dd class="mt-1 text-sm font-bold text-slate-800">{{ $tanggalLong }}</dd>
                    </div>
                    <div class="rounded-xl bg-slate-50 p-3.5 border border-slate-100">
                        <dt class="text-[11px] font-bold uppercase tracking-wide text-slate-400">Jam Pelajaran</dt>
                        <dd class="mt-1 text-sm font-bold text-slate-800">{{ $jamText }}</dd>
                    </div>
                    <div class="rounded-xl bg-slate-50 p-3.5 border border-slate-100">
                        <dt class="text-[11px] font-bold uppercase tracking-wide text-slate-400">Status Kehadiran Guru</dt>
                        <dd class="mt-1 text-sm font-bold text-emerald-700 flex items-center gap-1">
                            <i class="bi bi-person-check-fill"></i> {{ $jurnal->status_kehadiran_guru ?? 'Hadir' }}
                        </dd>
                    </div>
                </dl>
            </section>

            {{-- 2. MATA PELAJARAN & KELAS --}}
            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-sm font-extrabold uppercase tracking-wide text-slate-700">Mata Pelajaran &amp; Kelas</h2>
                <dl class="mt-3 grid gap-3 sm:grid-cols-2">
                    <div class="rounded-xl bg-emerald-50/70 p-3.5 border border-emerald-100">
                        <dt class="text-[11px] font-bold uppercase tracking-wide text-emerald-700">Mata Pelajaran</dt>
                        <dd class="mt-1 text-sm font-bold text-slate-900">{{ $jurnal->mapel->nama_mapel ?? '-' }}</dd>
                    </div>
                    <div class="rounded-xl bg-emerald-50/70 p-3.5 border border-emerald-100">
                        <dt class="text-[11px] font-bold uppercase tracking-wide text-emerald-700">Kelas</dt>
                        <dd class="mt-1 text-sm font-bold text-slate-900">{{ $jurnal->kelas->nama_kelas ?? '-' }}</dd>
                    </div>
                </dl>
            </section>

            {{-- 3. RINGKASAN MATERI --}}
            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-sm font-extrabold uppercase tracking-wide text-slate-700">Materi / Pokok Pembahasan</h2>
                <div class="mt-3 rounded-xl bg-slate-50 p-4 border border-slate-100">
                    <p class="text-sm leading-relaxed text-slate-700 whitespace-pre-line">{{ $jurnal->materi }}</p>
                </div>
                @if($jurnal->ada_tugas)
                    <div class="mt-2.5 flex items-center gap-2 text-xs font-semibold text-emerald-700 bg-emerald-50 px-3 py-1.5 rounded-lg border border-emerald-100 w-fit">
                        <i class="bi bi-clipboard-check-fill"></i> Terdapat tugas / pekerjaan rumah yang diberikan oleh guru
                    </div>
                @endif
            </section>

            {{-- 4. CATATAN KHUSUS / HAMBATAN DI KELAS --}}
            <section class="rounded-2xl border border-amber-200 bg-amber-50/70 p-5 shadow-sm">
                <div class="flex items-center gap-2">
                    <i class="bi bi-exclamation-triangle-fill text-amber-600" aria-hidden="true"></i>
                    <h2 class="text-sm font-extrabold text-amber-900 uppercase tracking-wide">Catatan Khusus / Hambatan di Kelas</h2>
                </div>
                <div class="mt-3 rounded-xl border border-amber-100 bg-white p-4">
                    <p class="text-sm leading-relaxed {{ $jurnal->catatan ? 'text-amber-900' : 'text-slate-400 italic' }}">
                        {{ $jurnal->catatan ?: 'Tidak ada catatan khusus / hambatan dari guru pada sesi ini.' }}
                    </p>
                </div>
            </section>

            {{-- 5. REKAP PRESENSI KEHADIRAN SISWA --}}
            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <h2 class="text-sm font-extrabold uppercase tracking-wide text-slate-700">Rincian Kehadiran Siswa</h2>
                    <span class="w-fit rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700 border border-emerald-100">
                        Total {{ $jurnal->kelas->jumlah_siswa ?? ($jurnal->absensis->count() ?: 36) }} Siswa
                    </span>
                </div>

                {{-- Badges Ringkasan --}}
                <div class="mt-3 flex flex-wrap gap-2 text-xs font-bold">
                    <span class="rounded-full bg-emerald-100 px-3 py-1 text-emerald-800">
                        {{ $jurnal->jumlah_hadir ?? 0 }} Hadir
                    </span>
                    @if(($jurnal->jumlah_sakit ?? 0) > 0)
                        <span class="rounded-full bg-amber-100 px-3 py-1 text-amber-800">
                            {{ $jurnal->jumlah_sakit }} Sakit
                        </span>
                    @endif
                    @if(($jurnal->jumlah_izin ?? 0) > 0)
                        <span class="rounded-full bg-blue-100 px-3 py-1 text-blue-800">
                            {{ $jurnal->jumlah_izin }} Izin
                        </span>
                    @endif
                    @if(($jurnal->jumlah_alpa ?? 0) > 0)
                        <span class="rounded-full bg-rose-100 px-3 py-1 text-rose-800">
                            {{ $jurnal->jumlah_alpa }} Alpa
                        </span>
                    @endif
                    @if(($jurnal->jumlah_dispensasi ?? 0) > 0)
                        <span class="rounded-full bg-indigo-100 px-3 py-1 text-indigo-800">
                            {{ $jurnal->jumlah_dispensasi }} Dispensasi
                        </span>
                    @endif
                </div>

                {{-- Daftar Siswa Tidak Masuk / Daftar Lengkap --}}
                <div class="mt-4">
                    @php
                        $tidakHadir = $jurnal->absensis->filter(fn ($a) => trim(strtolower($a->status ?? '')) !== 'hadir');
                    @endphp

                    @if($tidakHadir->count() > 0)
                        <div class="rounded-xl border border-amber-200 bg-amber-50/40 p-3 mb-3">
                            <p class="text-xs font-bold text-amber-900 mb-2 flex items-center gap-1.5">
                                <i class="bi bi-person-x-fill text-amber-600"></i>
                                Siswa Tidak Masuk ({{ $tidakHadir->count() }} orang):
                            </p>
                            <div class="space-y-1.5">
                                @foreach($tidakHadir as $ab)
                                    @php
                                        $normStatus = strtoupper(trim((string) $ab->status));
                                        $statusLabel = in_array($normStatus, ['D', 'DISPENSASI']) ? 'Dispensasi' : (in_array($normStatus, ['ALFA', 'ALPA']) ? 'Alpa' : $ab->status);
                                        $badgeBg = match($statusLabel) {
                                            'Sakit' => 'bg-amber-100 text-amber-800 border-amber-200',
                                            'Izin' => 'bg-blue-100 text-blue-800 border-blue-200',
                                            'Alpa' => 'bg-rose-100 text-rose-800 border-rose-200',
                                            'Dispensasi' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                                            default => 'bg-slate-100 text-slate-800 border-slate-200',
                                        };
                                    @endphp
                                    <div class="flex items-center justify-between gap-3 rounded-lg bg-white border border-slate-200 p-2.5 text-xs shadow-2xs">
                                        <div class="min-w-0">
                                            <div class="flex items-center gap-1.5">
                                                <span class="font-bold text-slate-800">{{ $ab->siswa->nama ?? 'Siswa' }}</span>
                                                <span class="text-[10px] text-slate-400">(NIS: {{ $ab->siswa->nis ?? '-' }})</span>
                                            </div>
                                            <p class="mt-0.5 text-[11px] text-slate-600">Alasan: {{ $ab->catatan ?: 'Tidak ada keterangan yang diisi guru.' }}</p>
                                        </div>
                                        <span class="shrink-0 rounded-md border px-2 py-0.5 text-[10px] font-bold {{ $badgeBg }}">
                                            {{ $statusLabel }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="rounded-xl border border-emerald-200 bg-emerald-50/50 p-3 text-xs font-semibold text-emerald-800 flex items-center gap-2">
                            <i class="bi bi-check-circle-fill text-emerald-600 text-base"></i>
                            Semua siswa hadir di kelas (Nihil tidak hadir).
                        </div>
                    @endif
                </div>
            </section>

            {{-- 6. FOTO LIVE BUKTI MENGAJAR DI KELAS --}}
            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="bi bi-camera-fill text-emerald-600 text-lg"></i>
                        <h2 class="text-sm font-extrabold uppercase tracking-wide text-slate-800">Foto Live Bukti Kehadiran di Kelas</h2>
                    </div>
                    @if($jurnal->lampiran)
                        <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-[10px] font-bold text-emerald-700">
                            Foto Terverifikasi
                        </span>
                    @endif
                </div>

                <div class="mt-4">
                    @if($jurnal->lampiran)
                        @php
                            $fotoUrl = asset('storage/' . $jurnal->lampiran);
                        @endphp
                        <div class="overflow-hidden rounded-xl border border-slate-200 bg-slate-100 shadow-2xs">
                            <a href="{{ $fotoUrl }}" target="_blank" title="Buka foto ukuran penuh" class="group block relative">
                                <img
                                    src="{{ $fotoUrl }}"
                                    alt="Foto Live Bukti Mengajar Guru"
                                    class="max-h-96 w-full object-contain rounded-xl transition duration-200 group-hover:opacity-95"
                                >
                                <div class="absolute bottom-2 right-2 rounded-lg bg-black/60 px-2.5 py-1 text-[10px] font-medium text-white backdrop-blur-xs flex items-center gap-1">
                                    <i class="bi bi-arrows-fullscreen"></i> Buka Ukuran Penuh
                                </div>
                            </a>
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center rounded-xl border-2 border-dashed border-slate-200 bg-slate-50/70 p-6 text-center">
                            <i class="bi bi-camera-video-off text-2xl text-slate-400"></i>
                            <p class="mt-1 text-xs font-semibold text-slate-500">Tidak ada foto live bukti kehadiran yang dilampirkan.</p>
                        </div>
                    @endif
                </div>
            </section>

        </div>

        {{-- ACTION FOOTER VALIDASI hanya untuk jurnal yang belum memperoleh keputusan. --}}
        @if($statusValidasi === 'belum_divalidasi')
        <section class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <form action="{{ route('pengurus-kelas.jurnal-validasi', ['id' => $jurnal->id_jurnal]) }}" method="POST">
                @csrf
                <div class="p-5">
                    <label for="catatan_validasi" class="block text-sm font-extrabold text-slate-900">
                        Catatan Pengurus Kelas <span class="text-xs font-normal text-slate-500">(wajib jika meminta revisi/menolak)</span>
                    </label>
                    <textarea
                        id="catatan_validasi"
                        name="catatan_validasi"
                        rows="3"
                        placeholder="Tuliskan catatan atau masukan untuk guru pengajar..."
                        class="mt-2.5 w-full resize-none rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-xs text-slate-700 placeholder:text-slate-400 focus:border-emerald-600 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-100 sm:text-sm"
                    >{{ old('catatan_validasi', $jurnal->catatan_validasi) }}</textarea>
                </div>

                <div class="flex flex-col-reverse gap-3 border-t border-slate-100 bg-slate-50 px-5 py-4 sm:flex-row sm:justify-between sm:items-center">
                    <a href="{{ route('pengurus-kelas.jurnal-detail') }}" class="inline-flex items-center justify-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-slate-900">
                        <i class="bi bi-arrow-left"></i> Kembali ke Riwayat
                    </a>

                    <div class="flex flex-col-reverse gap-2 sm:flex-row sm:items-center">
                        <button
                            type="submit"
                            name="action"
                            value="tolak"
                            class="inline-flex min-h-10 items-center justify-center gap-2 rounded-xl border border-rose-300 bg-white px-4 py-2 text-xs font-bold text-rose-700 transition hover:bg-rose-50 focus:outline-none focus:ring-2 focus:ring-rose-500"
                        >
                            <i class="bi bi-arrow-counterclockwise"></i> Minta Revisi / Tolak
                        </button>
                        <button
                            type="submit"
                            name="action"
                            value="setujui"
                            class="inline-flex min-h-10 items-center justify-center gap-2 rounded-xl bg-emerald-600 px-5 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-600"
                        >
                            <i class="bi bi-check-lg text-base"></i> Setujui Logbook
                        </button>
                    </div>
                </div>
            </form>
        </section>
        @else
        <section class="mt-6 flex flex-col gap-3 rounded-2xl border border-{{ $statusColor }}-200 bg-{{ $statusColor }}-50/60 p-5 shadow-sm sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-extrabold text-{{ $statusColor }}-900">Logbook sudah {{ strtolower($statusLabel) }}</p>
                <p class="mt-1 text-xs text-slate-600">Keputusan validasi telah tersimpan sehingga tindakan validasi tidak tersedia lagi.</p>
            </div>
            <a href="{{ route('pengurus-kelas.jurnal-detail') }}" class="inline-flex w-fit items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-bold text-slate-700 transition hover:bg-slate-50"><i class="bi bi-arrow-left"></i>Kembali ke Riwayat</a>
        </section>
        @endif
    @endif

</div>
@endsection
