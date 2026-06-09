@extends('layouts.app', ['heading' => 'Riwayat Absensi'])
@section('content')
<style>
    .employee-attendance-page .attendance-toolbar {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
        padding: 24px;
    }

    .employee-attendance-page .attendance-filters {
        display: inline-flex;
        flex-wrap: wrap;
        gap: 8px;
        border: 1px solid var(--ta-gray-200);
        border-radius: 16px;
        background: linear-gradient(145deg, #ffffff 0%, #f8fbff 100%);
        box-shadow: 0 12px 28px rgba(16, 24, 40, .06);
        padding: 6px;
    }

    .employee-attendance-page .attendance-filter {
        display: inline-flex;
        min-height: 38px;
        align-items: center;
        gap: 8px;
        border-radius: 12px;
        color: var(--ta-gray-600);
        font-size: 14px;
        font-weight: 700;
        padding: 9px 14px;
        text-decoration: none;
        transition: background-color .18s ease, color .18s ease, box-shadow .18s ease, transform .18s ease;
    }

    .employee-attendance-page .attendance-filter::before {
        content: "";
        width: 8px;
        height: 8px;
        border-radius: 999px;
        background: var(--filter-color);
        box-shadow: 0 0 0 4px color-mix(in srgb, var(--filter-color) 12%, transparent);
    }

    .employee-attendance-page .attendance-filter:hover {
        background: color-mix(in srgb, var(--filter-color) 9%, #ffffff);
        color: var(--ta-gray-900);
        transform: translateY(-1px);
    }

    .employee-attendance-page .attendance-filter.is-active {
        background: var(--filter-color);
        color: #ffffff;
        box-shadow: 0 12px 26px color-mix(in srgb, var(--filter-color) 26%, transparent);
    }

    .employee-attendance-page .attendance-filter.is-active::before {
        background: #ffffff;
        box-shadow: 0 0 0 4px rgba(255, 255, 255, .22);
    }

    html.dark .employee-attendance-page .attendance-filters {
        border-color: rgba(159, 176, 207, .24);
        background: linear-gradient(145deg, rgba(12, 22, 44, .9), rgba(15, 23, 42, .82));
        box-shadow: 0 20px 44px rgba(0, 0, 0, .24);
    }

    html.dark .employee-attendance-page .attendance-filter {
        color: #c8d4e8;
    }

    html.dark .employee-attendance-page .attendance-filter:hover {
        background: color-mix(in srgb, var(--filter-color) 16%, transparent);
        color: #f4f7ff;
    }

    @media (max-width: 767.98px) {
        .employee-attendance-page .attendance-toolbar {
            align-items: stretch;
        }

        .employee-attendance-page .attendance-filters {
            width: 100%;
        }

        .employee-attendance-page .attendance-filter {
            flex: 1 1 auto;
            justify-content: center;
        }
    }
</style>

<div class="employee-attendance-page">
<div class="card overflow-hidden">
    <div class="attendance-toolbar border-bottom">
        <div>
            <h2 class="h5 mb-1">Riwayat Absensi</h2>
            <div class="text-muted small">Daftar scan masuk, pulang, telat, dan lembur kamu.</div>
        </div>
        <div class="attendance-filters" aria-label="Filter status absensi">
            @foreach($statusFilters as $filter)
                @php
                    $filterValue = $filter['value'];
                    $filterUrl = $filterValue
                        ? route('employee.attendance.history', ['status' => $filterValue])
                        : route('employee.attendance.history');
                    $filterColor = match ($filterValue) {
                        'present' => '#22c55e',
                        'late' => '#f59e0b',
                        'absent' => '#ef4444',
                        default => '#465fff',
                    };
                @endphp
                <a
                    class="attendance-filter {{ $statusFilter === $filterValue ? 'is-active' : '' }}"
                    style="--filter-color:{{ $filterColor }};"
                    href="{{ $filterUrl }}"
                    aria-current="{{ $statusFilter === $filterValue ? 'page' : 'false' }}"
                >
                    {{ $filter['label'] }}
                </a>
            @endforeach
        </div>
    </div>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead><tr><th>Tanggal</th><th>Masuk</th><th>Pulang</th><th>Status</th><th>Telat</th><th>Lembur</th></tr></thead>
            <tbody>
            @forelse($absensi as $attendance)
                @php($status = \App\Support\DisplayLabel::status($attendance->status))
                <tr>
                    <td><span class="ta-code-chip">{{ $attendance->tanggal_absensi->format('d M Y') }}</span></td>
                    <td><span class="ta-time-pill">{{ $attendance->jam_masuk?->format('H:i') ?? '-' }}</span></td>
                    <td><span class="ta-time-pill">{{ $attendance->jam_pulang?->format('H:i') ?? '-' }}</span></td>
                    <td><span class="badge text-bg-{{ $status['badge'] }}">{{ $status['label'] }}</span></td>
                    <td>{{ $attendance->menit_telat }} menit</td>
                    <td>{{ number_format($attendance->jam_lembur, 2) }} jam</td>
                </tr>
            @empty
                <tr><td colspan="6" class="ta-table-empty">{{ $statusFilter ? 'Belum ada riwayat untuk filter ini.' : 'Belum ada riwayat.' }}</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if(method_exists($absensi, 'links'))
        @include('shared._pagination', ['paginator' => $absensi, 'label' => 'absensi'])
    @endif
</div>
</div>
@endsection
