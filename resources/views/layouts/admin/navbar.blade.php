<header class="sticky top-0 z-40 bg-white h-16 border-b border-gray-100 flex items-center px-6 w-full">
  <div class="w-full flex justify-between items-center relative">

    <div class="flex items-center gap-3">
      <div class="text-xl font-bold text-gray-800">Admin</div>
      <span class="hidden sm:inline-block px-2.5 py-0.5 text-[11px] font-semibold bg-emerald-50 text-emerald-700 rounded-full border border-emerald-200">
        JurnalKita Management
      </span>
    </div>

    <div class="flex items-center gap-4 text-gray-500">

      <!-- Icon Notification & Dropdown -->
      <div class="relative" id="notifDropdownContainer">
        <button
          type="button"
          id="btnNotifToggle"
          class="relative p-2 rounded-xl text-gray-600 hover:text-emerald-600 hover:bg-emerald-50/60 transition cursor-pointer flex items-center justify-center focus:outline-none"
          title="Notifikasi Laporan Ganti Password"
        >
          <i class="bi bi-bell text-xl"></i>
          @if(isset($unreadLaporanCount) && $unreadLaporanCount > 0)
            <span id="notifBadge" class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] bg-rose-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center px-1 ring-2 ring-white animate-pulse">
              {{ $unreadLaporanCount > 9 ? '9+' : $unreadLaporanCount }}
            </span>
          @endif
        </button>

        <!-- Dropdown Menu Laporan Ganti PW -->
        <div
          id="notifDropdownMenu"
          class="hidden absolute right-0 mt-3 w-[360px] sm:w-[440px] bg-white rounded-2xl shadow-2xl border border-gray-100 z-50 overflow-hidden transform transition-all duration-200 origin-top-right"
        >
          <!-- Header Notifikasi -->
          <div class="p-4 bg-gradient-to-r from-emerald-800 to-[#0f463c] text-white flex justify-between items-center">
            <div>
              <div class="flex items-center gap-2">
                <i class="bi bi-shield-lock-fill text-emerald-300"></i>
                <h3 class="font-bold text-sm tracking-wide">Laporan Ganti Password</h3>
              </div>
              <p class="text-[11px] text-emerald-100/80 mt-0.5">Permintaan reset kata sandi dari pengguna</p>
            </div>
            @if(isset($unreadLaporanCount) && $unreadLaporanCount > 0)
              <span class="bg-rose-500/90 text-white text-xs font-bold px-2.5 py-0.5 rounded-full shadow-xs">
                {{ $unreadLaporanCount }} Menunggu
              </span>
            @else
              <span class="bg-white/20 text-emerald-100 text-xs font-medium px-2.5 py-0.5 rounded-full">
                Semua Beres
              </span>
            @endif
          </div>

          <!-- Tab Filter Sederhana -->
          <div class="flex border-b border-gray-100 bg-gray-50/80 px-4 py-2 text-xs font-semibold text-gray-500 gap-2">
            <button type="button" onclick="filterNotifTab('semua')" id="tabBtnSemua" class="px-3 py-1 rounded-lg bg-white text-emerald-700 font-bold shadow-2xs border border-gray-200 cursor-pointer">
              Semua
            </button>
            <button type="button" onclick="filterNotifTab('menunggu')" id="tabBtnMenunggu" class="px-3 py-1 rounded-lg hover:bg-white text-gray-600 transition cursor-pointer flex items-center gap-1.5">
              <span>Menunggu</span>
              @if(isset($unreadLaporanCount) && $unreadLaporanCount > 0)
                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
              @endif
            </button>
            <button type="button" onclick="filterNotifTab('selesai')" id="tabBtnSelesai" class="px-3 py-1 rounded-lg hover:bg-white text-gray-600 transition cursor-pointer">
              Selesai / Riwayat
            </button>
          </div>

          <!-- List Laporan Ganti Password -->
          <div class="max-h-[380px] overflow-y-auto divide-y divide-gray-100 bg-white" id="notifListContainer">
            @if(isset($laporanGantiPw) && count($laporanGantiPw) > 0)
              @foreach($laporanGantiPw as $item)
                @php
                  $initials = strtoupper(substr($item->nama, 0, 2));
                  $roleLower = strtolower($item->role);
                  $badgeColor = match($roleLower) {
                    'guru' => 'bg-blue-100 text-blue-700 border-blue-200',
                    'piket' => 'bg-purple-100 text-purple-700 border-purple-200',
                    'sekretaris' => 'bg-amber-100 text-amber-800 border-amber-200',
                    'waka' => 'bg-indigo-100 text-indigo-700 border-indigo-200',
                    default => 'bg-gray-100 text-gray-700 border-gray-200'
                  };
                  $roleLabel = match($roleLower) {
                    'guru' => 'Guru Pengajar',
                    'piket' => 'Guru Piket',
                    'sekretaris' => 'Sekretaris',
                    'waka' => 'Waka Kesiswaan',
                    default => ucfirst($item->role)
                  };
                  $filterCategory = $item->status === 'menunggu' ? 'menunggu' : 'selesai';
                @endphp
                <div class="p-4 hover:bg-gray-50/80 transition notif-item" data-status="{{ $filterCategory }}">
                  <div class="flex items-start gap-3">

                    <!-- Avatar Inisial -->
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-800 font-bold flex items-center justify-center text-xs shrink-0 shadow-2xs border border-emerald-200">
                      {{ $initials }}
                    </div>

                    <!-- Detail Info -->
                    <div class="flex-1 min-w-0">
                      <div class="flex items-center justify-between gap-2">
                        <h4 class="text-sm font-bold text-gray-800 truncate" title="{{ $item->nama }}">
                          {{ $item->nama }}
                        </h4>
                        <span class="text-[10px] text-gray-400 whitespace-nowrap">
                          {{ $item->created_at->diffForHumans() }}
                        </span>
                      </div>

                      <div class="flex items-center gap-2 mt-0.5 flex-wrap">
                        <span class="text-xs text-gray-500 font-mono font-medium">
                          <i class="bi bi-person text-gray-400"></i> {{ $item->username }}
                        </span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $badgeColor }}">
                          {{ $roleLabel }}
                        </span>
                        @if($item->no_hp)
                          <a
                            href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $item->no_hp)) }}?text={{ urlencode('Halo ' . $item->nama . ', terkait laporan permohonan reset password akun JurnalKita Anda:') }}"
                            target="_blank"
                            class="text-[11px] text-emerald-600 hover:text-emerald-700 flex items-center gap-1 font-medium hover:underline"
                            title="Chat WhatsApp"
                          >
                            <i class="bi bi-whatsapp"></i> {{ $item->no_hp }}
                          </a>
                        @endif
                      </div>

                      <!-- Alasan Permintaan -->
                      <div class="mt-2 p-2 bg-gray-50 rounded-lg text-xs text-gray-600 border border-gray-100 leading-relaxed">
                        <span class="font-semibold text-gray-700">Alasan:</span>
                        {{ $item->alasan ?: 'Lupa kata sandi lama, meminta bantuan reset password ke Admin.' }}
                      </div>

                      <!-- Status & Aksi -->
                      <div class="mt-3 flex items-center justify-between gap-2">
                        @if($item->status === 'menunggu')
                          <span class="inline-flex items-center gap-1 text-[11px] font-bold text-amber-700 bg-amber-50 px-2.5 py-1 rounded-lg border border-amber-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-ping"></span>
                            Menunggu Respon
                          </span>

                          <div class="flex items-center gap-2">
                            <!-- Tombol Tolak -->
                            <button
                              type="button"
                              onclick="openRejectModal({{ $item->id }}, '{{ addslashes($item->nama) }}', '{{ addslashes($item->username) }}')"
                              class="px-2.5 py-1 text-xs font-semibold text-rose-600 bg-rose-50 hover:bg-rose-100 rounded-lg border border-rose-200 transition cursor-pointer"
                            >
                              <i class="bi bi-x-circle"></i> Tolak
                            </button>

                            <!-- Tombol Terima & Ganti PW -->
                            <button
                              type="button"
                              onclick="openApproveModal({{ $item->id }}, '{{ addslashes($item->nama) }}', '{{ addslashes($item->username) }}', '{{ $roleLabel }}')"
                              class="px-3 py-1 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg shadow-2xs transition cursor-pointer flex items-center gap-1"
                            >
                              <i class="bi bi-key-fill"></i> Terima & Ganti PW
                            </button>
                          </div>
                        @elseif($item->status === 'disetujui')
                          <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-200">
                            <i class="bi bi-check-circle-fill text-emerald-600"></i> Password Telah Diubah
                          </span>
                          <span class="text-[10px] text-gray-400 italic">
                            {{ $item->catatan_admin ?: 'Disetujui oleh Admin' }}
                          </span>
                        @else
                          <span class="inline-flex items-center gap-1 text-[11px] font-bold text-rose-700 bg-rose-50 px-2.5 py-1 rounded-lg border border-rose-200">
                            <i class="bi bi-x-circle-fill text-rose-600"></i> Permintaan Ditolak
                          </span>
                          <span class="text-[10px] text-gray-400 italic truncate max-w-[180px]">
                            {{ $item->catatan_admin ?: 'Ditolak oleh Admin' }}
                          </span>
                        @endif
                      </div>

                    </div>
                  </div>
                </div>
              @endforeach
            @else
              <!-- State Kosong -->
              <div class="p-8 text-center">
                <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-full flex items-center justify-center text-xl mx-auto mb-2">
                  <i class="bi bi-bell-slash"></i>
                </div>
                <h4 class="text-sm font-bold text-gray-700">Tidak Ada Laporan Baru</h4>
                <p class="text-xs text-gray-400 mt-1">Belum ada permohonan ganti password yang masuk dari pengguna.</p>
              </div>
            @endif
          </div>

          <!-- Footer Notifikasi -->
          <div class="p-3 bg-gray-50 border-t border-gray-100 flex items-center justify-between text-xs text-gray-500">
            <span class="flex items-center gap-1 text-emerald-700 font-medium">
              <i class="bi bi-shield-check"></i> Terhubung Langsung ke Akun User
            </span>
            <a href="{{ route('admin.manajemen-user') }}" class="font-bold text-emerald-700 hover:underline">
              Kelola Semua User &rarr;
            </a>
          </div>
        </div>
      </div>

      <!-- Icon Help -->
      <div
        class="cursor-pointer hover:text-emerald-600 transition p-2 rounded-xl hover:bg-gray-50"
        onclick="alert('JurnalKita Management System\n\nUntuk mereset password pengguna:\n1. Klik ikon lonceng untuk melihat laporan user yang lupa password.\n2. Klik Terima & Ganti PW untuk memasukkan password baru.\n3. User bisa langsung login menggunakan password baru tersebut.')"
        title="Bantuan Penggunaan"
      >
        <i class="bi bi-question-circle text-xl"></i>
      </div>

      <!-- Divider -->
      <div class="w-px h-6 bg-gray-200 mx-1"></div>

      <!-- User Profile Pill -->
      <div class="flex items-center gap-2 text-sm text-gray-700 font-semibold pl-1">
        <div class="w-8 h-8 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-xs shadow-2xs">
          A
        </div>
        <span class="hidden md:inline-block">Administrator</span>
      </div>

    </div>
  </div>
