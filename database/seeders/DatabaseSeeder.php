<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,           // Mengisi akun multi-role (piket, waka, sekretaris, guru)
            KelasSeeder::class,          // Seeder kelas
            MapelSeeder::class,          
            SiswaSeeder::class,          
            JadwalPelajaranSeeder::class, 
            JadwalPiketSeeder::class,
            JurnalMengajarSeeder::class,
        ]);

        // Sinkronisasi otomatis mapel utama guru berdasarkan jadwal pelajaran
        $mapels = \App\Models\Mapel::all()->keyBy('nama_mapel');
        $jadwalCounts = \App\Models\JadwalPelajaran::select('id_user', 'mapel', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
            ->whereNotNull('mapel')
            ->groupBy('id_user', 'mapel')
            ->orderBy('id_user')
            ->orderByDesc('total')
            ->get()
            ->groupBy('id_user');

        foreach ($jadwalCounts as $userId => $subjects) {
            $user = \App\Models\User::find($userId);
            if ($user && !$user->mapel_id) {
                $topSubject = $subjects->first()->mapel;
                if (isset($mapels[$topSubject])) {
                    $user->update(['mapel_id' => $mapels[$topSubject]->id]);
                }
            }
        }
    }
}
