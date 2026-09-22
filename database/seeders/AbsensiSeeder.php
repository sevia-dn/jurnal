<?php

namespace Database\Seeders;

use App\Models\Absensi;
use App\Models\JurnalMengajar;
use App\Models\Siswa;
use Illuminate\Database\Seeder;

class AbsensiSeeder extends Seeder
{
    public function run(): void
    {
        $jurnals = JurnalMengajar::with('kelas')->get();

        foreach ($jurnals as $jurnal) {

            $siswa = Siswa::where(
                'kelas_id',
                $jurnal->id_kelas
            )->get();

            foreach ($siswa as $s) {

                Absensi::updateOrCreate(
                    [
                        'id_jurnal' => $jurnal->id_jurnal,
                        'id_siswa' => $s->id,
                    ],
                    [
                        'status' => 'Hadir',
                        'catatan' => null,
                    ]
                );
            }
        }
    }
}
