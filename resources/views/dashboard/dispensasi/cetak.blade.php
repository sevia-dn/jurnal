<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Dispensasi - {{ $dispensasi->nama }}</title>
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

    <!-- Action Bar (Hidden on Print) -->
    <div class="no-print max-w-3xl mx-auto mb-6 flex items-center justify-between bg-white p-4 rounded-xl shadow-sm border border-slate-200">
        <a href="javascript:history.back()" class="inline-flex items-center gap-2 text-sm font-sans font-semibold text-slate-600 hover:text-slate-900">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
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

            <!-- Biodata Siswa -->
            <div class="ml-4 sm:ml-8 my-4 space-y-2 text-sm sm:text-base">
                <div class="grid grid-cols-12">
                    <span class="col-span-4 font-semibold text-slate-700">Nama Siswa</span>
                    <span class="col-span-8 font-bold text-slate-900">: {{ $dispensasi->siswa?->nama ?? $dispensasi->nama }}</span>
                </div>
                <div class="grid grid-cols-12">
                    <span class="col-span-4 font-semibold text-slate-700">NIS</span>
                    <span class="col-span-8 font-mono text-slate-900">: {{ $dispensasi->siswa?->nis ?? '-' }}</span>
                </div>
                <div class="grid grid-cols-12">
                    <span class="col-span-4 font-semibold text-slate-700">Kelas</span>
                    <span class="col-span-8 font-bold text-slate-900">: {{ $dispensasi->siswa?->kelas?->nama_kelas ?? 'Umum' }}</span>
                </div>
                <div class="grid grid-cols-12">
                    <span class="col-span-4 font-semibold text-slate-700">Keperluan Dispensasi</span>
                    <span class="col-span-8 font-semibold text-emerald-800 uppercase">: {{ ucwords(str_replace('_', ' ', $dispensasi->jenis_dispensasi)) }}</span>
                </div>
                <div class="grid grid-cols-12">
                    <span class="col-span-4 font-semibold text-slate-700">Berlaku Tanggal</span>
                    <span class="col-span-8 text-slate-900 font-semibold">: 
                        {{ $dispensasi->tanggal ? $dispensasi->tanggal->format('d F Y') : date('d F Y') }}
                        @if($dispensasi->tanggal_selesai && $dispensasi->tanggal?->format('Y-m-d') !== $dispensasi->tanggal_selesai->format('Y-m-d'))
                            s/d {{ $dispensasi->tanggal_selesai->format('d F Y') }}
                        @endif
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
                <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-xl font-bold">
                    <i class="bi bi-shield-check"></i>
                </div>
                <div>
                    <p class="font-bold text-slate-800">DISAHKAN SECARA RESMI OLEH WAKA KESISWAAN</p>
                    <p class="text-slate-500">Status: <span class="text-emerald-700 font-bold uppercase">{{ $dispensasi->status_akhir }}</span> | Token: <span class="font-mono">{{ substr($dispensasi->token_approval ?? 'DISP', 0, 16) }}...</span></p>
                </div>
            </div>
            <div class="text-right text-slate-500">
                Tgl Pengesahan: {{ $dispensasi->diproses_at ? $dispensasi->diproses_at->format('d/m/Y H:i') : '-' }} WIB
            </div>
        </div>

        <!-- TANDA TANGAN -->
        <div class="mt-12 grid grid-cols-2 text-center text-sm font-sans gap-8">
            <div>
                <p class="text-slate-500">Guru Piket yang Mengajukan,</p>
                <div class="h-20 flex items-center justify-center">
                    <span class="text-xs text-emerald-700 font-mono italic">[ Diverifikasi Piket ]</span>
                </div>
                <p class="font-bold text-slate-900 underline">{{ $dispensasi->pembuat?->name ?? 'Guru Piket' }}</p>
                <p class="text-xs text-slate-500">NIP: {{ $dispensasi->pembuat?->nip ?? '-' }}</p>
            </div>

            <div>
                <p class="text-slate-500">Boyolangu, {{ ($dispensasi->diproses_at ?? now())->format('d F Y') }}</p>
                <p class="text-slate-500">Waka Kesiswaan,</p>
                <div class="h-20 flex items-center justify-center">
                    <span class="text-xs text-purple-700 font-mono italic">[ Tervalidasi Sistem ]</span>
                </div>
                <p class="font-bold text-slate-900 underline">{{ $dispensasi->pemroses?->name ?? 'Fajar Siswanto, S.Pd' }}</p>
                <p class="text-xs text-slate-500">NIP: {{ $dispensasi->pemroses?->nip ?? '198501012010011003' }}</p>
            </div>
        </div>

    </div>

</body>
</html>
