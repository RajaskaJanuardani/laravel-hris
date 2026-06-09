<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Absensi Harian</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111; }
        .meta { font-size: 10px; color: #444; margin-bottom: 10px; }
        h1 { font-size: 16px; margin: 0 0 6px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 6px 8px; }
        th { background: #f3f4f6; text-align: left; }
        .right { text-align: right; }
        .muted { color: #666; font-size: 10px; }
    </style>
</head>
<body>
    <h1>Laporan Absensi Harian</h1>
    <div class="meta">
        Tanggal: <strong>{{ $date->translatedFormat('d F Y') }}</strong>
        <span class="muted">| Dicetak: {{ now()->format('d/m/Y H:i') }}</span>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 18%;">Karyawan</th>
                <th style="width: 8%;">Masuk</th>
                <th style="width: 8%;">Pulang</th>
                <th style="width: 10%;">Telat (menit)</th>
                <th style="width: 10%;">Lembur (jam)</th>
                <th style="width: 10%;">Status</th>
                <th>Catatan</th>
            </tr>
        </thead>
        <tbody>
        @forelse($rows as $row)
            <tr>
                <td>
                    <strong>{{ $row->employee->full_name }}</strong><br>
                    <span class="muted">{{ $row->employee->karyawan_id }}</span>
                </td>
                <td>{{ $row->jam_masuk?->format('H:i') ?? '-' }}</td>
                <td>{{ $row->jam_pulang?->format('H:i') ?? '-' }}</td>
                <td class="right">{{ (int) $row->menit_telat }}</td>
                <td class="right">{{ number_format((float) $row->jam_lembur, 2) }}</td>
                <td>{{ \App\Support\DisplayLabel::statusLabel($row->status) }}</td>
                <td>{{ $row->catatan ?? '-' }}</td>
            </tr>
        @empty
            <tr><td colspan="7" class="muted">Tidak ada data.</td></tr>
        @endforelse
        </tbody>
    </table>
</body>
</html>
