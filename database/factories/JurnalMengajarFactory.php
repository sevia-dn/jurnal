<?php

namespace Database\Factories;

use App\Models\JurnalMengajar;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<JurnalMengajar>
 */
class JurnalMengajarFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $kelas = Kelas::create([
            'nama_kelas' => 'X-'.$this->faker->unique()->numerify('##'),
            'jumlah_siswa' => 30,
        ]);
        $mapel = Mapel::create([
            'kode_mapel' => $this->faker->unique()->lexify('???'),
            'nama_mapel' => $this->faker->word(),
            'kategori' => 'umum',
        ]);

        return [
            'id_user' => User::factory(),
            'id_kelas' => $kelas->id_kelas,
            'id_mapel' => $mapel->id,
            'tanggal' => $this->faker->date(),
            'jam_ke' => $this->faker->numberBetween(1, 8),
            'materi' => $this->faker->sentence(),
            'jumlah_hadir' => $this->faker->numberBetween(20, 35),
            'jumlah_sakit' => 0,
            'jumlah_izin' => 0,
            'jumlah_alpa' => 0,
            'jumlah_dispensasi' => 0,
            'status_kehadiran_guru' => 'Hadir',
            'ada_tugas' => false,
            'filled_at' => null,
            'is_late' => false,
            'late_mode' => null,
        ];
    }
}
