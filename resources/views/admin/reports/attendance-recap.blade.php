@extends('layouts.app', ['heading' => 'Rekap Absensi (Periode)'])
@section('content')
<div class="card p-4 mb-4">
    <h2 class="h5 mb-3">Filter</h2>
    @component('admin.reports._filters', ['routeName' => 'admin.reports.attendance.recap'])
        <div class="col-md-3">
            <label class="form-label">Dari</label>
            <input class="form-control" type="date" name="from" value="{{ $from->format('Y-m-d') }}">
        </div>
        <div class="col-md-3">
            <label class="form-label">Sampai</label>
            <input class="form-control" type="date" name="to" value="{{ $to->format('Y-m-d') }}">
        </div>
    @endcomponent
</div>

<div class="card p-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <h2 class="h5 mb-1">Rekap Per Karyawan</h2>
            <div class="text-muted small">{{ $from->translatedFormat('d M Y') }} - {{ $to->translatedFormat('d M Y') }}</div>
        </div>
        <div>
            <a class="btn btn-outline-success" href="{{ route('admin.reports.attendance.recap.excel', request()->query()) }}">Export Excel</a>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Karyawan</th>
                    <th>Hadir</th>
                    <th>Telat</th>
                    <th>Total Telat (menit)</th>
                    <th>Absen</th>
                    <th>Cuti</th>
                    <th>Lembur (jam)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $row)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $row->full_name }}</div>
                            <div class="text-muted small">{{ $row->employee_id }}</div>
                        </td>
                        <td>{{ (int) $row->present_days }}</td>
                        <td>{{ (int) $row->late_days }}</td>
                        <td>{{ (int) $row->late_minutes_total }}</td>
                        <td>{{ (int) $row->absent_days }}</td>
                        <td>{{ (int) $row->leave_days_total }}</td>
                        <td>{{ number_format((float) $row->overtime_hours_total, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-muted">Belum ada data untuk periode ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $rows->links() }}
</div>
@endsection