</header>

<!-- ========================================================================= -->
<!-- MODAL 1: TERIMA PERMOHONAN & GANTI PASSWORD BARU -->
<!-- ========================================================================= -->
<div id="modalApprovePw" class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs z-50 flex items-center justify-center p-4 opacity-0 pointer-events-none transition-opacity duration-200">
  <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-gray-100 overflow-hidden transform scale-95 transition-transform duration-200" id="cardApprovePw">

    <!-- Modal Header -->
    <div class="p-5 bg-gradient-to-r from-emerald-800 to-[#0f463c] text-white flex justify-between items-center">
      <div class="flex items-center gap-2.5">
        <div class="w-9 h-9 rounded-xl bg-white/10 flex items-center justify-center text-lg">
          <i class="bi bi-key-fill text-emerald-300"></i>
        </div>
        <div>
          <h3 class="font-bold text-base leading-tight">Ganti Password Baru</h3>
          <p class="text-xs text-emerald-100/80">Setel kata sandi baru untuk pengguna</p>
        </div>
      </div>
      <button type="button" onclick="closeApproveModal()" class="text-emerald-200 hover:text-white p-1 rounded-lg cursor-pointer">
        <i class="bi bi-x-lg text-lg"></i>
      </button>
    </div>

    <!-- Modal Form -->
    <form id="formApprovePw" method="POST" action="" autocomplete="off" class="p-6 space-y-4">
      @csrf

      <!-- Info Pengguna yang Direset -->
      <div class="p-3.5 bg-emerald-50/70 border border-emerald-200/70 rounded-xl flex items-center gap-3">
        <div class="w-10 h-10 rounded-full bg-emerald-600 text-white font-bold flex items-center justify-center text-sm shadow-2xs shrink-0" id="approveModalAvatar">
          US
        </div>
        <div class="min-w-0 flex-1">
          <h4 class="text-sm font-bold text-gray-800 truncate" id="approveModalNama">Nama Pengguna</h4>
          <div class="flex items-center gap-2 text-xs text-gray-500 font-mono mt-0.5">
            <span id="approveModalUsername">username</span>
            <span>&bull;</span>
            <span class="font-sans font-semibold text-emerald-700" id="approveModalRole">Guru</span>
          </div>
        </div>
      </div>

      <!-- Input Password Baru -->
      <div>
        <label class="block text-xs font-bold text-gray-700 mb-1.5">
          Password Baru <span class="text-red-500">*</span>
        </label>
        <div class="relative">
          <i class="bi bi-lock-fill absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400"></i>
          <input
            type="password"
            name="password_baru"
            id="inputNewPassword"
            required
            minlength="4"
            autocomplete="new-password"
            placeholder="Masukkan kata sandi baru..."
            class="w-full pl-10 pr-10 py-2.5 text-sm rounded-xl border border-gray-200 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition font-mono"
          >
          <button
            type="button"
            id="toggleNewPasswordBtn"
            onclick="togglePasswordVisibility('inputNewPassword', 'toggleNewPasswordIcon')"
            class="absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 cursor-pointer"
            title="Lihat / Sembunyikan Password"
          >
            <i class="bi bi-eye text-base" id="toggleNewPasswordIcon"></i>
          </button>
        </div>
      </div>

      <!-- Tombol Generate Rekomendasi Password Cepat -->
      <div class="bg-gray-50 p-2.5 rounded-xl border border-gray-200/70 text-xs">
        <span class="text-gray-500 font-medium block mb-1.5">Pilihan Cepat Password:</span>
        <div class="flex flex-wrap gap-1.5">
          <button type="button" onclick="setQuickPassword('password123')" class="px-2.5 py-1 bg-white hover:bg-emerald-50 text-gray-700 hover:text-emerald-700 font-mono rounded-lg border border-gray-200 transition cursor-pointer text-[11px]">
            password123
          </button>
          <button type="button" onclick="setQuickPassword('12345678')" class="px-2.5 py-1 bg-white hover:bg-emerald-50 text-gray-700 hover:text-emerald-700 font-mono rounded-lg border border-gray-200 transition cursor-pointer text-[11px]">
            12345678
          </button>
          <button type="button" onclick="generateRandomPassword()" class="px-2.5 py-1 bg-emerald-100 hover:bg-emerald-200 text-emerald-800 font-bold rounded-lg transition cursor-pointer text-[11px] flex items-center gap-1">
            <i class="bi bi-dice-5"></i> Buat Acak
          </button>
        </div>
      </div>

      <!-- Catatan Admin Opsional -->
      <div>
        <label class="block text-xs font-bold text-gray-700 mb-1.5">
          Catatan Admin <span class="text-gray-400 font-normal">(Opsional)</span>
        </label>
        <textarea
          name="catatan"
          rows="2"
          placeholder="Contoh: Password diubah sesuai permintaan via WhatsApp..."
          class="w-full px-3.5 py-2 text-xs rounded-xl border border-gray-200 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition resize-none"
        ></textarea>
      </div>

      <!-- Modal Action Buttons -->
      <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-gray-100">
        <button
          type="button"
          onclick="closeApproveModal()"
          class="px-4 py-2.5 text-xs font-semibold text-gray-600 hover:bg-gray-100 rounded-xl transition cursor-pointer"
        >
          Batal
        </button>
        <button
          type="submit"
          class="px-5 py-2.5 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-sm hover:shadow-md transition cursor-pointer flex items-center gap-1.5"
        >
          <i class="bi bi-check-circle-fill"></i> Simpan & Aktifkan Password
        </button>
      </div>
    </form>
  </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL 2: TOLAK PERMOHONAN GANTI PASSWORD -->
