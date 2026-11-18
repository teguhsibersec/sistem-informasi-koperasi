<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>{{ $laporan['judul'] }}</title>
    <style>
        @page { margin: 18mm 14mm; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 9px; color: #1e293b; }
        h1 { font-size: 15px; margin: 0; color: #0B1F6B; }
        .sub { color: #64748b; margin: 2px 0 10px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #0B1F6B; color: #fff; padding: 5px 6px; font-weight: bold; }
        td { padding: 4px 6px; border-bottom: 1px solid #e2e8f0; }
        tfoot td { font-weight: bold; background: #f1f5f9; border-top: 2px solid #0B1F6B; }
        .r { text-align: right; } .l { text-align: left; }
        .ringkas td { border: 1px solid #e2e8f0; padding: 6px 8px; width: 25%; }
        .ringkas .lbl { color: #64748b; font-size: 8px; }
        .ringkas .val { font-size: 12px; font-weight: bold; color: #0B1F6B; }
        .catatan { margin-top: 10px; color: #64748b; font-size: 8px; }
        .foot { margin-top: 14px; color: #94a3b8; font-size: 8px; }
    </style>
</head>
<body>
    <div style="font-size:10px;font-weight:bold">{{ $koperasi }}</div>
    <h1>{{ $laporan['judul'] }}</h1>
    <p class="sub">{{ $laporan['periode'] }}</p>

    <table class="ringkas" style="margin-bottom:10px">
        <tr>
            @foreach ($laporan['ringkasan'] as $r)
                <td><div class="lbl">{{ $r['label'] }}</div><div class="val">{{ \App\Support\FormatLaporan::sel($r['nilai'], $r['tipe']) }}</div></td>
            @endforeach
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                @foreach ($laporan['kolom'] as $k)
                    <th class="{{ $k['tipe'] === 'teks' ? 'l' : 'r' }}">{{ $k['label'] }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse ($laporan['baris'] as $baris)
                <tr>
                    @foreach ($baris as $i => $nilai)
                        <td class="{{ $laporan['kolom'][$i]['tipe'] === 'teks' ? 'l' : 'r' }}">{{ \App\Support\FormatLaporan::sel($nilai, $laporan['kolom'][$i]['tipe']) }}</td>
                    @endforeach
                </tr>
            @empty
                <tr><td colspan="{{ count($laporan['kolom']) }}" style="text-align:center;padding:14px;color:#94a3b8">Tidak ada data pada periode ini.</td></tr>
            @endforelse
        </tbody>
        @if ($laporan['total'] && count($laporan['baris']))
            <tfoot>
                <tr>
                    @foreach ($laporan['total'] as $i => $nilai)
                        <td class="{{ $laporan['kolom'][$i]['tipe'] === 'teks' ? 'l' : 'r' }}">{{ \App\Support\FormatLaporan::sel($nilai, $laporan['kolom'][$i]['tipe']) }}</td>
                    @endforeach
                </tr>
            </tfoot>
        @endif
    </table>

    @foreach ($laporan['catatan'] as $c)
        <div class="catatan">* {{ $c }}</div>
    @endforeach
    <div class="foot">Dicetak oleh {{ $pencetak }} pada {{ now()->format('d/m/Y H:i') }} WIB</div>
</body>
</html>
