@extends('layouts.app', ['heading' => 'Laporan Payroll'])
@section('content')
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
            <select class="form-select" name="payroll_period_id">
                <option value="">Semua</option>
                @foreach($periods as $p)
                    <option value="{{ $p->id }}" @selected($periodId===$p->id)>{{ $p->name }}</option>
                @endforeach
            </select>
        </div>
    @endcomponent
</div>

<div class="card p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="h5 mb-1">Rekap Payslip</h2>
            <div class="text-muted small">{{ $from->translatedFormat('d M Y') }} - {{ $to->translatedFormat('d M Y') }}</div>
        </div>
        <div>
            <a class="btn btn-outline-success" href="{{ route('admin.reports.payroll.excel', request()->query()) }}">Export Excel</a>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead><tr><th>Periode</th><th>Karyawan</th><th>Gaji Pokok</th><th>Lembur</th><th>Potongan</th><th>Bersih</th><th>Status</th><th></th></tr></thead>
            <tbody>
            @forelse($rows as $row)
                <tr>
                    <td>{{ $row->payrollPeriod->name }}</td>
                    <td>{{ $row->employee->full_name }}</td>
                    <td>Rp {{ number_format($row->base_salary,0,',','.') }}</td>
                    <td>Rp {{ number_format($row->overtime_amount,0,',','.') }}</td>
                    <td>Rp {{ number_format($row->total_deduction,0,',','.') }}</td>
                    <td class="fw-semibold">Rp {{ number_format($row->net_salary,0,',','.') }}</td>
                    <td><span class="badge text-bg-{{ $row->status === 'paid' ? 'success' : ($row->status === 'final' ? 'primary' : 'secondary') }}">{{ $row->status }}</span></td>
                    <td class="text-end">
                        <a class="btn btn-sm btn-outline-danger" href="{{ route('admin.reports.payroll.payslip.pdf', $row) }}">PDF</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-muted">Belum ada payslip pada periode ini.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $rows->links() }}
</div>
@endsection