<!-- ========================================================================= -->
<div id="modalRejectPw" class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs z-50 flex items-center justify-center p-4 opacity-0 pointer-events-none transition-opacity duration-200">
  <div class="bg-white rounded-2xl max-w-sm w-full shadow-2xl border border-gray-100 overflow-hidden transform scale-95 transition-transform duration-200" id="cardRejectPw">

    <div class="p-5 bg-rose-600 text-white flex justify-between items-center">
      <div class="flex items-center gap-2.5">
        <i class="bi bi-exclamation-octagon-fill text-xl text-rose-200"></i>
        <h3 class="font-bold text-base">Tolak Permohonan</h3>
      </div>
      <button type="button" onclick="closeRejectModal()" class="text-rose-200 hover:text-white p-1 rounded-lg cursor-pointer">
        <i class="bi bi-x-lg text-lg"></i>
      </button>
    </div>

    <form id="formRejectPw" method="POST" action="" class="p-5 space-y-4">
      @csrf

      <p class="text-xs text-gray-600 leading-relaxed">
        Apakah Anda yakin ingin menolak permohonan reset password dari
        <strong id="rejectModalNama" class="text-gray-900">User</strong> (<span id="rejectModalUsername" class="font-mono">user</span>)?
      </p>

      <div>
        <label class="block text-xs font-bold text-gray-700 mb-1">Alasan Penolakan</label>
        <textarea
          name="catatan"
          rows="2"
          placeholder="Contoh: Akun masih aktif atau data tidak sesuai..."
          class="w-full px-3 py-2 text-xs rounded-xl border border-gray-200 focus:outline-none focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20 transition resize-none"
        ></textarea>
      </div>

      <div class="flex items-center justify-end gap-2 pt-2 border-t border-gray-100">
        <button
          type="button"
          onclick="closeRejectModal()"
          class="px-3.5 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100 rounded-xl transition cursor-pointer"
        >
          Batal
        </button>
        <button
          type="submit"
          class="px-4 py-2 text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-xl shadow-xs transition cursor-pointer flex items-center gap-1"
        >
          <i class="bi bi-x-circle-fill"></i> Ya, Tolak
        </button>
      </div>
    </form>
  </div>
