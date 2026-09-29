<?php

namespace Tests\Feature;

use App\Models\Absensi;
use App\Models\JurnalMengajar;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\PiketKehadiranSiswa;
use App\Models\Siswa;
use App\Models\User;
use App\Services\StudentAttendanceSynchronizationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentAttendanceSynchronizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_piket_student_absence_updates_existing_teacher_logbook_and_summary(): void
    {
        $teacher = User::factory()->create(['role' => 'guru']);
        $piket = User::factory()->create(['role' => 'guru']);
        $class = Kelas::create(['nama_kelas' => 'XI RPL 1', 'jumlah_siswa' => 2]);
        $subject = Mapel::create(['kode_mapel' => 'PWEB', 'nama_mapel' => 'Pemrograman Web']);
        $student = Siswa::create(['kelas_id' => $class->id_kelas, 'nis' => '1001', 'nama' => 'Siswa Izin', 'jenis_kelamin' => 'L']);
        $journal = JurnalMengajar::create([
            'id_user' => $teacher->id,
            'id_kelas' => $class->id_kelas,
            'id_mapel' => $subject->id,
            'tanggal' => '2026-09-29',
            'jam_ke' => 1,
            'materi' => 'Materi awal',
            'jumlah_hadir' => 1,
        ]);
        Absensi::create(['id_jurnal' => $journal->id_jurnal, 'id_siswa' => $student->id, 'status' => 'Hadir']);

        $attendance = PiketKehadiranSiswa::create([
            'siswa_id' => $student->id,
            'kelas_id' => $class->id_kelas,
            'tanggal' => '2026-09-29',
            'status' => 'Izin',
            'sumber' => PiketKehadiranSiswa::SumberGuruPiket,
            'catatan' => 'Surat dari orang tua',
            'dicatat_oleh' => $piket->id,
        ]);

        app(StudentAttendanceSynchronizationService::class)->synchronize($attendance);

        $this->assertDatabaseHas('absensis', [
            'id_jurnal' => $journal->id_jurnal,
            'id_siswa' => $student->id,
            'status' => 'Izin',
            'catatan' => 'Otomatis dari Guru Piket: Surat dari orang tua',
        ]);
        $this->assertDatabaseHas('jurnal_mengajars', [
            'id_jurnal' => $journal->id_jurnal,
            'jumlah_hadir' => 0,
            'jumlah_izin' => 1,
            'jumlah_tidak_hadir' => 1,
        ]);
    }
}
