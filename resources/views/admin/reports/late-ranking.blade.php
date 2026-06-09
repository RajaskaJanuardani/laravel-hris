@extends('layouts.app', ['heading' => 'Ranking Telat'])
@section('content')
<div class="card p-4 mb-4">
    <h2 class="h5 mb-3">Filter</h2>
    @component('admin.reports._filters', ['routeName' => 'admin.reports.late-ranking'])
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

<div class="card overflow-hidden">
    <div class="d-flex justify-content-between align-items-center p-4 border-bottom">
        <div>
            <h2 class="h5 mb-1">Ranking Telat</h2>
            <div class="text-muted small">{{ $from->translatedFormat('d M Y') }} - {{ $to->translatedFormat('d M Y') }}</div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Karyawan</th>
                    <th>Telat (hari)</th>
                    <th>Total Telat (menit)</th>
                    <th>Telat Terakhir</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $i => $row)
                    <tr>
                        <td><span class="ta-code-chip">#{{ $rows->firstItem() + $i }}</span></td>
                        <td>@include('shared._employee_table_cell', ['name' => $row->full_name, 'meta' => $row->karyawan_id])</td>
                        <td>{{ (int) $row->late_days }}</td>
                        <td>{{ (int) $row->menit_telat_total }}</td>
                        <td><span class="ta-code-chip">{{ $row->latest_late_date ? \Carbon\Carbon::parse($row->latest_late_date)->format('d M Y') : '-' }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="ta-table-empty">Tidak ada data telat untuk periode ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @include('shared._pagination', ['paginator' => $rows, 'label' => 'ranking'])
</div>
@endsection
