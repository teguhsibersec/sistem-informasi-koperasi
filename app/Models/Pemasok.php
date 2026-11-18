<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pemasok extends Model
{
    use SoftDeletes;

    protected $table = 'pemasok';
    protected $primaryKey = 'id_pemasok';
    protected $fillable = ['nama_pemasok', 'telepon_pemasok', 'alamat_pemasok'];

    public function pembelian()
    {
        return $this->hasMany(Pembelian::class, 'id_pemasok');
    }
}
