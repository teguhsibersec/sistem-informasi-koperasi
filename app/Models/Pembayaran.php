<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    protected $table = 'pembayaran';
    protected $primaryKey = 'id_pembayaran';
    protected $fillable = [
        'id_penjualan', 'metode_pembayaran', 'jumlah_tagihan', 'uang_diterima', 'kembalian',
        'status_pembayaran', 'kode_referensi', 'qris_payload', 'bukti_bayar',
        'dibayar_pada', 'kedaluwarsa_pada', 'dikonfirmasi_oleh',
    ];
    protected $casts = [
        'jumlah_tagihan' => 'decimal:2', 'uang_diterima' => 'decimal:2', 'kembalian' => 'decimal:2',
        'dibayar_pada' => 'datetime', 'kedaluwarsa_pada' => 'datetime',
    ];

    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class, 'id_penjualan');
    }

    public function pengonfirmasi()
    {
        return $this->belongsTo(Pengurus::class, 'dikonfirmasi_oleh');
    }
}
