@extends('layouts.app')

@section('title', 'Halaman Utama Guru - JurnalKita')

@section('sidebar')
    @include('layouts.guru-pengajar.sidebar', ['activePage' => 'utama'])
@endsection

@section('navbar')
    @include('layouts.guru-pengajar.navbar', ['activePage' => 'utama'])
@endsection

@section('content')
    @php
        $students = [
            ['number' => '01', 'name' => 'Aisyah Nurhaliza'],
            ['number' => '02', 'name' => 'Bagas Pratama'],
            ['number' => '03', 'name' => 'Citra Lestari'],
            ['number' => '04', 'name' => 'Dimas Saputra'],
            ['number' => '05', 'name' => 'Fajar Ramadhan'],
            ['number' => '06', 'name' => 'Gilang Maulana'],
        ];
    @endphp

    <style>[x-cloak] { display: none !important; }</style>

    <div x-data="{ hasCheckedIn: false, isWithinSchedule: true, showForm: false, statusKehadiran: 'hadir', namaGuru: '', nipGuru: '19871212 201001 1 001' }" class="mx-auto w-full max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <section class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-semibold text-emerald-700">Senin, 14 September 2026</p>
                <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Halaman Utama Guru</h1>
                <p class="mt-2 text-sm text-slate-500">Kelola kehadiran dan logbook pembelajaran Anda hari ini.</p>
            </div>
            <span class="w-fit rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700">SMKN 1 Boyolangu</span>
        </section>

        <section class="mt-6 rounded-2xl bg-white p-5 shadow-md sm:p-6" aria-labelledby="lapor-kehadiran-guru">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div class="flex items-start gap-3">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-xl text-emerald-700">
                        <i class="bi bi-person-check-fill" aria-hidden="true"></i>
                    </span>
                    <div>
                        <p class="text-sm font-semibold text-emerald-700">Kehadiran Guru</p>
                        <h2 id="lapor-kehadiran-guru" class="mt-1 text-xl font-bold text-slate-900">Lapor Kehadiran Guru</h2>
                        <p class="mt-1 text-sm text-slate-500">Lakukan absen masuk sebelum memulai pembelajaran.</p>
                    </div>
                </div>
                <span x-show="!hasCheckedIn" class="w-fit rounded-full bg-amber-50 px-3 py-1.5 text-xs font-semibold text-amber-700">Belum absen</span>
                <span x-cloak x-show="hasCheckedIn" class="w-fit rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700">Sudah absen</span>
            </div>

            <div class="mt-6 overflow-hidden rounded-xl border border-slate-200">
                <div class="grid grid-cols-[96px_minmax(0,1fr)] border-b border-slate-100 bg-emerald-50/60 text-sm sm:grid-cols-[130px_minmax(0,1fr)_160px]">
                    <div class="flex items-center border-r border-emerald-100 px-4 py-4 font-bold text-emerald-800">Jam 1 - 2</div>
                    <div class="px-4 py-4"><p class="font-semibold text-slate-800">XI RPL 2 - Informatika</p><p class="mt-1 text-xs text-slate-500">07.00 - 08.20 WIB</p></div>
                    <span class="hidden items-center justify-center text-xs font-semibold text-emerald-700 sm:flex">Jadwal aktif</span>
                </div>
                <div class="grid grid-cols-[96px_minmax(0,1fr)] border-b border-slate-100 text-sm sm:grid-cols-[130px_minmax(0,1fr)_160px]">
                    <div class="flex items-center border-r border-slate-100 px-4 py-4 font-semibold text-slate-500">Jam 3 - 4</div>
                    <div class="px-4 py-4"><p class="font-medium text-slate-700">XI RPL 1 - Informatika</p><p class="mt-1 text-xs text-slate-500">08.20 - 09.40 WIB</p></div>
                    <span class="hidden items-center justify-center text-xs text-slate-400 sm:flex">Berikutnya</span>
                </div>
                <div class="grid grid-cols-[96px_minmax(0,1fr)] text-sm sm:grid-cols-[130px_minmax(0,1fr)_160px]">
                    <div class="flex items-center border-r border-slate-100 px-4 py-4 font-semibold text-slate-500">Jam 5 - 10</div>
                    <div class="px-4 py-4"><p class="font-medium text-slate-700">Jadwal berikutnya</p><p class="mt-1 text-xs text-slate-500">Lihat agenda mengajar untuk detail sesi.</p></div>
                    <span class="hidden items-center justify-center text-xs text-slate-400 sm:flex">Preview</span>
                </div>
            </div>

            <div class="mt-5 flex flex-col gap-3 rounded-xl bg-slate-50 p-4 sm:flex-row sm:items-center sm:justify-between">
                <p class="flex items-start gap-2 text-xs leading-relaxed text-slate-500"><i class="bi bi-info-circle-fill mt-0.5 text-emerald-600" aria-hidden="true"></i><span>Data absensi masuk akan diteruskan ke monitoring Piket dan Admin.</span></p>
                <button type="button" @click="showForm = true" x-show="!hasCheckedIn" class="inline-flex shrink-0 items-center justify-center gap-2 rounded-lg bg-emerald-600 px-4 py-3 text-sm font-medium text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:ring-offset-2">
                    <i class="bi bi-box-arrow-in-right" aria-hidden="true"></i>
                    Absen Masuk / Lapor Kehadiran
                </button>
                <span x-cloak x-show="hasCheckedIn" class="inline-flex shrink-0 items-center gap-2 rounded-lg bg-emerald-100 px-4 py-3 text-sm font-semibold text-emerald-700"><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Absen masuk tercatat</span>
            </div>

            <form x-cloak
                    x-show="showForm"
                    x-transition
                    @submit.prevent="hasCheckedIn = true; showForm = false"
                    class="mt-6 rounded-2xl border border-emerald-100 bg-white p-5 shadow-md sm:p-6"
                    aria-labelledby="form-lapor-kehadiran">
                <div class="flex flex-col gap-3 border-b border-slate-100 pb-5 sm:flex-row sm:items-start sm:justify-between">
                    <div class="flex items-start gap-3">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700"><i class="bi bi-clipboard2-check-fill" aria-hidden="true"></i></span>
                        <div>
                            <p class="text-sm font-semibold text-emerald-700">Form Kehadiran</p>
                            <h3 id="form-lapor-kehadiran" class="mt-1 text-lg font-bold text-slate-900">Lapor Kehadiran</h3>
                            <p class="mt-1 text-sm text-slate-500">Lengkapi laporan kehadiran Anda sebelum mengisi logbook.</p>
                        </div>
                    </div>
                    <span class="w-fit rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700">Hari ini</span>
                </div>

                <div class="mt-5 grid gap-5 sm:grid-cols-2">
                    <label for="nama-guru" class="block">
                        <span class="text-sm font-semibold text-slate-700">Nama</span>
                        <input id="nama-guru" x-model="namaGuru" type="text" required placeholder="Masukkan nama lengkap" class="mt-2 w-full rounded-lg border border-slate-200 px-4 py-3 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-emerald-600 focus:ring-4 focus:ring-emerald-100">
                    </label>
                    <label for="nip-guru" class="block">
                        <span class="text-sm font-semibold text-slate-700">NIP</span>
                        <input id="nip-guru" :value="namaGuru ? nipGuru : ''" type="text" readonly placeholder="Terisi otomatis setelah nama diisi" class="mt-2 w-full cursor-not-allowed rounded-lg border border-slate-200 bg-slate-100 px-4 py-3 text-sm font-medium text-slate-600 outline-none placeholder:font-normal placeholder:text-slate-400">
                    </label>
                    <label for="status-kehadiran" class="block">
                        <span class="text-sm font-semibold text-slate-700">Status Kehadiran</span>
                        <select id="status-kehadiran" x-model="statusKehadiran" class="mt-2 w-full rounded-lg border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-emerald-600 focus:ring-4 focus:ring-emerald-100">
                            <option value="hadir">Hadir</option>
                            <option value="tidak-hadir">Tidak Hadir</option>
                        </select>
                    </label>
                    <label for="alasan-ketidakhadiran" class="block">
                        <span class="text-sm font-semibold text-slate-700">Alasan Ketidakhadiran</span>
                        <select id="alasan-ketidakhadiran" :disabled="statusKehadiran === 'hadir'" :class="statusKehadiran === 'hadir' ? 'cursor-not-allowed border-slate-200 bg-slate-100 text-slate-400' : 'border-slate-200 bg-white text-slate-700 focus:border-emerald-600 focus:ring-4 focus:ring-emerald-100'" class="mt-2 w-full rounded-lg px-4 py-3 text-sm outline-none transition">
                            <option value="">Pilih alasan ketidakhadiran</option>
                            <option value="sakit">Sakit</option>
                            <option value="izin">Izin</option>
                            <option value="keperluan-dinas">Keperluan dinas</option>
                        </select>
                    </label>
                    <label for="bukti-kehadiran" class="flex cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed border-emerald-200 bg-emerald-50 px-5 py-8 text-center transition hover:bg-emerald-100 sm:col-span-2">
                        <i class="bi bi-cloud-arrow-up-fill text-3xl text-emerald-700" aria-hidden="true"></i>
                        <span class="mt-3 text-sm font-bold text-emerald-800">Unggah Bukti Kehadiran</span>
                        <span class="mt-1 text-xs text-emerald-700">Tambahkan foto atau dokumen dokumentasi real-time.</span>
                        <input id="bukti-kehadiran" type="file" accept="image/*,.pdf" capture="environment" class="sr-only">
                    </label>
                </div>

                <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                    <button type="button" @click="showForm = false" class="rounded-lg px-5 py-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-slate-300">Batal</button>
                    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-lg bg-emerald-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:ring-offset-2"><i class="bi bi-send-fill" aria-hidden="true"></i>Kirim Laporan</button>
                </div>
            </form>
        </section>

        <section class="mt-8" aria-labelledby="isi-logbook">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700"><i class="bi bi-journal-text" aria-hidden="true"></i></span>
                <div>
                    <h2 id="isi-logbook" class="text-xl font-bold text-slate-900">Isi Logbook Mengajar</h2>
                    <p class="mt-1 text-sm text-slate-500">Jurnal pembelajaran untuk jadwal aktif Anda.</p>
                </div>
            </div>

            <div x-show="!hasCheckedIn" class="mt-4 rounded-xl border border-amber-200 bg-amber-50 p-4 text-amber-900" role="alert">
                <div class="flex items-start gap-3"><i class="bi bi-lock-fill mt-0.5 text-amber-700" aria-hidden="true"></i><div><p class="text-sm font-bold">Silakan Absen Masuk Terlebih Dahulu</p><p class="mt-1 text-xs leading-relaxed text-amber-800">Form logbook akan terbuka setelah kehadiran guru tercatat pada jadwal aktif.</p></div></div>
            </div>

            <div x-cloak x-show="hasCheckedIn && !isWithinSchedule" class="mt-4 rounded-xl border border-amber-200 bg-amber-50 p-4 text-amber-900" role="alert">
                <div class="flex items-start gap-3"><i class="bi bi-clock-fill mt-0.5 text-amber-700" aria-hidden="true"></i><div><p class="text-sm font-bold">Form Logbook hanya aktif saat jam pelajaran berlangsung (07.00 - 08.20)</p><p class="mt-1 text-xs leading-relaxed text-amber-800">Silakan kembali saat sesi kelas aktif untuk mengisi jurnal.</p></div></div>
            </div>

            <form x-cloak
                    x-show="hasCheckedIn && isWithinSchedule"
                    action="{{ route('guru.riwayat') }}"
                    method="GET"
                    class="mt-4 space-y-6"
                    @submit.prevent="window.location.href='{{ route('guru.riwayat') }}'">
                <div class="rounded-2xl bg-white p-5 shadow-md sm:p-6">
                    <div class="grid gap-5 sm:grid-cols-2">
                        <label class="block"><span class="text-sm font-semibold text-slate-700">Mata Pelajaran</span><input type="text" value="Informatika" readonly class="mt-2 w-full cursor-not-allowed rounded-lg border border-slate-200 bg-slate-100 px-4 py-3 text-sm font-medium text-slate-600 outline-none"></label>
                        <label class="block"><span class="text-sm font-semibold text-slate-700">Kelas</span><input type="text" value="XI RPL 2" readonly class="mt-2 w-full cursor-not-allowed rounded-lg border border-slate-200 bg-slate-100 px-4 py-3 text-sm font-medium text-slate-600 outline-none"></label>
                        <label class="block"><span class="text-sm font-semibold text-slate-700">Jam Pelajaran</span><input type="text" value="Jam ke 1-2" readonly class="mt-2 w-full cursor-not-allowed rounded-lg border border-slate-200 bg-slate-100 px-4 py-3 text-sm font-medium text-slate-600 outline-none"></label>
                        <label class="block sm:col-span-2"><span class="text-sm font-semibold text-slate-700">Materi / Pembahasan</span><input type="text" placeholder="Contoh: Pengenalan struktur data array" class="mt-2 w-full rounded-lg border border-slate-200 px-4 py-3 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-emerald-600 focus:ring-4 focus:ring-emerald-100"></label>
                    </div>
                </div>

                <div class="overflow-hidden rounded-2xl bg-white shadow-md">
                    <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-5 py-4 sm:px-6"><div><h3 class="font-bold text-slate-900">Absensi Siswa Roster</h3><p class="mt-1 text-xs text-slate-500">Status awal setiap siswa adalah Hadir (H).</p></div><span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">XI RPL 2</span></div>
                    <div class="max-h-72 overflow-y-auto custom-scrollbar divide-y divide-slate-100">
                        @foreach ($students as $student)
                            <div x-data="{ status: 'H' }" class="flex items-center justify-between gap-3 px-5 py-4 sm:px-6">
                                <div class="flex min-w-0 items-center gap-3"><span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-emerald-50 text-xs font-bold text-emerald-700">{{ $student['number'] }}</span><p class="truncate text-sm font-semibold text-slate-700">{{ $student['name'] }}</p></div>
                                <div class="flex shrink-0 gap-1" aria-label="Status kehadiran {{ $student['name'] }}">
                                    <button type="button" @click="status = 'H'" :class="status === 'H' ? 'bg-emerald-600 text-white' : 'bg-emerald-50 text-emerald-700'" class="flex h-8 w-8 items-center justify-center rounded-full text-xs font-bold transition">H</button>
                                    <button type="button" @click="status = 'S'" :class="status === 'S' ? 'bg-amber-500 text-white' : 'bg-amber-50 text-amber-700'" class="flex h-8 w-8 items-center justify-center rounded-full text-xs font-bold transition">S</button>
                                    <button type="button" @click="status = 'I'" :class="status === 'I' ? 'bg-sky-500 text-white' : 'bg-sky-50 text-sky-700'" class="flex h-8 w-8 items-center justify-center rounded-full text-xs font-bold transition">I</button>
                                    <button type="button" @click="status = 'A'" :class="status === 'A' ? 'bg-rose-500 text-white' : 'bg-rose-50 text-rose-700'" class="flex h-8 w-8 items-center justify-center rounded-full text-xs font-bold transition">A</button>
                                    <button type="button" @click="status = 'D'" :class="status === 'D' ? 'bg-violet-500 text-white' : 'bg-violet-50 text-violet-700'" class="flex h-8 w-8 items-center justify-center rounded-full text-xs font-bold transition">D</button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="rounded-2xl bg-white p-5 shadow-md sm:p-6">
                    <label for="catatan-khusus" class="block"><span class="text-sm font-semibold text-slate-700">Catatan Khusus / Hambatan Kelas</span><textarea id="catatan-khusus" rows="4" placeholder="Tulis catatan atau hambatan selama pembelajaran..." class="mt-2 w-full resize-none rounded-lg border border-slate-200 px-4 py-3 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-emerald-600 focus:ring-4 focus:ring-emerald-100"></textarea></label>
                    <label for="foto-bukti-kelas" class="mt-5 flex cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed border-emerald-200 bg-emerald-50 px-5 py-8 text-center transition hover:bg-emerald-100"><i class="bi bi-camera-fill text-2xl text-emerald-700" aria-hidden="true"></i><span class="mt-3 text-sm font-bold text-emerald-800">Unggah Foto Bukti Kehadiran di Kelas</span><span class="mt-1 text-xs text-emerald-700">Gunakan dokumentasi langsung aktivitas pembelajaran.</span><input id="foto-bukti-kelas" type="file" accept="image/*" capture="environment" class="sr-only"></label>
                </div>

                <button type="submit"
                        class="flex w-full items-center justify-center gap-2 rounded-lg bg-emerald-600 px-5 py-3.5 text-sm font-medium text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:ring-offset-2">
                    <i class="bi bi-send-fill" aria-hidden="true"></i>
                    Kirim Logbook
                </button>
            </form>
        </section>
    </div>

@endsection
