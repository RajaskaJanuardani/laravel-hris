@extends('layouts.app', ['heading' => 'Monitoring RFID'])
@section('content')
<div class="row g-4">
    <div class="col-lg-4">
        <form class="card p-4" method="POST" action="{{ route('admin.attendance.simulate') }}">
            @csrf
            <h2 class="h5">Simulator Scan RFID</h2>
            <p class="text-muted small">Masukkan UID kartu. Endpoint yang sama siap dipakai ESP32.</p>
            <input class="form-control form-control-lg mb-3" name="uid" placeholder="Contoh: A1B2C3D4" required>
            <button class="btn btn-primary w-100">Simulasikan Scan</button>
            <hr>
            <div class="small text-muted">Kartu aktif: {{ $cards->pluck('uid')->join(', ') }}</div>
        </form>
    </div>
    <div class="col-lg-8">
        <div class="card overflow-hidden">
            <div class="p-4 border-bottom">
                <h2 class="h5 mb-1">Log Scan Terbaru</h2>
                <div class="text-muted small">Aktivitas scan RFID yang baru masuk ke sistem.</div>
            </div>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>UID</th>
                            <th>Karyawan</th>
                            <th>Tipe</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($logs as $log)
                        @php($status = \App\Support\DisplayLabel::status($log->status))
                        <tr>
                            <td><span class="ta-code-chip">{{ $log->dipindai_pada->format('H:i:s d/m') }}</span></td>
                            <td><span class="ta-code-chip">{{ $log->uid }}</span></td>
                            <td>@include('shared._employee_table_cell', ['employee' => $log->employee, 'name' => $log->employee?->full_name ?? '-'])</td>
                            <td>{{ \App\Support\DisplayLabel::scanType($log->tipe_scan) }}</td>
                            <td><span class="badge text-bg-{{ $status['badge'] }}">{{ $log->message }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="ta-table-empty">Belum ada log.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
