@extends('layouts.app', ['heading' => 'Slip Gaji Saya'])
@section('content')
<div class="card overflow-hidden">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 p-4 border-bottom">
        <div>
            <h2 class="h5 mb-1">Daftar Slip Gaji</h2>
            <div class="text-muted small">Rincian gaji pokok, lembur, THR, potongan, dan gaji bersih.</div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead><tr><th>Periode</th><th>Gaji Pokok</th><th>Lembur</th><th>THR</th><th>Potongan</th><th>Bersih</th><th></th></tr></thead>
            <tbody>
            @forelse($slip_gaji as $payslip)
                <tr>
                    <td><span class="ta-code-chip">{{ $payslip->payrollPeriod->name }}</span></td>
                    <td>Rp {{ number_format($payslip->gaji_pokok,0,',','.') }}</td>
                    <td>Rp {{ number_format($payslip->upah_lembur,0,',','.') }}</td>
                    <td>Rp {{ number_format($payslip->bonus_thr,0,',','.') }}</td>
                    <td>Rp {{ number_format($payslip->total_potongan,0,',','.') }}</td>
                    <td class="fw-semibold">Rp {{ number_format($payslip->gaji_bersih,0,',','.') }}</td>
                    <td><a class="btn btn-sm btn-outline-primary" href="{{ route('employee.payslip.show',$payslip) }}">Detail</a></td>
                </tr>
            @empty
                <tr><td colspan="7" class="ta-table-empty">Belum ada slip gaji.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if(method_exists($slip_gaji, 'links'))
        @include('shared._pagination', ['paginator' => $slip_gaji, 'label' => 'slip'])
    @endif
</div>
@endsection
