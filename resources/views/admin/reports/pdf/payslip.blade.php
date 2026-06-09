<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Slip Gaji</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111; }
        .muted { color: #555; font-size: 10px; }
        .card { border: 1px solid #e5e7eb; border-radius: 10px; padding: 14px; }
        h1 { font-size: 18px; margin: 0 0 4px; }
        h2 { font-size: 12px; margin: 14px 0 8px; }
        .row { width: 100%; }
        .row td { vertical-align: top; }
        .kv { width: 100%; border-collapse: collapse; }
        .kv td { padding: 4px 0; }
        .kv td:first-child { width: 38%; color: #444; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { border: 1px solid #e5e7eb; padding: 8px; }
        .table th { background: #f3f4f6; text-align: left; }
        .right { text-align: right; }
        .total { font-size: 14px; font-weight: 700; }
    </style>
</head>
<body>
    <div class="card">
        <table class="row">
            <tr>
                <td>
                    <h1>Slip Gaji</h1>
                    <div class="muted">Periode: {{ $payslip->payrollPeriod->name }}</div>
                    <div class="muted">Tanggal Payroll: {{ $payslip->tanggal_penggajian?->format('d M Y') }}</div>
                </td>
                <td class="right">
                    <div class="muted">Dicetak: {{ now()->format('d/m/Y H:i') }}</div>
                    <div>Status: <strong>{{ \App\Support\DisplayLabel::statusLabel($payslip->status) }}</strong></div>
                </td>
            </tr>
        </table>

        <h2>Data Karyawan</h2>
        <table class="kv">
            <tr><td>NIK</td><td>: {{ $payslip->employee->karyawan_id }}</td></tr>
            <tr><td>Nama</td><td>: {{ $payslip->employee->full_name }}</td></tr>
            <tr><td>Email</td><td>: {{ $payslip->employee->email }}</td></tr>
        </table>

        <h2>Rincian</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Komponen</th>
                    <th class="right">Nilai</th>
                </tr>
            </thead>
            <tbody>
                <tr><td>Gaji Pokok</td><td class="right">Rp {{ number_format($payslip->gaji_pokok, 0, ',', '.') }}</td></tr>
                <tr><td>Lembur ({{ number_format($payslip->jam_lembur, 2) }} jam)</td><td class="right">Rp {{ number_format($payslip->upah_lembur, 0, ',', '.') }}</td></tr>
                <tr><td>THR</td><td class="right">Rp {{ number_format($payslip->bonus_thr, 0, ',', '.') }}</td></tr>
                <tr><td>Potongan Telat ({{ $payslip->total_menit_telat }} menit)</td><td class="right">Rp {{ number_format($payslip->potongan_telat, 0, ',', '.') }}</td></tr>
                <tr><td>Total Potongan</td><td class="right">Rp {{ number_format($payslip->total_potongan, 0, ',', '.') }}</td></tr>
                <tr><td class="total">Gaji Bersih</td><td class="right total">Rp {{ number_format($payslip->gaji_bersih, 0, ',', '.') }}</td></tr>
            </tbody>
        </table>

        @if($payslip->catatan)
            <h2>Catatan</h2>
            <div>{{ $payslip->catatan }}</div>
        @endif
    </div>
</body>
</html>
