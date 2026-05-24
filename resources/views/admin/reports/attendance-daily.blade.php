@extends('layouts.app', ['heading' => 'Laporan Absensi Harian'])
@section('content')
<div class="card p-4 mb-4">
    <h2 class="h5 mb-3">Filter</h2>
    @component('admin.reports._filters', ['routeName' => 'admin.reports.attendance.daily'])
        <div class="col-md-3">
            <label class="form-label">Tanggal</label>
            <input class="form-control" type="date" name="date" value="{{ $date->format('Y-m-d') }}">
        </div>
    @endcomponent
</div>

<div class="card p-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <h2 class="h5 mb-1">Absensi {{ $date->translatedFormat('d F Y') }}</h2>
            <div class="text-muted small">Masuk, pulang, telat, lembur, status.</div>
        </div>
        <div class="d-flex gap-2">
            <a class="btn btn-outline-success" href="{{ route('admin.reports.attendance.daily.excel', request()->query()) }}">Export Excel</a>
            <a class="btn btn-outline-danger" href="{{ route('admin.reports.attendance.daily.pdf', request()->query()) }}">Export PDF</a>
            <a class="btn btn-outline-secondary" href="{{ route('admin.reports.attendance.recap', ['from' => $date->format('Y-m-d'), 'to' => $date->format('Y-m-d')]) }}">Lihat Rekap</a>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Karyawan</th>
                    <th>Masuk</th>
                    <th>Pulang</th>
                    <th>Telat (menit)</th>
                    <th>Lembur (jam)</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $row)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $row->employee->full_name }}</div>
                            <div class="text-muted small">{{ $row->employee->employee_id }}</div>
                        </td>
                        <td>{{ $row->check_in_time?->format('H:i') ?? '-' }}</td>
                        <td>{{ $row->check_out_time?->format('H:i') ?? '-' }}</td>
                        <td>{{ (int) $row->late_minutes }}</td>
                        <td>{{ number_format((float) $row->overtime_hours, 2) }}</td>
                        <td><span class="badge text-bg-primary">{{ $row->status }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-muted">Tidak ada data absensi untuk tanggal ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
