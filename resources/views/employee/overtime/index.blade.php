@extends('layouts.app', ['heading' => 'Lembur Saya'])
@section('content')
<style>
    .employee-overtime-page .overtime-summary {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 18px;
        margin-bottom: 28px;
    }

    .employee-overtime-page .overtime-metric {
        position: relative;
        isolation: isolate;
        overflow: hidden;
        min-height: 154px;
        border: 1px solid rgba(228, 231, 236, .92);
        border-radius: 18px;
        background: linear-gradient(145deg, #ffffff 0%, #f8fbff 100%);
        box-shadow: 0 18px 42px rgba(16, 24, 40, .08);
        padding: 22px;
    }

    .employee-overtime-page .overtime-metric::before {
        content: "";
        position: absolute;
        inset: 0 auto 0 0;
        width: 5px;
        background: var(--metric-color);
    }

    .employee-overtime-page .overtime-metric::after {
        content: "";
        position: absolute;
        right: -34px;
        top: -42px;
        z-index: -1;
        width: 148px;
        height: 148px;
        border-radius: 999px;
        background: color-mix(in srgb, var(--metric-color) 16%, transparent);
    }

    .employee-overtime-page .overtime-metric-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
    }

    .employee-overtime-page .overtime-metric-label {
        color: var(--ta-gray-500);
        font-size: 14px;
        font-weight: 600;
    }

    .employee-overtime-page .overtime-metric-value {
        color: var(--ta-gray-900);
        font-size: 32px;
        font-weight: 800;
        line-height: 1.05;
        margin-top: 16px;
    }

    .employee-overtime-page .overtime-metric-note {
        color: var(--ta-gray-500);
        font-size: 14px;
        margin-top: 10px;
    }

    .employee-overtime-page .overtime-metric-icon {
        display: inline-flex;
        width: 48px;
        height: 48px;
        flex: 0 0 48px;
        align-items: center;
        justify-content: center;
        border-radius: 16px;
        background: color-mix(in srgb, var(--metric-color) 12%, #ffffff);
        color: var(--metric-color);
        box-shadow: inset 0 0 0 1px color-mix(in srgb, var(--metric-color) 18%, transparent);
    }

    .employee-overtime-page .overtime-metric-icon svg {
        width: 23px;
        height: 23px;
    }

    html.dark .employee-overtime-page .overtime-metric {
        border-color: rgba(159, 176, 207, .24);
        background: linear-gradient(145deg, rgba(12, 22, 44, .9), rgba(15, 23, 42, .82));
        box-shadow: 0 20px 44px rgba(0, 0, 0, .28);
    }

    html.dark .employee-overtime-page .overtime-metric-label,
    html.dark .employee-overtime-page .overtime-metric-note {
        color: #9fb0cf;
    }

    html.dark .employee-overtime-page .overtime-metric-value {
        color: #f4f7ff;
    }

    html.dark .employee-overtime-page .overtime-metric-icon {
        background: color-mix(in srgb, var(--metric-color) 18%, transparent);
        box-shadow: inset 0 0 0 1px color-mix(in srgb, var(--metric-color) 28%, transparent);
    }

    @media (max-width: 991.98px) {
        .employee-overtime-page .overtime-summary {
            grid-template-columns: 1fr;
        }

        .employee-overtime-page .overtime-metric {
            min-height: 136px;
        }
    }
</style>

@php
    $nextOvertimeDate = $nextOvertime?->tanggal_lembur?->format('d M') ?? '-';
    $nextOvertimeTime = $nextOvertime ? '17:00 - '.($nextOvertime->jam_selesai?->format('H:i') ?? '-') : 'Belum ada jadwal';
    $latestStatus = $nextOvertime ? \App\Support\DisplayLabel::statusLabel($nextOvertime->status) : 'Belum ada';
@endphp

<div class="employee-overtime-page">
<div class="overtime-summary">
    <div class="overtime-metric" style="--metric-color:#465fff;">
        <div class="overtime-metric-head">
            <div>
                <div class="overtime-metric-label">Lembur Disetujui Bulan Ini</div>
                <div class="overtime-metric-value">{{ $approvedOvertimeThisMonth }}</div>
                <div class="overtime-metric-note">Approval lembur aktif bulan berjalan</div>
            </div>
            <span class="overtime-metric-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none"><path d="M8 12.5 10.8 15 16 8.8" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/><path d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" stroke="currentColor" stroke-width="1.9"/></svg>
            </span>
        </div>
    </div>

    <div class="overtime-metric" style="--metric-color:#14b8a6;">
        <div class="overtime-metric-head">
            <div>
                <div class="overtime-metric-label">Lembur Berikutnya</div>
                <div class="overtime-metric-value">{{ $nextOvertimeDate }}</div>
                <div class="overtime-metric-note">{{ $nextOvertimeTime }}</div>
            </div>
            <span class="overtime-metric-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none"><path d="M7 3v3M17 3v3M4.5 9h15" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/><path d="M5 5.5h14a1.5 1.5 0 0 1 1.5 1.5v11.5A1.5 1.5 0 0 1 19 20H5a1.5 1.5 0 0 1-1.5-1.5V7A1.5 1.5 0 0 1 5 5.5Z" stroke="currentColor" stroke-width="1.9"/><path d="M12 13v3l2 1" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </span>
        </div>
    </div>

    <div class="overtime-metric" style="--metric-color:#f59e0b;">
        <div class="overtime-metric-head">
            <div>
                <div class="overtime-metric-label">Status Terakhir</div>
                <div class="overtime-metric-value">{{ $latestStatus }}</div>
                <div class="overtime-metric-note">Status dari approval terbaru</div>
            </div>
            <span class="overtime-metric-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none"><path d="M12 8v5" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/><path d="M12 16.5h.01" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/><path d="M10.3 4.5 2.8 17.2A2 2 0 0 0 4.5 20h15a2 2 0 0 0 1.7-2.8L13.7 4.5a2 2 0 0 0-3.4 0Z" stroke="currentColor" stroke-width="1.9" stroke-linejoin="round"/></svg>
            </span>
        </div>
    </div>
</div>
<div class="card overflow-hidden">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 p-4 border-bottom">
        <h2 class="h5 mb-0">Riwayat Approval Lembur</h2>
        <span class="text-muted small">Lembur yang disetujui admin akan muncul di sini.</span>
    </div>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Jam</th>
                    <th>Status</th>
                    <th>Catatan</th>
                    <th>Disetujui Oleh</th>
                </tr>
            </thead>
            <tbody>
                @forelse($overtimeApprovals as $approval)
                    @php($status = \App\Support\DisplayLabel::status($approval->status))
                    <tr>
                        <td><span class="ta-code-chip">{{ $approval->tanggal_lembur->format('d M Y') }}</span></td>
                        <td><span class="ta-time-pill">17:00 - {{ $approval->jam_selesai->format('H:i') }}</span></td>
                        <td><span class="badge text-bg-{{ $status['badge'] }}">{{ $status['label'] }}</span></td>
                        <td>{{ $approval->catatan ?? '-' }}</td>
                        <td>{{ $approval->approvedBy?->name ?? '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="ta-table-empty">Belum ada approval lembur.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @include('shared._pagination', ['paginator' => $overtimeApprovals, 'label' => 'approval'])
</div>
</div>
@endsection