</div>

<!-- ========================================================================= -->
<!-- JAVASCRIPT LOGIC UNTUK DROPDOWN & MODAL NOTIFIKASI -->
<!-- ========================================================================= -->
<script>
  (function() {
    const btnToggle = document.getElementById('btnNotifToggle');
    const dropdown = document.getElementById('notifDropdownMenu');
    const container = document.getElementById('notifDropdownContainer');

    if (btnToggle && dropdown) {
      btnToggle.addEventListener('click', function(e) {
        e.stopPropagation();
        dropdown.classList.toggle('hidden');
      });

      // Tutup dropdown jika klik di luar
      document.addEventListener('click', function(e) {
        if (!container.contains(e.target)) {
          dropdown.classList.add('hidden');
        }
      });
    }
  })();

  // Filter Tab Notifikasi
  function filterNotifTab(tab) {
    const items = document.querySelectorAll('.notif-item');
    const btnSemua = document.getElementById('tabBtnSemua');
    const btnMenunggu = document.getElementById('tabBtnMenunggu');
    const btnSelesai = document.getElementById('tabBtnSelesai');

    // Reset button style
    [btnSemua, btnMenunggu, btnSelesai].forEach(btn => {
      if (btn) {
        btn.classList.remove('bg-white', 'text-emerald-700', 'font-bold', 'shadow-2xs', 'border', 'border-gray-200');
        btn.classList.add('text-gray-600');
      }
    });

    const activeBtn = tab === 'semua' ? btnSemua : (tab === 'menunggu' ? btnMenunggu : btnSelesai);
    if (activeBtn) {
      activeBtn.classList.add('bg-white', 'text-emerald-700', 'font-bold', 'shadow-2xs', 'border', 'border-gray-200');
      activeBtn.classList.remove('text-gray-600');
    }

    items.forEach(el => {
      const status = el.getAttribute('data-status');
      if (tab === 'semua') {
        el.style.display = '';
      } else if (tab === 'menunggu' && status === 'menunggu') {
        el.style.display = '';
      } else if (tab === 'selesai' && status === 'selesai') {
        el.style.display = '';
      } else {
        el.style.display = 'none';
      }
    });
  }

  // Modal Terima & Ganti PW
  function openApproveModal(id, nama, username, role) {
    // Tutup dropdown notifikasi dulu
    const dropdown = document.getElementById('notifDropdownMenu');
    if (dropdown) dropdown.classList.add('hidden');

    const form = document.getElementById('formApprovePw');
    form.action = '/dashboard/admin/laporan-ganti-pw/' + id + '/terima';

    document.getElementById('approveModalNama').innerText = nama;
    document.getElementById('approveModalUsername').innerText = username;
    document.getElementById('approveModalRole').innerText = role;
    document.getElementById('approveModalAvatar').innerText = nama.substring(0, 2).toUpperCase();

    // Default password saran
    document.getElementById('inputNewPassword').value = 'password123';

    const modal = document.getElementById('modalApprovePw');
    const card = document.getElementById('cardApprovePw');
    modal.classList.remove('opacity-0', 'pointer-events-none');
    card.classList.remove('scale-95');
    card.classList.add('scale-100');
  }

  function closeApproveModal() {
    const modal = document.getElementById('modalApprovePw');
    const card = document.getElementById('cardApprovePw');
    modal.classList.add('opacity-0', 'pointer-events-none');
    card.classList.remove('scale-100');
    card.classList.add('scale-95');
  }

  // Modal Tolak Permohonan
  function openRejectModal(id, nama, username) {
    const dropdown = document.getElementById('notifDropdownMenu');
    if (dropdown) dropdown.classList.add('hidden');

    const form = document.getElementById('formRejectPw');
    form.action = '/dashboard/admin/laporan-ganti-pw/' + id + '/tolak';

    document.getElementById('rejectModalNama').innerText = nama;
    document.getElementById('rejectModalUsername').innerText = username;

    const modal = document.getElementById('modalRejectPw');
    const card = document.getElementById('cardRejectPw');
    modal.classList.remove('opacity-0', 'pointer-events-none');
    card.classList.remove('scale-95');
    card.classList.add('scale-100');
  }

  function closeRejectModal() {
    const modal = document.getElementById('modalRejectPw');
    const card = document.getElementById('cardRejectPw');
    modal.classList.add('opacity-0', 'pointer-events-none');
    card.classList.remove('scale-100');
    card.classList.add('scale-95');
  }

  // Helper Set Password Cepat
  function setQuickPassword(val) {
    const inp = document.getElementById('inputNewPassword');
    if (inp) {
      inp.value = val;
      inp.focus();
    }
  }

  // Helper Generate Password Acak (8 karakter ramah dibaca)
  function generateRandomPassword() {
    const chars = 'abcdefghjkmnpqrstuvwxyz23456789';
    let res = '';
    for (let i = 0; i < 8; i++) {
      res += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    setQuickPassword(res);
  }

  // Helper Toggle Password Eye
  function togglePasswordVisibility(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    if (input && icon) {
      if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
      } else {
        input.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
      }
    }
  }
</script>