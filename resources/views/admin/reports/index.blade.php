@extends('layouts.app', ['heading' => 'Laporan'])
@section('content')
<style>
    .reports-page .report-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 22px;
    }

    .reports-page .report-link {
        display: block;
        height: 100%;
        color: inherit;
        text-decoration: none;
    }

    .reports-page .report-card {
        position: relative;
        isolation: isolate;
        overflow: hidden;
        min-height: 150px;
        height: 100%;
        border: 1px solid rgba(228, 231, 236, .92);
        border-radius: 18px;
        background: linear-gradient(145deg, #ffffff 0%, #f8fbff 100%);
        box-shadow: 0 18px 42px rgba(16, 24, 40, .08);
        padding: 24px;
        transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
    }

    .reports-page .report-card::before {
        content: "";
        position: absolute;
        inset: 0 auto 0 0;
        width: 5px;
        background: var(--report-color);
    }

    .reports-page .report-card::after {
        content: "";
        position: absolute;
        right: -44px;
        top: -54px;
        z-index: -1;
        width: 156px;
        height: 156px;
        border-radius: 999px;
        background: color-mix(in srgb, var(--report-color) 14%, transparent);
    }

    .reports-page .report-link:hover .report-card {
        border-color: color-mix(in srgb, var(--report-color) 28%, #d0d5dd);
        box-shadow: 0 22px 48px rgba(16, 24, 40, .12);
        transform: translateY(-2px);
    }

    .reports-page .report-body {
        display: flex;
        align-items: flex-start;
        gap: 18px;
    }

    .reports-page .report-icon {
        display: inline-flex;
        width: 54px;
        height: 54px;
        flex: 0 0 54px;
        align-items: center;
        justify-content: center;
        border-radius: 17px;
        background: color-mix(in srgb, var(--report-color) 12%, #ffffff);
        color: var(--report-color);
        box-shadow: inset 0 0 0 1px color-mix(in srgb, var(--report-color) 18%, transparent);
    }

    .reports-page .report-icon img {
        width: 24px;
        height: 24px;
        filter: invert(34%) sepia(98%) saturate(2296%) hue-rotate(221deg) brightness(101%) contrast(103%);
    }

    .reports-page .report-content {
        min-width: 0;
    }

    .reports-page .report-title {
        color: var(--ta-gray-900);
        font-size: 18px;
        font-weight: 800;
        line-height: 1.25;
    }

    .reports-page .report-copy {
        color: var(--ta-gray-500);
        font-size: 14px;
        line-height: 1.55;
        margin-top: 7px;
    }

    .reports-page .report-action {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: var(--report-color);
        font-size: 13px;
        font-weight: 700;
        margin-top: 16px;
    }

    .reports-page .report-action svg {
        width: 15px;
        height: 15px;
        transition: transform .18s ease;
    }

    .reports-page .report-link:hover .report-action svg {
        transform: translateX(3px);
    }

    html.dark .reports-page .report-card {
        border-color: rgba(159, 176, 207, .24);
        background: linear-gradient(145deg, rgba(12, 22, 44, .9), rgba(15, 23, 42, .82));
        box-shadow: 0 20px 44px rgba(0, 0, 0, .28);
    }

    html.dark .reports-page .report-link:hover .report-card {
        border-color: color-mix(in srgb, var(--report-color) 38%, rgba(159, 176, 207, .24));
        box-shadow: 0 26px 54px rgba(0, 0, 0, .38);
    }

    html.dark .reports-page .report-icon {
        background: color-mix(in srgb, var(--report-color) 18%, transparent);
        box-shadow: inset 0 0 0 1px color-mix(in srgb, var(--report-color) 28%, transparent);
    }

    html.dark .reports-page .report-icon img {
        filter: invert(91%) sepia(11%) saturate(835%) hue-rotate(187deg) brightness(103%) contrast(103%);
    }

    html.dark .reports-page .report-title {
        color: #f4f7ff;
    }

    html.dark .reports-page .report-copy {
        color: #9fb0cf;
    }

    @media (max-width: 1199.98px) {
        .reports-page .report-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 767.98px) {
        .reports-page .report-grid {
            grid-template-columns: 1fr;
        }

        .reports-page .report-card {
            min-height: 136px;
        }
    }
</style>

<div class="reports-page">
<div class="report-grid">
    @foreach([
        ['Absensi Harian','admin.reports.attendance.daily','Rekap kehadiran per tanggal: masuk, pulang, telat, lembur.','time.svg','#465fff'],
        ['Rekap Absensi (Periode)','admin.reports.attendance.recap','Ringkasan per karyawan pada periode tertentu.','table.svg','#14b8a6'],
        ['Ranking Telat','admin.reports.late-ranking','Urutan karyawan paling sering telat pada periode.','arrow-up.svg','#f59e0b'],
        ['Laporan Lembur','admin.reports.overtime','Daftar approval lembur dan detail jam lembur.','task.svg','#8b5cf6'],
        ['Laporan Cuti/Izin','admin.reports.leaves','Pengajuan cuti/izin per periode dan statusnya.','calender-line.svg','#06b6d4'],
        ['Laporan Payroll','admin.reports.payroll','Rekap slip gaji per periode payroll.','dollar-line.svg','#22c55e'],
        ['Audit Scan RFID','admin.reports.rfid-audit','Log scan RFID: berhasil/gagal, device, IP, waktu.','docs.svg','#ef4444'],
    ] as $item)
        <div>
            <a class="report-link" href="{{ route($item[1]) }}">
                <div class="report-card" style="--report-color:{{ $item[4] }};">
                    <div class="report-body">
                        <span class="report-icon" aria-hidden="true">
                            <img src="{{ asset('tailadmin-nextjs-1.0.0/src/icons/'.$item[3]) }}" alt="">
                        </span>
                        <div class="report-content">
                            <div class="report-title">{{ $item[0] }}</div>
                            <div class="report-copy">{{ $item[2] }}</div>
                            <div class="report-action">
                                Buka laporan
                                <svg viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M7.5 4.5 13 10l-5.5 5.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    @endforeach
</div>
</div>
@endsection
