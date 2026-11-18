<?php

namespace App\Services\Qris;

use InvalidArgumentException;

/** Mengubah string QRIS statis menjadi dinamis: tag 01 = 12, sisip tag 54 (nominal), hitung ulang CRC (tag 63). */
class QrisDinamis
{
    public static function dari(string $statis, float|int|string $nominal): string
    {
        $angka = self::formatNominal($nominal);
        $hasil = [];
        $sisip = false;

        foreach (self::urai(trim($statis)) as [$tag, $nilai]) {
            if ($tag === '63' || $tag === '54') {
                continue; // CRC lama dan nominal lama dibuang
            }
            if ($tag === '01') {
                $nilai = '12'; // 11 = statis, 12 = dinamis
            }
            if (! $sisip && (int) $tag > 54) {
                $hasil[] = ['54', $angka];
                $sisip = true;
            }
            $hasil[] = [$tag, $nilai];
        }
        if (! $sisip) {
            $hasil[] = ['54', $angka];
        }

        $isi = implode('', array_map(fn ($t) => $t[0] . str_pad((string) strlen($t[1]), 2, '0', STR_PAD_LEFT) . $t[1], $hasil)) . '6304';

        return $isi . QrisCrc16::hitung($isi);
    }

    /** @return array<int, array{0:string,1:string}> */
    public static function urai(string $qris): array
    {
        $hasil = [];
        $pos = 0;
        $panjang = strlen($qris);

        while ($pos < $panjang) {
            if ($pos + 4 > $panjang || ! ctype_digit(substr($qris, $pos, 4))) {
                throw new InvalidArgumentException('String QRIS tidak valid.');
            }
            $tag = substr($qris, $pos, 2);
            $len = (int) substr($qris, $pos + 2, 2);
            if ($pos + 4 + $len > $panjang) {
                throw new InvalidArgumentException('String QRIS terpotong.');
            }
            $hasil[] = [$tag, substr($qris, $pos + 4, $len)];
            $pos += 4 + $len;
        }

        $crc = end($hasil);
        if ($crc === false || $crc[0] !== '63' || strlen($crc[1]) !== 4 || ! ctype_xdigit($crc[1])
            || ! hash_equals(strtoupper($crc[1]), QrisCrc16::hitung(substr($qris, 0, -4)))) {
            throw new InvalidArgumentException('Checksum QRIS tidak valid.');
        }

        return $hasil;
    }

    public static function tag(string $qris, string $tag): ?string
    {
        foreach (self::urai($qris) as [$t, $v]) {
            if ($t === $tag) {
                return $v;
            }
        }

        return null;
    }

    private static function formatNominal(float|int|string $nominal): string
    {
        $n = (float) $nominal;

        return floor($n) == $n ? (string) (int) $n : number_format($n, 2, '.', '');
    }
}
