<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    protected $table = 'penjualan';
    protected $primaryKey = 'id_penjualan';
    protected $fillable = [
        'no_nota_penjualan', 'tgl_penjualan', 'id_pengurus', 'id_anggota', 'bayar_penjualan',
        'sumber_penjualan', 'status_penjualan', 'catatan_penjualan',
    ];
    protected $casts = ['tgl_penjualan' => 'datetime', 'bayar_penjualan' => 'decimal:2'];

    public function pengurus()
    {
        return $this->belongsTo(Pengurus::class, 'id_pengurus');
    }

    public function anggota()
    {
        return $this->belongsTo(Anggota::class, 'id_anggota');
    }

    public function details()
    {
        return $this->hasMany(DetailPenjualan::class, 'id_penjualan', 'id_penjualan');
    }

    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class, 'id_penjualan');
    }

    public function pembayaranTerakhir()
    {
        return $this->hasOne(Pembayaran::class, 'id_penjualan')->latestOfMany('id_pembayaran');
    }

    /** Nilai turunan: tidak disimpan di tabel. */
    public function getTotalAttribute(): float
    {
        $d = $this->relationLoaded('details') ? $this->details : $this->details()->get();

        return (float) $d->sum(fn ($x) => $x->qty_detail_penjualan * $x->harga_satuan_detail_penjualan);
    }

    public function getKembalianAttribute(): float
    {
        return max(0, (float) $this->bayar_penjualan - $this->total);
    }
}
