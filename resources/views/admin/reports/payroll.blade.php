@extends('layouts.app', ['heading' => 'Laporan Payroll'])
@section('content')
<div class="report-payroll-page">
    <div class="card p-4 mb-4">
        <h2 class="h5 mb-3">Filter</h2>
        @component('admin.reports._filters', ['routeName' => 'admin.reports.payroll'])
            <div class="col-md-3">
                <label class="form-label">Dari</label>
                <input class="form-control" type="date" name="from" value="{{ $from->format('Y-m-d') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Sampai</label>
                <input class="form-control" type="date" name="to" value="{{ $to->format('Y-m-d') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Periode Payroll</label>
                <select class="form-select" name="periode_penggajian_id">
                    <option value="">Semua</option>
                    @foreach($periods as $p)
                        <option value="{{ $p->id }}" @selected($periodId===$p->id)>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
        @endcomponent
    </div>

    <div class="card overflow-hidden">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 p-4 border-bottom">
            <div>
                <h2 class="h5 mb-1">Rekap Slip Gaji</h2>
                <div class="text-muted small">{{ $from->translatedFormat('d M Y') }} - {{ $to->translatedFormat('d M Y') }}</div>
            </div>
            <div>
                <a class="btn btn-outline-success" href="{{ route('admin.reports.payroll.excel', request()->query()) }}">Export Excel</a>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead><tr><th>Periode</th><th>Karyawan</th><th>Gaji Pokok</th><th>Lembur</th><th>Potongan</th><th>Bersih</th><th>Status</th><th></th></tr></thead>
                <tbody>
                @forelse($rows as $row)
                    <tr>
                        <td><span class="ta-code-chip">{{ $row->payrollPeriod->name }}</span></td>
                        <td>@include('shared._employee_table_cell', ['employee' => $row->employee])</td>
                        <td>Rp {{ number_format($row->gaji_pokok,0,',','.') }}</td>
                        <td>Rp {{ number_format($row->upah_lembur,0,',','.') }}</td>
                        <td>Rp {{ number_format($row->total_potongan,0,',','.') }}</td>
                        <td class="fw-semibold">Rp {{ number_format($row->gaji_bersih,0,',','.') }}</td>
                        @php($status = \App\Support\DisplayLabel::status($row->status))
                        <td><span class="badge text-bg-{{ $status['badge'] }}">{{ $status['label'] }}</span></td>
                        <td class="text-end">
                            <div class="ta-action-group justify-content-end">
                                <a class="btn btn-sm btn-outline-danger" href="{{ route('admin.reports.payroll.payslip.pdf', $row) }}">PDF</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="ta-table-empty">Belum ada payslip pada periode ini.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @include('shared._pagination', ['paginator' => $rows, 'label' => 'slip'])
    </div>
</div>
@endsection
