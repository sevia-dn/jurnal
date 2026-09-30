<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Dispensasi - {{ $dispensasi->siswas->isNotEmpty() ? $dispensasi->siswas->count().' Siswa' : $dispensasi->nama }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('img/logo-mark-64.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('img/apple-touch-icon.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: white !important;
                padding: 0 !important;
            }
            .surat-container {
                box-shadow: none !important;
                border: none !important;
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }
        }
    </style>
</head>
<body class="bg-slate-100 font-serif text-slate-900 py-8 px-4">
    @php
        $siswasDispensasi = $dispensasi->siswas->isNotEmpty()
            ? $dispensasi->siswas
            : collect([$dispensasi->siswa])->filter();
    @endphp

    <!-- Action Bar (Hidden on Print) -->
    <div class="no-print max-w-3xl mx-auto mb-6 flex items-center justify-between bg-white p-4 rounded-xl shadow-sm border border-slate-200">
        <div class="flex items-center gap-3">
            <a href="{{ route('piket.dispensasi.history') }}" class="inline-flex items-center gap-2 text-sm font-sans font-semibold text-slate-600 hover:text-slate-900">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
            <span class="h-4 w-px bg-slate-200"></span>
            <div class="flex items-center gap-2 font-sans">
                <img src="{{ asset('img/logo-rounded.png') }}" alt="Logo JurnalKita" class="h-6 w-6 rounded object-cover">
                <span class="text-xs font-bold text-slate-700">JurnalKita</span>
            </div>
        </div>
        <div class="flex items-center gap-3 font-sans">
            <span class="text-xs text-slate-500">ID Dispensasi: #{{ $dispensasi->id }}</span>
            <button onclick="window.print()" class="inline-flex items-center gap-2 bg-emerald-700 hover:bg-emerald-800 text-white font-semibold text-sm px-5 py-2.5 rounded-lg shadow transition cursor-pointer">
                <i class="bi bi-printer-fill"></i> Cetak / Simpan PDF
            </button>
        </div>
    </div>

    <!-- Container Surat -->
    <div class="surat-container max-w-3xl mx-auto bg-white p-10 sm:p-14 shadow-lg border border-slate-200 rounded-sm">

        <!-- KOP SURAT RESMI -->
        <div class="border-b-4 border-double border-slate-900 pb-4 text-center">
            <h3 class="text-xs sm:text-sm tracking-widest font-sans uppercase font-bold text-slate-700">Pemerintah Provinsi Jawa Timur</h3>
            <h3 class="text-xs sm:text-sm tracking-widest font-sans uppercase font-bold text-slate-700">Dinas Pendidikan</h3>
            <h1 class="text-xl sm:text-2xl font-bold uppercase tracking-wide text-slate-900 mt-1">SMK NEGERI 1 BOYOLANGU</h1>
            <p class="text-xs sm:text-sm font-sans text-slate-600 mt-1">
                Jl. Ki Mangun Sarkoro VI/3, Dusun Serut, Boyolangu, Tulungagung, Jawa Timur 66233
            </p>
            <p class="text-xs font-sans text-slate-500">
                Telepon: (0355) 321798 | Laman: smkn1boyolangu.sch.id | Surel: info@smkn1boyolangu.sch.id
            </p>
        </div>

        <!-- JUDUL SURAT -->
        <div class="mt-8 text-center">
            <h2 class="text-lg sm:text-xl font-bold uppercase tracking-wider underline">SURAT KETERANGAN DISPENSASI</h2>
            <p class="text-xs sm:text-sm font-sans text-slate-600 mt-1">
                Nomor: 421.5/DISP-{{ str_pad($dispensasi->id, 4, '0', STR_PAD_LEFT) }}/SMKN1/{{ date('Y') }}
            </p>
        </div>

        <!-- ISI SURAT -->
        <div class="mt-8 text-sm sm:text-base leading-relaxed space-y-4 font-sans">
            <p>
                Yang bertanda tangan di bawah ini, Wakil Kepala Sekolah Bidang Kesiswaan SMK Negeri 1 Boyolangu, dengan ini menerangkan bahwa:
            </p>

            <div class="my-4 overflow-hidden rounded-lg border border-slate-300 text-sm sm:text-base">
                <table class="w-full border-collapse text-left"><thead class="bg-slate-100 text-xs uppercase text-slate-600"><tr><th class="w-12 px-3 py-2">No.</th><th class="px-3 py-2">Nama Siswa</th><th class="px-3 py-2">NIS</th><th class="px-3 py-2">Kelas</th></tr></thead><tbody class="divide-y divide-slate-200">@foreach($siswasDispensasi as $index => $siswa)<tr><td class="px-3 py-2">{{ $index + 1 }}</td><td class="px-3 py-2 font-bold">{{ $siswa->nama }}</td><td class="px-3 py-2 font-mono">{{ $siswa->nis ?? '-' }}</td><td class="px-3 py-2">{{ $siswa->kelas?->nama_kelas ?? '-' }}</td></tr>@endforeach</tbody></table>
            </div>
            <div class="ml-4 sm:ml-8 my-4 space-y-2 text-sm sm:text-base">
                <div class="grid grid-cols-12">
                    <span class="col-span-4 font-semibold text-slate-700">Keperluan Dispensasi</span>
                    <span class="col-span-8 font-semibold text-emerald-800 uppercase">: {{ ucwords(str_replace('_', ' ', $dispensasi->jenis_dispensasi)) }}</span>
                </div>
                <div class="grid grid-cols-12">
                    <span class="col-span-4 font-semibold text-slate-700">Waktu Dispensasi</span>
                    <span class="col-span-8 text-slate-900 font-semibold">:
                        {{ $dispensasi->deskripsi_waktu }}
                    </span>
                </div>
                <div class="grid grid-cols-12">
                    <span class="col-span-4 font-semibold text-slate-700">Alasan / Keterangan</span>
                    <span class="col-span-8 italic text-slate-800">: "{{ $dispensasi->alasan }}"</span>
                </div>
            </div>

            <p class="pt-2">
                Diberikan izin/dispensasi untuk meninggalkan atau tidak mengikuti kegiatan belajar mengajar (KBM) pada tanggal dan waktu tersebut di atas.
            </p>

            <p>
                Demikian surat keterangan dispensasi ini dibuat dengan sebenarnya untuk dipergunakan sebagaimana mestinya dan kepada bapak/ibu guru pengajar mohon untuk dapat memakluminya.
            </p>
        </div>

        <!-- STATUS VALIDASI ELEKTRONIK -->
        <div class="mt-8 p-4 bg-slate-50 border border-slate-200 rounded-lg text-xs font-sans flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-xl font-bold shrink-0">
                    <i class="bi bi-shield-check"></i>
                </div>
                <div>
                    <p class="font-bold text-slate-800 uppercase tracking-wide">DISAHKAN SECARA RESMI OLEH WAKASEK KESISWAAN</p>
                    <p class="text-slate-500">Status: <span class="text-emerald-700 font-bold uppercase">{{ $dispensasi->status_akhir }}</span> | ID: <span class="font-mono">#DISP-{{ $dispensasi->id }}</span></p>
                </div>
            </div>
            <div class="text-right text-slate-500 text-[11px]">
                <p>Tgl Pengesahan: <strong class="text-slate-700">{{ $dispensasi->diproses_at ? $dispensasi->diproses_at->format('d/m/Y H:i') : '-' }} WIB</strong></p>
                <p class="text-slate-400">Verifikasi Sistem: <span class="font-mono">{{ substr($dispensasi->token_verifikasi ?? $dispensasi->token_approval ?? 'VALID', 0, 16) }}...</span></p>
            </div>
        </div>

        <!-- TANDA TANGAN & QR VALIDASI -->
        <div class="mt-10 grid grid-cols-2 text-center text-sm font-sans gap-8 items-end">
            <div>
                <p class="text-slate-500 text-xs">Guru Piket yang Mengajukan,</p>
                <div class="h-24 flex items-center justify-center">
                    <span class="text-xs text-emerald-700 font-mono italic px-3 py-1 bg-emerald-50 rounded border border-emerald-200">[ Diverifikasi Piket ]</span>
                </div>
                <p class="font-bold text-slate-900 underline">{{ $dispensasi->pembuat?->name ?? 'Guru Piket' }}</p>
                <p class="text-xs text-slate-500">NIP: {{ $dispensasi->pembuat?->nip ?? '-' }}</p>
            </div>

            <div>
                <p class="text-slate-500 text-xs">Boyolangu, {{ ($dispensasi->diproses_at ?? now())->format('d F Y') }}</p>
                <p class="text-slate-500 text-xs">Wakasek Kesiswaan,</p>
                <div class="h-24 flex flex-col items-center justify-center py-1">
                    @if($dispensasi->token_verifikasi)
                        <img 
                            src="https://api.qrserver.com/v1/create-qr-code/?size=160x160&margin=0&format=png&data={{ urlencode(route('dispensasi.verify', ['token' => $dispensasi->token_verifikasi])) }}" 
                            alt="QR Verifikasi Resmi"
                            class="h-16 w-16 p-0.5 border border-slate-300 rounded bg-white"
                        >
                        <span class="text-[9px] text-slate-400 font-mono mt-0.5">Scan untuk Verifikasi</span>
                    @else
                        <span class="text-xs text-purple-700 font-mono italic px-3 py-1 bg-purple-50 rounded border border-purple-200">[ Tervalidasi Sistem ]</span>
                    @endif
                </div>
                <p class="font-bold text-slate-900 underline">{{ $dispensasi->pemroses?->name ?? 'Fajar Siswanto, S.Pd' }}</p>
                <p class="text-xs text-slate-500">NIP: {{ $dispensasi->pemroses?->nip ?? '198501012010011003' }}</p>
            </div>
        </div>

    </div>

</body>
</html>
