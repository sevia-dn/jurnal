@extends('layouts.app')

@section('title', 'Pengajuan & Monitoring Dispensasi Siswa')

@section('sidebar')
    @include('layouts.piket.sidebar')
@endsection

@section('navbar')
    @include('layouts.piket.navbar')
@endsection

@section('content')
<div id="piket-dispensasi-page" class="min-h-full bg-slate-50 p-5 pb-24 font-sans sm:p-8 lg:p-10">
    <div class="mx-auto max-w-7xl">

        <!-- Header -->
        <header class="mb-7 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="mb-2 flex items-center gap-2 text-xs font-bold uppercase tracking-[0.18em] text-emerald-700">
                    <span class="h-2 w-2 rounded-full bg-emerald-500 shadow-[0_0_0_4px_rgba(16,185,129,0.12)]"></span>
                    Layanan Piket Siswa
                </div>
                <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">Pengajuan & Monitoring Dispensasi</h1>
                <p class="mt-1 text-sm text-slate-500">Ajukan surat izin/dispensasi dan pantau status persetujuan dari Waka Kesiswaan.</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="rounded-full bg-emerald-50 px-3.5 py-1.5 text-xs font-semibold text-emerald-700 border border-emerald-200">
                    <i class="bi bi-calendar-event me-1"></i> {{ now()->translatedFormat('l, d F Y') }}
                </span>
            </div>
        </header>

        <!-- Flash Messages & Simulasi WhatsApp -->
        @if (session('success'))
            <div class="mb-6 rounded-2xl bg-emerald-50 border border-emerald-300 p-4 text-emerald-900 shadow-sm flex items-center gap-3">
                <i class="bi bi-check-circle-fill text-emerald-600 text-xl shrink-0"></i>
                <div class="text-sm font-medium">{{ session('success') }}</div>
            </div>
        @endif

        @if (session('approval_url'))
            <div class="mb-7 rounded-2xl bg-gradient-to-r from-purple-900 to-indigo-900 text-white p-5 shadow-lg">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-xl bg-purple-700/80 border border-purple-400/30 flex items-center justify-center text-2xl shrink-0 text-purple-200">
                            <i class="bi bi-whatsapp"></i>
                        </div>
                        <div>
                            <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-700 text-purple-100 border border-purple-500/40">Simulasi Notifikasi WA Waka</span>
                            <h4 class="text-base font-bold mt-1">Notifikasi persetujuan siap diuji</h4>
                            <p class="text-xs text-purple-200">Tautan rahasia dengan token approval telah dikirimkan ke log WhatsApp Waka Kesiswaan.</p>
                        </div>
                    </div>
                    <a href="{{ session('approval_url') }}" target="_blank" class="shrink-0 inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-bold shadow transition">
                        <span>Buka Halaman Persetujuan Waka</span>
                        <i class="bi bi-box-arrow-up-right"></i>
                    </a>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 rounded-2xl bg-rose-50 border border-rose-300 p-4 text-rose-800 shadow-sm">
                <p class="font-bold text-sm mb-1 flex items-center gap-2">
                    <i class="bi bi-exclamation-octagon-fill"></i> Terdapat kesalahan pada input formulir:
                </p>
                <ul class="list-disc list-inside text-xs space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Main Grid Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- LEFT COLUMN: Formulir Pengajuan Baru (5 Cols) -->
            <div class="lg:col-span-5 bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-7">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-xl shrink-0">
                        <i class="bi bi-pencil-square"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">Form Pengajuan Dispensasi</h2>
                        <p class="text-xs text-slate-500">Lengkapi identitas siswa dan berkas alasan izin.</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('piket.dispensasi.store') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <!-- Pilih Siswa -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Pilih Siswa <span class="text-rose-500">*</span>
                        </label>
                        <select name="siswa_id" required
                                class="w-full border {{ $errors->has('siswa_id') ? 'border-rose-400 bg-rose-50' : 'border-slate-300' }} rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-4 focus:ring-emerald-100 focus:border-emerald-500 transition bg-white text-slate-800">
                            <option value="">-- Cari Nama Siswa / Kelas --</option>
                            @foreach ($siswas as $siswa)
                                <option value="{{ $siswa->id }}" {{ old('siswa_id') == $siswa->id ? 'selected' : '' }}>
                                    {{ $siswa->nama }} - {{ $siswa->kelas->nama_kelas ?? 'Umum' }} (NIS: {{ $siswa->nis }})
                                </option>
                            @endforeach
                        </select>
                        @error('siswa_id')
                            <p class="mt-1 text-xs text-rose-600 flex items-center gap-1"><i class="bi bi-exclamation-circle"></i> {{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Jenis Dispensasi -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Jenis Dispensasi <span class="text-rose-500">*</span>
                        </label>
                        <select name="jenis_dispensasi" required
                                class="w-full border {{ $errors->has('jenis_dispensasi') ? 'border-rose-400 bg-rose-50' : 'border-slate-300' }} rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-4 focus:ring-emerald-100 focus:border-emerald-500 transition bg-white text-slate-800">
                            <option value="">-- Pilih Jenis Dispensasi --</option>
                            <option value="Sakit" {{ old('jenis_dispensasi') == 'Sakit' ? 'selected' : '' }}>Sakit (Perlu Istirahat / Pulang)</option>
                            <option value="Izin Keluarga" {{ old('jenis_dispensasi') == 'Izin Keluarga' ? 'selected' : '' }}>Izin Keperluan Keluarga</option>
                            <option value="Tugas / Lomba Sekolah" {{ old('jenis_dispensasi') == 'Tugas / Lomba Sekolah' ? 'selected' : '' }}>Tugas / Lomba Mewakili Sekolah</option>
                            <option value="Lainnya" {{ old('jenis_dispensasi') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                        @error('jenis_dispensasi')
                            <p class="mt-1 text-xs text-rose-600 flex items-center gap-1"><i class="bi bi-exclamation-circle"></i> {{ $message }}</p>
                        @enderror
                    </div>

                    <div x-data="{ modeWaktu: '{{ old('mode_waktu', 'sepanjang_hari') }}' }" class="space-y-4">
                        <!-- Tanggal Pelaksanaan -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Tanggal Mulai <span class="text-rose-500">*</span>
                                </label>
                                <input type="date" name="tanggal_mulai" required value="{{ old('tanggal_mulai', date('Y-m-d')) }}"
                                       class="w-full border {{ $errors->has('tanggal_mulai') ? 'border-rose-400 bg-rose-50' : 'border-slate-300' }} rounded-xl px-3.5 py-2 text-sm focus:outline-none focus:ring-4 focus:ring-emerald-100 focus:border-emerald-500 transition">
                                @error('tanggal_mulai')
                                    <p class="mt-1 text-xs text-rose-600 flex items-center gap-1"><i class="bi bi-exclamation-circle"></i> {{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Tanggal Selesai <span class="text-rose-500">*</span>
                                </label>
                                <input type="date" name="tanggal_selesai" required value="{{ old('tanggal_selesai', date('Y-m-d')) }}"
                                       class="w-full border {{ $errors->has('tanggal_selesai') ? 'border-rose-400 bg-rose-50' : 'border-slate-300' }} rounded-xl px-3.5 py-2 text-sm focus:outline-none focus:ring-4 focus:ring-emerald-100 focus:border-emerald-500 transition">
                                @error('tanggal_selesai')
                                    <p class="mt-1 text-xs text-rose-600 flex items-center gap-1"><i class="bi bi-exclamation-circle"></i> {{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Mode Waktu Dispensasi (Radio Toggle) -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Rentang Waktu Dispensasi <span class="text-rose-500">*</span>
                            </label>
                            <div class="grid grid-cols-2 gap-2">
                                <label class="flex items-center gap-2 p-2.5 border rounded-xl cursor-pointer transition text-xs select-none"
                                       :class="modeWaktu === 'sepanjang_hari' ? 'border-emerald-500 bg-emerald-50 text-emerald-900 font-bold' : 'border-slate-200 hover:bg-slate-50 text-slate-700'">
                                    <input type="radio" name="mode_waktu" value="sepanjang_hari" x-model="modeWaktu" class="text-emerald-600 focus:ring-emerald-500">
                                    <span>Sepanjang Hari</span>
                                </label>
                                <label class="flex items-center gap-2 p-2.5 border rounded-xl cursor-pointer transition text-xs select-none"
                                       :class="modeWaktu === 'jam_tertentu' ? 'border-emerald-500 bg-emerald-50 text-emerald-900 font-bold' : 'border-slate-200 hover:bg-slate-50 text-slate-700'">
                                    <input type="radio" name="mode_waktu" value="jam_tertentu" x-model="modeWaktu" class="text-emerald-600 focus:ring-emerald-500">
                                    <span>Jam Tertentu</span>
                                </label>
                            </div>
                            @error('mode_waktu')
                                <p class="mt-1 text-xs text-rose-600 flex items-center gap-1"><i class="bi bi-exclamation-circle"></i> {{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Dropdown Jam (Ditampilkan jika Jam Tertentu dipilih) -->
                        <div x-show="modeWaktu === 'jam_tertentu'" x-cloak class="grid grid-cols-1 sm:grid-cols-2 gap-3 p-3 bg-slate-50 rounded-xl border border-slate-200">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1">
                                    Dari Jam Ke- <span class="text-rose-500">*</span>
                                </label>
                                <select name="jam_ke_mulai" :required="modeWaktu === 'jam_tertentu'"
                                        class="w-full border {{ $errors->has('jam_ke_mulai') ? 'border-rose-400 bg-rose-50' : 'border-slate-300' }} rounded-xl px-3 py-2 text-xs focus:outline-none focus:ring-4 focus:ring-emerald-100 focus:border-emerald-500 transition bg-white text-slate-800">
                                    <option value="">-- Pilih Jam Mulai --</option>
                                    @if(isset($daftarJam))
                                        @foreach ($daftarJam as $jam)
                                            <option value="{{ $jam->jam_ke }}" {{ old('jam_ke_mulai') == $jam->jam_ke ? 'selected' : '' }}>
                                                Jam ke-{{ $jam->jam_ke }} ({{ substr($jam->jam_mulai, 0, 5) }} - {{ substr($jam->jam_selesai, 0, 5) }})
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('jam_ke_mulai')
                                    <p class="mt-1 text-xs text-rose-600 flex items-center gap-1"><i class="bi bi-exclamation-circle"></i> {{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1">
                                    Sampai Jam Ke-
                                </label>
                                <select name="jam_ke_selesai"
                                        class="w-full border {{ $errors->has('jam_ke_selesai') ? 'border-rose-400 bg-rose-50' : 'border-slate-300' }} rounded-xl px-3 py-2 text-xs focus:outline-none focus:ring-4 focus:ring-emerald-100 focus:border-emerald-500 transition bg-white text-slate-800">
                                    <option value="">Sampai selesai / pulang</option>
                                    @if(isset($daftarJam))
                                        @foreach ($daftarJam as $jam)
                                            <option value="{{ $jam->jam_ke }}" {{ old('jam_ke_selesai') == $jam->jam_ke ? 'selected' : '' }}>
                                                Jam ke-{{ $jam->jam_ke }} ({{ substr($jam->jam_mulai, 0, 5) }} - {{ substr($jam->jam_selesai, 0, 5) }})
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('jam_ke_selesai')
                                    <p class="mt-1 text-xs text-rose-600 flex items-center gap-1"><i class="bi bi-exclamation-circle"></i> {{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Alasan -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Alasan Lengkap <span class="text-rose-500">*</span>
                        </label>
                        <textarea name="alasan" rows="3" required placeholder="Tuliskan keterangan detail alasan izin..."
                                  class="w-full border {{ $errors->has('alasan') ? 'border-rose-400 bg-rose-50' : 'border-slate-300' }} rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-4 focus:ring-emerald-100 focus:border-emerald-500 transition">{{ old('alasan') }}</textarea>
                        @error('alasan')
                            <p class="mt-1 text-xs text-rose-600 flex items-center gap-1"><i class="bi bi-exclamation-circle"></i> {{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Bukti Dokumen -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Lampiran Bukti (Opsional)
                        </label>
                        <div class="border-2 border-dashed border-slate-200 hover:border-emerald-400 rounded-xl p-4 text-center cursor-pointer transition bg-slate-50 hover:bg-emerald-50/50">
                            <input type="file" name="bukti" id="bukti-input" class="hidden" accept="image/*,.pdf" onchange="document.getElementById('file-label').textContent = this.files[0]?.name || 'Pilih Berkas'">
                            <label for="bukti-input" class="cursor-pointer block">
                                <i class="bi bi-cloud-arrow-up text-2xl text-emerald-600 block mb-1"></i>
                                <span id="file-label" class="text-xs font-semibold text-slate-700 block">Pilih Foto Surat Dokter / Undangan Lomba</span>
                                <span class="text-[11px] text-slate-400 block mt-0.5">Format: PNG, JPG, PDF (Maks. 10MB)</span>
                            </label>
                        </div>
                        @error('bukti')
                            <p class="mt-1 text-xs text-rose-600 flex items-center gap-1"><i class="bi bi-exclamation-circle"></i> {{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                            class="w-full mt-2 inline-flex items-center justify-center gap-2 bg-emerald-700 hover:bg-emerald-800 text-white font-bold py-3 px-4 rounded-xl shadow transition cursor-pointer">
                        <i class="bi bi-send-fill"></i>
                        <span>Kirim Pengajuan ke Waka</span>
                    </button>
                </form>
            </div>


            <!-- RIGHT COLUMN: Tabel Monitoring & Riwayat (7 Cols) -->
            <div class="lg:col-span-7 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-5 sm:p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center text-xl shrink-0">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-slate-900">Riwayat Dispensasi</h2>
                            <p class="text-xs text-slate-500">Daftar permohonan dispensasi dan status persetujuannya.</p>
                        </div>
                    </div>
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">
                        Total: {{ $dispensasis->total() }} Data
                    </span>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-100">
                            <tr>
                                <th class="px-5 py-3.5">Siswa</th>
                                <th class="px-4 py-3.5">Keperluan & Waktu</th>
                                <th class="px-4 py-3.5">Status Waka</th>
                                <th class="px-4 py-3.5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @forelse($dispensasis as $disp)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <!-- Siswa -->
                                    <td class="px-5 py-4">
                                        <div class="font-bold text-slate-900">{{ $disp->nama }}</div>
                                        <div class="text-xs text-emerald-700 font-semibold">{{ $disp->siswa?->kelas?->nama_kelas ?? 'Umum' }}</div>
                                        <div class="text-[11px] text-slate-400 font-mono">NIS: {{ $disp->siswa?->nis ?? '-' }}</div>
                                    </td>

                                    <!-- Jenis & Waktu (Deskripsi Manusiawi) -->
                                    <td class="px-4 py-4">
                                        <div class="font-semibold text-slate-800">{{ $disp->jenis_dispensasi }}</div>
                                        <div class="text-xs font-semibold text-emerald-700 mt-1 flex items-center gap-1.5">
                                            <i class="bi bi-clock text-[11px]"></i>
                                            <span>{{ $disp->deskripsi_waktu }}</span>
                                        </div>
                                        <div class="text-xs text-slate-400 mt-1 line-clamp-1 italic" title="{{ $disp->alasan }}">
                                            "{{ $disp->alasan }}"
                                        </div>
                                        @if($disp->bukti)
                                            <div class="mt-1.5">
                                                <a href="{{ asset('storage/' . $disp->bukti) }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] font-semibold text-indigo-600 hover:underline">
                                                    <i class="bi bi-paperclip"></i> Lihat Berkas
                                                </a>
                                            </div>
                                        @endif
                                    </td>

                                    <!-- Status Waka -->
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        @if(in_array(strtolower($disp->status_waka), ['disetujui', 'approved']))
                                            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-800 border border-emerald-200">
                                                <i class="bi bi-check-circle-fill text-[11px]"></i> Disetujui
                                            </span>
                                        @elseif(in_array(strtolower($disp->status_waka), ['ditolak', 'rejected']))
                                            <span class="inline-flex items-center gap-1 rounded-full bg-rose-100 px-3 py-1 text-xs font-bold text-rose-800 border border-rose-200">
                                                <i class="bi bi-x-circle-fill text-[11px]"></i> Ditolak
                                            </span>
                                            @if($disp->catatan_waka)
                                                <p class="text-[10px] text-rose-600 mt-1 max-w-[140px] truncate" title="{{ $disp->catatan_waka }}">
                                                    "{{ $disp->catatan_waka }}"
                                                </p>
                                            @endif
                                        @else
                                            <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-800 border border-amber-300">
                                                <i class="bi bi-clock-history text-[11px]"></i> Menunggu
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Aksi -->
                                    <td class="px-4 py-4 text-right whitespace-nowrap">
                                        @if(in_array(strtolower($disp->status_waka), ['disetujui', 'approved']))
                                            <a href="{{ route('dispensasi.cetak', $disp->id) }}" target="_blank"
                                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold shadow-sm transition">
                                                <i class="bi bi-printer-fill"></i> Cetak Surat
                                            </a>
                                        @elseif(!in_array(strtolower($disp->status_waka), ['ditolak', 'rejected']))
                                            <a href="{{ route('dispensasi.approval', ['token' => $disp->token_approval ?? $disp->id]) }}" target="_blank"
                                               class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg border border-purple-300 bg-purple-50 text-purple-700 hover:bg-purple-100 text-xs font-semibold transition" title="Buka Link Approval Waka">
                                                <i class="bi bi-shield-check"></i> Link Waka
                                            </a>
                                        @else
                                            <span class="text-xs text-slate-400 font-mono">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-slate-400">
                                        <i class="bi bi-inbox text-3xl block mb-2 text-slate-300"></i>
                                        <p class="font-medium text-slate-600">Belum ada data pengajuan dispensasi.</p>
                                        <p class="text-xs text-slate-400 mt-1">Gunakan formulir di sebelah kiri untuk membuat permohonan baru.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($dispensasis->hasPages())
                    <div class="p-4 border-t border-slate-100">
                        {{ $dispensasis->links() }}
                    </div>
                @endif
            </div>

        </div>

    </div>
</div>
@endsection