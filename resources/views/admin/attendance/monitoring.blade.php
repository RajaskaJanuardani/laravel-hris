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
        <div class="card p-4">
            <h2 class="h5">Log Scan Terbaru</h2>
            <table class="table align-middle"><thead><tr><th>Waktu</th><th>UID</th><th>Karyawan</th><th>Tipe</th><th>Status</th></tr></thead>
            <tbody>@forelse($logs as $log)<tr><td>{{ $log->scanned_at->format('H:i:s d/m') }}</td><td>{{ $log->uid }}</td><td>{{ $log->employee->full_name ?? '-' }}</td><td>{{ $log->scan_type }}</td><td><span class="badge {{ $log->status==='success'?'text-bg-success':'text-bg-danger' }}">{{ $log->message }}</span></td></tr>@empty<tr><td colspan="5" class="text-muted">Belum ada log.</td></tr>@endforelse</tbody></table>
        </div>
    </div>
</div>
@endsection
