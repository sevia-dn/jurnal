@extends('layouts.app')

@section('content')
<div class="w-full max-w-xl md:max-w-3xl lg:max-w-5xl mx-auto bg-[#f4faf7] min-h-screen p-4 md:p-8 pb-28 font-sans text-gray-800">

    <!-- Header UI -->
    <div class="flex justify-between items-center mb-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('sekretaris.jurnal.index') }}" class="bg-white text-[#0d6e59] p-2 rounded-xl shadow-sm hover:bg-[#e4f3ed] transition border border-[#d6ebe3]">
                <i class="bi bi-arrow-left text-lg"></i>
            </a>
            <div>
                <h1 class="text-xl md:text-2xl font-bold text-[#0d6e59]">Notifikasi Sekretariat</h1>
                <p class="text-xs text-[#5e7e75] hidden sm:block">Pemberitahuan validasi jurnal mengajar dan kehadiran guru</p>
            </div>
        </div>

        @if($countUnread > 0)
            <form action="{{ route('sekretaris.notifikasi.read-all') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="text-xs md:text-sm font-semibold text-[#0d6e59] hover:underline bg-white px-3 py-1.5 rounded-xl border border-[#d6ebe3] shadow-2xs hover:bg-[#eaf6f2] transition cursor-pointer flex items-center gap-1.5">
                    <i class="bi bi-check2-all"></i>
                    <span>Tandai Semua Dibaca</span>
                </button>
            </form>
        @else
            <span class="text-xs text-slate-400 bg-slate-100 px-3 py-1 rounded-lg">
                Semua Sudah Dibaca
            </span>
        @endif
    </div>

    <!-- Alert Sukses Flash Message -->
    @if(session('success'))
        <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-xs flex items-center justify-between shadow-2xs">
            <div class="flex items-center gap-2">
                <i class="bi bi-check-circle-fill text-emerald-600 text-sm"></i>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 cursor-pointer">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    @endif

    <!-- Filter Tab (Responsive Scrollable) -->
    <div class="flex gap-2 overflow-x-auto pb-2 mb-4 scrollbar-none">
        <a href="{{ route('sekretaris.notifikasi', ['tab' => 'all']) }}" 
           class="{{ ($tab ?? 'all') === 'all' ? 'bg-[#0d6e59] text-white shadow-sm' : 'bg-white text-gray-600 hover:bg-gray-50 border border-gray-100 shadow-2xs' }} text-xs md:text-sm font-semibold px-4 py-2 rounded-xl whitespace-nowrap transition !no-underline flex items-center gap-1.5">
            <span>Semua</span>
            <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ ($tab ?? 'all') === 'all' ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-600' }}">{{ $countSemua }}</span>
        </a>

        <a href="{{ route('sekretaris.notifikasi', ['tab' => 'unread']) }}" 
           class="{{ ($tab ?? '') === 'unread' ? 'bg-[#0d6e59] text-white shadow-sm' : 'bg-white text-gray-600 hover:bg-gray-50 border border-gray-100 shadow-2xs' }} text-xs md:text-sm font-semibold px-4 py-2 rounded-xl whitespace-nowrap transition !no-underline flex items-center gap-1.5">
            <span>Belum Dibaca</span>
            <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ ($tab ?? '') === 'unread' ? 'bg-white/20 text-white' : 'bg-amber-100 text-amber-800' }}">{{ $countUnread }}</span>
        </a>

        <a href="{{ route('sekretaris.notifikasi', ['tab' => 'validasi']) }}" 
           class="{{ ($tab ?? '') === 'validasi' ? 'bg-[#0d6e59] text-white shadow-sm' : 'bg-white text-gray-600 hover:bg-gray-50 border border-gray-100 shadow-2xs' }} text-xs md:text-sm font-semibold px-4 py-2 rounded-xl whitespace-nowrap transition !no-underline flex items-center gap-1.5">
            <span>Validasi Guru Piket</span>
            <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ ($tab ?? '') === 'validasi' ? 'bg-white/20 text-white' : 'bg-emerald-100 text-emerald-800' }}">{{ $countValidasi }}</span>
        </a>

        <a href="{{ route('sekretaris.notifikasi', ['tab' => 'izin']) }}" 
           class="{{ ($tab ?? '') === 'izin' ? 'bg-[#0d6e59] text-white shadow-sm' : 'bg-white text-gray-600 hover:bg-gray-50 border border-gray-100 shadow-2xs' }} text-xs md:text-sm font-semibold px-4 py-2 rounded-xl whitespace-nowrap transition !no-underline flex items-center gap-1.5">
            <span>Izin & Sakit</span>
            <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ ($tab ?? '') === 'izin' ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-600' }}">{{ $countIzin }}</span>
        </a>
    </div>

    <!-- List Notifikasi Container -->
    <div class="space-y-3">
        @forelse($notifikasis as $item)
            @php
                $isUnread = !$item->is_read;
                
                // Style icon & warna berdasarkan tipe
                if ($item->tipe === 'validasi') {
                    $iconClass = 'bi-journal-check';
                    $iconBoxClass = 'bg-[#e4f3ed] text-[#0d6e59]';
                    $borderClass = $isUnread ? 'border-[#0d6e59]' : 'border-emerald-300';
                    $badgeText = 'Validasi Selesai';
                    $badgeClass = 'bg-emerald-100 text-emerald-800 border-emerald-200';
                } elseif ($item->tipe === 'inval') {
                    $iconClass = 'bi-person-badge-fill';
                    $iconBoxClass = 'bg-amber-100 text-amber-700';
                    $borderClass = $isUnread ? 'border-amber-500' : 'border-amber-300';
                    $badgeText = 'Guru Inval';
                    $badgeClass = 'bg-amber-100 text-amber-800 border-amber-200';
                } elseif ($item->tipe === 'izin') {
                    $iconClass = 'bi-exclamation-circle-fill';
                    $iconBoxClass = 'bg-amber-100 text-amber-600';
                    $borderClass = $isUnread ? 'border-amber-400' : 'border-slate-300';
                    $badgeText = 'Izin Guru';
                    $badgeClass = 'bg-amber-50 text-amber-700 border-amber-200';
                } elseif ($item->tipe === 'sakit') {
                    $iconClass = 'bi-person-x-fill';
                    $iconBoxClass = 'bg-red-50 text-red-500';
                    $borderClass = $isUnread ? 'border-red-400' : 'border-slate-300';
                    $badgeText = 'Guru Sakit';
                    $badgeClass = 'bg-red-50 text-red-700 border-red-200';
                } else {
                    $iconClass = 'bi-info-circle-fill';
                    $iconBoxClass = 'bg-sky-50 text-sky-600';
                    $borderClass = $isUnread ? 'border-sky-400' : 'border-slate-300';
                    $badgeText = 'Informasi';
                    $badgeClass = 'bg-sky-50 text-sky-700 border-sky-200';
                }

                $cardBg = $isUnread ? 'bg-white shadow-sm hover:shadow-md' : 'bg-white/75 shadow-2xs opacity-90';
            @endphp

            <div class="{{ $cardBg }} p-4 rounded-2xl border-l-4 {{ $borderClass }} transition relative">
                <div class="flex items-start gap-3 md:gap-4">
                    <!-- Icon Box -->
                    <div class="{{ $iconBoxClass }} p-2.5 md:p-3 rounded-xl shrink-0 text-lg md:text-xl flex items-center justify-center">
                        <i class="bi {{ $iconClass }}"></i>
                    </div>

                    <!-- Konten Notifikasi -->
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center justify-between gap-1.5 mb-1.5">
                            <div class="flex items-center gap-2">
                                <h4 class="text-xs md:text-sm font-bold text-gray-800 {{ $isUnread ? 'text-gray-900' : 'text-gray-700' }}">
                                    {{ $item->judul }}
                                </h4>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold border {{ $badgeClass }}">
                                    {{ $badgeText }}
                                </span>
                                @if($isUnread)
                                    <span class="w-2 h-2 rounded-full bg-[#0d6e59]" title="Belum Dibaca"></span>
                                @endif
                            </div>
                            <span class="text-[10px] md:text-xs text-gray-400 whitespace-nowrap">
                                <i class="bi bi-clock mr-1"></i>{{ $item->created_at ? $item->created_at->diffForHumans() : '-' }}
                            </span>
                        </div>

                        <p class="text-xs md:text-sm text-gray-600 leading-relaxed mb-3">
                            {{ $item->pesan }}
                        </p>

                        <!-- Tombol Aksi -->
                        <div class="flex items-center gap-2">
                            @if($isUnread)
                                <form action="{{ route('sekretaris.notifikasi.read', $item->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="bg-[#0d6e59] text-white text-[11px] md:text-xs font-semibold px-3 py-1.5 rounded-lg hover:bg-[#095243] transition flex items-center gap-1.5 cursor-pointer shadow-2xs">
                                        <i class="bi bi-check2"></i> Tandai Dibaca
                                    </button>
                                </form>
                            @else
                                <span class="text-[11px] text-gray-400 flex items-center gap-1">
                                    <i class="bi bi-check2-all text-emerald-600"></i> Sudah dibaca
                                </span>
                            @endif

                            @if($item->id_jurnal)
                                <a href="{{ route('sekretaris.jadwal') }}" class="text-[11px] md:text-xs font-semibold text-[#0d6e59] hover:underline bg-[#e4f3ed]/60 px-3 py-1.5 rounded-lg border border-[#d6ebe3] !no-underline flex items-center gap-1">
                                    <i class="bi bi-eye"></i> Lihat Jadwal & Jurnal
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white p-12 rounded-2xl text-center border border-dashed border-gray-200">
                <i class="bi bi-bell-slash text-4xl text-gray-300 mb-3 block"></i>
                <h3 class="font-bold text-gray-700 text-sm">Tidak Ada Notifikasi</h3>
                <p class="text-xs text-gray-400 mt-1">Belum ada notifikasi pada kategori ini.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($notifikasis->hasPages())
        <div class="mt-6 flex justify-center">
            {{ $notifikasis->links() }}
        </div>
    @endif

    <!-- Bottom Navigation Bar Sekre -->
    <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-100 py-2.5 px-6 flex justify-around items-center max-w-xl md:max-w-3xl lg:max-w-5xl mx-auto shadow-lg z-50">
        <a href="{{ route('sekretaris.jurnal.index') }}" class="flex flex-col items-center gap-0.5 text-gray-500 hover:text-[#0d6e59] !no-underline">
            <i class="bi bi-grid text-sm md:text-base"></i>
            <span class="text-[10px] md:text-xs font-medium">Dashboard</span>
        </a>
        <a href="{{ route('sekretaris.jadwal') }}" class="flex flex-col items-center gap-0.5 text-gray-500 hover:text-[#0d6e59] !no-underline">
            <i class="bi bi-journal-text text-sm md:text-base"></i>
            <span class="text-[10px] md:text-xs font-medium">Rekap Sesi</span>
        </a>
        <a href="{{ route('sekretaris.notifikasi') }}" class="flex flex-col items-center gap-0.5 bg-[#a3e3cd] text-[#0d6e59] px-5 py-1.5 rounded-2xl !no-underline relative">
            <i class="bi bi-bell-fill text-sm md:text-base"></i>
            <span class="text-[10px] md:text-xs font-bold">Notifikasi</span>
            @if($countUnread > 0)
                <span class="absolute top-1 right-3 bg-red-500 text-white text-[9px] font-bold w-4 h-4 rounded-full flex items-center justify-center">
                    {{ $countUnread > 9 ? '9+' : $countUnread }}
                </span>
            @endif
        </a>
        <a href="{{ route('catatan-jurnal') }}" class="flex flex-col items-center gap-0.5 text-gray-500 hover:text-[#0d6e59] !no-underline" title="Catatan Jurnal Guru Piket">
            <i class="bi bi-clipboard-data text-sm md:text-base"></i>
            <span class="text-[10px] md:text-xs font-medium">Monitoring</span>
        </a>
    </div>

</div>
@endsection
