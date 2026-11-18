<?php

use App\Http\Controllers\Admin\AnggotaController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PengurusController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\KategoriController;
use App\Http\Controllers\Admin\PembelianController;
use App\Http\Controllers\Admin\PemasokController;
use App\Http\Controllers\Admin\ProdukController;
use App\Http\Controllers\Admin\PengaturanController;
use App\Http\Controllers\Admin\StokController;
use App\Http\Controllers\Admin\PembayaranController as AdminPembayaranController;
use App\Http\Controllers\Admin\TransaksiController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Kasir\PembayaranController as KasirPembayaranController;
use App\Http\Controllers\Kasir\PosController;
use App\Http\Controllers\NotaController;
use App\Http\Controllers\Toko\BelanjaController;
use App\Http\Controllers\Toko\BerandaController;
use App\Http\Controllers\Toko\CheckoutController;
use App\Http\Controllers\Toko\KeranjangController;
use App\Http\Controllers\Toko\PesananController;
use App\Http\Controllers\Toko\ProfilController;
use Illuminate\Support\Facades\Route;

// Placeholder sementara untuk halaman admin yang belum dibuat.
$halaman = fn (string $judul) => fn () => view('admin.placeholder', ['judul' => $judul]);

// ---------- Autentikasi ----------
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:20,1');
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->middleware('throttle:6,1');
});
Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');

// ---------- Storefront (publik) ----------
Route::get('/', [BerandaController::class, 'index'])->name('toko.beranda');
Route::get('/belanja', [BelanjaController::class, 'index'])->name('toko.belanja');
Route::get('/produk/{barang}', [BelanjaController::class, 'show'])->name('toko.produk')->whereNumber('barang');
Route::get('/tentang', [BerandaController::class, 'tentang'])->name('toko.tentang');

// ---------- Storefront (anggota) ----------
Route::middleware(['auth', 'peran:anggota'])->group(function () {
    Route::get('/keranjang', [KeranjangController::class, 'index'])->name('toko.keranjang');
    Route::post('/keranjang', [KeranjangController::class, 'tambah'])->name('toko.keranjang.tambah');
    Route::patch('/keranjang/{idBarang}', [KeranjangController::class, 'ubah'])->name('toko.keranjang.ubah')->whereNumber('idBarang');
    Route::delete('/keranjang/{idBarang}', [KeranjangController::class, 'hapus'])->name('toko.keranjang.hapus')->whereNumber('idBarang');

    Route::get('/checkout', [CheckoutController::class, 'create'])->name('toko.checkout');
    Route::post('/checkout', [CheckoutController::class, 'store'])->middleware('throttle:10,1')->name('toko.checkout.store');

    Route::get('/pesanan', [PesananController::class, 'index'])->name('toko.pesanan');
    Route::get('/pesanan/{penjualan}', [PesananController::class, 'show'])->name('toko.pesanan.show');
    Route::get('/pesanan/{penjualan}/bayar', [PesananController::class, 'bayar'])->name('toko.pesanan.bayar');
    Route::get('/pesanan/{penjualan}/status', [PesananController::class, 'status'])->name('toko.pesanan.status');
    Route::post('/pesanan/{penjualan}/bukti', [PesananController::class, 'bukti'])->middleware('throttle:10,1')->name('toko.pesanan.bukti');
    Route::post('/pesanan/{penjualan}/batal', [PesananController::class, 'batal'])->name('toko.pesanan.batal');
    Route::get('/pesanan/{penjualan}/nota', [PesananController::class, 'nota'])->name('toko.pesanan.nota');
    Route::get('/pesanan/{penjualan}/nota/pdf', [PesananController::class, 'notaPdf'])->name('toko.pesanan.nota.pdf');

    Route::get('/profil', [ProfilController::class, 'show'])->name('toko.profil');
    Route::put('/profil', [ProfilController::class, 'update'])->name('toko.profil.update');
});

