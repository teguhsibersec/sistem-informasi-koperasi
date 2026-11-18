<?php $__env->startSection('judul', 'Bayar QRIS'); ?>

<?php $__env->startSection('isi'); ?>
<?php
    $cfg = [
        'payload' => $bayar->qris_payload,
        'sisa' => $sisa,
        'status' => $bayar->status_pembayaran,
        'urlStatus' => route('toko.pesanan.status', $penjualan),
        'urlNota' => route('toko.pesanan.show', $penjualan),
    ];
?>
<div class="mx-auto max-w-md space-y-4">
    <div class="rounded-2xl bg-white p-6 text-center shadow-sm" x-data="qrisBayar(<?php echo \Illuminate\Support\Js::from($cfg)->toHtml() ?>)">
        <p class="text-xs text-slate-500">Pesanan <?php echo e($penjualan->no_nota_penjualan); ?></p>
        <p class="mt-1 text-sm text-slate-600">Total pembayaran</p>
        <p class="text-3xl font-bold text-navy"><?php echo e(\App\Support\Rupiah::format($bayar->jumlah_tagihan)); ?></p>

        <div x-show="status === 'menunggu' && sisa > 0" class="mt-4">
            <?php if(\App\Models\Pengaturan::ambil('qris_gambar') && \Illuminate\Support\Facades\Storage::disk('public')->exists(\App\Models\Pengaturan::ambil('qris_gambar'))): ?>
                <img src="<?php echo e(\Illuminate\Support\Facades\Storage::url(\App\Models\Pengaturan::ambil('qris_gambar'))); ?>" alt="QRIS <?php echo e(\App\Models\Pengaturan::ambil('nama_merchant_qris', config('koperasi.nama'))); ?>" class="mx-auto max-h-72 w-auto object-contain">
                <p class="mt-2 text-sm font-semibold text-slate-700"><?php echo e(\App\Models\Pengaturan::ambil('nama_merchant_qris', config('koperasi.nama'))); ?></p>
                <p class="mt-1 text-xs text-amber-700">Masukkan nominal pembayaran sesuai total tagihan jika aplikasi pembayaran memintanya.</p>
            <?php else: ?>
                <canvas x-ref="qr" class="mx-auto"></canvas>
            <?php endif; ?>
            <p class="mt-3 text-sm text-slate-600">Selesaikan dalam <span class="font-mono text-lg font-bold text-red-600" x-text="waktu"></span></p>
            <p class="mt-1 text-xs text-slate-400">Scan dengan aplikasi e-wallet atau m-banking. Halaman ini otomatis berpindah setelah pembayaran dikonfirmasi.</p>
        </div>

        <div x-show="status !== 'menunggu' || sisa <= 0" x-cloak class="mt-6 rounded-lg bg-red-50 p-4 text-sm text-red-600">
            <p class="font-semibold">Waktu pembayaran habis.</p>
            <p>Pesanan akan dibatalkan otomatis dan stok dikembalikan. Silakan buat pesanan baru.</p>
            <a href="<?php echo e(route('toko.pesanan.show', $penjualan)); ?>" class="btn-navy mt-3">Lihat Pesanan</a>
        </div>
    </div>

    <form method="POST" action="<?php echo e(route('toko.pesanan.bukti', $penjualan)); ?>" enctype="multipart/form-data" class="rounded-xl bg-white p-4 shadow-sm">
        <?php echo csrf_field(); ?>
        <p class="font-semibold text-navy">Sudah bayar? Unggah bukti (opsional)</p>
        <p class="text-xs text-slate-500">Membantu pengurus memverifikasi lebih cepat. JPG/PNG/WEBP, maks. 2 MB.</p>
        <input type="file" name="bukti" accept="image/png,image/jpeg,image/webp" required class="mt-3 block w-full text-sm">
        <?php if($bayar->bukti_bayar): ?>
            <p class="mt-2 text-xs text-sukses">Bukti sudah terunggah; mengunggah lagi akan menggantinya.</p>
        <?php endif; ?>
        <button class="btn-navy mt-3 w-full">Kirim Bukti</button>
    </form>

    <form method="POST" action="<?php echo e(route('toko.pesanan.batal', $penjualan)); ?>" onsubmit="return confirm('Batalkan pesanan ini?')">
        <?php echo csrf_field(); ?>
        <button class="w-full text-center text-sm text-red-600 hover:underline">Batalkan pesanan</button>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.toko', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/vol14_7/infinityfree.com/if0_43425548/htdocs/resources/views/toko/pesanan/bayar.blade.php ENDPATH**/ ?>