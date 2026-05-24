@extends('layouts.app', ['heading' => 'Laporan'])
@section('content')
<div class="row g-4">
    @foreach([
        ['Absensi Harian','admin.reports.attendance.daily','Rekap kehadiran per tanggal: masuk, pulang, telat, lembur.','time.svg'],
        ['Rekap Absensi (Periode)','admin.reports.attendance.recap','Ringkasan per karyawan pada periode tertentu.','table.svg'],
        ['Ranking Keterlambatan','admin.reports.late-ranking','Urutan karyawan paling sering telat pada periode.','arrow-up.svg'],
        ['Laporan Lembur','admin.reports.overtime','Daftar approval lembur dan detail jam lembur.','task.svg'],
        ['Laporan Cuti/Izin','admin.reports.leaves','Pengajuan cuti/izin per periode dan statusnya.','calender-line.svg'],
        ['Laporan Payroll','admin.reports.payroll','Rekap slip gaji per periode payroll.','dollar-line.svg'],
        ['Audit Scan RFID','admin.reports.rfid-audit','Log scan RFID: berhasil/gagal, device, IP, waktu.','docs.svg'],
    ] as $item)
        <div class="col-md-6 col-xl-4">
            <a class="text-decoration-none" href="{{ route($item[1]) }}">
                <div class="card p-4 h-100">
                    <div class="d-flex align-items-start gap-3">
                        <div class="rounded-3 bg-light d-inline-flex align-items-center justify-content-center" style="width:44px;height:44px;">
                            <img src="{{ asset('tailadmin-nextjs-1.0.0/src/icons/'.$item[3]) }}" alt="">
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-semibold">{{ $item[0] }}</div>
                            <div class="text-muted small">{{ $item[2] }}</div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    @endforeach
</div>
@endsection