// ---------- Kasir (POS), nota, transaksi, pembayaran: admin + kasir ----------
Route::middleware(['auth', 'peran:admin,kasir'])->group(function () {
    Route::get('/kasir', [PosController::class, 'index'])->name('kasir.pos');
    Route::get('/kasir/cari-barang', [PosController::class, 'cariBarang'])->name('kasir.barang');
    Route::get('/kasir/cari-anggota', [PosController::class, 'cariAnggota'])->name('kasir.anggota');
    Route::post('/kasir/checkout', [PosController::class, 'checkout'])->name('kasir.checkout');

    Route::get('/kasir/bayar/{penjualan}', [KasirPembayaranController::class, 'show'])->name('kasir.bayar');
    Route::get('/kasir/bayar/{penjualan}/status', [KasirPembayaranController::class, 'status'])->name('kasir.bayar.status');
    Route::post('/kasir/bayar/{penjualan}/lunas', [KasirPembayaranController::class, 'lunas'])->name('kasir.bayar.lunas');
    Route::post('/kasir/bayar/{penjualan}/batal', [KasirPembayaranController::class, 'batal'])->name('kasir.bayar.batal');

    Route::get('/nota/{penjualan}', [NotaController::class, 'show'])->name('nota.show');
    Route::get('/nota/{penjualan}/pdf', [NotaController::class, 'pdf'])->name('nota.pdf');
});

// ---------- Dashboard & manajemen ----------
Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () use ($halaman) {
    Route::middleware('peran:admin')->group(function () use ($halaman) {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('/no-admin', [DashboardController::class, 'updateNoAdmin'])->name('no_admin.update');
        Route::get('/api/grafik', [DashboardController::class, 'grafik'])->name('grafik');
        Route::get('/anggota/laporan', [AnggotaController::class, 'laporan'])->name('anggota.laporan');
        Route::resource('anggota', AnggotaController::class)->except(['show', 'create'])->parameters(['anggota' => 'anggota'])->names('anggota');
        Route::get('/pengurus/laporan', [PengurusController::class, 'laporan'])->name('pengurus.laporan');
        Route::resource('pengurus', PengurusController::class)->except(['show', 'create'])->parameters(['pengurus' => 'pengurus'])->names('pengurus');
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/excel', [LaporanController::class, 'excel'])->name('laporan.excel');
        Route::get('/laporan/pdf', [LaporanController::class, 'pdf'])->name('laporan.pdf');
        Route::get('/pengaturan', [PengaturanController::class, 'index'])->name('pengaturan.index');
        Route::put('/pengaturan', [PengaturanController::class, 'update'])->name('pengaturan.update');
    });

    Route::middleware('peran:admin,gudang')->group(function () use ($halaman) {
        Route::resource('produk', ProdukController::class)->except('show')->parameters(['produk' => 'barang'])->names('produk');
        Route::resource('kategori', KategoriController::class)->except('show')->names('kategori');

        Route::get('/stok', [StokController::class, 'index'])->name('stok.index');
        Route::get('/stok/riwayat', [StokController::class, 'riwayat'])->name('stok.riwayat');
        Route::get('/stok/{barang}/edit', [StokController::class, 'edit'])->name('stok.edit');
        Route::put('/stok/{barang}', [StokController::class, 'update'])->name('stok.update');

        // Pemasok berada di bawah /pembelian agar menu "Pembelian / Pemasok" tetap aktif; didaftarkan sebelum {pembelian}.
        Route::resource('pembelian/pemasok', PemasokController::class)->except('show')->names('pembelian.pemasok');
        Route::resource('pembelian', PembelianController::class)->except(['edit', 'update'])->names('pembelian');
        Route::post('/pembelian/{pembelian}/terima', [PembelianController::class, 'terima'])->name('pembelian.terima');
    });

    Route::middleware('peran:admin,kasir')->group(function () {
        Route::get('/transaksi', [TransaksiController::class, 'index'])->name('transaksi.index');
        Route::get('/transaksi/{penjualan}', [TransaksiController::class, 'show'])->name('transaksi.show');
        Route::post('/transaksi/{penjualan}/batal', [TransaksiController::class, 'batal'])->name('transaksi.batal');
        Route::post('/transaksi/{penjualan}/status', [TransaksiController::class, 'status'])->name('transaksi.status');

        Route::get('/pembayaran', [AdminPembayaranController::class, 'index'])->name('pembayaran.index');
        Route::post('/pembayaran/{pembayaran}/lunas', [AdminPembayaranController::class, 'lunas'])->name('pembayaran.lunas');
        Route::post('/pembayaran/{pembayaran}/cash', [AdminPembayaranController::class, 'cash'])->name('pembayaran.cash');
    });
});
