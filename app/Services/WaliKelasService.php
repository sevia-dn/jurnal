<?php

namespace App\Services;

use App\Models\Kelas;
use App\Models\User;
use Illuminate\Support\Collection;

class WaliKelasService
{
    /**
     * Get every class assigned to the teacher as homeroom teacher.
     *
     * The assignment is stored as a name in the kelas table, so names are
     * normalized to keep punctuation and academic titles from blocking access.
     *
     * @return Collection<int, Kelas>
     */
    public function classesFor(User $user): Collection
    {
        $normalizedName = $this->normalizeName($user->name);

        if ($normalizedName === '') {
            return collect();
        }

        return Kelas::query()
            ->whereNotNull('wali_kelas')
            ->orderBy('nama_kelas')
            ->get()
            ->filter(fn (Kelas $kelas): bool => $this->normalizeName($kelas->wali_kelas) === $normalizedName)
            ->values();
    }

    private function normalizeName(?string $name): string
    {
        return preg_replace('/[^a-z0-9]/', '', mb_strtolower((string) $name)) ?? '';
    }
}
