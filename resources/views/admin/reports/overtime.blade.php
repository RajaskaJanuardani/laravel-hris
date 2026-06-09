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
            <select class="form-select" name="karyawan_id">
                <option value="">Semua</option>
                @foreach($karyawan as $emp)
                    <option value="{{ $emp->id }}" @selected($employeeId===$emp->id)>{{ $emp->full_name }} ({{ $emp->karyawan_id }})</option>
                @endforeach
            </select>
        </div>
    @endcomponent
</div>

<div class="card overflow-hidden">
    <div class="d-flex justify-content-between align-items-center p-4 border-bottom">
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
            <thead><tr><th>Tanggal</th><th>Karyawan</th><th>Jam</th><th>Catatan</th><th>Disetujui Oleh</th></tr></thead>
            <tbody>
            @forelse($rows as $row)
                <tr>
                    <td><span class="ta-code-chip">{{ $row->tanggal_lembur->format('d M Y') }}</span></td>
                    <td>@include('shared._employee_table_cell', ['employee' => $row->employee])</td>
                    <td><span class="ta-time-pill">{{ $row->jam_mulai?->format('H:i') ?? '17:00' }} - {{ $row->jam_selesai?->format('H:i') ?? '-' }}</span></td>
                    <td>{{ $row->catatan ?? '-' }}</td>
                    <td>{{ $row->approvedBy?->name ?? '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="ta-table-empty">Belum ada lembur pada periode ini.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @include('shared._pagination', ['paginator' => $rows, 'label' => 'lembur'])
</div>
@endsection
