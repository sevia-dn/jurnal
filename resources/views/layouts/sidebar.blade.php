<aside class="sidebar">
  <div class="sidebar-inner d-flex flex-column h-100">
    <div class="logo-wrap">
      <div class="logo-icon"><i class="bi bi-mortarboard-fill"></i></div>
      <div class="logo-text">
        <div class="brand">JurnalKita</div>
        <div class="subtitle">Management System</div>
      </div>
    </div>

    <nav class="nav flex-column px-3">
  <!-- Halaman Utama -->
  <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
    <i class="bi bi-grid"></i>
    <span>Halaman Utama</span>
  </a>

  <!-- Data Guru -->
  <a href="#" class="nav-link {{ request()->routeIs('data-guru*') ? 'active' : '' }}">
    <i class="bi bi-person"></i>
    <span>Data Guru</span>
  </a>

  <!-- Data Kelas -->
  <a href="#" class="nav-link {{ request()->routeIs('data-kelas*') ? 'active' : '' }}">
    <i class="bi bi-book"></i>
    <span>Data Kelas</span>
  </a>

  <!-- Catatan Jurnal -->
  <a href="{{ route('catatan-jurnal') }}" class="nav-link {{ request()->routeIs('catatan-jurnal*') ? 'active' : '' }}">
    <i class="bi bi-journal-text"></i>
    <span>Catatan Jurnal</span>
  </a>
</nav>

    <div class="sidebar-footer">
      <a href="#" class="btn btn-add">+ Tambah Jurnal</a>
      <div class="sidebar-divider"></div>
      <a href="#" class="setting-link"><i class="bi bi-gear"></i><span>Pengaturan</span></a>
      <a href="{{ route('login') }}" class="logout-link"><i class="bi bi-box-arrow-right"></i><span>Keluar</span></a>
    </div>
  </div>
</aside>