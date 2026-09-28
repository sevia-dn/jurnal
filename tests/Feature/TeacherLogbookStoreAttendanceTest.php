<?php

namespace Tests\Feature;

use App\Models\JadwalMengajar;
use App\Models\JadwalPiket;
use App\Models\JurnalMengajar;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Pengaturan;
use App\Models\Siswa;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TeacherLogbookStoreAttendanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_can_submit_logbook_with_absent_students(): void
    {
        Storage::fake('public');
        Carbon::setLocale('id');
        $now = Carbon::parse('2026-09-28 08:30:00', 'Asia/Jakarta');
        $this->travelTo($now);
        $hariIni = $now->translatedFormat('l');

        Pengaturan::setValue('tenggat_status', 0);
        Pengaturan::setValue('tenggat_opsi', 'hari_ini');

        $kelas = Kelas::create(['nama_kelas' => 'XI RPL 2', 'jumlah_siswa' => 5]);
        $guru = User::create([
            'name' => 'Budi Santoso, S.Kom',
            'username' => 'budisantoso',
            'password' => Hash::make('password123'),
            'role' => 'guru',
            'nip' => '198001012005011001',
        ]);
        $mapel = Mapel::create([
            'nama_mapel' => 'Pemrograman Web',
            'kode_mapel' => 'PWEB',
        ]);

        $jadwal = JadwalMengajar::create([
            'id_user' => $guru->id,
            'id_kelas' => $kelas->id_kelas,
            'id_mapel' => $mapel->id,
            'hari' => $hariIni,
            'jam_mulai' => 1,
            'jam_selesai' => 3,
        ]);

        $siswa1 = Siswa::create(['nama' => 'Ahmad', 'nis' => '1001', 'kelas_id' => $kelas->id_kelas, 'jenis_kelamin' => 'L']);
        $siswa2 = Siswa::create(['nama' => 'Budi', 'nis' => '1002', 'kelas_id' => $kelas->id_kelas, 'jenis_kelamin' => 'L']);
        $siswa3 = Siswa::create(['nama' => 'Citra', 'nis' => '1003', 'kelas_id' => $kelas->id_kelas, 'jenis_kelamin' => 'P']);
        $siswa4 = Siswa::create(['nama' => 'Dewi', 'nis' => '1004', 'kelas_id' => $kelas->id_kelas, 'jenis_kelamin' => 'P']);
        $siswa5 = Siswa::create(['nama' => 'Eko', 'nis' => '1005', 'kelas_id' => $kelas->id_kelas, 'jenis_kelamin' => 'L']);

        $file = UploadedFile::fake()->image('bukti.jpg');

        $response = $this->actingAs($guru)->post(route('guru.jurnal.store'), [
            'id_kelas' => $kelas->id_kelas,
            'id_mapel' => $mapel->id,
            'jam_ke' => 1,
            'jam_selesai' => 3,
            'tanggal' => $now->toDateString(),
            'materi' => 'CRUD Laravel',
            'ada_tugas' => 'Tidak',
            'catatan' => 'Semua tertib',
            'lampiran' => $file,
            'absensi' => [
                $siswa1->id => 'Hadir',
                $siswa2->id => 'Sakit',
                $siswa3->id => 'Izin',
                $siswa4->id => 'Alpa',
                $siswa5->id => 'Dispensasi',
            ],
            'absensi_catatan' => [
                $siswa2->id => 'Demam tinggi',
                $siswa3->id => 'Acara keluarga',
                $siswa4->id => 'Tanpa keterangan',
                $siswa5->id => 'Lomba Pramuka',
            ],
        ]);

        $response->assertRedirect(route('guru.riwayat'));
        $response->assertSessionHas('success');

        $jurnal = JurnalMengajar::with('absensis.siswa')->first();
        $this->assertNotNull($jurnal);
        $this->assertSame(1, $jurnal->jumlah_hadir);
        $this->assertSame(1, $jurnal->jumlah_sakit);
        $this->assertSame(1, $jurnal->jumlah_izin);
        $this->assertSame(1, $jurnal->jumlah_alpa);
        $this->assertSame(1, $jurnal->jumlah_dispensasi);
        $this->assertSame(4, $jurnal->jumlah_tidak_hadir);

        $absensis = $jurnal->absensis->keyBy('id_siswa');
        $this->assertSame('Hadir', $absensis->get($siswa1->id)->status);
        $this->assertSame('Sakit', $absensis->get($siswa2->id)->status);
        $this->assertSame('Demam tinggi', $absensis->get($siswa2->id)->catatan);
        $this->assertSame('Izin', $absensis->get($siswa3->id)->status);
        $this->assertSame('Acara keluarga', $absensis->get($siswa3->id)->catatan);
        $this->assertSame('Alpa', $absensis->get($siswa4->id)->status);
        $this->assertSame('Tanpa keterangan', $absensis->get($siswa4->id)->catatan);
        $this->assertSame('D', $absensis->get($siswa5->id)->status);
        $this->assertSame('Lomba Pramuka', $absensis->get($siswa5->id)->catatan);

        // Test pengurus kelas view
        $pengurus = User::create([
            'name' => 'Pengurus Kelas XI RPL 2',
            'username' => 'xirpl2',
            'password' => Hash::make('password123'),
            'role' => 'pengurus_kelas',
        ]);

        $resPengurusDetail = $this->actingAs($pengurus)->get(route('pengurus-kelas.jurnal-detail', ['id' => $jurnal->id_jurnal]));
        $resPengurusDetail->assertOk();
        $resPengurusDetail->assertSee('Budi');
        $resPengurusDetail->assertSee('Demam tinggi');
        $resPengurusDetail->assertSee('Citra');
        $resPengurusDetail->assertSee('Acara keluarga');
        $resPengurusDetail->assertSee('Dewi');
        $resPengurusDetail->assertSee('Tanpa keterangan');
        $resPengurusDetail->assertSee('Eko');

        $resPengurusKehadiran = $this->actingAs($pengurus)->get(route('pengurus-kelas.kehadiran-siswa'));
        $resPengurusKehadiran->assertOk();
        $resPengurusKehadiran->assertSee('Budi');
        $resPengurusKehadiran->assertSee('Citra');
        $resPengurusKehadiran->assertSee('Dewi');
        $resPengurusKehadiran->assertSee('Eko');

        // Test guru piket view
        $piketUser = User::create([
            'name' => 'Guru Piket Hari Ini',
            'username' => 'gurupiket',
            'password' => Hash::make('password123'),
            'role' => 'piket',
        ]);
        JadwalPiket::create([
            'user_id' => $piketUser->id,
            'hari' => $hariIni,
            'tanggal' => $now->toDateString(),
            'tipe' => 'harian',
            'jam_mulai' => '07:00',
            'jam_selesai' => '16:00',
        ]);

        $resPiketDetail = $this->actingAs($piketUser)->get(route('piket.jurnal.show', ['jurnal' => $jurnal->id_jurnal]));
        $resPiketDetail->assertOk();
        $resPiketDetail->assertSee('Budi');
        $resPiketDetail->assertSee('Demam tinggi');
        $resPiketDetail->assertSee('Citra');
        $resPiketDetail->assertSee('Dewi');
        $resPiketDetail->assertSee('Eko');

        $resPiketKehadiran = $this->actingAs($piketUser)->get(route('piket.kehadiran-siswa', ['kelas_id' => $kelas->id_kelas, 'tanggal' => $now->toDateString()]));
        $resPiketKehadiran->assertOk();
        $resPiketKehadiran->assertSee('Budi');
        $resPiketKehadiran->assertSee('Citra');
        $resPiketKehadiran->assertSee('Dewi');
        $resPiketKehadiran->assertSee('Eko');
    }
}
