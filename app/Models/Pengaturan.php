<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Pengaturan extends Model
{
    protected $table = 'pengaturan';
    protected $primaryKey = 'kunci';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = ['kunci', 'nilai'];

    public static function ambil(string $kunci, $default = null)
    {
        return Cache::rememberForever("pengaturan.$kunci", fn () => static::find($kunci)?->nilai) ?? $default;
    }

    public static function simpan(string $kunci, ?string $nilai): void
    {
        static::updateOrCreate(['kunci' => $kunci], ['nilai' => $nilai]);
        Cache::forget("pengaturan.$kunci");
    }

    public static function noAdmin(): string
    {
        return static::ambil('no_admin', '081234567890');
    }

    public static function noAdminWaLink(?string $pesan = null): string
    {
        $no = static::noAdmin();
        $clean = preg_replace('/[^0-9]/', '', $no);
        if (str_starts_with($clean, '0')) {
            $clean = '62' . substr($clean, 1);
        } elseif (! str_starts_with($clean, '62') && ! empty($clean)) {
            $clean = '62' . $clean;
        }
        if (empty($clean)) {
            $clean = '6281234567890';
        }

        $url = "https://wa.me/{$clean}";
        if ($pesan) {
            $url .= '?text=' . urlencode($pesan);
        }

        return $url;
    }
}
