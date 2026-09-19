<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kelas;
use App\Models\Siswa;

class KelasSeeder extends Seeder
{
    public function run(): void
    {
        $jsonPath = base_path('scripts/master_data.json');
        if (file_exists($jsonPath)) {
            $data = json_decode(file_get_contents($jsonPath), true);
            $classes = $data['classes'] ?? [];
            foreach ($classes as $cname) {
                $k = Kelas::firstOrCreate(
                    ['nama_kelas' => $cname],
                    ['wali_kelas' => null, 'jumlah_siswa' => 2]
                );

                // Buat 2 sample siswa jika kelas belum memiliki siswa
                if (Siswa::where('kelas_id', $k->id_kelas)->count() === 0) {
                    $maxNis = (int) (Siswa::max('nisn') ?? 10100);
                    Siswa::create([
                        'kelas_id' => $k->id_kelas,
                        'nama' => 'Onang Pralam ' . substr($cname, 2),
                        'nisn' => (string) ($maxNis + 1),
                        'jenis_kelamin' => 'L',
                    ]);
                    Siswa::create([
                        'kelas_id' => $k->id_kelas,
                        'nama' => 'Bely Trundi ' . substr($cname, 2),
                        'nisn' => (string) ($maxNis + 2),
                        'jenis_kelamin' => 'P',
                    ]);
                    $k->update(['jumlah_siswa' => 2]);
                }
            }
        } else {
            $defaultClasses = [
                ['nama_kelas' => 'X RPL 1', 'wali_kelas' => 'Sutrisno, S.Kom', 'jumlah_siswa' => 32],
                ['nama_kelas' => 'X RPL 2', 'wali_kelas' => 'Rahmawati, S.Kom', 'jumlah_siswa' => 30],
                ['nama_kelas' => 'XI RPL 1', 'wali_kelas' => 'Yusuf Hidayat, S.Kom', 'jumlah_siswa' => 31],
                ['nama_kelas' => 'XI RPL 2', 'wali_kelas' => 'Dewi Anjani, S.Pd', 'jumlah_siswa' => 29],
                ['nama_kelas' => 'X TKJ 1', 'wali_kelas' => 'Agus Setiawan, S.Kom', 'jumlah_siswa' => 33],
                ['nama_kelas' => 'X TKJ 2', 'wali_kelas' => 'Putri Handayani, S.Pd', 'jumlah_siswa' => 31],
                ['nama_kelas' => 'XI TKJ 1', 'wali_kelas' => 'Hendra Gunawan, S.Kom', 'jumlah_siswa' => 30],
                ['nama_kelas' => 'XI TKJ 2', 'wali_kelas' => 'Lestari Wahyuni, S.Pd', 'jumlah_siswa' => 29],
            ];

            foreach ($defaultClasses as $c) {
                Kelas::firstOrCreate(['nama_kelas' => $c['nama_kelas']], $c);
            }
        }
    }
}