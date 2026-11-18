<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatStok extends Model
{
    public const UPDATED_AT = null;

    protected $table = 'riwayat_stok';
    protected $primaryKey = 'id_riwayat_stok';
    protected $fillable = [
        'id_barang', 'tipe', 'qty_perubahan', 'stok_sebelum', 'stok_sesudah',
        'referensi', 'catatan', 'id_pengurus',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang')->withTrashed();
    }

    public function pengurus()
    {
        return $this->belongsTo(Pengurus::class, 'id_pengurus');
    }
}
