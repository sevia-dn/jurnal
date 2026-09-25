<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Admin
        User::updateOrCreate(
            ['username' => 'admin1'],
            [
                'name' => 'Admin Sekolah',
                'nip' => null,
                'email' => null,
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ]
        );

        // 3. Akun Guru. Status Wakasek Kesiswaan diatur dari jadwal sumber September.
        User::updateOrCreate(
            ['username' => 'fajarsiswanto'],
            [
                'name' => 'Fajar Siswanto S.Pd',
                'nip' => '198501012010011003',
                'email' => null,
                'password' => Hash::make('fajar123'),
                'role' => 'guru',
                'is_waka' => false,
            ]
        );

        // 4. 48 Akun Pengurus / Sekretaris Kelas (Sesuai Jadwal KBM SMKN 1 Boyolangu)
        $daftarKelas = [
            // Kelas X (24 Kelas)
            ['name' => 'Pengurus Kelas X TKI 1', 'username' => 'xtki1'],
            ['name' => 'Pengurus Kelas X TKI 2', 'username' => 'xtki2'],
            ['name' => 'Pengurus Kelas X RPL 1', 'username' => 'xrpl1'],
            ['name' => 'Pengurus Kelas X RPL 2', 'username' => 'xrpl2'],
            ['name' => 'Pengurus Kelas X TKJ 1', 'username' => 'xtkj1'],
            ['name' => 'Pengurus Kelas X TKJ 2', 'username' => 'xtkj2'],
            ['name' => 'Pengurus Kelas X BD 1', 'username' => 'xbd1'],
            ['name' => 'Pengurus Kelas X BD 2', 'username' => 'xbd2'],
            ['name' => 'Pengurus Kelas X BD 3', 'username' => 'xbd3'],
            ['name' => 'Pengurus Kelas X MP 1', 'username' => 'xmp1'],
            ['name' => 'Pengurus Kelas X MP 2', 'username' => 'xmp2'],
            ['name' => 'Pengurus Kelas X MP 3', 'username' => 'xmp3'],
            ['name' => 'Pengurus Kelas X MP 4', 'username' => 'xmp4'],
            ['name' => 'Pengurus Kelas X AK 1', 'username' => 'xak1'],
            ['name' => 'Pengurus Kelas X AK 2', 'username' => 'xak2'],
            ['name' => 'Pengurus Kelas X AK 3', 'username' => 'xak3'],
            ['name' => 'Pengurus Kelas X AK 4', 'username' => 'xak4'],
            ['name' => 'Pengurus Kelas X ULW', 'username' => 'xulw'],
            ['name' => 'Pengurus Kelas X DKV 1', 'username' => 'xdkv1'],
            ['name' => 'Pengurus Kelas X DKV 2', 'username' => 'xdkv2'],
            ['name' => 'Pengurus Kelas X PSPT 1', 'username' => 'xpspt1'],
            ['name' => 'Pengurus Kelas X PSPT 2', 'username' => 'xpspt2'],
            ['name' => 'Pengurus Kelas X AN 1', 'username' => 'xan1'],
            ['name' => 'Pengurus Kelas X AN 2', 'username' => 'xan2'],

            // Kelas XI (24 Kelas)
            ['name' => 'Pengurus Kelas XI TKI 1', 'username' => 'xitki1'],
            ['name' => 'Pengurus Kelas XI TKI 2', 'username' => 'xitki2'],
            ['name' => 'Pengurus Kelas XI RPL 1', 'username' => 'xirpl1'],
            ['name' => 'Pengurus Kelas XI RPL 2', 'username' => 'xirpl2'],
            ['name' => 'Pengurus Kelas XI TKJ 1', 'username' => 'xitkj1'],
            ['name' => 'Pengurus Kelas XI TKJ 2', 'username' => 'xitkj2'],
            ['name' => 'Pengurus Kelas XI BD 1', 'username' => 'xibd1'],
            ['name' => 'Pengurus Kelas XI BD 2', 'username' => 'xibd2'],
            ['name' => 'Pengurus Kelas XI BD 3', 'username' => 'xibd3'],
            ['name' => 'Pengurus Kelas XI MP 1', 'username' => 'ximp1'],
            ['name' => 'Pengurus Kelas XI MP 2', 'username' => 'ximp2'],
            ['name' => 'Pengurus Kelas XI MP 3', 'username' => 'ximp3'],
            ['name' => 'Pengurus Kelas XI MP 4', 'username' => 'ximp4'],
            ['name' => 'Pengurus Kelas XI AK 1', 'username' => 'xiak1'],
            ['name' => 'Pengurus Kelas XI AK 2', 'username' => 'xiak2'],
            ['name' => 'Pengurus Kelas XI AK 3', 'username' => 'xiak3'],
            ['name' => 'Pengurus Kelas XI AK 4', 'username' => 'xiak4'],
            ['name' => 'Pengurus Kelas XI ULW', 'username' => 'xiulw'],
            ['name' => 'Pengurus Kelas XI DKV 1', 'username' => 'xidkv1'],
            ['name' => 'Pengurus Kelas XI DKV 2', 'username' => 'xidkv2'],
            ['name' => 'Pengurus Kelas XI PSPT 1', 'username' => 'xipspt1'],
            ['name' => 'Pengurus Kelas XI PSPT 2', 'username' => 'xipspt2'],
            ['name' => 'Pengurus Kelas XI AN 1', 'username' => 'xian1'],
            ['name' => 'Pengurus Kelas XI AN 2', 'username' => 'xian2'],

            // Kelas XII (24 Kelas)
            ['name' => 'Pengurus Kelas XII TKI 1', 'username' => 'xiitki1'],
            ['name' => 'Pengurus Kelas XII TKI 2', 'username' => 'xiitki2'],
            ['name' => 'Pengurus Kelas XII RPL 1', 'username' => 'xiirpl1'],
            ['name' => 'Pengurus Kelas XII RPL 2', 'username' => 'xiirpl2'],
            ['name' => 'Pengurus Kelas XII TKJ 1', 'username' => 'xiitkj1'],
            ['name' => 'Pengurus Kelas XII TKJ 2', 'username' => 'xiitkj2'],
            ['name' => 'Pengurus Kelas XII BD 1', 'username' => 'xiibd1'],
            ['name' => 'Pengurus Kelas XII BD 2', 'username' => 'xiibd2'],
            ['name' => 'Pengurus Kelas XII BD 3', 'username' => 'xiibd3'],
            ['name' => 'Pengurus Kelas XII MP 1', 'username' => 'xiimp1'],
            ['name' => 'Pengurus Kelas XII MP 2', 'username' => 'xiimp2'],
            ['name' => 'Pengurus Kelas XII MP 3', 'username' => 'xiimp3'],
            ['name' => 'Pengurus Kelas XII MP 4', 'username' => 'xiimp4'],
            ['name' => 'Pengurus Kelas XII AK 1', 'username' => 'xiiak1'],
            ['name' => 'Pengurus Kelas XII AK 2', 'username' => 'xiiak2'],
            ['name' => 'Pengurus Kelas XII AK 3', 'username' => 'xiiak3'],
            ['name' => 'Pengurus Kelas XII AK 4', 'username' => 'xiiak4'],
            ['name' => 'Pengurus Kelas XII ULW', 'username' => 'xiiulw'],
            ['name' => 'Pengurus Kelas XII DKV 1', 'username' => 'xiidkv1'],
            ['name' => 'Pengurus Kelas XII DKV 2', 'username' => 'xiidkv2'],
            ['name' => 'Pengurus Kelas XII PSPT 1', 'username' => 'xiipspt1'],
            ['name' => 'Pengurus Kelas XII PSPT 2', 'username' => 'xiipspt2'],
            ['name' => 'Pengurus Kelas XII AN 1', 'username' => 'xiian1'],
            ['name' => 'Pengurus Kelas XII AN 2', 'username' => 'xiian2'],
        ];

        $daftarKelas = array_map(static function (array $kelas): array {
            $kelas['password'] = $kelas['username'];

            return $kelas;
        }, $daftarKelas);

        foreach ($daftarKelas as $kelas) {
            User::updateOrCreate(
                ['username' => $kelas['username']],
                [
                    'name' => $kelas['name'],
                    'nip' => null,
                    'email' => null,
                    'password' => Hash::make($kelas['password']),
                    'role' => 'pengurus_kelas',
                ]
            );
        }

        // 5. Seluruh Akun Guru Pengajar (Login via NIP atau Username)
        $daftarGuru = [
            ['name' => 'Trisno Wibowo, S.Pd., M.M', 'username' => 'trisnowibowo', 'nip' => '19810115 200312 1 003', 'password' => 'trisno123'],
            ['name' => 'Martiin, S.Pd', 'username' => 'martiinspd', 'nip' => '19670604 198903 2 009', 'password' => 'martiin123'],
            ['name' => 'Yani, S.Pd.', 'username' => 'yanispd', 'nip' => '19661207 199412 1 003', 'password' => 'yani123'],
            ['name' => 'Siti Umiharsih, S.Pd', 'username' => 'sitiumiharsihspd', 'nip' => '19700825 199512 2 001', 'password' => 'siti123'],
            ['name' => 'Winarsih, S.Pd, M.Pd', 'username' => 'winarsihspdmpd', 'nip' => '19700325 200312 2 007', 'password' => 'winarsih123'],
            ['name' => 'Dwi Rini Manfaati, S.Pd', 'username' => 'dwirinimanfaatispd', 'nip' => '19701017 199703 2 004', 'password' => 'dwi123'],
            ['name' => 'Dra. Anik Indriani', 'username' => 'anikindriani', 'nip' => '19681128 200501 2 004', 'password' => 'anik123'],
            ['name' => 'Sri Rahayu, S.Pd', 'username' => 'srirahayuspd', 'nip' => '19700304 200501 2 006', 'password' => 'sri123'],
            ['name' => 'Arvia Rienetasary, S.Pd', 'username' => 'arviarienetasaryspd', 'nip' => '19750304 200604 2 017', 'password' => 'arvia123'],
            ['name' => 'Peni Wulandari, S.Pd', 'username' => 'peniwulandarispd', 'nip' => '19730601 200604 2 024', 'password' => 'peni123'],
            ['name' => 'Rindang Rejeki, S.Pd', 'username' => 'rindangrejekispd', 'nip' => '19691006 200701 2 022', 'password' => 'rindang123'],
            ['name' => 'Erna Rinawati, S.Pd', 'username' => 'ernarinawatispd', 'nip' => '19710520 200604 2 018', 'password' => 'erna123'],
            ['name' => 'Sunarti, S.Pd', 'username' => 'sunartispd', 'nip' => '19730108 200604 2 015', 'password' => 'sunarti123'],
            ['name' => 'Setiyo Winarko, S.Pd', 'username' => 'setiyowinarkospd', 'nip' => '19721030 200312 1 002', 'password' => 'setiyo123'],
            ['name' => 'Isti Mufadah, S.Pd', 'username' => 'istimufadahspd', 'nip' => '19780202 200604 2 027', 'password' => 'isti123'],
            ['name' => 'Indayah, S.Pd., M.Pd', 'username' => 'indayahspdmpd', 'nip' => '19731001 200604 2 012', 'password' => 'indayah123'],
            ['name' => 'Umi Kulsum, S.Pd', 'username' => 'umikulsumspd', 'nip' => '19690425 200701 2 025', 'password' => 'umi123'],
            ['name' => 'Rulik Indrawati, S.Pd', 'username' => 'rulikindrawatispd', 'nip' => '19691126 200701 2 007', 'password' => 'rulik123'],
            ['name' => 'Lilik Suratmi, S.Pd', 'username' => 'liliksuratmispd', 'nip' => '19690814 200701 2 026', 'password' => 'lilik123'],
            ['name' => 'Basuki Sarjono, S.Pd', 'username' => 'basukisarjonospd', 'nip' => '19670421 200701 1 026', 'password' => 'basuki123'],
            ['name' => 'Titik Samsistini, S.Pd', 'username' => 'titiksamsistinispd', 'nip' => '19680825 200801 2 019', 'password' => 'titik123'],
            ['name' => 'Endang Ary Handayani, S.T., M.Pd', 'username' => 'endangaryhandayanistmpd', 'nip' => '19760210 200801 2 017', 'password' => 'endang123'],
            ['name' => 'Purwati, S.Pd', 'username' => 'purwatispd', 'nip' => '19690616 200701 2 026', 'password' => 'purwati123'],
            ['name' => 'Ninik Sriwidayati, S.Pd., M.Pd', 'username' => 'niniksriwidayatispdmpd', 'nip' => '19750409 200701 2 010', 'password' => 'ninik123'],
            ['name' => 'Agustina Mardika Rini, S.Pd., M.Pd.', 'username' => 'agustinamardikarinispdmpd', 'nip' => '19770817 200701 2 012', 'password' => 'agustina123'],
            ['name' => 'Komariyah, S.Pd', 'username' => 'komariyahspd', 'nip' => '19690805 200801 2 025', 'password' => 'komariyah123'],
            ['name' => 'Muashofah, M.Pd', 'username' => 'muashofahmpd', 'nip' => '19710806 200801 2 012', 'password' => 'muashofah123'],
            ['name' => 'Atih Wilupi, S.E, M.Pd', 'username' => 'atihwilupisempd', 'nip' => '19690915 200801 2 028', 'password' => 'atih123'],
            ['name' => 'Winartin, S.Pd', 'username' => 'winartin', 'nip' => '19801224 200801 2 016', 'password' => 'winartin123'],
            ['name' => 'Siti Khoiriyah, S.Pd', 'username' => 'sitikhoiriyahspd', 'nip' => '19681014 200801 2 011', 'password' => 'siti123'],
            ['name' => 'Sri Subekti, S.Pd', 'username' => 'srisubektispd', 'nip' => '19690917 200701 2 012', 'password' => 'sri123'],
            ['name' => 'Kasmi, S.Pd., M.Pd', 'username' => 'kasmispdmpd', 'nip' => '19700831 200801 2 017', 'password' => 'kasmi123'],
            ['name' => 'Ilham Sungeidi, S.Pd', 'username' => 'ilhamsungeidispd', 'nip' => '19700824 200801 1 008', 'password' => 'ilham123'],
            ['name' => 'Lutfia Marsalina, S.Pd.I, M.Pd.', 'username' => 'lutfiamarsalina', 'nip' => '19800329 200901 2 006', 'password' => 'lutfia123'],
            ['name' => 'Indriati, S.Pd', 'username' => 'indriatispd', 'nip' => '19850910 200903 2 009', 'password' => 'indriati123'],
            ['name' => 'Agus Fahruddy, S.Pd., M.Pd', 'username' => 'agusfahruddyspdmpd', 'nip' => '19761118 200701 1 004', 'password' => 'agus123'],
            ['name' => 'Titin Sukmasari, S.Pd., M.Pd', 'username' => 'titinsukmasarispdmpd', 'nip' => '19790202 200701 2 025', 'password' => 'titin123'],
            ['name' => 'Dian Mawarti, S.Pd', 'username' => 'dianmawartispd', 'nip' => '19800410 200901 2 007', 'password' => 'dian123'],
            ['name' => 'Niken Hari Pratiwi, S.Psi., M.Pd', 'username' => 'nikenharipratiwispsimpd', 'nip' => '19820303 200901 2 009', 'password' => 'niken123'],
            ['name' => 'Siti Munawaroh, S.Kom., M.Pd', 'username' => 'sitimunawarohskommpd', 'nip' => '19740914 200901 2 001', 'password' => 'siti123'],
            ['name' => 'Dwi Nova Setyandari, S.Pd', 'username' => 'dwinovasetyandarispd', 'nip' => '19821103 201001 2 025', 'password' => 'dwi123'],
            ['name' => 'Diana Hartanti, S.T., M.Pd', 'username' => 'dianahartantistmpd', 'nip' => '19801026 201001 2 016', 'password' => 'diana123'],
            ['name' => 'Andri Retno Yuli Astuti, S.Pd', 'username' => 'andriretnoyuliastutispd', 'nip' => '19730719 201001 2 002', 'password' => 'andri123'],
            ['name' => 'Siswanti Purwaningsih, S.T., M.Pd', 'username' => 'siswantipurwaningsihstmpd', 'nip' => '19770426 201001 2 008', 'password' => 'siswanti123'],
            ['name' => 'Ayu Puspitorini, ST', 'username' => 'ayupuspitorinist', 'nip' => '19760826 201001 2 010', 'password' => 'ayu123'],
            ['name' => 'Elysa Yuli Nur\'aini, S.Si', 'username' => 'elysayulinurainissi', 'nip' => '19800723 201001 2 016', 'password' => 'elysa123'],
            ['name' => 'Agus Muharyanto, M.Pd', 'username' => 'agusmuharyantompd', 'nip' => '19710826 200604 1 011', 'password' => 'agus123'],
            ['name' => 'Septiani, S.Pd., M.Pd', 'username' => 'septianispdmpd', 'nip' => '19781001 200604 2 021', 'password' => 'septiani123'],
            ['name' => 'Retno Widyastuti, S.Pd., M.Pd.', 'username' => 'retnowidyastutispdmpd', 'nip' => '19870316 200901 2 002', 'password' => 'retno123'],
            ['name' => 'Ratih Dian Irawati, SE', 'username' => 'ratihdianirawatise', 'nip' => '19840222 200902 2 007', 'password' => 'ratih123'],
            ['name' => 'Andri Krisdianto, SE., M.Pd', 'username' => 'andrikrisdiantosempd', 'nip' => '19830101 201001 1 042', 'password' => 'andri123'],
            ['name' => 'Ruly Dwi Setyaningrum, S.Kom', 'username' => 'rulydwisetyaningrum', 'nip' => '19850418 201001 2 031', 'password' => 'ruly123'],
            ['name' => 'Ary Sunaryo, ST., M.Pd', 'username' => 'arysunaryostmpd', 'nip' => '19770306 201101 1 003', 'password' => 'ary123'],
            ['name' => 'Listyana Hartati, S.Kom., M.Pd', 'username' => 'listyanahartatiskommpd', 'nip' => '19820204 201101 2 006', 'password' => 'listyana123'],
            ['name' => 'Dhuana Putri Puspitasary, S.Pd', 'username' => 'dhuanaputripuspitasaryspd', 'nip' => '19870217 201101 2 012', 'password' => 'dhuana123'],
            ['name' => 'Angga Widhy Wirawan, S.Pd., M.Pd.', 'username' => 'anggawidhy', 'nip' => '19860127 201101 1 013', 'password' => 'angga123'],
            ['name' => 'Mas\'an Widodo, S.Pd., M.T.', 'username' => 'masanwidodospdmt', 'nip' => '19830113 200901 1 003', 'password' => 'masan123'],
            ['name' => 'Endik Kuswantoro, S.Kom., M.T', 'username' => 'endikkuswantoroskommt', 'nip' => '19850203 201101 1 012', 'password' => 'endik123'],
            ['name' => 'Anang Prasetyo, S.Pd', 'username' => 'anangprasetyospd', 'nip' => '19711129 201101 1 002', 'password' => 'anang123'],
            ['name' => 'Arif Setyobudi, S.Pd', 'username' => 'arifsetyobudispd', 'nip' => '19780830 200701 1 017', 'password' => 'arif123'],
            ['name' => 'Benny Mamora, S.Kom', 'username' => 'bennymamoraskom', 'nip' => '19760719 200901 1 003', 'password' => 'benny123'],
            ['name' => 'Danang Anjar Hymawanto, S.Pd', 'username' => 'dananganjarhymawantospd', 'nip' => '19850316 201101 1 012', 'password' => 'danang123'],
            ['name' => 'Hardini Indahing Budi, S.E., M.Pd.', 'username' => 'hardiniindahingbudisempd', 'nip' => '19820822 201407 2 002', 'password' => 'hardini123'],
            ['name' => 'Erwan Septiyono, S.Pd', 'username' => 'erwanseptiyonospd', 'nip' => '19900907 201903 1 004', 'password' => 'erwan123'],
            ['name' => 'Istiana Suhartati, S.T', 'username' => 'istianasuhartatist', 'nip' => '19910708 201903 2 017', 'password' => 'istiana123'],
            ['name' => 'Risqi Nur Imama, S.Tr.Par', 'username' => 'risqinurimamasstrpar', 'nip' => '19960728 202012 2 013', 'password' => 'risqi123'],
            ['name' => 'Badrus Sulaiman, S.Pd.', 'username' => 'badrussulaiman', 'nip' => '19900418 202012 1 017', 'password' => 'badrus123'],
            ['name' => 'Nurul Azizah, S.Pd', 'username' => 'nurulazizahspd', 'nip' => '19780822 202221 2 006', 'password' => 'nurul123'],
            ['name' => 'Hendro Suwignyo, ST', 'username' => 'hendrosuwignyost', 'nip' => '19771112 202221 1 007', 'password' => 'hendro123'],
            ['name' => 'Dyah Esti Rahayu, S.Pd', 'username' => 'dyahestirahayuspd', 'nip' => '19740805 202221 2 008', 'password' => 'dyah123'],
            ['name' => 'Baskoro, S.Si', 'username' => 'baskorossi', 'nip' => '19810124 202221 1 012', 'password' => 'baskoro123'],
            ['name' => 'Luluk Munfarida, S.Pd', 'username' => 'lulukmunfaridaspd', 'nip' => '19830306 202221 2 048', 'password' => 'luluk123'],
            ['name' => 'Khuriyatul Kamila, S.Si', 'username' => 'khuriyatulkamilassi', 'nip' => '19830707 202221 2 027', 'password' => 'khuriyatul123'],
            ['name' => 'Veronica Damay Rulitasari, S.Pd', 'username' => 'veronicadamayrulitasarispd', 'nip' => '19880521 202221 2 020', 'password' => 'veronica123'],
            ['name' => 'Nur Nastutisari, S.ST.Par.', 'username' => 'nurnastutisarisstpar', 'nip' => '19870303 202221 2 026', 'password' => 'nur123'],
            ['name' => 'Alfinu Farikh Abdillah, S.Pd.I', 'username' => 'alfinufarikhabdillahspdi', 'nip' => '19820318 202221 1 012', 'password' => 'alfinu123'],
            ['name' => 'Khoyrotun Hisani, S.Sn', 'username' => 'khoyrotunhisanissn', 'nip' => '19910914 202221 2 015', 'password' => 'khoyrotun123'],
            ['name' => 'Joko Priyanto, S.Kom', 'username' => 'jokopriyantoskom', 'nip' => '19911103 202221 1 007', 'password' => 'joko123'],
            ['name' => 'Elyana Frisca Monica, S.Pd', 'username' => 'elyanafrisca', 'nip' => '19920504 202221 2 022', 'password' => 'elyana123'],
            ['name' => 'Rika Okta Maulida, S.Ds.', 'username' => 'rikaoktamaulidasds', 'nip' => '19951027 202221 2 012', 'password' => 'rika123'],
            ['name' => 'Dra. Hanik Pangestuti', 'username' => 'hanikpangestuti', 'nip' => '19670512 202221 2 003', 'password' => 'hanik123'],
            ['name' => 'Sa\'ad Wazis Hiedayat, S.Pd', 'username' => 'saadwazishiedayatspd', 'nip' => '19850112 202221 1 020', 'password' => 'saad123'],
            ['name' => 'Shinta Indyar Shanty Susanto, S.Kom', 'username' => 'shintaindyarshantysusantoskom', 'nip' => '19850121 202221 2 037', 'password' => 'shinta123'],
            ['name' => 'Widodo, S.Pd', 'username' => 'widodospd', 'nip' => '19871014 202221 1 014', 'password' => 'widodo123'],
            ['name' => 'Nur Eko Wahyuningsih, S.Pd', 'username' => 'nurekowahyuningsihspd', 'nip' => '19940101 202221 2 024', 'password' => 'nur123'],
            ['name' => 'Kurnila Putri Islamawati, S.Pd', 'username' => 'kurnilaputri', 'nip' => '19970318 202221 2 010', 'password' => 'kurnila123'],
            ['name' => 'Sulistyowati, SS', 'username' => 'sulistyowatiss', 'nip' => '19710728 202321 2 004', 'password' => 'sulistyowati123'],
            ['name' => 'Wiwik Yuniarsih, S.Pd', 'username' => 'wiwikyuniarsih', 'nip' => '19750616 202321 2 007', 'password' => 'wiwik123'],
            ['name' => 'Fajar Luthfianto, S.Pd', 'username' => 'fajarluthfiantospd', 'nip' => '19780810 202321 1 005', 'password' => 'fajar123'],
            ['name' => 'Fajar Wahyu Pratiwi, S.S', 'username' => 'fajarwahyu', 'nip' => '19820529 202321 2 015', 'password' => 'fajar123'],
            ['name' => 'Agung Yulianto, S.Pd', 'username' => 'agungyuliantospd', 'nip' => '19820718 202321 1 006', 'password' => 'agung123'],
            ['name' => 'Sri Kusumastuti, S.Pd', 'username' => 'srikusumastutispd', 'nip' => '19830331 202321 2 015', 'password' => 'sri123'],
            ['name' => 'Yuli Ratnasari, S.Pd', 'username' => 'yuliratnasarispd', 'nip' => '19840730 202321 2 018', 'password' => 'yuli123'],
            ['name' => 'Fitria Renytasari, S.Pd', 'username' => 'fitriarenytasarispd', 'nip' => '19850627 202321 2 020', 'password' => 'fitria123'],
            ['name' => 'Tutut Sriatin, S.Pd', 'username' => 'tututsriatinspd', 'nip' => '19710523 202421 2 002', 'password' => 'tutut123'],
            ['name' => 'Dwi Kuswanto, S.Pd', 'username' => 'dwikuswantospd', 'nip' => '19751113 202421 1 001', 'password' => 'dwi123'],
            ['name' => 'Erna Qoriah, S.E.', 'username' => 'ernaqoriahse', 'nip' => '19751211 202421 2 008', 'password' => 'erna123'],
            ['name' => 'Pipit Ambarwati, S.Pd', 'username' => 'pipitambarwatispd', 'nip' => '19780701 202421 2 002', 'password' => 'pipit123'],
            ['name' => 'Fitri Amaliyah, S.Pd', 'username' => 'fitriamaliyah', 'nip' => '19800312 202421 2 013', 'password' => 'fitri123'],
            ['name' => 'Niken Dewi Hastika, S.Pd', 'username' => 'nikendewihastikaspd', 'nip' => '19880113 202421 2 002', 'password' => 'niken123'],
            ['name' => 'Ista Nofasari, S.Pd', 'username' => 'istanofasarispd', 'nip' => '19880503 202421 2 030', 'password' => 'ista123'],
            ['name' => 'Anisa Kusumawati, S.Pd', 'username' => 'anisakusumawatispd', 'nip' => '19880521 202421 2 009', 'password' => 'anisa123'],
            ['name' => 'Mega Mahardika, S.Pd', 'username' => 'megamahardikaspd', 'nip' => '19920423 202421 2 010', 'password' => 'mega123'],
            ['name' => 'Rifkotin Na\'imah, S.Pd', 'username' => 'rifkotinnaimahspd', 'nip' => '19940415 202421 2 053', 'password' => 'rifkotin123'],
            ['name' => 'Sinta Lestari, S.Pd.I', 'username' => 'sintalestarispdi', 'nip' => '19901031 202521 2 015', 'password' => 'sinta123'],
            ['name' => 'Astra Bella Flamboyan, S.Psi', 'username' => 'astrabellaflamboyanspsi', 'nip' => '19900806 202521 2 028', 'password' => 'astra123'],
            ['name' => 'Fitria Diah Ayu Hartati, S.Pd', 'username' => 'fitriadiahayuhartatispd', 'nip' => '19970204 202521 2 014', 'password' => 'fitria123'],
            ['name' => 'Dra. Susakti Yuharini', 'username' => 'drasusaktiyuharini', 'nip' => '19661108 202521 2 001', 'password' => 'susakti123'],
            ['name' => 'Agus Pramono, S.Sn', 'username' => 'aguspramonossn', 'nip' => '19700717 202521 1 036', 'password' => 'agus123'],
            ['name' => 'Ajeng Okvitasari, S.Pd', 'username' => 'ajengokvitasarispd', 'nip' => '19871004 202521 2 098', 'password' => 'ajeng123'],
            ['name' => 'Nishfu Laili, S.Pd', 'username' => 'nishfulailispd', 'nip' => '19860414 202521 2 103', 'password' => 'nishfu123'],
            ['name' => 'Andika Christian Sasmita, S.ST', 'username' => 'andikachristiansasmitasst', 'nip' => '19830102 202521 1 100', 'password' => 'andika123'],
            ['name' => 'Muto\'atul Khosi\'ah, S.Pd', 'username' => 'mutoatulkhosiahspd', 'nip' => '19821215 202521 2 065', 'password' => 'mutoatul123'],
            ['name' => 'Bella Prakoso, S.Pd', 'username' => 'bellaprakosospd', 'nip' => '19871213 202521 1 096', 'password' => 'bella123'],
            ['name' => 'Yustin Febrini, S.Pd', 'username' => 'yustinfebrini', 'nip' => '19920205 202521 2 129', 'password' => 'yustin123'],
            ['name' => 'Siti Maisaroh, S.Pd', 'username' => 'sitimaisarohspd', 'nip' => '19850926 202521 2 055', 'password' => 'siti123'],
            ['name' => 'Laili Ermawati, S.Pd', 'username' => 'lailiermawati', 'nip' => '19890418 202521 2 117', 'password' => 'laili123'],
            ['name' => 'Muhammad Fajar Assidiqi, S.Pd', 'username' => 'muhammadfajarassidiqispd', 'nip' => '19971223 202521 1 078', 'password' => 'muhammad123'],
            ['name' => 'Yuni Jiastuti, S.Pd', 'username' => 'yunijiastutispd', 'nip' => '19820609 202521 2 057', 'password' => 'yuni123'],
            ['name' => 'Tuhu Eries Kudori, S.Sn', 'username' => 'tuhuerieskudorissn', 'nip' => '19781006 202521 1 046', 'password' => 'tuhu123'],
            ['name' => 'Eko Saputro, S.Pd', 'username' => 'ekosaputrospd', 'nip' => '19961117 202521 1 096', 'password' => 'eko123'],
            ['name' => 'Zainul Arifin, S.Pd', 'username' => 'zainularifinspd', 'nip' => '19871116 202521 1 085', 'password' => 'zainul123'],
            ['name' => 'Endang Safitri, S.Pd', 'username' => 'endangsafitrispd', 'nip' => null, 'password' => 'endang123'],
            ['name' => 'Mufatiroh, S.Ag', 'username' => 'mufatirohssag', 'nip' => null, 'password' => 'mufatiroh123'],
            ['name' => 'Abdul Rohman, S.Pd', 'username' => 'abdulrohmanspd', 'nip' => null, 'password' => 'abdul123'],
            ['name' => 'Rizki Putri Wulandari, S.Pd', 'username' => 'rizkiputriwulandarispd', 'nip' => null, 'password' => 'rizki123'],
            ['name' => 'Pdt. Juklianus Steven Immanuel Bahihi, S.Pdk., M.Pd', 'username' => 'pdtjuklianusstevenimmanuelbahihispdkmpd', 'nip' => null, 'password' => 'juklianus123'],
            ['name' => 'Sukamto, S.Ag', 'username' => 'sukamtosag', 'nip' => null, 'password' => 'sukamto123'],
        ];

        $normal = static function (string $value): string {
            return preg_replace('/[^a-z0-9]+/i', '', strtolower(trim($value)));
        };

        foreach ($daftarGuru as $guru) {
            $nama = $guru['name'];
            $nip = $guru['nip'];
            $username = $guru['username'];
            $password = $guru['password'];

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

            if (! $user) {
                $user = User::where('username', $username)->first();
            }

            if ($user) {
                $user->update([
                    'name' => $nama,
                    'username' => $username,
                    'nip' => $nip,
                    'email' => null,
                    'password' => Hash::make($password),
                    'role' => 'guru',
                ]);
            } else {
                User::create([
                    'name' => $nama,
                    'username' => $username,
                    'nip' => $nip,
                    'email' => null,
                    'password' => Hash::make($password),
                    'role' => 'guru',
                ]);
            }
        }
    }
}
