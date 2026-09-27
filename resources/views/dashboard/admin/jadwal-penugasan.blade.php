@extends('layouts.app')

@section('title', 'Jadwal Penugasan Piket - JurnalKita')

@section('sidebar')
    @include('layouts.admin.sidebar')
@endsection

@section('navbar')
    @include('layouts.admin.navbar')
@endsection

@section('content')
@php
    $opsiGuru = $gurus->map(fn ($guru) => [
        'value' => (string) $guru->id,
        'label' => $guru->name.' — '.($guru->nip ?? 'Guru'),
    ])->values();
    $opsiBulan = collect(range(1, 12))->map(fn ($nomorBulan) => [
        'value' => (string) $nomorBulan,
        'label' => \Carbon\Carbon::create()->month($nomorBulan)->translatedFormat('F'),
    ]);
    $opsiTahun = $periodeTersedia->pluck('tahun')->unique()->whenEmpty(fn () => collect([$tahun]))->map(fn ($tahunTersedia) => [
        'value' => (string) $tahunTersedia,
        'label' => (string) $tahunTersedia,
    ])->values();
    $labelGuru = fn ($id): string => data_get($opsiGuru->firstWhere('value', (string) $id), 'label', '');
@endphp

<div x-data="{ scheduleQuery: '', viewDay: 'Semua' }" class="p-6 font-sans sm:p-10 lg:p-8 xl:p-10">
    @if(session('success'))
        <div class="mb-6 flex items-center justify-between rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800 shadow-xs">
            <span class="flex items-center gap-2"><i class="bi bi-check-circle-fill"></i>{{ session('success') }}</span>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700"><i class="bi bi-x-lg"></i></button>
        </div>
    @endif


    <div class="grid gap-6 lg:grid-cols-3">
        <section class="lg:col-span-1">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="mb-5 flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#155d50] text-white shadow-sm"><i class="bi bi-person-gear text-lg"></i></div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Atur Penugasan</h2>
                        <p class="mt-0.5 text-xs text-slate-500">Dua sesi, masing-masing 3 guru dan 1 koordinator.</p>
                    </div>
                </div>
                <form action="{{ route('dashboard.rekap-jurnal.penugasan-piket') }}" method="POST" class="space-y-4">
            @csrf
                    @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $hari)
                        @php
                            $jadwalHari = $penugasanMingguan->get($hari, collect());
                            $guruPagiIds = $jadwalHari->where('tipe', 'guru')->where('shift', 1)->pluck('user_id')->values();
                            $koordinatorPagiId = $jadwalHari->where('tipe', 'koordinator')->where('shift', 1)->first()?->user_id;
                            $guruSiangIds = $jadwalHari->where('tipe', 'guru')->where('shift', 2)->pluck('user_id')->values();
                            $koordinatorSiangId = $jadwalHari->where('tipe', 'koordinator')->where('shift', 2)->first()?->user_id;
                            $wakaTerpilih = $jadwalHari->firstWhere('tipe', 'waka')?->user_id;
                        @endphp
                        <details class="rounded-xl border border-slate-200 bg-slate-50/60" @if($loop->first) open @endif>
                            <summary class="flex cursor-pointer items-center justify-between px-3 py-3 text-sm font-bold text-slate-800"><span>{{ $hari }}</span><i class="bi bi-chevron-down text-xs text-slate-400"></i></summary>
                            <div class="space-y-3 border-t border-slate-200 p-3">
                                <section class="rounded-lg border border-amber-100 bg-amber-50/60 p-3">
                                    <p class="mb-3 text-xs font-bold text-amber-800"><i class="bi bi-sun mr-1"></i>Sesi pagi · 07.00–11.00</p>
                                    <div class="space-y-2.5">
                                        @for($urutan = 0; $urutan < 3; $urutan++)
                                            @include('dashboard.admin.partials.searchable-assignment-field', ['fieldName' => "penugasan[{$hari}][pagi][guru][]", 'label' => 'Guru Piket '.($urutan + 1), 'selectedId' => $guruPagiIds->get($urutan), 'selectedLabel' => $labelGuru($guruPagiIds->get($urutan)), 'options' => $opsiGuru])
                                        @endfor
                                        @include('dashboard.admin.partials.searchable-assignment-field', ['fieldName' => "penugasan[{$hari}][pagi][koordinator]", 'label' => 'Koordinator Pagi', 'selectedId' => $koordinatorPagiId, 'selectedLabel' => $labelGuru($koordinatorPagiId), 'options' => $opsiGuru])
                                    </div>
                                </section>
                                <section class="rounded-lg border border-sky-100 bg-sky-50/60 p-3">
                                    <p class="mb-3 text-xs font-bold text-sky-800"><i class="bi bi-cloud-sun mr-1"></i>Sesi siang · 11.00–15.00</p>
                                    <div class="space-y-2.5">
                                        @for($urutan = 0; $urutan < 3; $urutan++)
                                            @include('dashboard.admin.partials.searchable-assignment-field', ['fieldName' => "penugasan[{$hari}][siang][guru][]", 'label' => 'Guru Piket '.($urutan + 1), 'selectedId' => $guruSiangIds->get($urutan), 'selectedLabel' => $labelGuru($guruSiangIds->get($urutan)), 'options' => $opsiGuru])
                                        @endfor
                                        @include('dashboard.admin.partials.searchable-assignment-field', ['fieldName' => "penugasan[{$hari}][siang][koordinator]", 'label' => 'Koordinator Siang', 'selectedId' => $koordinatorSiangId, 'selectedLabel' => $labelGuru($koordinatorSiangId), 'options' => $opsiGuru])
                                    </div>
                                </section>
                                <section class="rounded-lg border border-emerald-100 bg-emerald-50/60 p-3">
                                    @include('dashboard.admin.partials.searchable-assignment-field', ['fieldName' => "penugasan[{$hari}][waka]", 'label' => 'Piket Waka · 07.00–15.00', 'selectedId' => $wakaTerpilih, 'selectedLabel' => $labelGuru($wakaTerpilih), 'options' => $opsiGuru])
                                </section>
                            </div>
                        </details>
                    @endforeach
                    <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-emerald-600 px-4 py-3 text-xs font-bold text-white transition hover:bg-emerald-700"><i class="bi bi-check2"></i>Simpan Penugasan Mingguan</button>
                </form>
            </div>
        </section>

        <section class="lg:col-span-2 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-5 py-4">
                <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
                    <div><h2 class="text-base font-bold text-slate-900">Lihat Jadwal Penugasan</h2><p class="mt-0.5 text-xs text-slate-500">Pilih periode lalu filter hari untuk melihat jadwal piket PDF pada hari tersebut.</p></div>
                    <span class="w-fit rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700">{{ $penugasanPiket->count() }} hari terjadwal</span>
                </div>
                <form method="GET" class="mt-4 grid gap-2 sm:grid-cols-[1fr_1fr_auto]">
                    <div x-data="searchableSelect(@js($bulan), @js($opsiBulan->firstWhere('value', (string) $bulan)['label'] ?? ''), @js($opsiBulan))" @click.outside="open = false" class="relative"><input type="hidden" name="bulan" x-model="selected"><div class="relative"><i class="bi bi-search pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i><input x-model="query" @focus="open = true" @input="selected = ''; open = true" type="search" autocomplete="off" placeholder="Cari bulan" class="w-full rounded-lg border border-slate-300 bg-white py-2 pl-9 pr-3 text-xs text-slate-700 outline-none focus:border-[#155d50] focus:ring-2 focus:ring-[#155d50]/10"></div><div x-cloak x-show="open" x-transition class="absolute z-20 mt-1 max-h-52 w-full overflow-y-auto rounded-lg border border-slate-200 bg-white p-1 shadow-lg"><template x-for="option in filteredOptions()" :key="option.value"><button type="button" @click="choose(option)" class="block w-full rounded-md px-3 py-2 text-left text-xs text-slate-700 hover:bg-emerald-50" x-text="option.label"></button></template></div></div>
                    <div x-data="searchableSelect(@js($tahun), @js((string) $tahun), @js($opsiTahun))" @click.outside="open = false" class="relative"><input type="hidden" name="tahun" x-model="selected"><div class="relative"><i class="bi bi-search pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i><input x-model="query" @focus="open = true" @input="selected = ''; open = true" type="search" autocomplete="off" placeholder="Cari tahun" class="w-full rounded-lg border border-slate-300 bg-white py-2 pl-9 pr-3 text-xs text-slate-700 outline-none focus:border-[#155d50] focus:ring-2 focus:ring-[#155d50]/10"></div><div x-cloak x-show="open" x-transition class="absolute z-20 mt-1 max-h-52 w-full overflow-y-auto rounded-lg border border-slate-200 bg-white p-1 shadow-lg"><template x-for="option in filteredOptions()" :key="option.value"><button type="button" @click="choose(option)" class="block w-full rounded-md px-3 py-2 text-left text-xs text-slate-700 hover:bg-emerald-50" x-text="option.label"></button></template></div></div>
                    <button type="submit" class="rounded-lg bg-slate-800 px-4 py-2 text-xs font-bold text-white transition hover:bg-slate-900">Tampilkan</button>
                </form>
                <div class="relative mt-2"><i class="bi bi-search pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i><input x-model="scheduleQuery" type="search" placeholder="Cari tanggal, hari, atau nama petugas..." class="w-full rounded-lg border border-slate-200 bg-slate-50 py-2.5 pl-9 pr-3 text-xs text-slate-700 outline-none transition focus:border-[#155d50] focus:bg-white focus:ring-2 focus:ring-[#155d50]/10"></div>
                <div class="mt-3 flex flex-wrap gap-1.5" aria-label="Filter hari penugasan">
                    @foreach(['Semua', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $hariFilter)
                        <button type="button" @click="viewDay = '{{ $hariFilter }}'" :class="viewDay === '{{ $hariFilter }}' ? 'bg-[#155d50] text-white shadow-sm' : 'border border-slate-200 bg-white text-slate-600 hover:bg-slate-50'" class="rounded-lg px-3 py-1.5 text-xs font-semibold transition">{{ $hariFilter }}</button>
                    @endforeach
                </div>
            </div>

            <div class="space-y-3 p-4 sm:p-5">
                <div class="flex items-center gap-2 px-1"><i class="bi bi-calendar-date text-slate-400"></i><h3 class="text-sm font-bold text-slate-700">Jadwal Berdasarkan Tanggal</h3></div>
                @forelse($penugasanPiket as $tanggal => $daftar)
                    @php
                        $tanggalCarbon = \Carbon\Carbon::parse($tanggal);
                        $pagi = $daftar->where('tipe', 'guru')->where('shift', 1);
                        $koordinatorPagi = $daftar->where('tipe', 'koordinator')->where('shift', 1)->first();
                        $siang = $daftar->where('tipe', 'guru')->where('shift', 2);
                        $koordinatorSiang = $daftar->where('tipe', 'koordinator')->where('shift', 2)->first();
                        $waka = $daftar->where('tipe', 'waka')->first();
                        $kataKunciPenugasan = strtolower($tanggalCarbon->translatedFormat('l d F Y').' '.$daftar->pluck('user.name')->filter()->join(' '));
                    @endphp
                    <article x-show="(viewDay === 'Semua' || $el.dataset.day === viewDay) && (!scheduleQuery || $el.dataset.search.includes(scheduleQuery.toLowerCase()))" data-day="{{ $tanggalCarbon->translatedFormat('l') }}" data-search="{{ $kataKunciPenugasan }}" class="rounded-xl border border-slate-200 p-4 transition hover:border-emerald-200 hover:bg-emerald-50/20">
                        <div class="mb-3 flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 pb-3"><h3 class="text-sm font-bold text-slate-800">{{ $tanggalCarbon->translatedFormat('l, d F Y') }}</h3><span class="rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-semibold text-slate-600">Piket KBM</span></div>
                        <div class="grid gap-3 sm:grid-cols-2">
                            <div><p class="mb-1.5 text-[11px] font-bold uppercase tracking-wide text-slate-400">Piket pagi · 07.00–11.00</p>@include('dashboard.admin.partials.penugasan-names', ['assignments' => $pagi])</div>
                            <div><p class="mb-1.5 text-[11px] font-bold uppercase tracking-wide text-slate-400">Koordinator pagi</p>@include('dashboard.admin.partials.penugasan-names', ['assignments' => collect([$koordinatorPagi])->filter(), 'coordinator' => true])</div>
                            <div><p class="mb-1.5 text-[11px] font-bold uppercase tracking-wide text-slate-400">Piket siang · 11.00–15.00</p>@include('dashboard.admin.partials.penugasan-names', ['assignments' => $siang])</div>
                            <div><p class="mb-1.5 text-[11px] font-bold uppercase tracking-wide text-slate-400">Koordinator siang</p>@include('dashboard.admin.partials.penugasan-names', ['assignments' => collect([$koordinatorSiang])->filter(), 'coordinator' => true])</div>
                            <div class="sm:col-span-2"><p class="mb-1.5 text-[11px] font-bold uppercase tracking-wide text-slate-400">Piket Waka</p>@include('dashboard.admin.partials.penugasan-names', ['assignments' => collect([$waka])->filter(), 'waka' => true])</div>
                        </div>
                    </article>
                @empty
                    <div class="py-12 text-center text-sm text-slate-400">Belum ada penugasan piket untuk periode ini.</div>
                @endforelse
                <p x-cloak x-show="scheduleQuery && ![...$el.parentElement.querySelectorAll('[data-search]')].some(item => item.dataset.search.includes(scheduleQuery.toLowerCase()))" class="py-8 text-center text-sm text-slate-400">Jadwal yang dicari tidak ditemukan.</p>
            </div>
        </section>
    </div>
</div>
@endsection

<script>
    function searchableSelect(selected, label, options) {
        return {
            selected: selected ? String(selected) : '',
            query: label || '',
            options,
            open: false,
            filteredOptions() {
                const keyword = this.query.toLowerCase().trim();

                return keyword ? this.options.filter((option) => option.label.toLowerCase().includes(keyword)) : this.options;
            },
            choose(option) {
                this.selected = option.value;
                this.query = option.label;
                this.open = false;
            },
            clear() {
                this.selected = '';
                this.query = '';
                this.open = false;
            },
        };
    }
</script>
