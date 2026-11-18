<?php
/**
 * Web Installer untuk deploy Laravel di InfinityFree / shared hosting.
 * Cara pakai: setelah upload, akses https://domain-anda/install.php SEKALI.
 * Setelah selesai, file ini akan menghapus dirinya sendiri.
 *
 * Untuk reset total (hapus semua data & isi ulang): akses install.php?fresh=1
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');
@set_time_limit(300);

$fresh = isset($_GET['fresh']);
$installedFlag = __DIR__ . '/storage/framework/installed.txt';

function out($msg, $type = 'info') {
    $colors = ['ok' => '#15803d', 'err' => '#b91c1c', 'info' => '#1d4ed8', 'warn' => '#a16207'];
    $c = $colors[$type] ?? $colors['info'];
    echo "<div style='margin:6px 0;padding:8px 12px;border-left:4px solid $c;background:#f8fafc;border-radius:4px;font-family:monospace;font-size:14px;'>$msg</div>";
}

echo "<!DOCTYPE html><html><head><meta charset='utf-8'><meta name='viewport' content='width=device-width,initial-scale=1'>";
echo "<title>Installer Koperasi</title>";
echo "<style>body{max-width:760px;margin:30px auto;padding:0 16px;font-family:system-ui,sans-serif;background:#f1f5f9}h1{color:#0f172a}a.btn{display:inline-block;margin:10px 4px;padding:10px 18px;background:#2563eb;color:#fff;text-decoration:none;border-radius:6px}a.btn.red{background:#dc2626}</style>";
echo "</head><body><h1>🚀 Installer Aplikasi Koperasi</h1>";

// ---------- 0. Jika sudah terinstall dan tanpa ?fresh ----------
if (file_exists($installedFlag) && !$fresh) {
    out("Aplikasi sudah pernah diinstall. Untuk masuk ke aplikasi, klik tombol di bawah.", 'ok');
    echo "<p><a class='btn' href='/'>Buka Aplikasi →</a></p>";
    echo "<p style='font-size:13px;color:#64748b'>Ingin reset total (hapus SEMUA data &amp; isi ulang dari awal)? <a href='?fresh=1'>Klik di sini</a> (tidak disarankan jika sudah ada data asli).</p>";
    echo "</body></html>";
    exit;
}

$errors = [];

// ---------- 1. Cek PHP version ----------
if (PHP_VERSION_ID < 80200) {
    $errors[] = "PHP version " . PHP_VERSION . " terlalu tua. Laravel 11 butuh PHP >= 8.2. Silakan ganti di cPanel InfinityFree: menu <b>Select PHP Version</b> → pilih 8.2 atau 8.3, lalu refresh halaman ini.";
} else {
    out("✅ PHP " . PHP_VERSION . " (>= 8.2) — OK", 'ok');
}

// ---------- 2. Cek ekstensi ----------
$required = ['pdo', 'pdo_mysql', 'mbstring', 'openssl', 'tokenizer', 'xml', 'ctype', 'json', 'fileinfo', 'curl', 'gd'];
$missing = [];
foreach ($required as $ext) {
    if (!extension_loaded($ext)) $missing[] = $ext;
}
if ($missing) {
    $errors[] = "Ekstensi PHP berikut tidak aktif: <b>" . implode(', ', $missing) . "</b>. Aktifkan di cPanel → Select PHP Version → centang ekstensi tersebut.";
} else {
    out("✅ Semua ekstensi PHP required aktif — OK", 'ok');
}

// ---------- 3. Cek folder writable ----------
$writableDirs = ['storage', 'bootstrap/cache', 'public/storage'];
foreach ($writableDirs as $d) {
    if (!is_writable(__DIR__ . '/' . $d)) {
        @chmod(__DIR__ . '/' . $d, 0775);
        if (!is_writable(__DIR__ . '/' . $d)) {
            $errors[] = "Folder <b>$d</b> tidak bisa ditulis. Set permission ke 755 atau 775 via File Manager.";
        }
    }
}
if (!$errors) out("✅ Permission folder storage & cache — OK", 'ok');

if ($errors) {
    out("❌ Beberapa persyaratan belum terpenuhi. Perbaiki lalu refresh halaman ini:", 'err');
    foreach ($errors as $e) out("• $e", 'err');
    echo "</body></html>";
    exit;
}

// ---------- 4. Bootstrap Laravel ----------
try {
    require __DIR__ . '/vendor/autoload.php';
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    out("✅ Bootstrap Laravel berhasil", 'ok');
} catch (Throwable $e) {
    out("❌ Gagal bootstrap Laravel: " . $e->getMessage(), 'err');
    echo "<pre style='font-size:12px;overflow:auto'>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    echo "</body></html>";
    exit;
}

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// ---------- 5. Cek koneksi database ----------
try {
    DB::connection()->getPdo();
    $dbName = config('database.connections.mysql.database');
    out("✅ Koneksi ke database <b>$dbName</b> berhasil", 'ok');
} catch (Throwable $e) {
    out("❌ Gagal konek database: " . $e->getMessage(), 'err');
    out("Periksa file .env: DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD harus sesuai dengan yang ada di InfinityFree → MySQL Databases.", 'warn');
    echo "</body></html>";
    exit;
}

// ---------- 6. Migrasi ----------
try {
    if ($fresh) {
        out("🔄 Mode FRESH: menghapus semua tabel & membuat ulang...", 'warn');
        Artisan::call('migrate:fresh', ['--force' => true]);
    } else {
        Artisan::call('migrate', ['--force' => true]);
    }
    out("✅ Migrasi database selesai", 'ok');
} catch (Throwable $e) {
    out("❌ Migrasi gagal: " . $e->getMessage(), 'err');
    echo "<pre style='font-size:12px;overflow:auto'>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    echo "</body></html>";
    exit;
}

// ---------- 7. Seeding (hanya jika tabel users masih kosong, atau mode fresh) ----------
try {
    $userCount = DB::table('users')->count();
    if ($userCount == 0 || $fresh) {
        Artisan::call('db:seed', ['--force' => true]);
        out("✅ Seeding data awal selesai (admin, kasir, gudang, anggota, barang, dll.)", 'ok');
    } else {
        out("ℹ️ Database sudah berisi data ($userCount user), seeding dilewati.", 'info');
    }
} catch (Throwable $e) {
    out("⚠️ Seeding gagal (aplikasi tetap bisa jalan): " . $e->getMessage(), 'warn');
}

// ---------- 8. Bersihkan semua cache ----------
try {
    Artisan::call('optimize:clear');
    @array_map('unlink', glob(__DIR__ . '/bootstrap/cache/*.php'));
    out("✅ Cache (config, route, view) dibersihkan", 'ok');
} catch (Throwable $e) {
    out("⚠️ Gagal bersihkan cache: " . $e->getMessage(), 'warn');
}

// ---------- 9. Pastikan permission ----------
@chmod(__DIR__ . '/storage', 0775);
@chmod(__DIR__ . '/bootstrap/cache', 0775);
@chmod(__DIR__ . '/public/storage', 0775);

// ---------- 10. Tandai selesai ----------
@file_put_contents($installedFlag, date('c'));

out("🎉 <b>Instalasi selesai!</b>", 'ok');
echo "<div style='background:#fff;padding:16px;border-radius:8px;margin:12px 0;border:1px solid #e2e8f0'>";
echo "<h3 style='margin-top:0'>Akun login default (password semuanya: <code>password</code>):</h3>";
echo "<ul style='line-height:1.8'>";
echo "<li><b>admin</b> / password — Admin (akses penuh)</li>";
echo "<li><b>kasir</b> / password — Pengurus / Kasir</li>";
echo "<li><b>gudang</b> / password — Pengurus / Gudang</li>";
echo "<li><b>anggota</b> / password — Anggota (belanja toko)</li>";
echo "</ul>";
echo "<p style='color:#64748b;font-size:13px'>Segera ganti password setelah login pertama kali.</p>";
echo "</div>";

echo "<p><a class='btn' href='/'>Buka Aplikasi →</a></p>";

// ---------- 11. Hapus installer sendiri ----------
$deleted = @unlink(__FILE__);
if ($deleted) {
    out("🗑️ File install.php telah dihapus otomatis demi keamanan.", 'info');
} else {
    out("⚠️ Gagal menghapus install.php otomatis. <b>Hapus manual file install.php dari htdocs setelah ini.</b>", 'warn');
}

echo "<p style='font-size:12px;color:#94a3b8;margin-top:30px'>Installer Koperasi — untuk InfinityFree</p>";
echo "</body></html>";
