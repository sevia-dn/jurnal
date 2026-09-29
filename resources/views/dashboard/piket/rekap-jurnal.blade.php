@extends('layouts.app')

@section('title', 'Riwayat & Rekap Jurnal - JurnalKita')

@section('sidebar')
    @include('layouts.guru-pengajar.sidebar', ['activePage' => 'piket'])
@endsection

@section('navbar')
    @include('layouts.guru-pengajar.navbar', ['activePage' => 'piket'])
@endsection

@section('content')
    <div class="min-h-full bg-slate-50 p-4 pb-24 font-sans sm:p-6 lg:p-8">
        <div class="mx-auto max-w-7xl">
            <div class="mb-5 flex flex-col gap-4 border-b border-slate-200 pb-5 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <a href="{{ route('dashboard.piket') }}" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs font-bold text-slate-700 transition hover:bg-slate-50"><i class="bi bi-arrow-left"></i>Kembali</a>
                    <h1 class="mt-2 text-xl font-extrabold text-slate-900">Riwayat &amp; Rekap Jurnal</h1>
                    <p class="mt-1 text-sm text-slate-500">Cari jurnal yang sudah lalu dan lihat rekap per guru atau per kelas.</p>
                </div>
                <a href="{{ route('piket.rekap-jurnal.download-pdf', request()->query()) }}" class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs font-bold text-slate-700 transition hover:bg-slate-50"><i class="bi bi-file-earmark-pdf text-rose-600"></i>Unduh Rekap PDF</a>
            </div>

            @if(session('success'))<div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-semibold text-emerald-800">{{ session('success') }}</div>@endif
            @if(session('error'))<div class="mb-5 rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm font-semibold text-rose-800">{{ session('error') }}</div>@endif

            <nav class="mb-5 flex gap-2 overflow-x-auto" aria-label="Jenis rekap">
                @foreach(['jurnal' => 'Riwayat Jurnal', 'rekap_kelas' => 'Rekap per Kelas', 'rekap_guru' => 'Rekap per Guru'] as $tabKey => $tabLabel)
                    <a href="{{ route('piket.rekap-jurnal', array_merge(request()->except('tab'), ['tab' => $tabKey])) }}" class="whitespace-nowrap rounded-xl px-3 py-2 text-xs font-bold transition {{ $tab === $tabKey ? 'bg-[#155d50] text-white' : 'border border-slate-200 bg-white text-slate-600 hover:bg-slate-50' }}">{{ $tabLabel }}</a>
                @endforeach
            </nav>

            <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                @if($tab === 'rekap_kelas')
                    {{-- Filter Khusus Rekap Per Kelas: Tanggal Mulai, Selesai (otomatis mengikuti), Searchable Dropdown Kelas --}}
                    <form
                        method="GET"
                        action="{{ route('piket.rekap-jurnal') }}"
                        x-data="{
                            tglMulai: '{{ $tanggalMulai ?? $tanggal }}',
                            tglSelesai: '{{ $tanggalSelesai ?? $tanggal }}',
                            updateSelesai() {
                                this.tglSelesai = this.tglMulai;
                            }
                        }"
                        class="grid gap-3 md:grid-cols-2 lg:grid-cols-4 items-end"
                    >
                        <input type="hidden" name="tab" value="rekap_kelas">
                        <div>
                            <label for="tanggal_mulai_kelas" class="mb-1 block text-xs font-bold text-slate-600">Tanggal Mulai</label>
                            <input
                                id="tanggal_mulai_kelas"
                                type="date"
                                name="tanggal_mulai"
                                x-model="tglMulai"
                                @change="updateSelesai()"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm outline-none focus:border-emerald-500"
                            >
                        </div>
                        <div>
                            <label for="tanggal_selesai_kelas" class="mb-1 block text-xs font-bold text-slate-600">Tanggal Selesai</label>
                            <input
                                id="tanggal_selesai_kelas"
                                type="date"
                                name="tanggal_selesai"
                                x-model="tglSelesai"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm outline-none focus:border-emerald-500"
                            >
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-bold text-slate-600">Pilih Kelas</label>
                            <x-searchable-select name="kelas_id" :options="$opsiKelasRekap" :selected="$kelasId === 'all' ? null : $kelasId" placeholder="Semua kelas" />
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="submit" class="w-full rounded-xl bg-[#155d50] px-4 py-2 text-sm font-bold text-white hover:bg-[#0f463c]">Terapkan</button>
                            <a href="{{ route('piket.rekap-jurnal', ['tab' => 'rekap_kelas']) }}" class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50">Reset</a>
                        </div>
                    </form>
                @elseif($tab === 'rekap_guru')
                    {{-- Filter Khusus Rekap Per Guru: Tanggal Mulai, Selesai (otomatis mengikuti), Searchable Dropdown Guru --}}
                    <form
                        method="GET"
                        action="{{ route('piket.rekap-jurnal') }}"
                        x-data="{
                            tglMulai: '{{ $tanggalMulai ?? $tanggal }}',
                            tglSelesai: '{{ $tanggalSelesai ?? $tanggal }}',
                            updateSelesai() {
                                this.tglSelesai = this.tglMulai;
                            }
                        }"
                        class="grid gap-3 md:grid-cols-2 lg:grid-cols-4 items-end"
                    >
                        <input type="hidden" name="tab" value="rekap_guru">
                        <div>
                            <label for="tanggal_mulai_guru" class="mb-1 block text-xs font-bold text-slate-600">Tanggal Mulai</label>
                            <input
                                id="tanggal_mulai_guru"
                                type="date"
                                name="tanggal_mulai"
                                x-model="tglMulai"
                                @change="updateSelesai()"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm outline-none focus:border-emerald-500"
                            >
                        </div>
                        <div>
                            <label for="tanggal_selesai_guru" class="mb-1 block text-xs font-bold text-slate-600">Tanggal Selesai</label>
                            <input
                                id="tanggal_selesai_guru"
                                type="date"
                                name="tanggal_selesai"
                                x-model="tglSelesai"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm outline-none focus:border-emerald-500"
                            >
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-bold text-slate-600">Pilih Guru</label>
                            <x-searchable-select name="guru_id" :options="$opsiGuruRekap" :selected="$guruId === 'all' ? null : $guruId" placeholder="Semua guru" />
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="submit" class="w-full rounded-xl bg-[#155d50] px-4 py-2 text-sm font-bold text-white hover:bg-[#0f463c]">Terapkan</button>
                            <a href="{{ route('piket.rekap-jurnal', ['tab' => 'rekap_guru']) }}" class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50">Reset</a>
                        </div>
                    </form>
                @else
                    {{-- Filter Tab Riwayat Jurnal: Tanggal, Kelas, Guru, Validasi, Pencarian --}}
                    <form method="GET" action="{{ route('piket.rekap-jurnal') }}" class="grid gap-3 md:grid-cols-2 xl:grid-cols-6 items-end">
                        <input type="hidden" name="tab" value="jurnal">
                        <div>
                            <label for="tanggal" class="mb-1 block text-xs font-bold text-slate-600">Tanggal</label>
                            <input id="tanggal" type="date" name="tanggal" value="{{ $tanggal }}" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm outline-none focus:border-emerald-500">
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-bold text-slate-600">Kelas</label>
                            <x-searchable-select name="kelas_id" :options="$opsiKelasRekap" :selected="$kelasId === 'all' ? null : $kelasId" placeholder="Semua kelas" />
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-bold text-slate-600">Guru</label>
                            <x-searchable-select name="guru_id" :options="$opsiGuruRekap" :selected="$guruId === 'all' ? null : $guruId" placeholder="Semua guru" />
                        </div>
                        <div>
                            <label for="validasi" class="mb-1 block text-xs font-bold text-slate-600">Validasi</label>
                            <select id="validasi" name="validasi" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-emerald-500">
                                <option value="all">Semua status</option>
                                <option value="disetujui" @selected($validasi === 'disetujui')>Tervalidasi</option>
                                <option value="belum_divalidasi" @selected($validasi === 'belum_divalidasi')>Menunggu validasi</option>
                                <option value="ditolak" @selected($validasi === 'ditolak')>Perlu revisi</option>
                            </select>
                        </div>
                        <div>
                            <label for="search" class="mb-1 block text-xs font-bold text-slate-600">Pencarian</label>
                            <input id="search" type="search" name="search" value="{{ $search }}" placeholder="Materi, mapel..." class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm outline-none focus:border-emerald-500">
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="submit" class="w-full rounded-xl bg-[#155d50] px-4 py-2 text-sm font-bold text-white hover:bg-[#0f463c]">Terapkan</button>
                            <a href="{{ route('piket.rekap-jurnal', ['tab' => 'jurnal']) }}" class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50">Reset</a>
                        </div>
                    </form>
                @endif
            </section>

            @if($tab === 'rekap_kelas')
                @php
                    $displayTglKelas = ($tanggalMulai && $tanggalSelesai && $tanggalMulai !== $tanggalSelesai)
                        ? \Carbon\Carbon::parse($tanggalMulai)->translatedFormat('d M Y') . ' s/d ' . \Carbon\Carbon::parse($tanggalSelesai)->translatedFormat('d M Y')
                        : \Carbon\Carbon::parse($tanggalMulai ?? $tanggal)->translatedFormat('d F Y');
                    $linkTglKelas = $tanggalMulai ?? $tanggal;
                @endphp
                <section class="mt-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-100 px-5 py-4"><h2 class="font-extrabold text-slate-800">Rekap kelas · {{ $displayTglKelas }}</h2><p class="mt-1 text-xs text-slate-500">Buka kelas untuk melihat sesi dan status persetujuan guru piket.</p></div>
                    <div class="divide-y divide-slate-100">
                        @forelse($rekapJadwalKelas as $classSummary)
                            <article class="flex flex-col gap-3 p-4 sm:flex-row sm:items-center sm:justify-between sm:p-5"><div><h3 class="font-extrabold text-slate-800">Kelas {{ $classSummary->nama_kelas }}</h3><p class="mt-1 text-sm text-slate-500">Terisi {{ $classSummary->total_terisi }}/{{ $classSummary->total_sesi }} · tervalidasi pengurus {{ $classSummary->total_tervalidasi }}/{{ $classSummary->total_sesi }}</p>@if($classSummary->persetujuan)<p class="mt-1 text-xs font-bold text-emerald-700">Disetujui piket oleh {{ $classSummary->persetujuan->piket?->name ?? '-' }}</p>@endif</div><a href="{{ route('piket.jurnal-kelas.sessions', ['kelas' => $classSummary->id_kelas, 'tanggal' => $linkTglKelas]) }}" class="inline-flex items-center justify-center gap-1.5 rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50"><i class="bi bi-eye"></i>Lihat Sesi</a></article>
                        @empty
                            <div class="px-5 py-12 text-center text-sm text-slate-500">Tidak ada kelas yang cocok.</div>
                        @endforelse
                    </div>
                </section>
            @elseif($tab === 'rekap_guru')
                @php
                    $displayTglGuru = ($tanggalMulai && $tanggalSelesai && $tanggalMulai !== $tanggalSelesai)
                        ? \Carbon\Carbon::parse($tanggalMulai)->translatedFormat('d M Y') . ' s/d ' . \Carbon\Carbon::parse($tanggalSelesai)->translatedFormat('d M Y')
                        : \Carbon\Carbon::parse($tanggalMulai ?? $tanggal)->translatedFormat('d F Y');
                @endphp
                <section class="mt-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-100 px-5 py-4"><h2 class="font-extrabold text-slate-800">Rekap pengisian guru · {{ $displayTglGuru }}</h2><p class="mt-1 text-xs text-slate-500">Rekap jurnal pada rentang tanggal yang dipilih.</p></div>
                    <div class="divide-y divide-slate-100">
                        @forelse($rekapGuru as $rekap)
                            <article class="flex flex-col gap-3 p-4 sm:flex-row sm:items-center sm:justify-between sm:p-5"><div><h3 class="font-extrabold text-slate-800">{{ $rekap->name }}</h3><p class="mt-1 text-sm text-slate-500">{{ $rekap->terisi }} jurnal terisi dari {{ $rekap->terjadwal }} sesi terjadwal</p></div><span class="w-fit rounded-full bg-slate-100 px-3 py-1.5 text-xs font-extrabold text-slate-700">{{ $rekap->persentase }}%</span></article>
                        @empty
                            <div class="px-5 py-12 text-center text-sm text-slate-500">Tidak ada data guru yang cocok.</div>
                        @endforelse
                    </div>
                </section>
            @else
                <section class="mt-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-100 px-5 py-4"><h2 class="font-extrabold text-slate-800">Daftar jurnal</h2><p class="mt-1 text-xs text-slate-500">{{ $jurnals->count() }} jurnal ditemukan. Tekan jurnal untuk melihat pengisian lengkapnya.</p></div>
                    <div class="divide-y divide-slate-100">
                        @forelse($jurnals as $jurnal)
                            @php $validation = $jurnal->status_validasi ?? 'belum_divalidasi'; $statusClass = $validation === 'disetujui' ? 'bg-emerald-100 text-emerald-800' : ($validation === 'ditolak' ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800'); $statusLabel = $validation === 'disetujui' ? 'Tervalidasi' : ($validation === 'ditolak' ? 'Perlu revisi' : 'Menunggu validasi'); @endphp
                            <a href="{{ route('piket.jurnal.show', $jurnal) }}" class="block p-4 transition hover:bg-slate-50 sm:p-5"><div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between"><div class="min-w-0"><h3 class="font-extrabold text-slate-800">{{ $jurnal->guru?->name ?? 'Guru tidak ditemukan' }}</h3><p class="mt-1 text-sm text-slate-500">{{ $jurnal->kelas?->nama_kelas ?? '-' }} · {{ $jurnal->mapel?->nama_mapel ?? '-' }} · Jam ke-{{ $jurnal->jam_ke }}</p><p class="mt-2 line-clamp-1 text-xs text-slate-600"><strong>Materi:</strong> {{ $jurnal->materi ?: '-' }}</p></div><div class="flex shrink-0 flex-wrap items-center gap-2"><span class="text-xs font-semibold text-slate-500">{{ \Carbon\Carbon::parse($jurnal->tanggal)->translatedFormat('d M Y') }}</span><span class="rounded-full px-2.5 py-1 text-[11px] font-bold {{ $statusClass }}">{{ $statusLabel }}</span></div></div></a>
                            <a href="{{ route('piket.jurnal.show', ['jurnal' => $jurnal, 'back_url' => url()->full()]) }}" class="block p-4 transition hover:bg-slate-50 sm:p-5"><div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between"><div class="min-w-0"><h3 class="font-extrabold text-slate-800">{{ $jurnal->guru?->name ?? 'Guru tidak ditemukan' }}</h3><p class="mt-1 text-sm text-slate-500">{{ $jurnal->kelas?->nama_kelas ?? '-' }} · {{ $jurnal->mapel?->nama_mapel ?? '-' }} · Jam ke-{{ $jurnal->jam_ke }}</p><p class="mt-2 line-clamp-1 text-xs text-slate-600"><strong>Materi:</strong> {{ $jurnal->materi ?: '-' }}</p></div><div class="flex shrink-0 flex-wrap items-center gap-2"><span class="text-xs font-semibold text-slate-500">{{ \Carbon\Carbon::parse($jurnal->tanggal)->translatedFormat('d M Y') }}</span><span class="rounded-full px-2.5 py-1 text-[11px] font-bold {{ $statusClass }}">{{ $statusLabel }}</span></div></div></a>
                        @empty
                            <div class="px-5 py-12 text-center text-sm text-slate-500">Tidak ada jurnal yang sesuai dengan filter.</div>
                        @endforelse
                    </div>
                </section>
            @endif
        </div>
    </div>
@endsection
