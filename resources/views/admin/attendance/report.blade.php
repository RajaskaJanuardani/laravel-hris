@extends('layouts.app', ['heading' => 'Laporan Absensi'])
@section('content')
<div class="card p-4">
    <form class="row g-2 mb-3"><div class="col-md-3"><input class="form-control" type="date" name="from" value="{{ request('from') }}"></div><div class="col-md-3"><input class="form-control" type="date" name="to" value="{{ request('to') }}"></div><div class="col-md-2"><button class="btn btn-primary w-100">Filter</button></div></form>
    <table class="table"><thead><tr><th>Tanggal</th><th>Karyawan</th><th>Jabatan</th><th>Status</th><th>Telat</th><th>Lembur</th></tr></thead><tbody>@foreach($attendances as $attendance)<tr><td>{{ $attendance->attendance_date->format('d M Y') }}</td><td>{{ $attendance->employee->full_name }}</td><td>{{ ucfirst($attendance->employee->job_role) }}</td><td>{{ $attendance->status }}</td><td>{{ $attendance->late_minutes }} menit</td><td>{{ number_format($attendance->overtime_hours, 2) }} jam</td></tr>@endforeach</tbody></table>
</div>
@endsection
