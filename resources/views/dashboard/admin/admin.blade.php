@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('sidebar')
    @include('layouts.admin.sidebar')
@endsection

@section('navbar')
    @include('layouts.admin.navbar')
@endsection

@section('content')
  <div class="p-6 sm:p-10 font-sans">
    
    <!-- Header Section -->
    <div class="mb-8">
      <h1 class="text-2xl font-bold text-gray-900 mb-1">Overview Dashboard</h1>
      <p class="text-sm text-gray-500">Ringkasan aktivitas dan operasional sekolah hari ini, {{ now()->translatedFormat('d F Y') }}</p>
    </div>

    <!-- Stat Cards Section -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
      
      <!-- Card 1: Guru -->
      <a href="{{ route('dashboard.guru') }}" class="block group !no-underline">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center gap-5 transition-all duration-300 group-hover:shadow-md group-hover:border-emerald-300 group-hover:-translate-y-1">
          <div class="w-14 h-14 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center text-2xl">
            <i class="bi bi-people"></i>
          </div>
          <div>
            <div class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">JUMLAH GURU</div>
            <div class="text-3xl font-bold !text-gray-800">{{ $jumlahGuru ?? 55 }}</div>
          </div>
        </div>
      </a>

      <!-- Card 2: Kelas -->
      <a href="{{ route('dashboard.kelas') }}" class="block group !no-underline">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center gap-5 transition-all duration-300 group-hover:shadow-md group-hover:border-emerald-300 group-hover:-translate-y-1">
          <div class="w-14 h-14 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center text-2xl">
            <i class="bi bi-building"></i>
          </div>
          <div>
            <div class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">JUMLAH KELAS</div>
            <div class="text-3xl font-bold !text-gray-800">{{ $jumlahKelas ?? 11 }}</div>
          </div>
        </div>
      </a>

      <!-- Card 3: Mapel -->
      <a href="{{ route('dashboard.mapel') }}" class="block group !no-underline">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center gap-5 transition-all duration-300 group-hover:shadow-md group-hover:border-emerald-300 group-hover:-translate-y-1">
          <div class="w-14 h-14 bg-purple-50 text-purple-600 rounded-2xl flex items-center justify-center text-2xl">
            <i class="bi bi-journal-bookmark"></i>
          </div>
          <div>
            <div class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">MATA PELAJARAN</div>
            <div class="text-3xl font-bold !text-gray-800">{{ $jumlahMapel ?? 35 }}</div>
          </div>
        </div>
      </a>

      <!-- Card 4: Jurnal -->
      <a href="{{ route('dashboard.rekap-jurnal') }}" class="block group !no-underline">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center gap-5 transition-all duration-300 group-hover:shadow-md group-hover:border-emerald-300 group-hover:-translate-y-1">
          <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center text-2xl">
            <i class="bi bi-journal-text"></i>
          </div>
          <div>
            <div class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">REKAP JURNAL HARI INI</div>
            <div class="text-3xl font-bold !text-gray-800">{{ $jurnalHariIni ?? 0 }}</div>
          </div>
        </div>
      </a>
    </div>

    <!-- Table Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mt-8">
      
      <!-- Table Header -->
      <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-white">
        <div>
          <h2 class="text-lg font-bold text-gray-800">Aktivitas Jurnal Mengajar Terbaru</h2>
          <p class="text-xs text-gray-500 mt-0.5">Pemantauan jurnal mengajar kelas kejuruan dan umum secara berkala</p>
        </div>
        <a href="{{ route('dashboard.rekap-jurnal') }}" class="text-sm font-semibold !text-emerald-600 hover:!text-emerald-700 !no-underline transition-colors flex items-center gap-1">
          Lihat Semua 
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </a>
      </div>

      <!-- Table Body -->
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-gray-50/70">
              <th class="py-4 px-6 text-xs font-bold text-gray-500 uppercase tracking-wider border-b border-gray-100">Waktu</th>
              <th class="py-4 px-6 text-xs font-bold text-gray-500 uppercase tracking-wider border-b border-gray-100">Nama Guru</th>
              <th class="py-4 px-6 text-xs font-bold text-gray-500 uppercase tracking-wider border-b border-gray-100">Mata Pelajaran</th>
              <th class="py-4 px-6 text-xs font-bold text-gray-500 uppercase tracking-wider border-b border-gray-100">Kelas</th>
              <th class="py-4 px-6 text-xs font-bold text-gray-500 uppercase tracking-wider border-b border-gray-100">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            @forelse($aktivitasTerbaru ?? [] as $item)
              <tr class="hover:bg-emerald-50/30 transition-colors duration-200">
                <td class="py-4 px-6 text-sm text-gray-600">{{ $item['waktu'] }}</td>
                <td class="py-4 px-6 text-sm font-bold text-gray-800">{{ $item['nama_guru'] }}</td>
                <td class="py-4 px-6 text-sm text-gray-600">
                  <span class="px-2 py-0.5 rounded text-xs bg-slate-100 text-slate-700 border border-slate-200">
                    {{ $item['mapel'] }}
                  </span>
                </td>
                <td class="py-4 px-6 text-sm font-semibold text-emerald-800">{{ $item['kelas'] }}</td>
                <td class="py-4 px-6">
                  @if($item['status'] === 'Hadir' || $item['status'] === 'Selesai')
                    <span class="px-3 py-1 bg-emerald-100/80 text-emerald-700 rounded-full text-xs font-bold tracking-wide">Hadir</span>
                  @elseif($item['status'] === 'Izin')
                    <span class="px-3 py-1 bg-blue-100/80 text-blue-700 rounded-full text-xs font-bold tracking-wide">Izin</span>
                  @elseif($item['status'] === 'Sakit')
                    <span class="px-3 py-1 bg-amber-100/80 text-amber-700 rounded-full text-xs font-bold tracking-wide">Sakit</span>
                  @else
                    <span class="px-3 py-1 bg-rose-100/80 text-rose-700 rounded-full text-xs font-bold tracking-wide">{{ $item['status'] }}</span>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="p-6 text-center text-gray-400">Belum ada aktivitas jurnal tercatat.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      
    </div>
  </div>
@endsection
