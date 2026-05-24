@extends('layouts.app', ['heading' => 'Laporan Lembur'])
@section('content')
<div class="card p-4 mb-4">
    <h2 class="h5 mb-3">Filter</h2>
    @component('admin.reports._filters', ['routeName' => 'admin.reports.overtime'])
        <div class="col-md-3">
            <label class="form-label">Dari</label>
            <input class="form-control" type="date" name="from" value="{{ $from->format('Y-m-d') }}">
        </div>
        <div class="col-md-3">
            <label class="form-label">Sampai</label>
            <input class="form-control" type="date" name="to" value="{{ $to->format('Y-m-d') }}">
        </div>
        <div class="col-md-4">
            <label class="form-label">Karyawan</label>
            <select class="form-select" name="employee_id">
                <option value="">Semua</option>
                @foreach($employees as $emp)
                    <option value="{{ $emp->id }}" @selected($employeeId===$emp->id)>{{ $emp->full_name }} ({{ $emp->employee_id }})</option>
                @endforeach
            </select>
        </div>
    @endcomponent
</div>

<div class="card p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="h5 mb-1">Approval Lembur</h2>
            <div class="text-muted small">{{ $from->translatedFormat('d M Y') }} - {{ $to->translatedFormat('d M Y') }}</div>
        </div>
        <div>
            <a class="btn btn-outline-success" href="{{ route('admin.reports.overtime.excel', request()->query()) }}">Export Excel</a>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead><tr><th>Tanggal</th><th>Karyawan</th><th>Jam</th><th>Catatan</th><th>Approved By</th></tr></thead>
            <tbody>
            @forelse($rows as $row)
                <tr>
                    <td>{{ $row->overtime_date->format('d M Y') }}</td>
                    <td>{{ $row->employee->full_name }}</td>
                    <td>{{ $row->start_time?->format('H:i') ?? '17:00' }} - {{ $row->end_time?->format('H:i') ?? '-' }}</td>
                    <td>{{ $row->notes ?? '-' }}</td>
                    <td>{{ $row->approvedBy?->name ?? '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-muted">Belum ada lembur pada periode ini.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $rows->links() }}
</div>
@endsection
