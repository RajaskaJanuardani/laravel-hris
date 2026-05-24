@extends('layouts.app', ['heading' => 'Absensi RFID'])
@section('content')
<div class="card p-4">
    <div class="d-flex justify-content-between mb-3"><h2 class="h5">Data Absensi</h2><a class="btn btn-primary" href="{{ route('admin.attendance.monitoring') }}">Monitoring RFID</a></div>
    <table class="table align-middle"><thead><tr><th>Tanggal</th><th>Karyawan</th><th>Masuk</th><th>Pulang</th><th>Telat</th><th>Lembur</th><th>Status</th></tr></thead>
    <tbody>@forelse($attendances as $attendance)<tr><td>{{ $attendance->attendance_date->format('d M Y') }}</td><td>{{ $attendance->employee->full_name }}</td><td>{{ $attendance->check_in_time?->format('H:i') ?? '-' }}</td><td>{{ $attendance->check_out_time?->format('H:i') ?? '-' }}</td><td>{{ $attendance->late_minutes }} menit</td><td>{{ number_format($attendance->overtime_hours, 2) }} jam</td><td><span class="badge text-bg-primary">{{ $attendance->status }}</span></td></tr>@empty<tr><td colspan="7" class="text-muted">Belum ada absensi.</td></tr>@endforelse</tbody></table>
    {{ $attendances->links() }}
</div>
@endsection
