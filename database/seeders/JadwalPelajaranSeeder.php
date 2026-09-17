<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Mapel;
use App\Models\Kelas;

class JadwalPelajaranSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('jadwal_pelajarans')->delete();

        $gurus = User::where('role', 'guru')->pluck('id')->toArray();
        if (empty($gurus)) {
            return;
        }

        $guru1 = $gurus[0];
        $guru2 = isset($gurus[1]) ? $gurus[1] : $guru1;
        $kelasId = Kelas::first()?->id_kelas ?? 1;

        $mapel1 = Mapel::find(1)?->id;
        $mapel2 = Mapel::find(2)?->id;
        $mapel5 = Mapel::find(5)?->id;

        DB::table('jadwal_pelajarans')->insert([
            // Jam 1 - 10 standar sekolah
            ['id_user' => $guru1, 'id_kelas' => $kelasId, 'id_mapel' => null,   'hari' => 'Senin', 'jam_ke' => 1,  'jam_mulai' => '07:00:00', 'jam_selesai' => '07:45:00', 'mapel' => 'Upacara / Apel'],
            ['id_user' => $guru2, 'id_kelas' => $kelasId, 'id_mapel' => $mapel5, 'hari' => 'Senin', 'jam_ke' => 2,  'jam_mulai' => '07:45:00', 'jam_selesai' => '08:30:00', 'mapel' => 'Kreativitas & Kewirausahaan'],
            ['id_user' => $guru2, 'id_kelas' => $kelasId, 'id_mapel' => $mapel5, 'hari' => 'Senin', 'jam_ke' => 3,  'jam_mulai' => '08:30:00', 'jam_selesai' => '09:15:00', 'mapel' => 'Kreativitas & Kewirausahaan'],
            ['id_user' => $guru2, 'id_kelas' => $kelasId, 'id_mapel' => $mapel5, 'hari' => 'Senin', 'jam_ke' => 4,  'jam_mulai' => '09:15:00', 'jam_selesai' => '10:00:00', 'mapel' => 'Kreativitas & Kewirausahaan'],
            ['id_user' => $guru1, 'id_kelas' => $kelasId, 'id_mapel' => $mapel1, 'hari' => 'Senin', 'jam_ke' => 5,  'jam_mulai' => '10:15:00', 'jam_selesai' => '11:00:00', 'mapel' => 'Bahasa Inggris'],
            ['id_user' => $guru1, 'id_kelas' => $kelasId, 'id_mapel' => $mapel1, 'hari' => 'Senin', 'jam_ke' => 6,  'jam_mulai' => '11:00:00', 'jam_selesai' => '11:45:00', 'mapel' => 'Bahasa Inggris'],
            ['id_user' => $guru1, 'id_kelas' => $kelasId, 'id_mapel' => $mapel2, 'hari' => 'Senin', 'jam_ke' => 7,  'jam_mulai' => '12:30:00', 'jam_selesai' => '13:15:00', 'mapel' => 'Konsentrasi RPL'],
            ['id_user' => $guru1, 'id_kelas' => $kelasId, 'id_mapel' => $mapel2, 'hari' => 'Senin', 'jam_ke' => 8,  'jam_mulai' => '13:15:00', 'jam_selesai' => '14:00:00', 'mapel' => 'Konsentrasi RPL'],
            ['id_user' => $guru1, 'id_kelas' => $kelasId, 'id_mapel' => $mapel2, 'hari' => 'Senin', 'jam_ke' => 9,  'jam_mulai' => '14:00:00', 'jam_selesai' => '14:45:00', 'mapel' => 'Konsentrasi RPL'],
            ['id_user' => $guru1, 'id_kelas' => $kelasId, 'id_mapel' => $mapel2, 'hari' => 'Senin', 'jam_ke' => 10, 'jam_mulai' => '14:45:00', 'jam_selesai' => '15:30:00', 'mapel' => 'Konsentrasi RPL'],
        ]);
    }
}