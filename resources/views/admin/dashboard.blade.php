@extends('layouts.app', ['heading' => 'Dashboard Admin'])

@section('content')
<style>
    .admin-dashboard .hero-card {
        overflow: hidden;
        border: 0;
        background:
            radial-gradient(circle at top left, rgba(31, 94, 255, .18), transparent 16rem),
            linear-gradient(135deg, #0f172a 0%, #12203f 52%, #1f5eff 100%);
        color: #f8fbff;
        box-shadow: 0 18px 40px rgba(15, 23, 42, .18);
    }

    .admin-dashboard .hero-copy {
        max-width: 540px;
    }

    .admin-dashboard .hero-eyebrow,
    .admin-dashboard .hero-subtle,
    .admin-dashboard .hero-stat-label {
        color: rgba(226, 232, 240, .76);
    }

    .admin-dashboard .hero-title {
        color: #fff;
        font-size: clamp(1.8rem, 2vw, 2.5rem);
        font-weight: 700;
        line-height: 1.05;
    }

    .admin-dashboard .hero-value {
        color: #fff;
        font-size: 1.9rem;
        font-weight: 700;
        line-height: 1;
    }

    .admin-dashboard .hero-chip {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 12px 14px;
        border: 1px solid rgba(255, 255, 255, .14);
        border-radius: 14px;
        background: rgba(255, 255, 255, .08);
        backdrop-filter: blur(12px);
    }

    .admin-dashboard .hero-stat {
        min-width: 150px;
    }

    .admin-dashboard .metric-link {
        position: relative;
        isolation: isolate;
        display: flex;
        min-height: 166px;
        overflow: hidden;
        border: 1px solid rgba(228, 231, 236, .92);
        border-radius: 18px;
        background: linear-gradient(145deg, #ffffff 0%, #f8fbff 100%);
        box-shadow: 0 18px 42px rgba(16, 24, 40, .08);
        height: 100%;
        color: inherit;
        text-decoration: none;
        transition: transform .18s ease, border-color .18s ease, box-shadow .18s ease;
    }

    .admin-dashboard .metric-link::before {
        content: "";
        position: absolute;
        inset: 0 auto 0 0;
        width: 5px;
        background: var(--metric-color);
    }

    .admin-dashboard .metric-link::after {
        content: "";
        position: absolute;
        right: -38px;
        top: -42px;
        z-index: -1;
        width: 148px;
        height: 148px;
        border-radius: 999px;
        background: color-mix(in srgb, var(--metric-color) 16%, transparent);
    }

    .admin-dashboard .metric-link:hover {
        border-color: color-mix(in srgb, var(--metric-color) 34%, #e4e7ec);
        transform: translateY(-3px);
        box-shadow: 0 22px 50px rgba(16, 24, 40, .12);
    }

    .admin-dashboard .metric-inner {
        display: flex;
        width: 100%;
        flex-direction: column;
        justify-content: space-between;
        gap: 18px;
        padding: 22px 24px 20px;
    }

    .admin-dashboard .metric-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
    }

    .admin-dashboard .metric-icon {
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

    .admin-dashboard .metric-icon svg {
        width: 23px;
        height: 23px;
    }

    .admin-dashboard .metric-label {
        color: var(--ta-gray-500);
        font-size: 14px;
        font-weight: 600;
    }

    .admin-dashboard .metric-value {
        color: var(--ta-gray-900);
        font-size: 32px;
        font-weight: 800;
        line-height: 1.05;
        margin-top: 16px;
    }

    .admin-dashboard .metric-note {
        color: var(--ta-gray-500);
        font-size: 14px;
        margin-top: 10px;
    }

    .admin-dashboard .metric-action {
        display: inline-flex;
        width: fit-content;
        align-items: center;
        gap: 7px;
        border-radius: 999px;
        color: var(--metric-color);
        font-size: 13px;
        font-weight: 700;
    }

    .admin-dashboard .metric-action svg {
        width: 15px;
        height: 15px;
        transition: transform .18s ease;
    }

    .admin-dashboard .metric-link:hover .metric-action svg {
        transform: translateX(3px);
    }

    .admin-dashboard .chart-card,
    .admin-dashboard .feed-card,
    .admin-dashboard .role-card {
        border: 0;
        border-radius: 24px;
        box-shadow: 0 16px 34px rgba(15, 23, 42, .08);
    }

    .admin-dashboard .chart-shell {
        position: relative;
        height: 340px;
    }

    .admin-dashboard .chart-legend {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .admin-dashboard .chart-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        border-radius: 999px;
        background: #f8fafc;
        color: #344054;
        font-size: 12px;
        font-weight: 600;
    }

    .admin-dashboard .chart-pill-dot {
        width: 10px;
        height: 10px;
        border-radius: 999px;
    }

    .admin-dashboard .insight-card {
        height: 100%;
        padding: 16px 18px;
        border: 1px solid #e4e7ec;
        border-radius: 18px;
        background: linear-gradient(180deg, #fff, #f8fafc);
    }

    .admin-dashboard .insight-label {
        color: #667085;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .06em;
    }

    .admin-dashboard .insight-value {
        color: #101828;
        font-size: 1.55rem;
        font-weight: 700;
    }

    .admin-dashboard .insight-note {
        color: #475467;
        font-size: 13px;
    }

    .admin-dashboard .feed-card .ta-activity-item {
        padding: 14px 0;
    }

    .admin-dashboard .role-strip {
        height: 10px;
        border-radius: 999px;
        background: #eef2ff;
        overflow: hidden;
    }

    .admin-dashboard .role-strip-fill {
        height: 100%;
        border-radius: inherit;
        background: linear-gradient(90deg, #1f5eff, #22c55e);
    }

    html.dark .admin-dashboard .hero-card {
        background:
            radial-gradient(circle at top left, rgba(111, 141, 255, .2), transparent 16rem),
            linear-gradient(135deg, rgba(11, 16, 32, .96) 0%, rgba(20, 35, 68, .96) 52%, rgba(54, 65, 245, .92) 100%);
        box-shadow: 0 18px 40px rgba(0, 0, 0, .28);
    }

    html.dark .admin-dashboard .metric-link,
    html.dark .admin-dashboard .chart-card,
    html.dark .admin-dashboard .feed-card,
    html.dark .admin-dashboard .role-card,
    html.dark .admin-dashboard .insight-card {
        border-color: rgba(159, 176, 207, .24);
        background: rgba(12, 22, 44, .86);
        box-shadow: 0 16px 34px rgba(0, 0, 0, .25);
    }

    html.dark .admin-dashboard .metric-icon {
        background: color-mix(in srgb, var(--metric-color) 18%, transparent);
        box-shadow: inset 0 0 0 1px color-mix(in srgb, var(--metric-color) 28%, transparent);
    }

    html.dark .admin-dashboard .metric-label,
    html.dark .admin-dashboard .insight-label,
    html.dark .admin-dashboard .text-muted,
    html.dark .admin-dashboard .hero-subtle,
    html.dark .admin-dashboard .hero-eyebrow,
    html.dark .admin-dashboard .hero-stat-label {
        color: #9fb0cf !important;
    }

    html.dark .admin-dashboard .metric-value,
    html.dark .admin-dashboard .insight-value,
    html.dark .admin-dashboard .metric-note,
    html.dark .admin-dashboard .insight-note,
    html.dark .admin-dashboard .chart-pill {
        color: #e7edf8;
    }

    html.dark .admin-dashboard .chart-pill {
        background: rgba(255, 255, 255, .05);
    }

    html.dark .admin-dashboard .role-strip {
        background: rgba(255, 255, 255, .08);
    }

    @media (max-width: 991.98px) {
        .admin-dashboard .chart-shell {
            height: 300px;
        }

        .admin-dashboard .metric-link {
            min-height: 146px;
        }
    }
</style>

@php
    $todayQuery = now()->toDateString();
    $stats = [
        ['label' => 'Total Karyawan', 'value' => $totalEmployees, 'note' => 'Semua karyawan aktif', 'icon' => 'users', 'color' => '#465fff', 'url' => route('admin.karyawan.index')],
        ['label' => 'Hadir Hari Ini', 'value' => $presentToday, 'note' => 'Masuk tepat waktu', 'icon' => 'check', 'color' => '#16a34a', 'url' => route('admin.attendance.index', ['date' => $todayQuery, 'status' => 'hadir'])],
        ['label' => 'Telat', 'value' => $lateToday, 'note' => 'Perlu perhatian hari ini', 'icon' => 'clock', 'color' => '#f59e0b', 'url' => route('admin.attendance.index', ['date' => $todayQuery, 'status' => 'telat'])],
        ['label' => 'Tidak Hadir', 'value' => $absentToday, 'note' => 'Tidak masuk hari ini', 'icon' => 'x', 'color' => '#ef4444', 'url' => route('admin.attendance.index', ['date' => $todayQuery, 'status' => 'tidak_hadir'])],
        ['label' => 'Cuti Aktif', 'value' => $leaveToday, 'note' => 'Sedang cuti/izin', 'icon' => 'calendar', 'color' => '#14b8a6', 'url' => route('admin.leaves.index', ['aktif' => 1])],
        ['label' => 'Cuti Menunggu', 'value' => $pendingLeaves, 'note' => 'Menunggu persetujuan', 'icon' => 'file', 'color' => '#8b5cf6', 'url' => route('admin.leaves.index', ['status' => 'pending'])],
        ['label' => 'Lembur Hari Ini', 'value' => $overtimeToday, 'note' => 'Approval aktif hari ini', 'icon' => 'bolt', 'color' => '#ec4899', 'url' => route('admin.overtime.index', ['date' => $todayQuery])],
    ];
    $totalJobRoles = max(1, $jobRoleStats->sum());
@endphp

<div class="admin-dashboard">
    <div class="card hero-card mb-4">
        <div class="card-body p-4 p-xl-5">
            <div class="row g-4 align-items-end">
                <div class="col-xl-7">
                    <div class="hero-copy">
                        <div class="hero-eyebrow small fw-semibold text-uppercase mb-3">Ringkasan Operasional</div>
                        <h2 class="hero-title mb-3">Pantau ritme absensi, telat, dan lembur dalam satu tampilan yang lebih enak dibaca.</h2>
                        <p class="hero-subtle mb-0">Dashboard ini merangkum kondisi hari ini dan tren bulanan supaya admin bisa cepat melihat pola kehadiran tanpa harus buka laporan satu per satu.</p>
                    </div>
                </div>
                <div class="col-xl-5">
                    <div class="d-flex flex-wrap gap-3 justify-content-xl-end">
                        <div class="hero-chip hero-stat">
                            <div>
                                <div class="hero-stat-label small">Rata-rata hadir</div>
                                <div class="hero-value">{{ $attendanceInsight['rata_hadir'] }}</div>
                            </div>
                        </div>
                        <div class="hero-chip hero-stat">
                            <div>
                                <div class="hero-stat-label small">Puncak hadir</div>
                                <div class="hero-value">{{ $attendanceInsight['puncak_hadir'] }}</div>
                                <div class="hero-subtle small mt-1">{{ $attendanceInsight['hari_puncak'] }}</div>
                            </div>
                        </div>
                        <div class="hero-chip hero-stat">
                            <div>
                                <div class="hero-stat-label small">Total lembur</div>
                                <div class="hero-value">{{ number_format($attendanceInsight['total_lembur'], 1) }}j</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        @foreach($stats as $stat)
            <div class="col-12 col-md-6 col-xl-4">
                <a class="metric-link" href="{{ $stat['url'] }}" style="--metric-color: {{ $stat['color'] }};">
                    <div class="metric-inner">
                        <div class="metric-head">
                            <div>
                                <div class="metric-label">{{ $stat['label'] }}</div>
                                <div class="metric-value">{{ number_format($stat['value']) }}</div>
                                <div class="metric-note">{{ $stat['note'] }}</div>
                            </div>
                            <span class="metric-icon" aria-hidden="true">
                                @switch($stat['icon'])
                                    @case('users')
                                        <svg viewBox="0 0 24 24" fill="none"><path d="M16 19c0-2.2-1.8-4-4-4H7c-2.2 0-4 1.8-4 4" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/><path d="M9.5 11a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Z" stroke="currentColor" stroke-width="1.9"/><path d="M21 19c0-2-1.4-3.6-3.3-3.9M16.5 4.5a3 3 0 0 1 0 6" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/></svg>
                                        @break
                                    @case('check')
                                        <svg viewBox="0 0 24 24" fill="none"><path d="m5 12.5 4 4L19 6.5" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/><path d="M21 12a9 9 0 1 1-5.2-8.2" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/></svg>
                                        @break
                                    @case('clock')
                                        <svg viewBox="0 0 24 24" fill="none"><path d="M12 7v5l3 2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Z" stroke="currentColor" stroke-width="1.9"/></svg>
                                        @break
                                    @case('x')
                                        <svg viewBox="0 0 24 24" fill="none"><path d="m8 8 8 8M16 8l-8 8" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/><path d="M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Z" stroke="currentColor" stroke-width="1.9"/></svg>
                                        @break
                                    @case('calendar')
                                        <svg viewBox="0 0 24 24" fill="none"><path d="M7 3v3M17 3v3M4 9h16" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/><path d="M5.5 5h13A1.5 1.5 0 0 1 20 6.5v12A1.5 1.5 0 0 1 18.5 20h-13A1.5 1.5 0 0 1 4 18.5v-12A1.5 1.5 0 0 1 5.5 5Z" stroke="currentColor" stroke-width="1.9"/><path d="m8 14 2 2 5-5" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        @break
                                    @case('file')
                                        <svg viewBox="0 0 24 24" fill="none"><path d="M8 7h5M8 11h8M8 15h5" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/><path d="M6.5 3.5h8.7L19 7.3v13.2H6.5A1.5 1.5 0 0 1 5 19V5a1.5 1.5 0 0 1 1.5-1.5Z" stroke="currentColor" stroke-width="1.9" stroke-linejoin="round"/><path d="M15 3.8V7h3.2" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        @break
                                    @default
                                        <svg viewBox="0 0 24 24" fill="none"><path d="M13 2 5 13h6l-1 9 8-11h-6l1-9Z" stroke="currentColor" stroke-width="1.9" stroke-linejoin="round"/></svg>
                                @endswitch
                            </span>
                        </div>
                        <div class="metric-action">
                            Lihat detail
                            <svg viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M6 3.5 10.5 8 6 12.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    <div class="row g-4">
        <div class="col-xl-8">
            <div class="card chart-card overflow-hidden h-100">
                <div class="card-body p-4 p-xl-5">
                    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
                        <div>
                            <h2 class="h4 mb-1">Grafik Absensi Bulan Ini</h2>
                            <p class="text-muted mb-0">Perbandingan hadir, telat, tidak hadir, dan total jam lembur per hari.</p>
                        </div>
                        <div class="chart-legend">
                            <span class="chart-pill"><span class="chart-pill-dot" style="background:#1f5eff;"></span>Hadir</span>
                            <span class="chart-pill"><span class="chart-pill-dot" style="background:#f59e0b;"></span>Telat</span>
                            <span class="chart-pill"><span class="chart-pill-dot" style="background:#ef4444;"></span>Tidak Hadir</span>
                            <span class="chart-pill"><span class="chart-pill-dot" style="background:#14b8a6;"></span>Lembur</span>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="insight-card">
                                <div class="insight-label">Rata-rata Hadir</div>
                                <div class="insight-value mt-2">{{ $attendanceInsight['rata_hadir'] }}</div>
                                <div class="insight-note mt-2">Karyawan hadir per hari sejak awal bulan.</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="insight-card">
                                <div class="insight-label">Puncak Kehadiran</div>
                                <div class="insight-value mt-2">{{ $attendanceInsight['puncak_hadir'] }}</div>
                                <div class="insight-note mt-2">Terjadi pada {{ $attendanceInsight['hari_puncak'] }}.</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="insight-card">
                                <div class="insight-label">Total Lembur</div>
                                <div class="insight-value mt-2">{{ number_format($attendanceInsight['total_lembur'], 1) }} jam</div>
                                <div class="insight-note mt-2">Akumulasi jam lembur bulan berjalan.</div>
                            </div>
                        </div>
                    </div>

                    <div class="chart-shell">
                        <canvas id="attendanceChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card feed-card h-100">
                <div class="card-body p-4 p-xl-5">
                    <div class="d-flex justify-content-between align-items-start gap-3 mb-4">
                        <div>
                            <h2 class="h4 mb-1">Scan RFID Terbaru</h2>
                            <p class="text-muted mb-0">Aktivitas scan yang paling baru masuk ke sistem.</p>
                        </div>
                    </div>
                    <div class="ta-activity-list">
                        @forelse($recentLogs as $log)
                            <div class="ta-activity-item">
                                <div class="ta-activity-copy">
                                    <div class="ta-activity-title">{{ $log->employee->full_name ?? $log->uid }}</div>
                                    <div class="ta-activity-message">{{ $log->message }}</div>
                                </div>
                                <span class="ta-status-pill {{ $log->status === 'success' ? 'is-success' : 'is-danger' }}">{{ \App\Support\DisplayLabel::scanType($log->tipe_scan) }}</span>
                            </div>
                        @empty
                            <p class="text-muted mb-0">Belum ada scan.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card role-card">
                <div class="card-body p-4 p-xl-5">
                    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
                        <div>
                            <h2 class="h4 mb-1">Komposisi Jabatan</h2>
                            <p class="text-muted mb-0">Distribusi jumlah karyawan aktif per jabatan.</p>
                        </div>
                    </div>
                    <div class="row g-3">
                        @forelse($jobRoleStats as $role => $total)
                            <div class="col-md-6">
                                <div class="insight-card">
                                    <div class="d-flex justify-content-between align-items-center gap-3 mb-3">
                                        <div>
                                            <div class="fw-semibold">{{ \App\Support\DisplayLabel::jobRole($role) }}</div>
                                            <div class="text-muted small">{{ $total }} karyawan</div>
                                        </div>
                                        <div class="fw-semibold">{{ number_format(($total / $totalJobRoles) * 100, 0) }}%</div>
                                    </div>
                                    <div class="role-strip">
                                        <div class="role-strip-fill" style="width: {{ ($total / $totalJobRoles) * 100 }}%"></div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-muted">Belum ada data jabatan.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
(() => {
    const canvas = document.getElementById('attendanceChart');
    if (!canvas || !window.Chart) return;

    const isDark = document.documentElement.classList.contains('dark');
    const ctx = canvas.getContext('2d');
    const gradient = ctx.createLinearGradient(0, 0, 0, canvas.height || 340);
    gradient.addColorStop(0, 'rgba(31, 94, 255, 0.30)');
    gradient.addColorStop(1, 'rgba(31, 94, 255, 0.02)');

    new Chart(canvas, {
        data: {
            labels: @json($attendanceTrend['labels']),
            datasets: [
                {
                    type: 'line',
                    label: 'Hadir',
                    data: @json($attendanceTrend['hadir']),
                    borderColor: '#1f5eff',
                    backgroundColor: gradient,
                    fill: true,
                    tension: .42,
                    borderWidth: 3,
                    pointRadius: 0,
                    pointHoverRadius: 5,
                    pointHoverBackgroundColor: '#1f5eff',
                    pointHoverBorderColor: '#ffffff',
                    pointHoverBorderWidth: 2,
                    yAxisID: 'y',
                    order: 1
                },
                {
                    type: 'bar',
                    label: 'Telat',
                    data: @json($attendanceTrend['telat']),
                    backgroundColor: 'rgba(245, 158, 11, .82)',
                    borderRadius: 10,
                    borderSkipped: false,
                    maxBarThickness: 18,
                    yAxisID: 'y',
                    order: 2
                },
                {
                    type: 'bar',
                    label: 'Tidak Hadir',
                    data: @json($attendanceTrend['absen']),
                    backgroundColor: 'rgba(239, 68, 68, .76)',
                    borderRadius: 10,
                    borderSkipped: false,
                    maxBarThickness: 18,
                    yAxisID: 'y',
                    order: 3
                },
                {
                    type: 'line',
                    label: 'Lembur (jam)',
                    data: @json($attendanceTrend['lembur']),
                    borderColor: '#14b8a6',
                    backgroundColor: '#14b8a6',
                    tension: .35,
                    borderWidth: 2,
                    pointRadius: 0,
                    pointHoverRadius: 4,
                    yAxisID: 'y1',
                    order: 0
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: isDark ? 'rgba(11, 16, 32, .92)' : 'rgba(15, 23, 42, .92)',
                    titleColor: '#fff',
                    bodyColor: 'rgba(255,255,255,.88)',
                    padding: 14,
                    cornerRadius: 14,
                    displayColors: true,
                    boxPadding: 4
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    border: {
                        display: false
                    },
                    ticks: {
                        maxRotation: 0,
                        autoSkip: true,
                        maxTicksLimit: 8
                    }
                },
                y: {
                    position: 'left',
                    beginAtZero: true,
                    grid: {
                        color: isDark ? 'rgba(159, 176, 207, .14)' : 'rgba(15, 23, 42, .08)',
                        drawBorder: false
                    },
                    border: {
                        display: false
                    },
                    ticks: {
                        precision: 0
                    }
                },
                y1: {
                    position: 'right',
                    beginAtZero: true,
                    grid: {
                        drawOnChartArea: false
                    },
                    border: {
                        display: false
                    },
                    ticks: {
                        callback: (value) => value + 'j'
                    }
                }
            }
        }
    });
})();
</script>
@endpush
@endsection
