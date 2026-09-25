<?php

namespace Database\Seeders;

use App\Models\JadwalPiket;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class SeptemberPiketSeeder extends Seeder
{
    /**
     * Jadwal Piket KBM Semester Ganjil SMKN 1 Boyolangu, September 2026.
     *
     * Usernames below were matched against the existing user master after
     * transcribing the supplied PDF. They are data mappings, never login rules.
     *
     * @var array<string, array<string, array<int, string>>>
     */
    private array $templates = [
        'a' => [
            'pagi' => ['sulistyowatiss', 'wiwikyuniarsih', 'srikusumastutispd'],
            'koordinator_pagi' => ['liliksuratmispd'],
            'siang' => ['kasmispdmpd', 'sitimunawarohskommpd', 'nikendewihastikaspd'],
            'koordinator_siang' => ['widodospd'],
            'waka' => ['nikenharipratiwispsimpd'],
        ],
        'b' => [
            'pagi' => ['tututsriatinspd', 'rikaoktamaulidasds', 'mufatirohsag'],
            'koordinator_pagi' => ['elyanafrisca'],
            'siang' => ['siswantipurwaningsihstmpd', 'shintaindyarshantysusantoskom', 'dhuanaputripuspitasaryspd'],
            'koordinator_siang' => ['erwanseptiyonospd'],
            'waka' => ['hardiniindahingbudisempd'],
        ],
        'c' => [
            'pagi' => ['yunijiastutispd', 'yuliratnasarispd', 'aguspramonossn'],
            'koordinator_pagi' => ['dananganjarhymawantospd'],
            'siang' => ['risqinurimamastrpar', 'lulukmunfaridaspd', 'tuhuerieskudorissn'],
            'koordinator_siang' => ['istianasuhartatist'],
            'waka' => ['hendrosuwignyost'],
        ],
        'd' => [
            'pagi' => ['arifsetyobudispd', 'sunartispd', 'istimufadahspd'],
            'koordinator_pagi' => ['jokopriyantoskom'],
            'siang' => ['hanikpangestuti', 'andrikrisdiantosempd', 'fitriarenytasarispd'],
            'koordinator_siang' => ['agungyuliantospd'],
            'waka' => ['fajarluthfiantospd'],
        ],
        'e' => [
            'pagi' => ['septianispdmpd', 'martiinspd', 'winarsihspdmpd'],
            'koordinator_pagi' => ['titinsukmasarispdmpd'],
            'siang' => ['nurulazizahspd', 'rifkotinnaimahspd', 'drasusaktiyuharini'],
            'koordinator_siang' => ['lutfiamarsalina'],
            'waka' => ['setiyowinarkospd'],
        ],
        'f' => [
            'pagi' => ['rindangrejekispd', 'dianahartantistmpd', 'veronicadamayrulitasarispd'],
            'koordinator_pagi' => ['ayupuspitorinist'],
            'siang' => ['umikulsumspd', 'rulydwisetyaningrum', 'srirahayuspd'],
            'koordinator_siang' => ['agustinamardikarinispdmpd'],
            'waka' => ['nikenharipratiwispsimpd'],
        ],
        'g' => [
            'pagi' => ['draanikindriani', 'retnowidyastutispdmpd', 'nurekowahyuningsihspd'],
            'koordinator_pagi' => ['saadwazishiedayatspd'],
            'siang' => ['purwatispd', 'ratihdianirawatise', 'sitimaisarohspd'],
            'koordinator_siang' => ['dyahestirahayuspd'],
            'waka' => ['hardiniindahingbudisempd'],
        ],
        'h' => [
            'pagi' => ['sitiumiharsihspd', 'ernarinawatispd', 'astrabellaflamboyanspsi'],
            'koordinator_pagi' => ['endangaryhandayanistmpd'],
            'siang' => ['niniksriwidayatispdmpd', 'endikkuswantoroskommt', 'komariyahspd'],
            'koordinator_siang' => ['dianmawartispd'],
            'waka' => ['hendrosuwignyost'],
        ],
        'i' => [
            'pagi' => ['yanispd', 'titiksamsistinispd', 'pipitambarwatispd'],
            'koordinator_pagi' => ['kurnilaputri'],
            'siang' => ['basukisarjonospd', 'atihwilupisempd', 'nishfulailispd'],
            'koordinator_siang' => ['nurnastutisarisstpar'],
            'waka' => ['fajarluthfiantospd'],
        ],
        'j' => [
            'pagi' => ['sitikhoiriyahspd', 'peniwulandarispd', 'badrussulaiman'],
            'koordinator_pagi' => ['dwirinimanfaatispd'],
            'siang' => ['elysayulinurainissi', 'dwinovasetyandarispd', 'masanwidodospdmt'],
            'koordinator_siang' => ['dwikuswantospd'],
            'waka' => ['setiyowinarkospd'],
        ],
    ];

    /** @var array<string, string> */
    private array $dates = [
        '2026-09-01' => 'a', '2026-09-02' => 'b', '2026-09-03' => 'c', '2026-09-04' => 'd',
        '2026-09-07' => 'e', '2026-09-08' => 'f', '2026-09-09' => 'g', '2026-09-10' => 'h', '2026-09-11' => 'i',
        '2026-09-14' => 'j', '2026-09-15' => 'a', '2026-09-16' => 'b', '2026-09-17' => 'c', '2026-09-18' => 'd',
        '2026-09-21' => 'e', '2026-09-22' => 'f', '2026-09-23' => 'g', '2026-09-24' => 'h', '2026-09-25' => 'i',
        '2026-09-28' => 'j', '2026-09-29' => 'a', '2026-09-30' => 'b',
    ];

    public function run(): void
    {
        $this->ensureAccountsFromSourceDocument();
        $users = User::query()->get()->keyBy('username');
        $wakaUsernames = collect($this->templates)->pluck('waka')->flatten()->unique()->values();

        foreach ($wakaUsernames as $username) {
            $this->userFor($users, $username);
        }

        User::query()->whereIn('role', ['piket', 'waka'])->update(['role' => 'guru']);
        User::query()->where('is_waka', true)->update(['is_waka' => false]);
        User::query()->whereIn('username', $wakaUsernames)->update(['is_waka' => true]);

        JadwalPiket::query()
            ->where(function ($query): void {
                $query->whereYear('tanggal', 2026)->whereMonth('tanggal', 9)->orWhereNull('tanggal');
            })
            ->delete();

        foreach ($this->dates as $date => $template) {
            $this->storeTemplate($users, $date, $this->templates[$template]);
        }
    }

    private function ensureAccountsFromSourceDocument(): void
    {
        foreach ([
            'hardiniindahingbudisempd' => 'Hardini Indahing Budi, S.E., M.Pd.',
            'drasusaktiyuharini' => 'Dra. Susakti Yuharini',
        ] as $username => $name) {
            User::query()->firstOrCreate(
                ['username' => $username],
                [
                    'name' => $name,
                    'password' => Hash::make('guru123'),
                    'role' => 'guru',
                    'is_waka' => false,
                ],
            );
        }
    }

    /**
     * @param  Collection<string, User>  $users
     * @param  array<string, array<int, string>>  $template
     */
    private function storeTemplate($users, string $date, array $template): void
    {
        $dateValue = Carbon::parse($date, 'Asia/Jakarta');
        $assignments = [
            ['users' => $template['pagi'], 'tipe' => 'guru', 'shift' => 1, 'start' => '07:00:00', 'end' => '11:00:00'],
            ['users' => $template['koordinator_pagi'], 'tipe' => 'koordinator', 'shift' => 1, 'start' => '07:00:00', 'end' => '11:00:00'],
            ['users' => $template['siang'], 'tipe' => 'guru', 'shift' => 2, 'start' => '11:00:00', 'end' => '15:00:00'],
            ['users' => $template['koordinator_siang'], 'tipe' => 'koordinator', 'shift' => 2, 'start' => '11:00:00', 'end' => '15:00:00'],
            ['users' => $template['waka'], 'tipe' => 'waka', 'shift' => 1, 'start' => '07:00:00', 'end' => '15:00:00'],
        ];

        foreach ($assignments as $assignment) {
            foreach ($assignment['users'] as $username) {
                $user = $this->userFor($users, $username);

                JadwalPiket::create([
                    'user_id' => $user->id,
                    'hari' => $dateValue->translatedFormat('l'),
                    'tanggal' => $date,
                    'tipe' => $assignment['tipe'],
                    'bulan' => 9,
                    'tahun' => 2026,
                    'shift' => $assignment['shift'],
                    'jam_mulai' => $assignment['start'],
                    'jam_selesai' => $assignment['end'],
                    'keterangan' => 'Jadwal Piket KBM September 2026',
                ]);
            }
        }
    }

    /** @param Collection<string, User> $users */
    private function userFor($users, string $username): User
    {
        $user = $users->get($username);

        if (! $user) {
            $normal = static fn (string $v): string => preg_replace('/[^a-z0-9]+/i', '', strtolower(trim($v)));
            $target = $normal($username);

            $user = User::all()->first(function (User $u) use ($target, $normal) {
                return $normal($u->username) === $target
                    || $normal($u->name) === $target
                    || str_contains($normal($u->name), $target)
                    || str_contains($target, $normal($u->name));
            });
        }

        if (! $user) {
            throw new RuntimeException("Akun guru untuk jadwal piket September tidak ditemukan: {$username}");
        }

        return $user;
    }
}
