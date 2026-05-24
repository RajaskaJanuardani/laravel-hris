@extends('layouts.app', ['heading' => 'Dashboard Karyawan'])
@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-3"><div class="card stat p-3"><div class="text-muted small">Status Hari Ini</div><div class="h4 mb-0"><span class="badge text-bg-{{ $currentStatus['badge'] }}">{{ $currentStatus['label'] }}</span></div></div></div>
    <div class="col-md-3"><div class="card stat p-3"><div class="text-muted small">Hadir Bulan Ini</div><div class="h4 mb-0">{{ $monthlyPresent }} hari</div></div></div>
    <div class="col-md-3"><div class="card stat p-3"><div class="text-muted small">Lembur Disetujui</div><div class="h4 mb-0">{{ $approvedOvertimeThisMonth }}</div></div></div>
    <div class="col-md-3"><div class="card stat p-3"><div class="text-muted small">Cuti Pending</div><div class="h4 mb-0">{{ $pendingLeaves }}</div></div></div>
</div>
<div class="row g-4">
    <div class="col-lg-7">
        <div class="card p-4">
            <h2 class="h5">Riwayat Absensi</h2>
            <div class="table-responsive"><table class="table align-middle">
                <thead><tr><th>Tanggal</th><th>Masuk</th><th>Pulang</th><th>Lembur</th><th>Status</th></tr></thead>
                <tbody>
                @forelse($attendances as $attendance)
                    <tr>
                        <td>{{ $attendance->attendance_date->format('d M Y') }}</td>
                        <td>{{ $attendance->check_in_time?->format('H:i') ?? '-' }}</td>
                        <td>{{ $attendance->check_out_time?->format('H:i') ?? '-' }}</td>
                        <td>{{ number_format($attendance->overtime_hours, 2) }} jam</td>
                        <td><span class="badge text-bg-primary">{{ $attendance->status }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-muted">Belum ada absensi.</td></tr>
                @endforelse
                </tbody>
            </table></div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card p-4 mb-4">
            <h2 class="h5">Approval Lembur</h2>
            @forelse($overtimeApprovals as $approval)
                <div class="border-bottom py-2">
                    <div class="fw-semibold">{{ $approval->overtime_date->format('d M Y') }}</div>
                    <div class="small text-muted">17:00 - {{ $approval->end_time->format('H:i') }} | {{ $approval->notes ?? 'Tanpa catatan' }}</div>
                    <span class="badge text-bg-{{ $approval->status === 'approved' ? 'success' : 'secondary' }}">{{ $approval->status }}</span>
                </div>
            @empty
                <p class="text-muted">Belum ada approval lembur.</p>
            @endforelse
        </div>
        <div class="card p-4">
            <h2 class="h5">Pengajuan Cuti</h2>
            @forelse($leaves as $leave)
                <div class="border-bottom py-2"><div class="fw-semibold">{{ $leave->leaveType->name }}</div><div class="small text-muted">{{ $leave->start_date->format('d M') }} - {{ $leave->end_date->format('d M Y') }} - {{ $leave->status }}</div></div>
            @empty
                <p class="text-muted">Belum ada pengajuan.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
