<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembelian extends Model
{
    protected $table = 'pembelian';
    protected $primaryKey = 'id_pembelian';
    protected $fillable = ['no_faktur_pembelian', 'tgl_pembelian', 'id_pemasok', 'status_pembelian'];
    protected $casts = ['tgl_pembelian' => 'date'];

    public function pemasok()
    {
        return $this->belongsTo(Pemasok::class, 'id_pemasok')->withTrashed();
    }

    public function details()
    {
        return $this->hasMany(DetailPembelian::class, 'id_pembelian', 'id_pembelian');
    }

    public function getTotalAttribute(): float
    {
        $d = $this->relationLoaded('details') ? $this->details : $this->details()->get();

        return (float) $d->sum(fn ($x) => $x->qty_detail_pembelian * $x->harga_beli_detail_pembelian);
    }
}
