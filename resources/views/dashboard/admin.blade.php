@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
  <div class="section-header">
    <div>
      <div class="title">Overview</div>
      <div class="subtitle">Ringkasan aktivitas hari ini, 24 Oct 2023</div>
    </div>
  </div>

  <div class="stat-cards">
    <div class="card-stat">
      <div class="icon"><i class="bi bi-people"></i></div>
      <div>
        <div class="label">JUMLAH GURU</div>
        <div class="value">42</div>
      </div>
    </div>

    <div class="card-stat">
      <div class="icon"><i class="bi bi-building"></i></div>
      <div>
        <div class="label">JUMLAH KELAS</div>
        <div class="value">12</div>
      </div>
    </div>

    <div class="card-stat">
      <div class="icon"><i class="bi bi-journal-bookmark"></i></div>
      <div>
        <div class="label">JURNAL HARI INI</div>
        <div class="value">8</div>
      </div>
    </div>
  </div>

  <div class="panel">
    <div class="panel-title">
      <div>
        <strong>Aktivitas Jurnal Terbaru</strong>
      </div>
      <a href="#" class="link-action">Lihat Semua -&gt;</a>
    </div>

    <div class="table-responsive">
      <table class="table">
        <thead>
          <tr>
            <th>Tanggal</th>
            <th>Nama Guru</th>
            <th>Kelas</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>24 Oct 2026, 08:30</td>
            <td>Budi Santoso, S.Pd</td>
            <td>XII IPA 1</td>
            <td><span class="badge-selesai">Selesai</span></td>
          </tr>
          <tr>
            <td>24 Oct 2026, 09:15</td>
            <td>Siti Aminah, M.Pd</td>
            <td>X IPS 2</td>
            <td><span class="badge-menunggu">Menunggu</span></td>
          </tr>
          <tr>
            <td>24 Oct 2026, 10:00</td>
            <td>Ahmad Yani, S.Kom</td>
            <td>XI RPL</td>
            <td><span class="badge-selesai">Selesai</span></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

@endsection
