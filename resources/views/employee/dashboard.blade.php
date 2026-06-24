@extends('layouts.app', ['heading' => 'Dashboard Karyawan'])

@section('content')
<style>
    .employee-dashboard .hero-panel {
        overflow: hidden;
        border: 0;
        border-radius: 24px;
        background:
            radial-gradient(circle at 88% 12%, rgba(20, 184, 166, .34), transparent 17rem),
            linear-gradient(135deg, #0f172a 0%, #173153 54%, #1f5eff 100%);
        color: #f8fbff;
        box-shadow: 0 18px 40px rgba(15, 23, 42, .18);
    }

    .employee-dashboard .hero-title {
        max-width: 620px;
        color: #fff;
        font-size: clamp(1.8rem, 2.2vw, 2.55rem);
        font-weight: 700;
        line-height: 1.08;
    }

    .employee-dashboard .hero-copy,
    .employee-dashboard .hero-label {
        color: rgba(226, 232, 240, .78);
    }

    .employee-dashboard .hero-avatar {
        width: 84px;
        height: 84px;
        border: 3px solid rgba(255, 255, 255, .32);
        border-radius: 22px;
        object-fit: cover;
        box-shadow: 0 18px 34px rgba(0, 0, 0, .22);
    }

    .employee-dashboard .hero-chip {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 10px 13px;
        border: 1px solid rgba(255, 255, 255, .15);
        border-radius: 14px;
        background: rgba(255, 255, 255, .08);
        color: #fff;
        font-size: 13px;
        font-weight: 600;
        backdrop-filter: blur(12px);
    }

    .employee-dashboard .quick-card {
        display: block;
        height: 100%;
        color: inherit;
        text-decoration: none;
    }

    .employee-dashboard .metric-card {
        height: 100%;
        border: 0;
        border-radius: 18px;
        background: linear-gradient(180deg, rgba(255, 255, 255, .98), rgba(247, 249, 252, .94));
        box-shadow: 0 14px 30px rgba(15, 23, 42, .08);
        transition: transform .18s ease, box-shadow .18s ease;
    }

    .employee-dashboard .quick-card:hover .metric-card {
        transform: translateY(-2px);
        box-shadow: 0 18px 38px rgba(15, 23, 42, .12);
    }

    .employee-dashboard .metric-label {
        color: #667085;
        font-size: 13px;
        font-weight: 600;
    }

    .employee-dashboard .metric-value {
        color: #101828;
        font-size: 2rem;
        font-weight: 700;
        line-height: 1;
    }

    .employee-dashboard .metric-note {
        color: #475467;
        font-size: 13px;
    }

    .employee-dashboard .metric-icon {
        display: inline-flex;
        width: 42px;
        height: 42px;
        align-items: center;
        justify-content: center;
        border-radius: 14px;
        font-size: 13px;
        font-weight: 700;
        line-height: 1;
    }

    .employee-dashboard .panel-card {
        border: 0;
        border-radius: 24px;
        box-shadow: 0 16px 34px rgba(15, 23, 42, .08);
    }

    .employee-dashboard .activity-row {
        display: grid;
        grid-template-columns: minmax(120px, .65fr) minmax(0, 1fr) auto;
        gap: 16px;
        align-items: center;
        padding: 16px 0;
        border-bottom: 1px solid #e4e7ec;
    }

    .employee-dashboard .activity-row:last-child,
    .employee-dashboard .compact-row:last-child {
        border-bottom: 0;
    }

    .employee-dashboard .activity-date {
        color: #101828;
        font-weight: 700;
    }

    .employee-dashboard .activity-meta {
        color: #667085;
        font-size: 13px;
    }

    .employee-dashboard .time-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 10px;
    }

    .employee-dashboard .time-cell {
        padding: 10px 12px;
        border: 1px solid #e4e7ec;
        border-radius: 14px;
        background: #f8fafc;
    }

    .employee-dashboard .time-label {
        color: #667085;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .06em;
        text-transform: uppercase;
    }

    .employee-dashboard .time-value {
        color: #101828;
        font-weight: 700;
    }

    .employee-dashboard .compact-row {
        padding: 15px 0;
        border-bottom: 1px solid #e4e7ec;
    }

    .employee-dashboard .summary-strip {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        overflow: hidden;
        border: 1px solid #e4e7ec;
        border-radius: 20px;
    }

    .employee-dashboard .summary-cell {
        padding: 18px;
        border-right: 1px solid #e4e7ec;
        background: #fff;
    }

    .employee-dashboard .summary-cell:last-child {
        border-right: 0;
    }

    .employee-dashboard .summary-value {
        color: #101828;
        font-size: 1.5rem;
        font-weight: 700;
    }

    .employee-dashboard .empty-state {
        padding: 22px;
        border: 1px dashed #d0d5dd;
        border-radius: 18px;
        color: #667085;
        text-align: center;
    }

    html.dark .employee-dashboard .hero-panel {
        background:
            radial-gradient(circle at 88% 12%, rgba(45, 212, 191, .22), transparent 17rem),
            linear-gradient(135deg, rgba(11, 16, 32, .96) 0%, rgba(20, 35, 68, .96) 54%, rgba(54, 65, 245, .92) 100%);
        box-shadow: 0 18px 40px rgba(0, 0, 0, .28);
    }

    html.dark .employee-dashboard .metric-card,
    html.dark .employee-dashboard .panel-card,
    html.dark .employee-dashboard .summary-cell,
    html.dark .employee-dashboard .time-cell {
        background: rgba(12, 22, 44, .86);
        box-shadow: 0 16px 34px rgba(0, 0, 0, .25);
    }

    html.dark .employee-dashboard .metric-label,
    html.dark .employee-dashboard .metric-note,
    html.dark .employee-dashboard .activity-meta,
    html.dark .employee-dashboard .time-label,
    html.dark .employee-dashboard .text-muted {
        color: #9fb0cf !important;
    }

    html.dark .employee-dashboard .metric-value,
    html.dark .employee-dashboard .activity-date,
    html.dark .employee-dashboard .time-value,
    html.dark .employee-dashboard .summary-value {
        color: #e7edf8;
    }

    html.dark .employee-dashboard .activity-row,
    html.dark .employee-dashboard .compact-row,
    html.dark .employee-dashboard .summary-cell {
        border-color: rgba(159, 176, 207, .25);
    }

    @media (max-width: 991.98px) {
        .employee-dashboard .activity-row {
            grid-template-columns: 1fr;
        }

        .employee-dashboard .summary-strip {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .employee-dashboard .summary-cell:nth-child(2) {
            border-right: 0;
        }
    }

    @media (max-width: 575.98px) {
        .employee-dashboard .time-grid,
        .employee-dashboard .summary-strip {
            grid-template-columns: 1fr;
        }

        .employee-dashboard .summary-cell {
            border-right: 0;
            border-bottom: 1px solid #e4e7ec;
        }

        .employee-dashboard .summary-cell:last-child {
            border-bottom: 0;
        }
    }
</style>

@php
    $todayStatus = $todayAttendance
        ? \App\Support\DisplayLabel::status($todayAttendance->status)
        : ['label' => 'Belum Scan', 'badge' => 'secondary'];
    $employeeName = $employee?->full_name ?? auth()->user()?->name ?? 'Karyawan';
    $employeeId = $employee?->karyawan_id ?? '-';
    $jobRole = $employee?->jabatan ? \App\Support\DisplayLabel::jobRole($employee->jabatan) : 'Karyawan';
    $latestPayslipStatus = $latestPayslip ? \App\Support\DisplayLabel::status($latestPayslip->status) : null;
    $quickStats = [
        ['label' => 'Hadir Bulan Ini', 'value' => $monthlyPresent, 'suffix' => 'hari', 'note' => 'Masuk tepat waktu', 'icon' => 'H', 'bg' => 'rgba(34,197,94,.14)', 'color' => '#16a34a', 'url' => route('employee.attendance.history')],
        ['label' => 'Telat Bulan Ini', 'value' => $monthlyLate, 'suffix' => 'hari', 'note' => 'Perlu dikurangi', 'icon' => 'T', 'bg' => 'rgba(245,158,11,.16)', 'color' => '#d97706', 'url' => route('employee.attendance.history')],
        ['label' => 'Total Lembur', 'value' => number_format($monthlyOvertimeHours, 1), 'suffix' => 'jam', 'note' => 'Akumulasi bulan ini', 'icon' => 'L', 'bg' => 'rgba(20,184,166,.16)', 'color' => '#0f766e', 'url' => route('employee.overtime.index')],
        ['label' => 'Cuti Menunggu', 'value' => $pendingLeaves, 'suffix' => '', 'note' => 'Menunggu persetujuan', 'icon' => 'C', 'bg' => 'rgba(168,85,247,.16)', 'color' => '#7c3aed', 'url' => route('employee.leaves.index')],
    ];
@endphp

<div class="employee-dashboard">
    <div class="hero-panel mb-4">
        <div class="p-4 p-xl-5">
            <div class="row g-4 align-items-center">
                <div class="col-xl-8">
                    <div class="d-flex flex-wrap align-items-center gap-3 mb-4">
                        <img class="hero-avatar" src="{{ $employee?->profile_photo_url ?? asset('tailadmin-nextjs-1.0.0/public/images/user/owner.jpg') }}" alt="{{ $employeeName }}">
                        <div>
                            <div class="hero-label small fw-semibold">{{ $employeeId }} - {{ $jobRole }}</div>
                            <h2 class="hero-title mb-0">Halo, {{ $employeeName }}.</h2>
                        </div>
                    </div>
                    <p class="hero-copy mb-0">Ringkasan absensi, lembur, cuti, dan slip gaji kamu dalam satu halaman yang lebih cepat dipindai.</p>
                </div>
                <div class="col-xl-4">
                    <div class="d-flex flex-wrap gap-2 justify-content-xl-end">
                        <span class="hero-chip">Status: {{ $currentStatus['label'] }}</span>
                        <span class="hero-chip">Hari ini: {{ $todayStatus['label'] }}</span>
                        <span class="hero-chip">Tarif: Rp {{ number_format($employee?->tarif_harian ?? 0, 0, ',', '.') }}/hari</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        @foreach($quickStats as $stat)
            <div class="col-12 col-md-6 col-xl-3">
                <a class="quick-card" href="{{ $stat['url'] }}">
                    <div class="card metric-card">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start gap-3">
                                <div>
                                    <div class="metric-label">{{ $stat['label'] }}</div>
                                    <div class="metric-value mt-3">{{ $stat['value'] }} <span class="fs-6 fw-semibold">{{ $stat['suffix'] }}</span></div>
                                    <div class="metric-note mt-2">{{ $stat['note'] }}</div>
                                </div>
                                <span class="metric-icon" style="background: {{ $stat['bg'] }}; color: {{ $stat['color'] }};">{{ $stat['icon'] }}</span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    <div class="card panel-card mb-4">
        <div class="card-body p-4 p-xl-5">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
                <div>
                    <h2 class="h4 mb-1">Status Hari Ini</h2>
                    <p class="text-muted mb-0">Ringkasan scan masuk, pulang, dan status kerja hari ini.</p>
                </div>
                <span class="badge text-bg-{{ $todayStatus['badge'] }}">{{ $todayStatus['label'] }}</span>
            </div>
            <div class="summary-strip">
                <div class="summary-cell">
                    <div class="metric-label">Masuk</div>
                    <div class="summary-value mt-2">{{ $todayAttendance?->jam_masuk?->format('H:i') ?? '-' }}</div>
                </div>
                <div class="summary-cell">
                    <div class="metric-label">Pulang</div>
                    <div class="summary-value mt-2">{{ $todayAttendance?->jam_pulang?->format('H:i') ?? '-' }}</div>
                </div>
                <div class="summary-cell">
                    <div class="metric-label">Telat</div>
                    <div class="summary-value mt-2">{{ $todayAttendance?->menit_telat ?? 0 }} menit</div>
                </div>
                <div class="summary-cell">
                    <div class="metric-label">Lembur</div>
                    <div class="summary-value mt-2">{{ number_format($todayAttendance?->jam_lembur ?? 0, 2) }} jam</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-8">
            <div class="card panel-card h-100">
                <div class="card-body p-4 p-xl-5">
                    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
                        <div>
                            <h2 class="h4 mb-1">Riwayat Absensi</h2>
                            <p class="text-muted mb-0">Aktivitas absensi terbaru dari RFID.</p>
                        </div>
                        <a class="btn btn-outline-secondary btn-sm" href="{{ route('employee.attendance.history') }}">Lihat Semua</a>
                    </div>
                    @forelse($absensi as $attendance)
                        @php($status = \App\Support\DisplayLabel::status($attendance->status))
                        <div class="activity-row">
                            <div>
                                <div class="activity-date">{{ $attendance->tanggal_absensi->format('d M Y') }}</div>
                                <div class="activity-meta">{{ $attendance->tanggal_absensi->translatedFormat('l') }}</div>
                            </div>
                            <div class="time-grid">
                                <div class="time-cell">
                                    <div class="time-label">Masuk</div>
                                    <div class="time-value">{{ $attendance->jam_masuk?->format('H:i') ?? '-' }}</div>
                                </div>
                                <div class="time-cell">
                                    <div class="time-label">Pulang</div>
                                    <div class="time-value">{{ $attendance->jam_pulang?->format('H:i') ?? '-' }}</div>
                                </div>
                                <div class="time-cell">
                                    <div class="time-label">Lembur</div>
                                    <div class="time-value">{{ number_format($attendance->jam_lembur, 2) }} jam</div>
                                </div>
                            </div>
                            <div class="text-xl-end">
                                <span class="badge text-bg-{{ $status['badge'] }}">{{ $status['label'] }}</span>
                                @if($attendance->menit_telat > 0)
                                    <div class="activity-meta mt-2">{{ $attendance->menit_telat }} menit telat</div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">Belum ada absensi.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card panel-card mb-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                        <div>
                            <h2 class="h5 mb-1">Slip Gaji Terakhir</h2>
                            <p class="text-muted small mb-0">Periode payroll terbaru.</p>
                        </div>
                        <a class="btn btn-outline-secondary btn-sm" href="{{ route('employee.payslip.index') }}">Detail</a>
                    </div>
                    @if($latestPayslip)
                        <div class="compact-row">
                            <div class="fw-semibold">{{ $latestPayslip->payrollPeriod?->name ?? 'Slip Gaji' }}</div>
                            <div class="text-muted small">{{ $latestPayslip->tanggal_penggajian?->format('d M Y') ?? '-' }}</div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center pt-3">
                            <div>
                                <div class="metric-label">Gaji Bersih</div>
                                <div class="summary-value mt-1">Rp {{ number_format($latestPayslip->gaji_bersih, 0, ',', '.') }}</div>
                            </div>
                            <span class="badge text-bg-{{ $latestPayslipStatus['badge'] }}">{{ $latestPayslipStatus['label'] }}</span>
                        </div>
                    @else
                        <div class="empty-state">Belum ada slip gaji.</div>
                    @endif
                </div>
            </div>

            <div class="card panel-card mb-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                        <div>
                            <h2 class="h5 mb-1">Jadwal Lembur</h2>
                            <p class="text-muted small mb-0">Informasi lembur dari admin.</p>
                        </div>
                        <a class="btn btn-outline-secondary btn-sm" href="{{ route('employee.overtime.index') }}">Semua</a>
                    </div>
                    @forelse($overtimeApprovals as $approval)
                        @php($status = \App\Support\DisplayLabel::overtimeStatus($approval->status))
                        <div class="compact-row">
                            <div class="d-flex justify-content-between align-items-start gap-3">
                                <div>
                                    <div class="fw-semibold">{{ $approval->tanggal_lembur->format('d M Y') }}</div>
                                    <div class="text-muted small">17:00 - {{ $approval->jam_selesai->format('H:i') }} | {{ $approval->catatan ?? 'Tanpa catatan' }}</div>
                                </div>
                                <span class="badge text-bg-{{ $status['badge'] }}">{{ $status['label'] }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">Belum ada jadwal lembur.</div>
                    @endforelse
                </div>
            </div>

            <div class="card panel-card">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                        <div>
                            <h2 class="h5 mb-1">Pengajuan Cuti</h2>
                            <p class="text-muted small mb-0">Riwayat pengajuan terbaru.</p>
                        </div>
                        <a class="btn btn-outline-secondary btn-sm" href="{{ route('employee.leaves.index') }}">Ajukan</a>
                    </div>
                    @forelse($leaves as $leave)
                        @php($status = \App\Support\DisplayLabel::status($leave->status))
                        <div class="compact-row">
                            <div class="d-flex justify-content-between align-items-start gap-3">
                                <div>
                                    <div class="fw-semibold">{{ $leave->leaveType->name }}</div>
                                    <div class="text-muted small">{{ $leave->tanggal_mulai->format('d M') }} - {{ $leave->tanggal_selesai->format('d M Y') }}</div>
                                </div>
                                <span class="badge text-bg-{{ $status['badge'] }}">{{ $status['label'] }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">Belum ada pengajuan.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
