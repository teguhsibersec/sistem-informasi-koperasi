<?php

namespace App\Services\Qris;

class QrisCrc16
{
    /** CRC16/CCITT-FALSE (poly 0x1021, init 0xFFFF) sesuai spesifikasi EMVCo/QRIS. */
    public static function hitung(string $data): string
    {
        $crc = 0xFFFF;
        foreach (unpack('C*', $data) as $byte) {
            $crc ^= $byte << 8;
            for ($i = 0; $i < 8; $i++) {
                $crc = ($crc & 0x8000) ? (($crc << 1) ^ 0x1021) : ($crc << 1);
                $crc &= 0xFFFF;
            }
        }

        return strtoupper(str_pad(dechex($crc), 4, '0', STR_PAD_LEFT));
    }
}
