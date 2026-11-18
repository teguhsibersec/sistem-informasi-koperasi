<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Data Pengurus</title>
    <style>
        @page { margin: 16mm 12mm; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 9px; color: #1e293b; }
        h1 { font-size: 15px; margin: 0; color: #0B1F6B; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #0B1F6B; color: #fff; padding: 5px 6px; text-align: left; }
        td { padding: 4px 6px; border-bottom: 1px solid #e2e8f0; }
        .foot { margin-top: 12px; color: #94a3b8; font-size: 8px; }
    </style>
</head>
<body>
    <div style="font-size:10px;font-weight:bold">{{ $koperasi }}</div>
    <h1>Laporan Data Pengurus</h1>
    <table>
        <thead><tr><th>No</th><th>ID Pengurus</th><th>Nama Pengurus</th><th>Jabatan</th><th>Alamat Pengurus</th><th>Kota</th><th>Telepon</th></tr></thead>
        <tbody>
        @foreach ($pengurus as $i => $p)
            <tr><td>{{ $i + 1 }}</td><td>{{ $p->kode_pengurus }}</td><td>{{ $p->nama_pengurus }}</td><td>{{ \App\Models\Pengurus::JABATAN[$p->jabatan_pengurus] ?? $p->jabatan_pengurus }}</td><td>{{ $p->alamat_pengurus ?? '-' }}</td><td>{{ $p->kota_pengurus ?? '-' }}</td><td>{{ $p->telepon_pengurus ?? '-' }}</td></tr>
        @endforeach
        </tbody>
    </table>
    <p class="foot">Total {{ $pengurus->count() }} pengurus. Dicetak {{ $dicetak }}.</p>
</body>
</html>
