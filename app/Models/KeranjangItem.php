<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KeranjangItem extends Model
{
    protected $table = 'keranjang_item';
    protected $primaryKey = 'id_keranjang_item';
    protected $fillable = ['id_keranjang', 'id_barang', 'qty'];

    public function keranjang()
    {
        return $this->belongsTo(Keranjang::class, 'id_keranjang');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang');
    }

    public function getSubtotalAttribute(): int
    {
        return (int) ($this->qty * ($this->barang?->harga_jual_barang ?? 0));
    }
}
