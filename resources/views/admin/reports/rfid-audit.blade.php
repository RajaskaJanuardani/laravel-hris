@extends('layouts.app', ['heading' => 'Audit Scan RFID'])
@section('content')
<div class="card p-4 mb-4">
    <h2 class="h5 mb-3">Filter</h2>
    @component('admin.reports._filters', ['routeName' => 'admin.reports.rfid-audit'])
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
                @foreach(['success','failed'] as $s)
                    <option value="{{ $s }}" @selected($status===$s)>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Source</label>
            <select class="form-select" name="source">
                <option value="">Semua</option>
                @foreach(['esp32','simulator'] as $src)
                    <option value="{{ $src }}" @selected($source===$src)>{{ $src }}</option>
                @endforeach
            </select>
        </div>
    @endcomponent
</div>

<div class="card p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="h5 mb-1">Log Scan RFID</h2>
            <div class="text-muted small">{{ $from->translatedFormat('d M Y') }} - {{ $to->translatedFormat('d M Y') }}</div>
        </div>
        <div>
            <a class="btn btn-outline-success" href="{{ route('admin.reports.rfid-audit.excel', request()->query()) }}">Export Excel</a>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead><tr><th>Waktu</th><th>Karyawan/UID</th><th>Device</th><th>IP</th><th>Type</th><th>Status</th><th>Message</th></tr></thead>
            <tbody>
            @forelse($rows as $row)
                <tr>
                    <td>{{ $row->scanned_at->format('d M Y H:i') }}</td>
                    <td>
                        <div class="fw-semibold">{{ $row->employee?->full_name ?? $row->uid }}</div>
                        <div class="text-muted small">{{ $row->uid }}</div>
                    </td>
                    <td>{{ $row->device_name ?? '-' }}<div class="text-muted small">{{ $row->source }}</div></td>
                    <td>{{ $row->ip_address ?? '-' }}</td>
                    <td>{{ $row->scan_type }}</td>
                    <td><span class="badge text-bg-{{ $row->status === 'success' ? 'success' : 'danger' }}">{{ $row->status }}</span></td>
                    <td>{{ $row->message }}</td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-muted">Belum ada log pada periode ini.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $rows->links() }}
</div>
@endsection
