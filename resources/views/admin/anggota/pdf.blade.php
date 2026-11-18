<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Data Anggota</title>
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
    <h1>Laporan Data Anggota</h1>
    <table>
        <thead><tr><th>No</th><th>Kode Anggota</th><th>NIS</th><th>Nama Anggota</th><th>Kelas</th><th>Alamat Anggota</th><th>Kota</th><th>Telepon</th><th>Status</th></tr></thead>
        <tbody>
        @foreach ($anggota as $i => $a)
            <tr><td>{{ $i + 1 }}</td><td>{{ $a->no_anggota }}</td><td>{{ $a->nis_anggota }}</td><td>{{ $a->nama_anggota }}</td><td>{{ $a->kelas_anggota }}</td><td>{{ $a->alamat_anggota ?? '-' }}</td><td>{{ $a->kota_anggota ?? '-' }}</td><td>{{ $a->no_hp_anggota ?? '-' }}</td><td>{{ ucfirst($a->status_anggota) }}</td></tr>
        @endforeach
        </tbody>
    </table>
    <p class="foot">Total {{ $anggota->count() }} anggota. Dicetak {{ $dicetak }}.</p>
</body>
</html>
