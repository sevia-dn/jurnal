@extends('layouts.app')

@section('title', 'Lapor Kehadiran Guru - Piket JurnalKita')

@section('sidebar')
    @include('layouts.guru-pengajar.sidebar', ['activePage' => 'piket'])
@endsection

@section('navbar')
    @include('layouts.guru-pengajar.navbar', ['activePage' => 'piket'])
@endsection

@section('content')
    <div class="min-h-full bg-slate-50 p-4 pb-24 font-sans sm:p-6 lg:p-8">
        <div class="mx-auto max-w-xl">
            <a href="{{ route('dashboard.piket') }}" class="mb-4 inline-flex items-center gap-2 text-sm font-semibold text-emerald-700 transition hover:text-emerald-800">
                <i class="bi bi-arrow-left"></i>Kembali ke halaman utama piket
            </a>

            <section id="form-kehadiran-guru" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                <div class="flex items-start gap-3 border-b border-slate-100 pb-4">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-700">
                        <i class="bi bi-person-exclamation text-lg"></i>
                    </span>
                    <div>
                        <h1 class="font-bold text-slate-800">Lapor Kehadiran Guru</h1>
                        <p class="mt-1 text-xs leading-relaxed text-slate-500">Gunakan formulir ini untuk mencatat guru yang berhalangan hadir (sakit atau izin) pada hari ini.</p>
                    </div>
                </div>

                @if(session('success'))
                    <div class="mt-5 flex items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-sm font-medium text-emerald-800">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mt-5 flex items-center gap-2 rounded-xl border border-rose-200 bg-rose-50 p-3 text-sm font-medium text-rose-800">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mt-5 rounded-xl border border-rose-200 bg-rose-50 p-3 text-sm text-rose-800">
                        <p class="font-bold">Data belum dapat disimpan:</p>
                        <ul class="mt-1 list-inside list-disc text-xs">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('piket.kehadiran.store') }}" method="POST" class="mt-5 space-y-4">
                    @csrf
                    {{-- SEARCHABLE GURU DROPDOWN --}}
                    <div
                        x-data="{
                            open: false,
                            search: '',
                            selectedId: '{{ old('user_id', '') }}',
                            selectedName: '',
                            teachers: [
                                @foreach($gurus as $guru)
                                    {
                                        id: '{{ $guru->id }}',
                                        name: '{{ addslashes($guru->name) }}',
                                        nip: '{{ addslashes($guru->nip ?? '') }}'
                                    },
                                @endforeach
                            ],
                            init() {
                                const found = this.teachers.find(t => String(t.id) === String(this.selectedId));
                                if (found) {
                                    this.selectedName = found.name + (found.nip ? ' (' + found.nip + ')' : '');
                                }
                            },
                            filteredTeachers() {
                                if (!this.search.trim()) return this.teachers;
                                const q = this.search.toLowerCase();
                                return this.teachers.filter(t => t.name.toLowerCase().includes(q) || (t.nip && t.nip.toLowerCase().includes(q)));
                            },
                            selectTeacher(t) {
                                this.selectedId = t.id;
                                this.selectedName = t.name + (t.nip ? ' (' + t.nip + ')' : '');
                                this.open = false;
                                this.search = '';
                            }
                        }"
                        class="relative"
                    >
                        <input type="hidden" name="user_id" :value="selectedId" required>

                        <label class="block">
                            <span class="text-xs font-bold text-slate-700">Guru <span class="text-rose-500">*</span></span>
                            <button
                                type="button"
                                @click="open = !open; if (open) $nextTick(() => $refs.searchInput.focus())"
                                class="mt-1.5 flex w-full items-center justify-between rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-left text-sm text-slate-700 shadow-xs outline-none transition focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100"
                                :class="{ 'border-emerald-500 ring-4 ring-emerald-100': open }"
                            >
                                <span x-text="selectedName || 'Pilih guru...'" :class="selectedName ? 'font-medium text-slate-800' : 'text-slate-400'"></span>
                                <i class="bi bi-chevron-down text-xs text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                            </button>
                        </label>

                        <!-- DROPDOWN DENGAN SEARCH BAR -->
                        <div
                            x-show="open"
                            @click.outside="open = false"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            class="absolute left-0 right-0 z-30 mt-1 rounded-2xl border border-slate-200 bg-white p-2 shadow-xl"
                            style="display: none;"
                        >
                            <!-- Search Bar Input -->
                            <div class="relative mb-2">
                                <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
                                <input
                                    x-ref="searchInput"
                                    x-model="search"
                                    type="text"
                                    placeholder="Ketik nama atau NIP guru..."
                                    class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2 pl-8 pr-7 text-xs text-slate-700 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-100"
                                    @keydown.escape="open = false"
                                >
                                <button
                                    type="button"
                                    x-show="search.length > 0"
                                    @click="search = ''; $refs.searchInput.focus()"
                                    class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600"
                                >
                                    <i class="bi bi-x-circle-fill text-xs"></i>
                                </button>
                            </div>

                            <!-- List Guru Terfilter -->
                            <div class="max-h-56 overflow-y-auto space-y-0.5">
                                <template x-for="guru in filteredTeachers()" :key="guru.id">
                                    <button
                                        type="button"
                                        @click="selectTeacher(guru)"
                                        class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-left text-xs transition hover:bg-emerald-50 hover:text-emerald-900"
                                        :class="selectedId === guru.id ? 'bg-emerald-50 font-bold text-emerald-900' : 'text-slate-700'"
                                    >
                                        <div>
                                            <p class="font-semibold" x-text="guru.name"></p>
                                            <p class="text-[11px] text-slate-400" x-show="guru.nip" x-text="'NIP: ' + guru.nip"></p>
                                        </div>
                                        <i class="bi bi-check-lg text-emerald-600 font-bold" x-show="selectedId === guru.id"></i>
                                    </button>
                                </template>
                                <div x-show="filteredTeachers().length === 0" class="py-4 text-center text-xs text-slate-400">
                                    <i class="bi bi-person-x text-lg mb-1 block"></i>
                                    Guru tidak ditemukan
                                </div>
                            </div>
                        </div>
                    </div>

                    <label class="block">
                        <span class="text-xs font-bold text-slate-700">Status <span class="text-rose-500">*</span></span>
                        <select name="status" required class="mt-1.5 w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-700 outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100">
                            <option value="Sakit" @selected(old('status') === 'Sakit')>Sakit</option>
                            <option value="Izin" @selected(old('status') === 'Izin')>Izin</option>
                        </select>
                    </label>

                    <label class="block">
                        <span class="text-xs font-bold text-slate-700">Keterangan <span class="text-rose-500">*</span></span>
                        <textarea name="keterangan" required rows="4" maxlength="500" class="mt-1.5 w-full resize-y rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-700 outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100" placeholder="Contoh: Sakit demam dan beristirahat di rumah.">{{ old('keterangan') }}</textarea>
                    </label>

                    <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 py-3 text-sm font-bold text-white transition hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-100">
                        <i class="bi bi-save"></i>Simpan Laporan
                    </button>
                </form>
            </section>
        </div>
    </div>
@endsection

