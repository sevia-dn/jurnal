@extends('layouts.app')

@section('title', 'Kelola Jurnal Publik - JurnalKita')

@section('sidebar')
    @include('layouts.guru-pengajar.sidebar', ['activePage' => 'piket'])
@endsection

@section('navbar')
    @include('layouts.guru-pengajar.navbar', ['activePage' => 'piket'])
@endsection

@section('content')
<div class="mx-auto max-w-6xl space-y-5 p-4 pb-24 font-sans sm:p-6 lg:p-8">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <a href="{{ route('dashboard.piket') }}" class="inline-flex items-center gap-1.5 rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs font-bold text-emerald-700 transition hover:bg-emerald-100"><i class="bi bi-arrow-left"></i>Kembali</a>
            <h1 class="mt-2 text-2xl font-extrabold text-slate-900">Kelola Jurnal Publik &amp; Riwayat</h1>
            <p class="mt-1 text-sm text-slate-500">Edit atau hapus jurnal yang telah disetujui. Perubahan langsung terlihat di halaman publik.</p>
        </div>
        <a href="{{ route('public.jurnal') }}" target="_blank" class="inline-flex items-center gap-2 rounded-xl border border-emerald-200 bg-white px-4 py-2.5 text-sm font-bold text-emerald-800 hover:bg-emerald-50"><i class="bi bi-box-arrow-up-right"></i>Lihat Halaman Publik</a>
    </div>

    @if (session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-semibold text-emerald-800"><i class="bi bi-check-circle-fill mr-2"></i>{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800"><ul class="list-inside list-disc">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    <form method="GET" action="{{ route('piket.jurnal-publik.index') }}" class="grid gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:grid-cols-[1fr_12rem_auto] sm:items-end">
        <label class="text-xs font-bold text-slate-600">Cari jurnal
            <input type="search" name="search" value="{{ request('search') }}" placeholder="Materi, guru, kelas, mapel" class="mt-1.5 w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm font-normal focus:border-emerald-600 focus:outline-none">
        </label>
        <label class="text-xs font-bold text-slate-600">Tanggal
            <input type="date" name="tanggal" value="{{ request('tanggal') }}" class="mt-1.5 w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm font-normal focus:border-emerald-600 focus:outline-none">
        </label>
        <button class="rounded-xl bg-emerald-700 px-5 py-2.5 text-sm font-bold text-white hover:bg-emerald-800">Terapkan</button>
    </form>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-5 py-4 text-sm font-bold text-slate-700">{{ $journals->total() }} jurnal disetujui</div>
        <div class="divide-y divide-slate-100">
            @forelse ($journals as $journal)
                <article class="p-5 sm:p-6">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-bold uppercase tracking-wide text-emerald-700">{{ $journal->kelas?->nama_kelas ?? 'Kelas' }} · {{ $journal->mapel?->nama_mapel ?? 'Mapel' }} · {{ \Carbon\Carbon::parse($journal->tanggal)->translatedFormat('d F Y') }}</p>
                            <h2 class="mt-1 font-extrabold text-slate-900">{{ $journal->guru?->name ?? 'Guru' }}</h2>
                            <p class="mt-2 whitespace-pre-line text-sm text-slate-700">{{ $journal->materi }}</p>
                            <p class="mt-1 text-xs text-slate-500">{{ $journal->keterangan ?: $journal->catatan ?: 'Tidak ada keterangan tambahan.' }}</p>
                        </div>
                        <div class="flex shrink-0 gap-2">
                            <a href="{{ route('piket.jurnal.show', $journal) }}" class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-bold text-slate-600 hover:bg-slate-50"><i class="bi bi-eye mr-1"></i>Detail</a>
                            <form method="POST" action="{{ route('piket.jurnal-publik.destroy', $journal) }}" onsubmit="return confirm('Hapus jurnal ini dari publik dan riwayat? Data absensi jurnal ini juga akan terhapus.');">
                                @csrf @method('DELETE')
                                <button class="rounded-lg bg-rose-50 px-3 py-2 text-xs font-bold text-rose-700 hover:bg-rose-100"><i class="bi bi-trash mr-1"></i>Hapus</button>
                            </form>
                        </div>
                    </div>
                    <details class="mt-4 rounded-xl border border-slate-200 bg-slate-50">
                        <summary class="cursor-pointer px-4 py-3 text-xs font-bold text-emerald-800"><i class="bi bi-pencil-square mr-1"></i>Edit jurnal</summary>
                        <form method="POST" action="{{ route('piket.jurnal-publik.update', $journal) }}" class="grid gap-3 border-t border-slate-200 p-4">
                            @csrf @method('PUT')
                            <label class="text-xs font-bold text-slate-600">Materi
                                <textarea name="materi" rows="3" maxlength="200" required class="mt-1 w-full rounded-lg border border-slate-200 bg-white p-3 text-sm font-normal">{{ $journal->materi }}</textarea>
                            </label>
                            <div class="grid gap-3 sm:grid-cols-2">
                                <label class="text-xs font-bold text-slate-600">Keterangan
                                    <input name="keterangan" maxlength="200" value="{{ $journal->keterangan }}" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm font-normal">
                                </label>
                                <label class="text-xs font-bold text-slate-600">Catatan
                                    <input name="catatan" maxlength="200" value="{{ $journal->catatan }}" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm font-normal">
                                </label>
                            </div>
                            <div class="flex justify-end"><button class="rounded-lg bg-emerald-700 px-4 py-2 text-xs font-bold text-white hover:bg-emerald-800"><i class="bi bi-floppy mr-1"></i>Simpan Perubahan</button></div>
                        </form>
                    </details>
                </article>
            @empty
                <div class="px-6 py-14 text-center"><i class="bi bi-journal-x text-3xl text-slate-300"></i><p class="mt-3 font-bold text-slate-700">Belum ada jurnal yang disetujui.</p></div>
            @endforelse
        </div>
        @if ($journals->hasPages())<div class="border-t border-slate-100 px-5 py-4">{{ $journals->links() }}</div>@endif
    </section>
</div>
@endsection
