<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Anggota extends Model
{
    use SoftDeletes;

    protected $table = 'anggota';
    protected $primaryKey = 'id_anggota';
    protected $fillable = [
        'no_anggota', 'nis_anggota', 'nama_anggota', 'kelas_anggota', 'alamat_anggota', 'kota_anggota',
        'no_hp_anggota', 'status_anggota', 'tgl_daftar_anggota',
    ];
    protected $casts = ['tgl_daftar_anggota' => 'date'];

    public function scopeAktif($q)
    {
        return $q->where('status_anggota', 'aktif');
    }

    public function penjualan()
    {
        return $this->hasMany(Penjualan::class, 'id_anggota');
    }

    public function keranjang()
    {
        return $this->hasOne(Keranjang::class, 'id_anggota');
    }
}
