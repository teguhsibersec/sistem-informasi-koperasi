<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Barang extends Model
{
    use SoftDeletes;

    protected $table = 'barang';
    protected $primaryKey = 'id_barang';
    protected $fillable = [
        'kode_barang', 'nama_barang', 'satuan_barang', 'id_kategori', 'harga_jual_barang',
        'stok_barang', 'stok_min_barang', 'gambar_barang', 'deskripsi_barang', 'status_barang',
    ];
    protected $casts = ['harga_jual_barang' => 'decimal:2'];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'id_kategori');
    }

    public function scopeAktif($q)
    {
        return $q->where('status_barang', 'aktif');
    }

    /** stok_barang <= stok_min_barang */
    public function scopeStokMenipis($q)
    {
        return $q->whereColumn('stok_barang', '<=', 'stok_min_barang');
    }

    public function getGambarUrlAttribute(): string
    {
        // Foto unggahan admin selalu menjadi prioritas.
        if ($this->gambar_barang) {
            return asset('storage/' . $this->gambar_barang);
        }

        // Foto produk asli dari katalog penjual/brand untuk demo katalog.
        // Untuk barang yang tidak memiliki foto merek yang cocok, gunakan foto asli
        // dari barang sejenis, bukan ilustrasi buatan.
        $nama = mb_strtolower($this->nama_barang ?? '');
        $foto = match (true) {
            str_contains($nama, 'air mineral') => 'https://www.static-src.com/wcsstore/Indraprastha/images/catalog/full/90/MTA-4492431/aqua_aqua_air_pet_600ml_full01_dm7pfaa5.jpg',
            str_contains($nama, 'pulpen') => 'https://i5.walmartimages.com/asr/069bf031-7bec-44af-b8e6-a2429d25394f.df62f408da6c0c18147c873b385c5981.jpeg',
            str_contains($nama, 'penghapus') => 'https://www.joyko.co.id/image/cache/data/additional/526-B20-BEAUTY-1-01-900x900.jpg',
            str_contains($nama, 'pensil') => 'https://prod.isg.bruneau.media/asset/aHR0cHM6Ly9icnVuZWF1LnNpbXBsZXdvcmtzcGFjZS5uZXQvZmlsZS9wdWJBc3NldEJhc2UvS2doNV9jaFg4WmdFX2M4ZmFQS1laUExMSFFUM3pnQlFwdkRQVHV4akNZUXgxRWoxbVVNLzE2NjI0NjgzMzAwMDAwL3MvMDc5NzcuanBn/?format=jpeg&height=477&quality=85&width=477',
            str_contains($nama, 'stabilo') || str_contains($nama, 'highlighter') => 'https://cdn-live.be-stabilo.com/media/2b/a0/60/1776390415/St_195825_70_24_Pen_diagonal_cs.jpg?quality=80&ts=1781831235&width=1280',
            str_contains($nama, 'tipe-x') || str_contains($nama, 'tipex') => 'https://down-id.img.susercontent.com/file/id-11134207-8224x-mj529kcnmyo00e',
            str_contains($nama, 'penggaris') => 'https://cdn.salla.sa/xAdGWK/U5gWuVdZ5dyn05LZdzHGFdwBGRROjYkhnlaI0zUO.jpg',
            str_contains($nama, 'mie instan') => 'https://img.id.my-best.com/product_images/52410225e490f0345207dfcd47d483a5.png?fit=clip&h=800&ixlib=rails-4.3.1&lossless=0&q=70&s=c7cf9cbd00c45ad101fbe82e189820d3&w=800',
            str_contains($nama, 'notebook') || str_contains($nama, 'buku tulis') => 'https://cf.shopee.co.id/file/2c0cb55adc79dbaaf6f4a5fd62666167',
            str_contains($nama, 'tas kain') => 'https://id-live-01.slatic.net/p/1acc6b068ae41cf104001d84c5165dee.png',
            str_contains($nama, 'teh botol') => 'https://rajasusu.com/wp-content/uploads/2023/12/SOSRO-TEH-BOTOL-PET-450-ML.jpg',
            str_contains($nama, 'kopi sachet') => 'https://www.static-src.com/wcsstore/Indraprastha/images/catalog/full/catalog-image/MTA-99549780/nescafe_kopi_nescafe_classic_100-_kopi_murni_sachet_-_2_gr_-kemasan_satuan-_full05_vn6tta16.jpg',
            str_contains($nama, 'roti cokelat') => 'https://down-id.img.susercontent.com/file/id-11134201-23030-ap140jzkpfovba',
            str_contains($nama, 'wafer') => 'https://down-id.img.susercontent.com/file/id-11134207-7r98u-lqe67dwzkyyua4',
            str_contains($nama, 'susu kotak') => 'https://www.static-src.com/wcsstore/Indraprastha/images/catalog/full/92/MTA-2556641/ultra_ultra-milk-full-cream-susu-uht--1000-ml-_full03.jpg',
            str_contains($nama, 'snack') || str_contains($nama, 'cokelat') => 'https://down-id.img.susercontent.com/file/id-11134207-7r98u-lqe67dwzkyyua4',
            str_contains($nama, 'map plastik') || str_contains($nama, 'sampul buku') || str_contains($nama, 'kertas hvs') || str_contains($nama, 'buku tulis') || str_contains($nama, 'notebook') => 'https://cf.shopee.co.id/file/2c0cb55adc79dbaaf6f4a5fd62666167',
            str_contains($nama, 'flashdisk') => 'https://www.static-src.com/wcsstore/Indraprastha/images/catalog/full//100/MTA-30031401/sandisk_sandisk_cruzer_blade_32gb_usb_flash_drive_full01.jpg',
            str_contains($nama, 'kalkulator') => 'https://m.media-amazon.com/images/I/61b3xL0N3oL._AC_SL1500_.jpg',
            str_contains($nama, 'kaos') => 'https://id-live-01.slatic.net/p/1acc6b068ae41cf104001d84c5165dee.png',
            str_contains($nama, 'tas') || str_contains($nama, 'payung') || str_contains($nama, 'tumbler') || str_contains($nama, 'gantungan kunci') => 'https://id-live-01.slatic.net/p/1acc6b068ae41cf104001d84c5165dee.png',
            default => asset('images/placeholder.png'),
        };

        return $foto;
    }

    public function riwayatStok()
    {
        return $this->hasMany(RiwayatStok::class, 'id_barang');
    }
}
