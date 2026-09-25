<?php

namespace App\Models;

class Pengaturan
{
    protected static function getFilePath(): string
    {
        return storage_path('app/pengaturan.json');
    }

    public static function defaults(): array
    {
        return [
            'shift_senin_minutes' => 40,
            'shift_jumat_minutes' => 30,
            'senin_is_maju' => 0,
            'senin_shifted_minutes' => 0,
            'jumat_is_maju' => 0,
            'jumat_shifted_minutes' => 0,
            'tenggat_opsi' => 'terbatas_jam', // 'terbatas_jam' | 'hari_ini' | 'los'
        ];
    }

    public static function all(): array
    {
        $path = static::getFilePath();
        $defaults = static::defaults();
        if (! file_exists($path)) {
            return $defaults;
        }

        $content = file_get_contents($path);
        $data = json_decode($content, true);

        return is_array($data) ? array_merge($defaults, $data) : $defaults;
    }

    /**
     * Ambil nilai pengaturan berdasarkan key, atau nilai default jika belum ada.
     */
    public static function getValue(string $key, mixed $default = null): mixed
    {
        $data = static::all();

        return array_key_exists($key, $data) ? $data[$key] : $default;
    }

    /**
     * Simpan atau update nilai pengaturan ke storage/app/pengaturan.json.
     */
    public static function setValue(string $key, mixed $value): void
    {
        $data = static::all();
        $data[$key] = is_numeric($value) ? (int) $value : $value;

        $dir = dirname(static::getFilePath());
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents(static::getFilePath(), json_encode($data, JSON_PRETTY_PRINT));
    }
}
