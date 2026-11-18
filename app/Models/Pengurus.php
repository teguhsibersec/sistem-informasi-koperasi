<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pengurus extends Model
{
    use SoftDeletes;

    protected $table = 'pengurus';
    protected $primaryKey = 'id_pengurus';
    protected $fillable = ['kode_pengurus', 'nama_pengurus', 'jabatan_pengurus', 'alamat_pengurus', 'kota_pengurus', 'telepon_pengurus'];

    public const JABATAN = ['ketua' => 'Ketua', 'sekretaris' => 'Sekretaris', 'bendahara' => 'Bendahara'];

    /** Jabatan -> hak akses. Ketua = semua; bendahara = kasir/transaksi; sekretaris = barang/stok. */
    public const AKSES = ['bendahara' => 'kasir', 'sekretaris' => 'gudang'];

    public function users()
    {
        return $this->hasMany(User::class, 'id_pengurus');
    }

    public function penjualan()
    {
        return $this->hasMany(Penjualan::class, 'id_pengurus');
    }
}
