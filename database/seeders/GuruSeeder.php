<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class GuruSeeder extends Seeder
{
    public function run(): void
    {
        // Data Guru lengkap (Nama, Gelar, dan NIP)
        $dataGuru = [
            ['nama' => 'Trisno Wibowo, S.Pd., M.M', 'nip' => '19810115 200312 1 003'],
            ['nama' => 'Martiin, S.Pd', 'nip' => '19670604 198903 2 009'],
            ['nama' => 'Yani, S.Pd.', 'nip' => '19661207 199412 1 003'],
            ['nama' => 'Siti Umiharsih, S.Pd', 'nip' => '19700825 199512 2 001'],
            ['nama' => 'Winarsih, S.Pd, M.Pd', 'nip' => '19700325 200312 2 007'],
            ['nama' => 'Dwi Rini Manfaati, S.Pd', 'nip' => '19701017 199703 2 004'],
            ['nama' => 'Dra. Anik Indriani', 'nip' => '19681128 200501 2 004'],
            ['nama' => 'Sri Rahayu, S.Pd', 'nip' => '19700304 200501 2 006'],
            ['nama' => 'Arvia Rienetasary, S.Pd', 'nip' => '19750304 200604 2 017'],
            ['nama' => 'Peni Wulandari, S.Pd', 'nip' => '19730601 200604 2 024'],
            ['nama' => 'Rindang Rejeki, S.Pd', 'nip' => '19691006 200701 2 022'],
            ['nama' => 'Erna Rinawati, S.Pd', 'nip' => '19710520 200604 2 018'],
            ['nama' => 'Sunarti, S.Pd', 'nip' => '19730108 200604 2 015'],
            ['nama' => 'Setiyo Winarko, S.Pd', 'nip' => '19721030 200312 1 002'],
            ['nama' => 'Isti Mufadah, S.Pd', 'nip' => '19780202 200604 2 027'],
            ['nama' => 'Indayah, S.Pd., M.Pd', 'nip' => '19731001 200604 2 012'],
            ['nama' => 'Umi Kulsum, S.Pd', 'nip' => '19690425 200701 2 025'],
            ['nama' => 'Rulik Indrawati, S.Pd', 'nip' => '19691126 200701 2 007'],
            ['nama' => 'Lilik Suratmi, S.Pd', 'nip' => '19690814 200701 2 026'],
            ['nama' => 'Basuki Sarjono, S.Pd', 'nip' => '19670421 200701 1 026'],
            ['nama' => 'Titik Samsistini, S.Pd', 'nip' => '19680825 200801 2 019'],
            ['nama' => 'Endang Ary Handayani, S.T., M.Pd', 'nip' => '19760210 200801 2 017'],
            ['nama' => 'Purwati, S.Pd', 'nip' => '19690616 200701 2 026'],
            ['nama' => 'Ninik Sriwidayati, S.Pd., M.Pd', 'nip' => '19750409 200701 2 010'],
            ['nama' => 'Agustina Mardika Rini, S.Pd., M.Pd.', 'nip' => '19770817 200701 2 012'],
            ['nama' => 'Komariyah, S.Pd', 'nip' => '19690805 200801 2 025'],
            ['nama' => 'Muashofah, M.Pd', 'nip' => '19710806 200801 2 012'],
            ['nama' => 'Atih Wilupi, S.E, M.Pd', 'nip' => '19690915 200801 2 028'],
            ['nama' => 'Winartin, S.Pd', 'nip' => '19801224 200801 2 016'],
            ['nama' => 'Siti Khoiriyah, S.Pd', 'nip' => '19681014 200801 2 011'],
            ['nama' => 'Sri Subekti, S.Pd', 'nip' => '19690917 200701 2 012'],
            ['nama' => 'Kasmi, S.Pd., M.Pd', 'nip' => '19700831 200801 2 017'],
            ['nama' => 'Ilham Sungeidi, S.Pd', 'nip' => '19700824 200801 1 008'],
            ['nama' => 'Lutfia Marsalina, S.Pd.I, M.Pd.', 'nip' => '19800329 200901 2 006'],
            ['nama' => 'Indriati, S.Pd', 'nip' => '19850910 200903 2 009'],
            ['nama' => 'Agus Fahruddy, S.Pd., M.Pd', 'nip' => '19761118 200701 1 004'],
            ['nama' => 'Titin Sukmasari, S.Pd., M.Pd', 'nip' => '19790202 200701 2 025'],
            ['nama' => 'Dian Mawarti, S.Pd', 'nip' => '19800410 200901 2 007'],
            ['nama' => 'Niken Hari Pratiwi, S.Psi., M.Pd', 'nip' => '19820303 200901 2 009'],
            ['nama' => 'Siti Munawaroh, S.Kom., M.Pd', 'nip' => '19740914 200901 2 001'],
            ['nama' => 'Dwi Nova Setyandari, S.Pd', 'nip' => '19821103 201001 2 025'],
            ['nama' => 'Diana Hartanti, S.T., M.Pd', 'nip' => '19801026 201001 2 016'],
            ['nama' => 'Andri Retno Yuli Astuti, S.Pd', 'nip' => '19730719 201001 2 002'],
            ['nama' => 'Siswanti Purwaningsih, S.T., M.Pd', 'nip' => '19770426 201001 2 008'],
            ['nama' => 'Ayu Puspitorini, ST', 'nip' => '19760826 201001 2 010'],
            ['nama' => 'Elysa Yuli Nur\'aini, S.Si', 'nip' => '19800723 201001 2 016'],
            ['nama' => 'Agus Muharyanto, M.Pd', 'nip' => '19710826 200604 1 011'],
            ['nama' => 'Septiani, S.Pd., M.Pd', 'nip' => '19781001 200604 2 021'],
            ['nama' => 'Retno Widyastuti, S.Pd., M.Pd.', 'nip' => '19870316 200901 2 002'],
            ['nama' => 'Ratih Dian Irawati, SE', 'nip' => '19840222 200902 2 007'],
            ['nama' => 'Andri Krisdianto, SE., M.Pd', 'nip' => '19830101 201001 1 042'],
            ['nama' => 'Ruly Dwi Setyaningrum, S.Kom', 'nip' => '19850418 201001 2 031'],
            ['nama' => 'Ary Sunaryo, ST., M.Pd', 'nip' => '19770306 201101 1 003'],
            ['nama' => 'Listyana Hartati, S.Kom., M.Pd', 'nip' => '19820204 201101 2 006'],
            ['nama' => 'Dhuana Putri Puspitasary, S.Pd', 'nip' => '19870217 201101 2 012'],
            ['nama' => 'Angga Widhy Wirawan, S.Pd., M.Pd.', 'nip' => '19860127 201101 1 013'],
            ['nama' => 'Mas\'an Widodo, S.Pd., M.T.', 'nip' => '19830113 200901 1 003'],
            ['nama' => 'Endik Kuswantoro, S.Kom., M.T', 'nip' => '19850203 201101 1 012'],
            ['nama' => 'Anang Prasetyo, S.Pd', 'nip' => '19711129 201101 1 002'],
            ['nama' => 'Arif Setyobudi, S.Pd', 'nip' => '19780830 200701 1 017'],
            ['nama' => 'Benny Mamora, S.Kom', 'nip' => '19760719 200901 1 003'],
            ['nama' => 'Danang Anjar Hymawanto, S.Pd', 'nip' => '19850316 201101 1 012'],
            ['nama' => 'Hardini Indahing Budi, S.E., M.Pd.', 'nip' => '19820822 201407 2 002'],
            ['nama' => 'Erwan Septiyono, S.Pd', 'nip' => '19900907 201903 1 004'],
            ['nama' => 'Istiana Suhartati, S.T', 'nip' => '19910708 201903 2 017'],
            ['nama' => 'Risqi Nur Imama, S.Tr.Par', 'nip' => '19960728 202012 2 013'],
            ['nama' => 'Badrus Sulaiman, S.Pd.', 'nip' => '19900418 202012 1 017'],
            ['nama' => 'Nurul Azizah, S.Pd', 'nip' => '19780822 202221 2 006'],
            ['nama' => 'Hendro Suwignyo, ST', 'nip' => '19771112 202221 1 007'],
            ['nama' => 'Dyah Esti Rahayu, S.Pd', 'nip' => '19740805 202221 2 008'],
            ['nama' => 'Baskoro, S.Si', 'nip' => '19810124 202221 1 012'],
            ['nama' => 'Luluk Munfarida, S.Pd', 'nip' => '19830306 202221 2 048'],
            ['nama' => 'Khuriyatul Kamila, S.Si', 'nip' => '19830707 202221 2 027'],
            ['nama' => 'Veronica Damay Rulitasari, S.Pd', 'nip' => '19880521 202221 2 020'],
            ['nama' => 'Nur Nastutisari, S.ST.Par.', 'nip' => '19870303 202221 2 026'],
            ['nama' => 'Alfinu Farikh Abdillah, S.Pd.I', 'nip' => '19820318 202221 1 012'],
            ['nama' => 'Khoyrotun Hisani, S.Sn', 'nip' => '19910914 202221 2 015'],
            ['nama' => 'Joko Priyanto, S.Kom', 'nip' => '19911103 202221 1 007'],
            ['nama' => 'Elyana Frisca Monica, S.Pd', 'nip' => '19920504 202221 2 022'],
            ['nama' => 'Rika Okta Maulida, S.Ds.', 'nip' => '19951027 202221 2 012'],
            ['nama' => 'Dra. Hanik Pangestuti', 'nip' => '19670512 202221 2 003'],
            ['nama' => 'Sa\'ad Wazis Hiedayat, S.Pd', 'nip' => '19850112 202221 1 020'],
            ['nama' => 'Shinta Indyar Shanty Susanto, S.Kom', 'nip' => '19850121 202221 2 037'],
            ['nama' => 'Widodo, S.Pd', 'nip' => '19871014 202221 1 014'],
            ['nama' => 'Nur Eko Wahyuningsih, S.Pd', 'nip' => '19940101 202221 2 024'],
            ['nama' => 'Kurnila Putri Islamawati, S.Pd', 'nip' => '19970318 202221 2 010'],
            ['nama' => 'Sulistyowati, SS', 'nip' => '19710728 202321 2 004'],
            ['nama' => 'Wiwik Yuniarsih, S.Pd', 'nip' => '19750616 202321 2 007'],
            ['nama' => 'Fajar Luthfianto, S.Pd', 'nip' => '19780810 202321 1 005'],
            ['nama' => 'Fajar Wahyu Pratiwi, S.S', 'nip' => '19820529 202321 2 015'],
            ['nama' => 'Agung Yulianto, S.Pd', 'nip' => '19820718 202321 1 006'],
            ['nama' => 'Sri Kusumastuti, S.Pd', 'nip' => '19830331 202321 2 015'],
            ['nama' => 'Yuli Ratnasari, S.Pd', 'nip' => '19840730 202321 2 018'],
            ['nama' => 'Fitria Renytasari, S.Pd', 'nip' => '19850627 202321 2 020'],
            ['nama' => 'Tutut Sriatin, S.Pd', 'nip' => '19710523 202421 2 002'],
            ['nama' => 'Dwi Kuswanto, S.Pd', 'nip' => '19751113 202421 1 001'],
            ['nama' => 'Erna Qoriah, S.E.', 'nip' => '19751211 202421 2 008'],
            ['nama' => 'Pipit Ambarwati, S.Pd', 'nip' => '19780701 202421 2 002'],
            ['nama' => 'Fitri Amaliyah, S.Pd', 'nip' => '19800312 202421 2 013'],
            ['nama' => 'Niken Dewi Hastika, S.Pd', 'nip' => '19880113 202421 2 002'],
            ['nama' => 'Ista Nofasari, S.Pd', 'nip' => '19880503 202421 2 030'],
            ['nama' => 'Anisa Kusumawati, S.Pd', 'nip' => '19880521 202421 2 009'],
            ['nama' => 'Mega Mahardika, S.Pd', 'nip' => '19920423 202421 2 010'],
            ['nama' => 'Rifkotin Na\'imah, S.Pd', 'nip' => '19940415 202421 2 053'],
            ['nama' => 'Sinta Lestari, S.Pd.I', 'nip' => '19901031 202521 2 015'],
            ['nama' => 'Astra Bella Flamboyan, S.Psi', 'nip' => '19900806 202521 2 028'],
            ['nama' => 'Fitria Diah Ayu Hartati, S.Pd', 'nip' => '19970204 202521 2 014'],
            ['nama' => 'Dra. Susakti Yuharini', 'nip' => '19661108 202521 2 001'],
            ['nama' => 'Agus Pramono, S.Sn', 'nip' => '19700717 202521 1 036'],
            ['nama' => 'Ajeng Okvitasari, S.Pd', 'nip' => '19871004 202521 2 098'],
            ['nama' => 'Nishfu Laili, S.Pd', 'nip' => '19860414 202521 2 103'],
            ['nama' => 'Andika Christian Sasmita, S.ST', 'nip' => '19830102 202521 1 100'],
            ['nama' => 'Muto\'atul Khosi\'ah, S.Pd', 'nip' => '19821215 202521 2 065'],
            ['nama' => 'Bella Prakoso, S.Pd', 'nip' => '19871213 202521 1 096'],
            ['nama' => 'Yustin Febrini, S.Pd', 'nip' => '19920205 202521 2 129'],
            ['nama' => 'Siti Maisaroh, S.Pd', 'nip' => '19850926 202521 2 055'],
            ['nama' => 'Laili Ermawati, S.Pd', 'nip' => '19890418 202521 2 117'],
            ['nama' => 'Muhammad Fajar Assidiqi, S.Pd', 'nip' => '19971223 202521 1 078'],
            ['nama' => 'Yuni Jiastuti, S.Pd', 'nip' => '19820609 202521 2 057'],
            ['nama' => 'Tuhu Eries Kudori, S.Sn', 'nip' => '19781006 202521 1 046'],
            ['nama' => 'Eko Saputro, S.Pd', 'nip' => '19961117 202521 1 096'],
            ['nama' => 'Zainul Arifin, S.Pd', 'nip' => '19871116 202521 1 085'],
            ['nama' => 'Endang Safitri, S.Pd', 'nip' => null],
            ['nama' => 'Mufatiroh, S.Ag', 'nip' => null],
            ['nama' => 'Abdul Rohman, S.Pd', 'nip' => null],
            ['nama' => 'Rizki Putri Wulandari, S.Pd', 'nip' => null],
            ['nama' => 'Pdt. Juklianus Steven Immanuel Bahihi, S.Pdk., M.Pd', 'nip' => null],
            ['nama' => 'Sukamto, S.Ag', 'nip' => null],
        ];

        $normal = static function (string $value): string {
            return preg_replace('/[^a-z0-9]+/i', '', strtolower(trim($value)));
        };

        foreach ($dataGuru as $item) {
            $nama = $item['nama'];
            $nip = $item['nip'];
            $username = Str::slug($nama, '');

            // Cari user berdasarkan nama yang sudah ada atau username yang mirip
            $cleanName = $normal($nama);
            $user = User::all()->first(function ($u) use ($cleanName, $normal) {
                return $normal($u->name) === $cleanName;
            });

            if (! $user && $nip) {
                $cleanNip = str_replace([' ', '-', '.'], '', $nip);
                $user = User::where('nip', $nip)
                    ->orWhere('nip', $cleanNip)
                    ->first();
            }

            if ($user) {
                $user->update([
                    'name' => $nama,
                    'username' => $user->username ?: $username,
                    'nip' => $nip,
                    'role' => 'guru',
                ]);
            } else {
                User::create([
                    'name' => $nama,
                    'username' => $username,
                    'nip' => $nip,
                    'email' => null,
                    'password' => Hash::make('guru123'),
                    'role' => 'guru',
                ]);
            }
        }
    }
}
