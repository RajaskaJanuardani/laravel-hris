@extends('layouts.app', ['heading' => 'Payroll'])
@section('content')
<style>
    .payroll-page .payroll-summary {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .payroll-page .payroll-stat {
        position: relative;
        isolation: isolate;
        overflow: hidden;
        min-height: 148px;
        border: 1px solid rgba(228, 231, 236, .92);
        border-radius: 18px;
        background: linear-gradient(145deg, #ffffff 0%, #f8fbff 100%);
        box-shadow: 0 18px 42px rgba(16, 24, 40, .08);
        padding: 22px;
    }

    .payroll-page .payroll-stat::before {
        content: "";
        position: absolute;
        inset: 0 auto 0 0;
        width: 5px;
        background: var(--stat-color);
    }

    .payroll-page .payroll-stat::after {
        content: "";
        position: absolute;
        right: -36px;
        top: -44px;
        z-index: -1;
        width: 148px;
        height: 148px;
        border-radius: 999px;
        background: color-mix(in srgb, var(--stat-color) 16%, transparent);
    }

    .payroll-page .payroll-stat-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
    }

    .payroll-page .payroll-stat-label {
        color: var(--ta-gray-500);
        font-size: 14px;
        font-weight: 600;
    }

    .payroll-page .payroll-stat-value {
        color: var(--ta-gray-900);
        font-size: 30px;
        font-weight: 800;
        line-height: 1.05;
        margin-top: 16px;
    }

    .payroll-page .payroll-stat-note {
        color: var(--ta-gray-500);
        font-size: 14px;
        margin-top: 10px;
    }

    .payroll-page .payroll-stat-icon {
        display: inline-flex;
        width: 48px;
        height: 48px;
        flex: 0 0 48px;
        align-items: center;
        justify-content: center;
        border-radius: 16px;
        background: color-mix(in srgb, var(--stat-color) 12%, #ffffff);
        color: var(--stat-color);
        box-shadow: inset 0 0 0 1px color-mix(in srgb, var(--stat-color) 18%, transparent);
    }

    .payroll-page .payroll-stat-icon svg {
        width: 23px;
        height: 23px;
    }

    .payroll-page .payroll-table th,
    .payroll-page .payroll-table td {
        white-space: nowrap;
    }

    .payroll-page .payroll-table .employee-name {
        min-width: 180px;
        white-space: normal;
    }

    .payroll-page .payroll-pagination .pagination {
        align-items: center;
        gap: 6px;
        justify-content: flex-end;
        margin-bottom: 0;
    }

    .payroll-page .payroll-pagination .page-link {
        border-color: #e4e7ec;
        border-radius: 8px;
        color: #344054;
        font-size: 14px;
        min-width: 38px;
        padding: 8px 12px;
        text-align: center;
    }

    .payroll-page .payroll-pagination .active > .page-link {
        background: #465fff;
        border-color: #465fff;
        color: #fff;
    }

    .payroll-page .payroll-pagination .disabled > .page-link {
        color: #98a2b3;
    }

    html.dark .payroll-page .payroll-stat {
        border-color: rgba(159, 176, 207, .24);
        background: linear-gradient(145deg, rgba(12, 22, 44, .9), rgba(15, 23, 42, .82));
        box-shadow: 0 20px 44px rgba(0, 0, 0, .28);
    }

    html.dark .payroll-page .payroll-stat-label,
    html.dark .payroll-page .payroll-stat-note,
    html.dark .payroll-page .text-muted {
        color: #9fb0cf !important;
    }

    html.dark .payroll-page .payroll-stat-value {
        color: #f4f7ff;
    }

    html.dark .payroll-page .payroll-stat-icon {
        background: color-mix(in srgb, var(--stat-color) 18%, transparent);
        box-shadow: inset 0 0 0 1px color-mix(in srgb, var(--stat-color) 28%, transparent);
    }

    html.dark .payroll-page .payroll-pagination .page-link {
        background: rgba(255, 255, 255, .03);
        border-color: rgba(159, 176, 207, .25);
        color: #d6e0f1;
        box-shadow: 0 10px 24px rgba(0, 0, 0, .18);
    }

    html.dark .payroll-page .payroll-pagination .page-link:hover,
    html.dark .payroll-page .payroll-pagination .page-link:focus {
        background: rgba(70, 95, 255, .18);
        border-color: rgba(70, 95, 255, .55);
        color: #eef3ff;
        box-shadow: 0 0 0 .2rem rgba(70, 95, 255, .14);
    }

    html.dark .payroll-page .payroll-pagination .active > .page-link {
        background: #465fff;
        border-color: #465fff;
        color: #fff;
        box-shadow: 0 12px 28px rgba(70, 95, 255, .32);
    }

    html.dark .payroll-page .payroll-pagination .disabled > .page-link {
        background: rgba(255, 255, 255, .02);
        border-color: rgba(159, 176, 207, .16);
        color: rgba(159, 176, 207, .55);
    }

    @media (max-width: 1199.98px) {
        .payroll-page .payroll-summary {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 767.98px) {
        .payroll-page .payroll-summary {
            grid-template-columns: 1fr;
        }

        .payroll-page .payroll-stat {
            min-height: 136px;
        }
    }
</style>

<div class="payroll-page">
    <div class="payroll-summary">
        <div class="payroll-stat" style="--stat-color:#465fff;">
            <div class="payroll-stat-head">
                <div>
                    <div class="payroll-stat-label">Total Slip</div>
                    <div class="payroll-stat-value">{{ number_format($summary['total_slip']) }}</div>
                    <div class="payroll-stat-note">{{ $periods->count() }} periode payroll</div>
                </div>
                <span class="payroll-stat-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none"><path d="M8 7h8M8 11h8M8 15h5" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/><path d="M6.5 3.5h8.7L19 7.3v13.2H6.5A1.5 1.5 0 0 1 5 19V5a1.5 1.5 0 0 1 1.5-1.5Z" stroke="currentColor" stroke-width="1.9" stroke-linejoin="round"/><path d="M15 3.8V7h3.2" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
            </div>
        </div>
        <div class="payroll-stat" style="--stat-color:#14b8a6;">
            <div class="payroll-stat-head">
                <div>
                    <div class="payroll-stat-label">Total Gaji Bersih</div>
                    <div class="payroll-stat-value">Rp {{ number_format($summary['total_gaji_bersih'], 0, ',', '.') }}</div>
                    <div class="payroll-stat-note">Akumulasi semua slip</div>
                </div>
                <span class="payroll-stat-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none"><path d="M4 7.5h16v9H4v-9Z" stroke="currentColor" stroke-width="1.9" stroke-linejoin="round"/><path d="M7 10.5h.01M17 13.5h.01" stroke="currentColor" stroke-width="2.6" stroke-linecap="round"/><path d="M12 14.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z" stroke="currentColor" stroke-width="1.9"/></svg>
                </span>
            </div>
        </div>
        <div class="payroll-stat" style="--stat-color:#f59e0b;">
            <div class="payroll-stat-head">
                <div>
                    <div class="payroll-stat-label">Total THR</div>
                    <div class="payroll-stat-value">Rp {{ number_format($summary['total_thr'], 0, ',', '.') }}</div>
                    <div class="payroll-stat-note">Bonus yang sudah digenerate</div>
                </div>
                <span class="payroll-stat-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none"><path d="M4 10h16v10H4V10Z" stroke="currentColor" stroke-width="1.9" stroke-linejoin="round"/><path d="M3.5 6.5h17v3.5h-17V6.5Z" stroke="currentColor" stroke-width="1.9" stroke-linejoin="round"/><path d="M12 6.5V20M12 6.5c-1.7-2.3-4.8-2.2-4.8.1 0 1.3 1.3 2.1 4.8 1.9M12 6.5c1.7-2.3 4.8-2.2 4.8.1 0 1.3-1.3 2.1-4.8 1.9" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
            </div>
        </div>
        <div class="payroll-stat" style="--stat-color:#a855f7;">
            <div class="payroll-stat-head">
                <div>
                    <div class="payroll-stat-label">Draf</div>
                    <div class="payroll-stat-value">{{ number_format($summary['draft']) }}</div>
                    <div class="payroll-stat-note">Slip belum final</div>
                </div>
                <span class="payroll-stat-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none"><path d="M8 12h4M8 16h8" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/><path d="M6.5 3.5h8.7L19 7.3v13.2H6.5A1.5 1.5 0 0 1 5 19V5a1.5 1.5 0 0 1 1.5-1.5Z" stroke="currentColor" stroke-width="1.9" stroke-linejoin="round"/><path d="M15 3.8V7h3.2" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
            </div>
        </div>
    </div>

    <div class="card overflow-hidden">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 p-4 border-bottom">
            <div>
                <h2 class="h5 mb-1">Daftar Slip Gaji</h2>
                <div class="text-muted small">Periode 2 mingguan dengan gaji harian, lembur, potongan telat, dan THR.</div>
            </div>
            <a class="btn btn-primary" href="{{ route('admin.payroll.create') }}">Generate Payroll</a>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle payroll-table">
                <thead>
                    <tr>
                        <th>Periode</th>
                        <th>Karyawan</th>
                        <th>Tarif/Hari</th>
                        <th>Hari Kerja</th>
                        <th>Telat</th>
                        <th>Lembur</th>
                        <th>THR</th>
                        <th>Gaji Bersih</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($slip_gaji as $payslip)
                        <tr>
                            <td>
                                <div class="fw-semibold"><span class="ta-code-chip">{{ $payslip->payrollPeriod?->name ?? '-' }}</span></div>
                                <div class="small text-muted">{{ $payslip->tanggal_penggajian?->format('d M Y') ?? '-' }}</div>
                            </td>
                            <td class="employee-name">@include('shared._employee_table_cell', ['employee' => $payslip->employee])</td>
                            <td>Rp {{ number_format($payslip->tarif_harian, 0, ',', '.') }}</td>
                            <td>{{ $payslip->hari_kerja }} hari</td>
                            <td>
                                <div>{{ $payslip->total_menit_telat }} menit</div>
                                <div class="small text-muted">Rp {{ number_format($payslip->potongan_telat, 0, ',', '.') }}</div>
                            </td>
                            <td>
                                <div>{{ number_format($payslip->jam_lembur, 2) }} jam</div>
                                <div class="small text-muted">Rp {{ number_format($payslip->upah_lembur, 0, ',', '.') }}</div>
                            </td>
                            <td>Rp {{ number_format($payslip->bonus_thr, 0, ',', '.') }}</td>
                            <td class="fw-semibold">Rp {{ number_format($payslip->gaji_bersih, 0, ',', '.') }}</td>
                            @php($status = \App\Support\DisplayLabel::status($payslip->status))
                            <td><span class="badge text-bg-{{ $status['badge'] }}">{{ $status['label'] }}</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="ta-table-empty">Belum ada payroll.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($slip_gaji->hasPages())
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 px-4 py-3 border-top">
                <div class="small text-muted">
                    Menampilkan {{ $slip_gaji->firstItem() }} sampai {{ $slip_gaji->lastItem() }} dari {{ $slip_gaji->total() }} slip
                </div>
                <div class="payroll-pagination">
                    {{ $slip_gaji->onEachSide(1)->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
