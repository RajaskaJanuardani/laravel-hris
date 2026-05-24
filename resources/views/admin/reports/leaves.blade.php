@extends('layouts.app', ['heading' => 'Laporan Cuti & Izin'])
@section('content')
<div class="card p-4 mb-4">
    <h2 class="h5 mb-3">Filter</h2>
    @component('admin.reports._filters', ['routeName' => 'admin.reports.leaves'])
        <div class="col-md-3">
            <label class="form-label">Dari</label>
            <input class="form-control" type="date" name="from" value="{{ $from->format('Y-m-d') }}">
        </div>
        <div class="col-md-3">
            <label class="form-label">Sampai</label>
            <input class="form-control" type="date" name="to" value="{{ $to->format('Y-m-d') }}">
        </div>
        <div class="col-md-3">
            <label class="form-label">Status</label>
            <select class="form-select" name="status">
                <option value="">Semua</option>
                @foreach(['pending','approved','rejected'] as $s)
                    <option value="{{ $s }}" @selected($status===$s)>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
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
            <h2 class="h5 mb-1">Pengajuan Cuti & Izin</h2>
            <div class="text-muted small">{{ $from->translatedFormat('d M Y') }} - {{ $to->translatedFormat('d M Y') }}</div>
        </div>
        <div>
            <a class="btn btn-outline-success" href="{{ route('admin.reports.leaves.excel', request()->query()) }}">Export Excel</a>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead><tr><th>Karyawan</th><th>Tipe</th><th>Tanggal</th><th>Durasi</th><th>Status</th><th>Approved By</th></tr></thead>
            <tbody>
            @forelse($rows as $row)
                <tr>
                    <td>
                        <div class="fw-semibold">{{ $row->employee->full_name }}</div>
                        <div class="text-muted small">{{ $row->employee->employee_id }}</div>
                    </td>
                    <td>{{ $row->leaveType->name }}</td>
                    <td>{{ $row->start_date->format('d M') }} - {{ $row->end_date->format('d M Y') }}</td>
                    <td>{{ $row->number_of_days }} hari</td>
                    <td>
                        <span class="badge text-bg-{{ $row->status === 'approved' ? 'success' : ($row->status === 'rejected' ? 'danger' : 'warning') }}">{{ $row->status }}</span>
                    </td>
                    <td>{{ $row->approvedBy?->name ?? '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-muted">Belum ada pengajuan cuti/izin pada periode ini.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $rows->links() }}
</div>
@endsection
