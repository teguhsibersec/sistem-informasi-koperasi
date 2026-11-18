<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPenjualan extends Model
{
    protected $table = 'detail_penjualan';
    protected $primaryKey = null;      // PK komposit (id_penjualan, id_barang)
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = ['id_penjualan', 'id_barang', 'qty_detail_penjualan', 'harga_satuan_detail_penjualan'];

    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class, 'id_penjualan');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang')->withTrashed();
    }

    public function getSubtotalAttribute(): float
    {
        return (float) $this->qty_detail_penjualan * (float) $this->harga_satuan_detail_penjualan;
    }
}
