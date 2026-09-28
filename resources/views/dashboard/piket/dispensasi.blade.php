@extends('layouts.app')

@section('title', 'Pengajuan Dispensasi - Piket JurnalKita')

@section('sidebar')
    @include('layouts.guru-pengajar.sidebar', ['activePage' => 'piket'])
@endsection

@section('navbar')
    @include('layouts.guru-pengajar.navbar', ['activePage' => 'piket'])
@endsection

@section('content')
    <div class="min-h-full bg-slate-50 p-4 pb-24 font-sans sm:p-6 lg:p-8">
        <div class="mx-auto max-w-2xl">
            <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
                <a href="{{ route('dashboard.piket') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-emerald-700 transition hover:text-emerald-800"><i class="bi bi-arrow-left"></i>Kembali ke halaman utama piket</a>
                <a href="{{ route('piket.dispensasi.history') }}" class="inline-flex items-center gap-1.5 rounded-xl border border-indigo-200 bg-indigo-50 px-3 py-1.5 text-xs font-bold text-indigo-700 hover:bg-indigo-100 transition"><i class="bi bi-clock-history"></i> Riwayat &amp; Pemantauan Dispensasi</a>
            </div>
            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">
                <div class="flex items-start gap-3 border-b border-slate-100 pb-4"><span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-xl text-emerald-700"><i class="bi bi-file-earmark-plus-fill"></i></span><div><h1 class="text-lg font-bold text-slate-900">Form Pengajuan Dispensasi</h1><p class="mt-1 text-xs text-slate-500">Lengkapi pengajuan untuk diteruskan kepada Wakasek Kesiswaan.</p></div></div>
                @if(session('success'))<div class="mt-5 flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800"><i class="bi bi-check-circle-fill"></i>{{ session('success') }}</div>@endif
                @if(session('approval_url'))<div class="mt-5 rounded-xl border border-indigo-200 bg-indigo-50 p-4 text-sm text-indigo-900"><p class="font-bold">Notifikasi Wakasek telah dibuat.</p><a href="{{ session('approval_url') }}" target="_blank" class="mt-2 inline-flex items-center gap-1 font-bold text-indigo-700 hover:underline">Buka tautan validasi <i class="bi bi-box-arrow-up-right"></i></a></div>@endif
                @if($errors->any())<div class="mt-5 rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800"><p class="font-bold">Pengajuan belum dapat disimpan.</p><ul class="mt-1 list-inside list-disc">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
                <form method="POST" action="{{ route('piket.dispensasi.store') }}" enctype="multipart/form-data" class="mt-5 space-y-4">@csrf
                    {{-- MULTI-SISWA SELECTION --}}
                    <div
                        x-data="{
                            open: false,
                            search: '',
                            selectedIds: [],
                            selectedSiswas: [],
                            students: [
                                @foreach($siswas as $siswa)
                                    {
                                        id: '{{ $siswa->id }}',
                                        nama: '{{ addslashes($siswa->nama) }}',
                                        kelas: '{{ addslashes($siswa->kelas?->nama_kelas ?? 'Umum') }}',
                                        nis: '{{ addslashes($siswa->nis ?? '') }}'
                                    },
                                @endforeach
                            ],
                            filteredStudents() {
                                if (!this.search.trim()) return this.students.filter(s => !this.selectedIds.includes(s.id));
                                const q = this.search.toLowerCase();
                                return this.students.filter(s =>
                                    !this.selectedIds.includes(s.id) && (
                                        s.nama.toLowerCase().includes(q) ||
                                        s.kelas.toLowerCase().includes(q) ||
                                        (s.nis && s.nis.toLowerCase().includes(q))
                                    )
                                );
                            },
                            addStudent(s) {
                                if (!this.selectedIds.includes(s.id)) {
                                    this.selectedIds.push(s.id);
                                    this.selectedSiswas.push(s);
                                }
                                this.search = '';
                                this.$nextTick(() => this.$refs.searchInput.focus());
                            },
                            removeStudent(id) {
                                this.selectedIds = this.selectedIds.filter(i => i !== id);
                                this.selectedSiswas = this.selectedSiswas.filter(s => s.id !== id);
                            }
                        }"
                        class="relative"
                    >
                        {{-- Hidden inputs untuk siswa_ids[] --}}
                        <template x-for="id in selectedIds" :key="id">
                            <input type="hidden" name="siswa_ids[]" :value="id">
                        </template>

                        <label class="block">
                            <span class="text-xs font-bold text-slate-700">Siswa <span class="text-rose-500">*</span></span>
                            <p class="text-[11px] text-slate-500 mt-0.5">Bisa pilih lebih dari satu siswa (misal: untuk tim/rombongan).</p>
                        </label>

                        {{-- Chips siswa terpilih --}}
                        <div x-show="selectedSiswas.length > 0" class="mt-2 flex flex-wrap gap-1.5">
                            <template x-for="s in selectedSiswas" :key="s.id">
                                <span class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-800">
                                    <span x-text="s.nama + ' · ' + s.kelas"></span>
                                    <button type="button" @click="removeStudent(s.id)" class="text-emerald-600 hover:text-red-600 transition">
                                        <i class="bi bi-x-lg text-[10px]"></i>
                                    </button>
                                </span>
                            </template>
                        </div>

                        {{-- Input pencarian --}}
                        <div class="relative mt-2">
                            <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
                            <input
                                x-ref="searchInput"
                                x-model="search"
                                type="text"
                                placeholder="Ketik nama, kelas, atau NIS untuk menambah siswa..."
                                @focus="open = true"
                                @input="open = true"
                                @keydown.escape="open = false; search = ''"
                                class="w-full rounded-xl border border-slate-300 bg-white py-2.5 pl-8 pr-8 text-sm text-slate-700 outline-none transition focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100"
                            >
                            <button type="button" x-show="search.length > 0" @click="search = ''; $refs.searchInput.focus()" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                                <i class="bi bi-x-circle-fill text-xs"></i>
                            </button>
                        </div>

                        {{-- Dropdown list --}}
                        <div
                            x-show="open && (search.length > 0 || filteredStudents().length > 0)"
                            @click.outside="open = false"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            class="absolute left-0 right-0 z-30 mt-1 max-h-56 overflow-y-auto rounded-2xl border border-slate-200 bg-white p-1.5 shadow-xl"
                            style="display: none;"
                        >
                            <template x-for="s in filteredStudents().slice(0, 80)" :key="s.id">
                                <button
                                    type="button"
                                    @click="addStudent(s)"
                                    class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-left text-xs transition hover:bg-emerald-50 hover:text-emerald-900 text-slate-700"
                                >
                                    <div class="min-w-0 flex-1">
                                        <p class="font-semibold truncate" x-text="s.nama"></p>
                                        <p class="text-[11px] text-slate-400" x-text="s.kelas + (s.nis ? ' · NIS ' + s.nis : '')"></p>
                                    </div>
                                    <i class="bi bi-plus-circle shrink-0 text-emerald-500"></i>
                                </button>
                            </template>
                            <div x-show="filteredStudents().length === 0 && search.trim().length > 0" class="py-4 text-center text-xs text-slate-400">
                                <i class="bi bi-person-x text-lg mb-1 block"></i>
                                Siswa tidak ditemukan
                            </div>
                        </div>

                        <div x-show="selectedIds.length === 0" class="mt-1 text-[11px] text-rose-500">
                            Pilih setidaknya satu siswa sebelum mengirim pengajuan.
                        </div>
                    </div>
                    <label class="block"><span class="text-xs font-bold text-slate-700">Jenis dispensasi <span class="text-rose-500">*</span></span><select name="jenis_dispensasi" required class="mt-1.5 w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-700 outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100"><option value="">Pilih jenis dispensasi</option>@foreach(['Sakit', 'Izin Keluarga', 'Tugas / Lomba Sekolah', 'Lainnya'] as $jenis)<option value="{{ $jenis }}" @selected(old('jenis_dispensasi') === $jenis)>{{ $jenis }}</option>@endforeach</select></label>

                    <div class="grid gap-4 sm:grid-cols-2"><label class="block"><span class="text-xs font-bold text-slate-700">Tanggal mulai <span class="text-rose-500">*</span></span><input type="date" name="tanggal_mulai" required value="{{ old('tanggal_mulai', now('Asia/Jakarta')->toDateString()) }}" class="mt-1.5 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-700 outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100"></label><label class="block"><span class="text-xs font-bold text-slate-700">Tanggal selesai <span class="text-rose-500">*</span></span><input type="date" name="tanggal_selesai" required value="{{ old('tanggal_selesai', now('Asia/Jakarta')->toDateString()) }}" class="mt-1.5 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-700 outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100"></label></div>
                    <div x-data="{ mode: '{{ old('mode_waktu', 'sepanjang_hari') }}' }"><p class="text-xs font-bold text-slate-700">Rentang waktu <span class="text-rose-500">*</span></p><div class="mt-1.5 grid grid-cols-2 gap-3"><label class="rounded-xl border p-3 text-sm font-semibold" :class="mode === 'sepanjang_hari' ? 'border-emerald-500 bg-emerald-50 text-emerald-800' : 'border-slate-200 text-slate-600'"><input type="radio" name="mode_waktu" value="sepanjang_hari" x-model="mode"> Sepanjang hari</label><label class="rounded-xl border p-3 text-sm font-semibold" :class="mode === 'jam_tertentu' ? 'border-emerald-500 bg-emerald-50 text-emerald-800' : 'border-slate-200 text-slate-600'"><input type="radio" name="mode_waktu" value="jam_tertentu" x-model="mode"> Jam tertentu</label></div><div x-show="mode === 'jam_tertentu'" x-cloak class="mt-3 grid gap-3 rounded-xl bg-slate-50 p-3 sm:grid-cols-2"><label class="block"><span class="text-xs font-bold text-slate-700">Mulai jam ke</span><select name="jam_ke_mulai" :required="mode === 'jam_tertentu'" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm">@foreach($daftarJam as $jam)<option value="{{ $jam->jam_ke }}" @selected(old('jam_ke_mulai') == $jam->jam_ke)>Jam ke-{{ $jam->jam_ke }}</option>@endforeach</select></label><label class="block"><span class="text-xs font-bold text-slate-700">Selesai jam ke</span><select name="jam_ke_selesai" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm"><option value="">Sampai selesai</option>@foreach($daftarJam as $jam)<option value="{{ $jam->jam_ke }}" @selected(old('jam_ke_selesai') == $jam->jam_ke)>Jam ke-{{ $jam->jam_ke }}</option>@endforeach</select></label></div></div>
                    <label class="block"><span class="text-xs font-bold text-slate-700">Alasan <span class="text-rose-500">*</span></span><textarea name="alasan" required rows="4" class="mt-1.5 w-full resize-y rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-700 outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100" placeholder="Jelaskan alasan dispensasi.">{{ old('alasan') }}</textarea></label>
                    <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 py-3 text-sm font-bold text-white transition hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-100"><i class="bi bi-send-fill"></i>Kirim Pengajuan ke Wakasek</button>
                </form>
            </section>
        </div>
    </div>
@endsection
